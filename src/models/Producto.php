<?php
/**
 * JETXCEL - Producto Model
 * Handles product database operations
 */

require_once __DIR__ . '/../../config/config.php';

class Producto
{
    private $conn;
    private $table_name = "productos";

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get all products with category and tax information
     */
    public function getAll($limit = null, $offset = null)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre, 
                         ic.nombre as impuesto_compra_nombre, ic.porcentaje as impuesto_compra_porcentaje,
                         iv.nombre as impuesto_venta_nombre, iv.porcentaje as impuesto_venta_porcentaje
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN impuestos ic ON p.impuesto_compra_id = ic.id
                  LEFT JOIN impuestos iv ON p.impuesto_id = iv.id
                  WHERE p.estado = 'activo'
                  ORDER BY p.nombre ASC";
        
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
     * Get product by ID
     */
    public function getById($id)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre, 
                         ic.nombre as impuesto_compra_nombre, ic.porcentaje as impuesto_compra_porcentaje,
                         iv.nombre as impuesto_venta_nombre, iv.porcentaje as impuesto_venta_porcentaje
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN impuestos ic ON p.impuesto_compra_id = ic.id
                  LEFT JOIN impuestos iv ON p.impuesto_id = iv.id
                  WHERE p.id = ? AND p.estado = 'activo'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Search products by name, reference or barcode
     */
    public function search($term, $category_id = null)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre, 
                         ic.nombre as impuesto_compra_nombre, ic.porcentaje as impuesto_compra_porcentaje,
                         iv.nombre as impuesto_venta_nombre, iv.porcentaje as impuesto_venta_porcentaje
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN impuestos ic ON p.impuesto_compra_id = ic.id
                  LEFT JOIN impuestos iv ON p.impuesto_id = iv.id
                  WHERE p.estado = 'activo' AND (
                      p.nombre LIKE ? OR 
                      p.referencia LIKE ? OR 
                      p.codigo_barras LIKE ?
                  )";
        
        $params = ["%$term%", "%$term%", "%$term%"];
        
        if ($category_id) {
            $query .= " AND p.categoria_id = ?";
            $params[] = $category_id;
        }
        
        $query .= " ORDER BY p.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all active product categories
     */
    public function getCategories()
    {
        $query = "SELECT DISTINCT c.id, c.nombre 
                  FROM categorias c
                  INNER JOIN " . $this->table_name . " p ON p.categoria_id = c.id
                  WHERE p.estado = 'activo'
                  ORDER BY c.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Count all active products
     */
    public function countAll()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE estado = 'activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null, $offset = null)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre, 
                         ic.nombre as impuesto_compra_nombre, ic.porcentaje as impuesto_compra_porcentaje,
                         iv.nombre as impuesto_venta_nombre, iv.porcentaje as impuesto_venta_porcentaje
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN impuestos ic ON p.impuesto_compra_id = ic.id
                  LEFT JOIN impuestos iv ON p.impuesto_id = iv.id
                  WHERE p.estado = 'activo' AND p.categoria_id = ?
                  ORDER BY p.nombre ASC";
        
        if ($limit) {
            $query .= " LIMIT " . intval($limit);
            if ($offset) {
                $query .= " OFFSET " . intval($offset);
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Create new product
     */
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, referencia, fabricante, modelo, imagen, categoria_id, codigo_barras, 
                   descripcion, costo_unitario, precio_venta_sin_iva, impuesto_compra_id, 
                   impuesto_id, stock, stock_minimo, ubicacion) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nombre'],
            $data['referencia'] ?? null,
            $data['fabricante'] ?? null,
            $data['modelo'] ?? null,
            $data['imagen'] ?? null,
            $data['categoria_id'] ?? null,
            $data['codigo_barras'] ?? null,
            $data['descripcion'] ?? null,
            $data['costo_unitario'],
            $data['precio_venta_sin_iva'],
            $data['impuesto_compra_id'] ?? DEFAULT_IVA_ID,
            $data['impuesto_id'] ?? DEFAULT_IVA_ID,
            $data['stock'] ?? 0,
            $data['stock_minimo'] ?? 5,
            $data['ubicacion'] ?? null
        ]);
    }

    /**
     * Update product
     */
    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table_name . " SET 
                  nombre = ?, referencia = ?, fabricante = ?, modelo = ?, imagen = ?, 
                  categoria_id = ?, codigo_barras = ?, descripcion = ?, costo_unitario = ?, 
                  precio_venta_sin_iva = ?, impuesto_compra_id = ?, impuesto_id = ?, 
                  stock = ?, stock_minimo = ?, ubicacion = ?, fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nombre'],
            $data['referencia'] ?? null,
            $data['fabricante'] ?? null,
            $data['modelo'] ?? null,
            $data['imagen'] ?? null,
            $data['categoria_id'] ?? null,
            $data['codigo_barras'] ?? null,
            $data['descripcion'] ?? null,
            $data['costo_unitario'],
            $data['precio_venta_sin_iva'],
            $data['impuesto_compra_id'] ?? DEFAULT_IVA_ID,
            $data['impuesto_id'] ?? DEFAULT_IVA_ID,
            $data['stock'] ?? 0,
            $data['stock_minimo'] ?? 5,
            $data['ubicacion'] ?? null,
            $id
        ]);
    }

    /**
     * Update product stock
     */
    public function updateStock($id, $quantity, $operation = 'add')
    {
        $operator = ($operation === 'add') ? '+' : '-';
        $query = "UPDATE " . $this->table_name . " SET stock = stock $operator ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $id]);
    }

    /**
     * Get products with low stock
     */
    public function getLowStock()
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.stock <= p.stock_minimo AND p.estado = 'activo'
                  ORDER BY p.stock ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get last inserted product ID
     */
    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
