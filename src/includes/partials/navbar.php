<div class="header">
    <input type="text" class="form-control search-bar" id="searchInput" placeholder="Buscar...">
    <div class="action-buttons d-flex gap-2">
        <button class="btn btn-light rounded-circle p-2" title="Notificaciones">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                3
            </span>
        </button>
        <button class="btn btn-primary rounded-circle p-2" title="Agregar">
            <i class="bi bi-plus-lg"></i>
        </button>
        <button class="btn btn-light rounded-circle p-2" title="Perfil">
            <i class="bi bi-person-circle"></i>
        </button>
    </div>
</div>

<div class="tabs-container">
    <div class="tab active" data-status="all"><span class="number" id="allCount">12</span> Todos</div>
    <div class="tab" data-status="pending"><span class="number" id="pendingCount">3</span> Pendientes</div>
    <div class="tab" data-status="in-progress"><span class="number" id="inProgressCount">5</span> En Progreso</div>
    <div class="tab" data-status="completed"><span class="number" id="completedCount">7</span> Completadas</div>
    <div class="tab" data-status="delayed"><span class="number" id="delayedCount">2</span> Retrasadas</div>
</div>
