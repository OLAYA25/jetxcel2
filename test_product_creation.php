<?php
// Test product creation
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/models/Producto.php';
require_once __DIR__ . '/src/controllers/ImagenController.php';

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test data
$test_product = [
    'nombre' => 'Producto de Prueba ' . time(),
    'referencia' => 'TEST-' . rand(1000, 9999),
    'categoria_id' => 1, // Make sure this category exists
    'costo_unitario' => 10000,
    'precio_venta_sin_iva' => 15000,
    'stock' => 10,
    'stock_minimo' => 5,
    'descripcion' => 'Este es un producto de prueba',
    'impuesto_id' => 2, // 19% IVA
    'impuesto_compra_id' => 2 // 19% IVA
];

try {
    // Test database connection
    $database = new Database();
    $conn = $database->getConnection();
    echo "Database connection successful!<br><br>";
    
    // Test product creation
    $producto = new Producto();
    $result = $producto->create($test_product);
    
    if ($result) {
        $producto_id = $producto->getLastInsertId();
        echo "Product created successfully!<br>";
        echo "Product ID: " . $producto_id . "<br>";
        
        // Get the created product
        $created = $producto->getById($producto_id);
        echo "<pre>";
        print_r($created);
        echo "</pre>";
    } else {
        echo "Failed to create product<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>
