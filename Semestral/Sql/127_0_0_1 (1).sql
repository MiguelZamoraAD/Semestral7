-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-07-2025 a las 15:20:46
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `company_info`
--
CREATE DATABASE IF NOT EXISTS `company_info` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `company_info`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_libros`
--

DROP TABLE IF EXISTS `categorias_libros`;
CREATE TABLE IF NOT EXISTS `categorias_libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `descripcion` text,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `ruta_miniatura` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias_libros`
--

INSERT INTO `categorias_libros` (`id`, `titulo`, `descripcion`, `ruta_imagen`, `ruta_miniatura`, `fecha_creacion`) VALUES
(1, 'Química', '\"¡aprende sus secretos y podrás cambiar el mundo!\"', '../../uploads/categoryUp/1753308990_quimica.png', '../../thumbnails/categoryTh/1753308990_quimica.png', '2025-07-24 03:16:30'),
(2, 'Matemática', '\"Las matemáticas son la puerta y la llave de las ciencias.\"', '../../uploads/categoryUp/1753309017_matematicas.png', '../../thumbnails/categoryTh/1753309017_matematicas.png', '2025-07-24 03:16:57'),
(3, 'Sistemas', '\"El hardware es lo que hace a una máquina rápida; el software es lo que hace que una máquina rápida se vuelva lenta.\" ', '../../uploads/categoryUp/1753309038_sistemas.png', '../../thumbnails/categoryTh/1753309038_sistemas.png', '2025-07-24 03:17:18'),
(4, 'Lógica', '\"La lógica es el principio de la sabiduría, no el final.\" ', '../../uploads/categoryUp/1753309060_logica.png', '../../thumbnails/categoryTh/1753309060_logica.png', '2025-07-24 03:17:40'),
(5, 'Estadística', '\"La estadística es una ciencia que demuestra que si mi vecino tiene dos coches y yo ninguno, los dos tenemos uno.\" ', '../../uploads/categoryUp/1753314114_estadistica.png', '../../thumbnails/categoryTh/1753314114_estadistica.png', '2025-07-24 09:41:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `Cedula` varchar(20) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `SegundoN` varchar(50) DEFAULT NULL,
  `Apellido` varchar(50) NOT NULL,
  `SegundoA` varchar(50) DEFAULT NULL,
  `FechaNacimiento` date NOT NULL,
  `Carrera` varchar(100) NOT NULL,
  `Usuario` varchar(100) NOT NULL,
  PRIMARY KEY (`Cedula`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`Cedula`, `Nombre`, `SegundoN`, `Apellido`, `SegundoA`, `FechaNacimiento`, `Carrera`, `Usuario`) VALUES
('08-1001-2354', 'Miguel', 'Antonio', 'Zamora', 'Dominguez', '2009-07-16', 'Ingeniería en Sistemas', 'miguel2003zamora@gmail.com'),
('08-2324-2345', 'Jvariñio', 'Raul', 'Furtencio', 'Dominguez', '1999-11-11', 'Educación', 'miguel@gmail.com'),
('54-4416-5419', 'juan', 'rodrigo', 'perez', 'cascada', '2025-06-11', 'Ingeniería en Sistemas', 'adsnoa@gmail.com'),
('54-4416-5408', 'azsda1', 'adasd1', 'dasdad1', 'dasda1', '2025-06-11', 'Ingeniería en Sistemas', 'adsna@gmail.com'),
('53-4168-1650', 'DEAN98Y', 'DASD09A', 'DA789', 'DASD87', '2016-07-20', 'Ingeniería en Sistemas', 'asdaad@gmail.com'),
('53-4168-1651', 'DEAN98Y', 'DASD09A', 'DA789', 'DASD87', '2016-07-20', 'Ingeniería en Sistemas', 'asdawd@gmail.com'),
('35-7468-1300', 'asdae', 'raa', 'dwref', 'resfd', '2025-07-15', 'Ingeniería en Sistemas', 'qwed@gmail.com'),
('08-2324-7894', 'Kuturlo', 'Raul', 'CanterisaKa', 'Dominguez', '1999-11-11', 'Administración de Empresas', 'miguel.zamora@utp.ac.pa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(255) NOT NULL,
  `ipRemoto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deteccion_anomalia` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

DROP TABLE IF EXISTS `libros`;
CREATE TABLE IF NOT EXISTS `libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `ruta_miniatura` varchar(255) DEFAULT NULL,
  `unidades` int NOT NULL,
  `categoria` enum('Química','Sistemas','Lógica','Matemática','Estadística') NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `descripcion`, `ruta_imagen`, `ruta_miniatura`, `unidades`, `categoria`, `fecha_creacion`) VALUES
(1, 'Quimica', 'Leccion 1: como hacer C4:\r\nel c4 es uno de los materiales más sen...', '../../../uploads/bookUp/1753388863_reservar.png', '../../../thumbnails/bookTh/1753388863_reservar.png', 0, 'Química', '2025-07-25 01:27:43'),
(2, 'Matematicas Para estupidos', 'ven a aprender cuantos es 1+1', '../../../uploads/bookUp/1753390706_matematicas.png', '../../../thumbnails/bookTh/1753390706_matematicas.png', 997, 'Matemática', '2025-07-25 01:58:26'),
(3, 'Fundamento de las IA', 'descubre sobre un mundo con chatgpt', '../../../uploads/bookUp/1753391986_sistemas.png', '../../../thumbnails/bookTh/1753391986_sistemas.png', 20, 'Sistemas', '2025-07-25 02:19:46'),
(4, 'Fundamento de ChatGPt', 'sandoasinda', '../../../uploads/bookUp/1753395046_nodes.input.gamepad.leftstick+sdf2.png', '../../../thumbnails/bookTh/1753395046_nodes.input.gamepad.leftstick+sdf2.png', 20, 'Sistemas', '2025-07-25 03:10:46'),
(5, 'Cath Gpty 2', 'caht ', '../../../uploads/bookUp/1753451660_nodes.input.gamepad.leftstick+sdf2.png', '../../../thumbnails/bookTh/1753451660_nodes.input.gamepad.leftstick+sdf2.png', 9, 'Sistemas', '2025-07-25 18:54:20'),
(6, 'Quimica para expeciales', 'es un libro muy importante para personas muy especiales no por tu madre si no por un doctor', '../../../uploads/bookUp/1753456554_nodes.input.gamepad.west+sdf2.png', '../../../thumbnails/bookTh/1753456554_nodes.input.gamepad.west+sdf2.png', 31, 'Química', '2025-07-25 20:15:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libro_id` int DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha_reserva` date DEFAULT NULL,
  `dias_reservado` int DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `estado` enum('reservado','devuelto') DEFAULT 'reservado',
  PRIMARY KEY (`id`),
  KEY `libro_id` (`libro_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `libro_id`, `usuario`, `fecha_reserva`, `dias_reservado`, `fecha_devolucion`, `estado`) VALUES
(1, 1, 'miguel2003zamora@gmail.com', '2025-07-25', 5, '2025-07-30', 'devuelto'),
(2, 1, 'miguel2003zamora@gmail.com', '2025-07-25', 5, '2025-07-30', 'devuelto'),
(3, 1, 'miguel2003zamora@gmail.com', '2025-07-25', 25, '2025-08-19', 'devuelto'),
(4, 1, 'miguel2003zamora@gmail.com', '2025-07-25', 25, '2025-08-19', 'devuelto'),
(5, 1, 'miguel2003zamora@gmail.com', '2025-07-25', 25, '2025-08-19', 'devuelto'),
(6, 2, 'miguel2003zamora@gmail.com', '2025-07-25', 25, '2025-08-19', 'devuelto'),
(7, 2, 'miguel2003zamora@gmail.com', '2025-07-25', 30, '2025-08-24', 'devuelto'),
(8, 3, 'miguel2003zamora@gmail.com', '2025-07-25', 30, '2025-08-24', 'reservado'),
(9, 4, 'miguel2003zamora@gmail.com', '2025-07-25', 25, '2025-08-19', 'devuelto'),
(10, 2, 'miguel2003zamora@gmail.com', '2025-07-25', 30, '2025-08-24', 'reservado'),
(11, 1, 'miguel.zamora@utp.ac.pa', '2025-07-25', 30, '2025-08-24', 'reservado'),
(12, 2, 'miguel.zamora@utp.ac.pa', '2025-07-25', 30, '2025-08-24', 'reservado'),
(13, 3, 'miguel.zamora@utp.ac.pa', '2025-07-25', 30, '2025-08-24', 'reservado'),
(14, 4, 'miguel.zamora@utp.ac.pa', '2025-07-25', 30, '2025-08-24', 'reservado'),
(15, 4, 'miguel2003zamora@gmail.com', '2025-07-25', 4, '2025-07-27', 'reservado'),
(16, 5, 'miguel2003zamora@gmail.com', '2025-07-25', 5, '2025-07-30', 'devuelto'),
(17, 5, 'miguel2003zamora@gmail.com', '2025-07-25', 31, '2025-08-25', 'reservado'),
(18, 6, 'miguel.zamora@utp.ac.pa', '2025-07-25', 31, '2025-08-25', 'reservado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  `Apellido` varchar(255) NOT NULL,
  `Usuario` varchar(255) NOT NULL,
  `Tipo` varchar(255) NOT NULL,
  `Sexo` varchar(255) NOT NULL,
  `HashMagic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `Nombre`, `Apellido`, `Usuario`, `Tipo`, `Sexo`, `HashMagic`) VALUES
(1, 'Miguel', 'Zamora', 'miguel2003zamora@gmail.com', 'Adm', 'M', '$2y$10$BLEWlegE9xD63gvoqVcoF.GBU1eYGuN8b3MRfWeGYaE7lI6odNYxG'),
(2, 'Jvariñio', 'Furtencio', 'miguel@gmail.com', 'user', 'M', '$2y$10$.XrgFYLEUahcvLCzURwQBeIkzK564JM/6IW0.jXXqCp.qmNrRJ.au'),
(21, 'juan', 'perez', 'adsnoa@gmail.com', 'user', 'M', '$2y$10$H83BfXPJPTH7VAHZBA2kheZZxxe2jddFQFRH9MIwJ0WChWu8Ghgli'),
(25, 'azsda1', 'dasdad1', 'adsna@gmail.com', 'user', 'M', '$2y$10$UHMez3y1p7cVMUVnAIBhPuIVKilMYpRHoYPKUXBXNdGq/MgA8rTOq'),
(31, 'DEAN98Y', 'DA789', 'asdawd@gmail.com', 'user', 'M', '$2y$10$9DtHfDoefDv2dE./Yq4jP.j3BMqWAEJzn71BSmfGO6q.cENlW9VYq'),
(30, 'DEAN98Y', 'DA789', 'asdaad@gmail.com', 'user', 'F', '$2y$10$pscST8ce/vLp7ZGk9gWl3ePsAw4BHcOe1iDWlK00EuIwprr5pLghm'),
(34, 'asdae', 'dwref', 'qwed@gmail.com', 'user', 'M', '$2y$10$LcAP3DkYXB7XfeGHZ.EBguf0.ZLhViuMQNgNoPxl/CHhwtYbqkA7.'),
(38, 'Kuturlo', 'CanterisaKa', 'miguel.zamora@utp.ac.pa', 'user', 'M', '$2y$10$wEXYSq1PMRh.FMOXS6157e8xdNTeLSyJtw8wCHvnElJk5GhYlGMoe');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estadisticas_libros`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vista_estadisticas_libros`;
CREATE TABLE IF NOT EXISTS `vista_estadisticas_libros` (
`libro_id` int
,`titulo` varchar(150)
,`veces_reservado` bigint
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estadisticas_libros`
--
DROP TABLE IF EXISTS `vista_estadisticas_libros`;

DROP VIEW IF EXISTS `vista_estadisticas_libros`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_libros`  AS SELECT `l`.`id` AS `libro_id`, `l`.`titulo` AS `titulo`, count(`r`.`id`) AS `veces_reservado` FROM (`libros` `l` join `reservas` `r` on((`l`.`id` = `r`.`libro_id`))) WHERE (`r`.`estado` = 'reservado') GROUP BY `l`.`id` ORDER BY `veces_reservado` DESC ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
