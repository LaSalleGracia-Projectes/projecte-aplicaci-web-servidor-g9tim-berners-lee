<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valoraciones extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'valoracion'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}