<?php
require '../config/db.php';

header('Content-Type: application/json');

$data = $_POST;

try {
    $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, nit, direccion, ciudad, email, descripcion) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['clientName'],
        $data['clientPhone'],
        $data['clientNIT'],
        $data['clientAddress'],
        $data['clientCity'],
        $data['clientEmail'],
        $data['clientDescription']
    ]);
    
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar cliente: ' . $e->getMessage()]);
}
?>
