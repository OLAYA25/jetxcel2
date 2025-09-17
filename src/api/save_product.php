<?php
require '../config/db.php';

header('Content-Type: application/json');

$data = $_POST;

try {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, referencia, fabricante, modelo, categoria, codigo_barras, 
                           descripcion, costo_unitario, precio_venta, stock) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['productName'],
        $data['productCode'],
        $data['productManufacturer'],
        $data['productModel'],
        $data['productCategory'],
        $data['productCode'], // Usando el mismo código para código de barras
        $data['productDescription'],
        $data['productCost'],
        $data['productPrice'],
        $data['productStock']
    ]);
    
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar producto: ' . $e->getMessage()]);
}
?>
