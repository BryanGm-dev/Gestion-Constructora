-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2024 a las 23:56:55
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
-- Base de datos: `constructora`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`cliente_id`, `nombre`, `direccion`, `email`) VALUES
(2, 'Jose Pineda', 'Calle 12, San Salvador', 'bryanG@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantes`
--

CREATE TABLE `comprobantes` (
  `comprobante_id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `costo_total` decimal(10,2) DEFAULT NULL,
  `fecha_generacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `empleado_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `puesto_id` int(11) DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`empleado_id`, `nombre`, `puesto_id`, `salario`, `email`) VALUES
(1, 'Bryan Gonzalez', 5, 1200.00, 'bryan@gmail.com'),
(2, 'Jose Pineda', 1, 15000.00, 'joseP@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinaria`
--

CREATE TABLE `maquinaria` (
  `maquinaria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Disponible','En uso','En mantenimiento') DEFAULT 'Disponible',
  `fecha_adquisicion` date DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `maquinaria`
--

INSERT INTO `maquinaria` (`maquinaria_id`, `nombre`, `descripcion`, `estado`, `fecha_adquisicion`, `costo`, `tipo`) VALUES
(1, 'Excavadora Komatsu PC200', 'Excavadora de 20 toneladas para trabajos de movimiento de tierra.', 'Disponible', '0000-00-00', 160.00, 'Excavadora');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia_prima`
--

CREATE TABLE `materia_prima` (
  `materia_prima_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `cantidad_en_stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `notificacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `proyecto_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `imagen` longblob DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_reprogramada` date DEFAULT NULL,
  `estado` enum('NI','EE','PF') DEFAULT 'NI',
  `porcentaje_avance` decimal(5,2) DEFAULT NULL,
  `inconvenientes` text DEFAULT NULL,
  `tipo_proyecto_id` int(11) DEFAULT NULL,
  `inversion_inicial` decimal(10,2) DEFAULT NULL,
  `inversion_final` decimal(10,2) DEFAULT NULL,
  `porcentaje_utilidad` decimal(5,2) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`proyecto_id`, `nombre`, `descripcion`, `ubicacion`, `imagen`, `fecha_inicio`, `fecha_fin`, `fecha_reprogramada`, `estado`, `porcentaje_avance`, `inconvenientes`, `tipo_proyecto_id`, `inversion_inicial`, `inversion_final`, `porcentaje_utilidad`, `cliente_id`) VALUES
(4, 'BINAES', 'Edificio', 'San Salvador', '2024-11-08', '2024-11-09', '2024-11-10', 'EE', 70.00, NULL, NULL, 150.00, 205.00, NULL, NULL);
INSERT INTO `proyectos` (`proyecto_id`, `nombre`, `descripcion`, `ubicacion`, `imagen`, `fecha_inicio`, `fecha_fin`, `fecha_reprogramada`, `estado`, `porcentaje_avance`, `inconvenientes`, `tipo_proyecto_id`, `inversion_inicial`, `inversion_final`, `porcentaje_utilidad`, `cliente_id`) VALUES
(5, 'Casa', 'Casa', 'San Salvador', NULL, '2024-11-09', '2024-11-10', '2024-11-11', 'NI', 0.00, NULL, NULL, 3000.00, 5000.00, NULL, NULL),
(6, 'Escuela', 'Escuela', 'San Salvador', NULL, '2024-11-09', '2024-11-10', '0000-00-00', 'PF', 100.00, NULL, NULL, 10000.00, 20000.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_empleados`
--

CREATE TABLE `proyectos_empleados` (
  `proyecto_empleado_id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `rol_en_proyecto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_maquinaria`
--

CREATE TABLE `proyectos_maquinaria` (
  `proyecto_maquinaria_id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `maquinaria_id` int(11) NOT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `fecha_liberacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_materia_prima`
--

CREATE TABLE `proyectos_materia_prima` (
  `proyecto_materia_prima_id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `materia_prima_id` int(11) NOT NULL,
  `cantidad_utilizada` int(11) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `fecha_utilizacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `puesto_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puestos`
--

INSERT INTO `puestos` (`puesto_id`, `nombre`) VALUES
(8, 'Albañil'),
(4, 'Arquitecto'),
(5, 'Asistente Administrativo'),
(6, 'Contador'),
(9, 'Electricista'),
(1, 'Gerente de Proyecto'),
(2, 'Ingeniero Civil'),
(7, 'Operador de Maquinaria'),
(10, 'Plomero'),
(3, 'Supervisor de Obra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre`) VALUES
(1, 'Administrador'),
(3, 'Encargado de Maquinaria'),
(2, 'Gerente de Proyectos'),
(4, 'Supervisor de Recursos Humanos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos_clientes`
--

CREATE TABLE `telefonos_clientes` (
  `telefono_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `numero_telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos_empleados`
--

CREATE TABLE `telefonos_empleados` (
  `telefono_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `numero_telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_proyecto`
--

CREATE TABLE `tipos_proyecto` (
  `tipo_proyecto_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `username`, `password_hash`, `rol_id`, `nombre`, `email`) VALUES
(12, 'Bryan123', '$2y$10$sTeRr2BmLfEFfbjYqDAsn.FLG1EfmgCVNrSDEE5EcqOj.kOLzbmdK', 1, 'BryanE', 'bryanGmm@gmail.com'),
(19, 'EliasG', '$2y$10$Yu0gjpiQUEH..uDabAPVK.Ee7mzyIzwoAq0nzMIfiMFvVUgqpx6Zm', 3, 'Elias', 'elias@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliente_id`);

--
-- Indices de la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD PRIMARY KEY (`comprobante_id`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`empleado_id`),
  ADD KEY `puesto_id` (`puesto_id`);

--
-- Indices de la tabla `maquinaria`
--
ALTER TABLE `maquinaria`
  ADD PRIMARY KEY (`maquinaria_id`);

--
-- Indices de la tabla `materia_prima`
--
ALTER TABLE `materia_prima`
  ADD PRIMARY KEY (`materia_prima_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`notificacion_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`proyecto_id`),
  ADD KEY `tipo_proyecto_id` (`tipo_proyecto_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `proyectos_empleados`
--
ALTER TABLE `proyectos_empleados`
  ADD PRIMARY KEY (`proyecto_empleado_id`),
  ADD UNIQUE KEY `proyecto_id` (`proyecto_id`,`empleado_id`,`fecha_asignacion`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `proyectos_maquinaria`
--
ALTER TABLE `proyectos_maquinaria`
  ADD PRIMARY KEY (`proyecto_maquinaria_id`),
  ADD UNIQUE KEY `proyecto_id` (`proyecto_id`,`maquinaria_id`,`fecha_asignacion`),
  ADD KEY `maquinaria_id` (`maquinaria_id`);

--
-- Indices de la tabla `proyectos_materia_prima`
--
ALTER TABLE `proyectos_materia_prima`
  ADD PRIMARY KEY (`proyecto_materia_prima_id`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `materia_prima_id` (`materia_prima_id`);

--
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`puesto_id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `telefonos_clientes`
--
ALTER TABLE `telefonos_clientes`
  ADD PRIMARY KEY (`telefono_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `telefonos_empleados`
--
ALTER TABLE `telefonos_empleados`
  ADD PRIMARY KEY (`telefono_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `tipos_proyecto`
--
ALTER TABLE `tipos_proyecto`
  ADD PRIMARY KEY (`tipo_proyecto_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  MODIFY `comprobante_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `empleado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `maquinaria`
--
ALTER TABLE `maquinaria`
  MODIFY `maquinaria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materia_prima`
--
ALTER TABLE `materia_prima`
  MODIFY `materia_prima_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `notificacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `proyecto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `proyectos_empleados`
--
ALTER TABLE `proyectos_empleados`
  MODIFY `proyecto_empleado_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos_maquinaria`
--
ALTER TABLE `proyectos_maquinaria`
  MODIFY `proyecto_maquinaria_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos_materia_prima`
--
ALTER TABLE `proyectos_materia_prima`
  MODIFY `proyecto_materia_prima_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `puesto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `telefonos_clientes`
--
ALTER TABLE `telefonos_clientes`
  MODIFY `telefono_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `telefonos_empleados`
--
ALTER TABLE `telefonos_empleados`
  MODIFY `telefono_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_proyecto`
--
ALTER TABLE `tipos_proyecto`
  MODIFY `tipo_proyecto_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comprobantes`
--
ALTER TABLE `comprobantes`
  ADD CONSTRAINT `comprobantes_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`proyecto_id`),
  ADD CONSTRAINT `comprobantes_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`puesto_id`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`tipo_proyecto_id`) REFERENCES `tipos_proyecto` (`tipo_proyecto_id`),
  ADD CONSTRAINT `proyectos_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`);

--
-- Filtros para la tabla `proyectos_empleados`
--
ALTER TABLE `proyectos_empleados`
  ADD CONSTRAINT `proyectos_empleados_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`proyecto_id`),
  ADD CONSTRAINT `proyectos_empleados_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`empleado_id`);

--
-- Filtros para la tabla `proyectos_maquinaria`
--
ALTER TABLE `proyectos_maquinaria`
  ADD CONSTRAINT `proyectos_maquinaria_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`proyecto_id`),
  ADD CONSTRAINT `proyectos_maquinaria_ibfk_2` FOREIGN KEY (`maquinaria_id`) REFERENCES `maquinaria` (`maquinaria_id`);

--
-- Filtros para la tabla `proyectos_materia_prima`
--
ALTER TABLE `proyectos_materia_prima`
  ADD CONSTRAINT `proyectos_materia_prima_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`proyecto_id`),
  ADD CONSTRAINT `proyectos_materia_prima_ibfk_2` FOREIGN KEY (`materia_prima_id`) REFERENCES `materia_prima` (`materia_prima_id`);

--
-- Filtros para la tabla `telefonos_clientes`
--
ALTER TABLE `telefonos_clientes`
  ADD CONSTRAINT `telefonos_clientes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`);

--
-- Filtros para la tabla `telefonos_empleados`
--
ALTER TABLE `telefonos_empleados`
  ADD CONSTRAINT `telefonos_empleados_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`empleado_id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
