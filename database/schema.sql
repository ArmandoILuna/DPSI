-- Base de datos para el Sistema de Asistencia
-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS sistema_asistencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_asistencia;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    rol ENUM('admin', 'empleado') DEFAULT 'empleado',
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de asistencias
CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_hora_entrada DATETIME NOT NULL,
    fecha_hora_salida DATETIME,
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_hora_entrada),
    INDEX idx_fecha_entrada (fecha_hora_entrada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de actividad de usuarios (log)
CREATE TABLE IF NOT EXISTS actividad_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_hora),
    INDEX idx_accion (accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (en producción debe cambiarse)
INSERT INTO usuarios (username, password, nombre, apellido, email, rol, activo) VALUES
('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lk4q9Zg5YhEm', 'Administrador', 'Sistema', 'admin@sistema.com', 'admin', 1);

-- Insertar usuarios de ejemplo (contraseña: 123456)
INSERT INTO usuarios (username, password, nombre, apellido, email, rol, activo) VALUES
('empleado1', '$2y$12$EY8FGqK5gVJvQq5ZQw8pKu5eLYS4XBTf4mJ/3j3TQmZfNz3fN0YGu', 'Juan', 'Pérez', 'juan.perez@empresa.com', 'empleado', 1),
('empleado2', '$2y$12$EY8FGqK5gVJvQq5ZQw8pKu5eLYS4XBTf4mJ/3j3TQmZfNz3fN0YGu', 'María', 'González', 'maria.gonzalez@empresa.com', 'empleado', 1),
('empleado3', '$2y$12$EY8FGqK5gVJvQq5ZQw8pKu5eLYS4XBTf4mJ/3j3TQmZfNz3fN0YGu', 'Carlos', 'Rodríguez', 'carlos.rodriguez@empresa.com', 'empleado', 1);

-- Insertar algunos registros de asistencia de ejemplo
INSERT INTO asistencias (usuario_id, fecha_hora_entrada, fecha_hora_salida, notas) VALUES
(2, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, 'Día normal de trabajo'),
(3, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 9 HOUR, 'Día normal de trabajo'),
(4, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, 'Día normal de trabajo'),
(2, NOW() - INTERVAL 2 HOUR, NULL, 'Entrada de hoy');
