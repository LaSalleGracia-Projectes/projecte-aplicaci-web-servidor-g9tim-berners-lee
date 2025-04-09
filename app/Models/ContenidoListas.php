<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listas;
use Illuminate\Support\Facades\Http;

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

    public function getPeliculaAttribute()
    {
        $apiKey = env('TMDB_API_KEY');
        $response = Http::get("https://api.themoviedb.org/3/movie/{$this->id_pelicula}?api_key={$apiKey}&language=es-ES");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
