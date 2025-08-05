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

   
}
