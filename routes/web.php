<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ContenidoListasController;
use App\Http\Controllers\LikesComentariosController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PeliculasSeriesController;
use App\Http\Controllers\RecomendacionesController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ValoracionesController;


Route::get('/', function () {
    return view('home');
});

Route::resource('administradores', AdministradoresController::class);
Route::resource('comentarios', ComentariosController::class);
Route::resource('contenidolistas', ContenidoListasController::class);
Route::resource('likescomentarios', LikesComentariosController::class);
Route::resource('listas', ListasController::class);
Route::resource('notificaciones', NotificacionesController::class);
Route::resource('peliculasseries', PeliculasSeriesController::class);
Route::resource('recomendaciones', RecomendacionesController::class);
Route::resource('seguimiento', SeguimientoController::class);
Route::resource('notificaciones', UsuariosController::class);
Route::resource('notificaciones', ValoracionesController::class);
