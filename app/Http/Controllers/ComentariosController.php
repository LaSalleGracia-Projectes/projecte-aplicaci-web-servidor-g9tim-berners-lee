<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\User;
use Illuminate\Http\Request;

class ComentariosController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comentarios = Comentarios::with('usuario')->get();
        return response()->json($comentarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tmdb_id' => 'required|integer',
            'tipo' => 'required|in:pelicula,serie',
            'comentario' => 'required|string',
            'es_spoiler' => 'boolean',
        ]);

        $comentario = Comentarios::create([
            'user_id' => $request->user_id,
            'tmdb_id' => $request->tmdb_id,
            'tipo' => $request->tipo,
            'comentario' => $request->comentario,
            'es_spoiler' => $request->es_spoiler ?? false,
            'destacado' => false,
        ]);

        $comentario->load('usuario');

        return response()->json($comentario, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comentario = Comentarios::with('usuario')->findOrFail($id);
        return response()->json($comentario);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comentario = Comentarios::findOrFail($id);

        $comentario->update($request->all());

        $comentario->load('usuario');

        return response()->json($comentario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comentario = Comentarios::findOrFail($id);
        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado']);
    }

    /**
     * Get comments for a specific movie or series.
     */
    public function getComentariosByTmdbId($tmdbId, $tipo)
    {
        $comentarios = Comentarios::with('usuario')
            ->where('tmdb_id', $tmdbId)
            ->where('tipo', $tipo)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comentarios);
    }
}
