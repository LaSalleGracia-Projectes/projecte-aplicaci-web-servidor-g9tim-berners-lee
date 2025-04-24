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
