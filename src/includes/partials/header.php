    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JETXCEL S.A.S - Gestión de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- JETXCEL Modular CSS -->
    <link rel="stylesheet" href="/jetxcel2/public/assets/css/core.css">
    <link rel="stylesheet" href="/jetxcel2/public/assets/css/layout.css">
    <link rel="stylesheet" href="/jetxcel2/public/assets/css/components.css">
    
    <?php
    // Load page-specific CSS based on current page
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $css_files = [
        'ventas' => '/jetxcel2/public/assets/css/ventas.css',
        'compras' => '/jetxcel2/public/assets/css/compras.css',
        'servicios_tecnicos' => '/jetxcel2/public/assets/css/servicios-tecnicos.css'
    ];
    
    if (isset($css_files[$current_page])) {
        echo '<link rel="stylesheet" href="' . $css_files[$current_page] . '">';
    }
    ?>
</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar collapsed" id="sidebar">
        <!-- Botón de toggle dentro del sidebar -->
        <button class="toggle-sidebar-btn" id="toggleSidebarBtn">
            <i class="bi bi-list"></i>
        </button>
        
        <a href="dashboard.php" class="sidebar-icon">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="servicios_tecnicos.php" class="sidebar-icon">
            <i class="bi bi-wrench"></i>
            <span class="sidebar-text">Servicios Técnicos</span>
        </a>
        <a href="tienda.php" class="sidebar-icon">
            <i class="bi bi-shop"></i>
            <span class="sidebar-text">Tienda</span>
        </a>
        <a href="compras.php" class="sidebar-icon">
            <i class="bi bi-cart-plus"></i>
            <span class="sidebar-text">Compras</span>
        </a>
        <a href="ventas.php" class="sidebar-icon">
            <i class="bi bi-cart-check"></i>
            <span class="sidebar-text">Ventas</span>
        </a>
        <a href="ordenes.php" class="sidebar-icon">
            <i class="bi bi-card-list"></i>
            <span class="sidebar-text">Órdenes</span>
        </a>
        <a href="basedatos.php" class="sidebar-icon">
            <i class="bi bi-database"></i>
            <span class="sidebar-text">Base de Datos</span>
        </a>
        <a href="estadisticas.php" class="sidebar-icon">
            <i class="bi bi-graph-up"></i>
            <span class="sidebar-text">Estadísticas</span>
        </a>
        <a href="perfil.php" class="sidebar-icon">
            <i class="bi bi-person-circle"></i>
            <span class="sidebar-text">Perfil</span>
        </a>
        <a href="configuracion.php" class="sidebar-icon">
            <i class="bi bi-gear"></i>
            <span class="sidebar-text">Configuración</span>
        </a>
    </div>

    <button class="toggle-sidebar" id="mobileToggleSidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Contenido principal -->
    <div class="content-wrapper">
        <div class="main-content sidebar-collapsed" id="mainContent">
        <!-- Encabezado con JETXCEL S.A.S -->
        <div class="header">
            <div class="d-flex align-items-center">
                <span class="company-title">JETXCEL S.A.S</span>
                <input type="text" class="form-control search-bar" id="searchInput" placeholder="Buscar...">
            </div>
            <div class="action-buttons d-flex gap-2">
                <button class="btn btn-light rounded-circle p-2" title="Notificaciones">
                    <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-primary rounded-circle p-2" title="Agregar">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn btn-light rounded-circle p-2" title="Perfil">
                    <i class="bi bi-person-circle"></i>
                </button>
            </div>
        </div>

        <!-- Pestañas -->
        <div class="tabs-container">
            <div class="tab active" data-status="all"><span class="number" id="allCount">0</span> Todos</div>
            <div class="tab" data-status="pending"><span class="number" id="pendingCount">0</span> Pendientes</div>
            <div class="tab" data-status="in-progress"><span class="number" id="inProgressCount">0</span> En Progreso</div>
            <div class="tab" data-status="completed"><span class="number" id="completedCount">0</span> Completadas</div>
            <div class="tab" data-status="delayed"><span class="number" id="delayedCount">0</span> Retrasadas</div>
        </div>
