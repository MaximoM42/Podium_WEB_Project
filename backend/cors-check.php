<?php
// Test directo de CORS (sin depender de config.php)
header('Content-Type: application/json');

$response = [
    'success' => true,
    'message' => 'Test de CORS directo',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'origin' => $_SERVER['HTTP_ORIGIN'] ?? 'No Origin header',
    'request_uri' => $_SERVER['REQUEST_URI'],
    'script_name' => $_SERVER['SCRIPT_NAME'],
    'headers_sent_by_php' => headers_list(),
    'note' => 'Este archivo se ejecuta directamente, sin pasar por el router'
];

echo json_encode($response, JSON_PRETTY_PRINT);

