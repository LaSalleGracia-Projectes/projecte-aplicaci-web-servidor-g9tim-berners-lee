<?php

namespace App\Http\Controllers;

use App\Models\ContenidoListas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContenidoListasRequest;
use App\Http\Requests\UpdateContenidoListasRequest;

class ContenidoListasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contenidos = ContenidoListas::all();
        return response()->json($contenidos);
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
            'id_lista' => 'required|exists:listas,id',
            'tmdb_id' => 'required|integer',
            'tipo' => 'required|in:pelicula,serie',
        ]);

        $contenidoLista = ContenidoListas::create([
            'id_lista' => $request->id_lista,
            'tmdb_id' => $request->tmdb_id,
            'tipo' => $request->tipo,
        ]);

        return response()->json($contenidoLista, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contenidoLista = ContenidoListas::findOrFail($id);

        return response()->json($contenidoLista);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContenidoListas $contenidoListas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContenidoListasRequest $request)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contenidoLista = ContenidoListas::findOrFail($id);
        $contenidoLista->delete();

        return response()->json(['message' => 'Contenido eliminado de la lista']);
    }

    /**
     * Get content for a specific list.
     */
    public function getContenidoByListaId($id_lista)
    {
        $contenidos = ContenidoListas::where('id_lista', $id_lista)->get();
        return response()->json([
            'message' => 'Contenido de la lista obtenido correctamente',
            'data' => $contenidos
        ], 200);
    }
}