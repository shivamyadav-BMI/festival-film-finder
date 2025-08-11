<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class FetchMissingFilmDescription extends Command
{
    protected $signature = 'films:scrape-single-description';
    protected $description = 'Scrape Letterboxd film description for a specific film (id=45) from a provided URL';

    public function handle()
    {
        $slug = 'time-to-be-strong/';
        $film = VerifiedFilm::find(545);

        if (!$film) {
            $this->warn("⚠️ Film not found.");
            return;
        }

        $url = 'https://letterboxd.com/film/' . $slug;
        $this->info("🔍 Fetching description for: {$film->title} ({$film->year}) → {$url}");

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ])->get($url);

            if ($response->status() !== 200) {
                $this->warn("❌ Failed to fetch page (status {$response->status()})");
                return;
            }

            $crawler = new Crawler($response->body());

            // ====== DESCRIPTION LOGIC ======
            $descNode = $crawler->filter('section.production-synopsis div.truncate p');
            $description = $descNode->count() ? trim($descNode->text()) : null;

            if (!$description) {
                $this->warn("⚠️ No description found at {$url}.");
            } else {
                $this->info("📖 Description:");
                $this->line($description);
                $this->info("✅ Description fetched.");
            }

            // ====== TRAILER LOGIC ======
            $trailerNode = $crawler->filter('p.trailer-link a');
            $trailerUrl = null;

            if (!$trailerNode->count()) {
                $this->warn("⚠️ No trailer link found.");
            } else {
                $href = $trailerNode->attr('href');

                if (!$href || !str_contains($href, 'youtube.com/embed')) {
                    $this->warn("⚠️ Trailer link not in expected format.");
                } else {
                    $trailerUrl = 'https:' . $href;
                    $this->info("📽️ Trailer URL:");
                    $this->line($trailerUrl);
                    $this->info("✅ Trailer URL fetched.");
                }
            }

            // ====== YEAR & DIRECTOR LOGIC ======
            $pageYear = $crawler->filter('div.productioninfo span.releasedate a')->text('');
            $pageDirector = $crawler->filter('p.credits span.creatorlist a.contributor span')->text('');

            $this->info("📅 Year: " . $pageYear);
            $this->info("🎬 Director: " . $pageDirector);

            // ====== STORE IN DATABASE ======
            $film->update([
                'description'   => $description,
                'trailer_url'   => $trailerUrl ?? '',
                'year'          => $pageYear ?: $film->year,
                'director'      => $pageDirector ?: $film->director,
            ]);

            $this->info("✅ Film details updated in database.");

        } catch (\Exception $e) {
            $this->error("💥 Error fetching description/trailer: " . $e->getMessage());
        }
    }
}
