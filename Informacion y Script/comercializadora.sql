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
    precio DECIMAL(10,2) NOT NULL, -- Precio del producto en la moneda base
    descuento DECIMAL(10,2) NOT NULL, -- Precio del producto en la moneda base 
    cantidad_descuento int NOT NULL, -- Precio del producto en la moneda base 
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
    cedula VARCHAR(20) NOT NULL, -- Cédula del persona (se asume como única)
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
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación del registro
	principal TINYINT(1) NOT NULL DEFAULT 0 -- Indica si es principal (0: No, 1: Sí)
);

-- Tabla para almacenar tasas de cambio.
CREATE TABLE tasas_cambio (
    id BIGINT AUTO_INCREMENT  PRIMARY KEY,
    moneda_id BIGINT NOT NULL, -- Referencia a la moneda
    tasa DECIMAL(10, 4) NOT NULL, -- Tasa de cambio respecto al dólar
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

-- Agregar clave foránea a la tabla facturas
ALTER TABLE facturas
ADD CONSTRAINT fk_facturas_moneda
FOREIGN KEY (moneda_id)
REFERENCES monedas(id);

-- Agregar clave foránea a la tabla usuarios
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuarios_roles
FOREIGN KEY (rol_id)
REFERENCES roles(id);

-- Agregar clave foránea a la tabla tasas_cambio
ALTER TABLE tasas_cambio
ADD CONSTRAINT fk_tasas_cambio_usuarios
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id);

-- Agregar clave foránea a la tabla tasas_cambio
ALTER TABLE tasas_cambio
ADD CONSTRAINT fk_tasas_cambio_monedas
FOREIGN KEY (moneda_id)
REFERENCES monedas(id);

-- Agregar clave foránea a la tabla facturas
ALTER TABLE personas
ADD CONSTRAINT fk_tipo_persona
FOREIGN KEY (tipo_persona_id)
REFERENCES tipo_persona(id);

-- Insert iniciales

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
('Proveedor');
-- ('Empleado');

-- Inserción de datos en la tabla tipo_factura
INSERT INTO tipo_factura (nombre) VALUES
('Venta'),
('Compra');

-- Inserción de datos en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Usuario con acceso completo al sistema'),
('Vendedor', 'Usuario que puede gestionar ventas');

-- Inserción de datos en la tabla monedas
INSERT INTO monedas (nombre, simbolo, principal) VALUES
('Dólar', '$', 1),
('Bolívar', 'Bs', 0),
('Peso Colombiano', 'COL$', 0);

-- Inserción de datos en la tabla usuarios
INSERT INTO usuarios (usuario, correo, clave, rol_id) VALUES
('admin', 'admin@example.com', '1234', 1)