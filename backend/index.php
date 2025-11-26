<?php
/**
 * API REST para Podium
 * Sistema de gestión de resultados de carreras
 */

// Cargar configuración y clases
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'middleware/auth.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/VehicleController.php';
require_once 'controllers/RaceController.php';
require_once 'controllers/PositionController.php';

// Manejar peticiones OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Obtener el método HTTP y la URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// SOPORTE PARA QUERY STRINGS (sin .htaccess)
// Ejemplo: /backend/index.php?resource=categories&id=TC
if (isset($_GET['resource'])) {
    // Construir segmentos desde query string
    $segments = [$_GET['resource']];
    if (isset($_GET['id'])) {
        $segments[] = $_GET['id'];
    }
    if (isset($_GET['action'])) {
        $segments[] = $_GET['action'];
    }
} else {
    // SOPORTE PARA URL REWRITE (con .htaccess)
    // Remover query string y obtener la ruta limpia
    $uri = parse_url($uri, PHP_URL_PATH);
    
    // Remover el prefijo /backend si existe
    $uri = preg_replace('#^/backend#', '', $uri);
    
    // Dividir la URI en segmentos
    $segments = array_values(array_filter(explode('/', $uri)));
}

// Router simple
try {
    // Verificar que hay al menos un segmento
    if (empty($segments)) {
        echo json_encode([
            'success' => true,
            'message' => 'API Podium v1.0',
            'endpoints' => [
                'users' => '/users',
                'categories' => '/categories',
                'vehicles' => '/vehicles',
                'races' => '/races',
                'positions' => '/positions'
            ]
        ]);
        exit;
    }
    
    $resource = $segments[0];
    $id = $segments[1] ?? null;
    $action = $segments[2] ?? null;
    
    // Rutas de Usuarios
    if ($resource === 'users') {
        $controller = new UserController();
        
        if ($method === 'POST' && !$id) {
            $controller->createOrUpdate();
        } elseif ($method === 'GET' && $id === 'current') {
            $controller->getCurrentUser();
        } elseif ($method === 'GET' && !$id) {
            $controller->listAll();
        } elseif ($method === 'PUT' && $id && $action === 'role') {
            $controller->updateRole($id);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
    
    // Rutas de Categorías
    elseif ($resource === 'categories') {
        $controller = new CategoryController();
        
        if ($method === 'GET' && !$id) {
            $controller->getAll();
        } elseif ($method === 'GET' && $id) {
            $controller->getById($id);
        } elseif ($method === 'POST' && !$id) {
            $controller->create();
        } elseif ($method === 'PUT' && $id) {
            $controller->update($id);
        } elseif ($method === 'DELETE' && $id) {
            $controller->delete($id);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
    
    // Rutas de Vehículos
    elseif ($resource === 'vehicles') {
        $controller = new VehicleController();
        
        if ($method === 'GET' && !$id) {
            $controller->getAll();
        } elseif ($method === 'GET' && $id) {
            $controller->getById($id);
        } elseif ($method === 'POST' && !$id) {
            $controller->create();
        } elseif ($method === 'PUT' && $id) {
            $controller->update($id);
        } elseif ($method === 'DELETE' && $id) {
            $controller->delete($id);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
    
    // Rutas de Carreras
    elseif ($resource === 'races') {
        $controller = new RaceController();
        
        if ($method === 'GET' && !$id) {
            $controller->getAll();
        } elseif ($method === 'GET' && $id && $action === 'positions') {
            $controller->getByCategoryWithPositions($id);
        } elseif ($method === 'GET' && $id) {
            $controller->getById($id);
        } elseif ($method === 'POST' && !$id) {
            $controller->create();
        } elseif ($method === 'PUT' && $id) {
            $controller->update($id);
        } elseif ($method === 'DELETE' && $id) {
            $controller->delete($id);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
    
    // Rutas de Posiciones
    elseif ($resource === 'positions') {
        $controller = new PositionController();
        
        if ($method === 'GET' && $id) {
            $controller->getByRace($id);
        } elseif ($method === 'POST' && $action === 'bulk') {
            $controller->updateBulk();
        } elseif ($method === 'POST' && !$id) {
            $controller->create();
        } elseif ($method === 'PUT' && $id) {
            $controller->update($id);
        } elseif ($method === 'DELETE' && $id) {
            $controller->delete($id);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
        }
    }
    
    // Recurso no encontrado
    else {
        header('HTTP/1.1 404 Not Found');
        http_response_code(404);
        echo json_encode(['error' => 'Recurso no encontrado']);
    }
    
} catch (Exception $e) {
    // Asegurar que el código HTTP se envíe correctamente
    header('HTTP/1.1 500 Internal Server Error');
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage()
    ]);
}

