<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use Illuminate\Support\Facades\Http;

class PeliculasController extends Controller
{
    public function index()
    {
        $peliculas = PeliculasSeries::where('tipo', 'pelicula')->get();
        return view('peliculas', compact('peliculas'));
    }

    public function show($id)
    {
        $apiKey = env('TMDB_API_KEY');

        $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&language=es-ES");

        if ($response->failed()) {
            abort(404);
        }

        $pelicula = $response->json();

        $watchProvidersResponse = Http::get("https://api.themoviedb.org/3/movie/{$id}/watch/providers?api_key={$apiKey}");
        $watchProviders = $watchProvidersResponse->json()['results']['ES'] ?? [];

        $creditsResponse = Http::get("https://api.themoviedb.org/3/movie/{$id}/credits?api_key={$apiKey}&language=es-ES");
        $credits = $creditsResponse->json();

        $elenco = $credits['cast'] ?? [];
        $director = collect($credits['crew'])->firstWhere('job', 'Director');

        return view('infoPelicula', compact('pelicula', 'watchProviders', 'elenco', 'director'));
    }

}
