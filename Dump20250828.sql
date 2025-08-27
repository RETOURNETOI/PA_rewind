CREATE DATABASE  IF NOT EXISTS `kayak_trip` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kayak_trip`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: kayak_trip
-- ------------------------------------------------------
-- Server version	9.1.0

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
-- Table structure for table `codes_promo`
--

DROP TABLE IF EXISTS `codes_promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codes_promo` (
  `id_code` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type_reduction` enum('pourcentage','montant') DEFAULT 'pourcentage',
  `valeur_reduction` decimal(8,2) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `usage_max` int DEFAULT NULL,
  `usage_actuel` int DEFAULT '0',
  `premiere_reservation_uniquement` tinyint(1) DEFAULT '0',
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_code`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_codes_promo_actif` (`actif`),
  KEY `idx_codes_promo_dates` (`date_debut`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `codes_promo`
--

LOCK TABLES `codes_promo` WRITE;
/*!40000 ALTER TABLE `codes_promo` DISABLE KEYS */;
/*!40000 ALTER TABLE `codes_promo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','payée','confirmée','annulée') DEFAULT 'en_attente',
  PRIMARY KEY (`id_commande`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande_hebergement`
--

DROP TABLE IF EXISTS `commande_hebergement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande_hebergement` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_hebergement` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `nb_personnes` int NOT NULL,
  PRIMARY KEY (`id_commande`,`id_hebergement`,`id_utilisateur`,`date_debut`),
  KEY `id_hebergement` (`id_hebergement`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande_hebergement`
--

LOCK TABLES `commande_hebergement` WRITE;
/*!40000 ALTER TABLE `commande_hebergement` DISABLE KEYS */;
/*!40000 ALTER TABLE `commande_hebergement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande_service`
--

DROP TABLE IF EXISTS `commande_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande_service` (
  `id_commande` int NOT NULL,
  `id_service` int NOT NULL,
  `quantite` int DEFAULT '1',
  PRIMARY KEY (`id_commande`,`id_service`),
  KEY `id_service` (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande_service`
--

LOCK TABLES `commande_service` WRITE;
/*!40000 ALTER TABLE `commande_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `commande_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id_conversation` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `id_commercial` int DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `statut` enum('ouvert','en_cours','ferme') DEFAULT 'ouvert',
  `priorite` enum('basse','normale','haute') DEFAULT 'normale',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_derniere_activite` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_conversation`),
  KEY `id_client` (`id_client`),
  KEY `id_commercial` (`id_commercial`),
  KEY `idx_conversations_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fermetures_hebergement`
--

DROP TABLE IF EXISTS `fermetures_hebergement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fermetures_hebergement` (
  `id_fermeture` int NOT NULL AUTO_INCREMENT,
  `id_hebergement` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `raison` enum('travaux','maintenance','saisonnier','autre') DEFAULT 'travaux',
  `notes` text,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_fermeture`),
  KEY `id_hebergement` (`id_hebergement`),
  KEY `idx_fermetures_dates` (`date_debut`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fermetures_hebergement`
--

LOCK TABLES `fermetures_hebergement` WRITE;
/*!40000 ALTER TABLE `fermetures_hebergement` DISABLE KEYS */;
/*!40000 ALTER TABLE `fermetures_hebergement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hebergement`
--

DROP TABLE IF EXISTS `hebergement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hebergement` (
  `id_hebergement` int NOT NULL AUTO_INCREMENT,
  `id_point` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('hotel','gite','camping','auberge') NOT NULL,
  `capacite` int NOT NULL,
  `prix_nuit` decimal(8,2) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_hebergement`),
  KEY `id_point` (`id_point`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hebergement`
--

LOCK TABLES `hebergement` WRITE;
/*!40000 ALTER TABLE `hebergement` DISABLE KEYS */;
INSERT INTO `hebergement` VALUES (1,1,'paradis','',6,250.00,'une villa pour 6'),(2,2,'tt','',8,895.00,'azerrty');
/*!40000 ALTER TABLE `hebergement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itineraire`
--

DROP TABLE IF EXISTS `itineraire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itineraire` (
  `id_itineraire` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_itineraire`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itineraire`
--

LOCK TABLES `itineraire` WRITE;
/*!40000 ALTER TABLE `itineraire` DISABLE KEYS */;
/*!40000 ALTER TABLE `itineraire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itineraire_etape`
--

DROP TABLE IF EXISTS `itineraire_etape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itineraire_etape` (
  `id_itineraire` int NOT NULL,
  `id_point` int NOT NULL,
  `id_hebergement` int DEFAULT NULL,
  `ordre` int NOT NULL,
  PRIMARY KEY (`id_itineraire`,`id_point`,`ordre`),
  KEY `id_point` (`id_point`),
  KEY `id_hebergement` (`id_hebergement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itineraire_etape`
--

LOCK TABLES `itineraire_etape` WRITE;
/*!40000 ALTER TABLE `itineraire_etape` DISABLE KEYS */;
/*!40000 ALTER TABLE `itineraire_etape` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `id_commercial` int DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_commercial` (`id_commercial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages_conversation`
--

DROP TABLE IF EXISTS `messages_conversation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages_conversation` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `id_conversation` int NOT NULL,
  `id_expediteur` int NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_message`),
  KEY `id_conversation` (`id_conversation`),
  KEY `id_expediteur` (`id_expediteur`),
  KEY `idx_messages_lu` (`lu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages_conversation`
--

LOCK TABLES `messages_conversation` WRITE;
/*!40000 ALTER TABLE `messages_conversation` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_conversation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_abonnes`
--

DROP TABLE IF EXISTS `newsletter_abonnes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_abonnes` (
  `id_abonne` int NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) DEFAULT '1',
  `token_desabonnement` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_abonne`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token_desabonnement` (`token_desabonnement`),
  KEY `idx_newsletter_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_abonnes`
--

LOCK TABLES `newsletter_abonnes` WRITE;
/*!40000 ALTER TABLE `newsletter_abonnes` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_abonnes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_campagnes`
--

DROP TABLE IF EXISTS `newsletter_campagnes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_campagnes` (
  `id_campagne` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_envoi` datetime DEFAULT NULL,
  `statut` enum('brouillon','programme','envoye') DEFAULT 'brouillon',
  `nb_destinataires` int DEFAULT '0',
  `nb_ouverts` int DEFAULT '0',
  `nb_clics` int DEFAULT '0',
  PRIMARY KEY (`id_campagne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_campagnes`
--

LOCK TABLES `newsletter_campagnes` WRITE;
/*!40000 ALTER TABLE `newsletter_campagnes` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_campagnes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pack`
--

DROP TABLE IF EXISTS `pack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pack` (
  `id_pack` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `prix` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id_pack`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pack`
--

LOCK TABLES `pack` WRITE;
/*!40000 ALTER TABLE `pack` DISABLE KEYS */;
/*!40000 ALTER TABLE `pack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pack_etape`
--

DROP TABLE IF EXISTS `pack_etape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pack_etape` (
  `id_pack` int NOT NULL,
  `id_point` int NOT NULL,
  `id_hebergement` int NOT NULL,
  `ordre` int DEFAULT NULL,
  PRIMARY KEY (`id_pack`,`id_point`,`id_hebergement`),
  KEY `id_point` (`id_point`),
  KEY `id_hebergement` (`id_hebergement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pack_etape`
--

LOCK TABLES `pack_etape` WRITE;
/*!40000 ALTER TABLE `pack_etape` DISABLE KEYS */;
/*!40000 ALTER TABLE `pack_etape` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plages_tarifaires`
--

DROP TABLE IF EXISTS `plages_tarifaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plages_tarifaires` (
  `id_plage` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `multiplicateur` decimal(4,2) DEFAULT '1.00',
  `description` text,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plage`),
  KEY `idx_plages_tarifaires_dates` (`date_debut`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plages_tarifaires`
--

LOCK TABLES `plages_tarifaires` WRITE;
/*!40000 ALTER TABLE `plages_tarifaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `plages_tarifaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_arret`
--

DROP TABLE IF EXISTS `point_arret`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_arret` (
  `id_point` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  PRIMARY KEY (`id_point`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_arret`
--

LOCK TABLES `point_arret` WRITE;
/*!40000 ALTER TABLE `point_arret` DISABLE KEYS */;
/*!40000 ALTER TABLE `point_arret` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `id_service` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `prix` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usage_codes_promo`
--

DROP TABLE IF EXISTS `usage_codes_promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usage_codes_promo` (
  `id_usage` int NOT NULL AUTO_INCREMENT,
  `id_code` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `id_commande` int DEFAULT NULL,
  `date_utilisation` datetime DEFAULT CURRENT_TIMESTAMP,
  `montant_reduit` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id_usage`),
  KEY `id_code` (`id_code`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_commande` (`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usage_codes_promo`
--

LOCK TABLES `usage_codes_promo` WRITE;
/*!40000 ALTER TABLE `usage_codes_promo` DISABLE KEYS */;
/*!40000 ALTER TABLE `usage_codes_promo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` enum('client','admin','commercial') DEFAULT 'client',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (4,'test','test','test@test.fr','$2y$10$m3aQ69IpqKmU71HYsSPUlODq8JUDFavfwGUB42ahX6.MCcxL2gada','','2025-08-25 15:55:47','admin'),(5,'Cataluna','Nicolas','aa@aa.fr','$2y$10$5QsnfwJLX63MS03sOnsoDu4iQpesndKphvA2zpLj5AOq1YvDSDWjO',NULL,'2025-08-26 12:55:17','client');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-28  0:31:53
