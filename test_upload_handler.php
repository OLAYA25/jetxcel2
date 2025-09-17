<?php
/**
 * JETXCEL - Test Upload Handler
 * Simple handler to test image upload functionality
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/controllers/ImagenController.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Upload Test Results</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h3>File Information:</h3>";
    echo "<pre>";
    print_r($_FILES['test_image']);
    echo "</pre>";
    
    try {
        $imagenController = new ImagenController();
        $result = $imagenController->uploadProductImage($_FILES['test_image']);
        
        echo "<h3>Upload Result:</h3>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        
        if ($result['success']) {
            echo "<p style='color: green;'>✓ Upload successful!</p>";
            echo "<p><strong>File URL:</strong> <a href='" . $result['url'] . "' target='_blank'>" . $result['url'] . "</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Upload failed: " . $result['message'] . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p>No file uploaded or invalid request method.</p>";
}

echo "<p><a href='debug_upload.php'>← Back to Debug Page</a></p>";
?>
