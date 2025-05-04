<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use Illuminate\Support\Facades\Http;

class PeliculasController extends Controller
{
    public function index()
    {
        // Obtener todas las películas
        $movies = PeliculasSeries::where('tipo', 'pelicula')->get();

        // Obtener películas aleatorias para la sección destacada
        $randomMovies = PeliculasSeries::where('tipo', 'pelicula')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        // Géneros predefinidos
        $genresList = [
            ['name' => 'Acción'],
            ['name' => 'Aventura'],
            ['name' => 'Comedia'],
            ['name' => 'Drama'],
            ['name' => 'Ciencia Ficción'],
            ['name' => 'Terror'],
            ['name' => 'Romance'],
            ['name' => 'Documental']
        ];

        return view('peliculas', compact('movies', 'randomMovies', 'genresList'));
    }

    public function show($id)
    {
        $pelicula = PeliculasSeries::where('id', $id)
            ->where('tipo', 'pelicula')
            ->first();

        if (!$pelicula) {
            $apiKey = env('TMDB_API_KEY');
            $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&language=es-ES");

            if ($response->failed()) {
                abort(404, 'La película no se encontró');
            }

            $peliculaData = $response->json();

            $pelicula = new PeliculasSeries();
            $pelicula->id = $peliculaData['id'];
            $pelicula->titulo = $peliculaData['title'];
            $pelicula->sinopsis = $peliculaData['overview'] ?? '';
            $pelicula->año_estreno = substr($peliculaData['release_date'] ?? date('Y-m-d'), 0, 4);
            $pelicula->duracion = $peliculaData['runtime'] ?? null;
            $pelicula->api_id = $peliculaData['id'];
            $pelicula->tipo = 'pelicula';
            $pelicula->save();
        }

        // Obtener información adicional desde la API de TMDB
        $apiKey = env('TMDB_API_KEY');
        $tmdbId = $pelicula->api_id ?? $pelicula->id;

        $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$apiKey}&language=es-ES");
        if (!$response->failed()) {
            $movieData = $response->json();
            $pelicula->poster_url = 'https://image.tmdb.org/t/p/w500' . ($movieData['poster_path'] ?? '');
            $pelicula->tmdb_rating = $movieData['vote_average'] ?? 0;
            $pelicula->backdrop_url = isset($movieData['backdrop_path']) ? 'https://image.tmdb.org/t/p/original' . $movieData['backdrop_path'] : null;
        } else {
            $pelicula->poster_url = asset('images/no-poster.jpg');
            $pelicula->tmdb_rating = 0;
            $pelicula->backdrop_url = null;
        }

        // Obtener elenco y director
        $casting = $this->getActorsList($tmdbId, $apiKey);
        $elenco = $casting['elenco'];
        $director = $casting['director'];

        // Obtener películas similares
        $similarMoviesResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}/similar?api_key={$apiKey}&language=es-ES&page=1");
        $peliculasSimilares = [];

        if ($similarMoviesResponse->successful()) {
            $similarData = $similarMoviesResponse->json();
            // Limitamos a 6 películas similares máximo
            $peliculasSimilares = array_slice($similarData['results'] ?? [], 0, 6);
        }

        // Obtener proveedores de streaming
        $watchProvidersResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}/watch/providers?api_key={$apiKey}");

        $watchProviders = [];
        if (!$watchProvidersResponse->failed()) {
            $watchProvidersData = $watchProvidersResponse->json();
            $watchProviders = $watchProvidersData['results']['ES'] ?? [];
        }

        return view('infoPelicula', compact('pelicula', 'elenco', 'director', 'watchProviders', 'peliculasSimilares'));
    }

    /**
     * Obtener lista de actores desde TMDB
     */
    private function getActorsList($movieId, $apiKey)
    {
        $response = Http::get("https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=es-ES");

        if ($response->failed()) {
            return [];
        }

        $credits = $response->json();

        $elenco = $credits['cast'] ?? [];
        $director = collect($credits['crew'])->firstWhere('job', 'Director');

        return compact('elenco', 'director'); // Devuelve un array con la información
    }
}
