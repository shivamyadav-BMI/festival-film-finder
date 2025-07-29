<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FindMissingFilmsInJson extends Command
{
    protected $signature = 'films:find-missing-from-json';
    protected $description = 'Find films in excel-to-json.json that are missing in films.json by title (with fuzzy matching)';

    public function handle()
    {
        $excelJsonPath = storage_path('app/excel-to-json.json');
        $filmsJsonPath = storage_path('app/films.json');

        $this->info("🔄 Loading excel-to-json.json...");
        $excelFilms = json_decode(file_get_contents($excelJsonPath), true);
        $excelTitles = array_filter(array_map(fn($f) => trim($f['Title'] ?? ''), $excelFilms));
        $excelTitles = array_unique($excelTitles);

        $this->info("🔄 Loading films.json...");
        $films = json_decode(file_get_contents($filmsJsonPath), true);
        $filmTitles = array_filter(array_map(fn($f) => trim($f['Title'] ?? ''), $films));
        $filmTitles = array_unique($filmTitles);

        $this->info("📊 Total titles in excel-to-json.json: " . count($excelTitles));
        $this->info("📊 Total titles in films.json: " . count($filmTitles));

        $missing = [];

        foreach ($excelTitles as $title) {
            $normalizedTitle = Str::lower(preg_replace('/\s*\(.*?\)\s*/', '', $title));

            $found = collect($filmTitles)->contains(function ($filmTitle) use ($normalizedTitle) {
                $normalizedFilm = Str::lower(preg_replace('/\s*\(.*?\)\s*/', '', $filmTitle));
                return $normalizedTitle === $normalizedFilm;
            });

            // Fuzzy match if not found
            if (!$found) {
                $found = collect($filmTitles)->contains(function ($filmTitle) use ($normalizedTitle) {
                    $normalizedFilm = Str::lower(preg_replace('/\s*\(.*?\)\s*/', '', $filmTitle));
                    similar_text($normalizedTitle, $normalizedFilm, $percent);
                    return $percent >= 90;
                });
            }

            if (!$found) {
                $missing[] = $title;
            }
        }

        $this->newLine();
        $this->warn("❌ Total missing films: " . count($missing));

        foreach ($missing as $title) {
            $this->line("- " . $title);
        }

        if (count($missing) === 0) {
            $this->info("🎉 All films from excel-to-json exist in films.json");
        }

        return Command::SUCCESS;
    }
}
