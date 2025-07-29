<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VerifiedFilm;

class ScrapeOmdbRatings extends Command
{
    protected $signature = 'films:scrape-omdb';
    protected $description = 'Scrape OMDb Metacritic and Rotten Tomatoes ratings and save to VerifiedFilm table';

    public function handle()
    {
        $apiKey = '63f48ad5';
        if (!$apiKey) {
            $this->error('OMDb API key is missing. Add OMDB_API_KEY in your .env file.');
            return;
        }

        $films = VerifiedFilm::all();

        foreach ($films as $film) {
            $title = $film->title;
            $director = $film->director;

            $searchResponse = Http::get('http://www.omdbapi.com/', [
                'apikey' => $apiKey,
                's' => $title,
                'type' => 'movie'
            ]);

            if (!$searchResponse->ok() || !isset($searchResponse['Search'])) {
                $this->warn("No OMDb results for: {$title}");
                continue;
            }

            foreach ($searchResponse['Search'] as $searchResult) {
                $movieDetails = Http::get('http://www.omdbapi.com/', [
                    'apikey' => $apiKey,
                    'i' => $searchResult['imdbID'],
                    'plot' => 'short'
                ]);

                if (!$movieDetails->ok() || !isset($movieDetails['Director'])) {
                    continue;
                }

                // Check director match
                if (strcasecmp($movieDetails['Director'], $director) === 0) {
                    // Metacritic Rating
                    $metacritic = is_numeric($movieDetails['Metascore']) ? (float) $movieDetails['Metascore'] : null;

                    // Rotten Tomatoes Rating
                    $rotten = null;
                    if (isset($movieDetails['Ratings'])) {
                        foreach ($movieDetails['Ratings'] as $rating) {
                            if ($rating['Source'] === 'Rotten Tomatoes') {
                                $rotten = str_replace('%', '', $rating['Value']);
                                $rotten = is_numeric($rotten) ? (float) $rotten : null;
                                break;
                            }
                        }
                    }

                    if ($metacritic !== null || $rotten !== null) {
                        $film->metacritic_rating = $metacritic;
                        $film->rotten_tomatoes_rating = $rotten;
                        $film->save();

                        $this->info("✅ Updated: {$title}");
                        $this->line("   🎯 Metacritic: " . ($metacritic ?? 'N/A'));
                        $this->line("   🍅 Rotten Tomatoes: " . ($rotten ?? 'N/A'));
                    } else {
                        $this->warn("⚠️  No ratings found for: {$title}");
                    }

                    break; // matched director, skip rest
                }
            }
        }

        $this->info("✅ Scraping and saving completed.");
    }
}
