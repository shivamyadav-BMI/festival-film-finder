<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckVerifiedFilms extends Command
{
    protected $signature = 'films:check-verified';
    protected $description = 'Check if all verified films are unique and how many are missing from the main films table';

    public function handle()
    {
        // Get all verified films with title and director
        $verifiedFilms = DB::table('verified_films')->select('title', 'director')->get();

        // Group by title + director
        $grouped = $verifiedFilms->groupBy(function ($item) {
            return strtolower($item->title . '|' . $item->director);
        });

        // Filter duplicates (count > 1)
        $duplicates = $grouped->filter(function ($group) {
            return $group->count() > 1;
        });

        $totalDuplicates = $duplicates->count();

        if ($totalDuplicates > 0) {
            $this->warn("❌ Found {$totalDuplicates} duplicate verified film entries based on title + director:");
            $this->line(str_pad('Title', 40) . str_pad('Director', 30) . 'Count');
            $this->line(str_repeat('-', 80));
            foreach ($duplicates as $group) {
                $film = $group->first();
                $this->line(
                    str_pad($film->title, 40) .
                    str_pad($film->director, 30) .
                    $group->count()
                );
            }
        } else {
            $this->info("✅ No duplicate verified film entries found based on title and director.");
        }

        // Check how many films are missing from verified list
        $missingCount = DB::table('films')
            ->whereNotIn('title', DB::table('verified_films')->pluck('title'))
            ->count();

        $this->info("📦 Missing films from verified list: {$missingCount}");

        // ✅ Show title + director of missing films
        $missingFilms = DB::table('films')
            ->select('title', 'director')
            ->whereNotIn('title', DB::table('verified_films')->pluck('title'))
            ->get();

        if ($missingFilms->isNotEmpty()) {
            $this->line('');
            $this->warn("🔍 Missing Film Details:");
            $this->line(str_pad('Title', 40) . 'Director');
            $this->line(str_repeat('-', 70));
            foreach ($missingFilms as $film) {
                $this->line(str_pad($film->title, 40) . $film->director);
            }
        }
    }
}
