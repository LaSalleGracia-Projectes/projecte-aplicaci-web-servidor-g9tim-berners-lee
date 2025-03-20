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
        // Intentar obtener la película de la base de datos primero
        $pelicula = PeliculasSeries::where('id', $id)
                  ->where('tipo', 'pelicula')
                  ->first();

        // Si no existe en la base de datos, buscar en TMDB
        if (!$pelicula) {
            $apiKey = env('TMDB_API_KEY');
            $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&language=es-ES");

            if ($response->failed()) {
                abort(404, 'La película no se encontró');
            }

            $peliculaData = $response->json();

            // Crear una nueva entrada en la base de datos
            $pelicula = new PeliculasSeries();
            $pelicula->id = $peliculaData['id'];
            $pelicula->titulo = $peliculaData['title'];
            $pelicula->sinopsis = $peliculaData['overview'] ?? '';
            $pelicula->elenco = $this->getActorsList($peliculaData['id'], $apiKey);
            $pelicula->año_estreno = substr($peliculaData['release_date'] ?? date('Y-m-d'), 0, 4);
            $pelicula->duracion = $peliculaData['runtime'] ?? null;
            $pelicula->api_id = $peliculaData['id'];
            $pelicula->tipo = 'pelicula';

            $pelicula->save();
        }

        // Obtener la URL del póster y rating desde TMDB
        $apiKey = env('TMDB_API_KEY');
        $tmdbId = $pelicula->api_id ?? $pelicula->id;
        $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$apiKey}&language=es-ES");

        if (!$response->failed()) {
            $movieData = $response->json();
            $pelicula->poster_url = 'https://image.tmdb.org/t/p/w500' . ($movieData['poster_path'] ?? '');
            $pelicula->tmdb_rating = $movieData['vote_average'] ?? 0;
        } else {
            $pelicula->poster_url = asset('images/no-poster.jpg');
            $pelicula->tmdb_rating = 0;
        }

        return view('infoPelicula', compact('pelicula'));
    }

    /**
     * Obtener lista de actores desde TMDB
     */
    private function getActorsList($movieId, $apiKey)
    {
        $response = Http::get("https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=es-ES");

        if ($response->failed()) {
            return '';
        }

        $credits = $response->json();
        $actors = [];

        // Tomar los primeros 5 actores
        $cast = $credits['cast'] ?? [];
        for ($i = 0; $i < min(5, count($cast)); $i++) {
            if (isset($cast[$i]['name'])) {
                $actors[] = $cast[$i]['name'];
            }
        }

        return implode(', ', $actors);
    }
}
