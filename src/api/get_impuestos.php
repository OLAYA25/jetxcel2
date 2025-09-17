<?php
/**
 * JETXCEL - Get Taxes API
 * Returns all active taxes for dropdowns
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../models/Impuesto.php';

try {
    $impuesto = new Impuesto();
    $impuestos = $impuesto->getAll();
    
    echo json_encode([
        'success' => true,
        'data' => $impuestos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los impuestos: ' . $e->getMessage()
    ]);
}
?>
