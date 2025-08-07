<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalEdition extends Model
{
    protected $fillable = [
        'festival_id',
        'year',
    ];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    public function awardResults()
    {
        return $this->hasMany(FestivalAwardResult::class);
    }
}
