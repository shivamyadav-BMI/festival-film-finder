<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\VerifiedFilm;
use Illuminate\Support\Str;

class ScrapeMetacriticRatings extends Command
{
    protected $signature = 'films:scrape-metacritic';
    protected $description = 'Scrape Metacritic critic score and update VerifiedFilm where metacritic_rating is null';

    public function handle()
    {
        $films = VerifiedFilm::whereNull('metacritic_rating')->get();

        foreach ($films as $film) {
            $originalTitle = $film->title;
            $this->info("🔍 Checking Metacritic for: {$originalTitle}");

            // Step 1: Clean title
            $cleanedTitle = preg_replace('/\s*\(\d{4}\)|\d{4}$/', '', $originalTitle); // remove year (2020) or 2020
            $slug = Str::slug($cleanedTitle);
            $url = "https://www.metacritic.com/movie/{$slug}/";

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->timeout(15)->get($url);

                if ($response->status() !== 200) {
                    $this->warn("❌ Not found: {$url}");
                    continue;
                }

                $crawler = new Crawler($response->body());

                // Look for metascore
                $scoreNode = $crawler->filter('.c-siteReviewScore u-flexbox-column span')->first();

                if (!$scoreNode->count()) {
                    // Try another fallback selector
                    $scoreNode = $crawler->filter('.c-siteReviewScore span')->first();
                }

                if ($scoreNode->count()) {
                    $scoreText = trim($scoreNode->text());

                    if (is_numeric($scoreText)) {
                        $film->metacritic_rating = floatval($scoreText);
                        $film->save();

                        $this->info("🎯 Metacritic Score Found: {$scoreText}");
                    } else {
                        $this->warn("❌ Score not numeric: {$scoreText}");
                    }
                } else {
                    $this->warn("❌ Score not found in DOM");
                }
            } catch (\Throwable $e) {
                $this->error("❌ Error for '{$originalTitle}': " . $e->getMessage());
            }

            $this->line(str_repeat('-', 60));
            sleep(1); // Respectful scraping
        }
    }
}
