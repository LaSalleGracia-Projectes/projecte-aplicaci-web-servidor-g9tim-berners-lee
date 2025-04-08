<?php

namespace App\Http\Controllers;

use App\Models\ContenidoListas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContenidoListasController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lista' => 'required|exists:listas,id',
            'id_pelicula' => 'required|exists:peliculas,id',
        ]);

        // Verificar que la lista pertenezca al usuario autenticado
        $lista = \App\Models\Listas::findOrFail($request->id_lista);

        if ($lista->user_id != Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para modificar esta lista'], 403);
        }

        // Verificar si la película ya está en la lista
        $existente = ContenidoListas::where('id_lista', $request->id_lista)
            ->where('id_pelicula', $request->id_pelicula)
            ->exists();

        if ($existente) {
            return response()->json(['message' => 'Esta película ya está en la lista'], 400);
        }

        // Crear el nuevo contenido
        $contenido = ContenidoListas::create([
            'id_lista' => $request->id_lista,
            'id_pelicula' => $request->id_pelicula
        ]);

        return response()->json([
            'message' => 'Película añadida a la lista',
            'data' => $contenido
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contenido = ContenidoListas::findOrFail($id);

        // Verificar que la lista pertenezca al usuario autenticado
        $lista = \App\Models\Listas::findOrFail($contenido->id_lista);

        if ($lista->user_id != Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para modificar esta lista'], 403);
        }

        $contenido->delete();

        return response()->json(['message' => 'Película eliminada de la lista']);
    }
}
