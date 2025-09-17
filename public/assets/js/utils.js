/**
 * Utilities Module - Common utility functions
 * Reusable functions for formatting, validation, and common operations
 */

// Utility functions that can be used across different modules
window.JetxcelUtils = {
    
    // Format currency
    formatCurrency: function(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    },
    
    // Parse currency string to number
    parseCurrency: function(currencyString) {
        return parseFloat(currencyString.replace(/[$,]/g, '')) || 0;
    },
    
    // Show message with auto-hide
    showMessage: function(type, message, duration = 3000) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        const alertId = type === 'error' ? '#mensajeError' : '#mensajeSuccess';
        
        $(alertId).removeClass('alert-danger alert-success alert-info')
                  .addClass(alertClass)
                  .text(message)
                  .show();
        
        setTimeout(() => {
            $(alertId).hide();
        }, duration);
    },
    
    // Validate required fields
    validateRequired: function(fields) {
        for (let field of fields) {
            const value = $(field.selector).val();
            if (!value || (typeof value === 'string' && !value.trim())) {
                this.showMessage('error', field.message);
                return false;
            }
        }
        return true;
    },
    
    // Validate numeric fields
    validateNumeric: function(fields) {
        for (let field of fields) {
            const value = parseFloat($(field.selector).val());
            if (isNaN(value) || value <= 0) {
                this.showMessage('error', field.message);
                return false;
            }
        }
        return true;
    },
    
    // Confirm action
    confirmAction: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },
    
    // Format date
    formatDate: function(date) {
        return new Date(date).toLocaleDateString('es-CO');
    },
    
    // Format datetime
    formatDateTime: function(date) {
        return new Date(date).toLocaleString('es-CO');
    },
    
    // Debounce function for search inputs
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Loading state management
    setLoading: function(element, loading = true) {
        const $element = $(element);
        if (loading) {
            $element.prop('disabled', true);
            const originalText = $element.text();
            $element.data('original-text', originalText);
            $element.html('<i class="bi bi-hourglass-split"></i> Cargando...');
        } else {
            $element.prop('disabled', false);
            const originalText = $element.data('original-text');
            if (originalText) {
                $element.text(originalText);
            }
        }
    },
    
    // Generate unique ID
    generateId: function() {
        return 'id_' + Math.random().toString(36).substr(2, 9);
    },
    
    // Local storage helpers
    storage: {
        set: function(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (e) {
                console.error('Error saving to localStorage:', e);
                return false;
            }
        },
        
        get: function(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (e) {
                console.error('Error reading from localStorage:', e);
                return defaultValue;
            }
        },
        
        remove: function(key) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (e) {
                console.error('Error removing from localStorage:', e);
                return false;
            }
        }
    }
};
