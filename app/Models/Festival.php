<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    protected $fillable = ['name', 'tier', 'location'];

    public function awards()
    {
        return $this->hasMany(FilmAward::class);
    }

    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_awards')
            ->withPivot(['award_year', 'award_name', 'award_category', 'result'])
            ->withTimestamps();
    }
}
