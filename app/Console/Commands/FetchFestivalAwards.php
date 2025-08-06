<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchFestivalAwards extends Command
{
    protected $signature = 'films:fetch-festival-awards';
    protected $description = 'Fetch awards from top festivals for films, grouped by festival tier and release year';

    public function handle()
    {
        // Manual array for single film
        // $films = collect([
        //     (object)[
        //         'title' => 'Village Rockstars 2',
        //         'year' => 2024,
        //         'director' => null
        //     ]
        // ]);
        $films = VerifiedFilm::where('id', 152)->get();
        if ($films->isEmpty()) {
            $this->warn("⚠️ No films found.");
            return;
        }

        $top3Festivals = ['Venice', 'Cannes', 'Berlin'];
        $bTierFestivals = ['Rotterdam', 'Toronto', 'Locarno', 'Sundance', 'Busan', 'San Sebastian', 'San Sebastián', 'Karlovy Vary'];

        foreach ($films as $film) {
            $title = $film->title;
            $year  = (int) $film->year;
            $director = $film->director ?? 'Unknown Director';

            $this->info("🔍 Searching IMDb for: {$title} ({$year})");

            try {
                // IMDb Search
                $slug = \Illuminate\Support\Str::slug($title);
                $firstLetter = strtolower($slug[0] ?? 'a');
                $suggestUrl = "https://v2.sg.media-imdb.com/suggestion/{$firstLetter}/{$slug}.json";

                $raw = Http::get($suggestUrl)->body();
                $json = preg_replace('/^[^(]+\((.*)\);?$/', '$1', $raw);
                $data = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE || empty($data['d'])) {
                    $this->error("❌ No suggestion results for: {$title}");
                    continue;
                }

                $match = collect($data['d'])->first(
                    fn($i) =>
                        isset($i['l'], $i['y']) &&
                        mb_strtolower($i['l']) === mb_strtolower($title) &&
                        (int)$i['y'] === $year
                );

                if (!$match || empty($match['id'])) {
                    $this->error("❌ No exact match found for: {$title}");
                    continue;
                }

                $imdbId = $match['id'];
                $this->info("✅ Found IMDb match → https://www.imdb.com/title/{$imdbId}/awards");

                sleep(2);

                // Fetch awards page
                $awardsPage = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->get("https://www.imdb.com/title/{$imdbId}/awards");

                if ($awardsPage->status() !== 200) {
                    $this->error("❌ Could not fetch awards page (status {$awardsPage->status()})");
                    continue;
                }

                $crawler = new Crawler($awardsPage->body());

                // Grouped arrays
                $top3Awards = [];
                $bTierAwards = [];
                $releaseYearAwards = [];

                // Loop through dropdown options (festivals)
                $crawler->filter('#jump-to option')->each(function ($option) use (&$top3Awards, &$bTierAwards, &$releaseYearAwards, $top3Festivals, $bTierFestivals, $crawler, $year) {
                    $festivalName = trim($option->text());
                    $festivalAnchor = $option->attr('value');

                    $section = $crawler->filter($festivalAnchor);
                    if ($section->count() === 0) {
                        return;
                    }

                    $parentSection = $section->ancestors()->filter('.ipc-metadata-list');
                    if ($parentSection->count() === 0) {
                        $parentSection = $section->ancestors()->first();
                    }

                    $awardsForFestival = [];
                    $parentSection->filter('.ipc-metadata-list-summary-item')->each(function ($node) use (&$awardsForFestival, $year) {
                        $eventText = $node->filter('.ipc-metadata-list-summary-item__t')->text('');
                        $category  = $node->filter('.awardCategoryName')->text('');
                        $awardText = trim("{$eventText} - {$category}");

                        preg_match('/\b(19|20)\d{2}\b/', $eventText, $yearMatches);
                        $awardYear = isset($yearMatches[0]) ? (int)$yearMatches[0] : null;

                        $awardsForFestival[] = [
                            'text' => $awardText,
                            'year' => $awardYear
                        ];
                    });

                    // Assign to correct group
                    if ($this->matchesFestivalName($festivalName, $top3Festivals)) {
                        $top3Awards[$festivalName] = $awardsForFestival;
                    } elseif ($this->matchesFestivalName($festivalName, $bTierFestivals)) {
                        $bTierAwards[$festivalName] = $awardsForFestival;
                    }

                    // Add release year awards grouped by festival
                    $yearSpecificAwards = array_filter($awardsForFestival, fn($a) => $a['year'] === $year);
                    if (!empty($yearSpecificAwards)) {
                        $releaseYearAwards[$festivalName] = $yearSpecificAwards;
                    }
                });

                // Output
                $this->line("");
                $this->info("🎬 Film: {$title} ({$director})");

                if (!empty($top3Awards)) {
                    $this->info("🏆 Top 3 Festivals:");
                    foreach ($top3Awards as $festival => $awards) {
                        $this->line("   {$festival}:");
                        foreach ($awards as $award) {
                            $this->line("      • {$award['text']}");
                        }
                    }
                } else {
                    $this->warn("🏆 No Top 3 festival awards found.");
                }

                if (!empty($bTierAwards)) {
                    $this->info("🏆 B-Tier Festivals:");
                    foreach ($bTierAwards as $festival => $awards) {
                        $this->line("   {$festival}:");
                        foreach ($awards as $award) {
                            $this->line("      • {$award['text']}");
                        }
                    }
                } else {
                    $this->warn("🏆 No B-Tier festival awards found.");
                }

                if (!empty($releaseYearAwards)) {
                    $this->info("📅 Awards in {$year}:");
                    foreach ($releaseYearAwards as $festival => $awards) {
                        $this->line("   {$festival}:");
                        foreach ($awards as $award) {
                            $this->line("      • {$award['text']}");
                        }
                    }
                } else {
                    $this->warn("📅 No awards found for release year {$year}.");
                }

                $this->line("");
                sleep(1);
            } catch (\Exception $e) {
                $this->error("💥 Error processing '{$title}': " . $e->getMessage());
            }
        }

        $this->info("✅ Done fetching awards.");
    }

    private function matchesFestivalName($name, $list)
    {
        foreach ($list as $festival) {
            if (stripos($name, $festival) !== false) {
                return true;
            }
        }
        return false;
    }
}
