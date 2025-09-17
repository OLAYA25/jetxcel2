<?php
/**
 * JETXCEL - Venta Model
 * Handles sales database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Venta
{
    private $conn;
    private $table_cabecera = "ventas_cabecera";
    private $table_detalle = "ventas_detalle";
    private $table_productos = "productos";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Create a new sale
     */
    public function create($data)
    {
        $this->conn->beginTransaction();
        
        try {
            // Insert sale header
            $query = "INSERT INTO " . $this->table_cabecera . " 
                     (cliente_id, numero_factura, fecha_factura, subtotal, impuestos, 
                      descuento, total, valor_recibido, vueltos, medio_pago, usuario_id, descripcion, estado)
                     VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completada')";
            
            $stmt = $this->conn->prepare($query);
            
            // Generate invoice number
            $numeroFactura = 'FAC-' . date('Ymd') . '-' . str_pad($this->getNextInvoiceNumber(), 5, '0', STR_PAD_LEFT);
            
            // Calculate change
            $vueltos = $data['pago']['valor_recibido'] - $data['pago']['total'];
            
            // Get user ID from session
            $usuarioId = $_SESSION['usuario_id'] ?? 1; // Fallback to 1 if not set
            
            $stmt->execute([
                $data['cliente_id'],
                $numeroFactura,
                $data['subtotal'],
                $data['impuestos'],
                $data['descuento'] ?? 0,
                $data['total'],
                $data['pago']['valor_recibido'],
                $vueltos,
                $data['pago']['metodo'],
                $usuarioId,
                $data['descripcion'] ?? ''
            ]);
            
            $ventaId = $this->conn->lastInsertId();
            
            // Insert sale details
            foreach ($data['productos'] as $producto) {
                $this->addSaleDetail($ventaId, $producto);
                
                // Update stock
                $this->updateStock($producto['id'], -$producto['cantidad']);
            }
            
            $this->conn->commit();
            return $ventaId;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    /**
     * Add sale detail
     */
    private function addSaleDetail($ventaId, $producto)
    {
        $query = "INSERT INTO " . $this->table_detalle . " 
                 (venta_id, producto_id, cantidad, precio_unitario_sin_iva, 
                  impuesto_id, porcentaje_impuesto, precio_unitario_con_iva)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $ventaId,
            $producto['id'],
            $producto['cantidad'],
            $producto['precio_unitario_sin_iva'],
            $producto['impuesto_id'],
            $producto['porcentaje_impuesto'],
            $producto['precio_unitario_con_iva']
        ]);
    }
    
    /**
     * Update product stock
     */
    private function updateStock($productoId, $cantidad)
    {
        $query = "UPDATE " . $this->table_productos . " 
                 SET stock = stock + ? 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cantidad, $productoId]);
    }
    
    /**
     * Get next invoice number
     */
    private function getNextInvoiceNumber()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_cabecera . " 
                 WHERE DATE(fecha_factura) = CURDATE()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'] + 1;
    }
    
    /**
     * Get recent sales
     */
    public function getRecentSales($limit = 5)
    {
        // Ensure limit is an integer
        $limit = (int)$limit;
        
        $query = "SELECT vc.id, vc.numero_factura, vc.fecha_factura, 
                         vc.total, vc.medio_pago, vc.estado,
                         c.nombre as cliente_nombre
                  FROM " . $this->table_cabecera . " vc
                  LEFT JOIN clientes c ON vc.cliente_id = c.id
                  ORDER BY vc.fecha_factura DESC, vc.id DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get sale by ID with details
     */
    public function getById($id)
    {
        // Get sale header
        $query = "SELECT vc.*, c.nombre as cliente_nombre, c.nit as cliente_nit, 
                         c.direccion as cliente_direccion, c.ciudad as cliente_ciudad,
                         u.nombre as vendedor_nombre
                  FROM " . $this->table_cabecera . " vc
                  JOIN clientes c ON vc.cliente_id = c.id
                  JOIN usuarios u ON vc.usuario_id = u.id
                  WHERE vc.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $venta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$venta) {
            return null;
        }
        
        // Get sale details
        $query = "SELECT vd.*, p.nombre as producto_nombre, p.referencia as producto_referencia,
                         p.codigo_barras as producto_codigo_barras
                  FROM " . $this->table_detalle . " vd
                  JOIN productos p ON vd.producto_id = p.id
                  WHERE vd.venta_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $venta['detalles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $venta;
    }
    
    /**
     * Get sales report by date range
     */
    public function getSalesReport($fechaInicio, $fechaFin, $usuarioId = null)
    {
        $query = "SELECT vc.*, c.nombre as cliente_nombre, u.nombre as vendedor_nombre
                  FROM " . $this->table_cabecera . " vc
                  JOIN clientes c ON vc.cliente_id = c.id
                  JOIN usuarios u ON vc.usuario_id = u.id
                  WHERE vc.fecha_venta BETWEEN ? AND ?";
        
        $params = [$fechaInicio, $fechaFin];
        
        if ($usuarioId) {
            $query .= " AND vc.usuario_id = ?";
            $params[] = $usuarioId;
        }
        
        $query .= " ORDER BY vc.fecha_venta DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
