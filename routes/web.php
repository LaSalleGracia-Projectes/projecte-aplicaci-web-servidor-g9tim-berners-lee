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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComentariosController;

// Rutas de perfil - protegidas por autenticación
    Route::get('/profile/{id}', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{id}/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}/photo', [UserProfileController::class, 'obtenerFotoPerfil'])->name('profile.foto');
    Route::get('/profile/change-password', [UserProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('profile.password.update');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('listas', App\Http\Controllers\ListasController::class);
Route::post('/api/contenido-listas', [App\Http\Controllers\ContenidoListasController::class, 'store']);
Route::delete('/api/contenido-listas/{id}', [App\Http\Controllers\ContenidoListasController::class, 'destroy'])
    ->name('contenido-listas.destroy');

Route::get('/criticos', [CriticosController::class, 'index'])->name('criticos');
Route::get('/haztecritico', [CriticosController::class, 'index'])->name('haztecritico');

Route::get('/peliculas', [PeliculasController::class, 'index'])->name('peliculas');
Route::get('/pelicula/{id}', [PeliculasController::class, 'show'])->name('pelicula.detail');

Route::get('/infoPelicula/{id}', [PeliculasController::class, 'show'])->name('pelicula.show');

Route::get('/series', [SeriesController::class, 'index'])->name('series');
Route::get('/serie/{id}', [SeriesController::class, 'show'])->name('serie.detail');

Route::get('/tendencias', [TendenciasController::class, 'index'])->name('tendencias');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/random/generate', [RandomController::class, 'generate'])->name('random.generate');

// Ruta temporal para crear admin (¡ELIMINAR EN PRODUCCIÓN!)
Route::get('/create-admin', function () {
    try {
        // Verificar si ya existe un usuario con este email
        $existingUser = \App\Models\User::where('email', 'admin@critflix.com')->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'El usuario administrador ya existe',
                'user' => $existingUser
            ]);
        }

        $admin = \App\Models\User::create([
            'name' => 'Administrador',
            'email' => 'admin@critflix.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'rol' => 'admin',
            'email_verified_at' => now()
        ]);

        return response()->json([
            'message' => 'Usuario administrador creado con éxito',
            'user' => $admin
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Rutas de administrador - protegidas por autenticación y rol de administrador
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function () {
    Route::group(['middleware' => [\App\Http\Middleware\AdminMiddleware::class]], function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/movies', [AdminController::class, 'movies'])->name('admin.movies');
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
        Route::get('/comments', [AdminController::class, 'comments'])->name('admin.comments');
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    });
});

// API routes - protegidas por autenticación y rol de administrador
Route::group(['prefix' => 'api/admin', 'middleware' => ['web', 'auth', \App\Http\Middleware\AdminMiddleware::class]], function () {
    // Usuarios
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::post('/users/{id}/make-admin', [AdminController::class, 'makeAdmin']);

    // Películas
    Route::delete('/movies/{id}', [AdminController::class, 'deleteMovie']);

    // Valoraciones
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview']);

    // Comentarios
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);
    Route::post('/comments/{id}/highlight', [AdminController::class, 'highlightComment']);
    Route::post('/comments/{id}/unhighlight', [AdminController::class, 'unhighlightComment']);

    // Estadísticas
    Route::get('/stats', [AdminController::class, 'getStats']);
});

// Rutas para comentarios
Route::prefix('api')->group(function () {
    Route::get('/comentarios/pelicula/{id}', [ComentariosController::class, 'getByPelicula']);
    Route::post('/comentarios', [ComentariosController::class, 'store']);
    Route::delete('/comentarios/{id}', [ComentariosController::class, 'destroy']);
});
