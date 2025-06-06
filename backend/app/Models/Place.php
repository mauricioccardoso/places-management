<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Place extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'city',
        'state',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($place) {
            $place->slug = Str::slug($place->name);
        });
    }
}
