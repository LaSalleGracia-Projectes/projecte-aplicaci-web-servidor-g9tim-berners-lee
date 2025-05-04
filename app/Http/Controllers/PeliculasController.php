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
        try {
            // Validar que $id sea un número válido
            if (!is_numeric($id)) {
                abort(404, 'El ID de la película no es válido');
            }

            $apiKey = env('TMDB_API_KEY');

            if (!$apiKey) {
                abort(500, 'Error: No se ha configurado la API key de TMDB');
            }

            // Usar el ID recibido en el parámetro para la consulta a la API
            $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&language=es-ES&append_to_response=credits,videos,watch/providers");

            if ($response->failed()) {
                abort(404, 'La película no se encontró en TMDB');
            }

            $pelicula = $response->json();

            // Obtener elenco y director usando el mismo ID
            $casting = $this->getActorsList($id, $apiKey);
            $elenco = $casting['elenco'];
            $director = $casting['director'];

            // Obtener proveedores de streaming
            $watchProviders = $pelicula['watch/providers']['results']['ES'] ?? [];

            // Obtener películas similares usando el mismo ID
            $similarMoviesResponse = Http::get("https://api.themoviedb.org/3/movie/{$id}/similar?api_key={$apiKey}&language=es-ES&page=1");
            $peliculasSimilares = [];

            if ($similarMoviesResponse->successful()) {
                $similarData = $similarMoviesResponse->json();
                // Limitamos a 6 películas similares máximo
                $peliculasSimilares = array_slice($similarData['results'] ?? [], 0, 6);
            }

            // Responder según el tipo de solicitud
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Película obtenida correctamente',
                    'data' => $pelicula
                ]);
            }

            return view('infoPelicula', compact('pelicula', 'elenco', 'director', 'watchProviders', 'peliculasSimilares'));
        } catch (\Exception $e) {
            \Log::error('Error al obtener información de película: ' . $e->getMessage());
            abort(404, 'Error al obtener la información de la película');
        }
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