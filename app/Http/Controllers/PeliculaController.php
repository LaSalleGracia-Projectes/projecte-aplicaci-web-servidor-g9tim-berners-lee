<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PeliculaController extends Controller
{
    public function index()
    {
        $apiKey = env('TMDB_API_KEY');
        $response = Http::get("https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=es-ES&page=1");

        if ($response->successful()) {
            $peliculas = $response->json()['results'];
            return view('peliculas.index', compact('peliculas'));
        }

        return view('peliculas.index', ['peliculas' => []]);
    }

    public function show($id)
    {
        $apiKey = env('TMDB_API_KEY');
        $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&language=es-ES");

        if ($response->successful()) {
            $pelicula = $response->json();
            return view('peliculas.show', compact('pelicula'));
        }

        return redirect()->route('peliculas.index')->with('error', 'Película no encontrada');
    }
}
