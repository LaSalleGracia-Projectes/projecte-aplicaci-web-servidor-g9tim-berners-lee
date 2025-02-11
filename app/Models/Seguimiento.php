<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PeliculasSeries;
use App\Models\Usuarios;

class Seguimiento extends Model
{
    /** @use HasFactory<\Database\Factories\SeguimientoFactory> */
    use HasFactory;

    protected $table = 'Seguimientos';

    protected $fillable = [
        'id_usuario',
        'id_pelicula',
        'fecha_seguimiento',
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
