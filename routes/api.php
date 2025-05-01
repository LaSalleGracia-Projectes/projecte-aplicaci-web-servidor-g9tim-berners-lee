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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('usuarios', UsuariosController::class);
Route::apiResource('peliculas_series', PeliculasSeriesController::class);
Route::apiResource('valoraciones', ValoracionesController::class);
Route::apiResource('likes_comentarios', LikesComentariosController::class);
Route::apiResource('listas', ListasController::class);
Route::apiResource('comentarios', ComentariosController::class);
Route::apiResource('contenido_listas', ContenidoListasController::class);
Route::apiResource('notificaciones', NotificacionesController::class);
Route::apiResource('recomendaciones', RecomendacionesController::class);
Route::apiResource('seguimientos', SeguimientoController::class);
Route::apiResource('administradores', AdministradoresController::class);

// Rutas adicionales para listas
Route::get('listas/user/{userId}', [ListasController::class, 'getListasByUsuario']);
Route::get('contenido_listas/lista/{id_lista}', [ContenidoListasController::class, 'getContenidoByListaId']);

// Rutas para comentarios
Route::get('comentarios/tmdb/{tmdbId}/{tipo}', [ComentariosController::class, 'getComentariosByTmdbId']);

// Rutas para likes/dislikes de comentarios
Route::get('likes_comentarios/status/{comentarioId}/{userId}', [LikesComentariosController::class, 'getLikeStatus']);
Route::get('likes_comentarios/count/{comentarioId}', [LikesComentariosController::class, 'getLikesCount']);

// Rutas para notificaciones
Route::get('notificaciones/user/{userId}', [NotificacionesController::class, 'getUserNotificaciones']);
Route::put('notificaciones/read/{id}', [NotificacionesController::class, 'markAsRead']);
Route::put('notificaciones/read_all/{userId}', [NotificacionesController::class, 'markAllAsRead']);

// Rutas para valoraciones (favoritos)
Route::get('valoraciones/usuario/{userId}', [ValoracionesController::class, 'getUserFavorites']);
Route::get('valoraciones/check/{userId}/{peliculaId}', [ValoracionesController::class, 'checkFavoriteStatus']);