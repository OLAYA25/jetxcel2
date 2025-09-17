/**
 * Servicios Técnicos Module - Technical services functionality
 * Handles service management, client interactions, and service status updates
 */

$(document).ready(function() {
    // Initialize services module
    initializeServices();
    
    // Service card interactions
    $(document).on('click', '.dashboard-card', function() {
        const serviceId = $(this).data('service-id');
        if (serviceId) {
            showServiceDetails(serviceId);
        }
    });
    
    // Add new service button
    $('.add-service-btn').click(function() {
        $('#serviceModal').modal('show');
    });
    
    // Edit service buttons
    $(document).on('click', '.card-action-btn.edit', function(e) {
        e.stopPropagation();
        const serviceId = $(this).closest('.dashboard-card').data('service-id');
        editService(serviceId);
    });
    
    // Delete service buttons
    $(document).on('click', '.card-action-btn.delete', function(e) {
        e.stopPropagation();
        const serviceId = $(this).closest('.dashboard-card').data('service-id');
        deleteService(serviceId);
    });
    
    // Status filter
    $('#statusFilter').change(function() {
        const status = $(this).val();
        filterServicesByStatus(status);
    });
    
    // Priority filter
    $('#priorityFilter').change(function() {
        const priority = $(this).val();
        filterServicesByPriority(priority);
    });
    
    // Search services
    $('#serviceSearch').on('input', JetxcelUtils.debounce(function() {
        const query = $(this).val();
        searchServices(query);
    }, 300));
    
    function initializeServices() {
        loadServices();
        updateServiceCounts();
    }
    
    function loadServices() {
        // In a real implementation, load from API
        const services = getServicesMockData();
        renderServices(services);
    }
    
    function renderServices(services) {
        const container = $('.card-grid');
        let html = '';
        
        services.forEach(service => {
            html += createServiceCard(service);
        });
        
        container.html(html);
    }
    
    function createServiceCard(service) {
        const statusClass = getStatusClass(service.status);
        const priorityBadge = getPriorityBadge(service.priority);
        
        return `
            <div class="dashboard-card" data-service-id="${service.id}" data-status="${service.status}" data-priority="${service.priority}">
                <div class="card-header" style="background-color: ${getStatusColor(service.status)};"></div>
                <div class="service-status ${statusClass}" style="background-color: ${getStatusColor(service.status)};">${getStatusText(service.status)}</div>
                <div class="card-actions">
                    <button class="card-action-btn edit" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="card-action-btn delete" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <h6 class="card-title">${service.title}</h6>
                <p class="card-text">${service.description}</p>
                <div class="client-info">
                    <strong>${service.client_name}</strong>
                    <div class="client-phone">${service.client_phone}</div>
                </div>
                <div class="service-details mt-2">
                    <small class="text-muted">
                        Creado: ${JetxcelUtils.formatDate(service.created_at)}<br>
                        ${priorityBadge}
                    </small>
                </div>
                <div class="progress-container">
                    <div class="progress-bar progress-green" style="width: ${service.progress}%;"></div>
                </div>
            </div>
        `;
    }
    
    function showServiceDetails(serviceId) {
        // Load service details and show in modal
        const service = getServiceById(serviceId);
        if (service) {
            const modalContent = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Servicio</h6>
                        <p><strong>ID:</strong> #${service.id}</p>
                        <p><strong>Título:</strong> ${service.title}</p>
                        <p><strong>Estado:</strong> ${getStatusText(service.status)}</p>
                        <p><strong>Prioridad:</strong> ${service.priority}</p>
                        <p><strong>Progreso:</strong> ${service.progress}%</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Información del Cliente</h6>
                        <p><strong>Nombre:</strong> ${service.client_name}</p>
                        <p><strong>Teléfono:</strong> ${service.client_phone}</p>
                        <p><strong>Email:</strong> ${service.client_email || 'No especificado'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Descripción</h6>
                        <p>${service.description}</p>
                        <h6>Notas Técnicas</h6>
                        <p>${service.technical_notes || 'Sin notas adicionales'}</p>
                    </div>
                </div>
            `;
            
            showInfoModal(`Detalles del Servicio #${service.id}`, modalContent);
        }
    }
    
    // Handle form submission for the existing modal
    $('#saveServiceBtn').click(function() {
        const formData = {
            serviceName: $('#serviceName').val(),
            clientName: $('#clientName').val(),
            clientPhone: $('#clientPhone').val(),
            serviceDescription: $('#serviceDescription').val(),
            technicianName: $('#technicianName').val(),
            serviceStatus: $('#serviceStatus').val()
        };
        
        createNewService(formData);
    });
    
    function createNewService(formData) {
        JetxcelUtils.showMessage('success', 'Servicio creado exitosamente');
        $('#serviceModal').modal('hide');
        
        // Clear the form
        $('#serviceForm')[0].reset();
        
        loadServices(); // Reload services
    }
    
    function editService(serviceId) {
        const service = getServiceById(serviceId);
        if (service) {
            // Similar to new service modal but pre-filled with existing data
            JetxcelUtils.showMessage('info', 'Funcionalidad de edición en desarrollo');
        }
    }
    
    function deleteService(serviceId) {
        showConfirmModal(
            'Eliminar Servicio',
            '¿Está seguro de que desea eliminar este servicio? Esta acción no se puede deshacer.',
            function() {
                JetxcelUtils.showMessage('success', 'Servicio eliminado exitosamente');
                loadServices(); // Reload services
            }
        );
    }
    
    function filterServicesByStatus(status) {
        if (!status) {
            $('.dashboard-card').show();
            return;
        }
        
        $('.dashboard-card').hide();
        $(`.dashboard-card[data-status="${status}"]`).show();
    }
    
    function filterServicesByPriority(priority) {
        if (!priority) {
            $('.dashboard-card').show();
            return;
        }
        
        $('.dashboard-card').hide();
        $(`.dashboard-card[data-priority="${priority}"]`).show();
    }
    
    function searchServices(query) {
        if (!query) {
            $('.dashboard-card').show();
            return;
        }
        
        $('.dashboard-card').each(function() {
            const cardText = $(this).text().toLowerCase();
            if (cardText.includes(query.toLowerCase())) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    function updateServiceCounts() {
        const services = getServicesMockData();
        const counts = {
            all: services.length,
            pending: services.filter(s => s.status === 'pending').length,
            inProgress: services.filter(s => s.status === 'in-progress').length,
            completed: services.filter(s => s.status === 'completed').length,
            delayed: services.filter(s => s.status === 'delayed').length
        };
        
        $('#allCount').text(counts.all);
        $('#pendingCount').text(counts.pending);
        $('#inProgressCount').text(counts.inProgress);
        $('#completedCount').text(counts.completed);
        $('#delayedCount').text(counts.delayed);
    }
    
    // Helper functions
    function getStatusClass(status) {
        const classes = {
            'pending': 'status-pending',
            'in-progress': 'status-in-progress',
            'completed': 'status-completed',
            'delayed': 'status-delayed'
        };
        return classes[status] || 'status-pending';
    }
    
    function getStatusColor(status) {
        const colors = {
            'pending': '#f59e0b',      // Amarillo para pendiente
            'in-progress': '#f59e0b',   // Amarillo para en proceso
            'completed': '#22c55e',     // Verde para completado
            'delayed': '#ef4444'        // Rojo para retrasado
        };
        return colors[status] || '#f59e0b';
    }
    
    function getStatusText(status) {
        const texts = {
            'pending': 'Pendiente',
            'in-progress': 'En Progreso',
            'completed': 'Completado',
            'delayed': 'Retrasado'
        };
        return texts[status] || 'Pendiente';
    }
    
    function getPriorityBadge(priority) {
        const badges = {
            'low': '<span class="badge bg-secondary">Baja</span>',
            'medium': '<span class="badge bg-warning">Media</span>',
            'high': '<span class="badge bg-danger">Alta</span>'
        };
        return badges[priority] || badges['medium'];
    }
    
    function getServiceById(id) {
        const services = getServicesMockData();
        return services.find(s => s.id == id);
    }
    
    function getServicesMockData() {
        return [
            {
                id: 1,
                title: 'Reparación Laptop HP',
                description: 'Laptop no enciende, posible problema con fuente de poder',
                client_name: 'Juan Pérez',
                client_phone: '+57 300 123 4567',
                client_email: 'juan@example.com',
                status: 'in-progress',
                priority: 'high',
                progress: 75,
                created_at: '2023-11-10',
                technical_notes: 'Se reemplazó la fuente de poder, pendiente pruebas finales'
            },
            {
                id: 2,
                title: 'Instalación Windows 11',
                description: 'Formateo completo e instalación de Windows 11',
                client_name: 'María González',
                client_phone: '+57 301 234 5678',
                status: 'pending',
                priority: 'medium',
                progress: 0,
                created_at: '2023-11-12'
            }
        ];
    }
});
