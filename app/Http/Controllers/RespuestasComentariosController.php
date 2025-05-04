<?php

namespace App\Http\Controllers;

use App\Models\RespuestasComentarios;
use App\Models\Comentarios;
use App\Models\Notificaciones;
use App\Models\User;
use Illuminate\Http\Request;

class RespuestasComentariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $respuestas = RespuestasComentarios::with('usuario')->get();
        return response()->json($respuestas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'comentario_id' => 'required|exists:comentarios,id',
                'user_id' => 'required|exists:users,id',
                'respuesta' => 'required|string',
                'es_spoiler' => 'boolean',
            ]);

            $respuesta = RespuestasComentarios::create([
                'comentario_id' => $request->comentario_id,
                'user_id' => $request->user_id,
                'respuesta' => $request->respuesta,
                'es_spoiler' => $request->es_spoiler ?? false,
            ]);

            $comentario = Comentarios::with('usuario')->findOrFail($request->comentario_id);
            $usuario_que_responde = User::findOrFail($request->user_id);

            if ($comentario->user_id !== $request->user_id) {
                Notificaciones::create([
                    'user_id' => $comentario->user_id,
                    'mensaje' => "{$usuario_que_responde->name} ha respondido a tu comentario",
                    'tipo' => 'nueva_respuesta',
                    'leido' => false,
                ]);
            }

            $respuesta->load('usuario');

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json($respuesta, 201);
            } else {
                return redirect()->back()->with('success', 'Respuesta publicada correctamente.');
            }
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Error al guardar la respuesta', 'detalle' => $e->getMessage()], 500);
            } else {
                return redirect()->back()->with('error', 'Error al guardar la respuesta: ' . $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $respuesta = RespuestasComentarios::with('usuario')->findOrFail($id);
        return response()->json($respuesta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $respuesta = RespuestasComentarios::findOrFail($id);

        $request->validate([
            'respuesta' => 'sometimes|string',
            'es_spoiler' => 'sometimes|boolean',
        ]);

        $respuesta->update($request->all());

        $respuesta->load('usuario');

        return response()->json($respuesta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $respuesta = RespuestasComentarios::findOrFail($id);
        $respuesta->delete();

        return response()->json(['message' => 'Respuesta eliminada']);
    }

    /**
     * Get all responses for a specific comment.
     */
    public function getRespuestasByComentarioId($comentarioId)
    {
        $respuestas = RespuestasComentarios::with('usuario')
            ->where('comentario_id', $comentarioId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($respuestas);
    }
}