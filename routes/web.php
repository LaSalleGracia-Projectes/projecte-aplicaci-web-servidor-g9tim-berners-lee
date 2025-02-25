<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CriticosController;
use App\Http\Controllers\PeliculasController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\TendenciasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RandomController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/criticos', [CriticosController::class, 'index'])->name('criticos');

Route::get('/peliculas', [PeliculasController::class, 'index'])->name('peliculas');
Route::get('/peliculas/{id}', [PeliculasController::class, 'show'])->name('pelicula.detail');

Route::get('/infoPelicula/{id}', [PeliculasController::class, 'show'])->name('pelicula.show');

Route::get('/series', [SeriesController::class, 'index'])->name('series');

Route::get('/tendencias', [TendenciasController::class, 'index'])->name('tendencias');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/random/generate', [RandomController::class, 'generate'])->name('random.generate');
