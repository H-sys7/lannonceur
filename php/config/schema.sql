-- ============================================================
-- L'annonceur - Schéma SQL unifié
-- À exécuter dans phpMyAdmin (onglet SQL)
-- ============================================================

CREATE DATABASE IF NOT EXISTS lannonceur
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lannonceur;

-- ------------------------------------------------------------
-- Table utilisateurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
    id                INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    pseudonyme        VARCHAR(50)     NOT NULL UNIQUE,
    email             VARCHAR(191)    NOT NULL UNIQUE,
    mot_de_passe      VARCHAR(255)    NOT NULL,
    role              ENUM('acheteur','vendeur','admin') NOT NULL DEFAULT 'acheteur',
    avatar            VARCHAR(255)    DEFAULT NULL,
    est_actif         TINYINT(1)      NOT NULL DEFAULT 1,
    token_reset       VARCHAR(100)    DEFAULT NULL,
    token_expiry      DATETIME        DEFAULT NULL,
    date_inscription  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email      (email),
    INDEX idx_pseudonyme (pseudonyme)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table produits (annonces)
-- Noms de colonnes alignés avec TOUS les fichiers PHP :
--   utilisateur_id  (creer_annonce.php, mes_annonces.php, supprimer_annonce.php)
--   est_actif       (get_annonces.php)
--   date_creation   (get_annonces.php, mes_annonces.php)
--   image_url       (get_annonces.php, mes_annonces.php, dashboard.html)
--   contact         (creer_annonce.php, produits.html)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produits (
    id               INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id   INT UNSIGNED    NOT NULL,
    titre            VARCHAR(255)    NOT NULL,
    description      TEXT            NOT NULL,
    prix             DECIMAL(10,2)   NOT NULL,
    categorie        VARCHAR(80)     NOT NULL,
    image_url        VARCHAR(255)    DEFAULT NULL,
    contact          VARCHAR(20)     DEFAULT NULL,
    est_actif        TINYINT(1)      NOT NULL DEFAULT 1,
    localisation     VARCHAR(100)    DEFAULT NULL,
    vues             INT UNSIGNED    NOT NULL DEFAULT 0,
    date_creation    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,

    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_categorie   (categorie),
    INDEX idx_est_actif   (est_actif),
    INDEX idx_prix        (prix)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table conversations (messagerie)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS conversations (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produit_id   INT UNSIGNED NOT NULL,
    acheteur_id  INT UNSIGNED NOT NULL,
    vendeur_id   INT UNSIGNED NOT NULL,
    cree_le      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (produit_id)  REFERENCES produits(id)      ON DELETE CASCADE,
    FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id)  ON DELETE CASCADE,
    FOREIGN KEY (vendeur_id)  REFERENCES utilisateurs(id)  ON DELETE CASCADE,

    UNIQUE KEY unique_conversation (produit_id, acheteur_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table messages
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id  INT UNSIGNED NOT NULL,
    expediteur_id    INT UNSIGNED NOT NULL,
    contenu          TEXT         NOT NULL,
    lu               TINYINT(1)   NOT NULL DEFAULT 0,
    envoye_le        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (expediteur_id)   REFERENCES utilisateurs(id)  ON DELETE CASCADE,

    INDEX idx_conversation (conversation_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table evaluations (optionnel)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS evaluations (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vendeur_id   INT UNSIGNED NOT NULL,
    acheteur_id  INT UNSIGNED NOT NULL,
    produit_id   INT UNSIGNED NOT NULL,
    note         TINYINT UNSIGNED NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire  TEXT         DEFAULT NULL,
    cree_le      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (vendeur_id)  REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id)  REFERENCES produits(id)     ON DELETE CASCADE,

    UNIQUE KEY unique_eval (acheteur_id, produit_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table panier (favoris/wishlist)
-- Permet aux utilisateurs de sauvegarder des annonces
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS panier (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id   INT UNSIGNED NOT NULL,
    produit_id       INT UNSIGNED NOT NULL,
    ajoute_le        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id)     REFERENCES produits(id)     ON DELETE CASCADE,

    UNIQUE KEY unique_panier (utilisateur_id, produit_id),
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB;
