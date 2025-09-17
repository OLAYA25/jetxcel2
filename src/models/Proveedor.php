<?php
/**
 * JETXCEL - Proveedor Model
 * Handles supplier database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Proveedor
{
    private $conn;
    private $table_name = "proveedores";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get all active suppliers
     */
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE estado = 'activo' 
                  ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get supplier by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND estado = 'activo'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Search suppliers by name or NIT
     */
    public function search($term)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE estado = 'activo' AND (nombre LIKE ? OR nit LIKE ?)
                  ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["%$term%", "%$term%"]);
        return $stmt->fetchAll();
    }

    /**
     * Create new supplier (without RUT field)
     */
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, telefono, nit, direccion, ciudad, email, descripcion) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nombre'],
            $data['telefono'] ?? null,
            $data['nit'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['email'] ?? null,
            $data['descripcion'] ?? null
        ]);
    }

    /**
     * Update supplier
     */
    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table_name . " SET 
                  nombre = ?, telefono = ?, nit = ?, direccion = ?, 
                  ciudad = ?, email = ?, descripcion = ?, 
                  fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nombre'],
            $data['telefono'] ?? null,
            $data['nit'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['email'] ?? null,
            $data['descripcion'] ?? null,
            $id
        ]);
    }

    /**
     * Deactivate supplier
     */
    public function deactivate($id)
    {
        $query = "UPDATE " . $this->table_name . " SET estado = 'inactivo' WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Get last inserted supplier ID
     */
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
