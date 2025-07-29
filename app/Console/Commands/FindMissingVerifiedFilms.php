<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\VerifiedFilm;

class FindMissingVerifiedFilms extends Command
{
    protected $signature = 'films:check-from-json';
    protected $description = 'Match films from structured films.json (with Title and Director key) against verified_films';

    public function handle()
    {
        $path = 'films.json';

        // ✅ Check file existence in storage/app
        if (!Storage::exists($path)) {
            $this->error("❌ File not found at storage/app/{$path}");
            return;
        }

        // ✅ Read JSON content
        $jsonContent = Storage::get($path);
        $films = json_decode($jsonContent, true);

        if (!is_array($films)) {
            $this->error("❌ Invalid JSON format in {$path}. Expected array of film objects with 'Title' and 'Director' keys.");
            return;
        }

        // ✅ Fetch all verified films and normalize for comparison
        $verified = VerifiedFilm::all()->map(function ($film) {
            return [
                'title' => Str::lower(trim($film->title)),
                'director' => Str::lower(trim($film->director)),
            ];
        });

        $missing = [];

        foreach ($films as $film) {
            if (!isset($film['Title']) || !isset($film['Director'])) {
                $this->warn("⚠️ Skipping invalid entry (missing 'Title' or 'Director'): " . json_encode($film));
                continue;
            }

            $title = Str::lower(trim($film['Title']));
            $director = Str::lower(trim($film['Director']));

            $found = $verified->contains(function ($item) use ($title, $director) {
                return $item['title'] === $title && $item['director'] === $director;
            });

            if (!$found) {
                $missing[] = [
                    'Title' => $film['Title'],
                    'Director' => $film['Director'],
                ];
                $this->warn("❌ Not Found: {$film['Title']} by {$film['Director']}");
            } else {
                $this->info("✅ Found: {$film['Title']} by {$film['Director']}");
            }
        }

        $this->line("\n🎬 Total missing films: " . count($missing));
    }
}
