<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'director' => $this->director,
            'trailer' => $this->trailer_url,
            'description' => $this->description,
            'year' => $this->year,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),

            // Group by festival -> editions -> awards
            'festivals' => $this->whenLoaded('festivals', function () {
                return $this->festivals->map(function ($festival) {

                    // Calculate wins and nominations for the festival
                    $winCount = 0;
                    $nomCount = 0;
                    foreach ($festival->editions as $edition) {
                        foreach ($edition->awardResults as $awardResult) {
                            if (strtolower($awardResult->result) === 'winner') {
                                $winCount++;
                            } elseif (strtolower($awardResult->result) === 'nominee') {
                                $nomCount++;
                            }
                        }
                    }

                    return [
                        'id' => $festival->id,
                        'name' => $festival->name,
                        'win_count' => $winCount,
                        'nomination_count' => $nomCount,

                        'editions' => $festival->editions
                            ->sortByDesc('year')
                            ->map(function ($edition) {
                                return [
                                    'year' => $edition->year,
                                    'awards' => $edition->awardResults->map(function ($awardResult) {
                                        return [
                                            'result' => $awardResult->result,
                                            'award_name' => $awardResult->award?->name,
                                            'award_category' => $awardResult->award?->category,
                                            'film_title' => $awardResult->film?->title,
                                            'film_director' => $awardResult->film?->director,
                                            'film_poster' => $awardResult->film?->poster,
                                            'notes' => $awardResult->notes,
                                        ];
                                    }),
                                ];
                            })
                            ->values(),
                    ];
                });
            }),

            'poster' => $this->poster,
            'imdb_rating' => $this->imdb_rating,
            'plot_summary' => $this->plot_summary,
            'rotten_tomatoes_rating' => $this->rotten_tomatoes_rating,
            'metacritic_rating' => $this->metacritic_rating,
            'average_rating' => $this->avg_rating(
                $this->imdb_rating ?? 0,
                $this->rotten_tomatoes_rating ?? 0,
                $this->metacritic_rating ?? 0
            ),
        ];
    }
}
