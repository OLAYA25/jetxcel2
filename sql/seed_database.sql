-- 1. Insertar clientes de ejemplo (solo si no existen)
INSERT IGNORE INTO clientes (nombre, nit, telefono, email, direccion, ciudad, estado) VALUES
('Cliente General', 'CF', '00000000', 'cliente@ejemplo.com', 'Ciudad', 'Guatemala', 'activo'),
('Empresa ABC, S.A.', '12345678', '12345678', 'info@empresaabc.com', '5ta calle 10-25 zona 1', 'Ciudad de Guatemala', 'activo'),
('Distribuidora XYZ', '87654321', '87654321', 'ventas@distribuidoraxyz.com', '12 avenida 5-30 zona 10', 'Quetzaltenango', 'activo'),
('Tienda El Ahorro', '45678912', '45678912', 'contacto@tiahorro.com', '3ra calle 8-45 zona 2', 'Antigua Guatemala', 'activo'),
('Supermercado La Esperanza', '78912345', '78912345', 'info@supermercadoesperanza.com', '7ma avenida 15-60 zona 3', 'Quetzaltenango', 'activo');

-- Verificar si hay categorías, si no, insertar algunas
INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
('Computadoras', 'Laptops y computadoras de escritorio'),
('Periféricos', 'Teclados, ratones y otros dispositivos de entrada'),
('Almacenamiento', 'Discos duros, memorias USB, etc.'),
('Monitores', 'Monitores y pantallas'),
('Impresoras', 'Impresoras y escáneres'),
('Audio', 'Audífonos, bocinas, etc.');

-- Insertar impuesto por defecto si no existe
INSERT IGNORE INTO impuestos (nombre, porcentaje, descripcion, activo) 
VALUES ('IVA', 12.00, 'Impuesto al Valor Agregado', 1);

-- 2. Insertar productos de ejemplo (solo si no existen)
-- Primero, obtener el ID de la categoría de Computadoras
SET @categoria_computadoras = (SELECT id FROM categorias WHERE nombre = 'Computadoras' LIMIT 1);
SET @categoria_perifericos = (SELECT id FROM categorias WHERE nombre = 'Periféricos' LIMIT 1);
SET @categoria_almacenamiento = (SELECT id FROM categorias WHERE nombre = 'Almacenamiento' LIMIT 1);
SET @categoria_monitores = (SELECT id FROM categorias WHERE nombre = 'Monitores' LIMIT 1);
SET @categoria_impresoras = (SELECT id FROM categorias WHERE nombre = 'Impresoras' LIMIT 1);
SET @categoria_audio = (SELECT id FROM categorias WHERE nombre = 'Audio' LIMIT 1);

-- Insertar productos
INSERT IGNORE INTO productos (nombre, referencia, fabricante, modelo, descripcion, costo_unitario, precio_venta_sin_iva, impuesto_id, categoria_id, stock, stock_minimo, estado) VALUES
('Laptop HP 15', 'HP15-DW1021LA', 'HP', '15-dw1021la', 'Laptop HP 15-dw1021la, 15.6", Intel Core i5, 8GB RAM, 256GB SSD', 5500.00, 6500.00, 1, @categoria_computadoras, 20, 2, 'activo'),
('Mouse Inalámbrico', 'M170', 'Logitech', 'M170', 'Mouse inalámbrico, 1000 DPI, 12 meses de duración de batería', 80.00, 120.00, 1, @categoria_perifericos, 50, 5, 'activo'),
('Teclado Mecánico', 'K552', 'Redragon', 'K552', 'Teclado mecánico gaming, retroiluminación RGB, switch Outemu Blue', 300.00, 450.00, 1, @categoria_perifericos, 15, 3, 'activo'),
('Disco Duro Externo 1TB', 'WDBA3A0010BBK', 'Western Digital', 'My Passport', 'Disco duro externo Western Digital, USB 3.0, color negro', 400.00, 550.00, 1, @categoria_almacenamiento, 30, 4, 'activo'),
('Memoria USB 64GB', 'DTLUX3/64GB', 'Kingston', 'DataTraveler', 'Memoria USB 3.2, velocidad de lectura hasta 200MB/s', 60.00, 90.00, 1, @categoria_almacenamiento, 100, 10, 'activo'),
('Monitor 24" Samsung FHD', 'LS24F354FHLXZX', 'Samsung', 'S24F354FHL', 'Monitor LED 24" Full HD, 75Hz, 5ms, HDMI, VGA', 1200.00, 1500.00, 1, @categoria_monitores, 12, 2, 'activo'),
('Impresora Multifuncional', '2776', 'HP', 'DeskJet 2776', 'Impresora multifuncional HP DeskJet 2776, WiFi, impresión móvil', 700.00, 850.00, 1, @categoria_impresoras, 8, 1, 'activo'),
('Audífonos Gamer', 'HX-HSCA-RD', 'HyperX', 'Cloud Stinger', 'Audífonos gaming con micrófono, sonido envolvente 7.1', 350.00, 500.00, 1, @categoria_audio, 25, 3, 'activo');

-- 3. Insertar ventas de ejemplo (solo si no existen)
-- Verificar si ya hay ventas
SET @venta_count = (SELECT COUNT(*) FROM ventas_cabecera);

-- Solo insertar si no hay ventas existentes
SET @insert_ventas = IF(@venta_count = 0, 1, 0);

-- Verificar si existe al menos un usuario
SET @usuario_id = (SELECT id FROM usuarios LIMIT 1);
SET @usuario_id = IFNULL(@usuario_id, 1); -- Usar 1 como valor por defecto si no hay usuarios

-- Venta 1
SET @fecha_venta1 = '2023-09-20 10:30:00';
SET @numero_factura1 = 'FAC-20230921-0001';

-- Verificar si la factura ya existe
SET @factura_existe1 = (SELECT COUNT(*) FROM ventas_cabecera WHERE numero_factura = @numero_factura1);

-- Insertar cabecera de venta 1 si no existe
INSERT INTO ventas_cabecera (cliente_id, numero_factura, fecha_factura, subtotal, impuestos, descuento, total, valor_recibido, vueltos, medio_pago, usuario_id, descripcion, estado) 
SELECT 2, @numero_factura1, DATE(@fecha_venta1), 7100.00, 0.00, 0.00, 7100.00, 7200.00, 100.00, 'Efectivo', @usuario_id, 'Venta al contado', 'completada'
WHERE @factura_existe1 = 0 AND @insert_ventas = 1;

-- Obtener el ID de la venta insertada
SET @venta_id1 = (SELECT id FROM ventas_cabecera WHERE numero_factura = @numero_factura1);

-- Insertar detalle de venta 1 si la venta fue insertada
INSERT IGNORE INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario_sin_iva, impuesto_id, porcentaje_impuesto, precio_unitario_con_iva)
SELECT @venta_id1, id, 
       CASE id 
           WHEN 1 THEN 1 
           WHEN 2 THEN 2 
           WHEN 8 THEN 1 
       END,
       CASE id 
           WHEN 1 THEN 6500.00
           WHEN 2 THEN 120.00
           WHEN 8 THEN 500.00
       END,
       1, 0,
       CASE id 
           WHEN 1 THEN 6500.00
           WHEN 2 THEN 120.00
           WHEN 8 THEN 500.00
       END
FROM productos 
WHERE id IN (1, 2, 8) AND @factura_existe1 = 0 AND @insert_ventas = 1;

-- Actualizar stock para venta 1
UPDATE productos p
JOIN (
    SELECT 1 as id, 1 as cantidad
    UNION ALL SELECT 2, 2
    UNION ALL SELECT 8, 1
) as venta_items ON p.id = venta_items.id
SET p.stock = p.stock - venta_items.cantidad
WHERE @factura_existe1 = 0 AND @insert_ventas = 1;

-- Venta 2
SET @fecha_venta2 = '2023-09-21 11:45:00';
SET @numero_factura2 = 'FAC-20230921-0002';

-- Verificar si la factura ya existe
SET @factura_existe2 = (SELECT COUNT(*) FROM ventas_cabecera WHERE numero_factura = @numero_factura2);

-- Insertar cabecera de venta 2 si no existe
INSERT INTO ventas_cabecera (cliente_id, numero_factura, fecha_factura, subtotal, impuestos, descuento, total, valor_recibido, vueltos, medio_pago, usuario_id, descripcion, estado) 
SELECT 3, @numero_factura2, DATE(@fecha_venta2), 2550.00, 0.00, 100.00, 2450.00, 2500.00, 50.00, 'Daviplata Edwin', @usuario_id, 'Venta con descuento por cliente frecuente', 'completada'
WHERE @factura_existe2 = 0 AND @insert_ventas = 1;

-- Obtener el ID de la venta insertada
SET @venta_id2 = (SELECT id FROM ventas_cabecera WHERE numero_factura = @numero_factura2);

-- Insertar detalle de venta 2 si la venta fue insertada
INSERT IGNORE INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario_sin_iva, impuesto_id, porcentaje_impuesto, precio_unitario_con_iva)
SELECT @venta_id2, id, 
       CASE id 
           WHEN 3 THEN 2 
           WHEN 4 THEN 3 
       END,
       CASE id 
           WHEN 3 THEN 450.00
           WHEN 4 THEN 550.00
       END,
       1, 0,
       CASE id 
           WHEN 3 THEN 450.00
           WHEN 4 THEN 550.00
       END
FROM productos 
WHERE id IN (3, 4) AND @factura_existe2 = 0 AND @insert_ventas = 1;

-- Actualizar stock para venta 2
UPDATE productos p
JOIN (
    SELECT 3 as id, 2 as cantidad
    UNION ALL SELECT 4, 3
) as venta_items ON p.id = venta_items.id
SET p.stock = p.stock - venta_items.cantidad
WHERE @factura_existe2 = 0 AND @insert_ventas = 1;

-- Venta 3 (más reciente)
SET @fecha_venta3 = NOW();
SET @numero_factura3 = CONCAT('FAC-', DATE_FORMAT(@fecha_venta3, '%Y%m%d'), '-0001');

-- Verificar si la factura ya existe
SET @factura_existe3 = (SELECT COUNT(*) FROM ventas_cabecera WHERE numero_factura = @numero_factura3);

-- Insertar cabecera de venta 3 si no existe
INSERT INTO ventas_cabecera (cliente_id, numero_factura, fecha_factura, subtotal, impuestos, descuento, total, valor_recibido, vueltos, medio_pago, usuario_id, descripcion, estado) 
SELECT 4, @numero_factura3, DATE(@fecha_venta3), 1500.00, 0.00, 0.00, 1500.00, 1500.00, 0.00, 'Efectivo', @usuario_id, 'Venta al contado', 'completada'
WHERE @factura_existe3 = 0 AND @insert_ventas = 1;

-- Obtener el ID de la venta insertada
SET @venta_id3 = (SELECT id FROM ventas_cabecera WHERE numero_factura = @numero_factura3);

-- Insertar detalle de venta 3 si la venta fue insertada
INSERT IGNORE INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario_sin_iva, impuesto_id, porcentaje_impuesto, precio_unitario_con_iva)
SELECT @venta_id3, id, 1, 1500.00, 1, 0, 1500.00
FROM productos 
WHERE id = 6 AND @factura_existe3 = 0 AND @insert_ventas = 1;

-- Actualizar stock para venta 3
UPDATE productos 
SET stock = stock - 1 
WHERE id = 6 AND @factura_existe3 = 0 AND @insert_ventas = 1;
