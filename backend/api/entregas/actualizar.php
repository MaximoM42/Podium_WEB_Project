<?php

/**
 * PODIUM - Actualizar Estado de Entrega
 * 
 * Permite al gestor actualizar el estado de una entrega
 * 
 * Endpoint: PUT /backend/api/entregas/actualizar.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir POST/PUT
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendError('Método no permitido', 405);
}

// Requiere autenticación
requireLogin();

try {
    // Verificar permisos
    $roles_permitidos = [ROL_ADMIN, ROL_GESTOR_ENTREGAS, 'supervisor'];
    if (!in_array($_SESSION['rol'], $roles_permitidos)) {
        sendError('No tiene permisos para actualizar entregas', 403);
    }

    // Obtener datos
    $data = getJSONInput();

    // Validar datos requeridos
    if (!isset($data['id_entrega']) || !is_numeric($data['id_entrega'])) {
        sendError('ID de entrega inválido', 400);
    }

    $id_entrega = intval($data['id_entrega']);
    $db = getDB();

    // Verificar que la entrega existe
    $sql_check = "SELECT id_entrega, estado FROM entregas WHERE id_entrega = ?";
    $entregas = $db->query($sql_check, [$id_entrega], 'i');

    if (!$entregas || count($entregas) === 0) {
        sendError('Entrega no encontrada', 404);
    }

    $entrega_actual = $entregas[0];

    // Preparar datos para actualizar
    $campos_actualizar = [];
    $params = [];
    $types = '';

    // Estado
    if (isset($data['estado'])) {
        $estados_validos = [
            ENTREGA_PENDIENTE,
            ENTREGA_EN_PROCESO,
            ENTREGA_LISTA,
            ENTREGA_ENTREGADA,
            ENTREGA_DEVUELTA,
            ENTREGA_CANCELADA
        ];

        if (!in_array($data['estado'], $estados_validos)) {
            sendError('Estado de entrega inválido', 400);
        }

        $campos_actualizar[] = "estado = ?";
        $params[] = $data['estado'];
        $types .= 's';

        // Si se marca como entregada, registrar fecha de entrega real
        if ($data['estado'] === ENTREGA_ENTREGADA && $entrega_actual['estado'] !== ENTREGA_ENTREGADA) {
            $campos_actualizar[] = "fecha_entrega_real = NOW()";
        }
    }

    // Fecha de entrega estimada
    if (isset($data['fecha_entrega_estimada'])) {
        $campos_actualizar[] = "fecha_entrega_estimada = ?";
        $params[] = $data['fecha_entrega_estimada'];
        $types .= 's';
    }

    // Almacén
    if (isset($data['id_storage'])) {
        if ($data['id_storage'] === null) {
            $campos_actualizar[] = "id_storage = NULL";
        } else {
            $campos_actualizar[] = "id_storage = ?";
            $params[] = intval($data['id_storage']);
            $types .= 'i';
        }
    }

    // Observaciones
    if (isset($data['observaciones'])) {
        $campos_actualizar[] = "observaciones = ?";
        $params[] = $data['observaciones'];
        $types .= 's';
    }

    // Verificar que hay algo para actualizar
    if (empty($campos_actualizar)) {
        sendError('No hay datos para actualizar', 400);
    }

    // Construir y ejecutar UPDATE
    $sql_update = "UPDATE entregas SET " . implode(', ', $campos_actualizar) . " WHERE id_entrega = ?";
    $params[] = $id_entrega;
    $types .= 'i';

    $resultado = $db->execute($sql_update, $params, $types);

    if ($resultado === false) {
        throw new Exception('Error al actualizar entrega');
    }

    // Log de la acción
    $usuario = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
    $cambios = json_encode($data, JSON_UNESCAPED_UNICODE);
    logMessage("Entrega #$id_entrega actualizada por $usuario - Cambios: $cambios");

    // Obtener entrega actualizada
    $sql_entrega = "SELECT 
                        e.*,
                        comp.numero_competidor,
                        p.nombre AS nombre_piloto,
                        p.apellido AS apellido_piloto,
                        c.nombre AS nombre_carrera,
                        s.nombre AS almacen
                    FROM entregas e
                    INNER JOIN compras comp ON e.id_compra = comp.id_compra
                    INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
                    INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
                    LEFT JOIN storage_id s ON e.id_storage = s.id_storage
                    WHERE e.id_entrega = ?";

    $entrega_actualizada = $db->query($sql_entrega, [$id_entrega], 'i');

    sendSuccess([
        'mensaje' => 'Entrega actualizada exitosamente',
        'entrega' => $entrega_actualizada[0]
    ]);
} catch (Exception $e) {
    logMessage("Error al actualizar entrega: " . $e->getMessage(), 'ERROR');
    sendError('Error al actualizar entrega', 500);
}
