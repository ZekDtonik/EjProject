-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.26-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para ejdb
CREATE DATABASE IF NOT EXISTS `ejdb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ejdb`;

-- Copiando estrutura para tabela ejdb.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) NOT NULL,
  `login` varchar(50) NOT NULL,
  `avatar` varchar(90) NOT NULL,
  `senha` varchar(60) NOT NULL,
  `status` int(1) NOT NULL,
  `tipo` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL,
  `matricula` int(11) NOT NULL,
  `banco` varchar(80) NOT NULL,
  `agencia` varchar(80) NOT NULL,
  `operacao` varchar(80) NOT NULL,
  `ctps` int(11) NOT NULL,
  `tel1` varchar(20) NOT NULL,
  `tel2` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela ejdb.users: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT IGNORE INTO `users` (`id`, `nome`, `login`, `avatar`, `senha`, `status`, `tipo`, `email`, `matricula`, `banco`, `agencia`, `operacao`, `ctps`, `tel1`, `tel2`) VALUES
	(1, 'Kevin', 'crafht', '', '303ecf7c0c8047b55dbf5113a768c186', 1, 1, 'crafht@gmail.com', 0, '', '', '', 0, '', NULL),
	(5, 'Kevin', 'crafhta', '', '303ecf7c0c8047b55dbf5113a768c186', 1, 2, 'crafht2@gmail.com', 1, '', '', '', 0, '', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
