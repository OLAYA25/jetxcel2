<?php
/**
 * JETXCEL - Cliente Model
 * Handles client database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Cliente
{
    private $conn;
    private $table_name = "clientes";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get all active clients
     */
    public function getAll()
    {
        $query = "SELECT id, nombre, nit, telefono, email, ciudad 
                 FROM " . $this->table_name . " 
                 WHERE estado = 'activo'
                 ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get client by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Search clients by name, NIT, or email
     */
    public function search($term)
    {
        $query = "SELECT id, nombre, nit, telefono, email, ciudad 
                 FROM " . $this->table_name . " 
                 WHERE (nombre LIKE ? OR nit LIKE ? OR email LIKE ?) 
                 AND estado = 'activo'
                 ORDER BY nombre ASC";
        
        $searchTerm = "%$term%";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new client
     */
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, nit, telefono, direccion, ciudad, email, descripcion, rut_archivo, estado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['nombre'],
            $data['nit'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['email'] ?? null,
            $data['descripcion'] ?? null,
            $data['rut_archivo'] ?? null
        ]);
    }

    /**
     * Update an existing client
     */
    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table_name . " SET 
                 nombre = ?, nit = ?, telefono = ?, direccion = ?, 
                 ciudad = ?, email = ?, descripcion = ?, rut_archivo = ?
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['nombre'],
            $data['nit'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['email'] ?? null,
            $data['descripcion'] ?? null,
            $data['rut_archivo'] ?? null,
            $id
        ]);
    }

    /**
     * Delete (deactivate) a client
     */
    public function delete($id)
    {
        $query = "UPDATE " . $this->table_name . " SET estado = 'inactivo' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Get client purchase history
     */
    public function getPurchaseHistory($clienteId)
    {
        $query = "SELECT vc.id, vc.numero_factura, vc.fecha_venta, vc.total, 
                         COUNT(vd.id) as total_productos
                  FROM ventas_cabecera vc
                  JOIN ventas_detalle vd ON vc.id = vd.venta_id
                  WHERE vc.cliente_id = ?
                  GROUP BY vc.id
                  ORDER BY vc.fecha_venta DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
