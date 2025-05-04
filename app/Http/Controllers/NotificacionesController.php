<?php

namespace App\Http\Controllers;

use App\Models\Notificaciones;
use App\Models\User;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificaciones = Notificaciones::with('usuario')->orderBy('created_at', 'desc')->get();
        return response()->json($notificaciones);
    }

    /**
     * Get notifications for a specific user
     */
    public function getUserNotificaciones($userId)
    {
        $notificaciones = Notificaciones::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notificacion) {
                $notificacion->leido = (bool)$notificacion->leido;
                return $notificacion;
            });

        return response()->json($notificaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'mensaje' => 'required|string',
            'tipo' => 'required|in:nuevo_like,nuevo_comentario',
        ]);

        $notificacion = Notificaciones::create([
            'user_id' => $request->user_id,
            'mensaje' => $request->mensaje,
            'tipo' => $request->tipo,
            'leido' => false,
            'created_at' => now(),
            'updated_at' => now()
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
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notificacion = Notificaciones::findOrFail($id);

        $notificacion->leido = true;
        $notificacion->updated_at = now();
        $notificacion->save();

        return response()->json($notificacion);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        Notificaciones::where('user_id', $userId)
            ->where('leido', false)
            ->update([
                'leido' => true,
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
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
