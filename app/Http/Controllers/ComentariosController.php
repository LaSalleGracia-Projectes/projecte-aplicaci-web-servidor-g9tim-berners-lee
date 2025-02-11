<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Http\Requests\StoreComentariosRequest;
use App\Http\Requests\UpdateComentariosRequest;
use Illuminate\Http\Request;

class ComentariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comentarios = Comentarios::all();
        return response()->json($comentarios);
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
            'id_usuario' => 'required|exists:usuarios,id',
            'id_pelicula' => 'required|exists:peliculas_series,id',
            'comentario' => 'required|string',
        ]);

        $comentario = Comentarios::create([
            'id_usuario' => $request->id_usuario,
            'id_pelicula' => $request->id_pelicula,
            'comentario' => $request->comentario,
            'es_spoiler' => $request->es_spoiler ?? false,
            'destacado' => $request->destacado ?? false,
        ]);

        return response()->json($comentario, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comentario = Comentarios::findOrFail($id);
        return response()->json($comentario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comentarios $comentarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comentario = Comentarios::findOrFail($id);

        $comentario->update($request->all());

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
}
