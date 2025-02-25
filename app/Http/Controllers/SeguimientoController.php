<?php

namespace App\Http\Controllers;

use App\Models\Seguimiento;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSeguimientoRequest;
use App\Http\Requests\UpdateSeguimientoRequest;

class SeguimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seguimientos = Seguimiento::all();
        return response()->json($seguimientos);
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

        $seguimiento = Seguimiento::create([
            'user_id' => $request->user_id,
            'id_pelicula' => $request->id_pelicula,
        ]);

        return response()->json($seguimiento, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $seguimiento = Seguimiento::findOrFail($id);

        return response()->json($seguimiento);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seguimiento $seguimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeguimientoRequest $request, Seguimiento $seguimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $seguimiento = Seguimiento::findOrFail($id);
        $seguimiento->delete();

        return response()->json(['message' => 'Seguimiento eliminado']);
    }
}
