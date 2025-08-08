<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchStreamingLinks extends Command
{
    protected $signature = 'movie:tmdb-watch-link';
    protected $description = 'Fetch TMDB watch page and scrape streaming links for verified films';

    public function handle()
    {
        $region = "IN";
        $apiKey = env('TMDB_API_KEY');

        $verifiedFilms = VerifiedFilm::all();

        foreach ($verifiedFilms as $film) {
            $title = $film->title;
            $localDirector = strtolower(trim($film->director ?? ''));

            $this->info("🔍 Searching TMDB for: {$title}");

            // Search TMDB
            $searchResponse = Http::get("https://api.themoviedb.org/3/search/movie", [
                'api_key' => $apiKey,
                'query'   => $title,
                'region'  => $region,
            ]);

            $results = $searchResponse->json('results');

            if (empty($results)) {
                $this->warn("❌ No TMDB movie found for '{$title}'");
                continue;
            }

            $matchedMovie = null;
            $matchedDirectorName = null;

            foreach ($results as $movie) {
                $movieId = $movie['id'];

                $creditsResponse = Http::get("https://api.themoviedb.org/3/movie/{$movieId}/credits", [
                    'api_key' => $apiKey,
                ]);

                $crew = $creditsResponse->json('crew', []);
                $tmdbDirector = collect($crew)->firstWhere('job', 'Director')['name'] ?? null;

                if (!$tmdbDirector) continue;

                $tmdbDirectorLower = strtolower(trim($tmdbDirector));
                $distance = levenshtein($localDirector, $tmdbDirectorLower);

                if ($distance <= 3 || str_contains($tmdbDirectorLower, $localDirector) || str_contains($localDirector, $tmdbDirectorLower)) {
                    $matchedMovie = $movie;
                    $matchedDirectorName = $tmdbDirector;
                    break;
                }
            }

            if (!$matchedMovie) {
                $this->warn("❌ No matching director found for '{$title}' with director '{$film->director}'");
                continue;
            }

            $this->info("✅ Matched Movie: {$matchedMovie['title']} (Director: {$matchedDirectorName})");

            // Fetch TMDB watch page
            $watchUrl = "https://www.themoviedb.org/movie/{$matchedMovie['id']}/watch?translate=false&locale={$region}";
            $this->info("🌐 Fetching streaming providers from: {$watchUrl}");

            $html = Http::get($watchUrl)->body();
            $crawler = new Crawler($html);

            $platformLinks = [];

            $crawler->filter('.ott_provider ul.providers li')->each(function (Crawler $liNode) use (&$platformLinks) {
                $classes = $liNode->attr('class') ?? '';
                $quality = 'unknown';

                if (str_contains($classes, 'ott_filter_4k')) {
                    $quality = '4K';
                } elseif (str_contains($classes, 'ott_filter_hd')) {
                    $quality = 'HD';
                } elseif (str_contains($classes, 'ott_filter_sd')) {
                    $quality = 'SD';
                }

                $aTag = $liNode->filter('a');
                if ($aTag->count() === 0) return;

                $href = $aTag->attr('href');
                $title = $aTag->attr('title');

                if (!$href || !$title) return;

                // Extract and normalize platform
                preg_match('/on (.+)$/i', $title, $matches);
                $rawPlatform = $matches[1] ?? 'Unknown';
                $canonicalPlatform = $this->normalizePlatform($rawPlatform);

                // 🚫 Skip Google Play Movies and YouTube
                if (in_array($canonicalPlatform, ['Google Play Movies', 'YouTube'])) {
                    return;
                }

                // Save best quality per canonical platform
                if (!isset($platformLinks[$canonicalPlatform]) || $this->qualityRank($quality) > $this->qualityRank($platformLinks[$canonicalPlatform]['quality'])) {
                    $platformLinks[$canonicalPlatform] = [
                        'title' => $title,
                        'url' => $href,
                        'quality' => $quality,
                        'rawPlatform' => $rawPlatform,
                    ];
                }
            });

            if (empty($platformLinks)) {
                $this->warn("⚠️  No streaming links found.");
                continue;
            }

            // Prefer Netflix if available
            if (isset($platformLinks['Netflix'])) {
                $this->info("🎥 Netflix link found: " . $platformLinks['Netflix']['url']);
            } else {
                $this->info("🎥 Top-Quality Streaming Links:");
                foreach ($platformLinks as $platform => $data) {
                    $this->line(" - {$data['title']} ({$data['quality']}): {$data['url']}");
                }
            }

            $this->line('');
        }

        return 0;
    }

    private function qualityRank(string $quality): int
    {
        return match (strtolower($quality)) {
            '4k' => 3,
            'hd' => 2,
            'sd' => 1,
            default => 0,
        };
    }

    private function normalizePlatform(string $platform): string
    {
        $platform = strtolower($platform);

        return match (true) {
            str_contains($platform, 'netflix') => 'Netflix',
            str_contains($platform, 'amazon') || str_contains($platform, 'prime video') => 'Amazon Prime Video',
            str_contains($platform, 'mubi') => 'MUBI',
            str_contains($platform, 'disney') => 'Disney+ Hotstar',
            str_contains($platform, 'sony') => 'Sony Liv',
            str_contains($platform, 'zee5') => 'ZEE5',
            str_contains($platform, 'vi ') => 'VI Movies and TV',
            str_contains($platform, 'google') => 'Google Play Movies',
            str_contains($platform, 'youtube') => 'YouTube',
            default => ucwords($platform),
        };
    }
}
