<?php

namespace App\Console\Commands;

use App\Models\VerifiedFilm;
use App\Models\Festival;
use App\Models\FestivalEdition;
use App\Models\FestivalAward;
use App\Models\FestivalAwardResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchFestivalAwards extends Command
{
    protected $signature = 'films:fetch-festival-awards';
    protected $description = 'Fetch awards from top festivals for films, grouped by festival tier and release year';

    public function handle()
    {
        $films = VerifiedFilm::take(20)->get();

        if ($films->isEmpty()) {
            $this->warn("⚠️ No films found.");
            return;
        }

        $top3Festivals = ['Cannes', 'Venice', 'Berlin'];
        $bTierFestivals = ['Rotterdam', 'Toronto', 'Locarno', 'Sundance', 'Busan', 'San Sebastian', 'San Sebastián', 'Karlovy Vary'];

        $excludedSections = [
            'ICS Cannes Awards',
            'Toronto Film Critics',
            'International Cinephile Society',
            'New York Film Critics',
            'Los Angeles Film Critics',
            'Boston Online Film Critics',
            'Online Film Critics Society',
            'Chicago Film Critics',
            'National Society of Film Critics',
            'Critics Choice Awards'
        ];

        foreach ($films as $film) {
            $title = $film->title;
            $year = (int) $film->year;
            $director = $film->director ?? 'Unknown Director';

            $this->info("🔍 Searching IMDb for: {$title} ({$year})");

            try {
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
                        (int) $i['y'] === $year
                );

                if (!$match || empty($match['id'])) {
                    $this->error("❌ No exact match found for: {$title}");
                    continue;
                }

                $imdbId = $match['id'];
                $this->info("✅ Found IMDb match → https://www.imdb.com/title/{$imdbId}/awards");

                sleep(2);

                $awardsPage = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->get("https://www.imdb.com/title/{$imdbId}/awards");

                if ($awardsPage->status() !== 200) {
                    $this->error("❌ Could not fetch awards page (status {$awardsPage->status()})");
                    continue;
                }

                $crawler = new Crawler($awardsPage->body());

                $top3Awards = [];
                $bTierAwards = [];
                $seenAwards = [];

                $crawler->filter('#jump-to option')->each(function ($option) use (
                    &$top3Awards,
                    &$bTierAwards,
                    $crawler,
                    $top3Festivals,
                    $bTierFestivals,
                    $excludedSections,
                    &$seenAwards,
                    $film,
                    $year
                ) {
                    $festivalName = trim($option->text());
                    $anchor = $option->attr('value');

                    foreach ($excludedSections as $excluded) {
                        if (stripos($festivalName, $excluded) !== false) return;
                    }

                    $matchedTier = null;
                    if ($this->matchesFestivalName($festivalName, $top3Festivals)) {
                        $matchedTier = 'top3';
                    } elseif ($this->matchesFestivalName($festivalName, $bTierFestivals)) {
                        $matchedTier = 'btier';
                    } else {
                        return;
                    }

                    $sectionId = ltrim($anchor, '#');
                    $section = $crawler->filter("#{$sectionId}");
                    if ($section->count() === 0) return;

                    $parentSection = $section->ancestors()->filter('.ipc-metadata-list')->first()
                        ?? $section->ancestors()->first();

                    $uniqueAwards = [];
                    $parentSection->filter('.ipc-metadata-list-summary-item')->each(function ($node) use (
                        &$uniqueAwards,
                        $festivalName,
                        &$seenAwards,
                        $film,
                        $year
                    ) {
                        $eventText = $node->filter('.ipc-metadata-list-summary-item__t')->text('');
                        $category = $node->filter('.awardCategoryName')->text('');
                        $resultType = str_contains(strtolower($eventText), 'winner') ? 'Winner' : 'Nominee';
                        $awardName = trim(str_replace(['Winner', 'Nominee'], '', $eventText));
                        $awardText = trim("{$eventText} - {$category}");

                        $dedupKey = strtolower("{$festivalName}|{$awardText}");
                        if (!isset($seenAwards[$dedupKey])) {
                            $seenAwards[$dedupKey] = true;

                            $uniqueAwards[] = [
                                'text' => $awardText,
                                'result' => $resultType,
                                'category' => $category,
                                'awardName' => $awardName,
                                'year' => $year
                            ];

                            $this->storeAward($festivalName, $year, $awardName, $category, $resultType, $awardText, $film->id);
                        }
                    });

                    if (!empty($uniqueAwards)) {
                        if ($matchedTier === 'top3') {
                            $top3Awards[$festivalName] = $uniqueAwards;
                        } elseif ($matchedTier === 'btier') {
                            $bTierAwards[$festivalName] = $uniqueAwards;
                        }
                    }
                });

                // Output
                $this->line("");
                $this->info("🎬 Film: {$title} ({$director})");

                if (!empty($top3Awards)) {
                    $this->info("🏆 Top 3 Festivals:");
                    foreach ($top3Awards as $festival => $awards) {
                        $this->line("   {$festival} (" . count($awards) . "):");
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
                        $this->line("   {$festival} (" . count($awards) . "):");
                        foreach ($awards as $award) {
                            $this->line("      • {$award['text']}");
                        }
                    }
                } else {
                    $this->warn("🏆 No B-Tier festival awards found.");
                }

                $this->line("");
                sleep(1);
            } catch (\Exception $e) {
                $this->error("💥 Error processing '{$title}': " . $e->getMessage());
            }
        }

        $this->info("✅ Done fetching awards.");
    }

    private function storeAward($rawFestivalName, $year, $awardName, $category, $result, $notes, $filmId)
    {
        $cleanFestivalName = preg_replace('/\(\d+\)$/', '', $rawFestivalName);
        $cleanFestivalName = trim($cleanFestivalName);

        $festival = Festival::get()->first(function ($fest) use ($cleanFestivalName) {
            return stripos($cleanFestivalName, $fest->name) !== false;
        });

        if (!$festival) {
            $this->warn("⚠️ Festival not found in DB: {$rawFestivalName}");
            return;
        }

        $edition = FestivalEdition::firstOrCreate([
            'festival_id' => $festival->id,
            'year' => $year
        ]);

        $award = FestivalAward::firstOrCreate([
            'festival_id' => $festival->id,
            'name' => $awardName,     // Actual award title
            'category' => $category   // Description / additional info
        ]);

        FestivalAwardResult::updateOrCreate([
            'verified_film_id' => $filmId,
            'festival_award_id' => $award->id,
            'festival_edition_id' => $edition->id,
        ], [
            'result' => $result,
            'notes' => $notes
        ]);
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
