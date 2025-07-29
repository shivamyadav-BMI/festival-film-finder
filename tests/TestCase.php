<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}

// <?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Models\Film;
// use Illuminate\Support\Facades\Http;

// class VerifyFilms extends Command
// {
//     protected $signature = 'films:verify-omdb';
//     protected $description = 'Verify film metadata using the OMDb API';

//     public function handle()
//     {
//         $apiKey = '63f48ad5';
//         $films = Film::take(10)->get();

//         $totalScore = 0;
//         $filmCount = 0;

//         foreach ($films as $film) {
//             $this->info("🔍 Searching: {$film->title} ({$film->year}) by {$film->director}");

//             $searchResponse = Http::get("https://www.omdbapi.com/", [
//                 'apikey' => $apiKey,
//                 's' => $film->title,
//                 'type' => 'movie',
//                 'y' => $film->year,
//             ]);

//             if ($searchResponse->failed() || $searchResponse->json('Response') === 'False') {
//                 $this->warn("❌ Not found in search for: {$film->title}");
//                 continue;
//             }

//             $matches = $searchResponse->json('Search', []);
//             $matchedImdbID = null;

//             foreach ($matches as $match) {
//                 $details = Http::get("https://www.omdbapi.com/", [
//                     'apikey' => $apiKey,
//                     'i' => $match['imdbID'],
//                     'plot' => 'short',
//                 ]);

//                 if ($details->ok() && strcasecmp($details['Title'], $film->title) == 0) {
//                     $omdbDirector = $details['Director'] ?? '';
//                     if (stripos($omdbDirector, $film->director) !== false) {
//                         $matchedImdbID = $match['imdbID'];
//                         break;
//                     }
//                 }
//             }

//             if (!$matchedImdbID) {
//                 $this->warn("❌ No accurate match found for: {$film->title}");
//                 continue;
//             }

//             // Fetch full details with imdbID
//             $data = Http::get("https://www.omdbapi.com/", [
//                 'apikey' => $apiKey,
//                 'i' => $matchedImdbID,
//                 'plot' => 'short',
//             ])->json();

//             $filmCount++;
//             $matchScore = 0;
//             $fieldsChecked = 0;

//             $ratings = collect($data['Ratings'] ?? []);

//             $map = [
//                 'Title' => [$film->title, $data['Title'] ?? null],
//                 'Director' => [$film->director, $data['Director'] ?? null],
//                 'Year' => [$film->year, $data['Year'] ?? null],
//                 'Genres' => [$film->genres, $data['Genre'] ?? null],
//                 'IMDB Rating' => [$film->imdb_rating, $data['imdbRating'] ?? null],
//                 'Rotten Tomatoes Rating' => [
//                     $film->rotten_tomatoes_rating,
//                     optional($ratings->firstWhere('Source', 'Rotten Tomatoes'))['Value'] ?? null
//                 ],
//                 'Metacritic Rating' => [
//                     $film->metacritic_rating,
//                     optional($ratings->firstWhere('Source', 'Metacritic'))['Value'] ?? null
//                 ],
//             ];

//             $this->line("🔍 Matching data for: {$film->title}");

//             foreach ($map as $label => [$local, $remote]) {
//                 $match = $this->isCloseMatch($local, $remote);
//                 $status = $match ? '✅' : '❌';
//                 $this->line("$label: $local | OMDb: $remote → $status");

//                 $matchScore += $match ? 1 : 0;
//                 $fieldsChecked++;
//             }

//             // 🎥 Festival Awards Matching (top 3)
//             $this->line("🏆 Verifying Film Festival Awards...");
//             $localFestivals = $this->extractTopFestivals($film->festival_awards);
//             $omdbAwards = strtolower($data['Awards'] ?? '');
//             $omdbProduction = strtolower($data['Production'] ?? '');

//             $festivalMatched = false;

//             foreach ($localFestivals as $festival) {
//                 $normalized = strtolower(trim($festival));
//                 if (stripos($omdbAwards, $normalized) !== false || stripos($omdbProduction, $normalized) !== false) {
//                     $festivalMatched = true;
//                     $this->line("✅ Matched Festival: {$festival}");
//                 } else {
//                     $this->line("❌ Not Matched: {$festival}");
//                 }
//             }

//             // Score for at least one matched festival
//             $matchScore += $festivalMatched ? 1 : 0;
//             $fieldsChecked++;

//             $accuracy = round(($matchScore / $fieldsChecked) * 100, 2);
//             $totalScore += $accuracy;
//             $this->info("🎯 Match Accuracy for '{$film->title}': $accuracy%");
//             $this->line(str_repeat('-', 50));
//         }

//         if ($filmCount > 0) {
//             $overallAccuracy = round($totalScore / $filmCount, 2);
//             $this->info("✅ Overall Matching Accuracy for $filmCount films: $overallAccuracy%");
//         } else {
//             $this->warn("⚠️ No valid film data was processed.");
//         }

//         return Command::SUCCESS;
//     }

//     protected function isCloseMatch($local, $remote): bool
//     {
//         if (is_null($local) || is_null($remote)) return false;

//         $local = strtolower(trim((string) $local));
//         $remote = strtolower(trim((string) $remote));

//         if (preg_match('/^\d+(\.\d+)?$/', $local) && preg_match('/^\d+(\.\d+)?/', $remote)) {
//             return abs(floatval($local) - floatval($remote)) < 0.5;
//         }

//         return similar_text($local, $remote, $percent) && $percent >= 70;
//     }

//     protected function extractTopFestivals($awardsString): array
//     {
//         if (empty($awardsString)) {
//             return [];
//         }

//         $festivals = explode(',', $awardsString);
//         return array_slice(array_map('trim', $festivals), 0, 3);
//     }
// }
