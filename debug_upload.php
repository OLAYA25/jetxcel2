<?php
/**
 * JETXCEL - Debug Upload Test
 * Simple test to verify image upload functionality
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/controllers/ImagenController.php';

echo "<h2>JETXCEL Image Upload Debug</h2>";

// Check directory permissions
$uploadDir = PRODUCT_IMAGES_PATH;
echo "<h3>Directory Information:</h3>";
echo "<p><strong>Upload Directory:</strong> $uploadDir</p>";
echo "<p><strong>Directory exists:</strong> " . (file_exists($uploadDir) ? "✓ Yes" : "❌ No") . "</p>";
echo "<p><strong>Directory writable:</strong> " . (is_writable($uploadDir) ? "✓ Yes" : "❌ No") . "</p>";
echo "<p><strong>Directory permissions:</strong> " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";

// Check PHP upload settings
echo "<h3>PHP Upload Settings:</h3>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";
echo "<p><strong>file_uploads:</strong> " . (ini_get('file_uploads') ? "✓ Enabled" : "❌ Disabled") . "</p>";

// Test ImagenController initialization
try {
    $imagenController = new ImagenController();
    echo "<p style='color: green;'>✓ ImagenController initialized successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ImagenController error: " . $e->getMessage() . "</p>";
}

// Simple upload form for testing
?>
<h3>Test Image Upload:</h3>
<form action="test_upload_handler.php" method="post" enctype="multipart/form-data">
    <p>
        <label>Select image file:</label><br>
        <input type="file" name="test_image" accept="image/*" required>
    </p>
    <p>
        <button type="submit">Test Upload</button>
    </p>
</form>

<h3>Recent Error Logs:</h3>
<div style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto;">
<?php
$error_log = '/opt/lampp/logs/php_error_log';
if (file_exists($error_log)) {
    $lines = file($error_log);
    $recent_lines = array_slice($lines, -20); // Last 20 lines
    foreach ($recent_lines as $line) {
        if (strpos($line, 'jetxcel') !== false || strpos($line, 'ImagenController') !== false) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
} else {
    echo "Error log not found at: $error_log";
}
?>
</div>
