<?php include '../includes/partials/header.php'; ?>

<!-- Filtro de búsqueda -->
<div class="filter-container d-flex justify-content-between align-items-center">
    <div class="services-title">SERVICIOS TÉCNICOS</div>
    <div class="d-flex align-items-center gap-2">
        <select class="form-select form-select-sm" id="technicianFilter" style="width: 150px;">
            <option value="">Todos los técnicos</option>
            <option value="FABIÁN">FABIÁN</option>
            <option value="FELIPE">FELIPE</option>
            <option value="MARLON">MARLON</option>
            <option value="EDWIN">EDWIN</option>
        </select>
        <select class="form-select form-select-sm" id="statusFilter" style="width: 150px;">
            <option value="">Todos los estados</option>
            <option value="pending">Pendiente</option>
            <option value="in-progress">En Progreso</option>
            <option value="completed">Completado</option>
            <option value="delayed">Retrasado</option>
        </select>
        <button class="add-service-btn" id="addServiceBtn">
            <i class="bi bi-plus-lg"></i> Agregar Servicio
        </button>
    </div>
</div>

<!-- Cuadrícula de tarjetas -->
<div class="card-grid" id="servicesGrid">
    <!-- Tarjetas se generarán dinámicamente -->
</div>

<!-- Mensaje cuando no hay servicios -->
<div id="noServicesMessage" class="no-services" style="display: none;">
    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
    <h5>No hay servicios técnicos</h5>
    <p class="text-muted">Agrega un nuevo servicio haciendo clic en el botón "Agregar Servicio"</p>
</div>

<!-- Barra lateral derecha -->
<div class="right-sidebar hidden">
    <!-- Botón de alternar -->
    <button class="toggle-sidebar-btn" id="toggleSidebar">
        <i class="bi bi-chevron-left"></i>
    </button>
    <h6 class="mb-3">Servicios Terminados Recientemente</h6>
    <div id="recentServicesList">
        <!-- Servicios recientes se generarán dinámicamente -->
    </div>
</div>

<!-- Modal para agregar/editar servicio -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Agregar Nuevo Servicio Técnico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="serviceForm">
                    <input type="hidden" id="serviceId">
                    <div class="mb-3">
                        <label for="serviceName" class="form-label">Nombre del Servicio:</label>
                        <input type="text" class="form-control" id="serviceName" placeholder="Ingrese el nombre del servicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="clientName" class="form-label">Cliente:</label>
                        <input type="text" class="form-control" id="clientName" placeholder="Ingrese el nombre del cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="clientPhone" class="form-label">Teléfono del Cliente:</label>
                        <input type="tel" class="form-control" id="clientPhone" placeholder="Ingrese el teléfono del cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceDescription" class="form-label">Descripción:</label>
                        <textarea class="form-control" id="serviceDescription" rows="3" placeholder="Ingrese la descripción del servicio" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="technicianName" class="form-label">Técnicos:</label>
                        <select class="form-select" id="technicianName" multiple required>
                            <option value="FABIÁN">FABIÁN</option>
                            <option value="FELIPE">FELIPE</option>
                            <option value="MARLON">MARLON</option>
                            <option value="EDWIN">EDWIN</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="serviceStatus" class="form-label">Estado:</label>
                        <select class="form-select" id="serviceStatus" required>
                            <option value="pending">Pendiente</option>
                            <option value="in-progress">En Progreso</option>
                            <option value="completed">Completado</option>
                            <option value="delayed">Retrasado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveServiceBtn">Guardar Servicio</button>
            </div>
        </div>
    </div>
</div>
</div> <!-- Cierre de main-content -->
<?php include '../includes/partials/footer.php'; ?>

<script>
    // JavaScript específico para servicios técnicos
    $(document).ready(function() {
        // Inicializar Select2
        $('#technicianFilter, #statusFilter').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Cargar servicios al inicializar
        loadServices();

        // Event listeners para filtros
        $('#technicianFilter, #statusFilter').on('change', function() {
            loadServices();
        });

        // Event listener para el botón de agregar servicio
        $('#addServiceBtn').on('click', function() {
            $('#serviceModal').modal('show');
        });

        // Event listener para guardar servicio
        $('#saveServiceBtn').on('click', function() {
            saveService();
        });

        // Event listeners para botones de acción en las tarjetas
        $(document).on('click', '.card-action-btn.edit', function() {
            const serviceId = $(this).closest('.dashboard-card').data('service-id');
            editService(serviceId);
        });

        $(document).on('click', '.card-action-btn.delete', function() {
            const serviceId = $(this).closest('.dashboard-card').data('service-id');
            deleteService(serviceId);
        });
    });

    function loadServices() {
        const technician = $('#technicianFilter').val();
        const status = $('#statusFilter').val();

        // Filtrar servicios según los criterios seleccionados
        displayFilteredServices(technician, status);
    }

    function displayFilteredServices(technicianFilter, statusFilter) {
        const allServices = [{
                id: 1,
                client: 'Juan Pérez Martínez',
                phone: '314-567-8901',
                device: 'Laptop HP Pavilion 15',
                problem: 'No enciende - Posible problema en la fuente',
                technician: 'FABIÁN',
                status: 'pending',
                date: '2024-12-08',
                priority: 'alta',
                estimatedTime: '2-3 días'
            },
            {
                id: 2,
                client: 'María García Rodríguez',
                phone: '320-987-6543',
                device: 'iPhone 12 Pro',
                problem: 'Pantalla rota - Cambio de display completo',
                technician: 'FELIPE',
                status: 'in-progress',
                date: '2024-12-07',
                priority: 'media',
                estimatedTime: '1 día'
            },
            {
                id: 3,
                client: 'Carlos López Sánchez',
                phone: '301-555-1234',
                device: 'PC Desktop Gamer',
                problem: 'Virus y malware - Formateo completo',
                technician: 'MARLON',
                status: 'completed',
                date: '2024-12-06',
                priority: 'baja',
                estimatedTime: 'Completado'
            },
            {
                id: 4,
                client: 'Ana Sofía Morales',
                phone: '315-444-7890',
                device: 'MacBook Air M1',
                problem: 'Teclado no responde - Cambio de teclado',
                technician: 'EDWIN',
                status: 'pending',
                date: '2024-12-08',
                priority: 'media',
                estimatedTime: '1-2 días'
            },
            {
                id: 5,
                client: 'Roberto Silva Castro',
                phone: '318-222-3456',
                device: 'Samsung Galaxy S23',
                problem: 'Batería se agota rápido - Cambio de batería',
                technician: 'FELIPE',
                status: 'in-progress',
                date: '2024-12-07',
                priority: 'media',
                estimatedTime: '4 horas'
            },
            {
                id: 6,
                client: 'Lucía Fernández Gómez',
                phone: '300-111-9876',
                device: 'Tablet iPad Pro',
                problem: 'No carga - Revisión puerto de carga',
                technician: 'FABIÁN',
                status: 'delayed',
                date: '2024-12-05',
                priority: 'alta',
                estimatedTime: 'Retrasado'
            },
            {
                id: 7,
                client: 'Miguel Ángel Torres',
                phone: '317-888-5432',
                device: 'PlayStation 5',
                problem: 'No lee discos - Limpieza de lector',
                technician: 'MARLON',
                status: 'completed',
                date: '2024-12-04',
                priority: 'media',
                estimatedTime: 'Completado'
            },
            {
                id: 8,
                client: 'Carmen Elena Ruiz',
                phone: '319-666-7777',
                device: 'Impresora HP LaserJet',
                problem: 'Atasco de papel constante - Mantenimiento',
                technician: 'EDWIN',
                status: 'pending',
                date: '2024-12-08',
                priority: 'baja',
                estimatedTime: '2 horas'
            }
        ];

        // Aplicar filtros
        let filteredServices = allServices;

        if (technicianFilter && technicianFilter !== '') {
            filteredServices = filteredServices.filter(service =>
                service.technician === technicianFilter
            );
        }

        if (statusFilter && statusFilter !== '') {
            filteredServices = filteredServices.filter(service =>
                service.status === statusFilter
            );
        }

        displayServices(filteredServices);
    }

    function displayServices(services) {
        let html = '';

        if (services && services.length > 0) {
            services.forEach(service => {
                html += createServiceCard(service);
            });
            $('#noServicesMessage').hide();
        } else {
            $('#noServicesMessage').show();
        }

        $('#servicesGrid').html(html);
        updateCounts(services || []);
    }

    function createServiceCard(service) {
        const statusClass = `status-${service.status}`;
        const statusText = getStatusText(service.status);
        const statusColor = getStatusColor(service.status);
        const priorityClass = `priority-${service.priority}`;
        const priorityIcon = getPriorityIcon(service.priority);

        return `
        <div class="dashboard-card" data-service-id="${service.id}">
            <div class="service-status ${statusClass}" style="background-color: ${statusColor};">${statusText}</div>
            <div class="card-actions">
                <div class="card-action-btn edit" title="Editar">
                    <i class="bi bi-pencil"></i>
                </div>
                <div class="card-action-btn delete" title="Eliminar">
                    <i class="bi bi-trash"></i>
                </div>
            </div>
            <div class="card-header ${priorityClass}"></div>
            
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">${service.client}</h6>
                <span class="priority-badge ${priorityClass}">
                    <i class="bi ${priorityIcon}"></i> ${service.priority.toUpperCase()}
                </span>
            </div>
            
            <div class="client-phone mb-2">
                <i class="bi bi-telephone"></i> ${service.phone}
            </div>
            
            <div class="service-details">
                <p class="mb-2"><strong><i class="bi bi-laptop"></i> Dispositivo:</strong><br>
                   <span class="text-muted">${service.device}</span></p>
                
                <p class="mb-2"><strong><i class="bi bi-exclamation-triangle"></i> Problema:</strong><br>
                   <span class="text-muted">${service.problem}</span></p>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <p class="mb-1"><strong><i class="bi bi-person-gear"></i> Técnico:</strong></p>
                        <span class="badge bg-primary">${service.technician}</span>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong><i class="bi bi-calendar"></i> Fecha:</strong></p>
                        <span class="text-muted small">${formatDate(service.date)}</span>
                    </div>
                </div>
                
                <div class="estimated-time mb-2">
                    <strong><i class="bi bi-clock"></i> Tiempo estimado:</strong>
                    <span class="badge bg-info ms-1">${service.estimatedTime}</span>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar ${getProgressColor(service.status)}"></div>
                <div class="progress-bar progress-blue" style="opacity: 0.3;"></div>
            </div>
        </div>
    `;
    }

    function getStatusText(status) {
        const statusMap = {
            'pending': 'Pendiente',
            'in-progress': 'En Progreso',
            'completed': 'Completado',
            'delayed': 'Retrasado'
        };
        return statusMap[status] || status;
    }

    function getPriorityIcon(priority) {
        const iconMap = {
            'alta': 'bi-exclamation-triangle-fill',
            'media': 'bi-exclamation-circle',
            'baja': 'bi-info-circle'
        };
        return iconMap[priority] || 'bi-info-circle';
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

    function getProgressColor(status) {
        const colorMap = {
            'pending': 'progress-red',
            'in-progress': 'progress-blue',
            'completed': 'progress-green',
            'delayed': 'progress-orange'
        };
        return colorMap[status] || 'progress-gray';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const diffTime = Math.abs(today - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) {
            return 'Hoy';
        } else if (diffDays === 1) {
            return 'Ayer';
        } else if (diffDays <= 7) {
            return `Hace ${diffDays} días`;
        } else {
            return date.toLocaleDateString('es-CO');
        }
    }

    function updateCounts(services) {
        const counts = {
            all: services.length,
            pending: services.filter(s => s.status === 'pending').length,
            'in-progress': services.filter(s => s.status === 'in-progress').length,
            completed: services.filter(s => s.status === 'completed').length,
            delayed: services.filter(s => s.status === 'delayed').length
        };

        $('#allCount').text(counts.all);
        $('#pendingCount').text(counts.pending);
        $('#inProgressCount').text(counts['in-progress']);
        $('#completedCount').text(counts.completed);
        $('#delayedCount').text(counts.delayed);
    }

    function saveService() {
        // Aquí iría la lógica para guardar el servicio
        // Por ahora, solo cerramos el modal
        $('#serviceModal').modal('hide');
        // Recargar servicios
        loadServices();
    }

    function editService(serviceId) {
        // Aquí iría la lógica para editar el servicio
        console.log('Editando servicio:', serviceId);
        $('#serviceModal').modal('show');
    }

    function deleteService(serviceId) {
        if (confirm('¿Está seguro de que desea eliminar este servicio?')) {
            // Aquí iría la lógica para eliminar el servicio
            console.log('Eliminando servicio:', serviceId);
            loadServices();
        }
    }
</script>
