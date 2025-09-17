<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/controllers/ImagenController.php';

// Create test directory if it doesn't exist
$test_dir = __DIR__ . '/tests';
if (!file_exists($test_dir)) {
    mkdir($test_dir, 0777, true);
}

// Create a test image
$test_image_path = $test_dir . '/test_image.jpg';
$im = imagecreatetruecolor(100, 100);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 99, 99, $white);
imagestring($im, 5, 10, 40, 'Test Image', $black);
imagejpeg($im, $test_image_path, 90);
imagedestroy($im);

// Test 1: Direct file upload test
function testDirectFileUpload($file_path) {
    $controller = new ImagenController();
    
    // Simulate $_FILES array
    $test_file = [
        'name' => basename($file_path),
        'type' => mime_content_type($file_path),
        'tmp_name' => $file_path,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($file_path)
    ];
    
    return $controller->uploadProductImage($test_file);
}

// Test 2: Simulate form submission with file upload
function testFormUpload($file_path) {
    // Create a copy of the file in the temp directory
    $tmp_file = '/tmp/' . uniqid() . '_' . basename($file_path);
    copy($file_path, $tmp_file);
    
    // Simulate $_FILES array as it would come from a form
    $test_file = [
        'name' => basename($file_path),
        'type' => mime_content_type($file_path),
        'tmp_name' => $tmp_file,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($file_path)
    ];
    
    $controller = new ImagenController();
    $result = $controller->uploadProductImage($test_file);
    
    // Clean up temp file
    if (file_exists($tmp_file)) {
        unlink($tmp_file);
    }
    
    return $result;
}

// Run tests
echo "<h1>Pruebas de Carga de Imágenes</h1>";

// Test 1: Direct file upload
echo "<h2>Test 1: Carga directa de archivo</h2>";
$result1 = testDirectFileUpload($test_image_path);
echo "<pre>";
print_r($result1);
echo "</pre>";

// Recreate test image for the second test
createTestImage($test_image_path);

// Test 2: Form upload simulation
echo "<h2>Test 2: Simulación de envío de formulario</h2>";
$result2 = testFormUpload($test_image_path);
echo "<pre>";
print_r($result2);
echo "</pre>";

// Clean up test image
if (file_exists($test_image_path)) {
    unlink($test_image_path);
}

// Function to create test image
function createTestImage($path) {
    $im = imagecreatetruecolor(100, 100);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 99, 99, $white);
    imagestring($im, 5, 10, 40, 'Test Image', $black);
    imagejpeg($im, $path, 90);
    imagedestroy($im);
    chmod($path, 0644);
}

// Display system information
echo "<h2>Información del sistema</h2>";

// Check upload directory permissions
$upload_dir = PRODUCT_IMAGES_PATH;
echo "<h3>Permisos del directorio de carga</h3>";
echo "Directorio: " . $upload_dir . "<br>";
echo "Existe: " . (file_exists($upload_dir) ? 'Sí' : 'No') . "<br>";
echo "Es escribible: " . (is_writable($upload_dir) ? 'Sí' : 'No') . "<br>";

// Check PHP upload settings
echo "<h3>Configuración de PHP</h3>";
echo "file_uploads: " . ini_get('file_uploads') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "<br>";

// Check required extensions
echo "<h3>Extensiones PHP requeridas</h3>";
echo "fileinfo: " . (extension_loaded('fileinfo') ? 'Cargada' : 'No cargada') . "<br>";
echo "gd: " . (extension_loaded('gd') ? 'Cargada' : 'No cargada') . "<br>";

// Check directory permissions
echo "<h3>Permisos de directorios</h3>";
$dirs_to_check = [
    '/opt/lampp/htdocs/jetxcel2/public/uploads',
    '/opt/lampp/htdocs/jetxcel2/public/uploads/productos',
    '/opt/lampp/temp'
];

foreach ($dirs_to_check as $dir) {
    echo "$dir: ";
    if (file_exists($dir)) {
        echo "Existe, ";
        echo is_writable($dir) ? 'Escribible' : 'No escribible';
    } else {
        echo "No existe";
    }
    echo "<br>";
}

// Check PHP user
$user = function_exists('exec') ? @exec('whoami') : 'No se pudo determinar';
echo "<p>Usuario de PHP: $user</p>";

// Check file owner
if (file_exists($test_image_path)) {
    $owner = fileowner($test_image_path);
    $group = filegroup($test_image_path);
    echo "<p>Propietario del archivo de prueba: $owner:$group</p>";
}
?>
