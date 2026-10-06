-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2026 a las 03:39:01
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
-- Estructura de tabla para la tabla `anlisis_sangre`
--

CREATE TABLE `anlisis_sangre` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Tipo_Analisis` varchar(100) DEFAULT NULL,
  `Resultado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bano_peluqueria`
--

CREATE TABLE `bano_peluqueria` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Productos_Utilizados` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carnet_vacunacion`
--

CREATE TABLE `carnet_vacunacion` (
  `ID_Vacunacion` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cirugia`
--

CREATE TABLE `cirugia` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Tipo_Operacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

CREATE TABLE `cita` (
  `ID_Cita` int(8) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Motivo` varchar(100) DEFAULT NULL,
  `ID_Servicios` int(8) DEFAULT NULL,
  `CI` int(8) DEFAULT NULL,
  `Estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `CI` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `CI` int(8) NOT NULL,
  `Rol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
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
-- Estructura de tabla para la tabla `historial_clinico`
--

CREATE TABLE `historial_clinico` (
  `ID_Historial` int(8) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Diagnostico` int(10) DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `CI` int(8) DEFAULT NULL,
  `ID_Mascotas` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `internacion`
--

CREATE TABLE `internacion` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Habitación` int(11) DEFAULT NULL,
  `Fecha_Ingreso` date DEFAULT NULL,
  `Fecha_Salida` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_factura`
--

CREATE TABLE `linea_factura` (
  `ID_Factura` int(8) NOT NULL,
  `ID_Linea` int(8) NOT NULL,
  `Detalle` varchar(100) DEFAULT NULL,
  `Numero_Factura` int(10) DEFAULT NULL,
  `IVA` decimal(5,4) DEFAULT NULL,
  `Total` int(10) DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pencionado_gato`
--

CREATE TABLE `pencionado_gato` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Duracion_Pencionado` varchar(100) DEFAULT NULL,
  `Habitacion` varchar(100) DEFAULT NULL,
  `Fecha_Ingreso` date DEFAULT NULL,
  `Fecha_Salida` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `CI` int(8) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Dirección` varchar(100) NOT NULL,
  `Departamento` varchar(100) NOT NULL,
  `Contrasena` varchar(100) NOT NULL,
  `Gmail` varchar(320) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Nombre_Producto` varchar(100) DEFAULT NULL,
  `Precio_Unitario` decimal(10,2) DEFAULT NULL,
  `Categoria` varchar(100) DEFAULT NULL,
  `Stock` int(100) DEFAULT NULL,
  `ID_Proveedor` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `ID_Proveedor` int(8) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Email` varchar(320) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `Departamento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `telefonos_persona`
--

CREATE TABLE `telefonos_persona` (
  `CI` int(8) DEFAULT NULL,
  `Telefono` int(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos_proveedor`
--

CREATE TABLE `telefonos_proveedor` (
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
-- Estructura de tabla para la tabla `vacunacion`
--

CREATE TABLE `vacunacion` (
  `ID_Servicios` int(8) DEFAULT NULL,
  `Tipo_Vacunacion` varchar(100) DEFAULT NULL,
  `Motivo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anlisis_sangre`
--
ALTER TABLE `anlisis_sangre`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `bano_peluqueria`
--
ALTER TABLE `bano_peluqueria`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `carnet_vacunacion`
--
ALTER TABLE `carnet_vacunacion`
  ADD PRIMARY KEY (`ID_Vacunacion`);

--
-- Indices de la tabla `cirugia`
--
ALTER TABLE `cirugia`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`ID_Cita`),
  ADD KEY `ID_Servicios` (`ID_Servicios`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`ID_Factura`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `CI` (`CI`),
  ADD KEY `ID_Mascotas` (`ID_Mascotas`);

--
-- Indices de la tabla `internacion`
--
ALTER TABLE `internacion`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `linea_factura`
--
ALTER TABLE `linea_factura`
  ADD PRIMARY KEY (`ID_Factura`,`ID_Linea`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`ID_Mascotas`),
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `pencionado_gato`
--
ALTER TABLE `pencionado_gato`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
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
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD KEY `ID_Servicios` (`ID_Servicios`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
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
-- Indices de la tabla `telefonos_persona`
--
ALTER TABLE `telefonos_persona`
  ADD KEY `CI` (`CI`);

--
-- Indices de la tabla `telefonos_proveedor`
--
ALTER TABLE `telefonos_proveedor`
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD KEY `ID_Historial` (`ID_Historial`);

--
-- Indices de la tabla `vacunacion`
--
ALTER TABLE `vacunacion`
  ADD KEY `ID_Servicios` (`ID_Servicios`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anlisis_sangre`
--
ALTER TABLE `anlisis_sangre`
  ADD CONSTRAINT `anlisis_sangre_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bano_peluqueria`
--
ALTER TABLE `bano_peluqueria`
  ADD CONSTRAINT `bano_peluqueria_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cirugia`
--
ALTER TABLE `cirugia`
  ADD CONSTRAINT `cirugia_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `cita_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE,
  ADD CONSTRAINT `cita_ibfk_2` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  ADD CONSTRAINT `historial_clinico_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_clinico_ibfk_2` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `internacion`
--
ALTER TABLE `internacion`
  ADD CONSTRAINT `internacion_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `linea_factura`
--
ALTER TABLE `linea_factura`
  ADD CONSTRAINT `linea_factura_ibfk_1` FOREIGN KEY (`ID_Factura`) REFERENCES `factura` (`ID_Factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pencionado_gato`
--
ALTER TABLE `pencionado_gato`
  ADD CONSTRAINT `pencionado_gato_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pertenece`
--
ALTER TABLE `pertenece`
  ADD CONSTRAINT `pertenece_ibfk_1` FOREIGN KEY (`ID_Factura`,`ID_Linea`) REFERENCES `linea_factura` (`ID_Factura`, `ID_Linea`) ON DELETE CASCADE,
  ADD CONSTRAINT `pertenece_ibfk_2` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posee`
--
ALTER TABLE `posee`
  ADD CONSTRAINT `posee_ibfk_1` FOREIGN KEY (`ID_Vacunacion`) REFERENCES `carnet_vacunacion` (`ID_Vacunacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `posee_ibfk_2` FOREIGN KEY (`ID_Mascotas`) REFERENCES `mascotas` (`ID_Mascotas`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `realizan`
--
ALTER TABLE `realizan`
  ADD CONSTRAINT `realizan_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `solicitan_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitan_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitan_ibfk_3` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonos_persona`
--
ALTER TABLE `telefonos_persona`
  ADD CONSTRAINT `telefonos_persona_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `persona` (`CI`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonos_proveedor`
--
ALTER TABLE `telefonos_proveedor`
  ADD CONSTRAINT `telefonos_proveedor_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD CONSTRAINT `tratamientos_ibfk_1` FOREIGN KEY (`ID_Historial`) REFERENCES `historial_clinico` (`ID_Historial`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vacunacion`
--
ALTER TABLE `vacunacion`
  ADD CONSTRAINT `vacunacion_ibfk_1` FOREIGN KEY (`ID_Servicios`) REFERENCES `servicios` (`ID_Servicios`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
