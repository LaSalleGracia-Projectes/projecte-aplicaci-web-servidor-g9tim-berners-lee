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
            'id_pelicula' => 'required|exists:peliculas_series,id',
        ]);

        $contenidoLista = ContenidoListas::create([
            'id_lista' => $request->id_lista,
            'id_pelicula' => $request->id_pelicula,
        ]);

        return response()->json($contenidoLista, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar el contenido en la lista con el ID especificado
        $contenidoLista = ContenidoListas::findOrFail($id);
    
        // Devolver los detalles del contenido de la lista
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
}
