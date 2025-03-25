-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-03-2025 a las 20:20:06
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
-- Base de datos: `ibisolutions`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_orders`
--

DROP TABLE IF EXISTS `job_orders`;
CREATE TABLE IF NOT EXISTS `job_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `idemployer` int DEFAULT NULL,
  `idemployee` int DEFAULT NULL COMMENT 'FK(users.id)',
  `idvilla` int DEFAULT NULL,
  `idservice` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'comentarios sobre el servicio',
  `check_in_employee` datetime DEFAULT NULL,
  `check_out_employee` datetime DEFAULT NULL,
  `comment_time` text NOT NULL COMMENT 'solo lo edita empleado, errores de fichaje, incidencias sobre la hora',
  PRIMARY KEY (`id`),
  KEY `job_orders_iduser_IDX` (`idemployer`) USING BTREE,
  KEY `job_orders_idvilla_IDX` (`idvilla`) USING BTREE,
  KEY `job_orders_idservice_IDX` (`idservice`) USING BTREE,
  KEY `idemployee` (`idemployee`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `job_orders`
--

INSERT INTO `job_orders` (`id`, `check_in`, `check_out`, `idemployer`, `idemployee`, `idvilla`, `idservice`, `comment`, `check_in_employee`, `check_out_employee`, `comment_time`) VALUES
(1, '2025-03-01 09:00:00', '2025-03-01 14:30:00', 3, 4, 1, 1, NULL, NULL, NULL, ''),
(2, '2025-03-02 09:00:00', '2025-03-02 14:30:00', 3, 4, 3, 1, NULL, '2025-03-02 10:00:00', '2025-03-02 15:00:00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'employee'),
(3, 'employer'),
(4, 'client'),
(5, 'owner');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_users`
--

DROP TABLE IF EXISTS `roles_users`;
CREATE TABLE IF NOT EXISTS `roles_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `iduser` int DEFAULT NULL,
  `idrole` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_users_iduser_IDX` (`iduser`,`idrole`) USING BTREE,
  KEY `idx_user` (`iduser`) INVISIBLE,
  KEY `idx_role` (`idrole`) INVISIBLE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles_users`
--

INSERT INTO `roles_users` (`id`, `iduser`, `idrole`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-02-03 19:08:47', '2025-02-03 19:08:47'),
(2, 1, 3, '2025-02-12 20:04:37', '2025-02-12 20:04:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'limpieza', '2025-03-06 18:43:19', '2025-03-06 18:43:19'),
(2, 'catering', '2025-03-06 18:43:19', '2025-03-06 18:43:19'),
(3, 'babysitter', '2025-03-06 18:43:19', '2025-03-06 18:43:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `dni` varchar(10) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `passwd` varchar(255) NOT NULL COMMENT 'encrypted password using php native function',
  `role` enum('admin','employer','employee') DEFAULT 'employee',
  `address` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `dni`, `phone`, `email`, `passwd`, `role`, `address`) VALUES
(1, 'admin', 'admin', '22222222Q', '6621341144', 'a@a.aa', '$2y$10$zbesai5OzNVLdK5mDiGqwuGeyFbTuHyb2gk0HXGdQm349xUbAG/9K', 'admin', 'calle de la alegria 7'),
(3, 'Mariela', 'Bevans', '55555555q', '662134123', 'mariela@mariela.com', '$2y$10$JLfM8KHp1pw/3wBqV0iCnO4XXj1q7cOglgzjtjjn6pmAlO8ee2nT.', 'employer', 'calle de ningun sitio 2'),
(4, 'Yanina', 'Echenique', '55465965Q', '662134123', 'yanina@yanina.com', '$2y$10$JLfM8KHp1pw/3wBqV0iCnO4XXj1q7cOglgzjtjjn6pmAlO8ee2nT.', 'employee', 'bunyola 13'),
(5, 'Asdgag', 'Gagrgol', '34523525a', '254245245', 'aa@a.aa', '$2y$10$aEAZTFaheNNS8Rn897ovJOfDS8L521kMrl3wa7wEzeswTBExrHbcm', 'admin', 'C/asoghasgoi'),
(6, 'asdgag', 'agagag', '34523525a', '254245245', 'ab@a.aa', '$2y$10$iXk3UYmt1k5FyKDq9cPWuufHbdphfUhjIv/p4MFYy6RgxGC9DITSa', 'admin', 'gaasgasfgasfg'),
(7, 'asdgag', 'agagag', '34523525a', '254245245', 'ac@a.aa', '$2y$10$lxBAqI2CpTgBYYM5WiRYa.Dg0tNIvD50YbGCC7chKIO2OUa2F9sKS', 'admin', 'gaasgasfgasfg'),
(8, 'aaa', 'bbb', '05683406d', '20495845', 'a1@a.aa', '$2y$10$NjCIoNZ.9VmZUCYu1rkJ1u4btv6E3JbUPvfONLYBGs5WVH3Qw0sVe', 'employer', 'C/soto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `villas`
--

DROP TABLE IF EXISTS `villas`;
CREATE TABLE IF NOT EXISTS `villas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `iduser` int NOT NULL COMMENT 'role owner',
  `num_rooms` int DEFAULT NULL,
  `num_baths` int DEFAULT NULL,
  `pool` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `villas_iduser_IDX` (`iduser`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `villas`
--

INSERT INTO `villas` (`id`, `name`, `address`, `iduser`, `num_rooms`, `num_baths`, `pool`) VALUES
(1, 'del Mar', 'agsfaqghafg', 4, 5, 1, 1),
(3, 'ca la cala', 'agsfaqghafg', 4, 4, 1, 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `job_orders`
--
ALTER TABLE `job_orders`
  ADD CONSTRAINT `job_orders_idemployee_FK` FOREIGN KEY (`idemployee`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `job_orders_idemployer_FK` FOREIGN KEY (`idemployer`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `job_orders_services_FK` FOREIGN KEY (`idservice`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `job_orders_villas_FK` FOREIGN KEY (`idvilla`) REFERENCES `villas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `fk_roles_users_idrole` FOREIGN KEY (`idrole`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_roles_users_iduser` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `villas`
--
ALTER TABLE `villas`
  ADD CONSTRAINT `villas_users_FK` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
