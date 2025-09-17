/**
 * Ventas Module - Sales functionality
 * Handles product selection, cart management, discounts, and sales completion
 */

// Global variables
let cart = [];
let currentClient = null;
const appConfig = {
    baseUrl: window.ventasData?.baseUrl || '',
    csrfToken: window.ventasData?.csrfToken || '',
    impuestoIva: window.ventasData?.impuestoIva || 19
};

$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Client selection and management
    $('#clientSelect').select2({
        placeholder: 'Seleccionar cliente',
        width: '100%',
        language: 'es',
        allowClear: true
    }).on('change', function() {
        const clientId = $(this).val();
        if (clientId) {
            loadClientDetails(clientId);
            $('#viewClientInfo').prop('disabled', false);
            checkSaleButtonState();
        } else {
            $('#clientInfo').addClass('d-none');
            $('#viewClientInfo').prop('disabled', true);
            checkSaleButtonState();
        }
    });
    
    // Show client details modal
    $('#viewClientInfo').on('click', function() {
        const clientId = $('#clientSelect').val();
        if (clientId) {
            showClientDetailsModal(clientId);
        }
    });
    
    // Add product to cart
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.stopPropagation();
        
        const productId = $(this).data('producto-id');
        const productName = $(this).data('producto-nombre');
        const productPrice = parseFloat($(this).data('producto-precio'));
        const productStock = parseInt($(this).data('producto-stock'));
        const productTaxRate = parseFloat($(this).data('producto-impuesto'));
        const productTaxId = $(this).data('producto-impuesto-id');
        
        // Check if product is already in cart
        const existingItemIndex = cart.findIndex(item => item.id === productId);
        
        if (existingItemIndex >= 0) {
            // Increase quantity if already in cart
            if (cart[existingItemIndex].cantidad < productStock) {
                cart[existingItemIndex].cantidad += 1;
                showMessage('success', `Cantidad actualizada para ${productName}`);
            } else {
                showMessage('warning', `No hay suficiente stock para ${productName}`);
                return;
            }
        } else {
            // Add new item to cart
            cart.push({
                id: productId,
                nombre: productName,
                precio: productPrice,
                cantidad: 1,
                stock: productStock,
                impuesto_id: productTaxId,
                porcentaje_impuesto: productTaxRate
            });
            showMessage('success', `${productName} agregado al carrito`);
        }
        
        updateCartDisplay();
        updateSaleTotals();
        checkSaleButtonState();
    });
    
    // Add product to cart from modal
    $('#addToCartFromModal').on('click', function() {
        const productId = $(this).data('producto-id');
        const $productCard = $(`[data-producto-id="${productId}"]`);
        $productCard.find('.add-to-cart-btn').trigger('click');
        $('#productDetailsModal').modal('hide');
    });
    
    // Show product details modal
    $(document).on('click', '.product-card', function(e) {
        // Don't trigger if clicking on add to cart button
        if ($(e.target).closest('.add-to-cart-btn').length) {
            return;
        }
        
        const productId = $(this).data('producto-id');
        showProductDetails(productId);
    });
    
    // Handle quantity controls
    $(document).on('click', '.quantity-increase', function(e) {
        e.stopPropagation();
        const $input = $(this).siblings('.product-quantity');
        const currentValue = parseInt($input.val()) || 1;
        const maxStock = parseInt($input.data('max-stock')) || 9999;
        
        if (currentValue < maxStock) {
            $input.val(currentValue + 1).trigger('change');
        } else {
            showMessage('warning', 'No hay suficiente stock disponible');
        }
    });
    
    $(document).on('click', '.quantity-decrease', function(e) {
        e.stopPropagation();
        const $input = $(this).siblings('.product-quantity');
        const currentValue = parseInt($input.val()) || 1;
        
        if (currentValue > 1) {
            $input.val(currentValue - 1).trigger('change');
        }
    });
    
    // Update cart when quantity changes
    $(document).on('change', '.product-quantity', function() {
        const $row = $(this).closest('.selected-product');
        const productId = $row.data('product-id');
        const newQuantity = parseInt($(this).val()) || 1;
        const maxStock = parseInt($(this).data('max-stock')) || 9999;
        
        // Validate quantity
        if (newQuantity < 1) {
            $(this).val(1);
            return;
        }
        
        if (newQuantity > maxStock) {
            $(this).val(maxStock);
            showMessage('warning', 'No hay suficiente stock disponible');
            return;
        }
        
        // Update cart
        const itemIndex = cart.findIndex(item => item.id === productId);
        if (itemIndex >= 0) {
            cart[itemIndex].cantidad = newQuantity;
            updateCartItem($row, cart[itemIndex]);
            updateSaleTotals();
        }
    });
    
    // Update price when changed
    $(document).on('change', '.product-price', function() {
        const $row = $(this).closest('.selected-product');
        const productId = $row.data('product-id');
        const newPrice = parseFloat($(this).val()) || 0;
        
        // Update cart
        const itemIndex = cart.findIndex(item => item.id === productId);
        if (itemIndex >= 0) {
            cart[itemIndex].precio = newPrice;
            updateCartItem($row, cart[itemIndex]);
            updateSaleTotals();
        }
    });
    
    // Remove product from cart
    $(document).on('click', '.remove-product', function(e) {
        e.stopPropagation();
        const $row = $(this).closest('.selected-product');
        const productId = $row.data('product-id');
        
        // Remove from cart
        cart = cart.filter(item => item.id !== productId);
        
        // Update display
        $row.fadeOut(300, function() {
            $(this).remove();
            if (cart.length === 0) {
                $('#selectedProducts').html('<div class="alert alert-info">Seleccione productos para la venta</div>');
            }
            updateSaleTotals();
            checkSaleButtonState();
        });
        
        showMessage('info', 'Producto eliminado del carrito');
    });
    
    // Calculate change
    $('#amountReceived').on('input', function() {
        updateChangeAmount();
    });
    
    // Apply discount
    $('#applyDiscountBtn').click(function() {
        updateSaleTotals();
    });
    
    // Payment method change
    $('#paymentMethod').change(function() {
        const method = $(this).val();
        if (method === 'Cuenta por pagar') {
            $('#amountReceived').val('0').prop('readonly', true);
            updateChangeAmount();
        } else {
            $('#amountReceived').prop('readonly', false);
        }
    });
    
    // New client form
    $('#newClientForm').on('submit', function(e) {
        e.preventDefault();
        saveNewClient();
    });
    
    // Filter products
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        window.location.href = `${appConfig.baseUrl}/ventas.php?${formData}`;
    });
    
    // Change items per page
    $('#productsPerPage').change(function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('por_pagina', perPage);
        url.searchParams.set('pagina', 1); // Reset to first page
        window.location.href = url.toString();
    });
    
    // Initialize the page
    resetSale();
    
    // Cancel sale
    $('#cancelSaleBtn').click(function() {
        if (confirm('¿Está seguro de que desea cancelar la venta actual?')) {
            resetSale();
            showMessage('info', 'Venta cancelada correctamente');
        }
    });
    
    // New sale button
    $('#newSaleBtn').click(function() {
        resetSale();
        $('#saleConfirmationModal').modal('hide');
    });
    
    // Print invoice
    $('#printInvoiceBtn').click(function() {
        const saleId = $(this).data('sale-id');
        if (saleId) {
            window.open(`${appConfig.baseUrl}/facturas/imprimir.php?id=${saleId}`, '_blank');
        }
    });
    
    // Helper functions
    function updateCartDisplay() {
        if (cart.length === 0) {
            $('#selectedProducts').html('<div class="alert alert-info">Seleccione productos para la venta</div>');
            return;
        }
        
        let html = '';
        cart.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            const iva = (subtotal * item.porcentaje_impuesto) / 100;
            
            html += `
                <div class="selected-product" data-product-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 product-name">${item.nombre}</h6>
                        <button type="button" class="btn-close remove-product" aria-label="Eliminar"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Cantidad</label>
                            <input type="number" class="form-control form-control-sm product-quantity" 
                                   min="1" max="${item.stock}" value="${item.cantidad}" 
                                   data-max-stock="${item.stock}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Precio Unit.</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control form-control-sm product-price" 
                                       step="0.01" min="0" value="${item.precio.toFixed(2)}">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Stock: <span class="stock-amount">${item.stock}</span></small>
                        <strong>Subtotal: $<span class="product-subtotal">${subtotal.toFixed(2)}</span></strong>
                    </div>
                    <input type="hidden" class="product-tax-rate" value="${item.porcentaje_impuesto}">
                    <input type="hidden" class="product-tax-id" value="${item.impuesto_id}">
                    <hr class="my-2">
                </div>
            `;
        });
        
        $('#selectedProducts').html(html);
    }
    
    function updateCartItem($row, item) {
        const subtotal = item.precio * item.cantidad;
        $row.find('.product-subtotal').text(subtotal.toFixed(2));
    }
    
    function updateSaleTotals() {
        let subtotal = 0;
        let totalIva = 0;
        
        $('.selected-product').each(function() {
            const price = parseFloat($(this).find('.product-price').val()) || 0;
            const quantity = parseInt($(this).find('.product-quantity').val()) || 1;
            const taxRate = parseFloat($(this).find('.product-tax-rate').val()) || 0;
            
            const itemSubtotal = price * quantity;
            const itemIva = (itemSubtotal * taxRate) / 100;
            
            subtotal += itemSubtotal;
            totalIva += itemIva;
        });
        
        // Apply discount
        const discount = parseFloat($('#discountInput').val()) || 0;
        const total = subtotal + totalIva - discount;
        
        // Update UI
        $('#subtotalAmount').text('$' + subtotal.toFixed(2));
        $('#taxAmount').text('$' + totalIva.toFixed(2));
        $('#discountAmount').text('$' + discount.toFixed(2));
        $('#totalAmount').text('$' + total.toFixed(2));
        
        // Store values for form submission
        $('#totalSinDescuento').val(subtotal);
        $('#totalConIva').val(total + discount); // Total before discount
        
        // Update change amount
        updateChangeAmount();
        
        // Check if sale can be completed
        checkSaleButtonState();
    }
    
    function updateChangeAmount() {
        const received = parseFloat($('#amountReceived').val()) || 0;
        const total = parseFloat($('#totalAmount').text().replace('$', '')) || 0;
        const change = received - total;
        
        if (change >= 0) {
            $('#changeAmount').val(change.toFixed(2));
            $('#changeAmount').removeClass('text-danger').addClass('text-success');
        } else {
            $('#changeAmount').val(Math.abs(change).toFixed(2));
            $('#changeAmount').removeClass('text-success').addClass('text-danger');
        }
    }
    
    function checkSaleButtonState() {
        const hasProducts = cart.length > 0;
        const hasClient = $('#clientSelect').val() !== '';
        const paymentMethod = $('#paymentMethod').val();
        const amountReceived = parseFloat($('#amountReceived').val()) || 0;
        const total = parseFloat($('#totalAmount').text().replace('$', '')) || 0;
        
        // Enable/disable complete sale button
        if (hasProducts && hasClient) {
            if (paymentMethod === 'Cuenta por pagar' || amountReceived >= total) {
                $('#completeSaleBtn').prop('disabled', false);
                return;
            }
        }
        
        $('#completeSaleBtn').prop('disabled', true);
    }
    
    function validateSale() {
        // Validate client
        if (!currentClient) {
            showMessage('error', 'Debe seleccionar un cliente para la venta');
            $('#clientSelect').focus();
            return false;
        }
        
        // Validate products
        if (cart.length === 0) {
            showMessage('error', 'Debe agregar al menos un producto a la venta');
            return false;
        }
        
        // Validate payment
        const paymentMethod = $('#paymentMethod').val();
        if (!paymentMethod) {
            showMessage('error', 'Debe seleccionar un método de pago');
            return false;
        }
        
        if (paymentMethod !== 'Cuenta por pagar') {
            const amountReceived = parseFloat($('#amountReceived').val()) || 0;
            const total = parseFloat($('#totalAmount').text().replace('$', '')) || 0;
            
            if (amountReceived < total) {
                showMessage('error', 'El monto recibido es menor al total de la venta');
                return false;
            }
        }
        
        return true;
    }

function resetSale() {
    // Reset cart
    cart = [];
    currentClient = null;
    
    // Reset form
    $('#selectedProducts').html('<div class="alert alert-info">Seleccione productos para la venta</div>');
    $('#clientSelect').val('').trigger('change');
    $('#clientInfo').addClass('d-none');
    $('#paymentMethod').val('Efectivo');
    $('#amountReceived').val('').prop('readonly', false);
    $('#changeAmount').val('0.00').removeClass('text-danger text-success');
    $('#discountInput').val('0');
    $('#saleNotes').val('');
    
    // Reset totals
    $('#subtotalAmount').text('$0.00');
    $('#taxAmount').text('$0.00');
    $('#discountAmount').text('$0.00');
    $('#totalAmount').text('$0.00');
    
    // Reset buttons
    $('#completeSaleBtn').prop('disabled', true);
}

function showMessage(type, message) {
    const $alert = $('#mensajeError, #mensajeSuccess').hide();
    
    if (type === 'error') {
        $('#mensajeError').text(message).show();
    } else if (type === 'success') {
        $('#mensajeSuccess').removeClass('alert-info').addClass('alert-success')
            .text(message).show();
    } else {
        // Info message
        $('#mensajeSuccess').removeClass('alert-success').addClass('alert-info')
            .text(message).show();
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        $alert.fadeOut();
    }, 5000);
}

function loadClientDetails(clientId) {
    $.ajax({
        url: `${appConfig.baseUrl}/api/clientes/detalle.php`,
        type: 'GET',
        data: { id: clientId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const client = response.data;
                currentClient = client;
                
                // Update client info card
                $('#clientName').text(client.nombre);
                $('#clientNit').text(client.nit ? `NIT: ${client.nit}` : '');
                
                let contactInfo = [];
                if (client.telefono) contactInfo.push(`Tel: ${client.telefono}`);
                if (client.email) contactInfo.push(`Email: ${client.email}`);
                
                let locationInfo = [];
                if (client.direccion) locationInfo.push(client.direccion);
                if (client.ciudad) locationInfo.push(client.ciudad);
                
                $('#clientContact').html(contactInfo.join(' | '));
                $('#clientLocation').html(locationInfo.join(', '));
                
                // Show client info
                $('#clientInfo').removeClass('d-none');
            } else {
                showMessage('error', response.message || 'Error al cargar los datos del cliente');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading client details:', error);
            showMessage('error', 'Error al cargar los datos del cliente');
        }
    });
}

function showClientDetailsModal(clientId) {
    // In a real implementation, this would load client details via AJAX
    // For now, we'll just show the modal with basic info
    const client = {
        id: clientId,
        nombre: $('#clientSelect option:selected').text().split(' - ')[0],
        nit: $('#clientSelect option:selected').text().split(' - ')[1] || '',
        telefono: '3001234567',
        email: 'cliente@ejemplo.com',
        direccion: 'Calle Falsa 123',
        ciudad: 'Bogotá',
        notas: 'Cliente frecuente'
    };
    
    // Update modal with client data
    $('#clientModalName').text(client.nombre);
    $('#clientModalNit').text(client.nit ? `NIT: ${client.nit}` : '');
    $('#clientModalPhone').text(client.telefono || 'No especificado');
    $('#clientModalEmail').text(client.email || 'No especificado');
    $('#clientModalAddress').text(client.direccion || 'No especificada');
    $('#clientModalCity').text(client.ciudad ? `, ${client.ciudad}` : '');
    $('#clientModalNotes').text(client.notas || 'Sin notas adicionales');
    
    // Load purchase history (simulated)
    const historyHtml = `
        <tr>
            <td>FAC-${new Date().getFullYear()}0001</td>
            <td>${new Date().toLocaleDateString()}</td>
            <td class="text-end">$1,250.00</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>FAC-${new Date().getFullYear() - 1}0012</td>
            <td>${new Date(new Date().setMonth(new Date().getMonth() - 1)).toLocaleDateString()}</td>
            <td class="text-end">$850.50</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#clientPurchaseHistory').html(historyHtml);
    $('#clientTotalSpent').text('$2,100.50');
    
    // Show the modal
    $('#clientDetailsModal').modal('show');
}

function showProductDetails(productId) {
    // In a real implementation, this would load product details via AJAX
    // For now, we'll just show the modal with basic info
    const $productCard = $(`[data-producto-id="${productId}"]`);
    
    // Update modal with product data
    $('#productDetailName').text($productCard.find('.card-title').text());
    $('#productDetailPrice').text($productCard.find('.card-price').text());
    
    // Set stock badge
    const stock = parseInt($productCard.find('.add-to-cart-btn').data('producto-stock')) || 0;
    const stockText = stock > 5 ? 'En stock' : stock > 0 ? 'Poco stock' : 'Sin stock';
    const stockClass = stock > 5 ? 'bg-success' : stock > 0 ? 'bg-warning' : 'bg-danger';
    
    $('#productDetailStock')
        .text(stockText)
        .removeClass('bg-success bg-warning bg-danger')
        .addClass(stockClass);
    
    // Set product image
    const imageUrl = $productCard.find('img').attr('src');
    $('#productDetailImage').attr('src', imageUrl);
    
    // Set other details (simulated)
    $('#productDetailReference').text($productCard.find('.card-details:contains("Ref:")').text().replace('Ref:', '').trim() || 'N/A');
    $('#productDetailModel').text($productCard.find('.card-details:contains("Modelo:")').text().replace('Modelo:', '').trim() || 'N/A');
    $('#productDetailCategory').text('Electrónicos');
    $('#productDetailManufacturer').text('Fabricante Ejemplo');
    $('#productDetailBarcode').text('1234567890123');
    $('#productDetailLocation').text('Estante A1');
    $('#productDetailTax').text('19%');
    $('#productDetailDescription').text('Descripción detallada del producto con todas sus características y especificaciones técnicas.');
    
    // Set product ID for add to cart button
    $('#addToCartFromModal').data('producto-id', productId);
    
    // Show the modal
    $('#productDetailsModal').modal('show');
}

function saveNewClient() {
    const formData = {
        nombre: $('#clientNameInput').val().trim(),
        nit: $('#clientNitInput').val().trim(),
        telefono: $('#clientPhoneInput').val().trim(),
        email: $('#clientEmailInput').val().trim(),
        direccion: $('#clientAddressInput').val().trim(),
        ciudad: $('#clientCityInput').val().trim(),
        descripcion: $('#clientDescriptionInput').val().trim()
    };
    
    // Basic validation
    if (!formData.nombre) {
        showMessage('error', 'El nombre del cliente es obligatorio');
        return;
    }
    
    // In a real implementation, this would be an AJAX call to save the client
    // For now, we'll simulate a successful save
    setTimeout(() => {
        // Add new client to select
        const newClientId = 'new_' + Date.now();
        const displayName = formData.nombre + (formData.nit ? ` - ${formData.nit}` : '');
        
        const $newOption = new Option(displayName, newClientId, true, true);
        $('#clientSelect').append($newOption).trigger('change');
        
        // Close modal and reset form
        $('#newClientModal').modal('hide');
        $('#newClientForm')[0].reset();
        
        showMessage('success', 'Cliente guardado correctamente');
    }, 1000);
}

function processSale() {
    // Prepare sale data
    const saleData = {
        cliente_id: currentClient.id,
        productos: [],
        pago: {
            metodo: $('#paymentMethod').val(),
            monto: parseFloat($('#totalAmount').text().replace('$', '')),
            recibido: parseFloat($('#amountReceived').val()) || 0,
            cambio: parseFloat($('#changeAmount').val()) || 0
        },
        descuento: parseFloat($('#discountInput').val()) || 0,
        notas: $('#saleNotes').val()
    };
    
    // Add products to sale
    $('.selected-product').each(function() {
        const productId = $(this).data('product-id');
        const cantidad = parseInt($(this).find('.product-quantity').val()) || 1;
        const precio = parseFloat($(this).find('.product-price').val()) || 0;
        const impuesto_id = $(this).find('.product-tax-id').val();
        const porcentaje_impuesto = parseFloat($(this).find('.product-tax-rate').val()) || 0;
        
        saleData.productos.push({
            id: productId,
            cantidad: cantidad,
            precio_unitario: precio,
            impuesto_id: impuesto_id,
            porcentaje_impuesto: porcentaje_impuesto
        });
    });
    
    // Show loading state
    const $completeBtn = $('#completeSaleBtn');
    const originalBtnText = $completeBtn.html();
    $completeBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');
    
    // Make AJAX call to save the sale
    $.ajax({
        url: appConfig.baseUrl + '/api/ventas/process',
        type: 'POST',
        data: JSON.stringify(saleData),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': appConfig.csrfToken
        },
        success: function(response) {
            // Reset button state
            $completeBtn.prop('disabled', false).html(originalBtnText);
            
            if (response.success) {
                // Update confirmation modal
                $('#saleInvoiceNumber').text(response.venta_id);
                $('#saleTotalAmount').text(`$${saleData.pago.monto.toFixed(2)}`);
                $('#printInvoiceBtn').data('sale-id', response.venta_id);
                
                // Show confirmation modal
                $('#saleConfirmationModal').modal('show');
                
                // Reset form
                resetSale();
                
                // Show success message
                showMessage('success', `Venta #${response.venta_id} completada exitosamente`);
            } else {
                showMessage('error', response.message || 'Error al procesar la venta');
            }
        },
        error: function(xhr, status, error) {
            // Reset button state
            $completeBtn.prop('disabled', false).html(originalBtnText);
            
            let errorMessage = 'Error al conectar con el servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showMessage('error', errorMessage);
            console.error('Error:', error);
        }
    });
}

// End of document ready
});