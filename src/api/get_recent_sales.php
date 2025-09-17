<?php
ob_start();
header('Content-Type: application/json');
require '../config/db.php';

try {
    $query = "
        SELECT 
            v.id, 
            v.fecha_venta, 
            v.medio_pago,
            v.total,
            v.descuento,
            c.nombre AS cliente_nombre,
            c.telefono AS cliente_telefono
        FROM ventas v
        LEFT JOIN clientes c ON v.cliente_id = c.id
        ORDER BY v.fecha_venta DESC
        LIMIT 5
    ";
    
    $stmt = $pdo->query($query);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener productos de cada venta
    foreach ($sales as &$sale) {
        $detalleQuery = "
            SELECT 
                p.nombre AS producto_nombre,
                dv.cantidad,
                dv.precio_unitario
            FROM detalle_ventas dv
            JOIN productos p ON dv.producto_id = p.id
            WHERE dv.venta_id = ?
        ";
        $stmtDetalle = $pdo->prepare($detalleQuery);
        $stmtDetalle->execute([$sale['id']]);
        $sale['productos'] = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        // Formatear fecha
        $date = new DateTime($sale['fecha_venta']);
        $sale['fecha_formateada'] = $date->format('d/m/Y H:i');
    }
    
    ob_clean();
    echo json_encode(['success' => true, 'data' => $sales]);
    exit;
} catch (PDOException $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al cargar ventas: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
?>
