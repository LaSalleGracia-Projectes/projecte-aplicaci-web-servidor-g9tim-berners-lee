<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PeliculasSeries;
use App\Models\User;

class Seguimiento extends Model
{
    /** @use HasFactory<\Database\Factories\SeguimientoFactory> */
    use HasFactory;

    protected $table = 'Seguimientos';

    protected $fillable = [
        'user_id',
        'id_pelicula',
        'fecha_seguimiento',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function peliculaSerie()
    {
        return $this->belongsTo(PeliculasSeries::class, 'id_pelicula');
    }
}
