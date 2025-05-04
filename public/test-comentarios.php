<?php

// Script para depuración de comentarios en la base de datos

// Cargar autoloader de Laravel
require __DIR__.'/../vendor/autoload.php';

// Cargar el bootstrap de la aplicación
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Configurar encabezados
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Información de la base de datos
    $dbInfo = [
        'database_name' => env('DB_DATABASE'),
        'database_connection' => env('DB_CONNECTION'),
        'database_host' => env('DB_HOST'),
        'tablas_existen' => [
            'comentarios' => \Illuminate\Support\Facades\Schema::hasTable('comentarios'),
            'respuestas_comentarios' => \Illuminate\Support\Facades\Schema::hasTable('respuestas_comentarios'),
            'likes_comentarios' => \Illuminate\Support\Facades\Schema::hasTable('likes_comentarios'),
        ]
    ];

    // Contar comentarios en la base de datos
    $totalComentarios = \App\Models\Comentarios::count();
    $comentariosPeliculas = \App\Models\Comentarios::where('tipo', 'pelicula')->count();
    $comentariosSeries = \App\Models\Comentarios::where('tipo', 'serie')->count();

    // Listar algunos comentarios de ejemplo si existen
    $ejemplosComentarios = [];
    if ($totalComentarios > 0) {
        $ejemplosComentarios = \App\Models\Comentarios::with('usuario')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    // Comprobar estructura del modelo
    $modelInfo = [
        'campos_fillable' => (new \App\Models\Comentarios())->getFillable(),
        'tiene_campo_tmdb_id' => in_array('tmdb_id', (new \App\Models\Comentarios())->getFillable()),
        'tiene_campo_tipo' => in_array('tipo', (new \App\Models\Comentarios())->getFillable()),
    ];

    // Verificar si hay mapeo erróneo en las relaciones
    $movieController = new \App\Http\Controllers\ComentariosController();
    $reflectionClass = new ReflectionClass($movieController);
    $methods = $reflectionClass->getMethods();
    $methodNames = array_map(function($method) { return $method->getName(); }, $methods);

    // Resultados
    $result = [
        'database_info' => $dbInfo,
        'conteo_comentarios' => [
            'total' => $totalComentarios,
            'peliculas' => $comentariosPeliculas,
            'series' => $comentariosSeries,
        ],
        'ejemplo_comentarios' => $ejemplosComentarios,
        'info_modelo' => $modelInfo,
        'metodos_controller' => $methodNames,
    ];

    echo json_encode($result, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Ha ocurrido un error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
