<?php
require '../config/db.php';

header('Content-Type: application/json');

// Habilitar CORS si es necesario
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents('php://input'), true);

// Validar datos recibidos
if (!$data || !isset($data['products']) || count($data['products']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos de venta inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Calcular el total de la venta
    $total = 0;
    foreach ($data['products'] as $product) {
        $total += $product['unit_price'] * $product['quantity'];
    }
    $total -= $data['discount'];

    // Insertar venta principal
    $stmt = $pdo->prepare("INSERT INTO ventas (cliente_id, medio_pago, valor_recibido, vueltos, descuento, total, descripcion) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['client_id'],
        $data['payment_method'],
        $data['amount_received'],
        $data['change'],
        $data['discount'],
        $total,
        $data['notes']
    ]);
    $saleId = $pdo->lastInsertId();
    
    // Insertar productos de la venta
    foreach ($data['products'] as $product) {
        $stmt = $pdo->prepare("INSERT INTO ventas_productos (venta_id, producto_id, cantidad, precio_unitario) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $saleId,
            $product['product_id'],
            $product['quantity'],
            $product['unit_price']
        ]);
        
        // Actualizar stock del producto
        $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$product['quantity'], $product['product_id']]);
        
        // Registrar movimiento de stock
        $stmt = $pdo->prepare("INSERT INTO movimientos_stock (producto_id, tipo, cantidad, referencia_id, notas) 
                               VALUES (?, 'venta', ?, ?, ?)");
        $stmt->execute([
            $product['product_id'],
            $product['quantity'] * -1,  // Cantidad negativa para ventas
            $saleId,
            "Venta #$saleId"
        ]);
    }
    
    $pdo->commit();

    // 🔥 Después de guardar la venta, obtenemos la venta recién creada para enviarla al frontend
    $stmt = $pdo->prepare("
        SELECT v.id, v.total, v.fecha, c.nombre AS cliente, v.medio_pago 
        FROM ventas v
        LEFT JOIN clientes c ON v.cliente_id = c.id
        WHERE v.id = ?
    ");
    $stmt->execute([$saleId]);
    $ventaReciente = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'sale_id' => $saleId,
        'venta' => $ventaReciente  // ✅ devolvemos la venta recién hecha
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar venta: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
