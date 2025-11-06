<?php

/**
 * PODIUM - API de Categorías
 * 
 * Obtiene la lista de categorías disponibles
 * 
 * Endpoint: GET /backend/api/categorias/listar.php
 */

require_once __DIR__ . '/../../config/config.php';

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Método no permitido', 405);
}

try {
    $db = getDB();

    // Obtener todas las categorías con conteo de carreras
    $sql = "SELECT 
                cat.id_categoria,
                cat.nombre,
                cat.descripcion,
                cat.imagen_url,
                COUNT(DISTINCT c.id_carrera) AS total_carreras,
                SUM(CASE WHEN c.estado = 'inscripciones_abiertas' THEN 1 ELSE 0 END) AS carreras_abiertas
            FROM categorias cat
            LEFT JOIN carreras c ON cat.id_categoria = c.id_categoria
            GROUP BY cat.id_categoria
            ORDER BY cat.nombre ASC";

    $categorias = $db->query($sql);

    if ($categorias === false) {
        throw new Exception('Error al obtener categorías');
    }

    // Convertir tipos de datos
    foreach ($categorias as &$categoria) {
        $categoria['id_categoria'] = intval($categoria['id_categoria']);
        $categoria['total_carreras'] = intval($categoria['total_carreras']);
        $categoria['carreras_abiertas'] = intval($categoria['carreras_abiertas']);
    }

    sendSuccess([
        'categorias' => $categorias,
        'total' => count($categorias)
    ]);
} catch (Exception $e) {
    logMessage("Error en listar categorías: " . $e->getMessage(), 'ERROR');
    sendError('Error al obtener categorías', 500);
}
