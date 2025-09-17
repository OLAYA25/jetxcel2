<?php
/**
 * JETXCEL - Create Purchase API
 * Creates a new purchase with details
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Compra.php';
require_once __DIR__ . '/../models/Producto.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos de entrada inválidos');
    }

    // Validate required fields
    $required_fields = ['proveedor_id', 'productos', 'medio_pago'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    if (!is_array($input['productos']) || empty($input['productos'])) {
        throw new Exception('Debe incluir al menos un producto');
    }

    // Calculate totals
    $subtotal = 0;
    $total_impuestos = 0;
    $detalles = [];

    foreach ($input['productos'] as $producto) {
        $cantidad = floatval($producto['cantidad']);
        $costo_unitario = floatval($producto['costo_unitario']);
        $porcentaje_impuesto = floatval($producto['porcentaje_impuesto'] ?? DEFAULT_IVA_PERCENTAGE);
        
        $subtotal_producto = $cantidad * $costo_unitario;
        $impuesto_producto = ($subtotal_producto * $porcentaje_impuesto) / 100;
        
        $subtotal += $subtotal_producto;
        $total_impuestos += $impuesto_producto;
        
        $detalles[] = [
            'producto_id' => $producto['producto_id'],
            'cantidad' => $cantidad,
            'costo_unitario' => $costo_unitario,
            'impuesto_id' => $producto['impuesto_id'] ?? DEFAULT_IVA_ID,
            'porcentaje_impuesto' => $porcentaje_impuesto,
            'precio_venta_sugerido' => floatval($producto['precio_venta_sugerido'] ?? $costo_unitario * 1.3)
        ];
    }

    $descuento = floatval($input['descuento'] ?? 0);
    $total = $subtotal + $total_impuestos - $descuento;

    // Prepare purchase data
    $compra_data = [
        'proveedor_id' => $input['proveedor_id'],
        'numero_factura' => $input['numero_factura'] ?? null,
        'fecha_factura' => $input['fecha_factura'] ?? date('Y-m-d'),
        'subtotal' => $subtotal,
        'impuestos' => $total_impuestos,
        'descuento' => $descuento,
        'total' => $total,
        'medio_pago' => $input['medio_pago'],
        'usuario_id' => $input['usuario_id'] ?? 1,
        'descripcion' => $input['descripcion'] ?? null
    ];

    $compra = new Compra();
    $compra_id = $compra->create($compra_data, $detalles);

    echo json_encode([
        'success' => true,
        'message' => 'Compra registrada exitosamente',
        'data' => [
            'compra_id' => $compra_id,
            'total' => $total
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la compra: ' . $e->getMessage()
    ]);
}
?>
