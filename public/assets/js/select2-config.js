/**
 * Select2 Configuration - Reusable Select2 initialization and configuration
 */

$(document).ready(function() {
    // Configuración base para Select2
    const select2BaseConfig = {
        allowClear: true,
        theme: 'bootstrap-5'
    };
    
    // Inicializar Select2 para clientes
    if ($('#clientSelect').length) {
        $('#clientSelect').select2({
            ...select2BaseConfig,
            placeholder: "Seleccionar cliente"
        });
    }
    
    // Inicializar Select2 para proveedores
    if ($('#supplierSelect, #proveedorSelect').length) {
        $('#supplierSelect, #proveedorSelect').select2({
            ...select2BaseConfig,
            placeholder: "Seleccionar proveedor"
        });
    }
    
    // Inicializar Select2 para productos
    if ($('#productoSelect').length) {
        $('#productoSelect').select2({
            ...select2BaseConfig,
            placeholder: "Seleccionar producto"
        });
    }
    
    // Configuración para selects múltiples
    $('.select2-multiple').select2({
        ...select2BaseConfig,
        multiple: true
    });
});
