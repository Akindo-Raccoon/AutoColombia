-- CREAR Y SELECCIONAR BASE DE DATOS

CREATE DATABASE IF NOT EXISTS concesionario_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE concesionario_db;

-- TABLA: CONCESIONARIO

CREATE TABLE concesionario (
    id_concesionario INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    domicilio        VARCHAR(200) NOT NULL,
    nif              VARCHAR(20)  NOT NULL UNIQUE,
    telefono         VARCHAR(20),
    email            VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: SERVICIO_OFICIAL

CREATE TABLE servicio_oficial (
    id_servicio      INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    domicilio        VARCHAR(200) NOT NULL,
    nif              VARCHAR(20)  NOT NULL UNIQUE,
    telefono         VARCHAR(20),
    email            VARCHAR(100),
    id_concesionario INT NOT NULL,
    FOREIGN KEY (id_concesionario)
        REFERENCES concesionario(id_concesionario)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: VENDEDOR

CREATE TABLE vendedor (
    id_vendedor        INT AUTO_INCREMENT PRIMARY KEY,
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
        ON UPDATE CASCADE ON DELETE RESTRICT
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: MARCA

CREATE TABLE marca (
    id_marca    INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50) NOT NULL UNIQUE,
    pais_origen VARCHAR(50),
    descripcion TEXT
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: CONCESIONARIO_MARCA

CREATE TABLE concesionario_marca (
    id_concesionario INT NOT NULL,
    id_marca         INT NOT NULL,
    fecha_acuerdo    DATE,
    PRIMARY KEY (id_concesionario, id_marca),
    FOREIGN KEY (id_concesionario)
        REFERENCES concesionario(id_concesionario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_marca)
        REFERENCES marca(id_marca)
        ON UPDATE CASCADE ON DELETE CASCADE
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: MODELO

CREATE TABLE modelo (
    id_modelo        INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    id_marca         INT NOT NULL,
    precio_base      DECIMAL(12,2) NOT NULL,
    descuento        DECIMAL(5,2) DEFAULT 0.00,
    potencia_fiscal  DECIMAL(8,2),
    cilindrada       INT,
    tipo_combustible ENUM('gasolina','diesel','hibrido','electrico','gas'),
    num_puertas      INT,
    num_plazas       INT,
    descripcion      TEXT,
    activo           BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_marca)
        REFERENCES marca(id_marca)
        ON UPDATE CASCADE ON DELETE RESTRICT
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: CARACTERISTICA

CREATE TABLE caracteristica (
    id_caracteristica INT AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(100) NOT NULL,
    descripcion       TEXT,
    categoria         VARCHAR(50)
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: MODELO_CARACTERISTICA_SERIE

CREATE TABLE modelo_caracteristica_serie (
    id_modelo         INT NOT NULL,
    id_caracteristica INT NOT NULL,
    PRIMARY KEY (id_modelo, id_caracteristica),
    FOREIGN KEY (id_modelo)
        REFERENCES modelo(id_modelo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_caracteristica)
        REFERENCES caracteristica(id_caracteristica)
        ON UPDATE CASCADE ON DELETE CASCADE
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: EXTRA

CREATE TABLE extra (
    id_extra    INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria   VARCHAR(50)
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: MODELO_EXTRA

CREATE TABLE modelo_extra (
    id_modelo    INT NOT NULL,
    id_extra     INT NOT NULL,
    precio_extra DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_modelo, id_extra),
    FOREIGN KEY (id_modelo)
        REFERENCES modelo(id_modelo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_extra)
        REFERENCES extra(id_extra)
        ON UPDATE CASCADE ON DELETE CASCADE
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: AUTOMOVIL

CREATE TABLE automovil (
    num_bastidor     VARCHAR(17) PRIMARY KEY,
    id_modelo        INT NOT NULL,
    color            VARCHAR(50),
    anio_fabricacion YEAR,
    estado           ENUM('disponible','vendido','reservado') DEFAULT 'disponible',
    ubicacion        ENUM('concesionario','servicio_oficial') DEFAULT 'concesionario',
    id_concesionario INT NOT NULL,
    id_servicio      INT NULL,
    fecha_ingreso    DATE,
    FOREIGN KEY (id_modelo)
        REFERENCES modelo(id_modelo)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_concesionario)
        REFERENCES concesionario(id_concesionario)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_servicio)
        REFERENCES servicio_oficial(id_servicio)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- TABLA: FORMA_PAGO

CREATE TABLE forma_pago (
    id_forma_pago     INT AUTO_INCREMENT PRIMARY KEY,
    tipo              ENUM('contado','financiera') NOT NULL,
    nombre_financiera VARCHAR(100) NULL,
    condiciones       TEXT,
    tasa_interes      DECIMAL(5,2) NULL
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: USUARIO

CREATE TABLE usuario (
    id_usuario     INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    apellidos      VARCHAR(150) NOT NULL,
    email          VARCHAR(100) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,
    perfil         ENUM('admin','vendedor','usuario') NOT NULL DEFAULT 'usuario',
    id_vendedor    INT NULL,
    activo         BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso  DATETIME NULL,
    FOREIGN KEY (id_vendedor)
        REFERENCES vendedor(id_vendedor)
        ON UPDATE CASCADE ON DELETE SET NULL
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: VENTA

CREATE TABLE venta (
    id_venta       INT AUTO_INCREMENT PRIMARY KEY,
    num_bastidor   VARCHAR(17) NOT NULL,
    fecha_venta    DATE NOT NULL,
    fecha_entrega  DATE,
    matricula      VARCHAR(10),
    precio_cobrado DECIMAL(12,2) NOT NULL,
    id_forma_pago  INT NOT NULL,
    es_stock       BOOLEAN DEFAULT TRUE,
    tipo_vendedor  ENUM('vendedor','servicio_oficial') NOT NULL,
    id_vendedor    INT NULL,
    id_servicio    INT NULL,
    id_usuario     INT NULL,
    observaciones  TEXT,
    FOREIGN KEY (num_bastidor)
        REFERENCES automovil(num_bastidor)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_forma_pago)
        REFERENCES forma_pago(id_forma_pago)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_vendedor)
        REFERENCES vendedor(id_vendedor)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (id_servicio)
        REFERENCES servicio_oficial(id_servicio)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE ON DELETE SET NULL
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TABLA: VENTA_EXTRA

CREATE TABLE venta_extra (
    id_venta             INT NOT NULL,
    id_extra             INT NOT NULL,
    precio_cobrado_extra DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_venta, id_extra),
    FOREIGN KEY (id_venta)
        REFERENCES venta(id_venta)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_extra)
        REFERENCES extra(id_extra)
        ON UPDATE CASCADE ON DELETE RESTRICT
);



-- AGREGAR LLAVE FORANEA A VENTA PARA USUARIO
ALTER TABLE venta 
    ADD CONSTRAINT fk_venta_usuario 
    FOREIGN KEY (id_usuario) 
    REFERENCES usuario(id_usuario) 
    ON UPDATE CASCADE ON DELETE SET NULL;


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
('Carlos',    'Ramirez Gutierrez', '1020304050', 'Calle 80 No. 90-10, Bogota',    '3101234567', 'carlos@autocolombia.com.co',   '2020-02-01', 1),
('Valentina', 'Torres Ospina',     '1030405060', 'Carrera 15 No. 85-20, Bogota',  '3209876543', 'valentina@autocolombia.com.co','2021-05-15', 1),
('Andres',    'Moreno Castillo',   '1040506070', 'Calle 33 No. 65-40, Medellin',  '3154567890', 'andres@medellinmotors.com.co', '2019-08-10', 2);

-- Marcas
INSERT INTO marca (nombre, pais_origen, descripcion) VALUES
('Chevrolet',  'Estados Unidos', 'Marca americana muy popular en Colombia'),
('Renault',    'Francia',        'Marca francesa con gran presencia en Colombia'),
('Mazda',      'Japon',          'Fabricante japones reconocido en Colombia'),
('Toyota',     'Japon',          'Fabricante japones lider en confiabilidad'),
('Kia',        'Corea del Sur',  'Marca coreana con crecimiento en Colombia');

-- Concesionario - Marca
INSERT INTO concesionario_marca (id_concesionario, id_marca, fecha_acuerdo) VALUES
(1, 1, '2018-01-01'),
(1, 2, '2018-01-01'),
(1, 3, '2019-06-01'),
(2, 4, '2017-03-01'),
(2, 5, '2020-01-01');

-- Modelos
INSERT INTO modelo (nombre, id_marca, precio_base, descuento, potencia_fiscal, cilindrada, tipo_combustible, num_puertas, num_plazas, descripcion) VALUES
('Onix',        1, 62000000.00,  3.00,  76.00, 1200, 'gasolina', 5, 5, 'Sedan compacto popular en Colombia'),
('Tracker',     1, 89000000.00,  2.00, 153.00, 1300, 'gasolina', 5, 5, 'SUV compacta muy vendida'),
('Logan',       2, 55000000.00,  4.00,  75.00, 1600, 'gasolina', 5, 5, 'Sedan economico y espacioso'),
('Sandero',     2, 52000000.00,  5.00,  75.00, 1600, 'gasolina', 5, 5, 'Hatchback economico'),
('Mazda 3',     3, 95000000.00,  2.00, 155.00, 2000, 'gasolina', 5, 5, 'Sedan premium compacto'),
('Hilux',       4, 145000000.00, 1.00, 204.00, 2800, 'diesel',   5, 5, 'Camioneta pickup resistente'),
('Sportage',    5, 105000000.00, 2.00, 180.00, 2000, 'gasolina', 5, 5, 'SUV mediana moderna');

-- Caracteristicas
INSERT INTO caracteristica (nombre, descripcion, categoria) VALUES
('Airbag conductor',      'Airbag frontal para el conductor',    'Seguridad'),
('Airbag acompanante',    'Airbag frontal para el acompanante',  'Seguridad'),
('Frenos ABS',            'Sistema antibloqueo de frenos',       'Seguridad'),
('Control de traccion',   'Sistema de control de traccion',      'Seguridad'),
('Cierre centralizado',   'Cierre centralizado de puertas',      'Confort'),
('Aire acondicionado',    'Sistema de climatizacion',            'Confort'),
('Camara de reversa',     'Camara trasera para parqueo',         'Tecnologia'),
('Pantalla tactil',       'Sistema de entretenimiento tactil',   'Tecnologia');

-- Modelo - Caracteristica Serie
INSERT INTO modelo_caracteristica_serie (id_modelo, id_caracteristica) VALUES
-- Onix: basico
(1, 1),(1, 3),(1, 5),
-- Tracker: completo
(2, 1),(2, 2),(2, 3),(2, 4),(2, 5),(2, 6),(2, 7),(2, 8),
-- Logan: basico
(3, 1),(3, 3),(3, 5),
-- Sandero: basico
(4, 1),(4, 3),(4, 5),
-- Mazda 3: completo
(5, 1),(5, 2),(5, 3),(5, 4),(5, 5),(5, 6),(5, 7),(5, 8),
-- Hilux: intermedio
(6, 1),(6, 2),(6, 3),(6, 4),(6, 5),(6, 6),
-- Sportage: completo
(7, 1),(7, 2),(7, 3),(7, 4),(7, 5),(7, 6),(7, 7),(7, 8);

-- Extras
INSERT INTO extra (nombre, descripcion, categoria) VALUES
('Aire acondicionado',    'Climatizacion manual',                'Confort'),
('Airbag acompanante',    'Airbag frontal adicional',            'Seguridad'),
('Camara de reversa',     'Camara trasera para parqueo',         'Tecnologia'),
('Pantalla tactil',       'Radio con pantalla tactil 7 pulgadas','Tecnologia'),
('Rines de aleacion',     'Rines deportivos de aleacion',        'Estetica'),
('Vidrios electricos',    'Vidrios electricos en todas puertas', 'Confort'),
('Sensor de parqueo',     'Sensores delanteros y traseros',      'Tecnologia'),
('Tapiceria en cuero',    'Asientos tapizados en cuero',         'Confort'),
('Techo panoramico',      'Techo solar panoramico electrico',    'Confort'),
('Pintura perlada',       'Acabado en pintura perlada especial', 'Estetica');

-- Modelo - Extra
INSERT INTO modelo_extra (id_modelo, id_extra, precio_extra) VALUES
-- Onix extras
(1, 1, 3500000.00),  -- Aire acondicionado
(1, 2, 1500000.00),  -- Airbag acompanante
(1, 3, 2000000.00),  -- Camara reversa
(1, 4, 2500000.00),  -- Pantalla tactil
(1, 5, 1800000.00),  -- Rines aleacion
(1, 10,1200000.00),  -- Pintura perlada
-- Logan extras
(3, 1, 3000000.00),  -- Aire acondicionado
(3, 2, 1500000.00),  -- Airbag acompanante
(3, 3, 1800000.00),  -- Camara reversa
(3, 6, 1200000.00),  -- Vidrios electricos
-- Sandero extras
(4, 1, 3000000.00),  -- Aire acondicionado
(4, 2, 1500000.00),  -- Airbag acompanante
(4, 5, 1800000.00),  -- Rines aleacion
(4, 10,1000000.00),  -- Pintura perlada
-- Tracker extras
(2, 8, 4500000.00),  -- Tapiceria cuero
(2, 9, 6000000.00),  -- Techo panoramico
(2, 10,1500000.00),  -- Pintura perlada
-- Mazda 3 extras
(5, 8, 5000000.00),  -- Tapiceria cuero
(5, 9, 7000000.00),  -- Techo panoramico
(5, 10,2000000.00),  -- Pintura perlada
-- Hilux extras
(6, 8, 5500000.00),  -- Tapiceria cuero
(6, 9, 8000000.00),  -- Techo panoramico
(6, 10,2000000.00),  -- Pintura perlada
-- Sportage extras
(7, 8, 4800000.00),  -- Tapiceria cuero
(7, 9, 7500000.00),  -- Techo panoramico
(7, 10,1800000.00);  -- Pintura perlada

-- Formas de Pago
INSERT INTO forma_pago (tipo, nombre_financiera, condiciones, tasa_interes) VALUES
('contado',    NULL,                  'Pago de contado inmediato',         NULL),
('financiera', 'Bancolombia',         'Hasta 60 meses, cuota fija',        1.20),
('financiera', 'Banco de Bogota',     'Hasta 48 meses sin cuota inicial',  1.35),
('financiera', 'Davivienda',          'Hasta 72 meses, tasa preferencial', 1.10),
('financiera', 'Credito FCA',         'Financiacion directa del fabricante',0.99);

-- Autos Disponibles Antiguos
INSERT INTO automovil (num_bastidor, id_modelo, color, anio_fabricacion, estado, ubicacion, id_concesionario, id_servicio, fecha_ingreso) VALUES
('8LNHM13N14Y410600', 1, 'Blanco',          2023, 'disponible', 'concesionario',    1, NULL, '2024-01-10'),
('8LNHM13N14Y410601', 1, 'Negro',            2023, 'disponible', 'servicio_oficial', 1, 1,   '2024-01-15'),
('8LNHM13N14Y410602', 2, 'Gris Plata',       2024, 'disponible', 'concesionario',    1, NULL, '2024-02-01');

-- Autos Vendidos Antiguos
INSERT INTO automovil (num_bastidor, id_modelo, color, anio_fabricacion, estado, ubicacion, id_concesionario, id_servicio, fecha_ingreso) VALUES
('8LNHM13N14Y410603', 3, 'Blanco',           2023, 'vendido', 'servicio_oficial', 1, 2,   '2024-02-10'),
('8LNHM13N14Y410604', 4, 'Rojo',             2023, 'vendido', 'concesionario',    1, NULL, '2024-02-15');

-- 10 Nuevos Autos Disponibles (Pruebas)
INSERT INTO automovil (num_bastidor, id_modelo, color, anio_fabricacion, estado, ubicacion, id_concesionario, id_servicio, fecha_ingreso) VALUES
('8LNHM13N14Y410610', 1, 'Rojo Fuego',       2024, 'disponible', 'concesionario',    1, NULL, '2024-05-20'),
('8LNHM13N14Y410611', 1, 'Plata Metálico',   2024, 'disponible', 'concesionario',    1, NULL, '2024-05-20'),
('8LNHM13N14Y410612', 2, 'Blanco Hielo',     2024, 'disponible', 'concesionario',    1, NULL, '2024-05-21'),
('8LNHM13N14Y410613', 2, 'Gris Oscuro',      2024, 'disponible', 'servicio_oficial', 1, 1,    '2024-05-21'),
('8LNHM13N14Y410614', 3, 'Azul Noche',       2024, 'disponible', 'concesionario',    2, NULL, '2024-05-22'),
('8LNHM13N14Y410615', 3, 'Negro Ébano',      2024, 'disponible', 'concesionario',    2, NULL, '2024-05-22'),
('8LNHM13N14Y410616', 4, 'Rojo Cereza',      2024, 'disponible', 'servicio_oficial', 2, 3,    '2024-05-23'),
('8LNHM13N14Y410617', 5, 'Blanco Perla',     2024, 'disponible', 'concesionario',    2, NULL, '2024-05-23'),
('8LNHM13N14Y410618', 6, 'Gris Plata',       2024, 'disponible', 'concesionario',    1, NULL, '2024-05-24'),
('8LNHM13N14Y410619', 7, 'Negro Mate',       2024, 'disponible', 'concesionario',    1, NULL, '2024-05-24');

-- 20 Nuevos Autos Vendidos (Historial)
INSERT INTO automovil (num_bastidor, id_modelo, color, anio_fabricacion, estado, ubicacion, id_concesionario, id_servicio, fecha_ingreso) VALUES
('8LNHM13N14Y410701', 1, 'Azul Rey',         2023, 'vendido', 'concesionario', 1, NULL, '2023-11-01'),
('8LNHM13N14Y410702', 2, 'Gris Platino',     2023, 'vendido', 'concesionario', 2, NULL, '2023-11-05'),
('8LNHM13N14Y410703', 3, 'Blanco Alpino',    2023, 'vendido', 'concesionario', 1, NULL, '2023-11-10'),
('8LNHM13N14Y410704', 4, 'Negro Titanio',    2023, 'vendido', 'concesionario', 2, NULL, '2023-11-15'),
('8LNHM13N14Y410705', 5, 'Plata Brillante',  2023, 'vendido', 'concesionario', 1, NULL, '2023-12-01'),
('8LNHM13N14Y410706', 6, 'Rojo Carmesí',     2023, 'vendido', 'concesionario', 2, NULL, '2023-12-05'),
('8LNHM13N14Y410707', 7, 'Blanco Nácar',     2023, 'vendido', 'concesionario', 1, NULL, '2023-12-10'),
('8LNHM13N14Y410708', 1, 'Gris Ratón',       2023, 'vendido', 'concesionario', 2, NULL, '2023-12-15'),
('8LNHM13N14Y410709', 2, 'Negro Azabache',   2024, 'vendido', 'concesionario', 1, NULL, '2024-01-05'),
('8LNHM13N14Y410710', 3, 'Rojo Magma',       2024, 'vendido', 'concesionario', 2, NULL, '2024-01-10'),
('8LNHM13N14Y410711', 4, 'Blanco Nieve',     2024, 'vendido', 'concesionario', 1, NULL, '2024-01-15'),
('8LNHM13N14Y410712', 5, 'Plata Lunar',      2024, 'vendido', 'concesionario', 2, NULL, '2024-01-20'),
('8LNHM13N14Y410713', 6, 'Azul Zafiro',      2024, 'vendido', 'concesionario', 1, NULL, '2024-02-05'),
('8LNHM13N14Y410714', 7, 'Gris Plomo',       2024, 'vendido', 'concesionario', 2, NULL, '2024-02-10'),
('8LNHM13N14Y410715', 1, 'Blanco Crema',     2024, 'vendido', 'concesionario', 1, NULL, '2024-02-15'),
('8LNHM13N14Y410716', 2, 'Rojo Rubi',        2024, 'vendido', 'concesionario', 2, NULL, '2024-03-01'),
('8LNHM13N14Y410717', 3, 'Plata Esterlina',  2024, 'vendido', 'concesionario', 1, NULL, '2024-03-05'),
('8LNHM13N14Y410718', 4, 'Negro Ónice',      2024, 'vendido', 'concesionario', 2, NULL, '2024-03-10'),
('8LNHM13N14Y410719', 5, 'Blanco Tiza',      2024, 'vendido', 'concesionario', 1, NULL, '2024-04-01'),
('8LNHM13N14Y410720', 6, 'Gris Acero',       2024, 'vendido', 'concesionario', 2, NULL, '2024-04-05');

-- Ventas
INSERT INTO venta (num_bastidor, fecha_venta, fecha_entrega, matricula, precio_cobrado, id_forma_pago, es_stock, tipo_vendedor, id_vendedor, id_servicio, observaciones) VALUES
('8LNHM13N14Y410603', '2024-04-01', '2024-04-08', 'DEF456', 58500000.00, 2, TRUE, 'vendedor',          2,    NULL, 'Financiado con Bancolombia'),
('8LNHM13N14Y410604', '2024-04-10', '2024-04-15', 'GHI789', 54200000.00, 3, TRUE, 'servicio_oficial',  NULL, 3,   'Venta en servicio Laureles'),
('8LNHM13N14Y410701', '2023-11-10', NULL, NULL, 62000000.00, 1, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410702', '2023-11-15', NULL, NULL, 89000000.00, 2, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410703', '2023-11-20', NULL, NULL, 55000000.00, 3, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410704', '2023-11-25', NULL, NULL, 52000000.00, 4, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410705', '2023-12-10', NULL, NULL, 95000000.00, 1, TRUE, 'vendedor', 1, NULL, 'Venta de fin de año'),
('8LNHM13N14Y410706', '2023-12-15', NULL, NULL, 145000000.00, 2, TRUE, 'vendedor', 2, NULL, 'Venta de fin de año'),
('8LNHM13N14Y410707', '2023-12-20', NULL, NULL, 105000000.00, 3, TRUE, 'vendedor', 1, NULL, 'Venta de fin de año'),
('8LNHM13N14Y410708', '2023-12-25', NULL, NULL, 62000000.00, 4, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410709', '2024-01-15', NULL, NULL, 89000000.00, 1, TRUE, 'vendedor', 1, NULL, 'Promoción de inicio de año'),
('8LNHM13N14Y410710', '2024-01-20', NULL, NULL, 55000000.00, 2, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410711', '2024-01-25', NULL, NULL, 52000000.00, 3, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410712', '2024-01-30', NULL, NULL, 95000000.00, 4, TRUE, 'vendedor', 2, NULL, 'Promoción de inicio de año'),
('8LNHM13N14Y410713', '2024-02-15', NULL, NULL, 145000000.00, 1, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410714', '2024-02-20', NULL, NULL, 105000000.00, 2, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410715', '2024-02-25', NULL, NULL, 62000000.00, 3, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410716', '2024-03-10', NULL, NULL, 89000000.00, 4, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410717', '2024-03-15', NULL, NULL, 55000000.00, 1, TRUE, 'vendedor', 1, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410718', '2024-03-20', NULL, NULL, 52000000.00, 2, TRUE, 'vendedor', 2, NULL, 'Venta generada para historial'),
('8LNHM13N14Y410719', '2024-04-10', NULL, NULL, 95000000.00, 3, TRUE, 'vendedor', 1, NULL, 'Campaña de abril'),
('8LNHM13N14Y410720', '2024-04-15', NULL, NULL, 145000000.00, 4, TRUE, 'vendedor', 2, NULL, 'Campaña de abril');

-- Venta Extra
INSERT INTO venta_extra (id_venta, id_extra, precio_cobrado_extra) VALUES
(2, 1, 3000000.00),  -- Venta 2 → Aire acondicionado
(2, 6, 1200000.00),  -- Venta 2 → Vidrios electricos
(3, 1, 3000000.00),  -- Venta 3 → Aire acondicionado
(3, 2, 1500000.00),  -- Venta 3 → Airbag acompanante
(4, 1, 3500000.00),  -- Venta 4 → Aire acondicionado
(4, 2, 1500000.00),  -- Venta 4 → Airbag acompanante
(4, 4, 2500000.00);  -- Venta 4 → Pantalla tactil

-- USUARIOS DE PRUEBA
INSERT INTO usuario (nombre, apellidos, email, password, perfil, id_vendedor, activo) VALUES
('Administrador', 'Root', 'root1@autocolombia.com.co', 'e10adc3949ba59abbe56e057f20f883e', 'admin', NULL, TRUE),
('Administrador', 'Sistema',          'admin@autocolombia.com',        '81dc9bdb52d04dc20036dbd8313ed055', 'admin',    NULL, TRUE),
('Carlos',        'Ramirez Gutierrez','vendedor1@autocolombia.com.co', '81dc9bdb52d04dc20036dbd8313ed055', 'vendedor', 1,    TRUE),
('Juan',          'Consulta',         'consulta1@autocolombia.com.co', '81dc9bdb52d04dc20036dbd8313ed055', 'usuario',  NULL, TRUE);
