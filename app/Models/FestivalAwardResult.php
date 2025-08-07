<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalAwardResult extends Model
{
    protected $fillable = [
        'verified_film_id',
        'festival_award_id',
        'festival_edition_id',
        'result',  // Winner / Nominee
    ];

    public function award()
    {
        return $this->belongsTo(FestivalAward::class, 'festival_award_id');
    }

    public function film()
    {
        return $this->belongsTo(VerifiedFilm::class, 'verified_film_id');
    }

    public function edition()
    {
        return $this->belongsTo(FestivalEdition::class, 'festival_edition_id');
    }
}
