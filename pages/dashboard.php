<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';

// Verificar autenticación
requireAuth();

$currentUser = getCurrentUser();
$db = Database::getInstance();

// Obtener estadísticas
$stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1");
$stmt->execute();
$totalEmpleados = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $db->prepare("SELECT COUNT(DISTINCT usuario_id) as total FROM asistencias WHERE DATE(fecha_hora_entrada) = CURDATE()");
$stmt->execute();
$presentesHoy = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM asistencias WHERE DATE(fecha_hora_entrada) = CURDATE()");
$stmt->execute();
$registrosHoy = $stmt->get_result()->fetch_assoc()['total'];

// Obtener asistencias recientes
$stmt = $db->prepare("
    SELECT a.*, u.nombre, u.apellido, u.username 
    FROM asistencias a 
    JOIN usuarios u ON a.usuario_id = u.id 
    ORDER BY a.fecha_hora_entrada DESC 
    LIMIT 10
");
$stmt->execute();
$asistenciasRecientes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Asistencia</title>
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
                    <?php if (hasRole('admin')): ?>
                        <a href="empleados.php">Empleados</a>
                        <a href="reportes.php">Reportes</a>
                    <?php endif; ?>
                    <a href="logout.php">Cerrar Sesión</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="dashboard">
        <div class="container">
            <h2>Panel de Control</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Empleados</h3>
                    <div class="stat-value"><?php echo $totalEmpleados; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Presentes Hoy</h3>
                    <div class="stat-value"><?php echo $presentesHoy; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Registros Hoy</h3>
                    <div class="stat-value"><?php echo $registrosHoy; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Tasa de Asistencia</h3>
                    <div class="stat-value">
                        <?php echo $totalEmpleados > 0 ? round(($presentesHoy / $totalEmpleados) * 100) : 0; ?>%
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3>Registrar Asistencia</h3>
                <div id="alert-container"></div>
                <form id="asistenciaForm">
                    <div class="form-group">
                        <label for="tipo" class="form-label">Tipo de Registro</label>
                        <select id="tipo" name="tipo" class="form-control" required>
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notas" class="form-label">Notas (opcional)</label>
                        <textarea id="notas" name="notas" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Registrar</button>
                </form>
            </div>
            
            <div class="card">
                <h3>Asistencias Recientes</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Usuario</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($asistenciasRecientes->num_rows > 0): ?>
                                <?php while ($asistencia = $asistenciasRecientes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($asistencia['nombre'] . ' ' . $asistencia['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($asistencia['username']); ?></td>
                                        <td><?php echo $asistencia['fecha_hora_entrada'] ? date('d/m/Y H:i', strtotime($asistencia['fecha_hora_entrada'])) : '-'; ?></td>
                                        <td><?php echo $asistencia['fecha_hora_salida'] ? date('d/m/Y H:i', strtotime($asistencia['fecha_hora_salida'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($asistencia['notas'] ?? '-'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay registros de asistencia</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../js/dashboard.js"></script>
</body>
</html>
