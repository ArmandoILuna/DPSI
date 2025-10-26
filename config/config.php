<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_asistencia');

// Configuración de la sesión
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de seguridad
define('HASH_ALGORITHM', 'sha256');
define('PASSWORD_MIN_LENGTH', 6);

// Zona horaria
date_default_timezone_set('America/Mexico_City');
?>
