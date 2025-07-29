<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Film;

class VerifyFilmsWithImdb extends Command
{
    protected $signature = 'films:verify-test';
    protected $description = 'Verify film metadata by scraping IMDb auto-suggest';

    public function handle()
    {
        $films = Film::all();

        foreach ($films as $film) {
            $this->line("🔍 Searching IMDb for: {$film->title} ({$film->year})");

            $slug = $this->slugify($film->title);
            $firstLetter = strtolower(substr($slug, 0, 1));
            $url = "https://v2.sg.media-imdb.com/suggestion/{$firstLetter}/{$slug}.json";

            $response = Http::get($url);

            if ($response->failed()) {
                $this->error("❌ IMDb request failed for: {$film->title}");
                $this->line(str_repeat('-', 40));
                continue;
            }

            $data = $response->json();

            if (empty($data['d'])) {
                $this->error("❌ IMDb result not found");
                $this->line(str_repeat('-', 40));
                continue;
            }

            // Try to find matching year and title
            $match = collect($data['d'])->first(function ($item) use ($film) {
                return isset($item['y'], $item['l']) &&
                    strtolower($item['l']) === strtolower($film->title) &&
                    $item['y'] == $film->year;
            });

            if (!$match) {
                $this->error("❌ No exact match found for: {$film->title}");
                $this->line(str_repeat('-', 40));
                continue;
            }

            $imdbId = $match['id'] ?? null;
            $year = $match['y'] ?? null;
            $title = $match['l'] ?? null;
            $cast = $match['s'] ?? null;

            $this->info("✅ Match found: $title ($year)");
            $this->line("📌 IMDb ID: $imdbId");
            $this->line("🎬 Cast: $cast");
            $this->line(str_repeat('-', 40));

            // Optional: Save or mark as verified
            // $film->update(['verified' => true, 'imdb_id' => $imdbId]);
        }

        $this->info("✅ Finished verifying all films.");
    }

    protected function slugify($string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $string)));
    }
}
