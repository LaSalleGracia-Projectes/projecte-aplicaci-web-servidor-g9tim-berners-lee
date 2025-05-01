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
use App\Http\Controllers\CriticasController;
use App\Http\Controllers\LikesCriticasController;

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
// Route::get('/tmdb/search', [PeliculasSeriesController::class, 'searchTMDB']);
// Route::get('/tmdb/movie/{id}', [PeliculasSeriesController::class, 'getMovieDetails']);

// Rutas para críticas
Route::get('/criticas', [CriticasController::class, 'index']);
Route::post('/criticas', [CriticasController::class, 'store']);
Route::get('/criticas/{id}', [CriticasController::class, 'show']);
Route::put('/criticas/{id}', [CriticasController::class, 'update']);
Route::delete('/criticas/{id}', [CriticasController::class, 'destroy']);
Route::get('/criticas/tmdb/{tmdbId}/{tipo}', [CriticasController::class, 'getCriticasByTmdbId']);

// Rutas para likes de críticas
Route::post('/likes-criticas', [LikesCriticasController::class, 'store']);
Route::delete('/likes-criticas/{id}', [LikesCriticasController::class, 'destroy']);
Route::get('/likes-criticas/critica/{criticaId}', [LikesCriticasController::class, 'getLikesByCriticaId']);

// Rutas para comentarios
Route::get('/comentarios/tmdb/{tmdbId}/{tipo}', [ComentariosController::class, 'getComentariosByTmdbId']);
Route::apiResource('comentarios', ComentariosController::class);
Route::apiResource('likes_comentarios', LikesComentariosController::class);
