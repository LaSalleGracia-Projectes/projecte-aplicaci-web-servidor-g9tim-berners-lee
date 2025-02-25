<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PeliculasSeries;
use App\Models\User;

class Valoraciones extends Model
{
    /** @use HasFactory<\Database\Factories\ValoracionesFactory> */
    use HasFactory;

    protected $table = 'Valoraciones';

    protected $fillable = [
        'user_id',
        'id_pelicula',
        'valoracion',
        'fecha_creacion',
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
