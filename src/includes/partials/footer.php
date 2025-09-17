        </div> <!-- Close main-content -->
    </div> <!-- Close content-wrapper -->
    
    <!-- footer -->
    <footer>
        <div class="container-fluid py-4" style="background-color: #1a2233;">
            <div class="row text-center text-white">
                <div class="col-md-6 mb-2 mb-md-0">
                    <span>&copy; <?php echo date('Y'); ?> JETXCEL S.A.S. Todos los derechos reservados.</span>
                </div>
                <div class="col-md-6">
                    <a href="mailto:soporte@jetxcel.com" class="text-white text-decoration-none me-3">
                        <i class="bi bi-envelope"></i> Contacto
                    </a>
                    <a href="tel:+573143594424" class="text-white text-decoration-none">
                        <i class="bi bi-telephone"></i> +57 314 359 4424
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Core JavaScript modules -->
    <script src="../../public/assets/js/utils.js"></script>
    <script src="../../public/assets/js/core.js"></script>
    <script src="../../public/assets/js/select2-config.js"></script>
    <script src="../../public/assets/js/modals.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    switch($currentPage) {
        case 'ventas.php':
            echo '<script src="../../public/assets/js/ventas.js"></script>';
            break;
        case 'compras.php':
            echo '<script src="../../public/assets/js/compras.js"></script>';
            break;
        case 'dashboard.php':
            echo '<script src="../../public/assets/js/dashboard.js"></script>';
            break;
        case 'servicios_tecnicos.php':
            echo '<script src="../../public/assets/js/servicios-tecnicos.js"></script>';
            break;
        // Add more cases as needed for other pages
    }
    ?>
</body>
</html>
