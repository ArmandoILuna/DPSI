<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';

// Verificar autenticación y rol de administrador
requireAuth();

if (!hasRole('admin')) {
    header('Location: dashboard.php');
    exit();
}

$currentUser = getCurrentUser();
$db = Database::getInstance();

// Obtener lista de empleados
$stmt = $db->prepare("
    SELECT id, username, nombre, apellido, email, telefono, rol, activo, fecha_registro 
    FROM usuarios 
    ORDER BY apellido, nombre
");
$stmt->execute();
$empleados = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - Sistema de Asistencia</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1>Sistema de Asistencia</h1>
                <nav class="nav">
                    <span style="color: white;">Bienvenido, <?php echo htmlspecialchars($currentUser['nombre_completo']); ?></span>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="empleados.php">Empleados</a>
                    <a href="reportes.php">Reportes</a>
                    <a href="logout.php">Cerrar Sesión</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="dashboard">
        <div class="container">
            <h2>Gestión de Empleados</h2>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Lista de Empleados</h3>
                    <button class="btn btn-success" onclick="alert('Funcionalidad de agregar empleado - Por implementar')">
                        + Agregar Empleado
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($empleados->num_rows > 0): ?>
                                <?php while ($empleado = $empleados->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $empleado['id']; ?></td>
                                        <td><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['username']); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['email'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['telefono'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; background-color: <?php echo $empleado['rol'] === 'admin' ? '#3498db' : '#95a5a6'; ?>; color: white;">
                                                <?php echo ucfirst($empleado['rol']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; background-color: <?php echo $empleado['activo'] ? '#27ae60' : '#e74c3c'; ?>; color: white;">
                                                <?php echo $empleado['activo'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($empleado['fecha_registro'])); ?></td>
                                        <td>
                                            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem;" onclick="alert('Editar empleado ID: <?php echo $empleado['id']; ?>')">
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No hay empleados registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Resaltar fila de tabla al pasar el mouse
        const tableRows = document.querySelectorAll('.table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#ecf0f1';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    </script>
</body>
</html>
