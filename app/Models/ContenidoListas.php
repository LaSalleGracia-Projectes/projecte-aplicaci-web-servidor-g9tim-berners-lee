<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listas;
use App\Models\PeliculasSeries;

class ContenidoListas extends Model
{
    /** @use HasFactory<\Database\Factories\ContenidoListasFactory> */
    use HasFactory;

    protected $table = 'contenidos_listas';

    protected $fillable = [
        'id_lista',
        'id_pelicula',
        'fecha_agregado',
    ];

    public $timestamps = false;

    public function lista()
    {
        return $this->belongsTo(Listas::class, 'id_lista');
    }

    public function peliculaSerie()
    {
        return $this->belongsTo(PeliculasSeries::class, 'id_pelicula');
    }
}
