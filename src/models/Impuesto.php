<?php
/**
 * JETXCEL - Impuesto Model
 * Handles tax database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Impuesto
{
    private $conn;
    private $table_name = "impuestos";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get all active taxes
     */
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE activo = 1 
                  ORDER BY porcentaje ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get tax by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get default IVA (19%)
     */
    public function getDefault()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([DEFAULT_IVA_ID]);
        return $stmt->fetch();
    }

    /**
     * Calculate tax amount
     */
    public function calculateTax($amount, $tax_percentage)
    {
        return ($amount * $tax_percentage) / 100;
    }

    /**
     * Calculate amount with tax
     */
    public function calculateWithTax($amount, $tax_percentage)
    {
        return $amount + $this->calculateTax($amount, $tax_percentage);
    }

    /**
     * Calculate amount without tax
     */
    public function calculateWithoutTax($amount_with_tax, $tax_percentage)
    {
        return $amount_with_tax / (1 + ($tax_percentage / 100));
    }
}
