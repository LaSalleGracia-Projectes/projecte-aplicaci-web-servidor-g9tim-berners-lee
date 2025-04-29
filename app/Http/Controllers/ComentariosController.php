<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Http\Requests\StoreComentariosRequest;
use App\Http\Requests\UpdateComentariosRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'id_pelicula' => 'required|exists:peliculas_series,id',
            'comentario' => 'required|string|min:10|max:1000',
            'es_spoiler' => 'boolean',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $comentario = Comentarios::create([
                'id_pelicula' => $request->id_pelicula,
                'comentario' => $request->comentario,
                'es_spoiler' => $request->es_spoiler ?? false,
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => true,
                'comentario' => $comentario->load('usuario')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Error al guardar el comentario: ' . $e->getMessage()]
            ], 500);
        }
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
    public function destroy(Request $request, $id)
    {
        $comentario = Comentarios::findOrFail($id);

        if ($comentario->user_id != $request->user_id && $request->user_rol !== 'admin') {
            return response()->json(['error' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado correctamente']);
    }

    public function getByPelicula($id)
    {
        $comentarios = Comentarios::with('usuario')
            ->where('id_pelicula', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comentarios);
    }
}
