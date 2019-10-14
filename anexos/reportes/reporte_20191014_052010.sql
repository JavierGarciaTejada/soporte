-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2019 a las 19:18:56
-- Versión del servidor: 10.3.16-MariaDB
-- Versión de PHP: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `evaluacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ad_sig`
--

CREATE TABLE `ad_sig` (
  `id` bigint(20) NOT NULL COMMENT 'Autonumerico',
  `ix` bigint(20) NOT NULL COMMENT 'Indice',
  `fx` datetime NOT NULL COMMENT 'Registro',
  `ux` bigint(20) NOT NULL COMMENT 'Usuario Ix',
  `rx` bigint(20) NOT NULL COMMENT 'Rol Ix',
  `sx` int(11) NOT NULL COMMENT 'Estatus',
  `cl` varchar(50) NOT NULL COMMENT 'Clave, codigo, sigla',
  `no` varchar(100) NOT NULL COMMENT 'Nombre',
  `ds` varchar(500) NOT NULL COMMENT 'Descripción ',
  `gcl` varchar(100) NOT NULL COMMENT 'Gerencias de las áreas de clientes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla de Siglas' ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `ad_sig`
--

INSERT INTO `ad_sig` (`id`, `ix`, `fx`, `ux`, `rx`, `sx`, `cl`, `no`, `ds`, `gcl`) VALUES
(1, 500006265838381, '0000-00-00 00:00:00', 0, 0, 0, 'Ll', 'Subdirecci&oacute;n de explotación planta interna', '', '819056262923854'),
(2, 500006265838382, '0000-00-00 00:00:00', 0, 0, 0, 'Lla', 'Gerencia de explotaci&oacute;n red de acceso', '', '819056262923854'),
(3, 500006265838383, '0000-00-00 00:00:00', 0, 0, 0, 'Llc', 'Gerencia de explotaci&oacute;n red de conectividad', '', '819056262923854'),
(4, 500006265838384, '0000-00-00 00:00:00', 0, 0, 0, 'Lld', 'Gerencia de explotaci&oacute;n red dorsal', '', '819056262923854'),
(5, 500006265838385, '0000-00-00 00:00:00', 0, 0, 0, 'Llf', 'Gerencia de explotaci&oacute;n red de fuerza y clima', '', '819056262923854'),
(6, 500006265838386, '0000-00-00 00:00:00', 0, 0, 0, 'Llp', 'Gerencia de Ingenier&iacute;a red de procesamiento, multimedia y gesti&oacute;n', '', '819056262923854'),
(7, 500006265838387, '0000-00-00 00:00:00', 0, 0, 0, 'Llt', ' Gerencia de Explotaci&oacute;n red de Transporte', '', '819056262923854'),
(8, 500006265838388, '0000-00-00 00:00:00', 0, 0, 0, 'Lp', 'Subdirecci&oacute;n de Ingeniería', '', '819056262923855'),
(9, 500006265838389, '0000-00-00 00:00:00', 0, 0, 0, 'Lpa', ' Gerencia de Ingenier&iacute;a red de acceso', '', '819056262923855'),
(10, 500006265838390, '0000-00-00 00:00:00', 0, 0, 0, 'Lpc', ' Gerencia de Ingenier&iacute;a red de conectividad', '', '819056262923855'),
(11, 500006265838391, '0000-00-00 00:00:00', 0, 0, 0, 'Lpe', 'Gerencia de ingenier&iacute;a planta externa', '', '819056262923855'),
(12, 500006265838392, '0000-00-00 00:00:00', 0, 0, 0, 'Lpp', ' Gerencia de Ingenier&iacute;a red de procesamiento, multimedia y gesti&oacute;n', '', '819056262923855'),
(13, 500006265838393, '0000-00-00 00:00:00', 0, 0, 0, 'Lpr', 'Gerencia de ingenier&iacute;a red uninet', '', '819056262923855'),
(14, 500006265838394, '0000-00-00 00:00:00', 0, 0, 0, 'Lpt', ' Gerencia de Ingenier&iacute;a red de Transporte', '', '819056262923855'),
(15, 500006265838395, '0000-00-00 00:00:00', 0, 0, 0, 'S', 'Direcci&oacute;n Desarrollo Tecnol&oacute;gico', '', '819056262923856'),
(16, 500006265838396, '0000-00-00 00:00:00', 0, 0, 0, 'Ta', ' Aprovisionamiento (compras)', '', '819056262923857'),
(17, 500006265838397, '0000-00-00 00:00:00', 0, 0, 0, 'To', 'Subdirección de operación planta', '', '819056262923857'),
(18, 500006265838398, '0000-00-00 00:00:00', 0, 0, 0, 'Toe', ' Gerencia de soporte planta externa', '', '819056262923857'),
(19, 500006265838399, '0000-00-00 00:00:00', 0, 0, 0, 'Lx', 'Subdirecci&oacute;n de Explotaci&oacute;n red de conectividad', '', '819056262923854'),
(20, 500006265838400, '2019-03-28 17:35:00', 0, 0, 0, 'Llg', 'Gerencia de explotaci&oacute;n red de gesti&oacute;n', '', '819056262923854'),
(21, 500006265838401, '2019-03-28 17:35:00', 0, 0, 0, 'Lll', 'Gerencia de explotaci&oacute;n red lada enlaces', '', '819056262923854'),
(22, 500006265838402, '2019-03-28 17:41:00', 0, 0, 0, 'Lper', 'Lper', '', '819056262923855'),
(23, 500006265838403, '2019-03-28 17:47:00', 0, 0, 0, 'St', 'Subdirección de operadores de telecomunicaciones', '', '819056262923856'),
(24, 500006265838404, '2019-03-29 16:39:00', 0, 1, 0, 'Lec', 'Gerencia de Evaluaci&oacute;n Y Soporte T&eacute;cnico Red Conectividad Banda Ancha', '', ''),
(25, 500006265838405, '2019-04-01 12:52:00', 0, 1, 0, 'Lee', 'Gerencia de Especificaciones T&eacute;cnicas', '', ''),
(26, 500006265838406, '2019-06-03 12:24:00', 0, 1, 0, 'Led', 'Gerencia de Auditoria T&eacute;cnica', '', ''),
(27, 500006265838407, '2019-06-03 12:36:00', 0, 1, 0, 'Let', 'Gerencia de Evaluaci&oacute;n Y Soporte T&eacute;cnico Red Transporte Banda Ancha', '', ''),
(28, 500006265838408, '2019-06-03 12:36:00', 0, 1, 0, 'Leg', 'Gerencia de Evaluaci&oacute;n Y Soporte T&eacute;cnico Y Gesti&oacute;n', '', ''),
(29, 500006265838409, '2019-06-03 12:36:00', 0, 1, 0, 'Lep', 'Gerencia de Evaluaci&oacute;n Y Soporte T&eacute;cnico Procesamiento Multimedia y Gesti&oacute;n', '', ''),
(30, 500006265838410, '2019-06-03 12:36:00', 0, 1, 0, 'Ler', 'Gerencia de Evaluaci&oacute;n Y Soporte T&eacute;cnico Red Acceso Banda Ancha', '', ''),
(32, 500006265838411, '2019-06-07 00:26:00', 0, 1, 0, 'Le', 'Subdirecci&oacute;n Evaluaci&oacute;n Y Soporte T&eacute;cnico', '', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ad_sig`
--
ALTER TABLE `ad_sig`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ix` (`ix`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ad_sig`
--
ALTER TABLE `ad_sig`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Autonumerico', AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
