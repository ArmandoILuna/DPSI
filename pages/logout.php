<?php
require_once __DIR__ . '/../includes/auth.php';

// Cerrar sesión
logout();

// Redirigir al login
header('Location: login.php');
exit();
?>
