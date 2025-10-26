<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../config/config.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Autenticar usuario
 */
function authenticateUser($username, $password) {
    $db = Database::getInstance();
    
    // Sanitizar entrada
    $username = $db->escape($username);
    
    // Preparar consulta
    $stmt = $db->prepare("SELECT id, username, password, nombre, apellido, rol FROM usuarios WHERE username = ? AND activo = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña (usando password_verify para bcrypt)
        if (password_verify($password, $user['password'])) {
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_completo'] = $user['nombre'] . ' ' . $user['apellido'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Registrar inicio de sesión
            logUserActivity($user['id'], 'login', 'Inicio de sesión exitoso');
            
            return true;
        }
    }
    
    return false;
}

/**
 * Verificar si el usuario está autenticado
 */
function isAuthenticated() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
        return false;
    }
    
    // Verificar timeout de sesión
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        logout();
        return false;
    }
    
    // Actualizar última actividad
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Cerrar sesión
 */
function logout() {
    if (isset($_SESSION['user_id'])) {
        logUserActivity($_SESSION['user_id'], 'logout', 'Cierre de sesión');
    }
    
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

/**
 * Redirigir si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /pages/login.php');
        exit();
    }
}

/**
 * Registrar actividad del usuario
 */
function logUserActivity($user_id, $action, $description) {
    $db = Database::getInstance();
    $stmt = $db->prepare("INSERT INTO actividad_usuarios (usuario_id, accion, descripcion, ip_address, fecha_hora) VALUES (?, ?, ?, ?, NOW())");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("isss", $user_id, $action, $description, $ip);
    $stmt->execute();
}

/**
 * Obtener información del usuario actual
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nombre_completo' => $_SESSION['nombre_completo'],
        'rol' => $_SESSION['rol']
    ];
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function hasRole($role) {
    return isAuthenticated() && isset($_SESSION['rol']) && $_SESSION['rol'] === $role;
}

/**
 * Crear hash de contraseña seguro
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Prevenir ataques CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
