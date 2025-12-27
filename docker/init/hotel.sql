/*
 Importación directa desde Docker
 */

CREATE DATABASE IF NOT EXISTS gestor_hotelero
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gestor_hotelero;

/*
   USUARIO
*/

CREATE TABLE IF NOT EXISTS usuario (
                                       id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                                       nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(80) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo_usuario ENUM('cliente', 'administrador') NOT NULL
    );

/*
   CLIENTE
*/

CREATE TABLE IF NOT EXISTS cliente (
                                       id_cliente INT PRIMARY KEY,
                                       telefono VARCHAR(20) NULL,
    direccion VARCHAR(150) NULL,
    CONSTRAINT fk_cliente_usuario
    FOREIGN KEY (id_cliente)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    );

/*
   ADMINISTRADOR
*/

CREATE TABLE IF NOT EXISTS administrador (
                                             id_admin INT PRIMARY KEY,
                                             nivel_acceso ENUM('basico', 'super') NOT NULL DEFAULT 'basico',
    CONSTRAINT fk_admin_usuario
    FOREIGN KEY (id_admin)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    );

/*
   HABITACIÓN
 */

CREATE TABLE IF NOT EXISTS habitacion (
                                          id_habitacion INT AUTO_INCREMENT PRIMARY KEY,
                                          numero INT NOT NULL,
                                          piso INT NOT NULL,
                                          capacidad TINYINT NOT NULL,
                                          precio DECIMAL(10,2) NOT NULL,
    tipo ENUM('estandar', 'suite', 'premium') NOT NULL
    );

/*
   HABITACIÓN ESTÁNDAR
*/

CREATE TABLE IF NOT EXISTS habitacion_estandar (
                                                   id_habitacion INT PRIMARY KEY,
                                                   tiene_tv BOOLEAN NOT NULL DEFAULT TRUE,
                                                   CONSTRAINT fk_estandar_habitacion
                                                   FOREIGN KEY (id_habitacion)
    REFERENCES habitacion(id_habitacion)
    ON DELETE CASCADE
    );

/*
   HABITACIÓN SUITE
*/

CREATE TABLE IF NOT EXISTS habitacion_suite (
                                                id_habitacion INT PRIMARY KEY,
                                                jacuzzi BOOLEAN NOT NULL,
                                                vistas VARCHAR(100) NULL,
    terraza BOOLEAN NOT NULL,
    minibar BOOLEAN NOT NULL,
    CONSTRAINT fk_suite_habitacion
    FOREIGN KEY (id_habitacion)
    REFERENCES habitacion(id_habitacion)
    ON DELETE CASCADE
    );

/*
   HABITACIÓN PREMIUM
*/

CREATE TABLE IF NOT EXISTS habitacion_premium (
                                                  id_habitacion INT PRIMARY KEY,
                                                  terraza BOOLEAN NOT NULL,
                                                  vistas VARCHAR(100) NULL,
    servicio_habitacion_24h BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_premium_habitacion
    FOREIGN KEY (id_habitacion)
    REFERENCES habitacion(id_habitacion)
    ON DELETE CASCADE
    );

/*
   RESERVA
*/

CREATE TABLE IF NOT EXISTS reserva (
                                       id_reserva INT AUTO_INCREMENT PRIMARY KEY,
                                       id_cliente INT NOT NULL,
                                       id_habitacion INT NOT NULL,
                                       fecha_entrada DATE NOT NULL,
                                       fecha_salida DATE NOT NULL,
                                       estado ENUM('solicitada','aceptada','cancelada','finalizada')
    NOT NULL DEFAULT 'solicitada',
    CONSTRAINT fk_reserva_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES cliente(id_cliente),
    CONSTRAINT fk_reserva_habitacion
    FOREIGN KEY (id_habitacion)
    REFERENCES habitacion(id_habitacion)
    );

/*
   DATOS INICIALES
*/

/* USUARIOS */

INSERT INTO usuario (nombre, apellidos, email, password, tipo_usuario)
VALUES
    ('Antonio', 'Profesor', 'antonio@hotel.com', '$2y$10$wC5sZc1JAbDLF5Ys/vH5pOZ4nGZnIwLqj69rGqwZPT.gu/RdLUPCm', 'administrador'),
    ('David', 'Alumno', 'david@hotel.com', '$2y$10$wC5sZc1JAbDLF5Ys/vH5pOZ4nGZnIwLqj69rGqwZPT.gu/RdLUPCm', 'administrador'),

    ('Cliente1', 'Apellido1', 'cliente1@hotel.com', '$2y$10$EZ./VOJgKx6/T8JG1HSj3uY21XlaIuN.2537.JPnDpNyHggb1ibGC', 'cliente'),
    ('Cliente2', 'Apellido2', 'cliente2@hotel.com', '$2y$10$EZ./VOJgKx6/T8JG1HSj3uY21XlaIuN.2537.JPnDpNyHggb1ibGC', 'cliente'),
    ('Cliente3', 'Apellido3', 'cliente3@hotel.com', '$2y$10$EZ./VOJgKx6/T8JG1HSj3uY21XlaIuN.2537.JPnDpNyHggb1ibGC', 'cliente');

/* ADMINISTRADORES */

INSERT INTO administrador (id_admin, nivel_acceso)
VALUES
    (1, 'basico'),
    (2, 'super');

/* CLIENTES */

INSERT INTO cliente (id_cliente, telefono, direccion)
VALUES
    (3, NULL, 'Calle Cliente 1'),
    (4, NULL, 'Calle Cliente 2'),
    (5, '600555666', 'Calle Cliente 3');

/* HABITACIONES */

INSERT INTO habitacion (numero, piso, capacidad, precio, tipo)
VALUES
    (101,1,2,60,'estandar'),
    (102,1,2,60,'estandar'),
    (103,1,3,70,'estandar'),
    (104,1,3,70,'estandar'),
    (105,1,4,80,'estandar'),

    (201,2,2,120,'suite'),
    (202,2,2,120,'suite'),
    (203,2,3,140,'suite'),
    (204,2,4,160,'suite'),
    (205,2,4,160,'suite'),

    (301,3,2,200,'premium'),
    (302,3,2,200,'premium'),
    (303,3,3,220,'premium'),
    (304,3,4,250,'premium'),
    (305,3,4,250,'premium');

/* DETALLES */

INSERT INTO habitacion_estandar VALUES
                                    (1,TRUE),(2,TRUE),(3,TRUE),(4,TRUE),(5,TRUE);

INSERT INTO habitacion_suite VALUES
                                 (6,TRUE,'Mar',TRUE,TRUE),
                                 (7,TRUE,'Ciudad',FALSE,TRUE),
                                 (8,FALSE,'Piscina',TRUE,TRUE),
                                 (9,TRUE,'Montaña',TRUE,FALSE),
                                 (10,FALSE,NULL,FALSE,TRUE);

INSERT INTO habitacion_premium VALUES
                                   (11,TRUE,'Mar',TRUE),
                                   (12,TRUE,'Ciudad',TRUE),
                                   (13,FALSE,NULL,TRUE),
                                   (14,TRUE,'Montaña',TRUE),
                                   (15,TRUE,NULL,TRUE);

/* RESERVAS */

INSERT INTO reserva (id_cliente,id_habitacion,fecha_entrada,fecha_salida,estado)
VALUES
    (4,6,'2025-07-10','2025-07-15','finalizada'),
    (3,11,'2025-08-01','2025-08-10','finalizada');
