<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PeliculasController extends Controller
{
    /**
     * Muestra el listado de películas obtenidas desde TMDB.
     * Se filtran las películas famosas (rating ≥ 7.0) para seleccionarlas de forma aleatoria.
     */
    public function index()
    {
        // Obtener la lista de películas populares (página 1)
        $response = Http::get(config('tmdb.base_url') . '/movie/popular', [
            'api_key'   => config('tmdb.api_key'),
            'language'  => 'es-ES',
            'page'      => 1,
        ]);

        $moviesData = $response->successful() ? $response->json()['results'] : [];

        // Mapear los datos para la vista
        $movies = collect($moviesData)->map(function($movie) {
            return (object)[
                'id'           => $movie['id'],
                'title'        => $movie['title'],
                'poster_url'   => config('tmdb.img_url') . $movie['poster_path'],
                'tmdb_rating'  => $movie['vote_average'],
                'release_date' => $movie['release_date'],
                // En este ejemplo no asignamos géneros; para un mapeo completo se puede hacer otro llamado a la API
                'genres'       => [],
            ];
        })->toArray();

        // Filtrar las películas famosas (rating ≥ 7.0)
        $famousMovies = array_filter($movies, function($movie) {
            return $movie->tmdb_rating >= 7.0;
        });

        // Seleccionar de forma aleatoria hasta 3 películas famosas
        $famousCount = count($famousMovies);
        if ($famousCount > 0) {
            $randomMovies = collect($famousMovies)->random(min(3, $famousCount))->all();
        } else {
            $randomMovies = [];
        }

        // Obtener la lista de géneros (para el filtrador)
        $genreResponse = Http::get(config('tmdb.base_url') . '/genre/movie/list', [
            'api_key'  => config('tmdb.api_key'),
            'language' => 'es-ES',
        ]);
        $genresList = $genreResponse->successful() ? $genreResponse->json()['genres'] : [];

        return view('peliculas', compact('movies', 'randomMovies', 'genresList'));
    }

    /**
     * Muestra el detalle de una película consultando la API de TMDB.
     */
    public function show($id)
    {
        $response = Http::get(config('tmdb.base_url') . '/movie/' . $id, [
            'api_key'  => config('tmdb.api_key'),
            'language' => 'es-ES',
        ]);

        if ($response->failed()) {
            abort(404);
        }

        $movieData = $response->json();

        $movie = (object)[
            'id'           => $movieData['id'],
            'title'        => $movieData['title'],
            'poster_url'   => config('tmdb.img_url') . $movieData['poster_path'],
            'banner_url'   => config('tmdb.img_url') . $movieData['backdrop_path'],
            'tmdb_rating'  => $movieData['vote_average'],
            'release_date' => $movieData['release_date'],
            'description'  => $movieData['overview'],
            'runtime'      => $movieData['runtime'],
            'genres'       => array_map(function($g) {
                return $g['name'];
            }, $movieData['genres']),
        ];

        return view('peliculas-detail', compact('movie'));
    }
}
