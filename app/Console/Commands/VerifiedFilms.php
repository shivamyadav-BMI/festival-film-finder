<?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Str;
    use Symfony\Component\DomCrawler\Crawler;
    use App\Models\Film;
    use App\Models\VerifiedFilm;

    class VerifiedFilms extends Command
    {
        protected $signature = 'app:verify-data';
        protected $description = 'Verify films metadata—including genres, awards, and rating—by scraping IMDb';

        public function handle()
        {
            $films = Film::skip(520)->take(20)->get(); //skip form : 40 take to : //
            $totalMatchPercentage = 0;
            $totalFilms = 0;

            $missingFilePath = storage_path('app/missing_films_latest.json');
            $missingFilms = file_exists($missingFilePath) ? json_decode(file_get_contents($missingFilePath), true) : [];

            foreach ($films as $film) {
                $title = $film->title;
                $director = $film->director;
                $year = $film->year;
                $slug = Str::slug($title);
                $firstLetter = strtolower($slug[0] ?? 'a');

                $suggestUrl = "https://v2.sg.media-imdb.com/suggestion/{$firstLetter}/{$slug}.json";
                $this->info("🔍 Searching IMDb for: {$title} ({$year})");

                try {
                    $raw = Http::get($suggestUrl)->body();
                    $json = preg_replace('/^[^(]+\((.*)\);?$/', '$1', $raw);
                    $data = json_decode($json, true);

                    if (json_last_error() !== JSON_ERROR_NONE || empty($data['d'])) {
                        $this->error("❌ No suggestion results for: {$title}");

                        $missingFilms[] = [
                            'title' => $title,
                            'director' => $director,
                            'year' => $year,
                            'timestamp' => now()->toDateTimeString(),
                        ];
                        file_put_contents($missingFilePath, json_encode($missingFilms, JSON_PRETTY_PRINT));
                        continue;
                    }

                    $match = collect($data['d'])->first(fn($i) =>
                        isset($i['l'], $i['y']) &&
                        mb_strtolower($i['l']) === mb_strtolower($title) &&
                        (int)$i['y'] === (int)$year
                    );

                    if (!$match || empty($match['id'])) {
                        $this->error("❌ No exact match found for: {$title}");

                        $missingFilms[] = [
                            'title' => $title,
                            'year' => $year,
                            'timestamp' => now()->toDateTimeString(),
                        ];
                        file_put_contents($missingFilePath, json_encode($missingFilms, JSON_PRETTY_PRINT));
                        continue;
                    }

                    $imdbId = $match['id'];
                    $poster = $match['i']['imageUrl'] ?? '';
                    $this->info("✅ Found IMDb match → https://www.imdb.com/title/{$imdbId}/");
                    $this->info("🖼️ Poster: {$poster}");

                    sleep(3); // Respect rate limits

                    $page = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ])->get("https://www.imdb.com/title/{$imdbId}/");

                    if ($page->status() !== 200) {
                        $this->error("❌ Could not fetch IMDb page (status {$page->status()})");
                        continue;
                    }

                    $crawler = new Crawler($page->body());

                    // IMDb rating
                    $imdbRating = trim($crawler
                        ->filter('[data-testid="hero-rating-bar__aggregate-rating__score"] span')
                        ->first()
                        ->text('')) ?? 0;

                    // Genres
                    $genres = $crawler->filter('[data-testid="genres"] a')->each(fn($n) => trim($n->text('')));
                    if (empty($genres)) {
                        $genres = $crawler->filter('.ipc-chip-list__scroller a')->each(fn($n) => trim($n->text('')));
                    }
                    $imdbGenres = implode(', ', $genres);

                    // Directors - FIXED to avoid duplicates and incorrect roles
                    $directors = [];

                    $crawler->filter('[data-testid="title-pc-principal-credit"]')->each(function ($node) use (&$directors) {
                        $labelNode = $node->filter('li')->first();
                        $label = strtolower(trim($labelNode->text('')));

                        if (str_contains($label, 'director')) {
                            $people = $node->filter('a[href^="/name/"]')->each(fn($n) => trim($n->text('')));
                            $directors = array_unique($people);
                        }
                    });

                    // Plot summary
                    $plot = optional($crawler->filter('[data-testid="plot-xl"]')->first())->text('') ?? '';

                    // Metacritic Score (optional)
                    $metacriticScore = null;
                    try {
                        $metascore = $crawler->filter('[data-testid="critic-reviews-title"] > div')->first();
                        if ($metascore->count()) {
                            $metacriticScore = (int) trim($metascore->text());
                        }
                    } catch (\Throwable $e) {
                        $metacriticScore = null;
                    }

                    sleep(3); // Before fetching awards

                    // Awards
                    $awardsPage = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ])->get("https://www.imdb.com/title/{$imdbId}/awards");

                    $awardsCrawler = new Crawler($awardsPage->body());

                    $topAwardKeywords = [
                        'Oscar', 'Academy Award', 'Palme d\'Or', 'Cannes',
                        'BAFTA', 'Golden Bear', 'Berlinale', 'Venice', 'Golden Lion'
                    ];

                    $awards = $awardsCrawler->filter('.ipc-metadata-list-summary-item')->each(function ($node) {
                        $event = $node->filter('.ipc-metadata-list-summary-item__t')->text('');
                        $category = $node->filter('.awardCategoryName')->text('');
                        return trim("{$event} - {$category}");
                    });

                    $filteredAwards = array_filter($awards, function ($award) use ($topAwardKeywords) {
                        foreach ($topAwardKeywords as $keyword) {
                            if (stripos($award, $keyword) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });

                    $uniqueAwards = array_slice(array_unique($filteredAwards), 0, 3);
                    $topAwards = implode(' | ', $uniqueAwards);

                    // Matching logic
                    $matchScore = 0;
                    $fieldsChecked = 5;

                    $yourDirectors = array_map('trim', explode(',', $film->director ?? ''));
                    $yourGenres = array_map('trim', explode(',', $film->genres ?? ''));

                    $matchedDirectors = array_intersect($yourDirectors, $directors);
                    $matchedGenres = array_intersect($yourGenres, $genres);

                    if (strcasecmp($film->title, $match['l']) === 0) $matchScore++;
                    if ((int)$film->year === (int)$match['y']) $matchScore++;
                    if (count($matchedDirectors)) $matchScore++;
                    if (count($matchedGenres)) $matchScore++;
                    if (round($film->imdb_rating, 1) == round($imdbRating, 1)) $matchScore++;

                    if ($film->metacritic_rating && $metacriticScore && (int) $film->metacritic_rating === (int) $metacriticScore) {
                        $fieldsChecked++;
                        $matchScore++;
                    }

                    $matchPercentage = round(($matchScore / $fieldsChecked) * 100, 2);
                    $totalMatchPercentage += $matchPercentage;
                    $totalFilms++;

                    $this->table(
                        ['Field', 'Your Data', 'IMDb Data'],
                        [
                            ['Title', $film->title, $match['l']],
                            ['Year', $film->year, $match['y']],
                            ['Director(s)', $film->director, implode(', ', $directors)],
                            ['Genres', $film->genres, $imdbGenres],
                            ['IMDb Rating', $film->imdb_rating, $imdbRating],
                            ['Metacritic Rating', $film->metacritic_rating ?? 'N/A', $metacriticScore ?? 'N/A'],
                            ['Poster', '-', $poster],
                            ['Plot Summary', Str::limit($film->plot_summary, 60), Str::limit($plot, 60)],
                            ['Festival Awards', $film->festival_awards ?? '', $topAwards],
                        ]
                    );

                    $this->info("🎯 Match Score: {$matchPercentage}%");
                    $this->line(str_repeat('-', 100));

                    // Save to VerifiedFilm
                    VerifiedFilm::create([
                        'title' => $match['l'],
                        'year' => $match['y'],
                        'director' => implode(', ', $directors),
                        'genres' => $imdbGenres,
                        'festival_awards' => $topAwards,
                        'poster' => $poster,
                        'imdb_rating' => $imdbRating,
                        'plot_summary' => $plot,
                    ]);

                } catch (\Exception $e) {
                    $this->error("❌ Error processing '{$title}': " . $e->getMessage());
                }
            }

            if ($totalFilms > 0) {
                $averageMatch = round($totalMatchPercentage / $totalFilms, 2);
                $this->info("✅ Total Films Checked: {$totalFilms}");
                $this->info("📊 Average Match Accuracy: {$averageMatch}%");
            } else {
                $this->warn("⚠️ No valid matches processed.");
            }
        }
    }
