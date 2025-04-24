<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listas;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContenidoListas extends Model
{
    /** @use HasFactory<\Database\Factories\ContenidoListasFactory> */
    use HasFactory;

    protected $table = 'contenidos_listas';

    protected $fillable = [
        'id_lista',
        'tmdb_id',
        'tipo',
        'fecha_agregado'
    ];

    public $timestamps = false;

    public function lista()
    {
        return $this->belongsTo(Listas::class, 'id_lista');
    }

    public function getPeliculaAttribute()
    {
        try {
            $apiKey = env('TMDB_API_KEY');
            $response = Http::get("https://api.themoviedb.org/3/movie/{$this->tmdb_id}?api_key={$apiKey}&language=es-ES");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching movie data: ' . $e->getMessage());
        }

        // Devolver un array con valores por defecto si algo falla
        return [
            'title' => 'Película no encontrada',
            'poster_path' => null,
            'release_date' => '',
            'vote_average' => 0
        ];
    }
}
