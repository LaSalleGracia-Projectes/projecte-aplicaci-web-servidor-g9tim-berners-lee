<?php

namespace App\Http\Controllers;

use App\Models\Notificaciones;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNotificacionesRequest;
use App\Http\Requests\UpdateNotificacionesRequest;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificaciones = Notificaciones::all();
        return response()->json($notificaciones);
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
            'mensaje' => 'required|string',
            'tipo' => 'required|in:nueva_temporada,nuevo_comentario,estreno',
        ]);

        $notificacion = Notificaciones::create([
            'id_usuario' => $request->id_usuario,
            'mensaje' => $request->mensaje,
            'tipo' => $request->tipo,
        ]);

        return response()->json($notificacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $notificacion = Notificaciones::findOrFail($id);
        return response()->json($notificacion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notificaciones $notificaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $notificacion = Notificaciones::findOrFail($id);

        $notificacion->update($request->all());

        return response()->json($notificacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $notificacion = Notificaciones::findOrFail($id);
        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada']);
    }
}
