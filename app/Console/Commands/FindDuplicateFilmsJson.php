<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\VerifiedFilm;
use Illuminate\Support\Str;

class FindDuplicateFilmsJson extends Command
{
    protected $signature = 'films:find-missing-data';
    protected $description = 'Find films from excel-to-json.json that are missing in the VerifiedFilm table';

    public function handle()
    {
        $path = storage_path('app/excel-to-json.json');

        if (!File::exists($path)) {
            $this->error("❌ File not found: excel-to-json.json");
            return Command::FAILURE;
        }

        $json = json_decode(File::get($path), true);

        if (!$json || !is_array($json)) {
            $this->error("❌ Invalid or malformed JSON file.");
            return Command::FAILURE;
        }

        $verifiedTitles = VerifiedFilm::pluck('title')
            ->map(fn($title) => Str::lower(trim($title)))
            ->unique();

        $missing = [];
        $seen = [];
        $duplicates = 0;

        foreach ($json as $film) {
            $title = $film['Title'] ?? null;

            if (!$title) continue;

            $normalized = Str::lower(trim($title));

            // Skip duplicates
            if (isset($seen[$normalized])) {
                $duplicates++;
                continue;
            }

            $seen[$normalized] = true;

            // Exact match first
            if ($verifiedTitles->contains($normalized)) {
                continue;
            }

            // Fuzzy match fallback
            $foundFuzzy = $verifiedTitles->first(function ($dbTitle) use ($normalized) {
                similar_text($dbTitle, $normalized, $percent);
                return $percent > 90;
            });

            if (!$foundFuzzy) {
                $missing[] = $title;
            }
        }

        // Output
        $this->info("✅ Total JSON entries: " . count($json));
        $this->info("✅ Unique JSON titles: " . count($seen));
        $this->info("✅ VerifiedFilm entries: " . $verifiedTitles->count());
        $this->info("✅ Duplicates skipped: $duplicates");
        $this->warn("⚠️  Missing films: " . count($missing));

        foreach ($missing as $title) {
            $this->line(" - $title");
        }

        return Command::SUCCESS;
    }
}
