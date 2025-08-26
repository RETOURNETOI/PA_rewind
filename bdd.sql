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



-- ==============================
-- A ajouter a la bdd
-- ==============================

-- Tables pour la gestion des codes promotionnels
CREATE TABLE IF NOT EXISTS codes_promo (
    id_code INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    type_reduction ENUM('pourcentage', 'montant') DEFAULT 'pourcentage',
    valeur_reduction DECIMAL(8,2) NOT NULL,
    date_debut DATE,
    date_fin DATE,
    usage_max INT DEFAULT NULL,
    usage_actuel INT DEFAULT 0,
    premiere_reservation_uniquement BOOLEAN DEFAULT FALSE,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tables pour les plages tarifaires saisonnières
CREATE TABLE IF NOT EXISTS plages_tarifaires (
    id_plage INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    multiplicateur DECIMAL(4,2) DEFAULT 1.0,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tables pour la gestion des fermetures d'hébergements
CREATE TABLE IF NOT EXISTS fermetures_hebergement (
    id_fermeture INT AUTO_INCREMENT PRIMARY KEY,
    id_hebergement INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    raison ENUM('travaux', 'maintenance', 'saisonnier', 'autre') DEFAULT 'travaux',
    notes TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_hebergement) REFERENCES hebergement(id_hebergement) ON DELETE CASCADE
);

-- Tables pour le système de messagerie commerciale
CREATE TABLE IF NOT EXISTS conversations (
    id_conversation INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_commercial INT NULL,
    sujet VARCHAR(255),
    statut ENUM('ouvert', 'en_cours', 'ferme') DEFAULT 'ouvert',
    priorite ENUM('basse', 'normale', 'haute') DEFAULT 'normale',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_derniere_activite DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_client) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_commercial) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS messages_conversation (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_conversation INT NOT NULL,
    id_expediteur INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_conversation) REFERENCES conversations(id_conversation) ON DELETE CASCADE,
    FOREIGN KEY (id_expediteur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

-- Tables pour la newsletter (VERSION CORRIGÉE)
CREATE TABLE IF NOT EXISTS newsletter_abonnes (
    id_abonne INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) UNIQUE NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE,
    token_desabonnement VARCHAR(100) UNIQUE
);

CREATE TABLE IF NOT EXISTS newsletter_campagnes (
    id_campagne INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_envoi DATETIME NULL,
    statut ENUM('brouillon', 'programme', 'envoye') DEFAULT 'brouillon',
    nb_destinataires INT DEFAULT 0,
    nb_ouverts INT DEFAULT 0,
    nb_clics INT DEFAULT 0
);

-- Table pour tracker l'usage des codes promo (optionnel)
CREATE TABLE IF NOT EXISTS usage_codes_promo (
    id_usage INT AUTO_INCREMENT PRIMARY KEY,
    id_code INT NOT NULL,
    id_utilisateur INT NOT NULL,
    id_commande INT,
    date_utilisation DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant_reduit DECIMAL(8,2),
    FOREIGN KEY (id_code) REFERENCES codes_promo(id_code) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_commande) REFERENCES commande(id_commande) ON DELETE CASCADE
);

-- Index pour optimiser les performances
CREATE INDEX idx_codes_promo_actif ON codes_promo(actif);
CREATE INDEX idx_codes_promo_dates ON codes_promo(date_debut, date_fin);
CREATE INDEX idx_plages_tarifaires_dates ON plages_tarifaires(date_debut, date_fin);
CREATE INDEX idx_fermetures_dates ON fermetures_hebergement(date_debut, date_fin);
CREATE INDEX idx_conversations_statut ON conversations(statut);
CREATE INDEX idx_messages_lu ON messages_conversation(lu);
CREATE INDEX idx_newsletter_actif ON newsletter_abonnes(actif);