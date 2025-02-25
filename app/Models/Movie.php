<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'banner_url',
        'poster_url',
        'description',
        'year',
        'release_date',
        'tmdb_rating',
        // Agrega otros campos que necesites.
    ];

    // Aquí puedes definir relaciones, por ejemplo, con géneros, actores, etc.
}
