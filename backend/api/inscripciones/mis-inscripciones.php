<?php
/**
 * PODIUM - Mis Inscripciones
 * 
 * Obtiene las inscripciones de un piloto (requiere autenticación Firebase)
 * 
 * Endpoint: GET /backend/api/inscripciones/mis-inscripciones.php
 * Headers: Authorization: Bearer {firebase_token}
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

try {
    // Obtener el UID de Firebase del header (simplificado, en producción validar el token)
    $firebase_uid = null;
    if (isset($_GET['firebase_uid'])) {
        $firebase_uid = $_GET['firebase_uid'];
    } else {
        // En producción, aquí validarías el token Bearer de Firebase
        sendError('Se requiere autenticación', 401);
    }
    
    $db = getDB();
    
    // Buscar piloto por firebase_uid
    $sql_piloto = "SELECT id_piloto FROM pilotos WHERE firebase_uid = ?";
    $pilotos = $db->query($sql_piloto, [$firebase_uid], 's');
    
    if (!$pilotos || count($pilotos) === 0) {
        sendError('Piloto no encontrado', 404);
    }
    
    $id_piloto = intval($pilotos[0]['id_piloto']);
    
    // Obtener inscripciones del piloto
    $sql = "SELECT 
                comp.id_compra,
                comp.numero_competidor,
                comp.fecha_compra,
                comp.estado_pago,
                comp.monto_total,
                comp.metodo_pago,
                c.id_carrera,
                c.nombre AS nombre_carrera,
                c.descripcion AS descripcion_carrera,
                c.fecha_inicio,
                c.fecha_fin,
                c.estado AS estado_carrera,
                c.imagen_url AS imagen_carrera,
                cat.nombre AS categoria,
                co.nombre AS circuito,
                ci.nombre AS ciudad,
                p.nombre AS pais,
                v.id_vehicle,
                v.nombre AS vehiculo,
                b.nombre AS marca,
                t.nombre AS tipo_vehiculo,
                e.id_entrega,
                e.fecha_entrega_estimada,
                e.fecha_entrega_real,
                e.estado AS estado_entrega,
                e.observaciones AS observaciones_entrega,
                r.id_resultado,
                r.posicion_final,
                r.tiempo_total,
                r.mejor_vuelta,
                r.puntos_obtenidos
            FROM compras comp
            INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
            INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
            INNER JOIN country co ON c.id_country = co.id_country
            INNER JOIN city ci ON co.id_city = ci.id_city
            INNER JOIN pais p ON ci.id_pais = p.id_pais
            LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
            LEFT JOIN brand b ON v.id_brand = b.id_brand
            LEFT JOIN type t ON v.id_type = t.id_type
            LEFT JOIN entregas e ON comp.id_compra = e.id_compra
            LEFT JOIN resultados r ON comp.id_compra = r.id_compra
            WHERE comp.id_piloto = ?
            ORDER BY c.fecha_inicio DESC, comp.fecha_compra DESC";
    
    $inscripciones = $db->query($sql, [$id_piloto], 'i');
    
    if ($inscripciones === false) {
        throw new Exception('Error al obtener inscripciones');
    }
    
    // Convertir tipos de datos y agrupar por estado
    $proximas = [];
    $en_curso = [];
    $finalizadas = [];
    $pendientes_pago = [];
    
    foreach ($inscripciones as &$insc) {
        $insc['id_compra'] = intval($insc['id_compra']);
        $insc['id_carrera'] = intval($insc['id_carrera']);
        $insc['numero_competidor'] = intval($insc['numero_competidor']);
        $insc['monto_total'] = floatval($insc['monto_total']);
        $insc['id_vehicle'] = $insc['id_vehicle'] ? intval($insc['id_vehicle']) : null;
        $insc['id_entrega'] = $insc['id_entrega'] ? intval($insc['id_entrega']) : null;
        $insc['id_resultado'] = $insc['id_resultado'] ? intval($insc['id_resultado']) : null;
        $insc['posicion_final'] = $insc['posicion_final'] ? intval($insc['posicion_final']) : null;
        $insc['puntos_obtenidos'] = $insc['puntos_obtenidos'] ? intval($insc['puntos_obtenidos']) : null;
        
        // Clasificar por estado
        if ($insc['estado_pago'] === PAGO_PENDIENTE) {
            $pendientes_pago[] = $insc;
        } elseif ($insc['estado_carrera'] === 'finalizada') {
            $finalizadas[] = $insc;
        } elseif ($insc['estado_carrera'] === 'en_curso') {
            $en_curso[] = $insc;
        } else {
            $proximas[] = $insc;
        }
    }
    
    sendSuccess([
        'inscripciones' => $inscripciones,
        'agrupadas' => [
            'pendientes_pago' => $pendientes_pago,
            'proximas' => $proximas,
            'en_curso' => $en_curso,
            'finalizadas' => $finalizadas
        ],
        'total' => count($inscripciones)
    ]);
    
} catch (Exception $e) {
    logMessage("Error en mis inscripciones: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener inscripciones', 500);
}

?>
