/**
 * API Module - AJAX requests and API communication
 * Centralized API calls for consistent error handling and data management
 */

window.JetxcelAPI = {
    
    // Base configuration
    baseUrl: '../../src/api/',
    
    // Common AJAX settings
    defaultSettings: {
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        beforeSend: function() {
            // Show loading state
        },
        complete: function() {
            // Hide loading state
        },
        error: function(xhr, status, error) {
            console.error('API Error:', error);
            JetxcelUtils.showMessage('error', 'Error de conexión. Intente nuevamente.');
        }
    },
    
    // Generic request method
    request: function(endpoint, data = {}, options = {}) {
        const settings = {
            ...this.defaultSettings,
            url: this.baseUrl + endpoint,
            data: data,
            ...options
        };
        
        return $.ajax(settings);
    },
    
    // Products API
    products: {
        getAll: function(filters = {}) {
            return JetxcelAPI.request('get_products.php', filters);
        },
        
        getById: function(id) {
            return JetxcelAPI.request('get_products.php', { id: id });
        },
        
        create: function(productData) {
            return JetxcelAPI.request('create_product.php', productData);
        },
        
        update: function(id, productData) {
            return JetxcelAPI.request('update_product.php', { id: id, ...productData });
        },
        
        delete: function(id) {
            return JetxcelAPI.request('delete_product.php', { id: id });
        }
    },
    
    // Clients API
    clients: {
        getAll: function() {
            return JetxcelAPI.request('get_clients.php');
        },
        
        getById: function(id) {
            return JetxcelAPI.request('get_clients.php', { id: id });
        },
        
        create: function(clientData) {
            return JetxcelAPI.request('create_client.php', clientData);
        },
        
        update: function(id, clientData) {
            return JetxcelAPI.request('update_client.php', { id: id, ...clientData });
        }
    },
    
    // Sales API
    sales: {
        getRecent: function(limit = 10) {
            return JetxcelAPI.request('get_recent_sales.php', { limit: limit });
        },
        
        create: function(saleData) {
            return JetxcelAPI.request('create_sale.php', saleData);
        },
        
        getById: function(id) {
            return JetxcelAPI.request('get_sale.php', { id: id });
        }
    },
    
    // Suppliers API
    suppliers: {
        getAll: function() {
            return JetxcelAPI.request('get_suppliers.php');
        },
        
        getById: function(id) {
            return JetxcelAPI.request('get_suppliers.php', { id: id });
        },
        
        create: function(supplierData) {
            return JetxcelAPI.request('create_supplier.php', supplierData);
        }
    },
    
    // Purchases API
    purchases: {
        create: function(purchaseData) {
            return JetxcelAPI.request('create_purchase.php', purchaseData);
        },
        
        getRecent: function(limit = 10) {
            return JetxcelAPI.request('get_recent_purchases.php', { limit: limit });
        }
    }
};
