<?php
/**
 * PODIUM - API de Autenticación
 * 
 * Endpoint para login de usuarios del backend (gestores, admin, jueces)
 * Incluye verificación de CAPTCHA
 * 
 * Endpoint: POST /backend/api/auth/login.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Método no permitido', 405);
}

// Obtener datos
$input = getJSONInput();

// Validar datos requeridos
if (empty($input['email']) || empty($input['password'])) {
    sendError('Email y contraseña son requeridos', 400);
}

// Validar CAPTCHA
if (empty($input['captcha'])) {
    sendError('CAPTCHA es requerido', 400);
}

try {
    $db = getDB();
    
    // Verificar CAPTCHA de sesión
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] !== strtoupper($input['captcha'])) {
        sendError('CAPTCHA inválido', 400);
    }
    
    // Limpiar CAPTCHA de sesión
    unset($_SESSION['captcha']);
    
    // Sanitizar email
    $email = $db->sanitize($input['email']);
    $password = $input['password']; // No sanitizar password para verificación
    
    // Buscar usuario
    $sql = "SELECT id_usuario, nombre, apellido, email, password, rol, telefono, activo 
            FROM usuarios 
            WHERE email = ? AND activo = TRUE";
    
    $result = $db->query($sql, [$email], 's');
    
    if (empty($result)) {
        // Log intento fallido
        logMessage("Intento de login fallido - Email: $email", 'WARNING');
        sendError('Credenciales inválidas', 401);
    }
    
    $user = $result[0];
    
    // Verificar contraseña
    if (!verifyPassword($password, $user['password'])) {
        logMessage("Intento de login fallido - Email: $email (contraseña incorrecta)", 'WARNING');
        sendError('Credenciales inválidas', 401);
    }
    
    // Actualizar último acceso
    $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?";
    $db->execute($updateSql, [$user['id_usuario']], 'i');
    
    // Crear sesión
    $_SESSION['user_id'] = $user['id_usuario'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
    $_SESSION['user_role'] = $user['rol'];
    $_SESSION['login_time'] = time();
    
    // Log de éxito
    logMessage("Login exitoso - Usuario: {$user['email']}, Rol: {$user['rol']}");
    
    // Enviar respuesta (sin password)
    unset($user['password']);
    
    sendSuccess([
        'user' => $user,
        'session_timeout' => SESSION_TIMEOUT
    ], 'Login exitoso');
    
} catch (Exception $e) {
    logMessage("Error en login: " . $e->getMessage(), 'ERROR');
    sendError('Error al procesar el login', 500);
}

?>
