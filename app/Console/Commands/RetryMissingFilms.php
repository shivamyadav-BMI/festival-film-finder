<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetryMissingFilms extends Command
{
    protected $signature = 'films:fetch-missing-metadata';
    protected $description = 'Fetch full metadata for films in missing_films.json using IMDb + OMDb';

    public function handle()
    {
        $apiKey = '63f48ad5'; // Your OMDb API key
        $jsonPath = storage_path('app/missing_films.json');

        if (!file_exists($jsonPath)) {
            $this->error("missing_films.json not found.");
            return 1;
        }

        $films = json_decode(file_get_contents($jsonPath), true);
        if (empty($films)) {
            $this->warn("No films in missing_films.json");
            return 1;
        }

        foreach ($films as $film) {
            $title = $film['title'] ?? null;
            $year = (int)($film['year'] ?? 0);
            $expectedDirector = Str::lower($film['director'] ?? '');

            if (!$title) continue;

            $this->line("🎬 Searching: $title ($year)");

            $imdbId = $this->getMatchingImdbId($title, $year, $expectedDirector);
            if (!$imdbId) {
                $this->warn("❌ No IMDb match found for: $title");
                continue;
            }

            $omdb = Http::get('https://www.omdbapi.com/', [
                'apikey' => $apiKey,
                'i' => $imdbId
            ]);

            if (!$omdb->ok() || $omdb->json('Response') === 'False') {
                $this->warn("❌ OMDb fetch failed for: $imdbId");
                continue;
            }

            $data = $omdb->json();
            $ratings = collect($data['Ratings'] ?? []);
            $rt = $ratings->firstWhere('Source', 'Rotten Tomatoes')['Value'] ?? null;
            $meta = $ratings->firstWhere('Source', 'Metacritic')['Value'] ?? null;

            $rtValue = $rt && preg_match('/(\d+)%/', $rt, $m) ? (int)$m[1] : null;
            $metaValue = $meta && preg_match('/(\d+)\/100/', $meta, $m) ? (int)$m[1] : null;

            // Output
            $this->info("✅ Found:");
            $this->line("  Title     : " . ($data['Title'] ?? 'N/A'));
            $this->line("  Year      : " . ($data['Year'] ?? 'N/A'));
            $this->line("  Director  : " . ($data['Director'] ?? 'N/A'));
            $this->line("  IMDb ID   : " . $imdbId);
            $this->line("  IMDb Rating : " . ($data['imdbRating'] ?? 'N/A'));
            $this->line("  Rotten Tomatoes : " . ($rtValue ?? 'N/A'));
            $this->line("  Metacritic      : " . ($metaValue ?? 'N/A'));
            $this->line(str_repeat('-', 60));

            sleep(1);
        }

        return 0;
    }

    private function getMatchingImdbId(string $title, ?int $year, ?string $expectedDirector = null): ?string
    {
        $variants = $this->generateTitleVariants($title);

        foreach ($variants as $query) {
            $url = "https://v2.sg.media-imdb.com/suggestion/" . strtolower($query[0]) . "/" . rawurlencode($query) . ".json";
            $response = Http::get($url);

            if (!$response->ok()) continue;
            $results = $response->json('d') ?? [];

            foreach ($results as $result) {
                if (!isset($result['id'], $result['l'])) continue;
                if (!str_starts_with($result['id'], 'tt')) continue;

                similar_text(Str::lower($result['l']), Str::lower($title), $percent);
                $yearMatch = isset($result['y']) && $year ? abs($result['y'] - $year) <= 1 : true;
                $directorMatch = true;

                // No direct director check via suggestion API, will validate later in OMDb
                if ($percent >= 80 && $yearMatch) {
                    return $result['id'];
                }
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
