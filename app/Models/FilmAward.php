<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmAward extends Model
{
    protected $fillable = [
        'verified_film_id',
        'festival_id',
        'award_year',
        'award_name',
        'award_category',
        'result'
    ];

    // public function film()
    // {
    //     return $this->belongsTo(VerifiedFilm::class);
    // }

    // public function festival()
    // {
    //     return $this->belongsTo(Festival::class);
    // }

}
