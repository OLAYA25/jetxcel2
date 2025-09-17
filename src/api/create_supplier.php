<?php
/**
 * JETXCEL - Create Supplier API
 * Creates a new supplier (without RUT field)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['nombre'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El nombre del proveedor es requerido']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Check if NIT already exists (if provided)
    if (!empty($input['nit'])) {
        $stmt = $conn->prepare("SELECT id FROM proveedores WHERE nit = ? AND estado = 'activo'");
        $stmt->execute([$input['nit']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ya existe un proveedor con este NIT']);
            exit;
        }
    }
    
    // Insert new supplier
    $stmt = $conn->prepare("
        INSERT INTO proveedores (nombre, telefono, nit, direccion, ciudad, email, descripcion) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        sanitizeInput($input['nombre']),
        sanitizeInput($input['telefono'] ?? ''),
        sanitizeInput($input['nit'] ?? ''),
        sanitizeInput($input['direccion'] ?? ''),
        sanitizeInput($input['ciudad'] ?? ''),
        sanitizeInput($input['email'] ?? ''),
        sanitizeInput($input['descripcion'] ?? '')
    ]);
    
    $supplierId = $conn->lastInsertId();
    
    // Get the created supplier
    $stmt = $conn->prepare("
        SELECT id, nombre, telefono, nit, direccion, ciudad, email, descripcion, fecha_creacion
        FROM proveedores WHERE id = ?
    ");
    $stmt->execute([$supplierId]);
    $supplier = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Proveedor creado exitosamente',
        'data' => $supplier
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Create supplier error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => APP_ENV === 'development' ? $e->getMessage() : null
    ], JSON_UNESCAPED_UNICODE);
}
?>
