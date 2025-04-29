<?php

namespace App\Http\Controllers;

use App\Models\Valoraciones;
use Illuminate\Http\Request;

class ValoracionesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'id_pelicula' => 'required|integer',
            'valoracion' => 'required|in:like,dislike',
        ]);

        // Verificar si ya existe la valoración
        $existingValoracion = Valoraciones::where('user_id', $request->user_id)
            ->where('id_pelicula', $request->id_pelicula)
            ->first();

        if ($existingValoracion) {
            return response()->json($existingValoracion, 200);
        }

        $valoracion = Valoraciones::create([
            'user_id' => $request->user_id,
            'id_pelicula' => $request->id_pelicula,
            'valoracion' => $request->valoracion,
        ]);

        return response()->json($valoracion, 201);
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
            ->where('id_pelicula', $peliculaId)
            ->where('valoracion', 'like')
            ->exists();

        return response()->json($favorito);
    }
}
