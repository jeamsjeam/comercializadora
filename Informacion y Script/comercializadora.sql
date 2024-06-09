-- Creacion de tablas

-- Tabla para almacenar las categorías de productos.
CREATE TABLE categorias (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre de la categoría de producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación de la categoría
);

-- Tabla para almacenar información sobre los productos disponibles en el inventario.
CREATE TABLE productos (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre del producto
    descripcion TEXT, -- Descripción del producto
    precio DECIMAL(10,2) NOT NULL, -- Precio del producto en la moneda base (USD)
    stock INT NOT NULL, -- Cantidad en stock del producto
    categoria_id BIGINT NOT NULL, -- Referencia a la categoría del producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del producto
    estado VARCHAR(20) DEFAULT 'Activo' -- Estado del producto (Activo/Inactivo)
);

-- Tabla para almacenar información sobre los clientes que realizan compras en el negocio.
CREATE TABLE clientes (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre del cliente
    cedula VARCHAR(20) UNIQUE, -- Cédula del cliente (se asume como única)
    telefono VARCHAR(20), -- Número de teléfono del cliente
    direccion TEXT, -- Dirección del cliente
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del cliente
    estado VARCHAR(20) DEFAULT 'Activo' -- Estado del cliente (Activo/Inactivo)
);

-- Tabla para registrar las facturas generadas para las ventas a los clientes.
CREATE TABLE facturas (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    cliente_id BIGINT NOT NULL, -- Referencia al cliente asociado a la factura
    fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, -- Fecha de emisión de la factura
    estado VARCHAR(20) NOT NULL, -- Estado de la factura (Por ejemplo: Pendiente, Pagada, Cancelada)
    total DECIMAL(10,2) NOT NULL, -- Total de la factura
    moneda_id BIGINT NOT NULL, -- Referencia a la moneda usada en la factura
    tasa_cambio DECIMAL(10, 4) NOT NULL DEFAULT 1, -- Tasa de cambio usada en la factura
    usuario_id BIGINT NOT NULL, -- Referencia al usuario que creó la factura
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación de la factura
);

-- Tabla para almacenar los detalles de las facturas, como los productos vendidos, la cantidad y el precio unitario.
CREATE TABLE detalles_factura (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    factura_id BIGINT NOT NULL, -- Referencia a la factura asociada
    producto_id BIGINT NOT NULL, -- Referencia al producto vendido
    cantidad INT NOT NULL, -- Cantidad de productos vendidos
    precio_unitario DECIMAL(10,2) NOT NULL, -- Precio unitario del producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del detalle de factura
);

-- Tabla para almacenar información sobre los proveedores de los productos.
CREATE TABLE proveedores (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre del proveedor
    telefono VARCHAR(20), -- Número de teléfono del proveedor
    direccion TEXT, -- Dirección del proveedor
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del proveedor
    estado VARCHAR(20) DEFAULT 'Activo' -- Estado del proveedor (Activo/Inactivo)
);

-- Tabla para registrar las compras realizadas por el negocio a los proveedores.
CREATE TABLE compras (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    proveedor_id BIGINT NOT NULL, -- Referencia al proveedor asociado a la compra
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, -- Fecha de la compra
    total DECIMAL(10,2) NOT NULL, -- Total de la compra
    moneda_id BIGINT NOT NULL, -- Referencia a la moneda usada en la compra
    tasa_cambio DECIMAL(10, 4) NOT NULL DEFAULT 1, -- Tasa de cambio usada en la compra
    usuario_id BIGINT NOT NULL, -- Referencia al usuario que creó la compra
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación de la compra
    estado VARCHAR(20) DEFAULT 'Activo' -- Estado de la compra (Activo/Inactivo)
);

-- Tabla para almacenar los detalles de las compras, como los productos comprados, la cantidad y el precio unitario.
CREATE TABLE detalles_compra (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    compra_id BIGINT NOT NULL, -- Referencia a la compra asociada
    producto_id BIGINT NOT NULL, -- Referencia al producto comprado
    cantidad INT NOT NULL, -- Cantidad de productos comprados
    precio_unitario DECIMAL(10,2) NOT NULL, -- Precio unitario del producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del detalle de compra
);

-- Tabla para almacenar roles de usuarios.
CREATE TABLE roles (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE, -- Nombre del rol (Administrador, Vendedor, etc.)
    descripcion TEXT, -- Descripción del rol
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del rol
);

-- Tabla para almacenar información sobre los usuarios del sistema.
CREATE TABLE usuarios (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre del usuario
    email VARCHAR(100) UNIQUE NOT NULL, -- Correo electrónico del usuario
    password VARCHAR(255) NOT NULL, -- Contraseña del usuario
    rol_id BIGINT NOT NULL, -- Referencia al rol del usuario
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del usuario
    estado VARCHAR(20) DEFAULT 'Activo' -- Estado del usuario (Activo/Inactivo)
);

-- Tabla para almacenar tipos de monedas aceptadas.
CREATE TABLE monedas (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE, -- Nombre de la moneda (Bolívar, Dólar, Peso Colombiano)
    simbolo VARCHAR(10) NOT NULL, -- Símbolo de la moneda (Bs, $, COL$)
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del registro
);

-- Tabla para almacenar el historial de tasas de cambio.
CREATE TABLE historial_tasas_cambio (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    moneda_id BIGINT NOT NULL, -- Referencia a la moneda
    tasa_cambio DECIMAL(10, 4) NOT NULL, -- Tasa de cambio respecto al dólar
    fecha DATE NOT NULL, -- Fecha de la tasa de cambio
    usuario_id BIGINT NOT NULL, -- Referencia al usuario que agregó la tasa de cambio
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del registro
);

-- Agregar clave foránea a la tabla productos
ALTER TABLE productos
ADD CONSTRAINT fk_productos_categorias
FOREIGN KEY (categoria_id)
REFERENCES categorias(id);

-- Agregar clave foránea a la tabla facturas
ALTER TABLE facturas
ADD CONSTRAINT fk_facturas_clientes
FOREIGN KEY (cliente_id)
REFERENCES clientes(id);

-- Agregar clave foránea a la tabla detalles_factura
ALTER TABLE detalles_factura
ADD CONSTRAINT fk_detalles_factura_facturas
FOREIGN KEY (factura_id)
REFERENCES facturas(id);

-- Agregar clave foránea a la tabla detalles_factura
ALTER TABLE detalles_factura
ADD CONSTRAINT fk_detalles_factura_productos
FOREIGN KEY (producto_id)
REFERENCES productos(id);

-- Agregar clave foránea a la tabla compras
ALTER TABLE compras
ADD CONSTRAINT fk_compras_proveedores
FOREIGN KEY (proveedor_id)
REFERENCES proveedores(id);

-- Agregar clave foránea a la tabla detalles_compra
ALTER TABLE detalles_compra
ADD CONSTRAINT fk_detalles_compra_compras
FOREIGN KEY (compra_id)
REFERENCES compras(id);

-- Agregar clave foránea a la tabla detalles_compra
ALTER TABLE detalles_compra
ADD CONSTRAINT fk_detalles_compra_productos
FOREIGN KEY (producto_id)
REFERENCES productos(id);

-- Agregar clave foránea a la tabla facturas
ALTER TABLE facturas
ADD CONSTRAINT fk_facturas_usuarios
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id);

-- Agregar clave foránea a la tabla compras
ALTER TABLE compras
ADD CONSTRAINT fk_compras_usuarios
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id);

-- Agregar clave foránea a la tabla usuarios
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_roles
FOREIGN KEY (rol_id)
REFERENCES roles(id);

-- Agregar clave foránea a la tabla historial_tasas_cambio
ALTER TABLE historial_tasas_cambio
ADD CONSTRAINT fk_historial_tasas_cambio_usuarios
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id);

-- Agregar clave foránea a la tabla historial_tasas_cambio
ALTER TABLE historial_tasas_cambio
ADD CONSTRAINT fk_historial_tasas_cambio_monedas
FOREIGN KEY (moneda_id)
REFERENCES monedas(id);


-- Procedimientos almacenados
DELIMITER $$

CREATE PROCEDURE ObtenerPrecioProducto(
    IN producto_id BIGINT,
    IN moneda_id BIGINT,
    IN fecha DATE
)
BEGIN
    IF fecha IS NULL THEN
        SET fecha = CURDATE();
    END IF;

    IF moneda_id IS NULL THEN
        -- Obtener precios en todas las monedas
        SELECT 
            p.nombre AS producto,
            m.nombre AS moneda,
            p.precio * h.tasa_cambio AS precio_convertido,
            h.tasa_cambio,
            h.fecha
        FROM 
            productos p
        JOIN 
            historial_tasas_cambio h ON h.moneda_id IN (SELECT id FROM monedas)
        JOIN 
            monedas m ON h.moneda_id = m.id
        WHERE 
            p.id = producto_id
            AND h.fecha = (
                SELECT MAX(fecha)
                FROM historial_tasas_cambio
                WHERE moneda_id = h.moneda_id
                AND fecha <= fecha
            );
    ELSE
        -- Obtener precio en la moneda especificada
        SELECT 
            p.nombre AS producto,
            m.nombre AS moneda,
            p.precio * h.tasa_cambio AS precio_convertido,
            h.tasa_cambio,
            h.fecha
        FROM 
            productos p
        JOIN 
            historial_tasas_cambio h ON h.moneda_id = moneda_id
        JOIN 
            monedas m ON h.moneda_id = m.id
        WHERE 
            p.id = producto_id
            AND h.fecha = (
                SELECT MAX(fecha)
                FROM historial_tasas_cambio
                WHERE moneda_id = moneda_id
                AND fecha <= fecha
            );
    END IF;
END $$

DELIMITER ;

CALL ObtenerPrecioProducto(1, 2, '2024-06-01'); -- Producto ID 1, Moneda ID 2 (Dólar), Fecha '2024-06-01'
CALL ObtenerPrecioProducto(1, NULL, '2024-06-01'); -- Producto ID 1, Todas las monedas, Fecha '2024-06-01'
CALL ObtenerPrecioProducto(1, NULL, NULL); -- Producto ID 1, Todas las monedas, Fecha actual


-- Insert

-- Insertar en la tabla categorias
INSERT INTO categorias (nombre) VALUES
('Bebidas'),
('Snacks'),
('Lácteos'),
('Carnes'),
('Frutas y Vegetales');

-- Insertar en la tabla productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, estado) VALUES
('Coca Cola', 'Bebida gaseosa 1L', 1.50, 100, 1, 'Activo'),
('Pepsi', 'Bebida gaseosa 1L', 1.45, 80, 1, 'Activo'),
('Doritos', 'Snacks de maíz', 2.00, 50, 2, 'Activo'),
('Yogurt', 'Yogurt natural 500ml', 3.00, 60, 3, 'Activo'),
('Manzana', 'Manzana roja', 0.75, 200, 5, 'Activo');

-- Insertar en la tabla clientes
INSERT INTO clientes (nombre, cedula, telefono, direccion, estado) VALUES
('Juan Perez', 'V-12345678', '04141234567', 'Calle 1, Ciudad', 'Activo'),
('Maria Lopez', 'V-87654321', '04149876543', 'Calle 2, Ciudad', 'Activo'),
('Carlos Martinez', 'V-23456789', '04141239876', 'Calle 3, Ciudad', 'Activo'),
('Ana Gomez', 'V-98765432', '04149988776', 'Calle 4, Ciudad', 'Activo'),
('Luis Rodriguez', 'V-34567890', '04142345678', 'Calle 5, Ciudad', 'Activo');

-- Insertar en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Administra el sistema'),
('Vendedor', 'Realiza ventas y gestiona clientes'),
('Almacenista', 'Gestiona el inventario'),
('Contador', 'Gestiona la contabilidad'),
('Gerente', 'Supervisa y administra las operaciones');

-- Insertar en la tabla usuarios
INSERT INTO usuarios (nombre, email, password, rol_id, estado) VALUES
('Admin', 'admin@correo.com', 'password1', 1, 'Activo'),
('Vendedor1', 'vendedor1@correo.com', 'password2', 2, 'Activo'),
('Vendedor2', 'vendedor2@correo.com', 'password3', 2, 'Activo'),
('Almacenista1', 'almacenista1@correo.com', 'password4', 3, 'Activo'),
('Contador1', 'contador1@correo.com', 'password5', 4, 'Activo');

-- Insertar en la tabla monedas
INSERT INTO monedas (nombre, simbolo) VALUES
('Dólar', '$'),
('Bolívar', 'Bs'),
('Peso Colombiano', 'COL$'),
('Euro', '€'),
('Libra Esterlina', '£');

-- Insertar en la tabla facturas
INSERT INTO facturas (cliente_id, fecha_factura, estado, total, moneda_id, tasa_cambio, usuario_id) VALUES
(1, '2024-06-01 10:00:00', 'Pagada', 15.50, 1, 1, 1),
(2, '2024-06-02 11:00:00', 'Pendiente', 30.00, 1, 1, 2),
(3, '2024-06-03 12:00:00', 'Cancelada', 20.00, 1, 1, 3),
(4, '2024-06-04 13:00:00', 'Pagada', 45.00, 1, 1, 4),
(5, '2024-06-05 14:00:00', 'Pendiente', 50.00, 1, 1, 5);

-- Insertar en la tabla detalles_factura
INSERT INTO detalles_factura (factura_id, producto_id, cantidad, precio_unitario) VALUES
(1, 1, 5, 1.50),
(1, 2, 5, 1.45),
(2, 3, 10, 2.00),
(2, 4, 10, 3.00),
(3, 5, 20, 0.75);

-- Insertar en la tabla proveedores
INSERT INTO proveedores (nombre, telefono, direccion, estado) VALUES
('Proveedor A', '04141112222', 'Avenida 1, Ciudad', 'Activo'),
('Proveedor B', '04142223333', 'Avenida 2, Ciudad', 'Activo'),
('Proveedor C', '04143334444', 'Avenida 3, Ciudad', 'Activo'),
('Proveedor D', '04144445555', 'Avenida 4, Ciudad', 'Activo'),
('Proveedor E', '04145556666', 'Avenida 5, Ciudad', 'Activo');

-- Insertar en la tabla compras
INSERT INTO compras (proveedor_id, fecha_compra, total, moneda_id, tasa_cambio, usuario_id, estado) VALUES
(1, '2024-06-01 10:00:00', 100.00, 1, 1, 1, 'Activo'),
(2, '2024-06-02 11:00:00', 200.00, 1, 1, 2, 'Activo'),
(3, '2024-06-03 12:00:00', 300.00, 1, 1, 3, 'Activo'),
(4, '2024-06-04 13:00:00', 400.00, 1, 1, 4, 'Activo'),
(5, '2024-06-05 14:00:00', 500.00, 1, 1, 5, 'Activo');

-- Insertar en la tabla detalles_compra
INSERT INTO detalles_compra (compra_id, producto_id, cantidad, precio_unitario) VALUES
(1, 1, 100, 1.50),
(1, 2, 100, 1.45),
(2, 3, 50, 2.00),
(2, 4, 60, 3.00),
(3, 5, 200, 0.75);

-- Insertar en la tabla historial_tasas_cambio
INSERT INTO historial_tasas_cambio (moneda_id, tasa_cambio, fecha, usuario_id) VALUES
(1, 1.0000, '2024-06-01', 1),
(2, 4.0000, '2024-06-01', 1),
(3, 4000.0000, '2024-06-01', 1),
(4, 0.8500, '2024-06-01', 1),
(5, 0.7500, '2024-06-01', 1);
