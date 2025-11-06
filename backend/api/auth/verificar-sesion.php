<?php
/**
 * PODIUM - Verificar Sesión
 * 
 * Verifica si hay una sesión activa y devuelve información del usuario
 * 
 * Endpoint: GET /backend/api/auth/verificar-sesion.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

try {
    // Verificar si hay sesión activa
    if (!isset($_SESSION['usuario_id'])) {
        sendJSON([
            'success' => false,
            'autenticado' => false,
            'mensaje' => 'No hay sesión activa'
        ], 401);
    }
    
    $db = getDB();
    
    // Obtener información actualizada del usuario
    $sql = "SELECT 
                id_usuario,
                nombre,
                apellido,
                email,
                rol,
                ultimo_acceso,
                fecha_creacion
            FROM usuarios
            WHERE id_usuario = ?";
    
    $usuarios = $db->query($sql, [$_SESSION['usuario_id']], 'i');
    
    if (!$usuarios || count($usuarios) === 0) {
        // Usuario no existe, destruir sesión
        session_unset();
        session_destroy();
        sendError('Usuario no encontrado', 404);
    }
    
    $usuario = $usuarios[0];
    
    // Actualizar último acceso
    $sql_update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?";
    $db->execute($sql_update, [$_SESSION['usuario_id']], 'i');
    
    sendSuccess([
        'autenticado' => true,
        'usuario' => [
            'id_usuario' => intval($usuario['id_usuario']),
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'ultimo_acceso' => $usuario['ultimo_acceso'],
            'fecha_creacion' => $usuario['fecha_creacion']
        ]
    ]);
    
} catch (Exception $e) {
    logMessage("Error en verificar sesión: " . $e->getMessage(), 'ERROR');
    sendError('Error al verificar sesión', 500);
}

?>
