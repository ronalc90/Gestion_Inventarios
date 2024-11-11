#DROP DATABASE IF EXISTS u781177445_limber;
CREATE DATABASE IF NOT EXISTS u781177445_limber;
USE u781177445_limber;

-- Creación de la tabla Usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    correo_electronico VARCHAR(100) UNIQUE,
    contrasena VARCHAR(255),
    rol ENUM('Administrador', 'Usuario', 'Empleado'),
    estado ENUM('Activo', 'Inactivo'),
    fecha_creacion DATETIME
);

-- Creación de la tabla Inventario
CREATE TABLE Inventario (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(100),
    cantidad_disponible INT,
    stock_minimo INT,
    fecha_ultima_actualizacion DATETIME
);

-- Creación de la tabla Entradas_Inventario
CREATE TABLE Entradas_Inventario (
    id_entrada INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    cantidad_entrada INT,
    fecha_entrada DATETIME,
    usuario_registro INT,
    FOREIGN KEY (id_producto) REFERENCES Inventario(id_producto),
    FOREIGN KEY (usuario_registro) REFERENCES Usuarios(id_usuario)
);

-- Creación de la tabla Salidas_Inventario
CREATE TABLE Salidas_Inventario (
    id_salida INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    cantidad_salida INT,
    fecha_salida DATETIME,
    usuario_registro INT,
    FOREIGN KEY (id_producto) REFERENCES Inventario(id_producto),
    FOREIGN KEY (usuario_registro) REFERENCES Usuarios(id_usuario)
);

-- Creación de la tabla Ventas
CREATE TABLE Ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_venta DATETIME,
    monto_total DECIMAL(10, 2),
    FOREIGN KEY (id_cliente) REFERENCES Usuarios(id_usuario)
);

-- Creación de la tabla Detalle_Ventas
CREATE TABLE Detalle_Ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT,
    id_producto INT,
    cantidad_vendida INT,
    precio_unitario DECIMAL(10, 2),
    FOREIGN KEY (id_venta) REFERENCES Ventas(id_venta),
    FOREIGN KEY (id_producto) REFERENCES Inventario(id_producto)
);

-- Creación de la tabla Proveedores
CREATE TABLE Proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100),
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion TEXT
);

-- Creación de la tabla Compras
CREATE TABLE Compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT,
    fecha_compra DATETIME,
    monto_total DECIMAL(10, 2),
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor)
);

-- Creación de la tabla Detalle_Compras
CREATE TABLE Detalle_Compras (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT,
    id_producto INT,
    cantidad_comprada INT,
    precio_unitario DECIMAL(10, 2),
    FOREIGN KEY (id_compra) REFERENCES Compras(id_compra),
    FOREIGN KEY (id_producto) REFERENCES Inventario(id_producto)
);


-- Creación de la tabla Incidentes_Soporte
CREATE TABLE Incidentes_Soporte (
    id_incidente INT AUTO_INCREMENT PRIMARY KEY,
    descripcion_incidente TEXT,
    fecha_reporte DATETIME,
    prioridad ENUM('Alta', 'Media', 'Baja'),
    estado_incidente ENUM('Abierto', 'En Proceso', 'Cerrado'),
    usuario_reporta INT,
    usuario_responsable INT,
    FOREIGN KEY (usuario_reporta) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (usuario_responsable) REFERENCES Usuarios(id_usuario)
);

-- Creación de la tabla Tareas_Mantenimiento
CREATE TABLE Tareas_Mantenimiento (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    descripcion_tarea TEXT,
    fecha_programada DATETIME,
    estado_tarea ENUM('Programada', 'Completada', 'Fallida'),
    usuario_responsable INT,
    FOREIGN KEY (usuario_responsable) REFERENCES Usuarios(id_usuario)
);

CREATE TABLE Reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte VARCHAR(50),         -- Tipo de reporte, como "ventas", "inventario", etc.
    fecha_generacion DATETIME,         -- Fecha en la que fue generado el reporte
    usuario_genero INT,                -- ID del usuario que generó el reporte (FK hacia Usuarios)
    formato_reporte VARCHAR(10),       -- Formato del reporte (por ejemplo, "PDF" o "Excel")
    FOREIGN KEY (usuario_genero) REFERENCES Usuarios(id_usuario)
);


CREATE TABLE Configuracion_Interfaz (
    id_configuracion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,                       -- Relación con la tabla Usuarios
    tema_color VARCHAR(50),               -- Preferencia del tema de color, como "claro" o "oscuro"
    tamano_fuente VARCHAR(10),            -- Tamaño de fuente preferido, como "pequeño", "mediano", "grande"
    modo_oscuro BOOLEAN DEFAULT 0,        -- Modo oscuro activado o desactivado
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) -- Llave foránea hacia la tabla Usuarios
);

CREATE TABLE Integracion_Externa (
    id_integracion INT AUTO_INCREMENT PRIMARY KEY,
    nombre_integracion VARCHAR(100) NOT NULL,   -- Nombre de la integración externa, por ejemplo "E-commerce"
    api_key VARCHAR(255) NOT NULL,              -- Clave API para la integración
    estado_integracion ENUM('Activa', 'Inactiva') DEFAULT 'Inactiva', -- Estado de la integración
    frecuencia_sincronizacion VARCHAR(50),      -- Frecuencia de sincronización, por ejemplo "diaria" o "semanal"
    ultimo_fallo DATETIME                       -- Fecha y hora del último fallo de integración, si aplica
);



select * from usuarios;

INSERT INTO Usuarios (nombre, correo_electronico, contrasena, rol, estado, fecha_creacion) VALUES
('Ronald', 'ron@correo.com', '$2y$10$.Fl7EXQb0N14yqbL8NIxlO94GlK6T5YgVJ31hYpN1TeDaabv2LqUC', 'Administrador', 'Activo', '2024-01-01 12:00:00');

INSERT INTO Usuarios (nombre, correo_electronico, contrasena, rol, estado, fecha_creacion) VALUES
('Carlos López', 'carlos@correo.com', '$2y$10$nAiXVPGAqvYgVDerQBxGnuEfiSNd/ASR8NL91RBRe2q3NARC4BdR.', 'Administrador', 'Activo', '2024-01-01 12:00:00'),
('Ana García', 'ana@correo.com', '$2y$10$nAiXVPGAqvYgVDerQBxGnuEfiSNd/ASR8NL91RBRe2q3NARC4BdR.', 'Usuario', 'Activo', '2024-01-02 13:00:00'),
('Luis Fernández', 'luis@correo.com', '$2y$10$nAiXVPGAqvYgVDerQBxGnuEfiSNd/ASR8NL91RBRe2q3NARC4BdR.', 'Empleado', 'Inactivo', '2024-01-03 14:00:00'),
('María Torres', 'maria@correo.com', '$2y$10$nAiXVPGAqvYgVDerQBxGnuEfiSNd/ASR8NL91RBRe2q3NARC4BdR.', 'Usuario', 'Activo', '2024-01-04 15:00:00'),
('Jorge Romero', 'jorge@correo.com', '$2y$10$nAiXVPGAqvYgVDerQBxGnuEfiSNd/ASR8NL91RBRe2q3NARC4BdR.', 'Administrador', 'Inactivo', '2024-01-05 16:00:00');


INSERT INTO Inventario (nombre_producto, cantidad_disponible, stock_minimo, fecha_ultima_actualizacion) VALUES
('Lápiz', 100, 20, '2024-01-01 10:00:00'),
('Cuaderno', 50, 10, '2024-01-02 11:00:00'),
('Bolígrafo', 200, 30, '2024-01-03 12:00:00'),
('Borrador', 75, 15, '2024-01-04 13:00:00'),
('Regla', 150, 25, '2024-01-05 14:00:00');


INSERT INTO Entradas_Inventario (id_producto, cantidad_entrada, fecha_entrada, usuario_registro) VALUES
(1, 50, '2024-01-06 10:00:00', 1),
(2, 30, '2024-01-06 11:00:00', 2),
(3, 100, '2024-01-06 12:00:00', 3),
(4, 20, '2024-01-06 13:00:00', 1),
(5, 40, '2024-01-06 14:00:00', 2);

INSERT INTO Salidas_Inventario (id_producto, cantidad_salida, fecha_salida, usuario_registro) VALUES
(1, 10, '2024-01-07 10:00:00', 2),
(2, 5, '2024-01-07 11:00:00', 1),
(3, 20, '2024-01-07 12:00:00', 3),
(4, 15, '2024-01-07 13:00:00', 1),
(5, 25, '2024-01-07 14:00:00', 2);

INSERT INTO Ventas (id_cliente, fecha_venta, monto_total) VALUES
(2, '2024-01-08 15:00:00', 150.50),
(3, '2024-01-08 16:00:00', 75.20),
(2, '2024-01-08 17:00:00', 220.30),
(4, '2024-01-08 18:00:00', 300.00),
(3, '2024-01-08 19:00:00', 125.75);

INSERT INTO Detalle_Ventas (id_venta, id_producto, cantidad_vendida, precio_unitario) VALUES
(1, 1, 10, 1.50),
(1, 2, 5, 2.00),
(2, 3, 20, 3.00),
(3, 4, 15, 2.50),
(4, 5, 25, 4.00);

INSERT INTO Proveedores (nombre_proveedor, telefono, email, direccion) VALUES
('Proveedor A', '123456789', 'proveedora@correo.com', 'Calle 1, Ciudad A'),
('Proveedor B', '987654321', 'proveedorb@correo.com', 'Avenida 2, Ciudad B'),
('Proveedor C', '456123789', 'proveedorc@correo.com', 'Calle 3, Ciudad C'),
('Proveedor D', '789456123', 'proveedord@correo.com', 'Avenida 4, Ciudad D'),
('Proveedor E', '321654987', 'proveedore@correo.com', 'Calle 5, Ciudad E');

INSERT INTO Compras (id_proveedor, fecha_compra, monto_total) VALUES
(1, '2024-01-09 10:00:00', 500.00),
(2, '2024-01-09 11:00:00', 300.00),
(3, '2024-01-09 12:00:00', 450.00),
(4, '2024-01-09 13:00:00', 700.00),
(5, '2024-01-09 14:00:00', 650.00);

INSERT INTO Detalle_Compras (id_compra, id_producto, cantidad_comprada, precio_unitario) VALUES
(1, 1, 50, 2.00),
(2, 2, 30, 1.80),
(3, 3, 100, 1.50),
(4, 4, 20, 2.50),
(5, 5, 40, 1.75);

INSERT INTO Incidentes_Soporte (descripcion_incidente, fecha_reporte, prioridad, estado_incidente, usuario_reporta, usuario_responsable) VALUES
('Error en el sistema al generar reportes', '2024-02-01 09:00:00', 'Alta', 'Abierto', 2, 1),
('Fallo en la conexión a la base de datos', '2024-02-02 10:30:00', 'Alta', 'En Proceso', 3, 1),
('Error al cargar el inventario', '2024-02-03 11:45:00', 'Media', 'Abierto', 4, 2),
('Problema con el acceso a la aplicación', '2024-02-04 14:20:00', 'Baja', 'Cerrado', 5, 3),
('Incidente de autenticación de usuario', '2024-02-05 16:50:00', 'Alta', 'En Proceso', 2, 3);


INSERT INTO Tareas_Mantenimiento (descripcion_tarea, fecha_programada, estado_tarea, usuario_responsable) VALUES
('Mantenimiento del servidor de base de datos', '2024-02-06 08:00:00', 'Programada', 1),
('Actualización de seguridad del sistema', '2024-02-07 09:30:00', 'Completada', 2),
('Limpieza de datos obsoletos del sistema', '2024-02-08 10:00:00', 'Fallida', 3),
('Optimización de consultas SQL en inventario', '2024-02-09 13:00:00', 'Completada', 2),
('Revisión de acceso a roles y permisos', '2024-02-10 15:30:00', 'Programada', 1);


INSERT INTO Reportes (tipo_reporte, fecha_generacion, usuario_genero, formato_reporte) VALUES
('Ventas Mensuales', '2024-02-01 10:00:00', 1, 'PDF'),
('Inventario Actual', '2024-02-02 12:30:00', 2, 'Excel'),
('Compras Realizadas', '2024-02-03 14:20:00', 3, 'PDF'),
('Soporte y Mantenimiento', '2024-02-04 09:15:00', 1, 'Excel'),
('Resumen de Actividades', '2024-02-05 11:45:00', 2, 'PDF');


INSERT INTO Configuracion_Interfaz (id_usuario, tema_color, tamano_fuente, modo_oscuro) VALUES
(1, 'claro', 'mediano', 0);  -- Ajusta el valor de id_usuario según el usuario en tu tabla Usuarios



select * from usuarios;


