<!-- Modal para detalles del cliente -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientDetailsModalLabel">Información del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Nombre:</div>
                            <div class="detail-value" id="client-name">Juan Pérez</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Identificación:</div>
                            <div class="detail-value" id="client-id">1234567890</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Teléfono:</div>
                            <div class="detail-value" id="client-phone">+57 300 123 4567</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value" id="client-email">juan.perez@example.com</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Dirección:</div>
                            <div class="detail-value" id="client-address">Calle 123 #45-67, Bogotá</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Ciudad:</div>
                            <div class="detail-value" id="client-city">Bogotá D.C.</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tipo de cliente:</div>
                            <div class="detail-value" id="client-type">Premium</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Compras previas:</div>
                            <div class="detail-value" id="client-purchases">15</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Total gastado:</div>
                            <div class="detail-value" id="client-total">$8,450.00</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Última compra:</div>
                            <div class="detail-value" id="client-last">12/11/2023</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="mb-3">Historial de Compras</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12/11/2023</td>
                                    <td>Laptop HP Pavilion</td>
                                    <td>1</td>
                                    <td>$850.00</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>05/11/2023</td>
                                    <td>Mouse Inalámbrico</td>
                                    <td>2</td>
                                    <td>$70.00</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>28/10/2023</td>
                                    <td>Teclado Mecánico</td>
                                    <td>1</td>
                                    <td>$75.00</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="mb-3">Estado de Crédito</h6>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">Límite: $10,000</div>
                        </div>
                        <span class="ms-3 badge bg-success">Disponible: $1,550</span>
                    </div>
                    <div class="mt-2 text-muted small">
                        Límite de crédito: $10,000 | Utilizado: $8,450 | Disponible: $1,550
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="editClientBtn">
                    <i class="bi bi-pencil"></i> Editar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.detail-row {
    display: flex;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f1f1;
}

.detail-label {
    font-weight: 600;
    min-width: 140px;
    color: #4a5568;
}

.detail-value {
    flex: 1;
    color: #2d3748;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
</style>

<script>
// Script para cargar datos del cliente cuando se muestra el modal
$('#clientDetailsModal').on('show.bs.modal', function (event) {
    // En una implementación real, aquí se cargarían los datos del cliente seleccionado
    const clientId = $('#clientSelect').val();
    console.log('Cargando información del cliente ID:', clientId);
    
    // Simulación de carga de datos (en producción se haría una petición AJAX)
    // $('#client-name').text('Nombre del cliente');
    // $('#client-id').text('Identificación del cliente');
    // ... etc
});
</script>
