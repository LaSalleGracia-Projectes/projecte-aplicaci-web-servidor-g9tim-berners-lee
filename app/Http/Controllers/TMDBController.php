<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TMDBController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
    }

    public function search(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }

        try {
            $response = Http::get("{$this->baseUrl}/search/movie", [
                'api_key' => $this->apiKey,
                'query' => $query,
                'language' => 'es-ES'
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Error al buscar películas'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al conectar con TMDB'], 500);
        }
    }

    public function getMovie($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/movie/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES'
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Error al obtener la película'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al conectar con TMDB'], 500);
        }
    }
}
