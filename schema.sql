-

DROP TABLE IF EXISTS ligne_approvisionnement CASCADE;
DROP TABLE IF EXISTS approvisionnement       CASCADE;
DROP TABLE IF EXISTS paiement                CASCADE;
DROP TABLE IF EXISTS dette                   CASCADE;
DROP TABLE IF EXISTS ligne_commande          CASCADE;
DROP TABLE IF EXISTS commande                CASCADE;
DROP TABLE IF EXISTS client                  CASCADE;
DROP TABLE IF EXISTS fournisseur             CASCADE;
DROP TABLE IF EXISTS produit                 CASCADE;
DROP TABLE IF EXISTS utilisateur             CASCADE;

CREATE TABLE utilisateur (
    id            SERIAL PRIMARY KEY,
    nom           VARCHAR(100)      NOT NULL,
    email         VARCHAR(150)      NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255)      NOT NULL,
    role          VARCHAR(20)       NOT NULL
        CHECK (role IN ('ADMIN', 'VENTE', 'STOCK', 'INVENTAIRE'))
);

CREATE TABLE produit (
    id               SERIAL PRIMARY KEY,
    nom              VARCHAR(150)   NOT NULL,
    prix_vente       NUMERIC(12,2)  NOT NULL CHECK (prix_vente >= 0),
    quantite_stock   INTEGER        NOT NULL DEFAULT 0 CHECK (quantite_stock >= 0)
);

CREATE TABLE client (
    id             SERIAL PRIMARY KEY,
    prenom         VARCHAR(100)   NOT NULL,
    nom            VARCHAR(100)   NOT NULL,
    telephone      VARCHAR(20)    NOT NULL UNIQUE,
    email          VARCHAR(150),
    limite_credit  NUMERIC(12,2)  NOT NULL DEFAULT 0 CHECK (limite_credit >= 0)
);

CREATE TABLE fournisseur (
    id         SERIAL PRIMARY KEY,
    nom        VARCHAR(150)  NOT NULL,
    telephone  VARCHAR(20),
    adresse    VARCHAR(255),
    email      VARCHAR(150)
);


CREATE TABLE commande (
    id               SERIAL PRIMARY KEY,
    client_id        INTEGER        NOT NULL REFERENCES client(id),
    date_commande    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_total    NUMERIC(12,2)  NOT NULL CHECK (montant_total >= 0),
    montant_verse    NUMERIC(12,2)  NOT NULL DEFAULT 0 CHECK (montant_verse >= 0),
    mode_reglement   VARCHAR(20)    NOT NULL
        CHECK (mode_reglement IN ('Especes', 'Wave', 'Orange Money')),
    statut           VARCHAR(20)    NOT NULL
        CHECK (statut IN ('COMPTANT', 'CREDIT')),
    CONSTRAINT chk_commande_verse_le_total CHECK (montant_verse <= montant_total)
);

CREATE TABLE ligne_commande (
    id              SERIAL PRIMARY KEY,
    commande_id     INTEGER        NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    produit_id      INTEGER        NOT NULL REFERENCES produit(id),
    quantite        INTEGER        NOT NULL CHECK (quantite > 0),
    prix_unitaire   NUMERIC(12,2)  NOT NULL CHECK (prix_unitaire >= 0)
);

CREATE TABLE dette (
    id                SERIAL PRIMARY KEY,
    commande_id       INTEGER        NOT NULL UNIQUE REFERENCES commande(id),
    montant_initial   NUMERIC(12,2)  NOT NULL CHECK (montant_initial >= 0),
    montant_restant   NUMERIC(12,2)  NOT NULL CHECK (montant_restant >= 0),
    date_creation     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut            VARCHAR(20)    NOT NULL DEFAULT 'NON SOLDEE'
        CHECK (statut IN ('NON SOLDEE', 'SOLDEE')),
    CONSTRAINT chk_dette_restant_le_initial CHECK (montant_restant <= montant_initial)
);
CREATE TABLE paiement (
    id              SERIAL PRIMARY KEY,
    dette_id        INTEGER        NOT NULL REFERENCES dette(id),
    montant         NUMERIC(12,2)  NOT NULL CHECK (montant > 0),
    mode_paiement   VARCHAR(20)    NOT NULL
        CHECK (mode_paiement IN ('Especes', 'Wave', 'Orange Money')),
    date_paiement   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE approvisionnement (
    id               SERIAL PRIMARY KEY,
    fournisseur_id   INTEGER      NOT NULL REFERENCES fournisseur(id),
    ref_bl           VARCHAR(50)  NOT NULL UNIQUE,
    date_reception   TIMESTAMP,
    statut           VARCHAR(20)  NOT NULL DEFAULT 'EN COURS'
        CHECK (statut IN ('EN COURS', 'REÇU'))
);
CREATE TABLE ligne_approvisionnement (
    id                    SERIAL PRIMARY KEY,
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
