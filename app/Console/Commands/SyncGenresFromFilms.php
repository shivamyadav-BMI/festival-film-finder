<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VerifiedFilm;
use App\Models\Genre;

class SyncGenresFromFilms extends Command
{
    protected $signature = 'films:sync-genres';
    protected $description = 'Sync unique genres from verified films and associate them properly.';

    public function handle()
    {
        $this->info("Starting genre sync...");

        $films = VerifiedFilm::all();
        $addedGenres = 0;
        $linkedGenres = 0;

        foreach ($films as $film) {
            if (!$film->genres) continue;

            $genres = collect(explode(',', $film->genres))
                ->map(fn($genre) => trim($genre))
                ->filter()
                ->unique();

            foreach ($genres as $genreName) {
                // Get or create the genre (globally unique)
                $genre = Genre::firstOrCreate(['name' => $genreName]);

                // Attach genre to film if not already attached
                if (!$film->genres()->where('genre_id', $genre->id)->exists()) {
                    $film->genres()->attach($genre->id);
                    $linkedGenres++;
                }
            }
        }

        $this->info("Genre sync complete. Linked genres: $linkedGenres");
        return Command::SUCCESS;
    }
}
