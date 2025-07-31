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
