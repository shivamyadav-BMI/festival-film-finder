<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm; // adjust if your model name is different

class GenerateFilmPlotSummaries extends Command
{
    protected $signature = 'films:generate-plot-summaries';
    protected $description = 'Fetch 300-word plot summaries for verified films and mark them as verified';

    public function handle()
    {

        $films = VerifiedFilm::where('plot_summary_verified',0)->take(1)->get();
        // dd($films);

        if ($films->isEmpty()) {
            $this->info('No films found needing summaries.');
            return;
        }

        foreach ($films as $film) {
            $this->info("Processing: {$film->title} ({$film->director})");

            $systemPrompt = "You are a Festival Film Finder. When given a film title and its director, identify the correct film and return only a verified, original plot summary of about 300 words. Focus on accuracy, narrative clarity, and avoid speculation.";
            $userPrompt = "Title: {$film->title}, Director: {$film->director}";

            try {
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4.1-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 900, // ~300 words
                    ]);

                if ($response->failed()) {
                    $this->error("API call failed for {$film->title}");
                    continue;
                }

                $summary = trim($response->json('choices.0.message.content', ''));

                if (empty($summary)) {
                    $this->warn("No summary returned for {$film->title}");
                    continue;
                }

                $film->update([
                    'plot_summary' => $summary,
                    'plot_summary_verified' => true,
                ]);

                $this->info("✅ Updated {$film->title}");

                // Be polite to API
                sleep(1);

            } catch (\Exception $e) {
                $this->error("Error processing {$film->title}: " . $e->getMessage());
            }
        }

        $this->info('Done processing films.');
    }
}
