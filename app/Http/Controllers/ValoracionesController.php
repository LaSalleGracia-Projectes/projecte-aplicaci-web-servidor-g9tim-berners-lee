<?php

namespace App\Http\Controllers;

use App\Models\Valoraciones;
use Illuminate\Http\Request;

class ValoracionesController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $valoraciones = Valoraciones::all();
        return response()->json($valoraciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tmdb_id' => 'required|integer',
            'valoracion' => 'required|in:like,dislike',
        ]);

        // Verificar si ya existe la valoración
        $existingValoracion = Valoraciones::where('user_id', $request->user_id)
            ->where('tmdb_id', $request->tmdb_id)
            ->first();

        if ($existingValoracion) {
            return response()->json($existingValoracion, 200);
        }

        $valoracion = Valoraciones::create([
            'user_id' => $request->user_id,
            'tmdb_id' => $request->tmdb_id,
            'valoracion' => $request->valoracion,
        ]);

        return response()->json($valoracion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $valoracion = Valoraciones::findOrFail($id);
        return response()->json($valoracion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'valoracion' => 'required|in:like,dislike',
        ]);

        $valoracion = Valoraciones::findOrFail($id);
        $valoracion->update([
            'valoracion' => $request->valoracion
        ]);

        return response()->json($valoracion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $valoracion = Valoraciones::findOrFail($id);
        $valoracion->delete();

        return response()->json(['message' => 'Valoración eliminada correctamente'], 200);
    }

    /**
     * Get user's favorite movies/shows.
     */
    public function getUserFavorites($userId)
    {
        $favoritos = Valoraciones::where('user_id', $userId)
            ->where('valoracion', 'like')
            ->get();

        return response()->json($favoritos);
    }

    /**
     * Check if a movie/show is marked as favorite by a user.
     */
    public function checkFavoriteStatus($userId, $peliculaId)
    {
        $favorito = Valoraciones::where('user_id', $userId)
            ->where('tmdb_id', $peliculaId)
            ->where('valoracion', 'like')
            ->exists();

        return response()->json($favorito);
    }
}
