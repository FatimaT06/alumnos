-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-02-2026 a las 08:14:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `escuela`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `grupo_id`, `activo`) VALUES
(1, 'Fatima', 'Zuñiga', 'Torres', 3, 1),
(2, 'Edgar', 'Prado', 'García', 4, 1),
(3, 'Angel', 'Robles', 'Barrientos', 3, 1),
(4, 'Martin', 'Tellez', 'Falcon', 1, 1),
(5, 'Gerardo', 'Vazquez', 'Jimenez', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id`, `nombre`, `abreviatura`, `activo`) VALUES
(1, 'Administración de Empresas', 'LAE', 1),
(2, 'Administración de Empresas Turísticas', 'LAET', 1),
(3, 'Relaciones Internacionales', 'LRI', 1),
(4, 'Contaduría Pública y Finanzas', 'LCPYF', 1),
(5, 'Derecho', 'LIDER', 1),
(6, 'Mercadotecnia y Publicidad', 'LMP', 1),
(7, 'Gastronomía', 'LGAS', 1),
(8, 'Periodismo y Ciencias de la Comunicación', 'LPCC', 1),
(9, 'Informática Administrativa y Fiscal', 'LIAF', 1),
(10, 'Pedagogía', 'LIPED', 1),
(11, 'Cultura Física y Educación del Deporte', 'LCFYED', 1),
(12, 'Idiomas', 'LIDIO', 1),
(13, 'Diseño Gráfico', 'LDG', 1),
(14, 'Diseño de Interiores', 'LDI', 1),
(15, 'Diseño de Modas', 'LDM', 1),
(16, 'Ingeniería en Sistemas Computacionales', 'ISC', 1),
(17, 'Ingeniería Mecánica Automotriz', 'IMA', 1),
(18, 'Ingeniería en Arquitectura', 'IAR', 1),
(19, 'Ingeniería en Logística y Transporte', 'ILT', 1),
(20, 'Psicología', 'LPSIC', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado`
--

CREATE TABLE `grado` (
  `id` int(11) NOT NULL,
  `grado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grado`
--

INSERT INTO `grado` (`id`, `grado`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `carrera_id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`, `carrera_id`, `turno_id`, `grado_id`, `activo`) VALUES
(1, 'LAET101-M', 2, 1, 1, 1),
(2, 'ISC1101-V', 16, 2, 11, 1),
(3, 'ISC1102-V', 16, 2, 11, 1),
(4, 'LDG1101-V', 13, 2, 11, 1),
(5, 'IAR1001-V', 18, 2, 10, 1),
(6, 'IAR1002-V', 18, 2, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `inicial` varchar(2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `nombre`, `inicial`, `activo`) VALUES
(1, 'Matutino', 'M', 1),
(2, 'Vespertino', 'V', 1),
(3, 'Mixto', 'MX', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alumno_grupo` (`grupo_id`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grado`
--
ALTER TABLE `grado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_grupo_carrera` (`carrera_id`),
  ADD KEY `fk_grupo_turno` (`turno_id`),
  ADD KEY `fk_grupo_grado` (`grado_id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `grado`
--
ALTER TABLE `grado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `fk_alumno_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupo_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`),
  ADD CONSTRAINT `fk_grupo_grado` FOREIGN KEY (`grado_id`) REFERENCES `grado` (`id`),
  ADD CONSTRAINT `fk_grupo_turno` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
