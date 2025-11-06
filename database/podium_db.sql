-- ============================================
-- BASE DE DATOS PODIUM - Sistema de Gestión de Carreras
-- ============================================
-- Versión: 2.0 - Mejorada basada en DER
-- Fecha: Noviembre 2025
-- ============================================

CREATE DATABASE IF NOT EXISTS podium_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE podium_db;

-- ============================================
-- TABLA: pais
-- Países disponibles para pilotos y ubicaciones
-- ============================================
CREATE TABLE IF NOT EXISTS pais (
    id_pais INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    codigo_iso VARCHAR(3) NOT NULL UNIQUE, -- ARG, USA, BRA, etc.
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_codigo (codigo_iso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuarios (User)
-- Usuarios del personal de gestión (backend)
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Hash de la contraseña
    rol ENUM('admin', 'gestor_entregas', 'supervisor', 'juez') DEFAULT 'gestor_entregas',
    telefono VARCHAR(20),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: brand (Marca de vehículos)
-- Marcas de autos participantes
-- ============================================
CREATE TABLE IF NOT EXISTS brand (
    id_brand INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    logo_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: type (Tipo de vehículo)
-- Tipos de vehículos por categoría
-- ============================================
CREATE TABLE IF NOT EXISTS type (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: vehicle (Vehículo)
-- Vehículos registrados en el sistema
-- ============================================
CREATE TABLE IF NOT EXISTS vehicle (
    id_vehicle INT AUTO_INCREMENT PRIMARY KEY,
    id_brand INT NOT NULL,
    id_type INT NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    año INT,
    numero_chasis VARCHAR(50) UNIQUE,
    color VARCHAR(50),
    especificaciones TEXT, -- JSON con specs técnicas
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_brand) REFERENCES brand(id_brand) ON DELETE RESTRICT,
    FOREIGN KEY (id_type) REFERENCES type(id_type) ON DELETE RESTRICT,
    INDEX idx_brand (id_brand),
    INDEX idx_type (id_type),
    INDEX idx_modelo (modelo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: categorias (Category)
-- Categorías de carreras (F1, Rally, NASCAR, etc.)
-- ============================================
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen_url VARCHAR(255),
    reglamento TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: city (Ciudad)
-- Ciudades donde se realizan las carreras
-- ============================================
CREATE TABLE IF NOT EXISTS city (
    id_city INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_pais INT NOT NULL,
    codigo_postal VARCHAR(20),
    latitud DECIMAL(10, 7),
    longitud DECIMAL(10, 7),
    FOREIGN KEY (id_pais) REFERENCES pais(id_pais) ON DELETE RESTRICT,
    INDEX idx_nombre (nombre),
    INDEX idx_pais (id_pais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: storage_id (Almacenamiento)
-- Lugares de almacenamiento de vehículos/equipamiento
-- ============================================
CREATE TABLE IF NOT EXISTS storage_id (
    id_storage INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_city INT NOT NULL,
    direccion VARCHAR(255),
    capacidad INT,
    tipo ENUM('garage', 'deposito', 'taller', 'paddock') DEFAULT 'garage',
    FOREIGN KEY (id_city) REFERENCES city(id_city) ON DELETE RESTRICT,
    INDEX idx_city (id_city),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: country (Circuito/Autódromo)
-- Circuitos donde se corren las carreras
-- ============================================
CREATE TABLE IF NOT EXISTS country (
    id_country INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    id_city INT NOT NULL,
    direccion VARCHAR(255),
    longitud_pista DECIMAL(6, 3), -- km
    numero_curvas INT,
    tipo_pista ENUM('circuito', 'calle', 'oval', 'mixto') DEFAULT 'circuito',
    capacidad_espectadores INT,
    imagen_url VARCHAR(255),
    FOREIGN KEY (id_city) REFERENCES city(id_city) ON DELETE RESTRICT,
    INDEX idx_nombre (nombre),
    INDEX idx_city (id_city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: pilotos (Pilots)
-- Pilotos participantes (clientes del frontend)
-- ============================================
CREATE TABLE IF NOT EXISTS pilotos (
    id_piloto INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(128) UNIQUE, -- UID de Firebase Auth
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    id_pais INT,
    fecha_nacimiento DATE,
    numero_licencia VARCHAR(50) UNIQUE,
    tipo_licencia ENUM('amateur', 'profesional', 'internacional') DEFAULT 'amateur',
    foto_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pais) REFERENCES pais(id_pais) ON DELETE SET NULL,
    INDEX idx_firebase_uid (firebase_uid),
    INDEX idx_email (email),
    INDEX idx_pais (id_pais),
    INDEX idx_licencia (numero_licencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: carreras (Races)
-- Eventos de carreras
-- ============================================
CREATE TABLE IF NOT EXISTS carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_categoria INT NOT NULL,
    id_country INT NOT NULL, -- Circuito donde se corre
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    precio_inscripcion DECIMAL(10, 2) NOT NULL,
    imagen_url VARCHAR(255),
    estado ENUM('programada', 'inscripciones_abiertas', 'en_curso', 'finalizada', 'cancelada') DEFAULT 'programada',
    cupo_maximo INT DEFAULT 50,
    numero_vueltas INT,
    distancia_total DECIMAL(8, 2), -- km totales
    premio_primero DECIMAL(10, 2),
    premio_segundo DECIMAL(10, 2),
    premio_tercero DECIMAL(10, 2),
    clima VARCHAR(50),
    transmision_url VARCHAR(255), -- URL de streaming
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT,
    FOREIGN KEY (id_country) REFERENCES country(id_country) ON DELETE RESTRICT,
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_estado (estado),
    INDEX idx_categoria (id_categoria),
    INDEX idx_country (id_country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: compras (Inscripciones)
-- Registro de inscripciones a carreras
-- ============================================
CREATE TABLE IF NOT EXISTS compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    id_piloto INT NOT NULL,
    id_carrera INT NOT NULL,
    id_vehicle INT, -- Vehículo con el que participará
    numero_competidor INT, -- Número asignado en la carrera
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10, 2) NOT NULL,
    estado_pago ENUM('pendiente', 'completado', 'reembolsado', 'cancelado') DEFAULT 'pendiente',
    metodo_pago VARCHAR(50),
    comprobante_url VARCHAR(255),
    datos_json TEXT, -- Datos adicionales en JSON
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id_piloto) ON DELETE CASCADE,
    FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE CASCADE,
    FOREIGN KEY (id_vehicle) REFERENCES vehicle(id_vehicle) ON DELETE SET NULL,
    UNIQUE KEY unique_piloto_carrera (id_piloto, id_carrera),
    INDEX idx_piloto (id_piloto),
    INDEX idx_carrera (id_carrera),
    INDEX idx_vehicle (id_vehicle),
    INDEX idx_fecha (fecha_compra),
    INDEX idx_estado (estado_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: bank (Información bancaria)
-- Datos bancarios para pagos/reembolsos
-- ============================================
CREATE TABLE IF NOT EXISTS bank (
    id_bank INT AUTO_INCREMENT PRIMARY KEY,
    id_piloto INT NOT NULL,
    nombre_banco VARCHAR(100) NOT NULL,
    tipo_cuenta ENUM('ahorro', 'corriente', 'paypal', 'mercadopago') DEFAULT 'ahorro',
    numero_cuenta VARCHAR(100),
    cbu_cvu VARCHAR(50),
    alias VARCHAR(50),
    titular VARCHAR(150),
    es_principal BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id_piloto) ON DELETE CASCADE,
    INDEX idx_piloto (id_piloto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: resultados (Race Results)
-- Resultados de las carreras
-- ============================================
CREATE TABLE IF NOT EXISTS resultados (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_carrera INT NOT NULL,
    id_piloto INT NOT NULL,
    id_vehicle INT,
    posicion_final INT NOT NULL,
    posicion_salida INT, -- Grilla de largada
    tiempo_total TIME,
    tiempo_mejor_vuelta TIME,
    vuelta_rapida INT, -- Número de vuelta más rápida
    puntos INT DEFAULT 0,
    vueltas_completadas INT,
    diferencia_primero TIME, -- Diferencia con el 1º
    velocidad_promedio DECIMAL(6, 2), -- km/h
    velocidad_maxima DECIMAL(6, 2), -- km/h
    estado ENUM('finalizado', 'abandonado', 'descalificado', 'dns') DEFAULT 'finalizado',
    motivo_abandono TEXT,
    penalizaciones TEXT,
    notas TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE CASCADE,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id_piloto) ON DELETE CASCADE,
    FOREIGN KEY (id_vehicle) REFERENCES vehicle(id_vehicle) ON DELETE SET NULL,
    UNIQUE KEY unique_carrera_piloto (id_carrera, id_piloto),
    INDEX idx_carrera (id_carrera),
    INDEX idx_piloto (id_piloto),
    INDEX idx_posicion (posicion_final)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: lap (Vueltas)
-- Registro de vueltas individuales
-- ============================================
CREATE TABLE IF NOT EXISTS lap (
    id_lap INT AUTO_INCREMENT PRIMARY KEY,
    id_resultado INT NOT NULL,
    numero_vuelta INT NOT NULL,
    tiempo_vuelta TIME NOT NULL,
    velocidad_promedio DECIMAL(6, 2),
    sector_1 TIME,
    sector_2 TIME,
    sector_3 TIME,
    posicion_en_vuelta INT,
    incidente TEXT, -- Descripción de incidentes
    FOREIGN KEY (id_resultado) REFERENCES resultados(id_resultado) ON DELETE CASCADE,
    INDEX idx_resultado (id_resultado),
    INDEX idx_vuelta (numero_vuelta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: entregas (Deliveries)
-- Gestión de entregas de kits/materiales
-- ============================================
CREATE TABLE IF NOT EXISTS entregas (
    id_entrega INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT NOT NULL,
    id_city INT NOT NULL,
    direccion_envio VARCHAR(255) NOT NULL,
    codigo_postal VARCHAR(20),
    referencias TEXT,
    estado ENUM('pendiente', 'preparando', 'en_camino', 'entregado', 'devuelto', 'cancelado') DEFAULT 'pendiente',
    fecha_despacho DATETIME NULL,
    fecha_entrega DATETIME NULL,
    id_usuario_gestor INT NULL,
    empresa_transporte VARCHAR(100),
    tracking_number VARCHAR(100),
    costo_envio DECIMAL(8, 2),
    notas TEXT,
    firma_receptor VARCHAR(255), -- URL de firma digital
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra) ON DELETE CASCADE,
    FOREIGN KEY (id_city) REFERENCES city(id_city) ON DELETE RESTRICT,
    FOREIGN KEY (id_usuario_gestor) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_compra (id_compra),
    INDEX idx_city (id_city),
    INDEX idx_estado (estado),
    INDEX idx_gestor (id_usuario_gestor),
    INDEX idx_tracking (tracking_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO
-- ============================================

-- Insertar países
INSERT INTO pais (nombre, codigo_iso) VALUES
('Argentina', 'ARG'),
('Brasil', 'BRA'),
('Estados Unidos', 'USA'),
('España', 'ESP'),
('Italia', 'ITA'),
('México', 'MEX'),
('Chile', 'CHL'),
('Uruguay', 'URY');

-- Insertar usuarios gestores
INSERT INTO usuarios (nombre, apellido, email, password, rol, telefono) VALUES
('Admin', 'Sistema', 'admin@podium.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+54911000001'),
('Juan', 'Pérez', 'gestor@podium.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor_entregas', '+54911000002'),
('María', 'López', 'juez@podium.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juez', '+54911000003');

-- Insertar marcas de vehículos
INSERT INTO brand (nombre, descripcion, logo_url) VALUES
('Ferrari', 'Marca italiana de prestigio en automovilismo', 'https://example.com/ferrari.png'),
('Mercedes-Benz', 'Fabricante alemán de vehículos de competición', 'https://example.com/mercedes.png'),
('Red Bull Racing', 'Equipo de competición de alto rendimiento', 'https://example.com/redbull.png'),
('Ford', 'Fabricante estadounidense', 'https://example.com/ford.png'),
('Chevrolet', 'Marca americana de deportivos', 'https://example.com/chevrolet.png'),
('Toyota', 'Fabricante japonés', 'https://example.com/toyota.png');

-- Insertar tipos de vehículos
INSERT INTO type (nombre, descripcion) VALUES
('Fórmula', 'Monoplazas de competición'),
('GT', 'Gran Turismo deportivos'),
('Rally', 'Vehículos preparados para rally'),
('Stock Car', 'Automóviles de serie modificados'),
('Karting', 'Karts de competición'),
('Prototype', 'Prototipos de resistencia');

-- Insertar vehículos de ejemplo
INSERT INTO vehicle (id_brand, id_type, modelo, año, numero_chasis, color, especificaciones) VALUES
(1, 1, 'SF90', 2023, 'FER-SF90-001', 'Rojo', '{"motor": "V6 Turbo", "potencia": "1050hp", "peso": "752kg"}'),
(2, 1, 'W14', 2023, 'MER-W14-001', 'Plata', '{"motor": "V6 Hybrid", "potencia": "1000hp", "peso": "798kg"}'),
(3, 1, 'RB19', 2023, 'RBR-RB19-001', 'Azul', '{"motor": "Honda V6", "potencia": "1020hp", "peso": "752kg"}'),
(4, 3, 'Fiesta Rally', 2022, 'FOR-FR-001', 'Blanco', '{"motor": "Turbo 4", "potencia": "380hp", "traccion": "4WD"}'),
(5, 4, 'Camaro NASCAR', 2023, 'CHV-CM-001', 'Amarillo', '{"motor": "V8", "potencia": "750hp", "cilindrada": "5.8L"}');

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, imagen_url, reglamento) VALUES
('Fórmula 1', 'Categoría reina del automovilismo mundial', 'https://example.com/f1.jpg', 'Reglamento FIA 2025'),
('Rally Championship', 'Carreras en terrenos mixtos', 'https://example.com/rally.jpg', 'Reglamento WRC'),
('NASCAR Cup Series', 'Carreras en óvalos', 'https://example.com/nascar.jpg', 'Reglamento NASCAR 2025'),
('Karting Pro', 'Categoría de iniciación profesional', 'https://example.com/karting.jpg', 'Reglamento CIK-FIA'),
('GT Championship', 'Gran Turismo de resistencia', 'https://example.com/gt.jpg', 'Reglamento FIA GT');

-- Insertar ciudades
INSERT INTO city (nombre, id_pais, codigo_postal, latitud, longitud) VALUES
('Buenos Aires', 1, 'C1000', -34.603722, -58.381592),
('Córdoba', 1, 'X5000', -31.416668, -64.183334),
('Mendoza', 1, 'M5500', -32.889458, -68.845839),
('São Paulo', 2, '01000-000', -23.550520, -46.633308),
('Miami', 3, '33101', 25.761681, -80.191788),
('Barcelona', 4, '08001', 41.385064, 2.173404),
('Monza', 5, '20900', 45.620498, 9.289215),
('Ciudad de México', 6, '06000', 19.432608, -99.133209);

-- Insertar almacenamientos
INSERT INTO storage_id (nombre, id_city, direccion, capacidad, tipo) VALUES
('Paddock Central BA', 1, 'Av. Libertador 12000', 50, 'paddock'),
('Garage Tech Córdoba', 2, 'Av. Colón 5000', 30, 'taller'),
('Depósito Mendoza', 3, 'Ruta 40 Km 25', 100, 'deposito');

-- Insertar circuitos
INSERT INTO country (nombre, id_city, direccion, longitud_pista, numero_curvas, tipo_pista, capacidad_espectadores, imagen_url) VALUES
('Autódromo Oscar Alfredo Gálvez', 1, 'Av. Gral. Paz y Av. Constituyentes', 4.259, 15, 'circuito', 75000, 'https://example.com/galvez.jpg'),
('Autódromo de Córdoba', 2, 'Camino a 60 Cuadras', 6.340, 18, 'circuito', 45000, 'https://example.com/cordoba.jpg'),
('Circuito Urbano Puerto Madero', 1, 'Puerto Madero', 3.200, 12, 'calle', 50000, 'https://example.com/pm.jpg'),
('Autódromo de Interlagos', 4, 'Av. Sen. Teotônio Vilela', 4.309, 15, 'circuito', 60000, 'https://example.com/interlagos.jpg'),
('Autodromo Hermanos Rodríguez', 8, 'Av. del Conscripto', 4.304, 17, 'circuito', 135000, 'https://example.com/mexico.jpg');

-- Insertar pilotos de ejemplo
INSERT INTO pilotos (nombre, apellido, email, telefono, id_pais, fecha_nacimiento, numero_licencia, tipo_licencia, foto_url) VALUES
('Carlos', 'Rodríguez', 'carlos.rodriguez@email.com', '+54911223344', 1, '1995-03-15', 'LIC-ARG-001', 'profesional', 'https://example.com/carlos.jpg'),
('María', 'González', 'maria.gonzalez@email.com', '+54911556677', 1, '1998-07-22', 'LIC-ARG-002', 'profesional', 'https://example.com/maria.jpg'),
('Diego', 'Martínez', 'diego.martinez@email.com', '+54911889900', 1, '1992-11-08', 'LIC-ARG-003', 'internacional', 'https://example.com/diego.jpg'),
('Lucas', 'Silva', 'lucas.silva@email.com', '+5511987654321', 2, '1996-05-20', 'LIC-BRA-001', 'profesional', 'https://example.com/lucas.jpg'),
('Ana', 'Fernández', 'ana.fernandez@email.com', '+54911445566', 1, '2000-12-10', 'LIC-ARG-004', 'amateur', 'https://example.com/ana.jpg');

-- Insertar carreras
INSERT INTO carreras (nombre, descripcion, id_categoria, id_country, fecha_inicio, fecha_fin, precio_inscripcion, imagen_url, estado, cupo_maximo, numero_vueltas, distancia_total, premio_primero, premio_segundo, premio_tercero) VALUES
('Gran Premio de Argentina 2025', 'Fecha inaugural del campeonato nacional', 1, 1, '2025-12-15 14:00:00', '2025-12-15 16:30:00', 1500.00, 'https://example.com/gp-arg.jpg', 'inscripciones_abiertas', 24, 53, 225.73, 50000, 30000, 20000),
('Rally de las Sierras', 'Desafío en caminos de montaña', 2, 2, '2025-11-20 08:00:00', '2025-11-20 18:00:00', 800.00, 'https://example.com/rally-sierras.jpg', 'programada', 40, 8, 320.50, 25000, 15000, 10000),
('Circuito Urbano Night Race', 'Carrera nocturna en Puerto Madero', 1, 3, '2025-12-01 20:00:00', '2025-12-01 22:00:00', 2000.00, 'https://example.com/night.jpg', 'inscripciones_abiertas', 20, 45, 144.00, 75000, 45000, 30000),
('Karting Championship Round 1', 'Primera fecha del campeonato de karting', 4, 2, '2025-11-25 10:00:00', '2025-11-25 15:00:00', 350.00, 'https://example.com/karting.jpg', 'inscripciones_abiertas', 30, 25, 158.50, 5000, 3000, 2000);

-- Insertar compras de ejemplo
INSERT INTO compras (id_piloto, id_carrera, id_vehicle, numero_competidor, monto, estado_pago, metodo_pago) VALUES
(1, 1, 1, 7, 1500.00, 'completado', 'Tarjeta de crédito'),
(2, 1, 2, 14, 1500.00, 'completado', 'Transferencia bancaria'),
(3, 3, 3, 1, 2000.00, 'completado', 'MercadoPago'),
(4, 2, 4, 22, 800.00, 'pendiente', 'Tarjeta de débito'),
(5, 4, NULL, 15, 350.00, 'completado', 'Efectivo');

-- Insertar información bancaria
INSERT INTO bank (id_piloto, nombre_banco, tipo_cuenta, numero_cuenta, cbu_cvu, alias, titular) VALUES
(1, 'Banco Nación', 'ahorro', '1234567890', '0110123456789012345678', 'CARLOS.CARRERAS', 'Carlos Rodríguez'),
(2, 'Banco Galicia', 'corriente', '9876543210', '0070987654321098765432', 'MARIA.RACING', 'María González'),
(3, 'MercadoPago', 'mercadopago', NULL, '0000003000000000000001', 'DIEGO.PODIUM', 'Diego Martínez');

-- Insertar entregas
INSERT INTO entregas (id_compra, id_city, direccion_envio, codigo_postal, estado, id_usuario_gestor, empresa_transporte, tracking_number, costo_envio) VALUES
(1, 1, 'Av. Corrientes 1234', 'C1043', 'entregado', 2, 'OCA', 'OCA123456789', 500.00),
(2, 1, 'Calle Florida 567', 'C1005', 'en_camino', 2, 'Andreani', 'AND987654321', 450.00),
(3, 1, 'Av. Santa Fe 2800', 'C1425', 'preparando', 2, 'Correo Argentino', 'CA456789123', 550.00);

-- Insertar resultados de ejemplo (carrera finalizada ficticia)
INSERT INTO resultados (id_carrera, id_piloto, id_vehicle, posicion_final, posicion_salida, tiempo_total, tiempo_mejor_vuelta, vuelta_rapida, puntos, vueltas_completadas, velocidad_promedio, velocidad_maxima, estado) VALUES
(1, 1, 1, 1, 3, '01:32:45.234', '00:01:44.123', 23, 25, 53, 245.5, 312.8, 'finalizado'),
(1, 2, 2, 2, 1, '01:33:12.567', '00:01:45.890', 15, 18, 53, 243.2, 308.5, 'finalizado'),
(1, 3, 3, 3, 2, '01:33:45.123', '00:01:45.234', 42, 15, 53, 241.8, 305.2, 'finalizado');

-- Insertar vueltas de ejemplo
INSERT INTO lap (id_resultado, numero_vuelta, tiempo_vuelta, velocidad_promedio, sector_1, sector_2, sector_3, posicion_en_vuelta) VALUES
(1, 1, '00:01:55.234', 234.5, '00:00:32.123', '00:00:41.567', '00:00:41.544', 3),
(1, 2, '00:01:46.123', 243.2, '00:00:30.234', '00:00:39.123', '00:00:36.766', 2),
(1, 23, '00:01:44.123', 248.7, '00:00:29.456', '00:00:38.234', '00:00:36.433', 1),
(2, 1, '00:01:54.567', 235.1, '00:00:31.890', '00:00:41.234', '00:00:41.443', 1),
(2, 15, '00:01:45.890', 244.3, '00:00:30.123', '00:00:39.456', '00:00:36.311', 2);

-- ============================================
-- VISTAS MEJORADAS
-- ============================================

-- Vista: Carreras con información completa
CREATE OR REPLACE VIEW v_carreras_completas AS
SELECT 
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
    cat.nombre AS categoria,
    cat.descripcion AS descripcion_categoria,
    co.nombre AS circuito,
    co.longitud_pista,
    co.tipo_pista,
    ci.nombre AS ciudad,
    p.nombre AS pais,
    COUNT(DISTINCT comp.id_compra) AS inscriptos,
    (c.cupo_maximo - COUNT(DISTINCT comp.id_compra)) AS cupos_disponibles
FROM carreras c
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
INNER JOIN country co ON c.id_country = co.id_country
INNER JOIN city ci ON co.id_city = ci.id_city
INNER JOIN pais p ON ci.id_pais = p.id_pais
LEFT JOIN compras comp ON c.id_carrera = comp.id_carrera AND comp.estado_pago IN ('completado', 'pendiente')
GROUP BY c.id_carrera;

-- Vista: Compras/Inscripciones completas
CREATE OR REPLACE VIEW v_compras_completas AS
SELECT 
    comp.id_compra,
    comp.numero_competidor,
    comp.fecha_compra,
    comp.monto,
    comp.estado_pago,
    comp.metodo_pago,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.email AS email_piloto,
    p.telefono,
    p.numero_licencia,
    p.tipo_licencia,
    pa.nombre AS pais_piloto,
    c.nombre AS carrera,
    c.fecha_inicio AS fecha_carrera,
    cat.nombre AS categoria,
    cir.nombre AS circuito,
    ci.nombre AS ciudad,
    v.modelo AS vehiculo,
    b.nombre AS marca_vehiculo
FROM compras comp
INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
INNER JOIN country cir ON c.id_country = cir.id_country
INNER JOIN city ci ON cir.id_city = ci.id_city
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
LEFT JOIN vehicle v ON comp.id_vehicle = v.id_vehicle
LEFT JOIN brand b ON v.id_brand = b.id_brand;

-- Vista: Entregas completas
CREATE OR REPLACE VIEW v_entregas_completas AS
SELECT 
    e.id_entrega,
    e.estado AS estado_entrega,
    e.direccion_envio,
    e.codigo_postal,
    e.fecha_despacho,
    e.fecha_entrega,
    e.tracking_number,
    e.empresa_transporte,
    e.costo_envio,
    ci.nombre AS ciudad,
    comp.id_compra,
    comp.numero_competidor,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.email AS email_piloto,
    p.telefono,
    c.nombre AS carrera,
    c.fecha_inicio AS fecha_carrera,
    CONCAT(u.nombre, ' ', u.apellido) AS gestor,
    u.email AS email_gestor
FROM entregas e
INNER JOIN compras comp ON e.id_compra = comp.id_compra
INNER JOIN pilotos p ON comp.id_piloto = p.id_piloto
INNER JOIN carreras c ON comp.id_carrera = c.id_carrera
INNER JOIN city ci ON e.id_city = ci.id_city
LEFT JOIN usuarios u ON e.id_usuario_gestor = u.id_usuario;

-- Vista: Resultados completos con ranking
CREATE OR REPLACE VIEW v_resultados_completos AS
SELECT 
    r.id_resultado,
    r.posicion_final,
    r.posicion_salida,
    r.tiempo_total,
    r.tiempo_mejor_vuelta,
    r.vuelta_rapida,
    r.puntos,
    r.vueltas_completadas,
    r.velocidad_promedio,
    r.velocidad_maxima,
    r.estado AS estado_resultado,
    r.motivo_abandono,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.numero_licencia,
    p.tipo_licencia,
    pa.nombre AS pais_piloto,
    c.nombre AS carrera,
    c.fecha_inicio AS fecha_carrera,
    cat.nombre AS categoria,
    cir.nombre AS circuito,
    ci.nombre AS ciudad,
    v.modelo AS vehiculo,
    b.nombre AS marca,
    t.nombre AS tipo_vehiculo
FROM resultados r
INNER JOIN pilotos p ON r.id_piloto = p.id_piloto
INNER JOIN carreras c ON r.id_carrera = c.id_carrera
INNER JOIN categorias cat ON c.id_categoria = cat.id_categoria
INNER JOIN country cir ON c.id_country = cir.id_country
INNER JOIN city ci ON cir.id_city = ci.id_city
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
LEFT JOIN vehicle v ON r.id_vehicle = v.id_vehicle
LEFT JOIN brand b ON v.id_brand = b.id_brand
LEFT JOIN type t ON v.id_type = t.id_type
ORDER BY r.id_carrera, r.posicion_final;

-- Vista: Ranking general de pilotos
CREATE OR REPLACE VIEW v_ranking_pilotos AS
SELECT 
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    p.numero_licencia,
    p.tipo_licencia,
    pa.nombre AS pais,
    COUNT(DISTINCT r.id_carrera) AS carreras_participadas,
    SUM(r.puntos) AS puntos_totales,
    COUNT(CASE WHEN r.posicion_final = 1 THEN 1 END) AS victorias,
    COUNT(CASE WHEN r.posicion_final <= 3 THEN 1 END) AS podios,
    AVG(r.posicion_final) AS posicion_promedio,
    MIN(r.tiempo_mejor_vuelta) AS mejor_vuelta_historica
FROM pilotos p
LEFT JOIN resultados r ON p.id_piloto = r.id_piloto
LEFT JOIN pais pa ON p.id_pais = pa.id_pais
WHERE p.activo = TRUE
GROUP BY p.id_piloto
ORDER BY puntos_totales DESC, victorias DESC;

-- Vista: Estadísticas de vueltas por carrera
CREATE OR REPLACE VIEW v_estadisticas_vueltas AS
SELECT 
    l.id_resultado,
    c.nombre AS carrera,
    CONCAT(p.nombre, ' ', p.apellido) AS piloto,
    COUNT(l.id_lap) AS total_vueltas,
    MIN(l.tiempo_vuelta) AS vuelta_mas_rapida,
    MAX(l.tiempo_vuelta) AS vuelta_mas_lenta,
    AVG(TIME_TO_SEC(l.tiempo_vuelta)) AS promedio_segundos,
    AVG(l.velocidad_promedio) AS velocidad_media
FROM lap l
INNER JOIN resultados r ON l.id_resultado = r.id_resultado
INNER JOIN carreras c ON r.id_carrera = c.id_carrera
INNER JOIN pilotos p ON r.id_piloto = p.id_piloto
GROUP BY l.id_resultado;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
