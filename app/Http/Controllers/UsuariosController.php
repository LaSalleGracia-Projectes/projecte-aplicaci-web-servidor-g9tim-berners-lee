<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Http\Requests\StoreUsuariosRequest;
use App\Http\Requests\UpdateUsuariosRequest;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuarios::all();
        return response()->json($usuarios);
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
        'nombre_usuario' => 'required|string|max:50',
        'correo' => 'required|email|unique:usuarios',
        'contrasena' => 'required|string|min:6',
    ]);

    $usuario = Usuarios::create([
        'nombre_usuario' => $request->nombre_usuario,
        'correo' => $request->correo,
        'contrasena' => bcrypt($request->contrasena),
        'foto_perfil' => $request->foto_perfil,
        'biografia' => $request->biografia,
        'rol' => $request->rol ?? 'usuario',
    ]);

    return response()->json($usuario, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = Usuarios::findOrFail($id);
        return response()->json($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuarios $usuarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::findOrFail($id);

        $request->validate([
            'nombre_usuario' => 'sometimes|string|max:50',
            'correo' => 'sometimes|email|unique:usuarios,correo,' . $id,
            'contrasena' => 'sometimes|string|min:6',
        ]);

        $usuario->update($request->all());

        return response()->json($usuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuarios::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
}
