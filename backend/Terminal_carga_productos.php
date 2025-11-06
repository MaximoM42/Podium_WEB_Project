<?php

/**
 * PODIUM - Terminal de Carga de Productos
 * 
 * Este archivo recibe las compras/inscripciones desde Vue.js
 * en formato JSON y las almacena en la base de datos MySQL
 * 
 * Endpoint: POST /backend/Terminal_carga_productos.php
 * Content-Type: application/json
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Método no permitido. Use POST', 405);
}

// Obtener datos JSON del request
$input = getJSONInput();

// Validar que se recibieron datos
if (empty($input)) {
    sendError('No se recibieron datos', 400);
}

// Log de la petición
logMessage("Terminal_carga_productos - Datos recibidos: " . json_encode($input));

try {
    // Obtener conexión a la base de datos
    $db = getDB();

    if (!$db->isConnected()) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Validar campos requeridos
    $requiredFields = ['id_piloto', 'id_carrera', 'monto'];
    $errors = [];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            $errors[] = "El campo '$field' es requerido";
        }
    }

    if (!empty($errors)) {
        sendError('Datos incompletos', 400, $errors);
    }

    // Extraer y sanitizar datos
    $id_piloto = intval($input['id_piloto']);
    $id_carrera = intval($input['id_carrera']);
    $monto = floatval($input['monto']);
    $id_vehicle = isset($input['id_vehicle']) ? intval($input['id_vehicle']) : null;
    $numero_competidor = isset($input['numero_competidor']) ? intval($input['numero_competidor']) : null;
    $metodo_pago = isset($input['metodo_pago']) ? $db->sanitize($input['metodo_pago']) : 'Tarjeta';
    $estado_pago = isset($input['estado_pago']) ? $db->sanitize($input['estado_pago']) : PAGO_PENDIENTE;
    $comprobante_url = isset($input['comprobante_url']) ? $db->sanitize($input['comprobante_url']) : null;

    // Datos adicionales en JSON (opcional)
    $datos_json = null;
    if (isset($input['datos_adicionales']) && is_array($input['datos_adicionales'])) {
        $datos_json = json_encode($input['datos_adicionales'], JSON_UNESCAPED_UNICODE);
    }

    // Validaciones de negocio

    // 1. Verificar que el piloto existe
    $sqlPiloto = "SELECT id_piloto, nombre, apellido, email FROM pilotos WHERE id_piloto = ? AND activo = TRUE";
    $piloto = $db->query($sqlPiloto, [$id_piloto], 'i');

    if (empty($piloto)) {
        sendError('El piloto no existe o está inactivo', 404);
    }

    // 2. Verificar que la carrera existe y acepta inscripciones
    $sqlCarrera = "SELECT id_carrera, nombre, estado, cupo_maximo, precio_inscripcion, fecha_inicio 
                   FROM carreras WHERE id_carrera = ?";
    $carrera = $db->query($sqlCarrera, [$id_carrera], 'i');

    if (empty($carrera)) {
        sendError('La carrera no existe', 404);
    }

    $carreraData = $carrera[0];

    // Verificar estado de la carrera
    if (!in_array($carreraData['estado'], [CARRERA_PROGRAMADA, CARRERA_INSCRIPCIONES])) {
        sendError('La carrera no está disponible para inscripciones', 400);
    }

    // Verificar que no haya pasado la fecha de inicio
    if (strtotime($carreraData['fecha_inicio']) < time()) {
        sendError('La fecha de inscripción ha expirado', 400);
    }

    // 3. Verificar que el piloto no esté ya inscrito
    $sqlCheck = "SELECT id_compra FROM compras WHERE id_piloto = ? AND id_carrera = ?";
    $existente = $db->query($sqlCheck, [$id_piloto, $id_carrera], 'ii');

    if (!empty($existente)) {
        sendError('El piloto ya está inscrito en esta carrera', 409);
    }

    // 4. Verificar cupos disponibles
    $sqlCupos = "SELECT COUNT(*) as inscriptos FROM compras 
                 WHERE id_carrera = ? AND estado_pago IN (?, ?)";
    $cuposResult = $db->query($sqlCupos, [$id_carrera, PAGO_COMPLETADO, PAGO_PENDIENTE], 'iss');

    $inscriptos = intval($cuposResult[0]['inscriptos']);
    $cupoMaximo = intval($carreraData['cupo_maximo']);

    if ($inscriptos >= $cupoMaximo) {
        sendError('No hay cupos disponibles para esta carrera', 400);
    }

    // 5. Verificar vehículo si se proporciona
    if ($id_vehicle !== null) {
        $sqlVehicle = "SELECT id_vehicle FROM vehicle WHERE id_vehicle = ? AND activo = TRUE";
        $vehicle = $db->query($sqlVehicle, [$id_vehicle], 'i');

        if (empty($vehicle)) {
            sendError('El vehículo no existe o está inactivo', 404);
        }
    }

    // 6. Asignar número de competidor si no se proporciona
    if ($numero_competidor === null) {
        $sqlMaxNum = "SELECT MAX(numero_competidor) as max_num FROM compras WHERE id_carrera = ?";
        $maxNumResult = $db->query($sqlMaxNum, [$id_carrera], 'i');
        $numero_competidor = !empty($maxNumResult) && $maxNumResult[0]['max_num'] !== null
            ? intval($maxNumResult[0]['max_num']) + 1
            : 1;
    }

    // Iniciar transacción
    $db->beginTransaction();

    try {
        // Insertar la compra/inscripción
        $sqlInsert = "INSERT INTO compras 
                     (id_piloto, id_carrera, id_vehicle, numero_competidor, monto, 
                      estado_pago, metodo_pago, comprobante_url, datos_json) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $id_piloto,
            $id_carrera,
            $id_vehicle,
            $numero_competidor,
            $monto,
            $estado_pago,
            $metodo_pago,
            $comprobante_url,
            $datos_json
        ];

        $types = 'iiiidssss';

        $id_compra = $db->execute($sqlInsert, $params, $types);

        if (!$id_compra) {
            throw new Exception('Error al registrar la compra');
        }

        // Si se incluyen datos de entrega, crear el registro de entrega
        if (isset($input['entrega']) && is_array($input['entrega'])) {
            $entrega = $input['entrega'];

            // Validar datos de entrega
            if (!isset($entrega['id_city']) || !isset($entrega['direccion_envio'])) {
                throw new Exception('Datos de entrega incompletos');
            }

            $sqlEntrega = "INSERT INTO entregas 
                          (id_compra, id_city, direccion_envio, codigo_postal, referencias, estado) 
                          VALUES (?, ?, ?, ?, ?, ?)";

            $entregaParams = [
                $id_compra,
                intval($entrega['id_city']),
                $db->sanitize($entrega['direccion_envio']),
                isset($entrega['codigo_postal']) ? $db->sanitize($entrega['codigo_postal']) : '',
                isset($entrega['referencias']) ? $db->sanitize($entrega['referencias']) : '',
                ENTREGA_PENDIENTE
            ];

            $entregaTypes = 'iissss';

            $id_entrega = $db->execute($sqlEntrega, $entregaParams, $entregaTypes);

            if (!$id_entrega) {
                throw new Exception('Error al registrar la entrega');
            }
        }

        // Confirmar transacción
        $db->commit();

        // Obtener datos completos de la compra
        $sqlCompraCompleta = "SELECT 
                                comp.*,
                                CONCAT(p.nombre, ' ', p.apellido) as nombre_piloto,
                                p.email as email_piloto,
                                c.nombre as nombre_carrera,
                                c.fecha_inicio,
                                cat.nombre as categoria
                             FROM compras comp
                             INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
                             INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
                             INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
                             WHERE comp.id_compra = ?";

        $compraCompleta = $db->query($sqlCompraCompleta, [$id_compra], 'i');

        // Log de éxito
        logMessage("Compra registrada exitosamente - ID: $id_compra, Piloto: {$piloto[0]['email']}, Carrera: {$carreraData['nombre']}");

        // Enviar respuesta de éxito
        sendSuccess([
            'id_compra' => $id_compra,
            'numero_competidor' => $numero_competidor,
            'compra' => $compraCompleta[0],
            'cupos_restantes' => $cupoMaximo - ($inscriptos + 1)
        ], 'Inscripción registrada exitosamente', 201);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $db->rollback();
        throw $e;
    }
} catch (Exception $e) {
    // Log del error
    logMessage("Error en Terminal_carga_productos: " . $e->getMessage(), 'ERROR');

    // Enviar respuesta de error
    sendError($e->getMessage(), 500);
}
