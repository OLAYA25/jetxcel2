<?php include '../includes/partials/header.php'; ?>

<!-- Contenido de compras -->
<div class="container-fluid mt-4">
    <div class="purchase-container">
        <div class="purchase-content">
            <div class="product-header">
                <h4 class="services-title">COMPRAS DE PRODUCTOS</h4>
                <button class="btn btn-primary" id="nuevaCompraBtn">
                    <i class="bi bi-plus-lg"></i> Nueva Compra
                </button>
            </div>

            <div id="mensajeError" class="alert alert-danger" style="display:none;"></div>
            <div id="mensajeSuccess" class="alert alert-success" style="display:none;"></div>

            <!-- Formulario de Compra -->
            <div class="card mt-3">
                <div class="card-body">
                    <form id="compraForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Producto</label>
                                <div class="input-group">
                                    <select class="form-select" id="productoSelect" name="producto_id" required>
                                        <option value="">Seleccionar producto</option>
                                        <!-- Opciones dinámicas desde DB -->
                                        <option value="1">Laptop HP Pavilion 15</option>
                                        <option value="2">Teclado Mecánico RGB</option>
                                        <option value="3">Monitor 24" Samsung</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="btnNuevoProducto" data-bs-toggle="modal" data-bs-target="#productoModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código de Barras</label>
                                <input type="text" class="form-control" name="codigo_barras" placeholder="Escanear código de barras">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" name="cantidad" required min="1" value="1">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Costo Unitario ($)</label>
                                <input type="number" class="form-control" name="costo_unitario" step="0.01" required value="0.00">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">IVA Compra</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ivaCompraCheck" checked>
                                    <label class="form-check-label" for="ivaCompraCheck">19%</label>
                                </div>
                                <input type="number" class="form-control mt-1" id="ivaCompraManual" step="0.01" value="19.00" style="display:none;">
                                <select class="form-select mt-1" name="impuesto_compra_id" id="impuestoCompraSelect">
                                    <option value="2" selected>IVA 19%</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Precio Venta ($)</label>
                                <input type="number" class="form-control" name="precio_venta" step="0.01" required value="0.00">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Total ($)</label>
                                <input type="text" class="form-control" id="totalCompra" readonly value="$0.00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Proveedor</label>
                                <div class="input-group">
                                    <select class="form-select" id="proveedorSelect" name="proveedor_id" required>
                                        <option value="">Seleccionar proveedor</option>
                                        <!-- Opciones dinámicas desde DB -->
                                        <option value="1">TecnoImport S.A.S</option>
                                        <option value="2">Componentes Colombia</option>
                                        <option value="3">Tecnología Global</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="btnNuevoProveedor" data-bs-toggle="modal" data-bs-target="#proveedorModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Medio de Pago</label>
                                <select class="form-select" name="medio_pago" required>
                                    <option value="">Seleccionar</option>
                                    <option value="Davivienda Marlon">Davivienda Marlon</option>
                                    <option value="Daviplata Edwin">Daviplata Edwin</option>
                                    <option value="Nequi Marlon">Nequi Marlon</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Cuenta por pagar">Cuenta por pagar</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="2" placeholder="Notas adicionales sobre la compra..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Registrar Compra
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="purchase-history">
                <div class="purchase-history-title">Historial de Compras Recientes</div>
                <div id="recentPurchases">
                    <!-- Compras recientes de ejemplo -->
                    <div class="purchase-item">
                        <div class="d-flex justify-content-between">
                            <strong>Compra #00125</strong>
                            <span class="text-success">$1,425.50</span>
                        </div>
                        <div class="text-muted">Proveedor: TecnoImport S.A.S • 12 Nov 2023, 14:30</div>
                    </div>
                    
                    <div class="purchase-item">
                        <div class="d-flex justify-content-between">
                            <strong>Compra #00124</strong>
                            <span class="text-success">$2,250.00</span>
                        </div>
                        <div class="text-muted">Proveedor: Componentes Colombia • 11 Nov 2023, 11:15</div>
                    </div>
                </div>
            </div>
        </div> <!-- Cierre de purchase-content -->

        <div class="purchase-form-container">
            <div class="form-section">
                <div class="form-section-title">Resumen de Compra</div>
                <div class="calculation-row">
                    <span>Subtotal:</span>
                    <span id="subtotalAmount">$0.00</span>
                </div>
                
                <div class="calculation-row">
                    <span>IVA (19%):</span>
                    <span id="ivaAmount">$0.00</span>
                </div>
                
                <div class="calculation-row total">
                    <span>Total a Pagar:</span>
                    <span id="totalAmount">$0.00</span>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">Información del Proveedor</div>
                <div class="mb-3">
                    <label class="form-label">Proveedor
                        <span class="info-tooltip" data-bs-toggle="tooltip" title="Seleccione un proveedor para ver sus detalles">
                            <i class="bi bi-info-circle"></i>
                        </span>
                    </label>
                    <div class="input-group">
                        <select class="form-select" id="supplierSelect" style="width: 100%">
                            <option value="">Seleccionar proveedor</option>
                            <option value="1">TecnoImport S.A.S</option>
                            <option value="2">Componentes Colombia</option>
                            <option value="3">Tecnología Global</option>
                        </select>
                        <button class="btn btn-outline-primary" type="button" id="newSupplierBtn" data-bs-toggle="modal" data-bs-target="#proveedorModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <button class="btn btn-outline-info w-100 supplier-info-btn" id="viewSupplierInfo">
                        <i class="bi bi-building"></i> Ver información del proveedor
                    </button>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">Documentos</div>
                <div class="mb-3">
                    <label class="form-label">Factura de Compra</label>
                    <input type="file" class="form-control" id="facturaInput">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" class="form-control" id="facturaNumber" placeholder="Número de factura">
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-light flex-grow-1" id="cancelPurchaseBtn">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button class="btn btn-primary flex-grow-1" id="completePurchaseBtn">
                    <i class="bi bi-check-circle"></i> Confirmar Compra
                </button>
            </div>
        </div> <!-- Cierre de purchase-form-container -->
    </div> <!-- Cierre de purchase-container -->
</div> <!-- Cierre de container-fluid -->

<!-- Modal para Nuevo Producto -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-labelledby="productoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productoModalLabel">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productoForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="1">Computadoras</option>
                                <option value="2">Componentes</option>
                                <option value="3">Periféricos</option>
                                <option value="4">Software</option>
                                <option value="5">Accesorios</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Referencia</label>
                            <input type="text" class="form-control" name="referencia">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fabricante</label>
                            <input type="text" class="form-control" name="fabricante">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" class="form-control" name="codigo_barras">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control" name="imagen" accept="image/*">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Costo Unitario ($)</label>
                            <input type="number" class="form-control" name="costo_unitario" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Precio de Venta ($)</label>
                            <input type="number" class="form-control" name="precio_venta_sin_iva" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">IVA Compra</label>
                            <select class="form-select" name="impuesto_compra_id">
                                <option value="1">IVA 0%</option>
                                <option value="2" selected>IVA 19%</option>
                                <option value="3">IVA 5%</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">IVA Venta</label>
                            <select class="form-select" name="impuesto_id">
                                <option value="1">IVA 0%</option>
                                <option value="2" selected>IVA 19%</option>
                                <option value="3">IVA 5%</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" name="stock" min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" name="stock_minimo" min="1" value="5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" class="form-control" name="ubicacion" placeholder="Ej: Estante A1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarProductoBtn">Guardar Producto</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Proveedor -->
<div class="modal fade" id="proveedorModal" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proveedorModalLabel">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="proveedorForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIT</label>
                            <input type="text" class="form-control" name="nit">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" class="form-control" name="ciudad">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarProveedorBtn">Guardar Proveedor</button>
            </div>
        </div>
    </div>
</div>

</div> <!-- Cierre de main-content -->

<?php include '../includes/partials/footer.php'; ?>