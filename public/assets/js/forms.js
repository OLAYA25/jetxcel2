/**
 * Forms Module - Form validation and handling utilities
 * Provides consistent form validation, submission handling, and user feedback
 */

$(document).ready(function() {
    // Auto-initialize all forms with the 'auto-validate' class
    $('.auto-validate').each(function() {
        initializeForm($(this));
    });
    
    // Initialize form validation and handling
    function initializeForm($form) {
        // Real-time validation on blur
        $form.find('input, select, textarea').on('blur', function() {
            validateField($(this));
        });
        
        // Form submission handling
        $form.on('submit', function(e) {
            if (!validateForm($(this))) {
                e.preventDefault();
                return false;
            }
        });
        
        // Clear validation on focus
        $form.find('input, select, textarea').on('focus', function() {
            clearFieldValidation($(this));
        });
    }
    
    // Validate individual field
    function validateField($field) {
        const value = $field.val();
        const fieldName = $field.attr('name') || $field.attr('id');
        let isValid = true;
        let message = '';
        
        // Required field validation
        if ($field.prop('required') && (!value || value.trim() === '')) {
            isValid = false;
            message = 'Este campo es requerido';
        }
        
        // Email validation
        if (isValid && $field.attr('type') === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Ingrese un email válido';
            }
        }
        
        // Phone validation (Colombian format)
        if (isValid && $field.hasClass('phone-input') && value) {
            const phoneRegex = /^(\+57|57)?[0-9]{10}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
                message = 'Ingrese un teléfono válido (+57 300 123 4567)';
            }
        }
        
        // Number validation
        if (isValid && $field.attr('type') === 'number' && value) {
            const min = parseFloat($field.attr('min'));
            const max = parseFloat($field.attr('max'));
            const numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                isValid = false;
                message = 'Ingrese un número válido';
            } else if (!isNaN(min) && numValue < min) {
                isValid = false;
                message = `El valor mínimo es ${min}`;
            } else if (!isNaN(max) && numValue > max) {
                isValid = false;
                message = `El valor máximo es ${max}`;
            }
        }
        
        // Custom validation patterns
        const pattern = $field.attr('pattern');
        if (isValid && pattern && value) {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                isValid = false;
                message = $field.attr('title') || 'Formato no válido';
            }
        }
        
        // Show/hide validation feedback
        if (isValid) {
            showFieldSuccess($field);
        } else {
            showFieldError($field, message);
        }
        
        return isValid;
    }
    
    // Validate entire form
    function validateForm($form) {
        let isValid = true;
        const $fields = $form.find('input, select, textarea').filter('[required], [type="email"], .phone-input, [pattern]');
        
        $fields.each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Show field error
    function showFieldError($field, message) {
        $field.removeClass('is-valid').addClass('is-invalid');
        
        let $feedback = $field.siblings('.invalid-feedback');
        if ($feedback.length === 0) {
            $feedback = $('<div class="invalid-feedback"></div>');
            $field.after($feedback);
        }
        $feedback.text(message);
    }
    
    // Show field success
    function showFieldSuccess($field) {
        $field.removeClass('is-invalid').addClass('is-valid');
        $field.siblings('.invalid-feedback').remove();
    }
    
    // Clear field validation
    function clearFieldValidation($field) {
        $field.removeClass('is-invalid is-valid');
        $field.siblings('.invalid-feedback').remove();
    }
    
    // Format currency inputs
    $(document).on('input', '.currency-input', function() {
        let value = $(this).val().replace(/[^0-9.]/g, '');
        if (value) {
            const numValue = parseFloat(value);
            if (!isNaN(numValue)) {
                $(this).val(numValue.toFixed(2));
            }
        }
    });
    
    // Format phone inputs
    $(document).on('input', '.phone-input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            if (value.startsWith('57')) {
                value = '+' + value;
            } else if (!value.startsWith('+57')) {
                value = '+57' + value;
            }
            
            // Format as +57 XXX XXX XXXX
            if (value.length > 3) {
                value = value.substring(0, 3) + ' ' + value.substring(3);
            }
            if (value.length > 7) {
                value = value.substring(0, 7) + ' ' + value.substring(7);
            }
            if (value.length > 11) {
                value = value.substring(0, 11) + ' ' + value.substring(11, 15);
            }
        }
        $(this).val(value);
    });
    
    // Auto-save form data to localStorage
    function enableAutoSave($form, key) {
        const saveKey = `form_autosave_${key}`;
        
        // Load saved data
        const savedData = JetxcelUtils.storage.get(saveKey);
        if (savedData) {
            Object.keys(savedData).forEach(name => {
                const $field = $form.find(`[name="${name}"]`);
                if ($field.length) {
                    $field.val(savedData[name]);
                }
            });
        }
        
        // Save on change
        $form.find('input, select, textarea').on('change input', JetxcelUtils.debounce(function() {
            const formData = {};
            $form.find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    formData[name] = $(this).val();
                }
            });
            JetxcelUtils.storage.set(saveKey, formData);
        }, 1000));
        
        // Clear on successful submit
        $form.on('submit', function() {
            JetxcelUtils.storage.remove(saveKey);
        });
    }
    
    // Public methods
    window.JetxcelForms = {
        validateForm: validateForm,
        validateField: validateField,
        initializeForm: initializeForm,
        enableAutoSave: enableAutoSave,
        
        // Reset form with animation
        resetForm: function($form) {
            $form[0].reset();
            $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $form.find('.invalid-feedback').remove();
            $form.find('.select2').val(null).trigger('change');
        },
        
        // Serialize form to object
        serializeObject: function($form) {
            const formArray = $form.serializeArray();
            const formObject = {};
            
            formArray.forEach(field => {
                if (formObject[field.name]) {
                    if (!Array.isArray(formObject[field.name])) {
                        formObject[field.name] = [formObject[field.name]];
                    }
                    formObject[field.name].push(field.value);
                } else {
                    formObject[field.name] = field.value;
                }
            });
            
            return formObject;
        }
    };
});
