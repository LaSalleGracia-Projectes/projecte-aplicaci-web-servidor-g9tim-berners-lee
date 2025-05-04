<?php

// Este script prueba las rutas de API para comentarios

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
    // Obtener el ID de película desde la URL
    $tmdbId = isset($_GET['id']) ? (int)$_GET['id'] : 505642; // Valor por defecto: Lego Batman

    // Rutas a probar
    $rutas = [
        'antigua' => "/api/comentarios/tmdb/{$tmdbId}/pelicula",
        'nueva' => "/api/comentarios-pelicula/{$tmdbId}",
    ];

    $resultados = [];

    // Probar la ruta antigua
    $request = Request::create($rutas['antigua'], 'GET');
    $response = app()->handle($request);
    $resultados['antigua'] = [
        'status' => $response->getStatusCode(),
        'content' => json_decode($response->getContent(), true)
    ];

    // Probar la ruta nueva
    $request = Request::create($rutas['nueva'], 'GET');
    $response = app()->handle($request);
    $resultados['nueva'] = [
        'status' => $response->getStatusCode(),
        'content' => json_decode($response->getContent(), true)
    ];

    // Verificar rutas existentes en el sistema
    $routes = app()->make('router')->getRoutes();
    $apiRoutes = [];

    foreach ($routes as $route) {
        if (str_contains($route->uri, 'api') &&
            (str_contains($route->uri, 'comentarios') || str_contains($route->uri, 'coment'))) {
            $apiRoutes[] = [
                'method' => implode('|', $route->methods),
                'uri' => $route->uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }
    }

    // Revisar controlador para ver si el método existe
    $controllerExists = class_exists('App\Http\Controllers\ComentariosController');
    $methodInfo = null;

    if ($controllerExists) {
        $reflection = new ReflectionClass('App\Http\Controllers\ComentariosController');
        $methodInfo = [
            'getComentariosPelicula_exists' => $reflection->hasMethod('getComentariosPelicula'),
            'getComentariosByTmdbId_exists' => $reflection->hasMethod('getComentariosByTmdbId')
        ];

        if ($reflection->hasMethod('getComentariosPelicula')) {
            $method = $reflection->getMethod('getComentariosPelicula');
            $methodInfo['getComentariosPelicula_public'] = $method->isPublic();
        }
    }

    echo json_encode([
        'tmdb_id' => $tmdbId,
        'rutas_probadas' => $rutas,
        'resultados' => $resultados,
        'rutas_api_comentarios' => $apiRoutes,
        'controlador_info' => [
            'controlador_existe' => $controllerExists,
            'metodos' => $methodInfo
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Ha ocurrido un error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
