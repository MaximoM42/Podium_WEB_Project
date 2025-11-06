<?php

/**
 * PODIUM - Listar Entregas (Backend)
 * 
 * Obtiene la lista de entregas para gestión
 * Requiere autenticación y rol de gestor o superior
 * 
 * Endpoint: GET /backend/api/entregas/listar.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

// Requiere autenticación
requireLogin();

try {
    // Verificar permisos (gestor_entregas, supervisor, admin)
    $roles_permitidos = [ROL_ADMIN, ROL_GESTOR_ENTREGAS, 'supervisor'];
    if (!in_array($_SESSION['rol'], $roles_permitidos)) {
        sendError('No tiene permisos para ver entregas', 403);
    }

    $db = getDB();

    // Parámetros de filtrado
    $estado = isset($_GET['estado']) ? $db->sanitize($_GET['estado']) : null;
    $fecha_desde = isset($_GET['fecha_desde']) ? $db->sanitize($_GET['fecha_desde']) : null;
    $fecha_hasta = isset($_GET['fecha_hasta']) ? $db->sanitize($_GET['fecha_hasta']) : null;
    $id_carrera = isset($_GET['id_carrera']) ? intval($_GET['id_carrera']) : null;

    // Construir consulta
    $sql = "SELECT 
                e.id_entrega,
                e.id_compra,
                e.fecha_entrega_estimada,
                e.fecha_entrega_real,
                e.estado,
                e.observaciones,
                e.fecha_creacion,
                comp.numero_competidor,
                comp.fecha_compra,
                comp.estado_pago,
                p.id_piloto,
                p.nombre AS nombre_piloto,
                p.apellido AS apellido_piloto,
                p.email AS email_piloto,
                p.telefono AS telefono_piloto,
                c.id_carrera,
                c.nombre AS nombre_carrera,
                c.fecha_inicio AS fecha_carrera,
                cat.nombre AS categoria,
                v.id_vehicle,
                v.nombre AS vehiculo,
                b.nombre AS marca,
                s.id_storage,
                s.nombre AS almacen
            FROM entregas e
            INNER JOIN compras comp ON e.id_compra = comp.id_compra
            INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
            INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
            INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
            LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
            LEFT JOIN brand b ON v.id_brand = b.id_brand
            LEFT JOIN storage_id s ON e.id_storage = s.id_storage
            WHERE 1=1";

    $params = [];
    $types = '';

    // Aplicar filtros
    if ($estado !== null) {
        $sql .= " AND e.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }

    if ($fecha_desde !== null) {
        $sql .= " AND e.fecha_entrega_estimada >= ?";
        $params[] = $fecha_desde;
        $types .= 's';
    }

    if ($fecha_hasta !== null) {
        $sql .= " AND e.fecha_entrega_estimada <= ?";
        $params[] = $fecha_hasta;
        $types .= 's';
    }

    if ($id_carrera !== null) {
        $sql .= " AND c.id_carrera = ?";
        $params[] = $id_carrera;
        $types .= 'i';
    }

    $sql .= " ORDER BY 
                CASE e.estado
                    WHEN '" . ENTREGA_PENDIENTE . "' THEN 1
                    WHEN '" . ENTREGA_EN_PROCESO . "' THEN 2
                    WHEN '" . ENTREGA_LISTA . "' THEN 3
                    WHEN '" . ENTREGA_ENTREGADA . "' THEN 4
                    ELSE 5
                END,
                e.fecha_entrega_estimada ASC";

    // Ejecutar consulta
    $entregas = empty($params)
        ? $db->query($sql)
        : $db->query($sql, $params, $types);

    if ($entregas === false) {
        throw new Exception('Error al obtener entregas');
    }

    // Convertir tipos de datos y agregar información útil
    foreach ($entregas as &$entrega) {
        $entrega['id_entrega'] = intval($entrega['id_entrega']);
        $entrega['id_compra'] = intval($entrega['id_compra']);
        $entrega['id_piloto'] = intval($entrega['id_piloto']);
        $entrega['id_carrera'] = intval($entrega['id_carrera']);
        $entrega['numero_competidor'] = intval($entrega['numero_competidor']);
        $entrega['id_vehicle'] = $entrega['id_vehicle'] ? intval($entrega['id_vehicle']) : null;
        $entrega['id_storage'] = $entrega['id_storage'] ? intval($entrega['id_storage']) : null;

        // Calcular días hasta entrega
        if ($entrega['fecha_entrega_estimada']) {
            $fecha_estimada = new DateTime($entrega['fecha_entrega_estimada']);
            $hoy = new DateTime();
            $diff = $hoy->diff($fecha_estimada);
            $entrega['dias_hasta_entrega'] = $diff->invert ? -$diff->days : $diff->days;
            $entrega['esta_atrasada'] = $diff->invert && $entrega['estado'] !== ENTREGA_ENTREGADA;
        }

        // Nombre completo del piloto
        $entrega['piloto_completo'] = trim($entrega['nombre_piloto'] . ' ' . $entrega['apellido_piloto']);
    }

    // Estadísticas rápidas
    $total = count($entregas);
    $pendientes = count(array_filter($entregas, fn($e) => $e['estado'] === ENTREGA_PENDIENTE));
    $en_proceso = count(array_filter($entregas, fn($e) => $e['estado'] === ENTREGA_EN_PROCESO));
    $listas = count(array_filter($entregas, fn($e) => $e['estado'] === ENTREGA_LISTA));
    $entregadas = count(array_filter($entregas, fn($e) => $e['estado'] === ENTREGA_ENTREGADA));
    $atrasadas = count(array_filter($entregas, fn($e) => isset($e['esta_atrasada']) && $e['esta_atrasada']));

    sendSuccess([
        'entregas' => $entregas,
        'estadisticas' => [
            'total' => $total,
            'pendientes' => $pendientes,
            'en_proceso' => $en_proceso,
            'listas' => $listas,
            'entregadas' => $entregadas,
            'atrasadas' => $atrasadas
        ]
    ]);
} catch (Exception $e) {
    logMessage("Error en listar entregas: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener entregas', 500);
}
