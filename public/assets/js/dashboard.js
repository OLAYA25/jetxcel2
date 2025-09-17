/**
 * Dashboard Module - Dashboard functionality and widgets
 * Handles dashboard-specific features like charts, statistics, and real-time updates
 */

$(document).ready(function() {
    // Dashboard initialization
    initializeDashboard();
    
    // Auto-refresh dashboard data every 5 minutes
    setInterval(refreshDashboardData, 300000);
    
    // Tab switching functionality
    $('.tab').click(function() {
        const status = $(this).data('status');
        switchTab(status);
    });
    
    // Initialize dashboard
    function initializeDashboard() {
        loadDashboardStats();
        loadRecentActivity();
        updateStatusCounts();
    }
    
    // Load dashboard statistics
    function loadDashboardStats() {
        // In a real implementation, this would make API calls
        const stats = {
            totalSales: 0,
            totalPurchases: 0,
            pendingOrders: 0,
            completedServices: 0
        };
        
        updateStatsDisplay(stats);
    }
    
    // Load recent activity
    function loadRecentActivity() {
        if (typeof JetxcelAPI !== 'undefined') {
            // Load recent sales
            JetxcelAPI.sales.getRecent(5)
                .done(function(data) {
                    updateRecentSales(data);
                })
                .fail(function() {
                    console.log('Could not load recent sales');
                });
        }
    }
    
    // Update status counts in tabs
    function updateStatusCounts() {
        // Simulate status counts - in real implementation, fetch from API
        const counts = {
            all: 25,
            pending: 8,
            inProgress: 12,
            completed: 5,
            delayed: 0
        };
        
        $('#allCount').text(counts.all);
        $('#pendingCount').text(counts.pending);
        $('#inProgressCount').text(counts.inProgress);
        $('#completedCount').text(counts.completed);
        $('#delayedCount').text(counts.delayed);
    }
    
    // Switch between tabs
    function switchTab(status) {
        $('.tab').removeClass('active');
        $(`.tab[data-status="${status}"]`).addClass('active');
        
        // Filter content based on status
        filterContentByStatus(status);
    }
    
    // Filter content by status
    function filterContentByStatus(status) {
        if (status === 'all') {
            $('.dashboard-card').show();
        } else {
            $('.dashboard-card').hide();
            $(`.dashboard-card[data-status="${status}"]`).show();
        }
    }
    
    // Update statistics display
    function updateStatsDisplay(stats) {
        // Update dashboard widgets with new statistics
        $('.stat-sales').text(JetxcelUtils.formatCurrency(stats.totalSales));
        $('.stat-purchases').text(JetxcelUtils.formatCurrency(stats.totalPurchases));
        $('.stat-pending').text(stats.pendingOrders);
        $('.stat-completed').text(stats.completedServices);
    }
    
    // Update recent sales display
    function updateRecentSales(sales) {
        const container = $('#recentSales');
        if (sales && sales.length > 0) {
            let html = '';
            sales.forEach(sale => {
                html += `
                    <div class="sale-item">
                        <div class="d-flex justify-content-between">
                            <strong>Venta #${sale.id}</strong>
                            <span class="text-success">${JetxcelUtils.formatCurrency(sale.total)}</span>
                        </div>
                        <div class="text-muted">Cliente: ${sale.client_name} • ${JetxcelUtils.formatDateTime(sale.created_at)}</div>
                    </div>
                `;
            });
            container.html(html);
        }
    }
    
    // Refresh dashboard data
    function refreshDashboardData() {
        loadDashboardStats();
        loadRecentActivity();
        updateStatusCounts();
    }
    
    // Export dashboard data
    window.exportDashboardData = function(format = 'excel') {
        JetxcelUtils.setLoading('#exportBtn', true);
        
        // Simulate export process
        setTimeout(() => {
            JetxcelUtils.setLoading('#exportBtn', false);
            JetxcelUtils.showMessage('success', `Datos exportados en formato ${format}`);
        }, 2000);
    };
    
    // Search functionality
    $('#searchInput').on('input', JetxcelUtils.debounce(function() {
        const query = $(this).val().toLowerCase();
        performDashboardSearch(query);
    }, 300));
    
    function performDashboardSearch(query) {
        if (!query) {
            $('.dashboard-card').show();
            return;
        }
        
        $('.dashboard-card').each(function() {
            const cardText = $(this).text().toLowerCase();
            if (cardText.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});
