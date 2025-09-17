/**
 * Modals Module - Modal management and interactions
 * Handles all modal-related functionality across the application
 */

$(document).ready(function() {
    // Modal initialization and common behaviors
    
    // Auto-focus first input when modal opens
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input:first').focus();
    });
    
    // Clear forms when modal closes
    $('.modal').on('hidden.bs.modal', function() {
        const form = $(this).find('form');
        if (form.length) {
            form[0].reset();
            // Clear Select2 if present
            form.find('.select2').val(null).trigger('change');
        }
    });
    
    // Product details modal handlers
    $('.more-details-btn').click(function(e) {
        e.stopPropagation();
        const productCard = $(this).closest('.product-card');
        const productTitle = productCard.find('.card-title').text();
        
        // Update modal title
        $('#productDetailsModalLabel').text(`Detalles de ${productTitle}`);
        
        // In a real implementation, you would load product details here
        // For now, the modal shows static data
    });
    
    // Client details modal
    $('#viewClientInfo').click(function() {
        const clientId = $('#clientSelect').val();
        if (!clientId) {
            JetxcelUtils.showMessage('error', 'Debe seleccionar un cliente primero');
            return;
        }
        
        // In a real implementation, load client data based on clientId
        $('#clientDetailsModal').modal('show');
    });
    
    // Supplier details modal
    $('#viewSupplierInfo').click(function() {
        const supplierId = $('#supplierSelect').val();
        if (!supplierId) {
            JetxcelUtils.showMessage('error', 'Debe seleccionar un proveedor primero');
            return;
        }
        
        // In a real implementation, load supplier data based on supplierId
        JetxcelUtils.showMessage('info', 'Funcionalidad de información del proveedor en desarrollo');
    });
    
    // Form submission handlers for modals
    $('#productoForm').on('submit', function(e) {
        e.preventDefault();
        $('#guardarProductoBtn').click();
    });
    
    $('#proveedorForm').on('submit', function(e) {
        e.preventDefault();
        $('#guardarProveedorBtn').click();
    });
    
    // Confirmation modals
    window.showConfirmModal = function(title, message, onConfirm, onCancel = null) {
        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="confirmBtn">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#confirmModal').remove();
        
        // Add new modal
        $('body').append(modalHtml);
        
        // Handle confirm button
        $('#confirmBtn').click(function() {
            $('#confirmModal').modal('hide');
            if (onConfirm) onConfirm();
        });
        
        // Handle cancel
        $('#confirmModal').on('hidden.bs.modal', function() {
            if (onCancel) onCancel();
            $(this).remove();
        });
        
        // Show modal
        $('#confirmModal').modal('show');
    };
    
    // Generic modal for displaying information
    window.showInfoModal = function(title, content) {
        const modalHtml = `
            <div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#infoModal').remove();
        
        // Add and show new modal
        $('body').append(modalHtml);
        $('#infoModal').modal('show');
        
        // Clean up when closed
        $('#infoModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    };
});
