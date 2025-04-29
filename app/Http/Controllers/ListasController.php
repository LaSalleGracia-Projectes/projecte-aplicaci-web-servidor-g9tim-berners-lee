<?php

namespace App\Http\Controllers;

use App\Models\Listas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreListasRequest;
use App\Http\Requests\UpdateListasRequest;

class ListasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listas = Listas::with('usuario')->get();

        return response()->json([
            'message' => 'Listas obtenidas correctamente',
            'data' => $listas
        ], 200);
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
            'user_id' => 'required|exists:users,id',
            'nombre_lista' => 'required|string|max:100',
        ]);

        $lista = Listas::create([
            'user_id' => $request->user_id,
            'nombre_lista' => $request->nombre_lista,
        ]);

        return response()->json($lista, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lista = Listas::with('contenidosListas')->findOrFail($id);
        return response()->json($lista);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listas $listas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lista = Listas::findOrFail($id);

        $lista->update($request->all());

        return response()->json($lista);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lista = Listas::findOrFail($id);
        $lista->delete();

        return response()->json(['message' => 'Lista eliminada']);
    }

    public function getListasByUsuario($userId)
{
    $listas = Listas::where('user_id', $userId)->get();

    return response()->json([
        'message' => 'Listas del usuario obtenidas correctamente',
        'data' => $listas
    ], 200);
}
}
