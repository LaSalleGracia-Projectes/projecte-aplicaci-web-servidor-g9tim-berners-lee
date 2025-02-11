<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PeliculasSeries;
use App\Models\Usuarios;

class Valoraciones extends Model
{
    /** @use HasFactory<\Database\Factories\ValoracionesFactory> */
    use HasFactory;

    protected $table = 'Valoraciones';

    protected $fillable = [
        'id_usuario',
        'id_pelicula',
        'valoracion',
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
