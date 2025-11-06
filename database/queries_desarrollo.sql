-- ============================================
-- CONSULTAS ÚTILES PARA DESARROLLO - PODIUM DB
-- ============================================
-- Archivo: queries_desarrollo.sql
-- Versión: 2.0
-- Descripción: Consultas SQL útiles para desarrollo y testing

USE podium_db;

-- ============================================
-- SECCIÓN 1: VERIFICACIÓN DE ESTRUCTURA
-- ============================================

-- 1.1 Ver todas las tablas
SHOW TABLES;

-- 1.2 Contar registros en cada tabla
SELECT 
    'pais' AS tabla, COUNT(*) AS registros FROM pais
UNION ALL SELECT 'usuarios', COUNT(*) FROM usuarios
UNION ALL SELECT 'brand', COUNT(*) FROM brand
UNION ALL SELECT 'type', COUNT(*) FROM type
UNION ALL SELECT 'vehicle', COUNT(*) FROM vehicle
UNION ALL SELECT 'categorias', COUNT(*) FROM categorias
UNION ALL SELECT 'city', COUNT(*) FROM city
UNION ALL SELECT 'storage_id', COUNT(*) FROM storage_id
UNION ALL SELECT 'country', COUNT(*) FROM country
UNION ALL SELECT 'pilotos', COUNT(*) FROM pilotos
UNION ALL SELECT 'carreras', COUNT(*) FROM carreras
UNION ALL SELECT 'compras', COUNT(*) FROM compras
UNION ALL SELECT 'bank', COUNT(*) FROM bank
UNION ALL SELECT 'entregas', COUNT(*) FROM entregas
UNION ALL SELECT 'resultados', COUNT(*) FROM resultados
UNION ALL SELECT 'lap', COUNT(*) FROM lap;

-- 1.3 Ver estructura de tablas importantes
DESCRIBE usuarios;
DESCRIBE pilotos;
DESCRIBE carreras;
DESCRIBE compras;

-- 1.4 Ver Foreign Keys de una tabla
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'podium_db'
AND TABLE_NAME = 'carreras'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================
-- SECCIÓN 2: CONSULTAS DE DATOS - FRONTEND
-- ============================================

-- 2.1 CARRERAS DISPONIBLES PARA INSCRIPCIÓN
-- Uso: Mostrar en la página principal del frontend
SELECT 
    id_carrera,
    nombre_carrera,
    categoria,
    circuito,
    ciudad,
    pais,
    fecha_inicio,
    precio_inscripcion,
    imagen_url,
    cupo_maximo,
    inscriptos,
    cupos_disponibles,
    numero_vueltas,
    distancia_total
FROM v_carreras_completas
WHERE estado = 'inscripciones_abiertas'
ORDER BY fecha_inicio;

-- 2.2 DETALLE DE UNA CARRERA ESPECÍFICA
-- Uso: Página de detalle de carrera
SELECT 
    c.*,
    cat.nombre AS categoria,
    cat.descripcion AS descripcion_categoria,
    co.nombre AS circuito,
    co.longitud_pista,
    co.numero_curvas,
    co.tipo_pista,
    co.capacidad_espectadores,
    ci.nombre AS ciudad,
    p.nombre AS pais
FROM carreras c
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
INNER JOIN country co ON c.id_country = co.id_country
INNER JOIN city ci ON co.id_city = ci.id_city
INNER JOIN pais p ON ci.id_pais = p.id_pais
WHERE c.id_carrera = 1;

-- 2.3 INSCRIPTOS A UNA CARRERA
-- Uso: Ver quiénes están inscritos (público)
SELECT 
    comp.numero_competidor,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    pa.nombre AS pais,
    v.modelo AS vehiculo,
    b.nombre AS marca
FROM compras comp
INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
LEFT JOIN brand b ON v.id_brand = b.id_brand
WHERE comp.id_carrera = 1
AND comp.estado_pago IN ('completado', 'pendiente')
ORDER BY comp.numero_competidor;

-- 2.4 CATEGORÍAS DISPONIBLES
-- Uso: Filtro de categorías en frontend
SELECT 
    id_categoria,
    nombre,
    descripcion,
    imagen_url,
    COUNT(DISTINCT c.id_carrera) AS total_carreras
FROM categorias cat
LEFT JOIN carreras c ON cat.id_categoria = c.id_categoria
WHERE cat.activo = TRUE
GROUP BY cat.id_categoria
ORDER BY nombre;

-- 2.5 CIRCUITOS/AUTÓDROMOS
-- Uso: Información de circuitos
SELECT 
    co.id_country,
    co.nombre AS circuito,
    co.longitud_pista,
    co.numero_curvas,
    co.tipo_pista,
    co.capacidad_espectadores,
    co.imagen_url,
    ci.nombre AS ciudad,
    p.nombre AS pais,
    COUNT(DISTINCT c.id_carrera) AS carreras_realizadas
FROM country co
INNER JOIN city ci ON co.id_city = ci.id_city
INNER JOIN pais p ON ci.id_pais = p.id_pais
LEFT JOIN carreras c ON co.id_country = c.id_country
GROUP BY co.id_country
ORDER BY co.nombre;

-- ============================================
-- SECCIÓN 3: CONSULTAS DE USUARIO - PILOTO
-- ============================================

-- 3.1 PERFIL DE PILOTO
-- Uso: Página de perfil del piloto
SELECT 
    p.*,
    pa.nombre AS pais_nombre,
    pa.codigo_iso,
    COUNT(DISTINCT comp.id_carrera) AS carreras_inscritas,
    COUNT(DISTINCT r.id_carrera) AS carreras_completadas,
    SUM(r.puntos) AS puntos_totales
FROM pilotos p
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
LEFT JOIN compras comp ON p.id_piloto = comp.id_piloto
LEFT JOIN resultados r ON p.id_piloto = r.id_piloto
WHERE p.email = 'carlos.rodriguez@email.com'
GROUP BY p.id_piloto;

-- 3.2 HISTORIAL DE INSCRIPCIONES DE UN PILOTO
-- Uso: Mis inscripciones / Mi historial
SELECT 
    comp.id_compra,
    comp.numero_competidor,
    comp.fecha_compra,
    comp.monto,
    comp.estado_pago,
    c.nombre AS carrera,
    c.fecha_inicio,
    c.estado AS estado_carrera,
    cat.nombre AS categoria,
    cir.nombre AS circuito,
    v.modelo AS vehiculo
FROM compras comp
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
INNER JOIN country cir ON c.id_country = cir.id_country
LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
WHERE comp.id_piloto = (SELECT id_piloto FROM pilotos WHERE email = 'carlos.rodriguez@email.com')
ORDER BY comp.fecha_compra DESC;

-- 3.3 ENTREGAS DE UN PILOTO
-- Uso: Seguimiento de envíos
SELECT 
    e.id_entrega,
    e.estado AS estado_entrega,
    e.tracking_number,
    e.empresa_transporte,
    e.fecha_despacho,
    e.fecha_entrega,
    e.direccion_envio,
    ci.nombre AS ciudad,
    c.nombre AS carrera
FROM entregas e
INNER JOIN compras comp ON e.id_compra = comp.id_compra
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
INNER JOIN city ci ON e.id_city = ci.id_city
WHERE comp.id_piloto = (SELECT id_piloto FROM pilotos WHERE email = 'carlos.rodriguez@email.com')
ORDER BY e.fecha_creacion DESC;

-- 3.4 RESULTADOS DE UN PILOTO
-- Uso: Historial de resultados
SELECT 
    c.nombre AS carrera,
    c.fecha_inicio,
    cat.nombre AS categoria,
    r.posicion_final,
    r.tiempo_total,
    r.puntos,
    r.tiempo_mejor_vuelta,
    r.velocidad_maxima,
    r.estado AS estado_resultado
FROM resultados r
INNER JOIN carreras c ON r.id_carrera = c.id_carrera
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
WHERE r.id_piloto = (SELECT id_piloto FROM pilotos WHERE email = 'carlos.rodriguez@email.com')
ORDER BY c.fecha_inicio DESC;

-- ============================================
-- SECCIÓN 4: RESULTADOS Y RANKINGS
-- ============================================

-- 4.1 TABLA DE POSICIONES DE UNA CARRERA
-- Uso: Ver resultados finales de una carrera
SELECT 
    r.posicion_final AS pos,
    r.numero_competidor AS num,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    pa.codigo_iso AS pais,
    v.modelo AS vehiculo,
    b.nombre AS marca,
    r.tiempo_total,
    r.tiempo_mejor_vuelta,
    r.puntos,
    r.estado AS estado
FROM resultados r
INNER JOIN pilotos p ON r.id_piloto = p.id_piloto
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
LEFT JOIN vehicle v ON r.id_vehicle = v.id_vehicle
LEFT JOIN brand b ON v.id_brand = b.id_brand
WHERE r.id_carrera = 1
ORDER BY r.posicion_final;

-- 4.2 RANKING GENERAL DE PILOTOS (TOP 10)
-- Uso: Clasificación del campeonato
SELECT * FROM v_ranking_pilotos
ORDER BY puntos_totales DESC, victorias DESC
LIMIT 10;

-- 4.3 MEJOR VUELTA DE UNA CARRERA
-- Uso: Estadística destacada
SELECT 
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    MIN(l.tiempo_vuelta) AS mejor_vuelta,
    l.numero_vuelta,
    l.velocidad_promedio
FROM lap l
INNER JOIN resultados r ON l.id_resultado = r.id_resultado
INNER JOIN pilotos p ON r.id_piloto = p.id_piloto
WHERE r.id_carrera = 1
GROUP BY l.id_lap
ORDER BY l.tiempo_vuelta ASC
LIMIT 1;

-- 4.4 VUELTAS DE UN PILOTO EN UNA CARRERA
-- Uso: Análisis detallado de performance
SELECT 
    l.numero_vuelta,
    l.tiempo_vuelta,
    l.sector_1,
    l.sector_2,
    l.sector_3,
    l.velocidad_promedio,
    l.posicion_en_vuelta
FROM lap l
INNER JOIN resultados r ON l.id_resultado = r.id_resultado
WHERE r.id_carrera = 1
AND r.id_piloto = 1
ORDER BY l.numero_vuelta;

-- 4.5 ESTADÍSTICAS DE UNA CARRERA
-- Uso: Resumen general de la carrera
SELECT 
    c.nombre AS carrera,
    COUNT(DISTINCT r.id_piloto) AS pilotos_participantes,
    COUNT(CASE WHEN r.estado = 'finalizado' THEN 1 END) AS finalizaron,
    COUNT(CASE WHEN r.estado = 'abandonado' THEN 1 END) AS abandonaron,
    MIN(r.tiempo_total) AS tiempo_ganador,
    MAX(r.velocidad_maxima) AS velocidad_maxima_carrera,
    AVG(r.velocidad_promedio) AS velocidad_promedio_carrera
FROM carreras c
LEFT JOIN resultados r ON c.id_carrera = r.id_carrera
WHERE c.id_carrera = 1
GROUP BY c.id_carrera;

-- ============================================
-- SECCIÓN 5: BACKEND - GESTIÓN
-- ============================================

-- 5.1 ENTREGAS PENDIENTES (Para gestores)
-- Uso: Dashboard de gestión de entregas
SELECT 
    e.id_entrega,
    e.estado AS estado_entrega,
    e.tracking_number,
    comp.numero_competidor,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.telefono,
    p.email,
    c.nombre AS carrera,
    c.fecha_inicio,
    e.direccion_envio,
    ci.nombre AS ciudad,
    e.empresa_transporte,
    DATEDIFF(NOW(), e.fecha_creacion) AS dias_desde_compra
FROM entregas e
INNER JOIN compras comp ON e.id_compra = comp.id_compra
INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
INNER JOIN city ci ON e.id_city = ci.id_city
WHERE e.estado IN ('pendiente', 'preparando')
ORDER BY e.fecha_creacion ASC;

-- 5.2 ENTREGAS POR GESTOR
-- Uso: Ver rendimiento de cada gestor
SELECT 
    CONCAT(u.nombre, ' ', u.apellido) AS gestor,
    u.email,
    COUNT(e.id_entrega) AS total_entregas,
    SUM(CASE WHEN e.estado = 'entregado' THEN 1 ELSE 0 END) AS entregadas,
    SUM(CASE WHEN e.estado = 'en_camino' THEN 1 ELSE 0 END) AS en_camino,
    SUM(CASE WHEN e.estado IN ('pendiente', 'preparando') THEN 1 ELSE 0 END) AS pendientes,
    ROUND(SUM(CASE WHEN e.estado = 'entregado' THEN 1 ELSE 0 END) * 100.0 / COUNT(e.id_entrega), 2) AS porcentaje_exito
FROM usuarios u
LEFT JOIN entregas e ON u.id_usuario = e.id_usuario_gestor
WHERE u.rol = 'gestor_entregas'
GROUP BY u.id_usuario
ORDER BY total_entregas DESC;

-- 5.3 COMPRAS PENDIENTES DE PAGO
-- Uso: Seguimiento de pagos
SELECT 
    comp.id_compra,
    comp.fecha_compra,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.email,
    p.telefono,
    c.nombre AS carrera,
    comp.monto,
    comp.metodo_pago,
    DATEDIFF(NOW(), comp.fecha_compra) AS dias_pendiente
FROM compras comp
INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
WHERE comp.estado_pago = 'pendiente'
ORDER BY comp.fecha_compra ASC;

-- 5.4 INGRESOS POR CARRERA
-- Uso: Reportes financieros
SELECT 
    c.nombre AS carrera,
    c.fecha_inicio,
    cat.nombre AS categoria,
    COUNT(comp.id_compra) AS total_inscripciones,
    SUM(CASE WHEN comp.estado_pago = 'completado' THEN comp.monto ELSE 0 END) AS ingresos_confirmados,
    SUM(CASE WHEN comp.estado_pago = 'pendiente' THEN comp.monto ELSE 0 END) AS ingresos_pendientes,
    c.cupo_maximo - COUNT(comp.id_compra) AS cupos_disponibles
FROM carreras c
LEFT JOIN compras comp ON c.id_carrera = comp.id_carrera
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
WHERE c.estado IN ('programada', 'inscripciones_abiertas')
GROUP BY c.id_carrera
ORDER BY c.fecha_inicio;

-- 5.5 USUARIOS ACTIVOS DEL SISTEMA
-- Uso: Gestión de usuarios backend
SELECT 
    id_usuario,
    CONCAT(nombre, ' ', apellido) AS nombre_completo,
    email,
    rol,
    telefono,
    DATE(ultimo_acceso) AS ultimo_acceso,
    DATEDIFF(NOW(), ultimo_acceso) AS dias_sin_acceso
FROM usuarios
WHERE activo = TRUE
ORDER BY ultimo_acceso DESC;

-- ============================================
-- SECCIÓN 6: INSERTS DE PRUEBA
-- ============================================

-- 6.1 Insertar nuevo piloto (después de Firebase Auth)
INSERT INTO pilotos (firebase_uid, nombre, apellido, email, telefono, id_pais, fecha_nacimiento, numero_licencia, tipo_licencia)
VALUES 
    ('firebase_uid_123456', 'Nuevo', 'Piloto', 'nuevo.piloto@test.com', '+54911999999', 1, '1997-05-15', 'LIC-ARG-999', 'amateur');

-- 6.2 Crear nueva inscripción
INSERT INTO compras (id_piloto, id_carrera, id_vehicle, numero_competidor, monto, estado_pago, metodo_pago)
VALUES (
    (SELECT id_piloto FROM pilotos WHERE email = 'nuevo.piloto@test.com'),
    1, -- ID de carrera
    NULL, -- Sin vehículo asignado aún
    99, -- Número de competidor
    1500.00,
    'pendiente',
    'Tarjeta de crédito'
);

-- 6.3 Crear entrega para una compra
INSERT INTO entregas (id_compra, id_city, direccion_envio, codigo_postal, estado, empresa_transporte)
VALUES (
    LAST_INSERT_ID(), -- ID de la compra recién creada
    1, -- Buenos Aires
    'Calle Falsa 123',
    'C1000',
    'pendiente',
    'OCA'
);

-- 6.4 Asignar gestor a una entrega
UPDATE entregas 
SET id_usuario_gestor = (SELECT id_usuario FROM usuarios WHERE email = 'gestor@podium.com'),
    estado = 'preparando'
WHERE id_entrega = 1;

-- 6.5 Actualizar estado de entrega
UPDATE entregas 
SET estado = 'en_camino',
    tracking_number = 'TRACK789456123',
    fecha_despacho = NOW()
WHERE id_entrega = 1;

-- 6.6 Marcar entrega como completada
UPDATE entregas 
SET estado = 'entregado',
    fecha_entrega = NOW()
WHERE id_entrega = 1;

-- 6.7 Completar pago de una inscripción
UPDATE compras 
SET estado_pago = 'completado',
    comprobante_url = 'https://example.com/comprobante123.pdf'
WHERE id_compra = 1;

-- 6.8 Registrar resultado de un piloto
INSERT INTO resultados (id_carrera, id_piloto, id_vehicle, posicion_final, posicion_salida, tiempo_total, tiempo_mejor_vuelta, vuelta_rapida, puntos, vueltas_completadas, velocidad_promedio, velocidad_maxima, estado)
VALUES (
    1, -- Carrera
    1, -- Piloto
    1, -- Vehículo
    1, -- Posición final
    3, -- Posición de salida
    '01:32:45.234', -- Tiempo total
    '00:01:44.123', -- Mejor vuelta
    23, -- Vuelta más rápida
    25, -- Puntos
    53, -- Vueltas completadas
    245.5, -- Velocidad promedio
    312.8, -- Velocidad máxima
    'finalizado'
);

-- 6.9 Registrar vuelta individual
INSERT INTO lap (id_resultado, numero_vuelta, tiempo_vuelta, velocidad_promedio, sector_1, sector_2, sector_3, posicion_en_vuelta)
VALUES (
    LAST_INSERT_ID(), -- Resultado recién creado
    1, -- Número de vuelta
    '00:01:55.234', -- Tiempo
    234.5, -- Velocidad
    '00:00:32.123', -- Sector 1
    '00:00:41.567', -- Sector 2
    '00:00:41.544', -- Sector 3
    3 -- Posición
);

-- ============================================
-- SECCIÓN 7: MANTENIMIENTO Y LIMPIEZA
-- ============================================

-- 7.1 Limpiar inscripciones pendientes antiguas (más de 7 días)
DELETE FROM compras 
WHERE estado_pago = 'pendiente' 
AND DATEDIFF(NOW(), fecha_compra) > 7;

-- 7.2 Archivar carreras finalizadas antiguas
UPDATE carreras 
SET estado = 'finalizada' 
WHERE fecha_fin < DATE_SUB(NOW(), INTERVAL 7 DAY)
AND estado NOT IN ('finalizada', 'cancelada');

-- 7.3 Ver tamaño de las tablas
SELECT 
    table_name AS Tabla,
    table_rows AS Registros,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Tamaño (MB)',
    ROUND((index_length / 1024 / 1024), 2) AS 'Índices (MB)'
FROM information_schema.TABLES 
WHERE table_schema = 'podium_db'
ORDER BY (data_length + index_length) DESC;

-- 7.4 Optimizar todas las tablas
OPTIMIZE TABLE pais, usuarios, brand, type, vehicle, categorias, city, 
storage_id, country, pilotos, carreras, compras, bank, entregas, resultados, lap;

-- 7.5 Verificar integridad referencial
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'podium_db'
AND CONSTRAINT_TYPE = 'FOREIGN KEY';

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
