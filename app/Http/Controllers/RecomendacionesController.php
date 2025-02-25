<?php

namespace App\Http\Controllers;

use App\Models\Recomendaciones;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRecomendacionesRequest;
use App\Http\Requests\UpdateRecomendacionesRequest;

class RecomendacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recomendaciones = Recomendaciones::all();
        return response()->json($recomendaciones);
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
            'id_pelicula' => 'required|exists:peliculas_series,id',
        ]);

        $recomendacion = Recomendaciones::create([
            'user_id' => $request->user_id,
            'id_pelicula' => $request->id_pelicula,
        ]);

        return response()->json($recomendacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $recomendacion = Recomendaciones::findOrFail($id);
        return response()->json($recomendacion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recomendaciones $recomendaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecomendacionesRequest $request, Recomendaciones $recomendaciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $recomendacion = Recomendaciones::findOrFail($id);
        $recomendacion->delete();

        return response()->json(['message' => 'Recomendación eliminada']);
    }
}
