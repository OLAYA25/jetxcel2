<?php
/**
 * JETXCEL - Create Product API
 * Creates a new product with image upload support
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log the request for debugging
error_log('Received request: ' . print_r($_REQUEST, true));
if (isset($_FILES['imagen'])) {
    error_log('File upload details: ' . print_r($_FILES['imagen'], true));
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Include required files
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../models/Producto.php';
    require_once __DIR__ . '/../controllers/ImagenController.php';

    // Ensure DEFAULT_IVA_ID is defined
    if (!defined('DEFAULT_IVA_ID')) {
        define('DEFAULT_IVA_ID', 2); // Default to 19% IVA if not defined in config
    }
    // Handle both JSON and form data
    if (isset($_POST['nombre'])) {
        $input = $_POST;
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
    }
    
    if (!$input) {
        throw new Exception('Datos de entrada inválidos');
    }
    

    // Validate required fields
    $required_fields = ['nombre', 'costo_unitario', 'precio_venta_sin_iva'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $producto = new Producto();
    $imagen_path = null;

    // Handle image upload if present
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        error_log("Processing file upload...");
        error_log("File info: " . print_r($_FILES['imagen'], true));
        
        try {
            $imagenController = new ImagenController();
            $upload_result = $imagenController->uploadProductImage($_FILES['imagen']);
            
            if ($upload_result['success']) {
                $imagen_path = $upload_result['path'];
                error_log("File uploaded successfully: " . $imagen_path);
            } else {
                $error_msg = 'Error al subir la imagen: ' . ($upload_result['message'] ?? 'Error desconocido');
                error_log($error_msg);
                throw new Exception($error_msg);
            }
        } catch (Exception $e) {
            error_log("Exception in image upload: " . $e->getMessage());
            throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
        }
    } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Log upload errors for debugging
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP (5MB)',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', ALLOWED_IMAGE_TYPES)
        ];
        
        $error_code = $_FILES['imagen']['error'];
        $error_msg = $error_messages[$error_code] ?? 'Error desconocido al subir archivo (Código: ' . $error_code . ')';
        error_log("File upload error: " . $error_msg);
        throw new Exception($error_msg);
    } else {
        error_log("No file uploaded or file upload not attempted");
    }

    // Prepare product data
    $producto_data = [
        'nombre' => sanitizeInput($input['nombre']),
        'referencia' => sanitizeInput($input['referencia'] ?? ''),
        'fabricante' => sanitizeInput($input['fabricante'] ?? ''),
        'modelo' => sanitizeInput($input['modelo'] ?? ''),
        'imagen' => $imagen_path,
        'categoria_id' => !empty($input['categoria_id']) ? intval($input['categoria_id']) : null,
        'codigo_barras' => sanitizeInput($input['codigo_barras'] ?? ''),
        'descripcion' => sanitizeInput($input['descripcion'] ?? ''),
        'costo_unitario' => floatval($input['costo_unitario']),
        'precio_venta_sin_iva' => floatval($input['precio_venta_sin_iva']),
        'impuesto_compra_id' => !empty($input['impuesto_compra_id']) ? intval($input['impuesto_compra_id']) : DEFAULT_IVA_ID,
        'impuesto_id' => !empty($input['impuesto_id']) ? intval($input['impuesto_id']) : DEFAULT_IVA_ID,
        'stock' => intval($input['stock'] ?? 0),
        'stock_minimo' => intval($input['stock_minimo'] ?? 5),
        'ubicacion' => sanitizeInput($input['ubicacion'] ?? '')
    ];

    $success = $producto->create($producto_data);
    
    if ($success) {
        $producto_id = $producto->getLastInsertId();
        
        // Update image with product ID if image was uploaded
        if ($imagen_path && $producto_id) {
            try {
                $imagenController = new ImagenController();
                $imagenController->uploadProductImage($_FILES['imagen'], $producto_id);
            } catch (Exception $e) {
                error_log('Error updating product image: ' . $e->getMessage());
                // Continue even if image update fails
            }
        }
        
        $response = [
            'success' => $success,
            'message' => $success ? 'Producto creado exitosamente' : 'Error al crear el producto',
            'producto_id' => $producto_id ?? null,
            'debug' => [
                'input' => $input,
                'producto_data' => $producto_data ?? null,
                'imagen_path' => $imagen_path ?? null
            ]
        ];
        
        error_log('API Response: ' . print_r($response, true));
        echo json_encode($response);
    } else {
        throw new Exception('Error al crear el producto en la base de datos');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el producto: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
    error_log('Error in create_producto.php: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
}
?>
