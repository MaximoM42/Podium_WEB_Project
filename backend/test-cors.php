<?php
/**
 * Test de headers CORS
 * Verifica que no haya headers duplicados
 */

// Cargar configuración
require_once 'config/config.php';

// Manejar OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Devolver información de headers
$response = [
    'success' => true,
    'message' => 'Test de CORS',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'origin' => $_SERVER['HTTP_ORIGIN'] ?? 'No Origin header',
    'headers_sent' => headers_list(),
    'note' => 'Si ves headers CORS duplicados aquí, hay un problema'
];

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

