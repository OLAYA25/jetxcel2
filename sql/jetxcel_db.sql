-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS jetxcel_db;
USE jetxcel_db;

-- Tabla de Categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Usuarios con 4 roles y teléfono
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'desarrollador', 'tecnico', 'vendedor') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de Impuestos (IVA y otros)
CREATE TABLE impuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    porcentaje DECIMAL(5,2) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Productos con IVA de compra y venta separados
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    referencia VARCHAR(100),
    fabricante VARCHAR(100),
    modelo VARCHAR(100),
    imagen VARCHAR(255) COMMENT 'Ruta de la imagen del producto',
    categoria_id INT,
    codigo_barras VARCHAR(50) UNIQUE,
    descripcion TEXT,
    costo_unitario DECIMAL(10,2) NOT NULL,
    precio_venta_sin_iva DECIMAL(10,2) NOT NULL,
    impuesto_compra_id INT DEFAULT 1,  -- IVA que se paga al proveedor
    impuesto_id INT DEFAULT 1,         -- IVA que se cobra al cliente
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    ubicacion VARCHAR(100),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (impuesto_compra_id) REFERENCES impuestos(id),
    FOREIGN KEY (impuesto_id) REFERENCES impuestos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Proveedores
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    nit VARCHAR(50) UNIQUE,
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    email VARCHAR(100),
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    nit VARCHAR(50) UNIQUE,
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    email VARCHAR(100),
    rut_archivo VARCHAR(255) COMMENT 'Ruta del archivo RUT subido',
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Compras (cabecera)
CREATE TABLE compras_cabecera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    numero_factura VARCHAR(100),
    fecha_factura DATE,
    subtotal DECIMAL(10,2) NOT NULL,
    impuestos DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    medio_pago ENUM('Davivienda Marlon', 'Daviplata Edwin', 'Nequi Marlon', 'Efectivo', 'Cuenta por pagar') NOT NULL,
    usuario_id INT NOT NULL,
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada',
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Detalle de Compras
CREATE TABLE compras_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    impuesto_id INT,
    porcentaje_impuesto DECIMAL(5,2) NOT NULL,
    precio_venta_sugerido DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras_cabecera(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (impuesto_id) REFERENCES impuestos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Ventas (cabecera)
CREATE TABLE ventas_cabecera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    numero_factura VARCHAR(100),
    fecha_factura DATE,
    subtotal DECIMAL(10,2) NOT NULL,
    impuestos DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    valor_recibido DECIMAL(10,2) NOT NULL,
    vueltos DECIMAL(10,2) COMMENT 'Calculado automáticamente',
    medio_pago ENUM('Davivienda Marlon', 'Daviplata Edwin', 'Nequi Marlon', 'Efectivo', 'Cuenta por pagar') NOT NULL,
    usuario_id INT NOT NULL,
    descripcion TEXT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Detalle de Ventas
CREATE TABLE ventas_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario_sin_iva DECIMAL(10,2) NOT NULL,
    impuesto_id INT,
    porcentaje_impuesto DECIMAL(5,2) NOT NULL,
    precio_unitario_con_iva DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas_cabecera(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (impuesto_id) REFERENCES impuestos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para registrar cambios en el stock
CREATE TABLE movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referencia_id INT COMMENT 'ID de la compra/venta relacionada',
    referencia_tipo ENUM('compra', 'venta', 'ajuste') COMMENT 'Tipo de referencia',
    notas TEXT,
    usuario_id INT NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Devoluciones
CREATE TABLE devoluciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    motivo TEXT,
    fecha_devolucion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (venta_id) REFERENCES ventas_cabecera(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar valores predeterminados
INSERT INTO categorias (nombre, descripcion) VALUES
('Computadoras', 'Equipos de computo completos'),
('Componentes', 'Partes y componentes de computadoras'),
('Periféricos', 'Dispositivos externos para computadoras'),
('Software', 'Programas y aplicaciones'),
('Accesorios', 'Accesorios varios para computación');

INSERT INTO impuestos (nombre, porcentaje, descripcion) VALUES
('IVA 0%', 0.00, 'Productos exentos de IVA'),
('IVA 19%', 19.00, 'IVA general Colombia'),
('IVA 5%', 5.00, 'IVA reducido');

-- Insertar 7 usuarios con teléfono (2 admin, 2 desarrolladores, 2 técnicos, 1 vendedor)
INSERT INTO usuarios (nombre, email, telefono, password_hash, rol) VALUES
-- Administradores
('Edwin Marquez', 'edwinjavierjavier9411@gmail.com', '+57-300-123-4567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Merlon Gomez', 'admin2@empresa.com', '+57-301-234-5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),

-- Desarrolladores
('Olaya', 'cheolaya.11@gmail.com', '+57-302-345-6789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'desarrollador'),
('Anghelica', 'JhulietTibasosa30@gmail.com', '+57-303-456-7890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'desarrollador'),

-- Técnicos
('Fabian', 'tecnico1@empresa.com', '+57-304-567-8901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico'),
('Felipe', 'tecnico2@empresa.com', '+57-305-678-9012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico'),

-- Vendedor
('Juan David', 'vendedor1@empresa.com', '+57-306-789-0123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor');

-- Insertar algunos productos de ejemplo con diferentes combinaciones de IVA
INSERT INTO productos (nombre, referencia, fabricante, modelo, categoria_id, codigo_barras, descripcion, costo_unitario, precio_venta_sin_iva, impuesto_compra_id, impuesto_id, stock, stock_minimo, ubicacion) VALUES
-- Producto con IVA 19% en compra y venta
('Monitor Gaming 24"', 'MON-GAM-24', 'Samsung', 'S24G45', 1, '1234567890123', 'Monitor gaming 144Hz', 450000.00, 650000.00, 2, 2, 10, 2, 'Estante A1'),

-- Producto con IVA 19% en compra pero 0% en venta (libros técnicos)
('Libro Programación Java', 'LIB-JAVA-001', 'Editorial Tech', 'Java Professional', 4, '1234567890124', 'Libro avanzado de Java', 80000.00, 120000.00, 2, 1, 15, 5, 'Estante B2'),

-- Producto con IVA 0% en compra pero 19% en venta (producto importado exento)
('Teclado Mecánico Importado', 'TEC-MEC-IMP', 'Keychron', 'K8 Pro', 3, '1234567890125', 'Teclado mecánico wireless', 250000.00, 380000.00, 1, 2, 8, 3, 'Estante C3'),

-- Producto con IVA 5% en compra y venta (productos médicos o especiales)
('Silla Ergonómica', 'SILLA-ERG-01', 'ErgoChair', 'EC-200', 5, '1234567890126', 'Silla ergonómica para oficina', 320000.00, 450000.00, 3, 3, 5, 1, 'Estante D4');

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_productos_impuestos ON productos(impuesto_compra_id, impuesto_id);
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_referencia ON productos(referencia);
CREATE INDEX idx_productos_codigo_barras ON productos(codigo_barras);

CREATE INDEX idx_compras_proveedor ON compras_cabecera(proveedor_id);
CREATE INDEX idx_compras_usuario ON compras_cabecera(usuario_id);
CREATE INDEX idx_compras_fecha ON compras_cabecera(fecha_compra);
CREATE INDEX idx_compras_detalle_producto ON compras_detalle(producto_id);
CREATE INDEX idx_compras_detalle_impuesto ON compras_detalle(impuesto_id);

CREATE INDEX idx_ventas_cliente ON ventas_cabecera(cliente_id);
CREATE INDEX idx_ventas_usuario ON ventas_cabecera(usuario_id);
CREATE INDEX idx_ventas_fecha ON ventas_cabecera(fecha_venta);
CREATE INDEX idx_ventas_detalle_producto ON ventas_detalle(producto_id);
CREATE INDEX idx_ventas_detalle_impuesto ON ventas_detalle(impuesto_id);

CREATE INDEX idx_movimientos_producto ON movimientos_stock(producto_id);
CREATE INDEX idx_movimientos_usuario ON movimientos_stock(usuario_id);
CREATE INDEX idx_movimientos_fecha ON movimientos_stock(fecha_movimiento);

CREATE INDEX idx_devoluciones_venta ON devoluciones(venta_id);
CREATE INDEX idx_devoluciones_producto ON devoluciones(producto_id);
CREATE INDEX idx_devoluciones_usuario ON devoluciones(usuario_id);
