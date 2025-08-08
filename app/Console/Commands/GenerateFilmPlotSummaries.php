<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm;

class GenerateFilmPlotSummaries extends Command
{
    protected $signature = 'films:generate-plot-summaries';
    protected $description = 'Fetch concise, enticing plot summary (100–150 words) for verified films and save it';

    public function handle()
    {
        $films = VerifiedFilm::skip(500)->take(100)->get();

        if ($films->isEmpty()) {
            $this->info('No films found needing plot summaries.');
            return;
        }

        foreach ($films as $film) {
            $this->info("🎬 Processing: {$film->title} ({$film->director})");

$systemPrompt = <<<PROMPT
You are Festival Film Finder.

When given a film title and director, identify the correct film and generate:

PLOT SUMMARY (100 to 150 words):
- Write in a tone that makes the film sound appealing to watch.
- Summarize the basic story arc and key themes without going into full narrative detail.
- Focus on mood, tone, emotional hooks, and what's compelling about the story.
- Mention main characters and the central conflict, but don't spoil the full journey or ending.
- Do NOT mention the film's title.
- End with 1–2 lines on how **{$film->director}** treats these themes in the film — highlighting their style, tone, or vision in a terse and concise manner.

The goal is to give festival audiences a strong idea of what the film explores while making them want to watch it.
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
                        'temperature' => 0.8,
                        'max_tokens' => 700, // ~150 words
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

                $wordCount = str_word_count(strip_tags($content));
                if ($wordCount < 100 || $wordCount > 160) {
                    $this->warn("⚠️ Plot summary not in range ({$wordCount} words) for {$film->title}");
                    continue;
                }

                $film->update([
                    'plot_summary' => $content,
                    'plot_summary_verified' => 1,
                ]);

                $this->info("✅ Saved plot summary for {$film->title} ({$wordCount} words)");
                sleep(1); // API rate limit buffer
            } catch (\Exception $e) {
                $this->error("💥 Error processing {$film->title}: " . $e->getMessage());
            }
        }

        $this->info('🏁 Done processing plot summaries.');
    }
}
