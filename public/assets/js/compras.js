/**
 * Compras Module - Purchase functionality
 * Handles purchase forms, supplier management, and purchase calculations
 */

$(document).ready(function() {
    // Initialize data on page load
    loadInitialData();
    
    // IVA checkbox functionality
    $('#ivaCompraCheck').change(function() {
        if ($(this).is(':checked')) {
            $('#ivaCompraManual').hide();
            $('#impuestoCompraSelect').show();
        } else {
            $('#ivaCompraManual').show();
            $('#impuestoCompraSelect').hide();
        }
        calculatePurchaseTotal();
    });
    
    // Manual IVA input
    $('#ivaCompraManual').on('input', function() {
        calculatePurchaseTotal();
    });
    
    // Tax select change
    $('#impuestoCompraSelect').change(function() {
        calculatePurchaseTotal();
    });
    
    // Calcular total de compra automáticamente
    $('input[name="cantidad"], input[name="costo_unitario"]').on('input', function() {
        calculatePurchaseTotal();
    });
    
    // Manejar envío del formulario de compra
    $('#compraForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validatePurchaseForm()) {
            return;
        }
        
        createPurchase();
    });
    
    // Botón nueva compra
    $('#nuevaCompraBtn').click(function() {
        clearPurchaseForm();
        showMessage('info', 'Formulario listo para nueva compra');
    });
    
    // Botón nuevo producto
    $('#btnNuevoProducto').click(function() {
        // El modal se abre automáticamente por data-bs-target
    });
    
    // Botón nuevo proveedor
    $('#btnNuevoProveedor').click(function() {
        // El modal se abre automáticamente por data-bs-target
    });
    
    // Guardar nuevo producto
    $('#guardarProductoBtn').click(function() {
        if (!validateProductForm()) {
            return;
        }
        
        createProduct();
    });
    
    // Guardar nuevo proveedor
    $('#guardarProveedorBtn').click(function() {
        if (!validateSupplierForm()) {
            return;
        }
        
        createSupplier();
    });
    
    // Función para mostrar información del proveedor (se puede implementar más adelante)
    function showSupplierInfo(supplierId) {
        // Implementar lógica para mostrar información del proveedor
        showMessage('info', 'Funcionalidad de información del proveedor en desarrollo');
    }
    
    // Completar compra
    $('#completePurchaseBtn').click(function() {
        if (!validatePurchaseData()) {
            return;
        }
        
        showMessage('success', 'Compra confirmada exitosamente');
        clearPurchaseForm();
    });
    
    // Cancelar compra
    $('#cancelPurchaseBtn').click(function() {
        if (confirm('¿Está seguro de que desea cancelar la compra?')) {
            clearPurchaseForm();
            showMessage('info', 'Compra cancelada');
        }
    });
    
    // Load initial data
    function loadInitialData() {
        loadProducts();
        loadSuppliers();
        loadTaxes();
    }
    
    // Load products from database
    function loadProducts() {
        $.get('/jetxcel2/src/api/get_products.php')
            .done(function(response) {
                if (response.success) {
                    const select = $('#productoSelect');
                    select.empty().append('<option value="">Seleccionar producto</option>');
                    
                    response.data.forEach(function(product) {
                        select.append(`<option value="${product.id}">${product.nombre} - ${product.referencia || 'Sin ref.'}</option>`);
                    });
                }
            })
            .fail(function() {
                showMessage('error', 'Error al cargar productos');
            });
    }
    
    // Load suppliers from database
    function loadSuppliers() {
        $.get('/jetxcel2/src/api/get_suppliers.php')
            .done(function(response) {
                if (response.success) {
                    const selects = $('#proveedorSelect, #supplierSelect');
                    selects.empty().append('<option value="">Seleccionar proveedor</option>');
                    
                    response.data.forEach(function(supplier) {
                        selects.append(`<option value="${supplier.id}">${supplier.nombre}</option>`);
                    });
                }
            })
            .fail(function() {
                showMessage('error', 'Error al cargar proveedores');
            });
    }
    
    // Load taxes from database
    function loadTaxes() {
        $.get('/jetxcel2/src/api/get_impuestos.php')
            .done(function(response) {
                if (response.success) {
                    const select = $('#impuestoCompraSelect');
                    select.empty();
                    
                    response.data.forEach(function(tax) {
                        const selected = tax.id == 2 ? 'selected' : ''; // Default to 19% IVA
                        select.append(`<option value="${tax.id}" ${selected}>${tax.nombre}</option>`);
                    });
                }
            })
            .fail(function() {
                showMessage('error', 'Error al cargar impuestos');
            });
    }
    
    // Create purchase
    function createPurchase() {
        const formData = {
            proveedor_id: $('#proveedorSelect').val(),
            numero_factura: $('#facturaNumber').val(),
            medio_pago: $('select[name="medio_pago"]').val(),
            descripcion: $('textarea[name="descripcion"]').val(),
            productos: [{
                producto_id: $('#productoSelect').val(),
                cantidad: parseFloat($('input[name="cantidad"]').val()),
                costo_unitario: parseFloat($('input[name="costo_unitario"]').val()),
                precio_venta_sugerido: parseFloat($('input[name="precio_venta"]').val()),
                impuesto_id: getSelectedTaxId(),
                porcentaje_impuesto: getSelectedTaxPercentage()
            }]
        };
        
        $.ajax({
            url: '/jetxcel2/src/api/create_compra.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData)
        })
        .done(function(response) {
            if (response.success) {
                showMessage('success', response.message);
                clearPurchaseForm();
                loadProducts(); // Refresh products to show updated stock
            } else {
                showMessage('error', response.message);
            }
        })
        .fail(function() {
            showMessage('error', 'Error al crear la compra');
        });
    }
    
    // Create product
    function createProduct() {
        const formData = new FormData();
        const form = document.getElementById('productoForm');
        
        // Add all form fields to FormData
        $(form).find('input, select, textarea').each(function() {
            if (this.type === 'file') {
                if (this.files[0]) {
                    formData.append(this.name, this.files[0]);
                }
            } else {
                formData.append(this.name, this.value);
            }
        });
        
        $.ajax({
            url: '/jetxcel2/src/api/create_producto.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(response) {
            if (response.success) {
                $('#productoModal').modal('hide');
                showMessage('success', response.message);
                clearProductForm();
                loadProducts(); // Refresh product list
            } else {
                showMessage('error', response.message);
            }
        })
        .fail(function() {
            showMessage('error', 'Error al crear el producto');
        });
    }
    
    // Create supplier
    function createSupplier() {
        const formData = {
            nombre: $('#proveedorForm input[name="nombre"]').val(),
            telefono: $('#proveedorForm input[name="telefono"]').val(),
            nit: $('#proveedorForm input[name="nit"]').val(),
            direccion: $('#proveedorForm input[name="direccion"]').val(),
            ciudad: $('#proveedorForm input[name="ciudad"]').val(),
            email: $('#proveedorForm input[name="email"]').val(),
            descripcion: $('#proveedorForm textarea[name="descripcion"]').val()
        };
        
        $.ajax({
            url: '/jetxcel2/src/api/create_supplier.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData)
        })
        .done(function(response) {
            if (response.success) {
                $('#proveedorModal').modal('hide');
                showMessage('success', response.message);
                clearSupplierForm();
                loadSuppliers(); // Refresh supplier list
            } else {
                showMessage('error', response.message);
            }
        })
        .fail(function() {
            showMessage('error', 'Error al crear el proveedor');
        });
    }
    
    // Get selected tax ID
    function getSelectedTaxId() {
        if ($('#ivaCompraCheck').is(':checked')) {
            return $('#impuestoCompraSelect').val();
        }
        return 2; // Default to 19% IVA
    }
    
    // Get selected tax percentage
    function getSelectedTaxPercentage() {
        if ($('#ivaCompraCheck').is(':checked')) {
            const selectedOption = $('#impuestoCompraSelect option:selected');
            const text = selectedOption.text();
            const match = text.match(/(\d+(?:\.\d+)?)%/);
            return match ? parseFloat(match[1]) : 19.0;
        } else {
            return parseFloat($('#ivaCompraManual').val()) || 19.0;
        }
    }
    
    // Funciones auxiliares
    function calculatePurchaseTotal() {
        const cantidad = parseFloat($('input[name="cantidad"]').val()) || 0;
        const costoUnitario = parseFloat($('input[name="costo_unitario"]').val()) || 0;
        const subtotal = cantidad * costoUnitario;
        const ivaPercentage = getSelectedTaxPercentage();
        const iva = (subtotal * ivaPercentage) / 100;
        const total = subtotal + iva;
        
        $('#totalCompra').val('$' + subtotal.toFixed(2));
        $('#subtotalAmount').text('$' + subtotal.toFixed(2));
        $('#ivaAmount').text('$' + iva.toFixed(2));
        $('#totalAmount').text('$' + total.toFixed(2));
    }
    
    function validatePurchaseForm() {
        const producto = $('#productoSelect').val();
        const cantidad = $('input[name="cantidad"]').val();
        const costoUnitario = $('input[name="costo_unitario"]').val();
        const precioVenta = $('input[name="precio_venta"]').val();
        const proveedor = $('#proveedorSelect').val();
        const medioPago = $('select[name="medio_pago"]').val();
        
        if (!producto) {
            showMessage('error', 'Debe seleccionar un producto');
            return false;
        }
        
        if (!cantidad || cantidad <= 0) {
            showMessage('error', 'La cantidad debe ser mayor a 0');
            return false;
        }
        
        if (!costoUnitario || costoUnitario <= 0) {
            showMessage('error', 'El costo unitario debe ser mayor a 0');
            return false;
        }
        
        if (!precioVenta || precioVenta <= 0) {
            showMessage('error', 'El precio de venta debe ser mayor a 0');
            return false;
        }
        
        if (!proveedor) {
            showMessage('error', 'Debe seleccionar un proveedor');
            return false;
        }
        
        if (!medioPago) {
            showMessage('error', 'Debe seleccionar un medio de pago');
            return false;
        }
        
        return true;
    }
    
    function validatePurchaseData() {
        const supplier = $('#supplierSelect').val();
        
        if (!supplier) {
            showMessage('error', 'Debe seleccionar un proveedor para confirmar la compra');
            return false;
        }
        
        const totalAmount = $('#totalAmount').text();
        if (totalAmount === '$0.00') {
            showMessage('error', 'Debe agregar productos a la compra');
            return false;
        }
        
        return true;
    }
    
    function validateProductForm() {
        const nombre = $('#productoForm input[name="nombre"]').val();
        const categoria = $('#productoForm select[name="categoria_id"]').val();
        const costoUnitario = $('#productoForm input[name="costo_unitario"]').val();
        const precioVenta = $('#productoForm input[name="precio_venta_sin_iva"]').val();
        
        if (!nombre.trim()) {
            showMessage('error', 'El nombre del producto es requerido');
            return false;
        }
        
        if (!categoria) {
            showMessage('error', 'Debe seleccionar una categoría');
            return false;
        }
        
        if (!costoUnitario || costoUnitario <= 0) {
            showMessage('error', 'El costo unitario debe ser mayor a 0');
            return false;
        }
        
        if (!precioVenta || precioVenta <= 0) {
            showMessage('error', 'El precio de venta debe ser mayor a 0');
            return false;
        }
        
        return true;
    }
    
    function validateSupplierForm() {
        const nombre = $('#proveedorForm input[name="nombre"]').val();
        
        if (!nombre.trim()) {
            showMessage('error', 'El nombre del proveedor es requerido');
            return false;
        }
        
        return true;
    }
    
    function clearPurchaseForm() {
        $('#compraForm')[0].reset();
        $('#productoSelect').val('').trigger('change');
        $('#proveedorSelect').val('').trigger('change');
        $('#supplierSelect').val('').trigger('change');
        $('#totalCompra').val('$0.00');
        $('#subtotalAmount').text('$0.00');
        $('#ivaAmount').text('$0.00');
        $('#totalAmount').text('$0.00');
        $('.supplier-info-btn').hide();
    }
    
    function clearProductForm() {
        $('#productoForm')[0].reset();
    }
    
    function clearSupplierForm() {
        $('#proveedorForm')[0].reset();
    }
    
    function showMessage(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        const alertId = type === 'error' ? '#mensajeError' : '#mensajeSuccess';
        
        $(alertId).removeClass('alert-danger alert-success alert-info')
                  .addClass(alertClass)
                  .text(message)
                  .show();
        
        setTimeout(() => {
            $(alertId).hide();
        }, 3000);
    }
});
