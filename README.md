+--------------+       +--------------+       +---------------+
|  productos   |       |  proveedores |       |   compras     |
+--------------+       +--------------+       +---------------+
| id (PK)      |<---+  | id (PK)      |<---+  | id (PK)       |
| nombre       |    |  | nombre       |    |  | producto_id   |
| referencia   |    |  | telefono     |    |  | cantidad      |
| fabricante   |    |  | nit          |    |  | costo_unitario|
| modelo       |    |  | direccion    |    |  | precio_venta  |
| imagen       |    |  | ciudad       |    |  | proveedor_id  |
| categoria    |    |  | email        |    |  | medio_pago    |
| codigo_barras|    |  | rut_archivo  |    |  | fecha_compra  |
| descripcion  |    |  | descripcion  |    |  | descripcion   |
| costo_unitario|    |  +--------------+    |  +---------------+
| precio_venta |    |                       |
| stock        |    |                       |
+--------------+    |                       |
                    |                       |
+--------------+    |                       |
|   clientes   |    |                       |
+--------------+    |                       |
| id (PK)      |<---+                       |
| nombre       |                            |
| telefono     |                            |
| nit          |                            |  +---------------+
| direccion    |                            |  |   ventas      |
| ciudad       |                            |  +---------------+
| email        |                            +--| id (PK)       |
| rut_archivo  |                            |  | producto_id   |
| descripcion  |                            |  | cantidad      |
+--------------+                            |  | precio_unitario|
                                            |  | descuento     |
                                            |  | cliente_id    |
                                            |  | valor_recibido|
                                            |  | vueltos       |
                                            |  | medio_pago    |
                                            |  | fecha_venta   |
                                            |  | descripcion   |
                                            |  +---------------+
                                            |
                                            |  +-------------------+
                                            |  | movimientos_stock |
                                            +--+ id (PK)           |
                                               | producto_id       |
                                               | tipo              |
                                               | cantidad          |
                                               | fecha_movimiento  |
                                               | referencia_id     |
                                               | notas             |
                                               +-------------------+
