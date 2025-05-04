<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContenidoListasRequest;
use App\Http\Requests\UpdateContenidoListasRequest;
use App\Models\ContenidoListas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            $validated = $request->validate([
                'id_lista' => 'required|exists:listas,id',
                'tmdb_id' => 'required|integer',
                'tipo' => 'required|string|in:movie,tv,serie,pelicula',
            ]);

            $contenido = ContenidoListas::create([
                'id_lista' => $validated['id_lista'],
                'tmdb_id' => $validated['tmdb_id'],
                'tipo' => $validated['tipo']
            ]);

            return response()->json([
                'message' => 'Película añadida correctamente',
                'data' => $contenido
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al añadir la película',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContenidoListas $contenido)
    {
        return response()->json($contenido);
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
    public function update(UpdateContenidoListasRequest $request, ContenidoListas $contenido)
    {
        $validated = $request->validated();
        $contenido->update($validated);
        return response()->json($contenido);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contenidoLista = ContenidoListas::findOrFail($id);
            $contenidoLista->delete();
            return response()->json([
                'message' => 'Contenido eliminado de la lista'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error en ContenidoListasController@destroy: " . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
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
