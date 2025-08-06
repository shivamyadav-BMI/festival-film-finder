<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm;

class GenerateFilmPlotSummaries extends Command
{
    protected $signature = 'films:generate-descriptions';
    protected $description = 'Fetch both description (~300 words) and plot summary (~500 words) for verified films and save them';

    public function handle()
    {
        $films = VerifiedFilm::where('plot_summary_verified', 0)
            ->take(100) // process 1 at a time to avoid hitting API rate limits
            ->get();

        if ($films->isEmpty()) {
            $this->info('No films found needing descriptions or summaries.');
            return;
        }

        foreach ($films as $film) {
            $this->info("🎬 Processing: {$film->title} ({$film->director})");

          $systemPrompt = <<<PROMPT
You are Festival Film Finder.
When given a film title and director, identify the correct film and produce **two separate sections** without explicitly starting with the film title or director's name.

1. DESCRIPTION (~300 words):
   - A compelling, engaging, marketing-style synopsis.
   - Written for a film festival catalog or promotional brochure.
   - Do NOT begin with the film's title or the director's name.
   - Avoid spoilers of major twists, but highlight themes, tone, and notable elements.

2. PLOT SUMMARY (~500 words):
   - Detailed, narrative recount of the film's story from start to end.
   - Do NOT begin with the film's title or the director's name.
   - Include major events, conflicts, and resolution.
   - Keep it factual, clear, and spoiler-friendly.
   - Maintain an original tone (do not copy from existing sources).

Return in exactly this format:
DESCRIPTION:
[description text]

PLOT SUMMARY:
[plot summary text]
PROMPT;


            $userPrompt = "Title: {$film->title}\nDirector: {$film->director}";

            try {
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4.1-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 2000, // enough for ~800 words total
                    ]);

                if ($response->failed()) {
                    $this->error("❌ API call failed for {$film->title}");
                    continue;
                }

                $content = trim($response->json('choices.0.message.content', ''));

                if (empty($content)) {
                    $this->warn("⚠️ No content returned for {$film->title}");
                    continue;
                }

                // Extract description & plot summary
                preg_match('/DESCRIPTION:\s*(.*?)\s*PLOT SUMMARY:\s*(.*)/is', $content, $matches);

                $description = isset($matches[1]) ? trim($matches[1]) : '';
                $plotSummary = isset($matches[2]) ? trim($matches[2]) : '';

                if (empty($description) || empty($plotSummary)) {
                    $this->warn("⚠️ Could not parse both sections for {$film->title}");
                    continue;
                }

                $film->update([
                    'description' => $description,
                    'plot_summary' => $plotSummary,
                    'plot_summary_verified' => true,
                ]);

                $this->info("✅ Updated {$film->title} with 300-word description & 500-word plot summary");

                sleep(1); // Respect API rate limits

            } catch (\Exception $e) {
                $this->error("💥 Error processing {$film->title}: " . $e->getMessage());
            }
        }

        $this->info('🏁 Done processing films.');
    }
}
