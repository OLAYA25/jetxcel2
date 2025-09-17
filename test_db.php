<?php
/**
 * JETXCEL - Database Connection Test
 * Simple test to verify database connection and basic functionality
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>JETXCEL Database Connection Test</h2>";

try {
    // Test database connection
    $conn = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test tables exist
    $tables = ['productos', 'proveedores', 'compras_cabecera', 'compras_detalle', 'impuestos', 'categorias'];
    
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "<p style='color: green;'>✓ Table '$table' exists with $count records</p>";
    }
    
    // Test models
    echo "<h3>Testing Models:</h3>";
    
    // Test Producto model
    require_once __DIR__ . '/src/models/Producto.php';
    $producto = new Producto();
    $products = $producto->getAll(5); // Get first 5 products
    echo "<p style='color: green;'>✓ Producto model works - Found " . count($products) . " products</p>";
    
    // Test Proveedor model
    require_once __DIR__ . '/src/models/Proveedor.php';
    $proveedor = new Proveedor();
    $suppliers = $proveedor->getAll();
    echo "<p style='color: green;'>✓ Proveedor model works - Found " . count($suppliers) . " suppliers</p>";
    
    // Test Impuesto model
    require_once __DIR__ . '/src/models/Impuesto.php';
    $impuesto = new Impuesto();
    $taxes = $impuesto->getAll();
    echo "<p style='color: green;'>✓ Impuesto model works - Found " . count($taxes) . " tax types</p>";
    
    // Test ImagenController
    require_once __DIR__ . '/src/controllers/ImagenController.php';
    $imagenController = new ImagenController();
    echo "<p style='color: green;'>✓ ImagenController initialized successfully</p>";
    
    echo "<h3>Sample Data:</h3>";
    
    if (!empty($products)) {
        echo "<h4>Products:</h4>";
        foreach (array_slice($products, 0, 3) as $product) {
            echo "<p>- {$product['nombre']} (Ref: {$product['referencia']}) - Cost: $" . number_format($product['costo_unitario'], 2) . "</p>";
        }
    }
    
    if (!empty($taxes)) {
        echo "<h4>Tax Types:</h4>";
        foreach ($taxes as $tax) {
            echo "<p>- {$tax['nombre']} ({$tax['porcentaje']}%)</p>";
        }
    }
    
    echo "<p style='color: blue; font-weight: bold;'>🎉 All tests passed! Database integration is ready.</p>";
    echo "<p><a href='/jetxcel2/src/views/compras.php'>Go to Compras View</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure XAMPP is running and the database 'jetxcel_db' exists.</p>";
    echo "<p>You can import the database using: /jetxcel2/sql/jetxcel_db.sql</p>";
}
?>
