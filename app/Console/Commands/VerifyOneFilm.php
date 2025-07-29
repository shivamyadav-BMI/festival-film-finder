<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\VerifiedFilm;
use Illuminate\Support\Str;

class VerifyOneFilm extends Command
{
    protected $signature = 'app:verify-one-film';
    protected $description = 'Scrape and save metadata for a specific IMDb film';

    public function handle()
    {
        $imdbId = 'tt5531994'; // From https://www.imdb.com/title/tt15424716/
        $url = "https://www.imdb.com/title/{$imdbId}/";

        $this->info("🔍 Fetching IMDb page: $url");

        $page = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get($url);

        if ($page->status() !== 200) {
            $this->error("❌ Could not fetch IMDb page (status {$page->status()})");
            return;
        }

        $crawler = new Crawler($page->body());

        // Title
        $title = trim($crawler->filter('h1[data-testid="hero-title-block__title"]')->text(''));

        // Year
        $year = (int) $crawler->filter('ul[data-testid="hero-title-block__metadata"] li')->first()->text('');

        // IMDb rating
        $imdbRating = trim($crawler
            ->filter('[data-testid="hero-rating-bar__aggregate-rating__score"] span')
            ->first()
            ->text('')) ?? 0;

        // Poster
        $poster = optional($crawler->filter('.ipc-media img')->first())->attr('src') ?? '';

        // Genres
        $genres = $crawler->filter('[data-testid="genres"] a')->each(fn($n) => trim($n->text('')));
        if (empty($genres)) {
            $genres = $crawler->filter('.ipc-chip-list__scroller a')->each(fn($n) => trim($n->text('')));
        }
        $imdbGenres = implode(', ', $genres);

        // Directors
        $directors = [];
        $crawler->filter('[data-testid="title-pc-principal-credit"]')->each(function ($node) use (&$directors) {
            $labelNode = $node->filter('li')->first();
            $label = strtolower(trim($labelNode->text('')));
            if (str_contains($label, 'director')) {
                $people = $node->filter('a[href^="/name/"]')->each(fn($n) => trim($n->text('')));
                $directors = array_unique($people);
            }
        });

        // Plot summary
        $plot = optional($crawler->filter('[data-testid="plot-xl"]')->first())->text('') ?? '';

        // Metacritic score
        $metacriticScore = null;
        try {
            $metascore = $crawler->filter('[data-testid="critic-reviews-title"] > div')->first();
            if ($metascore->count()) {
                $metacriticScore = (int) trim($metascore->text());
            }
        } catch (\Throwable $e) {
            $metacriticScore = null;
        }

        // Sleep before award fetch
        sleep(3);

        // Awards

        // Awards
        $awardsPage = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get("https://www.imdb.com/title/{$imdbId}/awards");

        $awardsCrawler = new Crawler($awardsPage->body());

        $topAwardKeywords = [
            'Oscar',
            'Academy Award',
            'Palme d\'Or',
            'Cannes',
            'BAFTA',
            'Golden Bear',
            'Berlinale',
            'Venice',
            'Golden Lion'
        ];

        $awards = $awardsCrawler->filter('.ipc-metadata-list-summary-item')->each(function ($node) {
            $event = $node->filter('.ipc-metadata-list-summary-item__t')->text('');
            $category = $node->filter('.awardCategoryName')->text('');
            return trim("{$event} - {$category}");
        });

        $filteredAwards = array_filter($awards, function ($award) use ($topAwardKeywords) {
            foreach ($topAwardKeywords as $keyword) {
                if (stripos($award, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        });

        $uniqueAwards = array_slice(array_unique($filteredAwards), 0, 3);
        $topAwards = implode(' | ', $uniqueAwards);

        // Save to VerifiedFilm
        VerifiedFilm::create([
            'title' => $title,
            'year' => $year,
            'director' => implode(', ', $directors),
            'genres' => $imdbGenres,
            'festival_awards' => $topAwards,
            'poster' => $poster,
            'imdb_rating' => $imdbRating,
            'metacritic_rating' => $metacriticScore,
            'plot_summary' => $plot,
        ]);

        $this->info("✅ Saved VerifiedFilm: {$title} ({$year})");
    }
}
