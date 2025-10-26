<?php
require_once __DIR__ . '/../includes/auth.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = 'Por favor, complete todos los campos.';
        } else {
            if (authenticateUser($username, $password)) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Asistencia</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Sistema de Asistencia</h1>
                <p>Ingrese sus credenciales para continuar</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" id="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        required 
                        autofocus
                        autocomplete="username"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="text-center mt-3">
                <p style="color: #7f8c8d; font-size: 0.9rem;">
                    Sistema de Control de Asistencia de Personal
                </p>
            </div>
        </div>
    </div>
    
    <script src="../js/login.js"></script>
</body>
</html>
