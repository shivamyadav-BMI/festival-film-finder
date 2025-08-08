<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;

class FetchFilmTrailer extends Command
{
    protected $signature = 'films:fetch-letterboxd-trailer';
    protected $description = 'Match and fetch Letterboxd trailer URLs for verified films';

    public function handle()
    {
        $films = VerifiedFilm::skip(300)->take(300)->get();

        if ($films->isEmpty()) {
            $this->warn("⚠️ No verified films found.");
            return;
        }

        $missingFilePath = 'missing-letterboxd-trailers.json';
        $missing = [];

        if (Storage::disk('local')->exists($missingFilePath)) {
            $missing = json_decode(Storage::disk('local')->get($missingFilePath), true) ?? [];
        }

        foreach ($films as $film) {
            $expectedTitle = trim($film->title);
            $expectedYear = (int) $film->year;
            $expectedDirector = trim($film->director);
            $slug = Str::slug($expectedTitle);

            $urlsToTry = [
                "https://letterboxd.com/film/{$slug}-{$expectedYear}/",
                "https://letterboxd.com/film/{$slug}/"
            ];

            $matched = false;

            foreach ($urlsToTry as $url) {
                $this->info("🔍 Trying: {$expectedTitle} ({$expectedYear}) → {$url}");

                try {
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])->get($url);

                    if ($response->status() !== 200) {
                        $this->warn("❌ Failed to fetch page (status {$response->status()})");
                        continue;
                    }

                    $crawler = new Crawler($response->body());

                    $pageTitle = $crawler->filter('h1.headline-1 span.name')->text('');
                    $pageYear = (int) $crawler->filter('div.productioninfo span.releasedate a')->text('');
                    $pageDirector = $crawler->filter('p.credits span.creatorlist a.contributor span')->text('');

                    $titleMatch = Str::lower($expectedTitle) === Str::lower($pageTitle);
                    $yearMatch = $expectedYear === $pageYear;
                    similar_text(Str::lower($expectedDirector), Str::lower($pageDirector), $directorMatchPercent);
                    $directorMatch = $directorMatchPercent >= 80 || str_contains(Str::lower($expectedDirector), Str::lower($pageDirector));

                    $this->table(
                        ['Field', 'Your Data', 'Letterboxd Data', 'Match'],
                        [
                            ['Title', $expectedTitle, $pageTitle, $titleMatch ? '✅' : '❌'],
                            ['Year', $expectedYear, $pageYear, $yearMatch ? '✅' : '❌'],
                            ['Director', $expectedDirector, $pageDirector, $directorMatch ? "✅ ({$directorMatchPercent}%)" : "❌ ({$directorMatchPercent}%)"]
                        ]
                    );

                    if (!($titleMatch && $yearMatch && $directorMatch)) {
                        $this->warn("❌ Film mismatch. Trying next URL if available...");
                        $this->line('');
                        continue;
                    }

                    $trailerNode = $crawler->filter('p.trailer-link a');

                    if (!$trailerNode->count()) {
                        $this->warn("⚠️ No trailer link found.");
                        $this->line('');
                        break;
                    }

                    $href = $trailerNode->attr('href');

                    if (!$href || !Str::contains($href, 'youtube.com/embed')) {
                        $this->warn("⚠️ Trailer link not in expected format.");
                        $this->line('');
                        break;
                    }

                    $trailerUrl = 'https:' . $href;

                    $this->info("📽️ Trailer URL:");
                    $this->line($trailerUrl);
                    $this->line('');

                    // You can optionally save it:
                    $film->update(['trailer_url' => $trailerUrl]);

                    $matched = true;
                    break;
                } catch (\Exception $e) {
                    $this->error("💥 Error at {$url}: " . $e->getMessage());
                    $this->line('');
                    continue;
                }
            }

            if (!$matched) {
                $this->warn("⚠️ No valid Letterboxd page matched for: {$expectedTitle} ({$expectedYear})");
                $missing[] = [
                    'title' => $expectedTitle,
                    'year' => $expectedYear,
                    'director' => $expectedDirector
                ];
            }

            sleep(1); // Be polite
        }

        Storage::disk('local')->put($missingFilePath, json_encode($missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Done fetching trailers.");
    }
}
