<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{

    protected $fillable = [
        'title',
        'director',
        'year',
        'genres',
        'festival_awards',
        'imdb_rating',
        'rotten_tomatoes_rating',
        'metacritic_rating',
        'plot_summary',
    ];

    // calculate the average rating and make it to the out of 10
    public function avg_rating($imdb, $tomatoes, $metacritic)
    {
        return ($imdb + ($tomatoes / 10) + ($metacritic / 10)) / 3; // to convert out of 10 rating
    }
}
