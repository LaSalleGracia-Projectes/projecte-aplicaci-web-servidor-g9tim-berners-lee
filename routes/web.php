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
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\PeliculasSeriesController;
use App\Http\Controllers\LanguageController;


// Rutas de perfil - protegidas por autenticación
    Route::get('/profile/{id}', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{id}/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}/photo', [UserProfileController::class, 'obtenerFotoPerfil'])->name('profile.foto');
    Route::get('/profile/change-password', [UserProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('profile.password.update');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/api/contenido-listas', [App\Http\Controllers\ContenidoListasController::class, 'store']);
Route::delete('/api/contenido-listas/{id}', [App\Http\Controllers\ContenidoListasController::class, 'destroy'])
    ->name('contenido-listas.destroy');

// Rutas para las vistas de listas
Route::get('/listas', [ListasController::class, 'index'])->name('listas.index');
Route::get('/listas/create', [ListasController::class, 'create'])->name('listas.create');
Route::post('/listas', [ListasController::class, 'store'])->name('listas.store');
Route::get('/listas/{id}', [ListasController::class, 'show'])->name('listas.show');
Route::get('/listas/{lista}/edit', [ListasController::class, 'edit'])->name('listas.edit');
Route::put('/listas/{id}', [ListasController::class, 'update'])->name('listas.update');
Route::delete('/listas/{id}', [ListasController::class, 'destroy'])->name('listas.destroy');
Route::get('/listas/redirect/destroy/{userId}', [ListasController::class, 'redirectAfterDestroy'])->name('listas.redirect.destroy');
Route::get('/listas/redirect/store/{listaId}', [ListasController::class, 'redirectAfterStore'])->name('listas.redirect.store');
Route::get('/listas/redirect/update/{listaId}', [ListasController::class, 'redirectAfterUpdate'])->name('listas.redirect.update');

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
        Route::put('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.password');
    });
});

// API routes - protegidas por autenticación y rol de administrador
Route::group(['prefix' => 'api/admin', 'middleware' => ['web', 'auth', \App\Http\Middleware\AdminMiddleware::class]], function () {
    // Usuarios
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getUser']);
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


// Rutas para prueba de correos - solo accesibles para administradores
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', \App\Http\Middleware\AdminMiddleware::class]], function () {
    Route::get('/email-test', [EmailTestController::class, 'showTestForm'])->name('admin.email.test.form');
    Route::post('/email-test', [EmailTestController::class, 'sendTestEmail'])->name('admin.email.test');
});

// Ruta directa para prueba de correo (para desarrolladores)
Route::get('/test-email/{email}', [AuthController::class, 'testEmail'])->name('test.email');

// Rutas de autenticación con Google
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Rutas de políticas y contacto
Route::get('/politicas/privacidad', function () {
    return view('policies.privacy');
})->name('policies.privacy');

Route::get('/politicas/terminos', function () {
    return view('policies.terms');
})->name('policies.terms');

Route::get('/contacto', function () {
    return view('policies.contact');
})->name('policies.contact');

// Rutas de idioma
Route::get('/change-language/{locale}', [App\Http\Controllers\LanguageController::class, 'change'])->name('language.change');
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'change']); // Ruta alternativa para compatibilidad

// Ruta para prueba de idioma
Route::get('/test-language', function () {
    return view('test-language');
})->name('test.language');
