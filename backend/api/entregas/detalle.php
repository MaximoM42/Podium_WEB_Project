<?php

/**
 * PODIUM - Detalle de Entrega
 * 
 * Obtiene información completa de una entrega
 * 
 * Endpoint: GET /backend/api/entregas/detalle.php?id=1
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

// Requiere autenticación
requireLogin();

try {
    // Verificar permisos
    $roles_permitidos = [ROL_ADMIN, ROL_GESTOR_ENTREGAS, 'supervisor'];
    if (!in_array($_SESSION['rol'], $roles_permitidos)) {
        sendError('No tiene permisos para ver entregas', 403);
    }

    // Validar parámetros
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        sendError('ID de entrega inválido', 400);
    }

    $id_entrega = intval($_GET['id']);
    $db = getDB();

    // Obtener información completa de la entrega
    $sql = "SELECT 
                e.*,
                comp.id_compra,
                comp.numero_competidor,
                comp.fecha_compra,
                comp.estado_pago,
                comp.monto_total,
                comp.metodo_pago,
                p.id_piloto,
                p.nombre AS nombre_piloto,
                p.apellido AS apellido_piloto,
                p.email AS email_piloto,
                p.telefono AS telefono_piloto,
                p.direccion AS direccion_piloto,
                p.ciudad AS ciudad_piloto,
                p.codigo_postal AS codigo_postal_piloto,
                c.id_carrera,
                c.nombre AS nombre_carrera,
                c.descripcion AS descripcion_carrera,
                c.fecha_inicio AS fecha_carrera,
                c.imagen_url AS imagen_carrera,
                cat.nombre AS categoria,
                cou.nombre AS circuito,
                v.id_vehicle,
                v.nombre AS vehiculo,
                v.descripcion AS descripcion_vehiculo,
                b.nombre AS marca,
                t.nombre AS tipo_vehiculo,
                s.id_storage,
                s.nombre AS almacen,
                s.direccion AS direccion_almacen,
                s.ciudad AS ciudad_almacen,
                s.telefono AS telefono_almacen
            FROM entregas e
            INNER JOIN compras comp ON e.id_compra = comp.id_compra
            INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
            INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
            INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
            INNER JOIN country cou ON c.id_country = cou.id_country
            LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
            LEFT JOIN brand b ON v.id_brand = b.id_brand
            LEFT JOIN type t ON v.id_type = t.id_type
            LEFT JOIN storage_id s ON e.id_storage = s.id_storage
            WHERE e.id_entrega = ?";

    $entregas = $db->query($sql, [$id_entrega], 'i');

    if (!$entregas || count($entregas) === 0) {
        sendError('Entrega no encontrada', 404);
    }

    $entrega = $entregas[0];

    // Convertir tipos de datos
    $entrega['id_entrega'] = intval($entrega['id_entrega']);
    $entrega['id_compra'] = intval($entrega['id_compra']);
    $entrega['id_piloto'] = intval($entrega['id_piloto']);
    $entrega['id_carrera'] = intval($entrega['id_carrera']);
    $entrega['numero_competidor'] = intval($entrega['numero_competidor']);
    $entrega['monto_total'] = floatval($entrega['monto_total']);
    $entrega['id_vehicle'] = $entrega['id_vehicle'] ? intval($entrega['id_vehicle']) : null;
    $entrega['id_storage'] = $entrega['id_storage'] ? intval($entrega['id_storage']) : null;

    // Calcular días hasta/desde entrega
    if ($entrega['fecha_entrega_estimada']) {
        $fecha_estimada = new DateTime($entrega['fecha_entrega_estimada']);
        $hoy = new DateTime();
        $diff = $hoy->diff($fecha_estimada);
        $entrega['dias_hasta_entrega'] = $diff->invert ? -$diff->days : $diff->days;
        $entrega['esta_atrasada'] = $diff->invert && $entrega['estado'] !== ENTREGA_ENTREGADA;
    }

    // Información adicional
    $entrega['piloto_completo'] = trim($entrega['nombre_piloto'] . ' ' . $entrega['apellido_piloto']);
    $entrega['direccion_completa'] = trim(
        ($entrega['direccion_piloto'] ?? '') . ', ' .
            ($entrega['ciudad_piloto'] ?? '') . ' ' .
            ($entrega['codigo_postal_piloto'] ?? '')
    );

    sendSuccess([
        'entrega' => $entrega
    ]);
} catch (Exception $e) {
    logMessage("Error al obtener detalle de entrega: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener detalle de entrega', 500);
}
