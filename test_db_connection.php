<?php
// Test database connection and query
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Database connection successful!<br><br>";
    
    // Test query to check products table
    $query = "SELECT COUNT(*) as count FROM productos";
    $stmt = $conn->query($query);
    $result = $stmt->fetch();
    
    echo "Number of products in database: " . $result['count'] . "<br><br>";
    
    // Show table structure
    echo "Table structure for 'productos':<br>";
    $stmt = $conn->query("DESCRIBE productos");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
