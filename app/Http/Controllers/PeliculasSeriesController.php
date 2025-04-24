<?php

namespace App\Http\Controllers;

use App\Models\PeliculasSeries;
use Illuminate\Http\Request;
use App\Http\Requests\StorePeliculasSeriesRequest;
use App\Http\Requests\UpdatePeliculasSeriesRequest;
use Illuminate\Support\Facades\Http;

class PeliculasSeriesController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peliculas = PeliculasSeries::all();
        return response()->json($peliculas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:pelicula,serie',
            'año_estreno' => 'required|integer',
        ]);

        $pelicula = PeliculasSeries::create([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'sinopsis' => $request->sinopsis,
            'elenco' => $request->elenco,
            'año_estreno' => $request->año_estreno,
            'duracion' => $request->duracion,
            'api_id' => $request->api_id,
        ]);

        return response()->json($pelicula, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pelicula = PeliculasSeries::with(['valoraciones', 'comentarios'])->findOrFail($id);
        return response()->json($pelicula);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PeliculasSeries $peliculasSeries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pelicula = PeliculasSeries::findOrFail($id);

        $pelicula->update($request->all());

        return response()->json($pelicula);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pelicula = PeliculasSeries::findOrFail($id);
        $pelicula->delete();

        return response()->json(['message' => 'Película/Serie eliminada']);
    }

    public function searchTMDB(Request $request)
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

    public function getMovieFromTMDB($id)
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
