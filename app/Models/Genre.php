<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'name',
    ];

    public function films()
    {
        return $this->belongsToMany(VerifiedFilm::class, 'film_genre', 'genre_id', 'verified_film_id');
    }
}
