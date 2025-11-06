<?php
/**
 * PODIUM - Logout
 * 
 * Cierra la sesión del usuario backend
 * 
 * Endpoint: POST /backend/api/auth/logout.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Método no permitido', 405);
}

try {
    // Verificar que haya sesión activa
    if (!isset($_SESSION['usuario_id'])) {
        sendError('No hay sesión activa', 400);
    }
    
    $usuario_id = $_SESSION['usuario_id'];
    $email = $_SESSION['email'] ?? 'desconocido';
    
    // Destruir sesión
    session_unset();
    session_destroy();
    
    // Log del logout
    logMessage("Logout exitoso - Usuario: $email (ID: $usuario_id)");
    
    sendSuccess([
        'mensaje' => 'Sesión cerrada exitosamente'
    ]);
    
} catch (Exception $e) {
    logMessage("Error en logout: " . $e->getMessage(), 'ERROR');
    sendError('Error al cerrar sesión', 500);
}

?>
