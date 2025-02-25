<?php

namespace App\Http\Controllers;

use App\Models\LikesComentarios;
use App\Http\Requests\StoreLikesComentariosRequest;
use App\Http\Requests\UpdateLikesComentariosRequest;
use Illuminate\Http\Request;

class LikesComentariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $likes = LikesComentarios::all();
        return response()->json($likes);
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
            'user_id' => 'required|exists:usuarios,id',
            'id_comentario' => 'required|exists:comentarios,id',
            'tipo' => 'required|in:like,dislike',
        ]);

        $likeComentario = LikesComentarios::create([
            'user_id' => $request->user_id,
            'id_comentario' => $request->id_comentario,
            'tipo' => $request->tipo,
        ]);

        return response()->json($likeComentario, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $likeComentario = LikesComentarios::findOrFail($id);
        return response()->json($likeComentario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LikesComentarios $likesComentarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $likeComentario = LikesComentarios::findOrFail($id);

        $request->validate([
            'tipo' => ['required', 'in:like,dislike'],
        ]);

        $likeComentario->update($request->all());

        return response()->json($likeComentario);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $likeComentario = LikesComentarios::findOrFail($id);
        $likeComentario->delete();

        return response()->json(['message' => 'Like/Dislike eliminado']);
    }
}
