
-- DROP DATABASE IF EXISTS kunfumanager;
CREATE DATABASE kungfumanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kungfumanager;


-- TABLAS PRINCIPALES
CREATE TABLE usuario(
id_usuario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
apellidos VARCHAR(100),
pass VARCHAR(255) NOT NULL,
dni VARCHAR(100) NOT NULL,
telefono VARCHAR(30),
fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
email VARCHAR(100),
cinturon ENUM('BLANCO','BLANCO-AMARILLO','AMARILLO','AMARILLO-NARANJA','NARANJA',
			'NARANJA-VERDE','VERDE','VERDE-AZUL','AZUL','AZUL-MARRON','MARRON','NEGRO') DEFAULT NULL
)ENGINE=InnoDB;

-- GRUPOS, ALUMNOS_GRUPOS, CLASES --------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE grupos (
id_grupo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(120) NOT NULL,
descripcion TEXT,
nivel VARCHAR(80)
) ENGINE=InnoDB;

CREATE TABLE alumnos_grupos (
id_alumno BIGINT UNSIGNED NOT NULL,
id_grupo BIGINT UNSIGNED NOT NULL,
fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,

PRIMARY KEY (id_alumno,id_grupo),
FOREIGN KEY (id_alumno) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE clases (
id_clase BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_grupo BIGINT UNSIGNED NOT NULL,
id_profesor BIGINT UNSIGNED,
dia_semana TINYINT NOT NULL, -- 0 (domingo) ... 6 (sábado)
hora_inicio TIME NOT NULL,
hora_fin TIME NOT NULL,
gimnasio VARCHAR(80),

FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo) ON DELETE CASCADE,
FOREIGN KEY (id_profesor) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- AUSENCIAS --------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE ausencias_profesores (
id_ausencia_profesor BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_profesor BIGINT UNSIGNED NOT NULL,
fecha DATE NOT NULL,
motivo TEXT,
sustituido_por BIGINT UNSIGNED,

FOREIGN KEY (id_profesor) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
FOREIGN KEY (sustituido_por) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- INVENTARIO Y SOLICITUDES --------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE inventario (
id_item BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(150) NOT NULL,
tipo ENUM('equipamiento','material') NOT NULL DEFAULT 'material',
descripcion TEXT,
cantidad_total INT NOT NULL DEFAULT 1,
cantidad_disponible INT NOT NULL DEFAULT 0,
fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE solicitudes_inventario (
id_solicitud BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_solicitante BIGINT UNSIGNED,
id_item BIGINT UNSIGNED NOT NULL,
cantidad INT NOT NULL DEFAULT 1,
fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
estado ENUM('pendiente','aprobado','rechazado','cancelado') DEFAULT 'pendiente',
fecha_resolucion DATETIME,
id_admin_resolucion BIGINT UNSIGNED,

FOREIGN KEY (id_item) REFERENCES inventario(id_item) ON DELETE RESTRICT,
FOREIGN KEY (id_admin_resolucion) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
FOREIGN KEY (id_solicitante) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- FACTURACIÓN, RECIBOS, GASTOS --------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE clientes (
id_cliente BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre_responsable VARCHAR(200),
dni VARCHAR(30) NOT NULL,
direccion_facturacion TEXT,
telefono VARCHAR(30)
) ENGINE=InnoDB;

CREATE TABLE facturas (
id_factura BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre_cliente VARCHAR(100),
fecha_emision DATE NOT NULL,
tipo ENUM('ingreso','gasto') DEFAULT 'gasto',
estado ENUM('pendiente','pagado') DEFAULT 'pendiente',
total DECIMAL(10,2)
) ENGINE=InnoDB;

CREATE TABLE Detalles(
id_producto BIGINT UNSIGNED,
id_factura BIGINT UNSIGNED,
nombre VARCHAR(150) NOT NULL,
descripcion TEXT,
cantidad INT NOT NULL DEFAULT 1,
precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,

PRIMARY KEY (id_producto,id_factura),
FOREIGN KEY (id_producto) REFERENCES inventario(id_item) ON DELETE CASCADE,
FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE
)ENGINE = InnoDB;

CREATE TABLE recibos (
id_recibo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(150) NOT NULL,
concepto VARCHAR(255),
monto DECIMAL(10,2) NOT NULL,
fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
estado ENUM('emitido','anulado') DEFAULT 'emitido'
) ENGINE=InnoDB;

CREATE TABLE gastos_dojo (
id_gasto BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_factura BIGINT UNSIGNED NOT NULL, -- REFERENCIAR LA ID DE LA FACTURA
concepto VARCHAR(255) NOT NULL,
monto DECIMAL(10,2) NOT NULL,
fecha DATE NOT NULL,
estado ENUM('pendiente','pagado') DEFAULT 'pendiente',

FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE
) ENGINE=InnoDB;

-- PAGOS DE ENTRENAMIENTO --------------------------------------------------------------------------------------------------------------------------------------------
CREATE TABLE pagos_entrenamiento (
id_pago BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_alumno BIGINT UNSIGNED NOT NULL,
tipo ENUM('mensual','trimestral','anual') NOT NULL,
fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
fecha_inicio DATE,
fecha_fin DATE,
estado ENUM('confirmado','pendiente','anulado') DEFAULT 'pendiente',
metodo_pago ENUM('Efectivo','Tarjeta'),
monto DECIMAL(10,2) NOT NULL DEFAULT 0.00,

FOREIGN KEY (id_alumno) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- INDICES --------------------------------------------------------------------------------------------------------------------------------------------
