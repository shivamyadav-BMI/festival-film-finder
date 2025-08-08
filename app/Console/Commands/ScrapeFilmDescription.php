<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;

class ScrapeFilmDescription extends Command
{
    protected $signature = 'films:match-letterboxd-description';
    protected $description = 'Match and scrape Letterboxd film descriptions from dynamic URL (with fallback)';

    public function handle()
    {
        $films = VerifiedFilm::skip(500)->take(100)->get();

        if ($films->isEmpty()) {
            $this->warn("⚠️ No verified films found.");
            return;
        }

        $missingFilePath = 'missing-letterboxd-films.json';
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

                    $descNode = $crawler->filter('section.production-synopsis div.truncate p');
                    $description = $descNode->count() ? trim($descNode->text()) : null;

                    if (!$description) {
                        $this->warn("⚠️ No description found.");
                        $this->line('');
                        break;
                    }

                    $this->info("📖 Description:");
                    $this->line($description);
                    $this->line('');

                    $film->update([
                        'description' => $description
                    ]);

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

        $this->info("✅ Done matching descriptions.");
    }
}
