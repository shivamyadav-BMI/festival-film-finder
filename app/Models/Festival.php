<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    protected $fillable = [
        'name',
    ];


    public function editions()
    {
        return $this->hasMany(FestivalEdition::class);
    }

    public function awards()
    {
        return $this->hasMany(FestivalAward::class);
    }
}
