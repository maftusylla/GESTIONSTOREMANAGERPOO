
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS ligne_approvisionnement;
DROP TABLE IF EXISTS approvisionnement;
DROP TABLE IF EXISTS paiement;
DROP TABLE IF EXISTS dette;
DROP TABLE IF EXISTS ligne_commande;
DROP TABLE IF EXISTS commande;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS fournisseur;
DROP TABLE IF EXISTS produit;
DROP TABLE IF EXISTS utilisateur;

CREATE TABLE utilisateur (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    nom           VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255)  NOT NULL,
    role          VARCHAR(20)   NOT NULL
        CHECK (role IN ('ADMIN', 'VENTE', 'STOCK', 'INVENTAIRE'))
);

CREATE TABLE produit (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    nom              VARCHAR(150)   NOT NULL,
    prix_vente       NUMERIC(12,2)  NOT NULL CHECK (prix_vente >= 0),
    quantite_stock   INTEGER        NOT NULL DEFAULT 0 CHECK (quantite_stock >= 0)
);

CREATE TABLE client (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    prenom         VARCHAR(100)   NOT NULL,
    nom            VARCHAR(100)   NOT NULL,
    telephone      VARCHAR(20)    NOT NULL UNIQUE,
    email          VARCHAR(150),
    limite_credit  NUMERIC(12,2)  NOT NULL DEFAULT 0 CHECK (limite_credit >= 0)
);


    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        VARCHAR(150)  NOT NULL,
    telephone  VARCHAR(20),
    adresse    VARCHAR(255),
    email      VARCHAR(150)
);

CREATE TABLE commande (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id        INTEGER        NOT NULL REFERENCES client(id),
    date_commande    TEXT           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_total    NUMERIC(12,2)  NOT NULL CHECK (montant_total >= 0),
    montant_verse    NUMERIC(12,2)  NOT NULL DEFAULT 0 CHECK (montant_verse >= 0),
    mode_reglement   VARCHAR(20)    NOT NULL
        CHECK (mode_reglement IN ('Especes', 'Wave', 'Orange Money')),
    statut           VARCHAR(20)    NOT NULL
        CHECK (statut IN ('COMPTANT', 'CREDIT')),
    CHECK (montant_verse <= montant_total)
);

-- 
CREATE TABLE ligne_commande (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id     INTEGER        NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    produit_id      INTEGER        NOT NULL REFERENCES produit(id),
    quantite        INTEGER        NOT NULL CHECK (quantite > 0),
    prix_unitaire   NUMERIC(12,2)  NOT NULL CHECK (prix_unitaire >= 0)
);

CREATE TABLE dette (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id       INTEGER        NOT NULL UNIQUE REFERENCES commande(id),
    montant_initial   NUMERIC(12,2)  NOT NULL CHECK (montant_initial >= 0),
    montant_restant   NUMERIC(12,2)  NOT NULL CHECK (montant_restant >= 0),
    date_creation     TEXT           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut            VARCHAR(20)    NOT NULL DEFAULT 'NON SOLDEE'
        CHECK (statut IN ('NON SOLDEE', 'SOLDEE')),
    CHECK (montant_restant <= montant_initial)
);

CREATE TABLE paiement (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    dette_id        INTEGER        NOT NULL REFERENCES dette(id),
    montant         NUMERIC(12,2)  NOT NULL CHECK (montant > 0),
    mode_paiement   VARCHAR(20)    NOT NULL
        CHECK (mode_paiement IN ('Especes', 'Wave', 'Orange Money')),
    date_paiement   TEXT           NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE approvisionnement (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    fournisseur_id   INTEGER      NOT NULL REFERENCES fournisseur(id),
    ref_bl           VARCHAR(50)  NOT NULL UNIQUE,
    date_reception   TEXT,
    statut           VARCHAR(20)  NOT NULL DEFAULT 'EN COURS'
        CHECK (statut IN ('EN COURS', 'REÇU'))
);

CREATE TABLE ligne_approvisionnement (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    approvisionnement_id  INTEGER        NOT NULL REFERENCES approvisionnement(id) ON DELETE CASCADE,
    produit_id            INTEGER        NOT NULL REFERENCES produit(id),
    quantite_commandee    INTEGER        NOT NULL CHECK (quantite_commandee >= 0),
    quantite_livree       INTEGER        NOT NULL DEFAULT 0 CHECK (quantite_livree >= 0),
    cout_unitaire         NUMERIC(12,2)  NOT NULL CHECK (cout_unitaire >= 0)
);

CREATE INDEX idx_commande_client_id            ON commande(client_id);
CREATE INDEX idx_ligne_commande_commande_id     ON ligne_commande(commande_id);
CREATE INDEX idx_ligne_commande_produit_id      ON ligne_commande(produit_id);
CREATE INDEX idx_dette_commande_id              ON dette(commande_id);
CREATE INDEX idx_paiement_dette_id              ON paiement(dette_id);
CREATE INDEX idx_approvisionnement_fournisseur  ON approvisionnement(fournisseur_id);
CREATE INDEX idx_ligne_appro_approvisionnement  ON ligne_approvisionnement(approvisionnement_id);
CREATE INDEX idx_ligne_appro_produit_id         ON ligne_approvisionnement(produit_id);
