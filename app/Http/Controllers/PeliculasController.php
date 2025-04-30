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
        $pelicula = PeliculasSeries::with(['valoraciones', 'comentarios'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Película obtenida correctamente',
                'data' => $pelicula
            ]);
        }

        return view('peliculas.show', compact('pelicula'));
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
