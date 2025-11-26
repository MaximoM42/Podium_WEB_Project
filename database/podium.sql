-- Base de datos Podium
-- Sistema de gestión de resultados de carreras

CREATE DATABASE IF NOT EXISTS podium CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE podium;

-- Tabla de usuarios con roles
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    firebase_uid VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_firebase_uid (firebase_uid),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categories (
    id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de vehículos
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    brand VARCHAR(100),
    model VARCHAR(100),
    year INT,
    category_id VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de carreras
CREATE TABLE IF NOT EXISTS races (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id VARCHAR(10) NOT NULL,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    date DATE NOT NULL,
    status ENUM('scheduled', 'ongoing', 'finished') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_date (date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de posiciones/resultados
CREATE TABLE IF NOT EXISTS positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    race_id INT NOT NULL,
    position INT NOT NULL,
    driver_name VARCHAR(100) NOT NULL,
    vehicle_id INT,
    time_seconds DECIMAL(10, 3),
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    INDEX idx_race (race_id),
    INDEX idx_position (position),
    UNIQUE KEY unique_race_position (race_id, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías iniciales
INSERT INTO categories (id, name, description, image_url) VALUES
('TC', 'Turismo Carretera', 'Categoría principal del automovilismo argentino', '/tc.jpg'),
('TCP', 'Turismo Carretera Pista', 'Categoría de pista del turismo carretera', '/tcp.jpg'),
('TCPK', 'Turismo Carretera Pick Ups', 'Categoría de pick-ups del turismo carretera', '/tcpk.jpg');

-- Insertar vehículos de ejemplo
INSERT INTO vehicles (name, brand, model, year, category_id) VALUES
('Toyota Camry', 'Toyota', 'Camry', 2023, 'TC'),
('Chevrolet Camaro', 'Chevrolet', 'Camaro', 2023, 'TC'),
('Torino', 'IKA', 'Torino', 1970, 'TC'),
('Dodge Challenger', 'Dodge', 'Challenger', 2023, 'TC'),
('Ford Falcon', 'Ford', 'Falcon', 1978, 'TC'),
('Chevrolet Chevy', 'Chevrolet', 'Chevy', 1998, 'TC'),
('Dodge GTX', 'Dodge', 'GTX', 1970, 'TCP'),
('Ford Mustang', 'Ford', 'Mustang', 2023, 'TCP'),
('Chevrolet Silverado', 'Chevrolet', 'Silverado', 2023, 'TCPK'),
('Ford F-150', 'Ford', 'F-150', 2023, 'TCPK'),
('RAM 1500', 'RAM', '1500', 2023, 'TCPK'),
('Toyota Hilux', 'Toyota', 'Hilux', 2023, 'TCPK');

-- Insertar carreras de ejemplo
INSERT INTO races (category_id, name, location, date, status) VALUES
('TC', 'RUS Grand Prix: San Luis Heat', 'San Luis', '2025-11-10', 'finished'),
('TC', 'Lusqtoff Black Grand Prix: Parana Heat', 'Paraná', '2025-11-15', 'finished'),
('TC', 'Shell V-Power Grand Prix: Buenos Aires Heat', 'Buenos Aires', '2025-11-15', 'finished'),
('TC', 'YPF Grand Prix: Córdoba Heat', 'Córdoba', '2025-11-20', 'scheduled'),
('TC', 'Moura Grand Prix: Rosario Heat', 'Rosario', '2025-11-25', 'scheduled'),
('TCP', 'RUS Grand Prix: San Luis Heat', 'San Luis', '2025-11-10', 'finished'),
('TCP', 'Lusqtoff Black Grand Prix: Parana Heat', 'Paraná', '2025-11-15', 'finished'),
('TCP', 'Shell V-Power Grand Prix: Buenos Aires Heat', 'Buenos Aires', '2025-11-20', 'scheduled'),
('TCPK', 'Gran Premio de La Plata', 'La Plata', '2025-11-22', 'scheduled'),
('TCPK', 'Desafío Mendoza', 'Mendoza', '2025-11-28', 'scheduled');

-- Insertar posiciones de ejemplo para las carreras finalizadas
-- Carrera 1: TC San Luis
INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES
(1, 1, 'Maximo', 1, 90.5, 25),
(1, 2, 'Ezequiel', 2, 92.3, 18),
(1, 3, 'Santiago', 5, 93.1, 15),
(1, 4, 'Federico', 3, 94.7, 12);

-- Carrera 2: TC Paraná
INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES
(2, 1, 'Laura', 3, 88.2, 25),
(2, 2, 'Carlos', 4, 90.1, 18),
(2, 3, 'Maximo', 1, 91.5, 15),
(2, 4, 'Diego', 6, 92.8, 12);

-- Carrera 3: TC Buenos Aires
INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES
(3, 1, 'Ana', 5, 87.5, 25),
(3, 2, 'Juan', 6, 89.2, 18),
(3, 3, 'Laura', 3, 90.0, 15),
(3, 4, 'Carlos', 4, 91.3, 12);

-- Carrera 6: TCP San Luis
INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES
(6, 1, 'Marcos', 7, 85.0, 25),
(6, 2, 'Claudio', 3, 87.5, 18),
(6, 3, 'Roberto', 8, 88.9, 15);

-- Carrera 7: TCP Paraná
INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES
(7, 1, 'Sergio', 5, 86.3, 25),
(7, 2, 'Marcos', 7, 88.1, 18),
(7, 3, 'Pablo', 8, 89.7, 15);

-- Insertar usuario administrador de ejemplo (deberás crear este usuario en Firebase primero)
-- Reemplaza 'FIREBASE_UID_AQUI' con el UID real de Firebase
INSERT INTO users (email, firebase_uid, role) VALUES
('admin@podium.com', 'ADMIN_FIREBASE_UID', 'admin');

