<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm;
use Illuminate\Support\Str;

class MissingRatingFilm extends Command
{
    protected $signature = 'films:fetch-missing-ratings';
    protected $description = 'Fetch Rotten Tomatoes and Metacritic ratings from OMDb for VerifiedFilms where ratings are missing';

    public function handle()
    {
        $apiKey = '63f48ad5'; // Replace with your actual API key
        if (!$apiKey) {
            $this->error('OMDb API key is missing');
            return;
        }

        $films = VerifiedFilm::latest()->take(10)->whereNull('rotten_tomatoes_rating')
            ->orWhereNull('metacritic_rating')
            ->get();

        foreach ($films as $film) {
            $this->info("🎬 Processing: {$film->title} ({$film->year})");

            // Fuzzy IMDb search to get IMDb ID
            $imdbId = $this->getImdbIdFromFuzzySearch($film->title);
            if (!$imdbId) {
                $this->warn("❌ IMDb ID not found for: {$film->title}");
                continue;
            }

            $this->info("🔗 IMDb ID found: $imdbId");

            // Fetch from OMDb using IMDb ID
            $response = Http::get("https://www.omdbapi.com/", [
                'apikey' => $apiKey,
                'i' => $imdbId
            ]);

            if ($response->failed() || $response->json('Response') === 'False') {
                $this->warn("❌ OMDb fetch failed for IMDb ID: $imdbId");
                continue;
            }

            $data = $response->json();
            $ratings = collect($data['Ratings'] ?? []);

            $rtValue = null;
            $metaValue = null;

            $rt = $ratings->firstWhere('Source', 'Rotten Tomatoes')['Value'] ?? null;
            if ($rt && preg_match('/(\d+)%/', $rt, $m)) {
                $rtValue = (float) $m[1];
            }

            $meta = $ratings->firstWhere('Source', 'Metacritic')['Value'] ?? null;
            if ($meta && preg_match('/(\d+)\/100/', $meta, $m)) {
                $metaValue = (float) $m[1];
            }

            $film->update([
                'rotten_tomatoes_rating' => $rtValue ?? Null,
                'metacritic_rating' => $metaValue ?? Null,
            ]);

            $this->info("✅ Updated: RT = " . ($rtValue ?? 'N/A') . " | Meta = " . ($metaValue ?? 'N/A'));
            $this->line(str_repeat('-', 50));
            sleep(1);
        }
    }

    private function getImdbIdFromFuzzySearch(string $title): ?string
    {
        $variants = $this->generateTitleVariants($title);

        foreach ($variants as $query) {
            $url = "https://v2.sg.media-imdb.com/suggestion/" . strtolower($query[0]) . "/" . rawurlencode($query) . ".json";
            $response = Http::get($url);

            if (!$response->ok()) continue;

            $results = $response->json('d') ?? [];

            foreach ($results as $result) {
                if (!isset($result['id'], $result['l'])) continue;

                return $result['id']; // Return first valid IMDb ID found
            }
        }

        return null;
    }

    private function generateTitleVariants(string $title): array
    {
        $variants = [];

        $clean = preg_replace('/\\s*\\(.*?\\)/', '', $title);
        $variants[] = trim($clean);
        $variants[] = $title;
        $variants[] = Str::slug($title, ' ');
        $variants[] = str_replace(["'", '’', '-'], '', $title);

        return array_values(array_unique(array_filter($variants)));
    }
}
