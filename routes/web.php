<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CriticosController;
use App\Http\Controllers\PeliculasController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\TendenciasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RandomController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UsuariosController;

// Rutas de perfil - todas públicas temporalmente para demo
Route::get('/profile/{id}', [UserProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
Route::get('/profile/change-password', [UserProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('profile.password.update');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/criticos', [CriticosController::class, 'index'])->name('criticos');
Route::get('/haztecritico', [CriticosController::class, 'index'])->name('haztecritico');

Route::get('/peliculas', [PeliculasController::class, 'index'])->name('peliculas');
Route::get('/pelicula/{id}', [PeliculasController::class, 'show'])->name('pelicula.detail');

Route::get('/infoPelicula/{id}', [PeliculasController::class, 'show'])->name('pelicula.show');

Route::get('/series', [SeriesController::class, 'index'])->name('series');

Route::get('/tendencias', [TendenciasController::class, 'index'])->name('tendencias');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/random/generate', [RandomController::class, 'generate'])->name('random.generate');
