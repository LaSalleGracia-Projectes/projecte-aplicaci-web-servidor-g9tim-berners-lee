<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listas;

class ContenidoListas extends Model
{
    /** @use HasFactory<\Database\Factories\ContenidoListasFactory> */
    use HasFactory;

    protected $table = 'contenidos_listas';

    protected $fillable = [
        'id_lista',
        'tmdb_id',
        'tipo',
        'fecha_agregado',
    ];

    public $timestamps = false;

    public function lista()
    {
        return $this->belongsTo(Listas::class, 'id_lista');
    }
}