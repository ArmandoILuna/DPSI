<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';

// Verificar autenticación
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener datos
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';

// Validar datos
if (empty($tipo) || !in_array($tipo, ['entrada', 'salida'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de registro inválido']);
    exit();
}

$db = Database::getInstance();
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

try {
    if ($tipo === 'entrada') {
        // Verificar si ya tiene entrada hoy sin salida
        $stmt = $db->prepare("
            SELECT id FROM asistencias 
            WHERE usuario_id = ? 
            AND DATE(fecha_hora_entrada) = CURDATE() 
            AND fecha_hora_salida IS NULL
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya tiene una entrada registrada sin salida']);
            exit();
        }
        
        // Registrar entrada
        $stmt = $db->prepare("
            INSERT INTO asistencias (usuario_id, fecha_hora_entrada, notas) 
            VALUES (?, NOW(), ?)
        ");
        $stmt->bind_param("is", $userId, $notas);
        $stmt->execute();
        
        logUserActivity($userId, 'registro_entrada', 'Registro de entrada');
        
        echo json_encode([
            'success' => true, 
            'message' => 'Entrada registrada exitosamente',
            'tipo' => 'entrada'
        ]);
        
    } else {
        // Buscar entrada sin salida para hoy
        $stmt = $db->prepare("
            SELECT id FROM asistencias 
            WHERE usuario_id = ? 
            AND DATE(fecha_hora_entrada) = CURDATE() 
            AND fecha_hora_salida IS NULL
            ORDER BY fecha_hora_entrada DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'No hay entrada registrada para hoy']);
            exit();
        }
        
        $asistencia = $result->fetch_assoc();
        
        // Registrar salida
        $stmt = $db->prepare("
            UPDATE asistencias 
            SET fecha_hora_salida = NOW(), notas = CONCAT(COALESCE(notas, ''), ?) 
            WHERE id = ?
        ");
        $notasCompletas = $notas ? "\n[Salida] " . $notas : '';
        $stmt->bind_param("si", $notasCompletas, $asistencia['id']);
        $stmt->execute();
        
        logUserActivity($userId, 'registro_salida', 'Registro de salida');
        
        echo json_encode([
            'success' => true, 
            'message' => 'Salida registrada exitosamente',
            'tipo' => 'salida'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al registrar asistencia: ' . $e->getMessage()]);
}
?>
