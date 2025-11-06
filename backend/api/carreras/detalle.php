<?php

/**
 * PODIUM - Detalle de Carrera
 * 
 * Obtiene información completa de una carrera específica
 * 
 * Endpoint: GET /backend/api/carreras/detalle.php?id=1
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

try {
    // Validar parámetros
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        sendError('ID de carrera inválido', 400);
    }

    $id_carrera = intval($_GET['id']);
    $db = getDB();

    // Obtener información de la carrera
    $sql = "SELECT 
                c.*,
                cat.nombre AS categoria,
                cat.descripcion AS descripcion_categoria,
                cat.imagen_url AS imagen_categoria,
                co.nombre AS circuito,
                co.longitud_pista,
                co.numero_curvas,
                co.tipo_pista,
                co.capacidad_espectadores,
                co.imagen_url AS imagen_circuito,
                co.descripcion AS descripcion_circuito,
                co.record_vuelta,
                co.dificultad,
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
            WHERE c.id_carrera = ?
            GROUP BY c.id_carrera";

    $carreras = $db->query($sql, [$id_carrera], 'i');

    if (!$carreras || count($carreras) === 0) {
        sendError('Carrera no encontrada', 404);
    }

    $carrera = $carreras[0];

    // Convertir tipos de datos
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
    $carrera['premio_primero'] = $carrera['premio_primero'] ? floatval($carrera['premio_primero']) : null;
    $carrera['premio_segundo'] = $carrera['premio_segundo'] ? floatval($carrera['premio_segundo']) : null;
    $carrera['premio_tercero'] = $carrera['premio_tercero'] ? floatval($carrera['premio_tercero']) : null;

    // Indicadores útiles
    $carrera['tiene_cupos'] = $carrera['cupos_disponibles'] > 0;
    $carrera['esta_lleno'] = $carrera['cupos_disponibles'] <= 0;
    $carrera['acepta_inscripciones'] = $carrera['estado'] === CARRERA_INSCRIPCIONES_ABIERTAS && $carrera['cupos_disponibles'] > 0;

    // Obtener pilotos inscritos (si la carrera ya finalizó o está en curso)
    $pilotos = [];
    if ($carrera['estado'] === CARRERA_EN_CURSO || $carrera['estado'] === CARRERA_FINALIZADA) {
        $sql_pilotos = "SELECT 
                            p.id_piloto,
                            p.nombre,
                            p.apellido,
                            p.apodo,
                            p.imagen_url,
                            comp.numero_competidor,
                            v.nombre AS vehiculo,
                            b.nombre AS marca,
                            t.nombre AS tipo_vehiculo,
                            comp.fecha_compra,
                            comp.estado_pago
                        FROM compras comp
                        INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
                        LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
                        LEFT JOIN brand b ON v.id_brand = b.id_brand
                        LEFT JOIN type t ON v.id_type = t.id_type
                        WHERE comp.id_carrera = ?
                            AND comp.estado_pago IN ('" . PAGO_COMPLETADO . "', '" . PAGO_PENDIENTE . "')
                        ORDER BY comp.numero_competidor ASC";

        $pilotos = $db->query($sql_pilotos, [$id_carrera], 'i');

        foreach ($pilotos as &$piloto) {
            $piloto['id_piloto'] = intval($piloto['id_piloto']);
            $piloto['numero_competidor'] = intval($piloto['numero_competidor']);
        }
    }

    // Obtener resultados (si la carrera finalizó)
    $resultados = [];
    if ($carrera['estado'] === CARRERA_FINALIZADA) {
        $sql_resultados = "SELECT 
                                r.id_resultado,
                                r.posicion_final,
                                r.tiempo_total,
                                r.mejor_vuelta,
                                r.puntos_obtenidos,
                                r.observaciones,
                                p.id_piloto,
                                p.nombre,
                                p.apellido,
                                p.apodo,
                                p.imagen_url,
                                comp.numero_competidor,
                                v.nombre AS vehiculo,
                                b.nombre AS marca
                            FROM resultados r
                            INNER JOIN compras comp ON r.id_compra = comp.id_compra
                            INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
                            LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
                            LEFT JOIN brand b ON v.id_brand = b.id_brand
                            WHERE comp.id_carrera = ?
                            ORDER BY r.posicion_final ASC";

        $resultados = $db->query($sql_resultados, [$id_carrera], 'i');

        foreach ($resultados as &$resultado) {
            $resultado['id_resultado'] = intval($resultado['id_resultado']);
            $resultado['id_piloto'] = intval($resultado['id_piloto']);
            $resultado['posicion_final'] = intval($resultado['posicion_final']);
            $resultado['numero_competidor'] = intval($resultado['numero_competidor']);
            $resultado['puntos_obtenidos'] = $resultado['puntos_obtenidos'] ? intval($resultado['puntos_obtenidos']) : null;
        }
    }

    sendSuccess([
        'carrera' => $carrera,
        'pilotos' => $pilotos,
        'resultados' => $resultados
    ]);
} catch (Exception $e) {
    logMessage("Error en detalle de carrera: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener detalle de carrera', 500);
}
