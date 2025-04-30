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
        return view('listas.index', compact('listas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('listas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nombre_lista' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $lista = Listas::create([
            'user_id' => $request->user_id,
            'nombre_lista' => $request->nombre_lista,
            'descripcion' => $request->descripcion,
        ]);

        if ($request->expectsJson()) {
            return response()->json($lista, 201);
        }

        return redirect()->route('listas.show', $lista->id)
            ->with('success', 'Lista creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lista = Listas::with('contenidosListas')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($lista);
        }

        return view('listas.show', compact('lista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listas $lista)
    {
        return view('listas.edit', compact('lista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lista = Listas::findOrFail($id);
        $lista->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($lista);
        }

        return redirect()->route('listas.show', $lista->id)
            ->with('success', 'Lista actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lista = Listas::findOrFail($id);
        $userId = $lista->user_id;
        $lista->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Lista eliminada']);
        }

        return redirect()->route('profile.show', $userId)
            ->with('success', 'Lista eliminada correctamente');
    }

    /**
     * Obtener las listas de un usuario específico
     */
    public function getListasByUsuario($userId)
    {
        $listas = Listas::where('user_id', $userId)->get();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Listas del usuario obtenidas correctamente',
                'data' => $listas
            ], 200);
        }

        return view('listas.user-lists', compact('listas'));
    }

    /**
     * Redirige al perfil del usuario después de eliminar una lista.
     */
    public function redirectAfterDestroy($userId)
    {
        return redirect()->route('profile.show', $userId)->with('success', 'Lista eliminada correctamente');
    }

    /**
     * Redirige a la vista de la lista después de crearla.
     */
    public function redirectAfterStore($listaId)
    {
        $lista = Listas::findOrFail($listaId);
        return redirect()->route('profile.show', $lista->user_id)->with('success', 'Lista creada correctamente');
    }

    /**
     * Redirige a la vista de la lista después de actualizarla.
     */
    public function redirectAfterUpdate($listaId)
    {
        $lista = Listas::findOrFail($listaId);
        return redirect()->route('profile.show', $lista->user_id)->with('success', 'Lista actualizada correctamente');
    }
}
