<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VerifiedFilm extends Model
{
    protected $guarded = [];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'film_genre', 'verified_film_id', 'genre_id');
    }

    public function awardResults()
    {
        return $this->hasMany(FestivalAwardResult::class);
    }

    public function festivals()
    {
        return $this->belongsToMany(Festival::class, 'festival_award_results', 'verified_film_id', 'festival_award_id')
            ->withPivot(['festival_award_id', 'festival_edition_id', 'result']);
    }


    // calculate the average rating and make it to the out of 10
    public function avg_rating($imdb, $tomatoes, $metacritic)
    {
        return ($imdb + ($tomatoes / 10) + ($metacritic / 10)) / 3; // to convert out of 10 rating
    }

    // query scopes
    public function scopeFilterBySort(Builder $query, $sortBy): Builder
    {
        return $query->when(
            in_array(strtolower($sortBy), ['asc', 'desc']),
            fn($q) => $q->orderBy('imdb_rating', $sortBy)
        );
    }

    public function scopeFilterBySearch(Builder $query, $search): Builder
    {
        return $query->when($search, function ($q) use ($search) {
            $q->whereAny(['title', 'director'], 'LIKE', "%{$search}%");
        });
    }


    public function scopeFilterByGenre(Builder $query, Genre $genre): Builder
    {
        return $query->when($genre, function ($q) use ($genre) {
            $q->whereHas('genres', fn($q) => $q->where('slug', $genre->slug));
        });
    }
}
