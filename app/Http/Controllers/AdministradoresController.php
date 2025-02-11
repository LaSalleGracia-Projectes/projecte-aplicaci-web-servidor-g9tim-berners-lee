<?php

namespace App\Http\Controllers;

use App\Models\Administradores;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdministradoresRequest;
use App\Http\Requests\UpdateAdministradoresRequest;

class AdministradoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $administradores = Administradores::all();
        return response()->json($administradores);
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
            'nombre_admin' => 'required|string|max:50',
            'correo' => 'required|email|unique:administradores',
            'contrasena' => 'required|string|min:6',
        ]);

        $administrador = Administradores::create([
            'nombre_admin' => $request->nombre_admin,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'rol' => $request->rol ?? 'moderador',
        ]);

        return response()->json($administrador, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $administrador = Administradores::findOrFail($id);
        return response()->json($administrador);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Administradores $administradores)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $administrador = Administradores::findOrFail($id);
    
        if ($request->has('contrasena') && !empty($request->contrasena)) {
            $request->merge([
                'contrasena' => bcrypt($request->contrasena)
            ]);
        }
    
        $administrador->update($request->all());
    
        return response()->json($administrador);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $administrador = Administradores::findOrFail($id);
        $administrador->delete();

        return response()->json(['message' => 'Administrador eliminado']);
    }
}
