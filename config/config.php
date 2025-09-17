<?php
/**
 * JETXCEL - Application Configuration
 * Global configuration settings for the application
 */

// Database configuration
require_once __DIR__ . '/database.php';

// Application settings
define('APP_NAME', 'JETXCEL S.A.S');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// File upload settings
define('UPLOAD_PATH', realpath(__DIR__ . '/../public/uploads/') . '/');
define('PRODUCT_IMAGES_PATH', realpath(__DIR__ . '/../public/uploads/productos/') . '/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Default IVA settings
define('DEFAULT_IVA_PERCENTAGE', 19.00);
define('DEFAULT_IVA_ID', 2); // IVA 19% from impuestos table

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('America/Bogota');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function formatCurrency($amount) {
    return '$' . number_format($amount, 0, ',', '.');
}

function formatDate($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

?>
