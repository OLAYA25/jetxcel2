<?php
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['sale_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de venta no especificado']);
    exit;
}

$saleId = $_GET['sale_id'];

try {
    $query = "SELECT p.nombre, vp.cantidad, vp.precio_unitario
              FROM ventas_productos vp
              JOIN productos p ON vp.producto_id = p.id
              WHERE vp.venta_id = :sale_id";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
}
?>
