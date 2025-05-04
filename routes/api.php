<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PeliculasSeriesController;
use App\Http\Controllers\ValoracionesController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\LikesComentariosController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\ContenidoListasController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\RecomendacionesController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('contenido_listas', ContenidoListasController::class);
Route::apiResource('usuarios', UsuariosController::class);
Route::apiResource('peliculas_series', PeliculasSeriesController::class);
Route::apiResource('valoraciones', ValoracionesController::class);
Route::apiResource('comentarios', ComentariosController::class);
Route::apiResource('likes_comentarios', LikesComentariosController::class);
Route::apiResource('listas', ListasController::class);
Route::apiResource('notificaciones', NotificacionesController::class);
Route::apiResource('recomendaciones', RecomendacionesController::class);
Route::apiResource('seguimientos', SeguimientoController::class);
Route::apiResource('administradores', AdministradoresController::class);

// Rutas para TMDB
Route::get('/tmdb/search', [PeliculasSeriesController::class, 'searchTMDB']);
Route::get('/tmdb/movie/{id}', [PeliculasSeriesController::class, 'getMovieFromTMDB']);

// Ruta para obtener trailer de una serie
Route::get('/series/{id}/trailer', [\App\Http\Controllers\SeriesController::class, 'getTrailer']);

// Ruta para gestionar favoritos
Route::post('/favorites', function (Request $request) {
    try {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $userId = Auth::id();
        $movieId = $request->input('movie_id');
        $action = $request->input('action', 'add'); // Por defecto añadir

        if ($action === 'add') {
            // Verificar si ya existe el favorito para evitar duplicados
            $exists = DB::table('favoritos')
                ->where('usuario_id', $userId)
                ->where('pelicula_id', $movieId)
                ->exists();

            if (!$exists) {
                // Insertar nuevo favorito
                DB::table('favoritos')->insert([
                    'usuario_id' => $userId,
                    'pelicula_id' => $movieId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Añadido a favoritos']);
        } else {
            // Eliminar de favoritos
            DB::table('favoritos')
                ->where('usuario_id', $userId)
                ->where('pelicula_id', $movieId)
                ->delete();

            return response()->json(['status' => 'success', 'message' => 'Eliminado de favoritos']);
        }
    } catch (\Exception $e) {
        \Log::error('Error al procesar favorito: ' . $e->getMessage());
        return response()->json(['error' => 'Error al procesar la solicitud'], 500);
    }
})->middleware('web');

// Ruta para obtener favoritos del usuario
Route::get('/user/favorites', function () {
    try {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $userId = Auth::id();

        // Obtener favoritos del usuario
        $favorites = DB::table('favoritos')
            ->where('usuario_id', $userId)
            ->get();

        return response()->json(['status' => 'success', 'favorites' => $favorites]);
    } catch (\Exception $e) {
        \Log::error('Error al obtener favoritos: ' . $e->getMessage());
        return response()->json(['error' => 'Error al procesar la solicitud'], 500);
    }
})->middleware('web');

// Rutas para comentarios
Route::prefix('api')->group(function () {
    Route::get('/comentarios/pelicula/{id}', [ComentariosController::class, 'getByPelicula']);
    Route::get('/comentarios/serie/{id}', [ComentariosController::class, 'getBySerie']);
    Route::post('/comentarios', [ComentariosController::class, 'store']);
    Route::delete('/comentarios/{id}', [ComentariosController::class, 'destroy']);
});
