<?php

// Este archivo sirve para probar la configuración CORS del servidor

// Configurar encabezados CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-TOKEN');

// Captura la URL solicitada
$requestedUrl = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'No origin';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referer';

// Información del servidor
$serverInfo = [
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
    'remote_addr' => $_SERVER['REMOTE_ADDR'],
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
];

// Respuesta
$response = [
    'success' => true,
    'message' => 'CORS test successful',
    'request' => [
        'method' => $method,
        'url' => $requestedUrl,
        'origin' => $origin,
        'referer' => $referer,
    ],
    'server' => $serverInfo,
    'timestamp' => date('Y-m-d H:i:s'),
];

echo json_encode($response, JSON_PRETTY_PRINT);
