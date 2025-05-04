<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCritico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SolicitudCriticoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = SolicitudCritico::with('usuario')->get();
        return response()->json($solicitudes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'edad' => 'required|integer|min:18|max:120',
            'motivo' => 'required|string|max:500',
        ]);

        $existingSolicitud = SolicitudCritico::where('user_id', $request->user_id)
            ->where('estado', 'pendiente')
            ->first();
            
        if ($existingSolicitud) {
            return response()->json(['message' => 'Ya tienes una solicitud pendiente'], 409);
        }

        $solicitud = SolicitudCritico::create([
            'user_id' => $request->user_id,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'edad' => $request->edad,
            'motivo' => $request->motivo,
            'estado' => 'pendiente'
        ]);

        return response()->json($solicitud, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $solicitud = SolicitudCritico::with('usuario')->findOrFail($id);
        
        return response()->json($solicitud);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $solicitud = SolicitudCritico::findOrFail($id);
        
        $request->validate([
            'estado' => 'sometimes|in:pendiente,aprobada,rechazada',
        ]);
            
        $solicitud->update($request->only(['estado']));
            
        if ($request->estado === 'aprobada') {
            $usuario = User::find($solicitud->user_id);
            if ($usuario) {
                $usuario->rol = 'critico';
                $usuario->save();
            }
        }
            
        return response()->json($solicitud);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $solicitud = SolicitudCritico::findOrFail($id);
        $solicitud->delete();
        return response()->json(['message' => 'Solicitud eliminada']);
    }
    
    /**
     * Get solicitudes by user ID.
     */
    public function getSolicitudesByUser($userId)
    {
        $solicitudes = SolicitudCritico::where('user_id', $userId)->get();
        return response()->json($solicitudes);
    }
}