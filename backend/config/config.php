<?php
// Configuración del backend PHP

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'podium');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// URL base del backend
define('BASE_URL', 'http://localhost/backend/');

// Configuración de Firebase
define('FIREBASE_PROJECT_ID', 'podium-19f2e');

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Habilitar errores en desarrollo (desactivar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// NOTA: Los headers CORS están configurados en .htaccess
// para que Apache los envíe automáticamente con cada respuesta

