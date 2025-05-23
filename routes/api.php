<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\SolicitudCriticoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RespuestasComentariosController;

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Resources API
Route::apiResource('usuarios', UsuariosController::class);
Route::apiResource('peliculas_series', PeliculasSeriesController::class);
Route::apiResource('valoraciones', ValoracionesController::class);
Route::apiResource('comentarios', ComentariosController::class);
Route::apiResource('likes_comentarios', LikesComentariosController::class);
Route::apiResource('listas', ListasController::class);
Route::apiResource('contenido_listas', ContenidoListasController::class);
Route::apiResource('notificaciones', NotificacionesController::class);
Route::apiResource('recomendaciones', RecomendacionesController::class);
Route::apiResource('seguimientos', SeguimientoController::class);
Route::apiResource('administradores', AdministradoresController::class);
Route::apiResource('solicitudes_critico', SolicitudCriticoController::class);
Route::apiResource('respuestas_comentarios', RespuestasComentariosController::class);

// Rutas para comentarios
Route::get('comentarios/tmdb/{tmdbId}/{tipo}', [ComentariosController::class, 'getComentariosByTmdbId']);
// Ruta simplificada para películas
Route::get('/comentarios-pelicula/{tmdbId}', [ComentariosController::class, 'getComentariosPelicula'])
    ->where('tmdbId', '[0-9]+');
// Ruta simplificada para series
Route::get('/comentarios-serie/{tmdbId}', [ComentariosController::class, 'getComentariosSerie'])
    ->where('tmdbId', '[0-9]+');
// Rutas para likes/dislikes de comentarios
Route::get('likes_comentarios/status/{comentarioId}/{userId}', [LikesComentariosController::class, 'getLikeStatus']);
Route::get('likes_comentarios/count/{comentarioId}', [LikesComentariosController::class, 'getLikesCount']);

// Rutas para respuestas a comentarios
Route::get('/respuestas_comentarios/comentario/{comentarioId}', [RespuestasComentariosController::class, 'getRespuestasByComentarioId']);
Route::post('/respuestas-comentarios', [RespuestasComentariosController::class, 'store']);

// Rutas para likes/dislikes de comentarios
Route::get('/likes_comentarios/status/{comentarioId}/{userId}', [LikesComentariosController::class, 'getLikeStatus']);
Route::get('/likes_comentarios/count/{comentarioId}', [LikesComentariosController::class, 'getLikesCount']);

// Rutas adicionales para listas
Route::get('/listas/user/{userId}', [ListasController::class, 'getListasByUsuario']);
Route::get('/contenido_listas/lista/{id_lista}', [ContenidoListasController::class, 'getContenidoByListaId']);

// Rutas para notificaciones
Route::get('/notificaciones/user/{userId}', [NotificacionesController::class, 'getUserNotificaciones']);
Route::put('/notificaciones/read/{id}', [NotificacionesController::class, 'markAsRead']);
Route::put('/notificaciones/read_all/{userId}', [NotificacionesController::class, 'markAllAsRead']);

// Rutas para valoraciones (favoritos)
Route::get('/valoraciones/usuario/{userId}', [ValoracionesController::class, 'getUserFavorites']);
Route::get('/valoraciones/check/{userId}/{tmdb_id}', [ValoracionesController::class, 'checkFavoriteStatus']);

// Rutas para solicitudes de crítico
Route::get('solicitudes_critico/user/{userId}', [SolicitudCriticoController::class, 'getSolicitudesByUser']);

// Rutas para comentarios
Route::get('/comentarios/tmdb/{tmdbId}/{tipo}', [ComentariosController::class, 'getComentariosByTmdbId']);
Route::apiResource('comentarios', ComentariosController::class);

Route::apiResource('likes_comentarios', LikesComentariosController::class);
