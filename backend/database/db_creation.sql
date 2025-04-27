-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sitio_licencias_lis
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `licencias`
--

DROP TABLE IF EXISTS `licencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `licencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plataforma` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `contrasena` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_de_compra` date NOT NULL,
  `fecha_de_suspension` date DEFAULT NULL,
  `fecha_de_renovacion` date DEFAULT NULL,
  `fecha_de_vencimiento` date NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `suspended` tinyint(1) DEFAULT NULL COMMENT 'forma en la que el usuario puede saber si su cuenta esta suspendida',
  PRIMARY KEY (`id`),
  KEY `fk_usuario_compras` (`id_usuario`),
  CONSTRAINT `fk_usuario_compras` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licencias`
--

LOCK TABLES `licencias` WRITE;
/*!40000 ALTER TABLE `licencias` DISABLE KEYS */;
INSERT INTO `licencias` VALUES (44,'Lista','se220355@alumno.udb.edu.sv','ert','2023-12-25','2025-04-16','2025-04-16','2024-12-24',14,1),(68,'FULSAMO','mr211604@alumno.udb.edu.sv','password','2024-04-16','2026-04-16',NULL,'2026-04-16',23,1),(69,'FUSALMO','mirandarodriguezleofernando@gmail.com','123456','2025-04-17',NULL,'2025-04-22','2026-04-17',23,NULL);
/*!40000 ALTER TABLE `licencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(20) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin'),(2,'user');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `id_rol` int NOT NULL COMMENT 'id para el rol del usuario',
  `active` tinyint(1) NOT NULL COMMENT 'Permite saber si el usuario puede iniciar sesion',
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuario_roles_id_rol` (`id_rol`),
  CONSTRAINT `fk_usuario_roles_id_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (14,'Hola','$2y$10$RR87CbKQDVDDSyFE8kfh1.jenJ9DYpYqRGVHo.PhdeelZcq8ATILG','',2,0),(15,'Ingrid','250cf8b51c773f3f8dc8b4be867a9a02','',2,0),(16,'Jenn','$argon2i$v=19$m=65536,t=4,p=1$aS9xY1FDYVJLNEk4MXZLUA$4Fkj1ChEWqb99f3gyEitRsOmFf9H5z2h6D1miqbjUcE','',2,0),(17,'Jeniffer','$2y$10$TL8O5TJQEvtzff04ypwcM.pY0t/bsmh/QMtOQxiFlwyU6joutUazW','',2,0),(18,'Fatima','YXio','',2,0),(19,'Milena','$2y$10$F/OGP05HyrAEBl5EeFKtveez6/3fFDhrCc6n5uDT3r2Ni11kJExoa','prueba@outlook.com',2,0),(21,'Lidia','YXio','',2,0),(22,'Ingrid','123','',2,0),(23,'admin','$2y$10$ddhJL5lsS2msLIFKsTPJTOLR/k98TysizToVrpcymb7RDTkBuCaya','correo@test.com',1,1),(24,'usertest','$2y$10$W7rxF/beIDFdVZjr8Q4QC.JgmNKxjkiyRR2gZFsIgjfqOrqjAhyFG','usertest@gmail.com',2,1),(26,'fernando','$2y$10$TF3E6EN6vV6fV0i3BbdxyeoxVUcF3cQnxVvZHhlfZXnRTTZ.BCsuW','example@email.com',2,1),(28,'tercerTest','$2y$10$J5bhppO7EcPrbzYbw7CzFuf3zzFk1/wAedGZfCWJbvAa7Aq3OvSB2','exampletest@tercero.com',2,0),(29,'PruebaFinal','$2y$10$XP/rCR2xmNN1.zB7U4ARCuwMNEONPOSLIHgDyG9IVWrmM90yDUEpq','ejemplo@gmail.com',1,1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-26 22:19:58
