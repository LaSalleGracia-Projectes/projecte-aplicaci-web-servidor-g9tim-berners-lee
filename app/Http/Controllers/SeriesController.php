<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use Illuminate\Support\Facades\Http;

class SeriesController extends Controller
{
    public function index()
    {
        // Obtener todas las series
        $series = PeliculasSeries::where('tipo', 'serie')->get();

        // Obtener series aleatorias para la sección destacada
        $randomSeries = PeliculasSeries::where('tipo', 'serie')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        // Géneros predefinidos para series
        $genresList = [
            ['name' => 'Drama'],
            ['name' => 'Comedia'],
            ['name' => 'Acción'],
            ['name' => 'Aventura'],
            ['name' => 'Ciencia Ficción'],
            ['name' => 'Terror'],
            ['name' => 'Romance'],
            ['name' => 'Documental']
        ];

        return view('series', compact('series', 'randomSeries', 'genresList'));
    }

    public function show($id)
    {
        $serie = PeliculasSeries::where('id', $id)
            ->where('tipo', 'serie')
            ->first();

        if (!$serie) {
            $apiKey = env('TMDB_API_KEY');
            $response = Http::get("https://api.themoviedb.org/3/tv/{$id}?api_key={$apiKey}&language=es-ES");

            if ($response->failed()) {
                abort(404, 'La serie no se encontró');
            }

            $serieData = $response->json();

            $serie = new PeliculasSeries();
            $serie->id = $serieData['id'];
            $serie->titulo = $serieData['name'];
            $serie->sinopsis = $serieData['overview'] ?? '';
            $serie->año_estreno = substr($serieData['first_air_date'] ?? date('Y-m-d'), 0, 4);
            $serie->duracion = $serieData['episode_run_time'][0] ?? null;
            $serie->api_id = $serieData['id'];
            $serie->tipo = 'serie';
            $serie->save();
        }

        // Obtener información adicional desde la API de TMDB
        $apiKey = env('TMDB_API_KEY');
        $tmdbId = $serie->api_id ?? $serie->id;

        $response = Http::get("https://api.themoviedb.org/3/tv/{$tmdbId}?api_key={$apiKey}&language=es-ES");
        if (!$response->failed()) {
            $serieData = $response->json();
            $serie->poster_url = 'https://image.tmdb.org/t/p/w500' . ($serieData['poster_path'] ?? '');
            $serie->tmdb_rating = $serieData['vote_average'] ?? 0;
            $serie->backdrop_url = isset($serieData['backdrop_path']) ? 'https://image.tmdb.org/t/p/original' . $serieData['backdrop_path'] : null;
        } else {
            $serie->poster_url = asset('images/no-poster.jpg');
            $serie->tmdb_rating = 0;
            $serie->backdrop_url = null;
        }

        // Obtener elenco y creador
        $casting = $this->getActorsList($tmdbId, $apiKey);
        $elenco = $casting['elenco'];
        $creador = $casting['creador'];

        // Obtener proveedores de streaming
        $watchProvidersResponse = Http::get("https://api.themoviedb.org/3/tv/{$tmdbId}/watch/providers?api_key={$apiKey}");

        $watchProviders = [];
        if (!$watchProvidersResponse->failed()) {
            $watchProvidersData = $watchProvidersResponse->json();
            $watchProviders = $watchProvidersData['results']['ES'] ?? [];
        }

        return view('infoSerie', compact('serie', 'elenco', 'creador', 'watchProviders'));
    }

    /**
     * Obtener lista de actores desde TMDB
     */
    private function getActorsList($serieId, $apiKey)
    {
        $response = Http::get("https://api.themoviedb.org/3/tv/{$serieId}/credits?api_key={$apiKey}&language=es-ES");

        if ($response->failed()) {
            return [];
        }

        $credits = $response->json();

        $elenco = $credits['cast'] ?? [];
        $creador = collect($credits['crew'])->firstWhere('job', 'Creator');

        return compact('elenco', 'creador');
    }
}

