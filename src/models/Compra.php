<?php
/**
 * JETXCEL - Compra Model
 * Handles purchase database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Compra
{
    private $conn;
    private $table_cabecera = "compras_cabecera";
    private $table_detalle = "compras_detalle";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get all purchases with supplier information
     */
    public function getAll($limit = null, $offset = null)
    {
        $query = "SELECT cc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                  FROM " . $this->table_cabecera . " cc
                  LEFT JOIN proveedores p ON cc.proveedor_id = p.id
                  LEFT JOIN usuarios u ON cc.usuario_id = u.id
                  ORDER BY cc.fecha_compra DESC";
        
        if ($limit) {
            $query .= " LIMIT " . intval($limit);
            if ($offset) {
                $query .= " OFFSET " . intval($offset);
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get purchase by ID with details
     */
    public function getById($id)
    {
        // Get purchase header
        $query = "SELECT cc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                  FROM " . $this->table_cabecera . " cc
                  LEFT JOIN proveedores p ON cc.proveedor_id = p.id
                  LEFT JOIN usuarios u ON cc.usuario_id = u.id
                  WHERE cc.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $purchase = $stmt->fetch();

        if ($purchase) {
            // Get purchase details
            $query_detail = "SELECT cd.*, pr.nombre as producto_nombre, i.nombre as impuesto_nombre
                            FROM " . $this->table_detalle . " cd
                            LEFT JOIN productos pr ON cd.producto_id = pr.id
                            LEFT JOIN impuestos i ON cd.impuesto_id = i.id
                            WHERE cd.compra_id = ?";

            $stmt_detail = $this->conn->prepare($query_detail);
            $stmt_detail->execute([$id]);
            $purchase['detalles'] = $stmt_detail->fetchAll();
        }

        return $purchase;
    }

    /**
     * Create new purchase with details
     */
    public function create($data, $detalles)
    {
        try {
            $this->conn->beginTransaction();

            // Insert purchase header
            $query = "INSERT INTO " . $this->table_cabecera . " 
                      (proveedor_id, numero_factura, fecha_factura, subtotal, impuestos, 
                       descuento, total, medio_pago, usuario_id, descripcion) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['proveedor_id'],
                $data['numero_factura'] ?? null,
                $data['fecha_factura'] ?? date('Y-m-d'),
                $data['subtotal'],
                $data['impuestos'],
                $data['descuento'] ?? 0,
                $data['total'],
                $data['medio_pago'],
                $data['usuario_id'] ?? 1, // Default user if not provided
                $data['descripcion'] ?? null
            ]);

            $compra_id = $this->conn->lastInsertId();

            // Insert purchase details
            $query_detail = "INSERT INTO " . $this->table_detalle . " 
                            (compra_id, producto_id, cantidad, costo_unitario, impuesto_id, 
                             porcentaje_impuesto, precio_venta_sugerido) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt_detail = $this->conn->prepare($query_detail);

            foreach ($detalles as $detalle) {
                $stmt_detail->execute([
                    $compra_id,
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['costo_unitario'],
                    $detalle['impuesto_id'] ?? DEFAULT_IVA_ID,
                    $detalle['porcentaje_impuesto'] ?? DEFAULT_IVA_PERCENTAGE,
                    $detalle['precio_venta_sugerido']
                ]);

                // Update product stock
                $this->updateProductStock($detalle['producto_id'], $detalle['cantidad'], 'add');
                
                // Update product cost and sale price
                $this->updateProductPrices($detalle['producto_id'], $detalle['costo_unitario'], $detalle['precio_venta_sugerido']);
            }

            $this->conn->commit();
            return $compra_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Purchase creation error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update product stock
     */
    private function updateProductStock($producto_id, $cantidad, $operation = 'add')
    {
        $operator = ($operation === 'add') ? '+' : '-';
        $query = "UPDATE productos SET stock = stock $operator ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cantidad, $producto_id]);
    }

    /**
     * Update product prices
     */
    private function updateProductPrices($producto_id, $costo_unitario, $precio_venta)
    {
        $query = "UPDATE productos SET 
                  costo_unitario = ?, 
                  precio_venta_sin_iva = ?,
                  fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$costo_unitario, $precio_venta, $producto_id]);
    }

    /**
     * Get recent purchases
     */
    public function getRecent($limit = 10)
    {
        $query = "SELECT cc.*, p.nombre as proveedor_nombre
                  FROM " . $this->table_cabecera . " cc
                  LEFT JOIN proveedores p ON cc.proveedor_id = p.id
                  ORDER BY cc.fecha_compra DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get purchases by date range
     */
    public function getByDateRange($fecha_inicio, $fecha_fin)
    {
        $query = "SELECT cc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                  FROM " . $this->table_cabecera . " cc
                  LEFT JOIN proveedores p ON cc.proveedor_id = p.id
                  LEFT JOIN usuarios u ON cc.usuario_id = u.id
                  WHERE DATE(cc.fecha_compra) BETWEEN ? AND ?
                  ORDER BY cc.fecha_compra DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll();
    }

    /**
     * Get purchases by supplier
     */
    public function getBySupplier($proveedor_id)
    {
        $query = "SELECT cc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
                  FROM " . $this->table_cabecera . " cc
                  LEFT JOIN proveedores p ON cc.proveedor_id = p.id
                  LEFT JOIN usuarios u ON cc.usuario_id = u.id
                  WHERE cc.proveedor_id = ?
                  ORDER BY cc.fecha_compra DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$proveedor_id]);
        return $stmt->fetchAll();
    }
}
