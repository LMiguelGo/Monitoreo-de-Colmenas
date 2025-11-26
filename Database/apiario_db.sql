-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 23:12:16
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
-- Base de datos: `apiario_db_2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actuadores`
--

CREATE TABLE `actuadores` (
  `id` int(11) NOT NULL,
  `nombre` enum('compuertas','calefactor') NOT NULL,
  `estado` float NOT NULL,
  `tipo_estado` enum('angulo','temperatura') NOT NULL,
  `colmena_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apicultores`
--

CREATE TABLE `apicultores` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `numero_id` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `direccion` varchar(150) DEFAULT NULL,
  `user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `chat_id` varchar(50) DEFAULT NULL,
  `bot_token` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colmenas`
--

CREATE TABLE `colmenas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitud` decimal(9,6) DEFAULT NULL,
  `longitud` decimal(9,6) DEFAULT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `dimensiones` varchar(50) DEFAULT NULL,
  `poblacion_abejas` int(11) DEFAULT NULL,
  `apicultor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_colmena`
--

CREATE TABLE `control_colmena` (
  `id` int(11) NOT NULL,
  `modo` enum('manual','automatico') NOT NULL,
  `colmena_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_medidos`
--

CREATE TABLE `datos_medidos` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `temperatura` decimal(5,2) NOT NULL,
  `humedad` decimal(5,2) NOT NULL,
  `actividad_entrante` int(11) NOT NULL,
  `actividad_saliente` int(11) NOT NULL,
  `colmena_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `investigadores`
--

CREATE TABLE `investigadores` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `numero_id` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `direccion` varchar(150) DEFAULT NULL,
  `user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `umbrales`
--

CREATE TABLE `umbrales` (
  `id` int(11) NOT NULL,
  `temp_min` decimal(5,2) NOT NULL,
  `temp_max` decimal(5,2) NOT NULL,
  `hum_min` decimal(5,2) NOT NULL,
  `hum_max` decimal(5,2) NOT NULL,
  `activ_min` decimal(10,2) NOT NULL,
  `activ_max` decimal(10,2) NOT NULL,
  `colmena_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actuadores`
--
ALTER TABLE `actuadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`,`colmena_id`),
  ADD KEY `colmena_id` (`colmena_id`);

--
-- Indices de la tabla `apicultores`
--
ALTER TABLE `apicultores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_id` (`numero_id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- Indices de la tabla `colmenas`
--
ALTER TABLE `colmenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_apicultor` (`apicultor_id`);

--
-- Indices de la tabla `control_colmena`
--
ALTER TABLE `control_colmena`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_colmena` (`colmena_id`);

--
-- Indices de la tabla `datos_medidos`
--
ALTER TABLE `datos_medidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `colmena_id` (`colmena_id`);

--
-- Indices de la tabla `investigadores`
--
ALTER TABLE `investigadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_id` (`numero_id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- Indices de la tabla `umbrales`
--
ALTER TABLE `umbrales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_colmena` (`colmena_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actuadores`
--
ALTER TABLE `actuadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT de la tabla `apicultores`
--
ALTER TABLE `apicultores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `colmenas`
--
ALTER TABLE `colmenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `control_colmena`
--
ALTER TABLE `control_colmena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `datos_medidos`
--
ALTER TABLE `datos_medidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2888;

--
-- AUTO_INCREMENT de la tabla `investigadores`
--
ALTER TABLE `investigadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `umbrales`
--
ALTER TABLE `umbrales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actuadores`
--
ALTER TABLE `actuadores`
  ADD CONSTRAINT `actuadores_ibfk_1` FOREIGN KEY (`colmena_id`) REFERENCES `colmenas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `colmenas`
--
ALTER TABLE `colmenas`
  ADD CONSTRAINT `fk_apicultor` FOREIGN KEY (`apicultor_id`) REFERENCES `apicultores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `control_colmena`
--
ALTER TABLE `control_colmena`
  ADD CONSTRAINT `control_colmena_ibfk_1` FOREIGN KEY (`colmena_id`) REFERENCES `colmenas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `datos_medidos`
--
ALTER TABLE `datos_medidos`
  ADD CONSTRAINT `datos_medidos_ibfk_1` FOREIGN KEY (`colmena_id`) REFERENCES `colmenas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `umbrales`
--
ALTER TABLE `umbrales`
  ADD CONSTRAINT `fk_colmena` FOREIGN KEY (`colmena_id`) REFERENCES `colmenas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
