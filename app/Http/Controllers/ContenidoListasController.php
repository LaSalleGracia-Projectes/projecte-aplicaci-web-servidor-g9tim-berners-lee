<?php

namespace App\Http\Controllers;

use App\Models\ContenidoListas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContenidoListasController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lista' => 'required|exists:listas,id',
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'release_date' => 'nullable|date',
            'vote_average' => 'nullable|numeric',
        ]);

        // Verificar si la película ya está en la lista
        $existente = ContenidoListas::where('id_lista', $request->id_lista)
            ->where('tmdb_id', $request->tmdb_id)
            ->exists();

        if ($existente) {
            return response()->json(['message' => 'Esta película ya está en la lista'], 400);
        }

        try {
            // Crear el nuevo contenido
            $contenido = ContenidoListas::create([
                'id_lista' => $request->id_lista,
                'tmdb_id' => $request->tmdb_id,
                'tipo' => 'pelicula',
                'fecha_agregado' => now()
            ]);

            return response()->json([
                'message' => 'Película añadida a la lista',
                'data' => $contenido
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear contenido de lista: ' . $e->getMessage());
            return response()->json(['message' => 'Error al añadir la película a la lista'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contenido = ContenidoListas::findOrFail($id);
            $contenido->delete();

            return response()->json(['message' => 'Película eliminada de la lista']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar contenido de lista: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar la película de la lista'], 500);
        }
    }
}
