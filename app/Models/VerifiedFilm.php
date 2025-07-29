<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifiedFilm extends Model
{
    protected $guarded = [];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'film_genre', 'verified_film_id', 'genre_id');
    }
}
