<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $clients]); // Estructura corregida
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener clientes: ' . $e->getMessage()]);
}
?>
