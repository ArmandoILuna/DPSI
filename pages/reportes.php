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

// Obtener rango de fechas (por defecto, últimos 7 días)
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-7 days'));
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

// Obtener resumen de asistencias
$stmt = $db->prepare("
    SELECT 
        u.id,
        u.nombre,
        u.apellido,
        COUNT(DISTINCT DATE(a.fecha_hora_entrada)) as dias_asistidos,
        COUNT(a.id) as total_registros,
        MIN(a.fecha_hora_entrada) as primera_entrada,
        MAX(COALESCE(a.fecha_hora_salida, a.fecha_hora_entrada)) as ultima_salida
    FROM usuarios u
    LEFT JOIN asistencias a ON u.id = a.usuario_id 
        AND DATE(a.fecha_hora_entrada) BETWEEN ? AND ?
    WHERE u.activo = 1 AND u.rol = 'empleado'
    GROUP BY u.id, u.nombre, u.apellido
    ORDER BY u.apellido, u.nombre
");
$stmt->bind_param("ss", $fechaInicio, $fechaFin);
$stmt->execute();
$resumen = $stmt->get_result();

// Calcular días hábiles en el rango
$diasHabiles = 0;
$fechaActual = strtotime($fechaInicio);
$fechaFinTimestamp = strtotime($fechaFin);
while ($fechaActual <= $fechaFinTimestamp) {
    $diaSemana = date('N', $fechaActual);
    if ($diaSemana < 6) { // Lunes a Viernes
        $diasHabiles++;
    }
    $fechaActual = strtotime('+1 day', $fechaActual);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Asistencia</title>
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
            <h2>Reportes de Asistencia</h2>
            
            <div class="card">
                <h3>Filtros</h3>
                <form method="GET" action="">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input 
                                type="date" 
                                id="fecha_inicio" 
                                name="fecha_inicio" 
                                class="form-control" 
                                value="<?php echo htmlspecialchars($fechaInicio); ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input 
                                type="date" 
                                id="fecha_fin" 
                                name="fecha_fin" 
                                class="form-control" 
                                value="<?php echo htmlspecialchars($fechaFin); ?>"
                                required
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Resumen de Asistencias</h3>
                    <div>
                        <strong>Período:</strong> <?php echo date('d/m/Y', strtotime($fechaInicio)); ?> - <?php echo date('d/m/Y', strtotime($fechaFin)); ?>
                        <br>
                        <strong>Días hábiles:</strong> <?php echo $diasHabiles; ?>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Días Asistidos</th>
                                <th>Total Registros</th>
                                <th>Tasa Asistencia</th>
                                <th>Primera Entrada</th>
                                <th>Última Salida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resumen->num_rows > 0): ?>
                                <?php while ($empleado = $resumen->fetch_assoc()): ?>
                                    <?php 
                                        $tasaAsistencia = $diasHabiles > 0 ? round(($empleado['dias_asistidos'] / $diasHabiles) * 100) : 0;
                                        $colorTasa = $tasaAsistencia >= 90 ? '#27ae60' : ($tasaAsistencia >= 70 ? '#f39c12' : '#e74c3c');
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?></td>
                                        <td><?php echo $empleado['dias_asistidos']; ?> / <?php echo $diasHabiles; ?></td>
                                        <td><?php echo $empleado['total_registros']; ?></td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; background-color: <?php echo $colorTasa; ?>; color: white; font-weight: bold;">
                                                <?php echo $tasaAsistencia; ?>%
                                            </span>
                                        </td>
                                        <td><?php echo $empleado['primera_entrada'] ? date('d/m/Y H:i', strtotime($empleado['primera_entrada'])) : 'N/A'; ?></td>
                                        <td><?php echo $empleado['ultima_salida'] ? date('d/m/Y H:i', strtotime($empleado['ultima_salida'])) : 'N/A'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay datos para el período seleccionado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <button class="btn btn-success" onclick="window.print()">
                        🖨️ Imprimir Reporte
                    </button>
                    <button class="btn btn-secondary" onclick="alert('Exportar a Excel - Por implementar')">
                        📊 Exportar a Excel
                    </button>
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
    
    <style>
        @media print {
            .header, .btn, form { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</body>
</html>
