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
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'director' => $this->director,
            'year' => $this->year,
            'genres' => GenreResource::collection($this->genres),
            'festival_awards' => $this->festival_awards,
            'poster' => $this->poster,
            'imdb_rating' => $this->imdb_rating,
            'plot_summary' => $this->plot_summary,
            'rotten_tomatoes_rating' => $this->rotten_tomatoes_rating,
            'metacritic_rating' => $this->metacritic_rating,
        ];
    }
}
