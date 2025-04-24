<?php

namespace App\Http\Controllers;

use App\Models\Listas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreListasRequest;
use App\Http\Requests\UpdateListasRequest;
use Illuminate\Support\Facades\Log;

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
    public function create(Request $request)
    {
        $user_id = $request->query('user_id');

        if (!$user_id) {
            return redirect()->route('home')->with('error', 'Usuario no especificado');
        }

        return view('listas.create', compact('user_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nombre_lista' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $lista = Listas::create([
            'user_id' => $request->user_id,
            'nombre_lista' => $request->nombre_lista,
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('profile.show', ['id' => $request->user_id])
                        ->with('success', 'Lista creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lista = Listas::with('contenidosListas')->findOrFail($id);

        // Si es una solicitud de API
        if (request()->expectsJson()) {
            return response()->json($lista);
        }

        // Si es una solicitud web
        return view('listas.show', compact('lista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lista = Listas::findOrFail($id);
        return view('listas.edit', compact('lista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lista = Listas::findOrFail($id);

        $request->validate([
            'nombre_lista' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $lista->update([
            'nombre_lista' => $request->nombre_lista,
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('listas.show', $lista->id)
                        ->with('success', 'Lista actualizada exitosamente');
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
}
