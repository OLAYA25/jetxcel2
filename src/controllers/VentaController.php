<?php
/**
 * JETXCEL - Venta Controller
 * Handles sales operations
 */

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venta.php';

class VentaController
{
    private $productoModel;
    private $clienteModel;
    private $ventaModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->ventaModel = new Venta();
    }

    /**
     * Display sales page with products and sales form
     */
    public function index()
    {
        try {
            // Get products with pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;
            $offset = ($page - 1) * $perPage;

            $products = $this->productoModel->getAll($perPage, $offset);
            $totalProducts = $this->productoModel->countAll();
            $totalPages = ceil($totalProducts / $perPage);

            // Get recent sales
            $ventasRecientes = $this->ventaModel->getRecentSales(5);

            // Get categories for filter
            $categorias = $this->productoModel->getCategories();

            // Get clients for dropdown
            $clientes = $this->clienteModel->getAll();

            // Include the view
            include __DIR__ . '/../views/ventas.php';
        } catch (Exception $e) {
            // Log error and show message
            error_log('Error in VentaController::index: ' . $e->getMessage());
            $error = 'Error al cargar la página de ventas. Por favor intente nuevamente.';
            include __DIR__ . '/../views/error.php';
        }
    }

    /**
     * Search products via AJAX
     */
    public function searchProducts()
    {
        header('Content-Type: application/json');
        
        try {
            $searchTerm = $_GET['q'] ?? '';
            $categoryId = $_GET['category_id'] ?? null;
            
            $products = $this->productoModel->search($searchTerm, $categoryId);
            
            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process a new sale
     */
    public function processSale()
    {
        header('Content-Type: application/json');
        
        try {
            // Validate input
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['cliente_id'], $data['productos'], $data['pago'])) {
                throw new Exception('Datos de venta incompletos');
            }
            
            // Process the sale
            $ventaId = $this->ventaModel->create($data);
            
            echo json_encode([
                'success' => true,
                'venta_id' => $ventaId,
                'message' => 'Venta registrada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get sale details
     */
    public function getSaleDetails($id)
    {
        header('Content-Type: application/json');
        
        try {
            $sale = $this->ventaModel->getById($id);
            
            if (!$sale) {
                throw new Exception('Venta no encontrada');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $sale
            ]);
        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get recent sales for the dashboard
     */
    public function getRecentSales()
    {
        header('Content-Type: application/json');
        
        try {
            $limit = $_GET['limit'] ?? 5;
            $sales = $this->ventaModel->getRecentSales($limit);
            
            echo json_encode([
                'success' => true,
                'data' => $sales
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener ventas recientes: ' . $e->getMessage()
            ]);
        }
    }
}
