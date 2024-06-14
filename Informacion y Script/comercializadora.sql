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

-- Tabla para almacenar las categorías de productos.
CREATE TABLE tipo_persona (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre de la categoría de producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación de la categoría
);

-- Tabla para almacenar información sobre los personas que realizan compras en el negocio.
CREATE TABLE personas (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre del persona
    cedula VARCHAR(20) UNIQUE, -- Cédula del persona (se asume como única)
	extrangero TINYINT(1) NOT NULL DEFAULT 0, -- Indica si es extrangero (0: No, 1: Sí)
    telefono VARCHAR(20), -- Número de teléfono del persona
    direccion TEXT, -- Dirección del persona
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del persona
    estado VARCHAR(20) DEFAULT 'Activo', -- Estado del persona (Activo/Inactivo)
    tipo_persona_id BIGINT NOT NULL -- Referencia al persona asociado a la factura
);

-- Tabla para almacenar las categorías de productos.
CREATE TABLE tipo_factura (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- Nombre de la categoría de producto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación de la categoría
);

-- Tabla para registrar las facturas generadas para las ventas a los personas.
CREATE TABLE facturas (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    persona_id BIGINT NOT NULL, -- Referencia al persona asociado a la factura
    fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, -- Fecha de emisión de la factura
    estado VARCHAR(20) NOT NULL, -- Estado de la factura (Por ejemplo: Pendiente, Pagada, Cancelada)
    total DECIMAL(10,2) NOT NULL, -- Total de la factura
    moneda_id BIGINT NOT NULL, -- Referencia a la moneda usada en la factura
    tasa_cambio DECIMAL(10, 4) NOT NULL DEFAULT 1, -- Tasa de cambio usada en la factura
    usuario_id BIGINT NOT NULL, -- Referencia al usuario que creó la factura
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación de la factura
    tipo_factura_id BIGINT NOT NULL -- Referencia al persona asociado a la factura
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
    usuario VARCHAR(100) UNIQUE NOT NULL, -- Nombre del usuario
    correo VARCHAR(100) UNIQUE NOT NULL, -- Correo electrónico del usuario
    clave VARCHAR(255) NOT NULL, -- Contraseña del usuario
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
ADD CONSTRAINT fk_tipo_facturas
FOREIGN KEY (tipo_factura_id)
REFERENCES tipo_factura(id);

-- Agregar clave foránea a la tabla facturas
ALTER TABLE facturas
ADD CONSTRAINT fk_facturas_personas
FOREIGN KEY (persona_id)
REFERENCES personas(id);

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

-- Agregar clave foránea a la tabla facturas
ALTER TABLE facturas
ADD CONSTRAINT fk_facturas_usuarios
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

-- Agregar clave foránea a la tabla facturas
ALTER TABLE personas
ADD CONSTRAINT fk_tipo_persona
FOREIGN KEY (tipo_persona_id)
REFERENCES tipo_persona(id);

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

-- Inserción de datos en la tabla categorias
INSERT INTO categorias (nombre) VALUES
('Frutas'),
('Verduras'),
('Carnes'),
('Lácteos'),
('Granos');

-- Inserción de datos en la tabla tipo_persona
INSERT INTO tipo_persona (nombre) VALUES
('Cliente'),
('Proveedor'),
('Empleado');

-- Inserción de datos en la tabla tipo_factura
INSERT INTO tipo_factura (nombre) VALUES
('Venta'),
('Compra');

-- Inserción de datos en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Usuario con acceso completo al sistema'),
('Vendedor', 'Usuario que puede gestionar ventas'),
('Cliente', 'Usuario que puede realizar compras');

-- Inserción de datos en la tabla monedas
INSERT INTO monedas (nombre, simbolo) VALUES
('Dólar', '$'),
('Bolívar', 'Bs'),
('Peso Colombiano', 'COL$');

-- Inserción de datos en la tabla usuarios
INSERT INTO usuarios (usuario, correo, clave, rol_id) VALUES
('admin', 'admin@example.com', '1234', 1),
('vendedor1', 'vendedor1@example.com', '1234', 2),
('cliente1', 'cliente1@example.com', '1234', 3),
('cliente2', 'cliente2@example.com', '1234', 3),
('vendedor2', 'vendedor2@example.com', '1234', 2);

-- Inserción de datos en la tabla personas
INSERT INTO personas (nombre, cedula, extrangero, telefono, direccion, tipo_persona_id) VALUES
('Juan Pérez', '12345678', 0, '04141234567', 'Calle Falsa 123', 1),
('María López', '87654321', 0, '04247654321', 'Avenida Siempre Viva 456', 1),
('Carlos Sánchez', '11223344', 1, '04121122334', 'Bulevar del Sol 789', 2),
('Ana Gómez', '55667788', 0, '04165566778', 'Calle Luna 101', 1),
('Pedro Fernández', '99887766', 0, '04269988776', 'Calle Sol 202', 3);

-- Inserción de datos en la tabla productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, estado) VALUES
('Manzanas', 'Manzanas frescas', 1.50, 100, 1, 'Activo'),
('Plátanos', 'Plátanos maduros', 0.80, 200, 1, 'Activo'),
('Tomates', 'Tomates rojos', 1.20, 150, 2, 'Activo'),
('Lechuga', 'Lechuga fresca', 0.90, 80, 2, 'Activo'),
('Pollo', 'Pollo entero', 5.00, 50, 3, 'Activo');

-- Inserción de datos en la tabla facturas
INSERT INTO facturas (persona_id, estado, total, moneda_id, tasa_cambio, usuario_id, tipo_factura_id) VALUES
(1, 'Pagada', 150.00, 1, 1, 2, 1),
(2, 'Pendiente', 80.00, 1, 1, 2, 1),
(3, 'Cancelada', 120.00, 2, 250000.0000, 1, 2),
(4, 'Pagada', 90.00, 3, 3800.0000, 2, 1),
(5, 'Pendiente', 50.00, 1, 1, 2, 1);

-- Inserción de datos en la tabla detalles_factura
INSERT INTO detalles_factura (factura_id, producto_id, cantidad, precio_unitario) VALUES
(1, 1, 50, 1.50),
(1, 3, 50, 1.20),
(2, 2, 100, 0.80),
(3, 5, 20, 5.00),
(4, 4, 100, 0.90),
(5, 1, 30, 1.50);

-- Inserción de datos en la tabla historial_tasas_cambio
INSERT INTO historial_tasas_cambio (moneda_id, tasa_cambio, fecha, usuario_id) VALUES
(2, 250000.0000, '2023-06-01', 1),
(3, 3800.0000, '2023-06-01', 1),
(2, 260000.0000, '2023-06-02', 1),
(3, 3850.0000, '2023-06-02', 1),
(2, 255000.0000, '2023-06-03', 1);
