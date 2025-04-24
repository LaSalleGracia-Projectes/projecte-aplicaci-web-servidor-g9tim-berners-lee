<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SerieController extends Controller
{
    public function index()
    {
        $apiKey = env('TMDB_API_KEY');
        $response = Http::get("https://api.themoviedb.org/3/tv/popular?api_key={$apiKey}&language=es-ES&page=1");

        if ($response->successful()) {
            $series = $response->json()['results'];
            return view('series.index', compact('series'));
        }

        return view('series.index', ['series' => []]);
    }

    public function show($id)
    {
        $apiKey = env('TMDB_API_KEY');
        $response = Http::get("https://api.themoviedb.org/3/tv/{$id}?api_key={$apiKey}&language=es-ES");

        if ($response->successful()) {
            $serie = $response->json();
            return view('series.show', compact('serie'));
        }

        return redirect()->route('series.index')->with('error', 'Serie no encontrada');
    }
}
