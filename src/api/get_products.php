<?php
/**
 * JETXCEL - Get Products API
 * Returns all active products with category and tax information
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../models/Producto.php';

try {
    $producto = new Producto();
    $products = $producto->getAll();
    
    echo json_encode([
        'success' => true,
        'data' => $products
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos: ' . $e->getMessage()
    ]);
}
?>
