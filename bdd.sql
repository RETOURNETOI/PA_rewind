-- =====================================
-- Base de données : kayak_Trip
-- =====================================

CREATE DATABASE IF NOT EXISTS Kayak_Trip
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE Kayak_Trip;

-- ==============================
-- Table Utilisateurs
-- ==============================
CREATE TABLE UTILISATEUR (
  id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  telephone VARCHAR(20),
  date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
  role ENUM('client','admin','commercial') DEFAULT 'client'
);

-- ==============================
-- Table Points d’arrêt
-- ==============================
CREATE TABLE POINT_ARRET (
  id_point INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  description TEXT,
  latitude DECIMAL(10,6),
  longitude DECIMAL(10,6)
);

-- ==============================
-- Table Hébergements
-- ==============================
CREATE TABLE HEBERGEMENT (
  id_hebergement INT AUTO_INCREMENT PRIMARY KEY,
  id_point INT NOT NULL,
  nom VARCHAR(100) NOT NULL,
  type ENUM('hotel','gite','camping','auberge') NOT NULL,
  capacite INT NOT NULL,
  prix_nuit DECIMAL(8,2) NOT NULL,
  description TEXT,
  FOREIGN KEY (id_point) REFERENCES POINT_ARRET(id_point)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- ==============================
-- Table Services
-- ==============================
CREATE TABLE SERVICE (
  id_service INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  description TEXT,
  prix DECIMAL(8,2) NOT NULL
);

-- ==============================
-- Table Packs
-- ==============================
CREATE TABLE PACK (
  id_pack INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  description TEXT,
  prix DECIMAL(8,2) NOT NULL
);

-- Table Packs - Étapes
CREATE TABLE PACK_ETAPE (
  id_pack INT,
  id_point INT,
  id_hebergement INT,
  ordre INT,
  PRIMARY KEY (id_pack, id_point, id_hebergement),
  FOREIGN KEY (id_pack) REFERENCES PACK(id_pack)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_point) REFERENCES POINT_ARRET(id_point)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_hebergement) REFERENCES HEBERGEMENT(id_hebergement)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- ==============================
-- Table Commandes
-- ==============================
CREATE TABLE COMMANDE (
  id_commande INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
  statut ENUM('en_attente','payée','confirmée','annulée') DEFAULT 'en_attente',
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Table Commande - Hébergements
CREATE TABLE COMMANDE_HEBERGEMENT (
  id_commande INT,
  id_hebergement INT,
  date_debut DATE,
  date_fin DATE,
  nb_personnes INT NOT NULL,
  PRIMARY KEY (id_commande, id_hebergement, date_debut),
  FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_hebergement) REFERENCES HEBERGEMENT(id_hebergement)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Table Commande - Services
CREATE TABLE COMMANDE_SERVICE (
  id_commande INT,
  id_service INT,
  quantite INT DEFAULT 1,
  PRIMARY KEY (id_commande, id_service),
  FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_service) REFERENCES SERVICE(id_service)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- ==============================
-- Table Itinéraires personnalisés
-- ==============================
CREATE TABLE ITINERAIRE (
  id_itineraire INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  nom VARCHAR(100),
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE ITINERAIRE_ETAPE (
  id_itineraire INT,
  id_point INT,
  id_hebergement INT,
  ordre INT,
  PRIMARY KEY (id_itineraire, id_point, ordre),
  FOREIGN KEY (id_itineraire) REFERENCES ITINERAIRE(id_itineraire)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_point) REFERENCES POINT_ARRET(id_point)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_hebergement) REFERENCES HEBERGEMENT(id_hebergement)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- ==============================
-- Table Chat (Messages)
-- ==============================
CREATE TABLE MESSAGE (
  id_message INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT,
  id_commercial INT,
  contenu TEXT NOT NULL,
  date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (id_commercial) REFERENCES UTILISATEUR(id_utilisateur)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
