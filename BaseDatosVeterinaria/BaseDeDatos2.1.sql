-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-10-2026 a las 22:50:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdd_equipo6`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anlisis_sangres`
--

CREATE TABLE `anlisis_sangres` (
  `ID_Servicios` int(8) NOT NULL,
  `Tipo_Analisis` varchar(100) DEFAULT NULL,
  `Resultado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banos_peluquerias`
--

CREATE TABLE `banos_peluquerias` (
  `ID_Servicios` int(8) NOT NULL,
  `Productos_Utilizados` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carnet_vacunaciones`
--

CREATE TABLE `carnet_vacunaciones` (
  `ID_Vacunacion` int(8) NOT NULL,
  `Nombre_Vacuna` varchar(100) DEFAULT NULL,
  `Fecha_Aplicacion` date DEFAULT NULL,
  `Fecha_Vencimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cirugias`
--

CREATE TABLE `cirugias` (
  `ID_Servicios` int(8) NOT NULL,
  `Tipo_Operacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `ID_Cita` int(8) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Motivo` varchar(100) DEFAULT NULL,
  `ID_Servicios` int(8) DEFAULT NULL,
  `CI` int(8) DEFAULT NULL,
  `Estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `CI` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`CI`) VALUES
(123);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `CI` int(8) NOT NULL,
  `Rol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `ID_Factura` int(8) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Numero_Factura` int(10) DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `CI` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historiales_clinicos`
--

CREATE TABLE `historiales_clinicos` (
  `ID_Historial` int(8) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Diagnostico` varchar(100) DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `CI` int(8) DEFAULT NULL,
  `ID_Mascotas` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `internaciones`
--

CREATE TABLE `internaciones` (
  `ID_Servicios` int(8) NOT NULL,
  `Habitación` int(10) DEFAULT NULL,
  `Fecha_Ingreso` date DEFAULT NULL,
  `Fecha_Salida` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_facturas`
--

CREATE TABLE `lineas_facturas` (
  `ID_Factura` int(8) NOT NULL,
  `ID_Linea` int(8) NOT NULL,
  `Detalle` varchar(100) DEFAULT NULL,
  `Numero_Factura` int(10) DEFAULT NULL,
  `IVA` decimal(10,0) DEFAULT NULL,
  `Total` decimal(10,0) DEFAULT NULL,
  `Metodo_Pago` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `ID_Mascotas` int(8) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Especie` varchar(100) DEFAULT NULL,
  `Raza` varchar(100) DEFAULT NULL,
  `Estado` varchar(100) DEFAULT NULL,
  `Sexo` varchar(100) DEFAULT NULL,
  `CI` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`ID_Mascotas`, `Nombre`, `Especie`, `Raza`, `Estado`, `Sexo`, `CI`) VALUES
(0, 'perro1', 'dalmata', 'manchitas?', 'vivo', 'macho', 123);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pencionados_gatos`
--

CREATE TABLE `pencionados_gatos` (
  `ID_Servicios` int(8) NOT NULL,
  `Duracion_Pencionado` varchar(100) DEFAULT NULL,
  `Habitacion` int(10) DEFAULT NULL,
  `Fecha_Ingreso` date DEFAULT NULL,
  `Fecha_Salida` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `CI` int(8) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Dirección` varchar(100) NOT NULL,
  `Departamento` varchar(100) NOT NULL,
  `Contrasena` varchar(100) NOT NULL,
  `Gmail` varchar(320) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`CI`, `Nombre`, `Apellido`, `Dirección`, `Departamento`, `Contrasena`, `Gmail`) VALUES
(123, 'teseo', 'jaso', 'juan jose muños', 'maldonado', '123', 'teseoteseo22@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pertenece`
--

CREATE TABLE `pertenece` (
  `ID_Linea` int(8) DEFAULT NULL,
  `ID_Factura` int(8) DEFAULT NULL,
  `ID_Servicios` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posee`
--

CREATE TABLE `posee` (
  `ID_Vacunacion` int(8) DEFAULT NULL,
  `ID_Mascotas` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_Servicios` int(8) NOT NULL,
  `Nombre_Producto` varchar(100) DEFAULT NULL,
  `Categoria` varchar(100) DEFAULT NULL,
  `Stock` int(10) DEFAULT NULL,
  `ID_Proveedor` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ID_Servicios`, `Nombre_Producto`, `Categoria`, `Stock`, `ID_Proveedor`) VALUES
(1, 'prueba1', 'catPrueba1', 1, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `ID_Proveedor` int(8) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Gmail` varchar(320) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `Departamento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`ID_Proveedor`, `Nombre`, `Gmail`, `Direccion`, `Departamento`) VALUES
(33, 'prueba1', 'teseo', '2 22 2', 'maldo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realizan`
--

CREATE TABLE `realizan` (
  `CI` int(8) DEFAULT NULL,
  `ID_Mascotas` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `ID_Servicios` int(8) NOT NULL,
  `Costo` decimal(10,0) DEFAULT NULL,
  `Descripcion` varchar(250) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `ID_Mascotas` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`ID_Servicios`, `Costo`, `Descripcion`, `Fecha`, `ID_Mascotas`) VALUES
(1, 10, 'en esta ocasión vamos a ver que pasa, jeje.', '2026-10-29', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitan`
--

CREATE TABLE `solicitan` (
  `CI` int(8) DEFAULT NULL,
  `ID_Proveedor` int(8) DEFAULT NULL,
  `ID_Servicios` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos_personas`
--

CREATE TABLE `telefonos_personas` (
  `CI` int(8) DEFAULT NULL,
  `Telefono` int(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos_proveedores`
--

CREATE TABLE `telefonos_proveedores` (
  `ID_Proveedor` int(8) DEFAULT NULL,
  `Contacto` int(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE `tratamientos` (
  `ID_Historial` int(8) DEFAULT NULL,
  `Tratamiento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunaciones`
--

CREATE TABLE `vacunaciones` (
  `ID_Servicios` int(8) NOT NULL,
  `Tipo_Vacunacion` varchar(100) DEFAULT NULL,
  `Motivo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacunaciones`
--

INSERT INTO `vacunaciones` (`ID_Servicios`, `Tipo_Vacunacion`, `Motivo`) VALUES
(1, 'vacuna1', 'por que se me canta');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anlisis_sangres`
--
ALTER TABLE `anlisis_sangres`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Indices de la tabla `banos_peluquerias`
--
ALTER TABLE `banos_peluquerias`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Indices de la tabla `carnet_vacunaciones`
--
ALTER TABLE `carnet_vacunaciones`
  ADD PRIMARY KEY (`ID_Vacunacion`);

--
-- Indices de la tabla `cirugias`
--
ALTER TABLE `cirugias`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`ID_Cita`),
  ADD KEY `ID_Servicios` (`ID_Servicios`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`ID_Factura`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `historiales_clinicos`
--
ALTER TABLE `historiales_clinicos`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `CI` (`CI`),
  ADD KEY `ID_Mascotas` (`ID_Mascotas`);

--
-- Indices de la tabla `internaciones`
--
ALTER TABLE `internaciones`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Indices de la tabla `lineas_facturas`
--
ALTER TABLE `lineas_facturas`
  ADD PRIMARY KEY (`ID_Factura`,`ID_Linea`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`ID_Mascotas`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `pencionados_gatos`
--
ALTER TABLE `pencionados_gatos`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `pertenece`
--
ALTER TABLE `pertenece`
  ADD KEY `ID_Factura` (`ID_Factura`,`ID_Linea`),
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `posee`
--
ALTER TABLE `posee`
  ADD KEY `ID_Vacunacion` (`ID_Vacunacion`),
  ADD KEY `ID_Mascotas` (`ID_Mascotas`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_Servicios`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`ID_Proveedor`);

--
-- Indices de la tabla `realizan`
--
ALTER TABLE `realizan`
  ADD KEY `CI` (`CI`),
  ADD KEY `ID_Mascotas` (`ID_Mascotas`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`ID_Servicios`),
  ADD KEY `ID_Mascotas` (`ID_Mascotas`);

--
-- Indices de la tabla `solicitan`
--
ALTER TABLE `solicitan`
  ADD KEY `CI` (`CI`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`),
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `telefonos_personas`
--
ALTER TABLE `telefonos_personas`
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `telefonos_proveedores`
--
ALTER TABLE `telefonos_proveedores`
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD KEY `ID_Historial` (`ID_Historial`);

--
-- Indices de la tabla `vacunaciones`
--
ALTER TABLE `vacunaciones`
  ADD PRIMARY KEY (`ID_Servicios`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anlisis_sangres`
--
ALTER TABLE `anlisis_sangres`
  ADD CONSTRAINT `anlisis_sangres_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `banos_peluquerias`
--
ALTER TABLE `banos_peluquerias`
  ADD CONSTRAINT `banos_peluquerias_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cirugias`
--
ALTER TABLE `cirugias`
  ADD CONSTRAINT `cirugias_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historiales_clinicos`
--
ALTER TABLE `historiales_clinicos`
  ADD CONSTRAINT `historiales_clinicos_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE,
  ADD CONSTRAINT `historiales_clinicos_ibfk_2` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `internaciones`
--
ALTER TABLE `internaciones`
  ADD CONSTRAINT `internaciones_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lineas_facturas`
--
ALTER TABLE `lineas_facturas`
  ADD CONSTRAINT `lineas_facturas_ibfk_1` FOREIGN KEY (`ID_Factura`) REFERENCES `facturas` (`ID_Factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pencionados_gatos`
--
ALTER TABLE `pencionados_gatos`
  ADD CONSTRAINT `pencionados_gatos_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pertenece`
--
ALTER TABLE `pertenece`
  ADD CONSTRAINT `pertenece_ibfk_1` FOREIGN KEY (`ID_Factura`,`ID_Linea`) REFERENCES `lineas_facturas` (`ID_Factura`, `ID_Linea`) ON DELETE CASCADE,
  ADD CONSTRAINT `pertenece_ibfk_2` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posee`
--
ALTER TABLE `posee`
  ADD CONSTRAINT `posee_ibfk_1` FOREIGN KEY (`ID_Vacunacion`) REFERENCES `carnet_vacunaciones` (`ID_Vacunacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `posee_ibfk_2` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `realizan`
--
ALTER TABLE `realizan`
  ADD CONSTRAINT `realizan_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE,
  ADD CONSTRAINT `realizan_ibfk_2` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitan`
--
ALTER TABLE `solicitan`
  ADD CONSTRAINT `solicitan_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitan_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitan_ibfk_3` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonos_personas`
--
ALTER TABLE `telefonos_personas`
  ADD CONSTRAINT `telefonos_personas_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `personas` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonos_proveedores`
--
ALTER TABLE `telefonos_proveedores`
  ADD CONSTRAINT `telefonos_proveedores_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD CONSTRAINT `tratamientos_ibfk_1` FOREIGN KEY (`ID_Historial`) REFERENCES `historiales_clinicos` (`ID_Historial`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vacunaciones`
--
ALTER TABLE `vacunaciones`
  ADD CONSTRAINT `vacunaciones_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
