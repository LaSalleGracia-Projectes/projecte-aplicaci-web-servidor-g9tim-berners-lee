<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuarios;
use App\Models\PeliculasSeries;

class Recomendaciones extends Model
{
    /** @use HasFactory<\Database\Factories\RecomendacionesFactory> */
    use HasFactory;

    protected $table = 'Recomendaciones';

    protected $fillable = [
        'id_usuario',
        'id_pelicula',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario');
    }

    public function peliculaSerie()
    {
        return $this->belongsTo(PeliculasSeries::class, 'id_pelicula');
    }
}
