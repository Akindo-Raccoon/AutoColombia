


-- BD PARA PROXIMO SERVICIO SQL CONNECT DE FIREBASE





-- CREAR BASE DE DATOS
CREATE DATABASE concesionario_db;

-- Conectarse a la base de datos antes de ejecutar el resto
\c concesionario_db;

CREATE TYPE tipo_combustible_enum AS ENUM (
	'gasolina',
	'diesel',
	'hibrido',
	'electrico',
	'gas'
);

CREATE TYPE estado_automovil_enum AS ENUM (
	'disponible',
	'vendido',
	'reservado'
);

CREATE TYPE ubicacion_enum AS ENUM (
	'concesionario',
	'servicio_oficial'
);

CREATE TYPE tipo_pago_enum AS ENUM (
	'contado',
	'financiera'
);

CREATE TYPE tipo_vendedor_enum AS ENUM (
	'vendedor',
	'servicio_oficial'
);

CREATE TYPE perfil_usuario_enum AS ENUM (
	'admin',
	'vendedor',
	'usuario'
);

-- TABLA: CONCESIONARIO

CREATE TABLE concesionario (
	id_concesionario SERIAL PRIMARY KEY,
	nombre           VARCHAR(100) NOT NULL,
	domicilio        VARCHAR(200) NOT NULL,
	nif              VARCHAR(20) NOT NULL UNIQUE,
	telefono         VARCHAR(20),
	email            VARCHAR(100)
);

-- TABLA: SERVICIO_OFICIAL

CREATE TABLE servicio_oficial (
	id_servicio      SERIAL PRIMARY KEY,
	nombre           VARCHAR(100) NOT NULL,
	domicilio        VARCHAR(200) NOT NULL,
	nif              VARCHAR(20) NOT NULL UNIQUE,
	telefono         VARCHAR(20),
	email            VARCHAR(100),
	id_concesionario INT NOT NULL,
	FOREIGN KEY (id_concesionario)
		REFERENCES concesionario(id_concesionario)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
);

-- TABLA: VENDEDOR

CREATE TABLE vendedor (
	id_vendedor        SERIAL PRIMARY KEY,
	nombre             VARCHAR(100) NOT NULL,
	apellidos          VARCHAR(150) NOT NULL,
	nif                VARCHAR(20) NOT NULL UNIQUE,
	domicilio          VARCHAR(200),
	telefono           VARCHAR(20),
	email              VARCHAR(100),
	fecha_contratacion DATE,
	activo             BOOLEAN DEFAULT TRUE,
	id_concesionario   INT NOT NULL,
	FOREIGN KEY (id_concesionario)
		REFERENCES concesionario(id_concesionario)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
);

-- TABLA: MARCA

CREATE TABLE marca (
	id_marca    SERIAL PRIMARY KEY,
	nombre      VARCHAR(50) NOT NULL UNIQUE,
	pais_origen VARCHAR(50),
	descripcion TEXT
);

-- TABLA: CONCESIONARIO_MARCA

CREATE TABLE concesionario_marca (
	id_concesionario INT NOT NULL,
	id_marca         INT NOT NULL,
	fecha_acuerdo    DATE,
	PRIMARY KEY (id_concesionario, id_marca),
	FOREIGN KEY (id_concesionario)
		REFERENCES concesionario(id_concesionario)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
	FOREIGN KEY (id_marca)
		REFERENCES marca(id_marca)
			ON UPDATE CASCADE
			ON DELETE CASCADE
);

-- TABLA: MODELO

CREATE TABLE modelo (
	id_modelo        SERIAL PRIMARY KEY,
	nombre           VARCHAR(100) NOT NULL,
	id_marca         INT NOT NULL,
	precio_base      DECIMAL(12,2) NOT NULL,
	descuento        DECIMAL(5,2) DEFAULT 0.00,
	potencia_fiscal  DECIMAL(8,2),
	cilindrada       INT,
	tipo_combustible tipo_combustible_enum,
	num_puertas      INT,
	num_plazas       INT,
	descripcion      TEXT,
	activo           BOOLEAN DEFAULT TRUE,
	FOREIGN KEY (id_marca)
		REFERENCES marca(id_marca)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
);

-- TABLA: CARACTERISTICA

CREATE TABLE caracteristica (
	id_caracteristica SERIAL PRIMARY KEY,
	nombre            VARCHAR(100) NOT NULL,
	descripcion       TEXT,
	categoria         VARCHAR(50)
);

-- TABLA: MODELO_CARACTERISTICA_SERIE

CREATE TABLE modelo_caracteristica_serie (
	id_modelo         INT NOT NULL,
	id_caracteristica INT NOT NULL,
	PRIMARY KEY (id_modelo, id_caracteristica),
	FOREIGN KEY (id_modelo)
		  REFERENCES modelo(id_modelo)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
	FOREIGN KEY (id_caracteristica)
		REFERENCES caracteristica(id_caracteristica)
			ON UPDATE CASCADE
			ON DELETE CASCADE
);

-- TABLA: EXTRA

CREATE TABLE extra (
	id_extra    SERIAL PRIMARY KEY,
	nombre      VARCHAR(100) NOT NULL,
	descripcion TEXT,
	categoria   VARCHAR(50)
);

-- TABLA: MODELO_EXTRA

CREATE TABLE modelo_extra (
	id_modelo    INT NOT NULL,
	id_extra     INT NOT NULL,
	precio_extra DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	PRIMARY KEY (id_modelo, id_extra),
	FOREIGN KEY (id_modelo)
		REFERENCES modelo(id_modelo)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
	FOREIGN KEY (id_extra)
		REFERENCES extra(id_extra)
			ON UPDATE CASCADE
			ON DELETE CASCADE
);

-- TABLA: AUTOMOVIL

CREATE TABLE automovil (
	num_bastidor     VARCHAR(17) PRIMARY KEY,
	id_modelo        INT NOT NULL,
	color            VARCHAR(50),
	anio_fabricacion INT,
	estado           estado_automovil_enum DEFAULT 'disponible',
	ubicacion        ubicacion_enum DEFAULT 'concesionario',
	id_concesionario INT NOT NULL,
	id_servicio      INT NULL,
	fecha_ingreso    DATE,
	FOREIGN KEY (id_modelo)
		REFERENCES modelo(id_modelo)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
	FOREIGN KEY (id_concesionario)
		REFERENCES concesionario(id_concesionario)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
	FOREIGN KEY (id_servicio)
		REFERENCES servicio_oficial(id_servicio)
			ON UPDATE CASCADE
			ON DELETE SET NULL
);

-- TABLA: FORMA_PAGO

CREATE TABLE forma_pago (
	id_forma_pago     SERIAL PRIMARY KEY,
	tipo              tipo_pago_enum NOT NULL,
	nombre_financiera VARCHAR(100) NULL,
	condiciones       TEXT,
	tasa_interes      DECIMAL(5,2) NULL
);

-- TABLA: VENTA

CREATE TABLE venta (
	id_venta       SERIAL PRIMARY KEY,
	num_bastidor   VARCHAR(17) NOT NULL,
	fecha_venta    DATE NOT NULL,
	fecha_entrega  DATE,
	matricula      VARCHAR(10),
	precio_cobrado DECIMAL(12,2) NOT NULL,
	id_forma_pago  INT NOT NULL,
	es_stock       BOOLEAN DEFAULT TRUE,
	tipo_vendedor  tipo_vendedor_enum NOT NULL,
	id_vendedor    INT NULL,
	id_servicio    INT NULL,
	observaciones  TEXT,
	FOREIGN KEY (num_bastidor)
		REFERENCES automovil(num_bastidor)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
	FOREIGN KEY (id_forma_pago)
		REFERENCES forma_pago(id_forma_pago)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
	FOREIGN KEY (id_vendedor)
		REFERENCES vendedor(id_vendedor)
			ON UPDATE CASCADE
			ON DELETE SET NULL,
	FOREIGN KEY (id_servicio)
		REFERENCES servicio_oficial(id_servicio)
			ON UPDATE CASCADE
			ON DELETE SET NULL
);

-- TABLA: VENTA_EXTRA

CREATE TABLE venta_extra (
	id_venta             INT NOT NULL,
	id_extra             INT NOT NULL,
	precio_cobrado_extra DECIMAL(10,2) NOT NULL,
	PRIMARY KEY (id_venta, id_extra),
	FOREIGN KEY (id_venta)
		REFERENCES venta(id_venta)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
	FOREIGN KEY (id_extra)
		REFERENCES extra(id_extra)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
);

-- TABLA: USUARIO

CREATE TABLE usuario (
	id_usuario     SERIAL PRIMARY KEY,
	nombre         VARCHAR(100) NOT NULL,
	apellidos      VARCHAR(150) NOT NULL,
	email          VARCHAR(100) NOT NULL UNIQUE,
	password       VARCHAR(255) NOT NULL,
	perfil         perfil_usuario_enum NOT NULL DEFAULT 'usuario',
	id_vendedor    INT NULL,
	activo         BOOLEAN DEFAULT TRUE,
	fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	ultimo_acceso  TIMESTAMP NULL,
	FOREIGN KEY (id_vendedor)
		REFERENCES vendedor(id_vendedor)
			ON UPDATE CASCADE
			ON DELETE SET NULL
);

-- DATOS DE PRUEBA

-- Concesionarios
INSERT INTO concesionario (nombre, domicilio, nif, telefono, email) VALUES
('AutoColombia Bogota',  'Av. El Dorado 68-50, Bogota',       '900123456-1', '6011234567', 'info@autocolombia.com.co'),
('Medellin Motors',      'Calle 50 No. 45-30, Medellin',      '900654321-2', '6044567890', 'info@medellinmotors.com.co');

-- Servicios Oficiales
INSERT INTO servicio_oficial (nombre, domicilio, nif, telefono, email, id_concesionario) VALUES
('AutoService Suba',        'Av. Suba 115-50, Bogota',          '900111111-1', '6011110001', 'suba@autocolombia.com.co',       1),
('AutoService Chapinero',   'Calle 63 No. 13-20, Bogota',       '900222222-2', '6011110002', 'chapinero@autocolombia.com.co',   1),
('AutoService Laureles',    'Circular 73 No. 39-50, Medellin',  '900333333-3', '6044440003', 'laureles@medellinmotors.com.co',  2);

-- Vendedores
INSERT INTO vendedor (nombre, apellidos, nif, domicilio, telefono, email, fecha_contratacion, id_concesionario) VALUES
('Carlos',    'Ramirez Gutierrez', '1020304050', 'Calle 80 No. 90-10, Bogota',    '3101234567', 'carlos@autocolombia.com.co',    '2020-02-01', 1),
('Valentina', 'Torres Ospina',     '1030405060', 'Carrera 15 No. 85-20, Bogota',  '3209876543', 'valentina@autocolombia.com.co', '2021-05-15', 1),
('Andres',    'Moreno Castillo',   '1040506070', 'Calle 33 No. 65-40, Medellin',  '3154567890', 'andres@medellinmotors.com.co',  '2019-08-10', 2);

-- Marcas
INSERT INTO marca (nombre, pais_origen, descripcion) VALUES
('Chevrolet', 'Estados Unidos', 'Marca americana muy popular en Colombia'),
('Renault',   'Francia',        'Marca francesa con gran presencia en Colombia'),
('Mazda',     'Japon',          'Fabricante japones reconocido en Colombia'),
('Toyota',    'Japon',          'Fabricante japones lider en confiabilidad'),
('Kia',       'Corea del Sur',  'Marca coreana con crecimiento en Colombia');

-- Concesionario - Marca
INSERT INTO concesionario_marca (id_concesionario, id_marca, fecha_acuerdo) VALUES
(1, 1, '2018-01-01'),
(1, 2, '2018-01-01'),
(1, 3, '2019-06-01'),
(2, 4, '2017-03-01'),
(2, 5, '2020-01-01');

-- Modelos
INSERT INTO modelo (nombre, id_marca, precio_base, descuento, potencia_fiscal, cilindrada, 
	tipo_combustible, num_puertas, num_plazas, descripcion) VALUES
('Onix',     1,  62000000.00, 3.00,  76.00, 1200, 'gasolina', 5, 5, 'Sedan compacto popular en Colombia'),
('Tracker',  1,  89000000.00, 2.00, 153.00, 1300, 'gasolina', 5, 5, 'SUV compacta muy vendida'),
('Logan',    2,  55000000.00, 4.00,  75.00, 1600, 'gasolina', 5, 5, 'Sedan economico y espacioso'),
('Sandero',  2,  52000000.00, 5.00,  75.00, 1600, 'gasolina', 5, 5, 'Hatchback economico'),
('Mazda 3',  3,  95000000.00, 2.00, 155.00, 2000, 'gasolina', 5, 5, 'Sedan premium compacto'),
('Hilux',    4, 145000000.00, 1.00, 204.00, 2800, 'diesel',   5, 5, 'Camioneta pickup resistente'),
('Sportage', 5, 105000000.00, 2.00, 180.00, 2000, 'gasolina', 5, 5, 'SUV mediana moderna');

-- Caracteristicas
INSERT INTO caracteristica (nombre, descripcion, categoria) VALUES
('Airbag conductor',    'Airbag frontal para el conductor',   'Seguridad'),
('Airbag acompanante',  'Airbag frontal para el acompanante', 'Seguridad'),
('Frenos ABS',          'Sistema antibloqueo de frenos',      'Seguridad'),
('Control de traccion', 'Sistema de control de traccion',     'Seguridad'),
('Cierre centralizado', 'Cierre centralizado de puertas',     'Confort'),
('Aire acondicionado',  'Sistema de climatizacion',           'Confort'),
('Camara de reversa',   'Camara trasera para parqueo',        'Tecnologia'),
('Pantalla tactil',     'Sistema de entretenimiento tactil',  'Tecnologia');

-- Modelo - Caracteristica Serie
INSERT INTO modelo_caracteristica_serie (id_modelo, id_caracteristica) VALUES
(1, 1),(1, 3),(1, 5),
(2, 1),(2, 2),(2, 3),(2, 4),(2, 5),(2, 6),(2, 7),(2, 8),
(3, 1),(3, 3),(3, 5),
(4, 1),(4, 3),(4, 5),
(5, 1),(5, 2),(5, 3),(5, 4),(5, 5),(5, 6),(5, 7),(5, 8),
(6, 1),(6, 2),(6, 3),(6, 4),(6, 5),(6, 6),
(7, 1),(7, 2),(7, 3),(7, 4),(7, 5),(7, 6),(7, 7),(7, 8);

-- Extras
INSERT INTO extra (nombre, descripcion, categoria) VALUES
('Aire acondicionado', 'Climatizacion manual',                 'Confort'),
('Airbag acompanante', 'Airbag frontal adicional',             'Seguridad'),
('Camara de reversa',  'Camara trasera para parqueo',          'Tecnologia'),
('Pantalla tactil',    'Radio con pantalla tactil 7 pulgadas', 'Tecnologia'),
('Rines de aleacion',  'Rines deportivos de aleacion',         'Estetica'),
('Vidrios electricos', 'Vidrios electricos en todas puertas',  'Confort'),
('Sensor de parqueo',  'Sensores delanteros y traseros',       'Tecnologia'),
('Tapiceria en cuero', 'Asientos tapizados en cuero',          'Confort'),
('Techo panoramico',   'Techo solar panoramico electrico',     'Confort'),
('Pintura perlada',    'Acabado en pintura perlada especial',  'Estetica');

-- Modelo - Extra
INSERT INTO modelo_extra (id_modelo, id_extra, precio_extra) VALUES
(1, 1, 3500000.00),
(1, 2, 1500000.00),
(1, 3, 2000000.00),
(1, 4, 2500000.00),
(1, 5, 1800000.00),
(1,10, 1200000.00),

(3, 1, 3000000.00),
(3, 2, 1500000.00),
(3, 3, 1800000.00),
(3, 6, 1200000.00),

(4, 1, 3000000.00),
(4, 2, 1500000.00),
(4, 5, 1800000.00),
(4,10, 1000000.00),

(2, 8, 4500000.00),
(2, 9, 6000000.00),
(2,10, 1500000.00),

(5, 8, 5000000.00),
(5, 9, 7000000.00),
(5,10, 2000000.00),

(6, 8, 5500000.00),
(6, 9, 8000000.00),
(6,10, 2000000.00),

(7, 8, 4800000.00),
(7, 9, 7500000.00),
(7,10, 1800000.00);

-- Automoviles
INSERT INTO automovil (
	num_bastidor,
	id_modelo,
	color,
	anio_fabricacion,
	estado,
	ubicacion,
	id_concesionario,
	id_servicio,
	fecha_ingreso
) VALUES
('8LNHM13N14Y410600', 1, 'Blanco',       2023, 'disponible', 'concesionario',    1, NULL, '2024-01-10'),
('8LNHM13N14Y410601', 1, 'Negro',        2023, 'disponible', 'servicio_oficial', 1, 1,    '2024-01-15'),
('8LNHM13N14Y410602', 2, 'Gris Plata',   2024, 'disponible', 'concesionario',    1, NULL, '2024-02-01'),
('8LNHM13N14Y410603', 3, 'Blanco',       2023, 'disponible', 'servicio_oficial', 1, 2,    '2024-02-10'),
('8LNHM13N14Y410604', 4, 'Rojo',         2023, 'disponible', 'concesionario',    1, NULL, '2024-02-15'),
('8LNHM13N14Y410605', 5, 'Azul Marino',  2024, 'disponible', 'concesionario',    2, NULL, '2024-03-01'),
('8LNHM13N14Y410606', 6, 'Blanco Perla', 2024, 'disponible', 'concesionario',    2, NULL, '2024-03-05'),
('8LNHM13N14Y410607', 7, 'Gris Oscuro',  2024, 'disponible', 'servicio_oficial', 2, 3,    '2024-03-10');

-- Formas de Pago
INSERT INTO forma_pago (tipo, nombre_financiera, condiciones, tasa_interes) VALUES
('contado',    NULL,              'Pago de contado inmediato',          NULL),
('financiera', 'Bancolombia',     'Hasta 60 meses, cuota fija',         1.20),
('financiera', 'Banco de Bogota', 'Hasta 48 meses sin cuota inicial',   1.35),
('financiera', 'Davivienda',      'Hasta 72 meses, tasa preferencial',  1.10),
('financiera', 'Credito FCA',     'Financiacion directa del fabricante',0.99);

-- Ventas
INSERT INTO venta (
	num_bastidor,
	fecha_venta,
	fecha_entrega,
	matricula,
	precio_cobrado,
	id_forma_pago,
	es_stock,
	tipo_vendedor,
	id_vendedor,
	id_servicio,
	observaciones
) VALUES
('8LNHM13N14Y410600', '2024-03-15', '2024-03-20', 'ABC123', 62000000.00, 1, TRUE, 'vendedor',          1,    NULL, 'Venta de contado sin extras'),
('8LNHM13N14Y410603', '2024-04-01', '2024-04-08', 'DEF456', 58500000.00, 2, TRUE, 'vendedor',          2,    NULL, 'Financiado con Bancolombia'),
('8LNHM13N14Y410604', '2024-04-10', '2024-04-15', 'GHI789', 54200000.00, 3, TRUE, 'servicio_oficial',  NULL, 3,    'Venta en servicio Laureles');

-- Venta Extra
INSERT INTO venta_extra (id_venta, id_extra, precio_cobrado_extra) VALUES
(2, 1, 3000000.00),
(2, 6, 1200000.00),
(3, 1, 3000000.00),
(3, 2, 1500000.00);

-- Usuarios
INSERT INTO usuario (nombre, apellidos, email, password, perfil, id_vendedor, activo) VALUES
('Administrador', 'Sistema',          'admin1@autocolombia.com.co',    '81dc9bdb52d04dc20036dbd8313ed055', 'admin',    NULL, TRUE),
('Carlos',        'Ramirez Gutierrez','vendedor1@autocolombia.com.co', '81dc9bdb52d04dc20036dbd8313ed055', 'vendedor', 1,    TRUE),
('Juan',          'Consulta',         'consulta1@autocolombia.com.co', '81dc9bdb52d04dc20036dbd8313ed055', 'usuario',  NULL, TRUE);
