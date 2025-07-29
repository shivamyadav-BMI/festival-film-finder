<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm;
use Illuminate\Support\Str;

class VerifyRatings extends Command
{
    protected $signature = 'films:verify-ratings-omdb';
    protected $description = 'Verify ratings by searching OMDb by title and matching title & director';

    public function handle()
    {
        $apiKey = '63f48ad5';
        if (!$apiKey) {
            $this->error('OMDB API key is missing in .env or config/services.php');
            return;
        }

        $films = VerifiedFilm::latest()->take(20)->get();

        foreach ($films as $film) {
            $this->line("🔍 Searching OMDb for: {$film->title}");

            // Step 1: Search by title with retries, timeout, and error handling
            $searchResponse = Http::retry(3, 1000)->timeout(30)->get("https://www.omdbapi.com/", [
                'apikey' => $apiKey,
                's' => $film->title,
                'type' => 'movie',
            ]);

            $searchResponseJson = $searchResponse->json();

            if ($searchResponse->failed() || !isset($searchResponseJson['Search'])) {
                $errorMsg = $searchResponseJson['Error'] ?? 'No search results found';
                $this->warn("❌ No search results found for: {$film->title} | Error: $errorMsg");
                $this->line(str_repeat('-', 50));
                sleep(1); // delay before next request
                continue;
            }

            $found = false;

            foreach ($searchResponseJson['Search'] as $omdbFilm) {
                $imdbID = $omdbFilm['imdbID'];

                // Step 2: Fetch full details using imdbID
                $details = Http::retry(3, 1000)->timeout(30)->get("https://www.omdbapi.com/", [
                    'apikey' => $apiKey,
                    'i' => $imdbID,
                ]);

                if ($details->failed() || $details->json('Response') === 'False') {
                    continue;
                }

                $data = $details->json();

                // Step 3: Match title and director (case insensitive)
                $omdbTitle = Str::lower($data['Title']);
                $omdbDirector = Str::lower($data['Director']);
                $localTitle = Str::lower($film->title);
                $localDirector = Str::lower($film->director);

                if ($omdbTitle === $localTitle && Str::contains($omdbDirector, $localDirector)) {
                    $this->info("🎬 Match Found: {$data['Title']} ({$data['Year']})");

                    $ratings = collect($data['Ratings'] ?? []);

                    // Extract rating values as numbers only (no % or /100)
                    $imdb = $ratings->firstWhere('Source', 'Internet Movie Database')['Value'] ?? null;
                    if ($imdb && preg_match('/([\d\.]+)\/10/', $imdb, $matches)) {
                        $imdbValue = (float) $matches[1];
                    } else {
                        $imdbValue = null;
                    }

                    $rt = $ratings->firstWhere('Source', 'Rotten Tomatoes')['Value'] ?? null;
                    if ($rt && preg_match('/(\d+)%/', $rt, $matches)) {
                        $rtValue = (float) $matches[1];
                    } else {
                        $rtValue = null;
                    }

                    $meta = $ratings->firstWhere('Source', 'Metacritic')['Value'] ?? null;
                    if ($meta && preg_match('/(\d+)\/100/', $meta, $matches)) {
                        $metaValue = (float) $matches[1];
                    } else {
                        $metaValue = null;
                    }

                    // Show ratings, replacing null with 0 for DB display consistency
                    $imdbDisplay = $imdbValue ?? 0;
                    $rtDisplay = $rtValue ?? 0;
                    $metaDisplay = $metaValue ?? 0;

                    $this->line("⭐ IMDb Rating:        DB: {$film->imdb_rating} | OMDb: $imdb");
                    $this->line("🍅 Rotten Tomatoes:   DB: {$film->rotten_tomatoes_rating} | OMDb: " . ($rtValue !== null ? $rtValue : '❌'));
                    $this->line("🎯 Metacritic:        DB: {$film->metacritic_rating} | OMDb: " . ($metaValue !== null ? $metaValue : '❌'));

                    // Calculate and show percentage difference for RT and Metacritic only
                    $rtPercentDiff = $film->rotten_tomatoes_rating > 0
                        ? abs($film->rotten_tomatoes_rating - $rtDisplay) / $film->rotten_tomatoes_rating * 100
                        : null;
                    $metaPercentDiff = $film->metacritic_rating > 0
                        ? abs($film->metacritic_rating - $metaDisplay) / $film->metacritic_rating * 100
                        : null;

                    if ($rtPercentDiff !== null) {
                        $this->line("📊 Rotten Tomatoes Difference: " . round($rtPercentDiff, 2) . "%");
                    } else {
                        $this->line("📊 Rotten Tomatoes Difference: N/A");
                    }

                    if ($metaPercentDiff !== null) {
                        $this->line("📊 Metacritic Difference: " . round($metaPercentDiff, 2) . "%");
                    } else {
                        $this->line("📊 Metacritic Difference: N/A");
                    }

                    // Update DB ratings with 0 if null
                    $film->update([
                        'rotten_tomatoes_rating' => $rtValue ?? 0,
                        'metacritic_rating' => $metaValue ?? 0,
                    ]);

                    $this->line(str_repeat('-', 50));
                    $found = true;
                    break; // Exit loop after match
                }
            }

            if (!$found) {
                $this->warn("⚠️ No match with title+director found for '{$film->title}'");
                $this->line(str_repeat('-', 50));
            }

            sleep(1); // delay to avoid hitting rate limits
        }
    }
}
