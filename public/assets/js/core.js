/**
 * Core functionality - Common scripts used across all pages
 * Includes sidebar management, navigation highlighting, and basic UI components
 */

$(document).ready(function() {
    // Resaltar enlace activo según la página actual
    const currentPage = window.location.pathname.split('/').pop();
    $('.sidebar-icon').removeClass('active');
    
    // Mapear páginas a enlaces del sidebar
    const pageMap = {
        'dashboard.php': 'dashboard.php',
        'servicios_tecnicos.php': 'src/views/servicios_tecnicos.php',
        'tienda.php': 'tienda.php',
        'compras.php': 'src/views/compras.php',
        'ventas.php': 'src/views/ventas.php',
        'ordenes.php': 'ordenes.php',
        'basedatos.php': 'basedatos.php',
        'estadisticas.php': 'estadisticas.php',
        'perfil.php': 'perfil.php',
        'configuracion.php': 'configuracion.php'
    };
    
    if (pageMap[currentPage]) {
        $(`.sidebar-icon[href="${pageMap[currentPage]}"]`).addClass('active');
    }
    
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Toggle sidebar - FUNCIÓN ACTUALIZADA
    $('#toggleSidebarBtn').click(function() {
        $('.sidebar').toggleClass('collapsed');
        $('.main-content').toggleClass('sidebar-collapsed');
    });
    
    // Mobile sidebar toggle
    $('#mobileToggleSidebar').click(function() {
        $('.sidebar').toggleClass('show');
    });
});
