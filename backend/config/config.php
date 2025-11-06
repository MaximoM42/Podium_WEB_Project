<?php

/**
 * PODIUM - Sistema de Gestión de Carreras
 * Archivo de Configuración General
 */

// Definir constante de acceso
define('PODIUM_ACCESS', true);

// Configuración de errores (cambiar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
session_start();

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de CORS (para Vue.js)
header('Access-Control-Allow-Origin: http://localhost:5173'); // URL de Vue dev server
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración de la aplicación
define('APP_NAME', 'PODIUM');
define('APP_VERSION', '2.0');
define('APP_URL', 'http://localhost/Podium_WEB_Project/backend');

// Directorios
define('ROOT_DIR', dirname(__DIR__));
define('CONFIG_DIR', ROOT_DIR . '/config');
define('API_DIR', ROOT_DIR . '/api');
define('UTILS_DIR', ROOT_DIR . '/utils');
define('UPLOADS_DIR', ROOT_DIR . '/uploads');

// URLs
define('BASE_URL', APP_URL);
define('API_URL', BASE_URL . '/api');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de archivos
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf']);

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 8);
define('HASH_ALGORITHM', PASSWORD_DEFAULT);
define('HASH_OPTIONS', ['cost' => 12]);

// Estados de carreras
define('CARRERA_PROGRAMADA', 'programada');
define('CARRERA_INSCRIPCIONES', 'inscripciones_abiertas');
define('CARRERA_INSCRIPCIONES_ABIERTAS', 'inscripciones_abiertas'); // Alias
define('CARRERA_EN_CURSO', 'en_curso');
define('CARRERA_FINALIZADA', 'finalizada');
define('CARRERA_CANCELADA', 'cancelada');

// Estados de pago
define('PAGO_PENDIENTE', 'pendiente');
define('PAGO_COMPLETADO', 'completado');
define('PAGO_REEMBOLSADO', 'reembolsado');
define('PAGO_CANCELADO', 'cancelado');

// Estados de entrega (basados en la BD)
define('ENTREGA_PENDIENTE', 'pendiente');
define('ENTREGA_EN_PROCESO', 'en_proceso');
define('ENTREGA_LISTA', 'lista');
define('ENTREGA_ENTREGADA', 'entregada');
define('ENTREGA_DEVUELTA', 'devuelta');
define('ENTREGA_CANCELADA', 'cancelada');

// Roles de usuario
define('ROL_ADMIN', 'admin');
define('ROL_GESTOR', 'gestor_entregas');
define('ROL_GESTOR_ENTREGAS', 'gestor_entregas'); // Alias
define('ROL_SUPERVISOR', 'supervisor');
define('ROL_JUEZ', 'juez');

// Mensajes de respuesta
define('MSG_SUCCESS', 'Operación exitosa');
define('MSG_ERROR', 'Ha ocurrido un error');
define('MSG_NOT_FOUND', 'Recurso no encontrado');
define('MSG_UNAUTHORIZED', 'No autorizado');
define('MSG_FORBIDDEN', 'Acceso denegado');
define('MSG_VALIDATION_ERROR', 'Error de validación');

// Cargar conexión a base de datos
require_once CONFIG_DIR . '/conexion.php';

// Función para verificar si el usuario está logueado
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

// Función para verificar rol del usuario
function hasRole($role)
{
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Función para verificar si el usuario es admin
function isAdmin()
{
    return hasRole(ROL_ADMIN);
}

// Función para requerir login
function requireLogin()
{
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => MSG_UNAUTHORIZED
        ]);
        exit();
    }
}

// Función para requerir rol específico
function requireRole($role)
{
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => MSG_FORBIDDEN
        ]);
        exit();
    }
}

// Función para enviar respuesta JSON
function sendJSON($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// Función para enviar respuesta de éxito
function sendSuccess($data = [], $message = MSG_SUCCESS, $statusCode = 200)
{
    sendJSON([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

// Función para enviar respuesta de error
function sendError($message = MSG_ERROR, $statusCode = 400, $errors = [])
{
    sendJSON([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}

// Función para validar email
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Función para validar teléfono
function validatePhone($phone)
{
    return preg_match('/^\+?[0-9\s\-()]+$/', $phone);
}

// Función para generar token aleatorio
function generateToken($length = 32)
{
    return bin2hex(random_bytes($length));
}

// Función para hashear contraseña
function hashPassword($password)
{
    return password_hash($password, HASH_ALGORITHM, HASH_OPTIONS);
}

// Función para verificar contraseña
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

// Función para limpiar input
function cleanInput($data)
{
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Función para obtener datos POST como JSON
function getJSONInput()
{
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}

// Función para logging
function logMessage($message, $level = 'INFO')
{
    $logFile = ROOT_DIR . '/logs/app.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;

    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Función para crear directorio de uploads si no existe
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}
