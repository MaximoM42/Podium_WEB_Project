<?php
/**
 * PODIUM - API de Carreras
 * 
 * Obtiene la lista de carreras disponibles para el frontend
 * 
 * Endpoint: GET /backend/api/carreras/listar.php
 * Query params: ?estado=inscripciones_abiertas&categoria=1
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

try {
    $db = getDB();
    
    // Parámetros de filtrado
    $estado = isset($_GET['estado']) ? $db->sanitize($_GET['estado']) : null;
    $categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
    $fecha_desde = isset($_GET['fecha_desde']) ? $db->sanitize($_GET['fecha_desde']) : null;
    $fecha_hasta = isset($_GET['fecha_hasta']) ? $db->sanitize($_GET['fecha_hasta']) : null;
    
    // Construir consulta base
    $sql = "SELECT 
                c.id_carrera,
                c.nombre AS nombre_carrera,
                c.descripcion,
                c.fecha_inicio,
                c.fecha_fin,
                c.precio_inscripcion,
                c.imagen_url,
                c.estado,
                c.cupo_maximo,
                c.numero_vueltas,
                c.distancia_total,
                c.premio_primero,
                c.premio_segundo,
                c.premio_tercero,
                c.clima,
                c.transmision_url,
                cat.id_categoria,
                cat.nombre AS categoria,
                cat.descripcion AS descripcion_categoria,
                cat.imagen_url AS imagen_categoria,
                co.id_country,
                co.nombre AS circuito,
                co.longitud_pista,
                co.numero_curvas,
                co.tipo_pista,
                co.capacidad_espectadores,
                co.imagen_url AS imagen_circuito,
                ci.nombre AS ciudad,
                p.nombre AS pais,
                COUNT(DISTINCT comp.id_compra) AS inscriptos,
                (c.cupo_maximo - COUNT(DISTINCT comp.id_compra)) AS cupos_disponibles
            FROM carreras c
            INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
            INNER JOIN country co ON c.id_country = co.id_country
            INNER JOIN city ci ON co.id_city = ci.id_city
            INNER JOIN pais p ON ci.id_pais = p.id_pais
            LEFT JOIN compras comp ON c.id_carrera = comp.id_carrera 
                AND comp.estado_pago IN ('" . PAGO_COMPLETADO . "', '" . PAGO_PENDIENTE . "')
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    // Aplicar filtros
    if ($estado !== null) {
        $sql .= " AND c.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }
    
    if ($categoria !== null) {
        $sql .= " AND c.id_categoria = ?";
        $params[] = $categoria;
        $types .= 'i';
    }
    
    if ($fecha_desde !== null) {
        $sql .= " AND c.fecha_inicio >= ?";
        $params[] = $fecha_desde;
        $types .= 's';
    }
    
    if ($fecha_hasta !== null) {
        $sql .= " AND c.fecha_inicio <= ?";
        $params[] = $fecha_hasta;
        $types .= 's';
    }
    
    $sql .= " GROUP BY c.id_carrera ORDER BY c.fecha_inicio ASC";
    
    // Ejecutar consulta
    $carreras = empty($params) 
        ? $db->query($sql) 
        : $db->query($sql, $params, $types);
    
    if ($carreras === false) {
        throw new Exception('Error al obtener carreras');
    }
    
    // Convertir tipos de datos
    foreach ($carreras as &$carrera) {
        $carrera['id_carrera'] = intval($carrera['id_carrera']);
        $carrera['id_categoria'] = intval($carrera['id_categoria']);
        $carrera['id_country'] = intval($carrera['id_country']);
        $carrera['precio_inscripcion'] = floatval($carrera['precio_inscripcion']);
        $carrera['cupo_maximo'] = intval($carrera['cupo_maximo']);
        $carrera['inscriptos'] = intval($carrera['inscriptos']);
        $carrera['cupos_disponibles'] = intval($carrera['cupos_disponibles']);
        $carrera['numero_vueltas'] = $carrera['numero_vueltas'] ? intval($carrera['numero_vueltas']) : null;
        $carrera['distancia_total'] = $carrera['distancia_total'] ? floatval($carrera['distancia_total']) : null;
        $carrera['longitud_pista'] = $carrera['longitud_pista'] ? floatval($carrera['longitud_pista']) : null;
        $carrera['numero_curvas'] = $carrera['numero_curvas'] ? intval($carrera['numero_curvas']) : null;
        $carrera['capacidad_espectadores'] = $carrera['capacidad_espectadores'] ? intval($carrera['capacidad_espectadores']) : null;
        
        // Indicar si hay cupos disponibles
        $carrera['tiene_cupos'] = $carrera['cupos_disponibles'] > 0;
        $carrera['esta_lleno'] = $carrera['cupos_disponibles'] <= 0;
    }
    
    sendSuccess([
        'carreras' => $carreras,
        'total' => count($carreras)
    ]);
    
} catch (Exception $e) {
    logMessage("Error en listar carreras: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener carreras', 500);
}

?>
