<?php

namespace App\Http\Controllers;

use App\Models\Valoraciones;
use App\Http\Requests\StoreValoracionesRequest;
use App\Http\Requests\UpdateValoracionesRequest;
use Illuminate\Http\Request;

class ValoracionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $valoraciones = Valoraciones::all();
        return response()->json($valoraciones);
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
            'valoracion' => 'required|in:like,dislike',
        ]);
    
        $valoracion = Valoraciones::create([
            'id_usuario' => $request->id_usuario,
            'id_pelicula' => $request->id_pelicula,
            'valoracion' => $request->valoracion,
        ]);
    
        return response()->json($valoracion, 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $valoracion = Valoraciones::findOrFail($id);
        return response()->json($valoracion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Valoraciones $valoraciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $valoracion = Valoraciones::findOrFail($id);

        $valoracion->update($request->all());

        return response()->json($valoracion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $valoracion = Valoraciones::findOrFail($id);
        $valoracion->delete();

        return response()->json(['message' => 'Valoración eliminada']);
    }
}
