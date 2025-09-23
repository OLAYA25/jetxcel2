<?php
// Incluir archivos necesarios
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venta.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User authentication check removed as per user request

// Obtener parámetros de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = isset($_GET['por_pagina']) ? (int)$_GET['por_pagina'] : 12;
$categoriaId = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : null;
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Inicializar modelos
$productoModel = new Producto();
$clienteModel = new Cliente();
$ventaModel = new Venta();

// Obtener productos con filtros
$productos = [];
$totalProductos = 0;

if (!empty($busqueda)) {
    // Búsqueda de productos
    $productos = $productoModel->search($busqueda, $categoriaId);
    $totalProductos = count($productos);
} else if ($categoriaId) {
    // Filtrar por categoría
    $productos = $productoModel->getByCategory($categoriaId);
    $totalProductos = count($productos);
} else {
    // Paginación normal
    $inicio = ($pagina - 1) * $porPagina;
    $productos = $productoModel->getAll($porPagina, $inicio);
    $totalProductos = $productoModel->countAll();
}

$totalPaginas = ceil($totalProductos / $porPagina);

// Obtener categorías para el filtro
$categorias = $productoModel->getCategories();

// Obtener clientes para el select
$clientes = $clienteModel->getAll();

// Obtener ventas recientes
$ventasRecientes = $ventaModel->getRecentSales(5);
?>

<?php include '../includes/partials/header.php'; ?>

<!-- Contenido de ventas -->
<div class="container-fluid mt-4">
    <div class="sale-container">
        <div class="sale-content">
            <div class="product-header">
                <h4 class="services-title">VENTAS DE PRODUCTOS</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form id="searchForm" class="d-flex gap-2 flex-wrap" style="width: 100%; max-width: 800px;">
                        <input type="text" class="form-control" id="productSearch" name="busqueda" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>" style="width: 250px;">
                        <select class="form-select category-filter" id="categoryFilter" name="categoria_id" style="width: 200px;">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo ($categoriaId == $categoria['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <select class="form-select products-per-page" id="productsPerPage" name="por_pagina" style="width: 150px;">
                            <option value="6" <?php echo ($porPagina == 6) ? 'selected' : ''; ?>>6 por página</option>
                            <option value="12" <?php echo ($porPagina == 12) ? 'selected' : ''; ?>>12 por página</option>
                            <option value="24" <?php echo ($porPagina == 24) ? 'selected' : ''; ?>>24 por página</option>
                            <option value="36" <?php echo ($porPagina == 36) ? 'selected' : ''; ?>>36 por página</option>
                        </select>
                    </form>
                </div>
            </div>

            <div id="mensajeError" class="alert alert-danger" style="display:none;"></div>
            <div id="mensajeSuccess" class="alert alert-success" style="display:none;"></div>

            <div class="product-grid" id="productsGrid">
                <?php if (empty($productos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">No se encontraron productos.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($productos as $producto):
                        // Cambia la ruta base para las imágenes de productos
                        $imagen = !empty($producto['imagen']) ?
                            '/jetxcel2/public/uploads/productos/' . basename($producto['imagen']) :
                            'assets/images/placeholder-product.png';
                        $claseStock = $producto['stock'] <= $producto['stock_minimo'] ? 'bg-warning' : 'bg-success';
                        $textoStock = $producto['stock'] <= $producto['stock_minimo'] ? 'Poco stock' : 'En stock';
                    ?>
                        <div class="product-card" data-producto-id="<?php echo $producto['id']; ?>">
                            <div class="product-image">
                                <img src="<?php echo $imagen; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <span class="badge <?php echo $claseStock; ?> product-badge"><?php echo $textoStock; ?></span>
                            </div>
                            <div class="product-info">
                                <h6 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h6>
                                <?php if (!empty($producto['modelo'])): ?>
                                    <p class="card-details">Modelo: <?php echo htmlspecialchars($producto['modelo']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($producto['referencia'])): ?>
                                    <p class="card-details">Ref: <?php echo htmlspecialchars($producto['referencia']); ?></p>
                                <?php endif; ?>
                                <p class="card-price">$<?php echo number_format($producto['precio_venta_sin_iva'], 2); ?></p>
                                <p class="card-stock">Disponible: <?php echo $producto['stock']; ?> unidades</p>
                            </div>
                            <button class="btn btn-sm btn-outline-primary add-to-cart-btn"
                                data-producto-id="<?php echo $producto['id']; ?>"
                                data-producto-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                data-producto-precio="<?php echo $producto['precio_venta_sin_iva']; ?>"
                                data-producto-stock="<?php echo $producto['stock']; ?>"
                                data-producto-impuesto="<?php echo $producto['impuesto_venta_porcentaje']; ?>"
                                data-producto-impuesto-id="<?php echo $producto['impuesto_id']; ?>">
                                <i class="bi bi-cart-plus"></i> Agregar
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1 && empty($busqueda) && !$categoriaId): ?>
                <div class="pagination-container mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&por_pagina=<?php echo $porPagina; ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?>&por_pagina=<?php echo $porPagina; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&por_pagina=<?php echo $porPagina; ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

            <div class="sale-history mt-4">
                <div class="sale-history-title">Historial de Ventas Recientes</div>
                <div id="recentSales">
                    <?php
                    // Depuración temporal
                    echo '<!-- ';
                    var_dump($ventasRecientes);
                    echo ' -->';

                    if (empty($ventasRecientes)): ?>
                        <div class="alert alert-info">No hay ventas recientes para mostrar.</div>
                    <?php else: ?>
                        <?php foreach ($ventasRecientes as $venta):
                            $fechaVenta = new DateTime($venta['fecha_factura']);
                        ?>
                            <div class="sale-item">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($venta['numero_factura']); ?></strong>
                                    <span class="text-success">$<?php echo number_format($venta['total'], 2); ?></span>
                                </div>
                                <div class="text-muted">
                                    Cliente: <?php echo htmlspecialchars($venta['cliente_nombre']); ?> •
                                    <?php echo $fechaVenta->format('d M Y, H:i'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- Cierre correcto de sale-content -->

        <div class="sale-form-container">
            <div class="form-section">
                <div class="form-section-title">Productos Seleccionados</div>
                <div id="selectedProducts">
                    <div class="alert alert-info">Seleccione productos para la venta</div>
                </div>

                <!-- Plantilla para productos seleccionados (oculta) -->
                <div id="selectedProductTemplate" class="d-none">
                    <div class="selected-product" data-product-id="">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 product-name"></h6>
                            <button type="button" class="btn-close remove-product" aria-label="Eliminar"></button>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Cantidad</label>
                                <input type="number" class="form-control form-control-sm product-quantity" min="1" value="1">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Precio Unit.</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm product-price" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">Stock: <span class="stock-amount"></span></small>
                            <strong>Subtotal: $<span class="product-subtotal">0.00</span></strong>
                        </div>
                        <input type="hidden" class="product-tax-rate" value="0">
                        <input type="hidden" class="product-tax-id" value="0">
                        <hr class="my-2">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Resumen de Venta</div>
                <div class="calculation-row">
                    <span>Subtotal:</span>
                    <span id="subtotalAmount">$0.00</span>
                </div>

                <div class="calculation-row">
                    <span>IVA (19%):</span>
                    <span id="taxAmount">$0.00</span>
                </div>

                <div class="discount-container">
                    <div class="discount-input">
                        <label class="form-label">Descuento ($)</label>
                        <input type="number" class="form-control" id="discountInput" placeholder="Ingrese descuento" min="0" step="0.01" value="0">
                    </div>
                    <div class="apply-discount-btn">
                        <label class="form-label" style="visibility: hidden;">Aplicar</label>
                        <button type="button" class="btn btn-outline-primary w-100" id="applyDiscountBtn">Aplicar</button>
                    </div>
                </div>

                <div class="calculation-row">
                    <span>Descuento aplicado:</span>
                    <span id="discountAmount">$0.00</span>
                </div>

                <div class="calculation-row total">
                    <span>Total a Pagar:</span>
                    <span id="totalAmount">$0.00</span>
                </div>

                <input type="hidden" id="totalSinDescuento" value="0">
                <input type="hidden" id="totalConIva" value="0">
            </div>

            <div class="form-section">
                <div class="form-section-title">Información del Cliente</div>
                <div class="mb-3">
                    <label class="form-label">Cliente
                        <span class="info-tooltip" data-bs-toggle="tooltip" title="Seleccione un cliente para ver sus detalles">
                            <i class="bi bi-info-circle"></i>
                        </span>
                    </label>
                    <div class="input-group">
                        <select class="form-select" id="clientSelect" style="width: 100%">
                            <option value="">Seleccionar cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>">
                                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                                    <?php echo !empty($cliente['nit']) ? ' - ' . htmlspecialchars($cliente['nit']) : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-primary" type="button" id="newClientBtn" data-bs-toggle="modal" data-bs-target="#newClientModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <button class="btn btn-outline-info w-100 client-info-btn mt-2" id="viewClientInfo" disabled>
                        <i class="bi bi-person-badge"></i> Ver información del cliente
                    </button>
                </div>

                <div id="clientInfo" class="d-none">
                    <div class="card bg-light p-2 mt-2">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1"><strong id="clientName"></strong></h6>
                            <small class="text-muted" id="clientNit"></small>
                        </div>
                        <div class="text-muted small" id="clientContact"></div>
                        <div class="text-muted small" id="clientLocation"></div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Pago</div>
                <div class="mb-3">
                    <label class="form-label">Medio de Pago <span class="text-danger">*</span></label>
                    <select class="form-select" id="paymentMethod" required>
                        <option value="">Seleccionar medio de pago</option>
                        <option value="Davivienda Marlon">Davivienda Marlon</option>
                        <option value="Daviplata Edwin">Daviplata Edwin</option>
                        <option value="Nequi Marlon">Nequi Marlon</option>
                        <option value="Efectivo" selected>Efectivo</option>
                        <option value="Cuenta por pagar">Cuenta por pagar</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valor Recibido ($) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="amountReceived"
                            placeholder="Ingrese el valor recibido" min="0" step="0.01" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cambio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="changeAmount" value="0.00" readonly>
                    </div>
                </div>

                <div class="change-container">
                    <div class="change-label">Cambio:</div>
                    <div class="change-amount" id="changeAmount">$0.00</div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Observaciones</div>
                <textarea class="form-control" id="saleNotes" rows="2" placeholder="Notas adicionales sobre la venta..."></textarea>
            </div>

            <div class="action-buttons">
                <button class="btn btn-light flex-grow-1" id="cancelSaleBtn">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button class="btn btn-primary flex-grow-1" id="completeSaleBtn">
                    <i class="bi bi-check-circle"></i> Completar Venta
                </button>
            </div>
        </div>
    </div> <!-- Cierre de sale-form-container -->
</div> <!-- Cierre de sale-container -->
</div> <!-- Cierre de container-fluid -->
</div> <!-- Cierre de main-content -->

<!-- Modal para detalles del producto -->
<div class="modal fade product-details-modal" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="productDetailsModalLabel">Detalles del Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="productDetailImage" src="" class="img-fluid rounded mb-3" alt="Imagen del producto">
                    </div>
                    <div class="col-md-6">
                        <h4 id="productDetailName" class="mb-3"></h4>

                        <div class="mb-3">
                            <h5 class="text-primary" id="productDetailPrice"></h5>
                            <span id="productDetailStock" class="badge"></span>
                        </div>

                        <div class="mb-3">
                            <h6>Información del Producto</h6>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Referencia:</th>
                                        <td id="productDetailReference"></td>
                                    </tr>
                                    <tr>
                                        <th>Modelo:</th>
                                        <td id="productDetailModel"></td>
                                    </tr>
                                    <tr>
                                        <th>Categoría:</th>
                                        <td id="productDetailCategory"></td>
                                    </tr>
                                    <tr>
                                        <th>Fabricante:</th>
                                        <td id="productDetailManufacturer"></td>
                                    </tr>
                                    <tr>
                                        <th>Código de Barras:</th>
                                        <td id="productDetailBarcode"></td>
                                    </tr>
                                    <tr>
                                        <th>Ubicación:</th>
                                        <td id="productDetailLocation"></td>
                                    </tr>
                                    <tr>
                                        <th>IVA Venta:</th>
                                        <td id="productDetailTax"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <h6>Descripción</h6>
                            <p id="productDetailDescription" class="text-muted"></p>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1" id="addToCartFromModal">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i> Cerrar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nombre:</div>
                    <div class="detail-value">Laptop HP Pavilion 15</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Modelo:</div>
                    <div class="detail-value">15-dw1005la</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Referencia:</div>
                    <div class="detail-value">HP-PAV-15-2023</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Fabricante:</div>
                    <div class="detail-value">HP Inc.</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Categoría:</div>
                    <div class="detail-value">Computadores</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Procesador:</div>
                    <div class="detail-value">Intel Core i5-1135G7</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Memoria RAM:</div>
                    <div class="detail-value">8GB DDR4</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Almacenamiento:</div>
                    <div class="detail-value">512GB SSD</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Pantalla:</div>
                    <div class="detail-value">15.6" Full HD</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Sistema operativo:</div>
                    <div class="detail-value">Windows 11 Home</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Garantía:</div>
                    <div class="detail-value">12 meses</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Peso:</div>
                    <div class="detail-value">1.75 kg</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Dimensiones:</div>
                    <div class="detail-value">36 x 23.4 x 1.8 cm</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Color:</div>
                    <div class="detail-value">Plateado</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Costo unitario:</div>
                    <div class="detail-value">$720.00</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Precio de venta:</div>
                    <div class="detail-value">$850.00</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Stock disponible:</div>
                    <div class="detail-value">12 unidades</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Ubicación en bodega:</div>
                    <div class="detail-value">Estante A-12</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Proveedor:</div>
                    <div class="detail-value">TecnoImport S.A.S</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Fecha de ingreso:</div>
                    <div class="detail-value">15/10/2023</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="addToSaleFromModal">
                    <i class="bi bi-cart-plus"></i> Agregar a la venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nuevo cliente -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newClientModalLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newClientForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="clientNameInput" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="clientNameInput" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clientNitInput" class="form-label">NIT/Cédula</label>
                                <input type="text" class="form-control" id="clientNitInput">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clientPhoneInput" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="clientPhoneInput">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="clientEmailInput" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="clientEmailInput">
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="clientAddressInput" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="clientAddressInput">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="clientCityInput" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="clientCityInput">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="clientDescriptionInput" class="form-label">Notas</label>
                        <textarea class="form-control" id="clientDescriptionInput" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para detalles del cliente -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clientDetailsModalLabel">Información del Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="client-avatar bg-primary bg-opacity-10 p-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-person-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 id="clientModalName" class="mt-3 mb-1"></h5>
                    <p class="text-muted mb-3" id="clientModalNit"></p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-outline-primary" id="editClientBtn">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="printClientBtn">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="client-details">
                    <div class="detail-item d-flex align-items-center mb-2">
                        <div class="icon-circle bg-light p-2 me-2">
                            <i class="bi bi-telephone text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Teléfono</div>
                            <div id="clientModalPhone"></div>
                        </div>
                    </div>

                    <div class="detail-item d-flex align-items-center mb-2">
                        <div class="icon-circle bg-light p-2 me-2">
                            <i class="bi bi-envelope text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Correo Electrónico</div>
                            <div id="clientModalEmail"></div>
                        </div>
                    </div>

                    <div class="detail-item d-flex align-items-start mb-2">
                        <div class="icon-circle bg-light p-2 me-2 mt-1">
                            <i class="bi bi-geo-alt text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Dirección</div>
                            <div id="clientModalAddress"></div>
                            <div id="clientModalCity"></div>
                        </div>
                    </div>

                    <div class="detail-item d-flex align-items-start">
                        <div class="icon-circle bg-light p-2 me-2 mt-1">
                            <i class="bi bi-card-text text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Notas</div>
                            <div id="clientModalNotes" class="text-muted"></div>
                        </div>
                    </div>
                </div>

                <div class="client-stats mt-4 pt-3 border-top">
                    <h6 class="mb-3">Historial de Compras</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="clientPurchaseHistory">
                                <!-- Las compras se cargarán aquí por AJAX -->
                                <tr>
                                    <td colspan="4" class="text-center">Cargando historial...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="2">Total Gastado:</th>
                                    <th class="text-end text-success" id="clientTotalSpent">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/partials/footer.php'; ?>