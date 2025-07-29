<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VerifiedFilm;
use Illuminate\Support\Facades\Storage;

class GenerateVerifiedFilmsJson extends Command
{
    protected $signature = 'films:export-verified';
    protected $description = 'Export all verified films to storage/app/verified_films_from_table.json excluding timestamps';

    public function handle()
    {
        $this->info("📤 Exporting VerifiedFilm records...");

        $films = VerifiedFilm::all();

        $output = [];

        foreach ($films as $film) {
            $output[] = [
                'title' => $film->title ?? null,
                'director' => $film->director ?? null,
                'year' => $film->year ?? null,
                'genres' => $film->genres ?? null,
                'festival_awards' => $film->festival_awards ?? null,
                'poster' => $film->poster ?? null,
                'imdb_rating' => $film->imdb_rating ?? null,
                'rotten_tomatoes_rating' => $film->rotten_tomatoes_rating ?? null,
                'metacritic_rating' => $film->metacritic_rating ?? null,
                'plot_summary' => $film->plot_summary ?? null,
            ];
        }

        $jsonPath = 'verified_films_from_table.json';
        Storage::put($jsonPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


        $this->info("✅ Exported " . count($output) . " films to storage/app/$jsonPath");
    }
}
