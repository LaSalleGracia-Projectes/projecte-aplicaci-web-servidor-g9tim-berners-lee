<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Valoraciones;
use App\Models\Comentarios;
use App\Models\ContenidoListas;
use App\Models\Recomendaciones;
use App\Models\Seguimiento;



class PeliculasSeries extends Model
{
    /** @use HasFactory<\Database\Factories\PeliculasSeriesFactory> */
    use HasFactory;

    protected $table = 'peliculas_series';

    protected $fillable = [
        'titulo',
        'tipo',
        'sinopsis',
        'elenco',
        'año_estreno',
        'duracion',
        'api_id',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function valoraciones()
    {
        return $this->hasMany(Valoraciones::class, 'id_pelicula');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'id_pelicula');
    }

    public function contenidosListas()
    {
        return $this->hasMany(ContenidoListas::class, 'id_pelicula');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendaciones::class, 'id_pelicula');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'id_pelicula');
    }
}
