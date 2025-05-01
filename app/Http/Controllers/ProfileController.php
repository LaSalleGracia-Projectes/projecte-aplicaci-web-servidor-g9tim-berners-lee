<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show($id)
    {
        try {
            $user = User::with(['listas.contenidosListas'])->findOrFail($id);

            foreach ($user->listas as $lista) {
                foreach ($lista->contenidosListas as $contenido) {
                    try {
                        $apiKey = config('services.tmdb.api_key');
                        if (!$apiKey) {
                            Log::error('API key de TMDB no configurada');
                            continue;
                        }

                        $response = Http::get("https://api.themoviedb.org/3/movie/{$contenido->tmdb_id}", [
                            'api_key' => $apiKey,
                            'language' => 'es-ES'
                        ]);

                        if ($response->successful()) {
                            $contenido->pelicula = $response->json();
                            Log::info("Datos de TMDB obtenidos para película {$contenido->tmdb_id}: " . json_encode($contenido->pelicula));
                        } else {
                            Log::warning("Error al obtener detalles de TMDB para película {$contenido->tmdb_id}: " . $response->status());
                            $contenido->pelicula = null;
                        }
                    } catch (\Exception $e) {
                        Log::error("Excepción al obtener detalles de TMDB: " . $e->getMessage());
                        $contenido->pelicula = null;
                        continue;
                    }
                }
            }

            if (request()->expectsJson()) {
                return response()->json($user);
            }

            return view('Profile.show', compact('user'));
        } catch (\Exception $e) {
            Log::error("Error en ProfileController@show: " . $e->getMessage());
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor'], 500);
            }
            abort(500, 'Error interno del servidor');
        }
    }
}
