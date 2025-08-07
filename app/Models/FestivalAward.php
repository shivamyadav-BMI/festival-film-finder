<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestivalAward extends Model
{
    protected $fillable = [
        'festival_id',
        'name',
        'category'
    ];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    public function results()
    {
        return $this->hasMany(FestivalAwardResult::class);
    }
}
