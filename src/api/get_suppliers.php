<?php
/**
 * JETXCEL - Get Suppliers API
 * Returns list of active suppliers (without RUT field)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/config.php';

try {
    $conn = getDBConnection();
    
    // Get all active suppliers
    $stmt = $conn->prepare("
        SELECT 
            id,
            nombre,
            telefono,
            nit,
            direccion,
            ciudad,
            email,
            descripcion,
            fecha_creacion
        FROM proveedores 
        WHERE estado = 'activo' 
        ORDER BY nombre ASC
    ");
    
    $stmt->execute();
    $suppliers = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $suppliers
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener proveedores: ' . $e->getMessage()
    ]);
}
?>
