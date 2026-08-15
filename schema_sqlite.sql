PRAGMA foreign_keys = ON;


CREATE TABLE utilisateur (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    nom           VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          VARCHAR(20) NOT NULL
        CHECK (role IN ('ADMIN', 'VENTE', 'STOCK', 'INVENTAIRE'))
);

INSERT INTO utilisateur
(nom, email, mot_de_passe, role)
VALUES
('Admin Boutique',    'admin@storemanager.sn',      'root', 'ADMIN'),
('Chargé de Vente',   'vente@storemanager.sn',      'root', 'VENTE'),
('Chargé de Stock',   'stock@storemanager.sn',      'root', 'STOCK'),
('Inventaire',        'inventaire@storemanager.sn', 'root', 'INVENTAIRE');



CREATE TABLE produit (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nom             VARCHAR(150) NOT NULL,
    prix_vente      NUMERIC(12,2) NOT NULL
        CHECK (prix_vente >= 0),
    quantite_stock  INTEGER NOT NULL DEFAULT 0
        CHECK (quantite_stock >= 0)
);

INSERT INTO produit
(nom, prix_vente, quantite_stock)
VALUES
('Riz parfumé 25kg',       15000, 50),
('Huile 20L',               18000, 30),
('Sucre 25kg',              14000, 40),
('Lait en poudre 500g',      3500, 100),
('Thé 250g',                 2500, 80),
('Farine 25kg',             12000, 25);



CREATE TABLE client (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    prenom         VARCHAR(100) NOT NULL,
    nom            VARCHAR(100) NOT NULL,
    telephone      VARCHAR(20) NOT NULL UNIQUE,
    email          VARCHAR(150) NOT NULL UNIQUE,
    limite_credit  NUMERIC(12,2) NOT NULL DEFAULT 0
        CHECK (limite_credit >= 0)
);

INSERT INTO client
(prenom, nom, telephone, email, limite_credit)
VALUES
('Abdou',    'Ndiaye',  '776543210', 'abdou.ndiaye@gmail.com',    150000),
('Fama',     'Diouf',   '781234567', 'fama.diouf@gmail.com',      200000),
('Moussa',   'Sarr',    '769876543', 'sarr@gmail.com',            250000),
('Maimouna', 'Diallo',  '701122334', 'maimouna.diallo@gmail.com', 120000),
('Ousmane',  'Sow',     '775554433', 'sow@gmail.com',             180000),
('Awa',      'Cisse',   '783332211', 'awa.cisse@gmail.com',       300000),
('Babacar',  'Faye',    '762221100', 'faye@gmail.com',            150000),
('Khady',    'Mbacke',  '704443322', 'mbacke@gmail.com',           400000),
('Ibrahima', 'Gueye',   '778887766', 'ibrahima.gueye@gmail.com',  100000),
('Fatou',    'Fall',    '789998877', 'fall@gmail.com',             250000);



CREATE TABLE fournisseur (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    nom         VARCHAR(150) NOT NULL,
    telephone   VARCHAR(20),
    adresse     VARCHAR(255),
    email       VARCHAR(150) NOT NULL UNIQUE
);

INSERT INTO fournisseur
(nom, telephone, adresse, email)
VALUES
('Comptoir Céréalier Sénégalais',
 '338245678',
 'Ouakam',
 'comptoir@gmail.com'),

('Grossiste Diop & Frères',
 '773456789',
 'Sipres',
 'diopfreres@gmail.com'),

('Sénégal Import-Export',
 '338211010',
 'Keur Massar',
 'senegalexportimport@gmail.com');


CREATE TABLE commande (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id        INTEGER NOT NULL,
    date_commande    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_total    NUMERIC(12,2) NOT NULL
        CHECK (montant_total >= 0),
    montant_verse    NUMERIC(12,2) NOT NULL DEFAULT 0
        CHECK (montant_verse >= 0),
    mode_reglement   VARCHAR(20) NOT NULL
        CHECK (mode_reglement IN
            ('Especes', 'Wave', 'Orange Money')),
    statut           VARCHAR(20) NOT NULL
        CHECK (statut IN ('COMPTANT', 'CREDIT')),

    CHECK (montant_verse <= montant_total),

    FOREIGN KEY (client_id)
        REFERENCES client(id)
);



INSERT INTO commande
(client_id, date_commande, montant_total, montant_verse,
 mode_reglement, statut)
VALUES
(1, '2026-08-01 09:30:00', 30000, 30000,
 'Especes', 'COMPTANT'),

(2, '2026-08-02 10:15:00', 36000, 20000,
 'Wave', 'CREDIT'),

(3, '2026-08-03 14:20:00', 42000, 42000,
 'Orange Money', 'COMPTANT'),

(4, '2026-08-04 11:00:00', 28000, 10000,
 'Especes', 'CREDIT'),

(5, '2026-08-05 16:45:00', 35000, 35000,
 'Wave', 'COMPTANT'),

(6, '2026-08-06 09:10:00', 54000, 30000,
 'Orange Money', 'CREDIT');


CREATE TABLE ligne_commande (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id     INTEGER NOT NULL,
    produit_id      INTEGER NOT NULL,
    quantite        INTEGER NOT NULL
        CHECK (quantite > 0),
    prix_unitaire   NUMERIC(12,2) NOT NULL
        CHECK (prix_unitaire >= 0),

    FOREIGN KEY (commande_id)
        REFERENCES commande(id)
        ON DELETE CASCADE,

    FOREIGN KEY (produit_id)
        REFERENCES produit(id)
);

INSERT INTO ligne_commande
(commande_id, produit_id, quantite, prix_unitaire)
VALUES

(1, 1, 2, 15000),

(2, 2, 2, 18000),

(3, 3, 3, 14000),

(4, 3, 2, 14000),

(5, 4, 10, 3500),

(6, 2, 3, 18000);



CREATE TABLE dette (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id       INTEGER NOT NULL UNIQUE,
    montant_initial   NUMERIC(12,2) NOT NULL
        CHECK (montant_initial >= 0),
    montant_restant   NUMERIC(12,2) NOT NULL
        CHECK (montant_restant >= 0),
    date_creation     TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut            VARCHAR(20) NOT NULL DEFAULT 'NON SOLDEE'
        CHECK (statut IN ('NON SOLDEE', 'SOLDEE')),

    CHECK (montant_restant <= montant_initial),

    FOREIGN KEY (commande_id)
        REFERENCES commande(id)
);



INSERT INTO dette
(commande_id, montant_initial, montant_restant, statut)
VALUES
(2, 36000, 6000,  'NON SOLDEE'),
(4, 28000, 13000, 'NON SOLDEE'),
(6, 54000, 9000,  'NON SOLDEE');



CREATE TABLE paiement (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    dette_id        INTEGER NOT NULL,
    montant         NUMERIC(12,2) NOT NULL
        CHECK (montant > 0),
    mode_paiement   VARCHAR(20) NOT NULL
        CHECK (mode_paiement IN
            ('Especes', 'Wave', 'Orange Money')),
    date_paiement   TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (dette_id)
        REFERENCES dette(id)
);



INSERT INTO paiement
(dette_id, montant, mode_paiement, date_paiement)
VALUES
(1, 10000, 'Wave',        '2026-08-07 10:00:00'),
(2, 5000,  'Especes',     '2026-08-08 11:30:00'),
(3, 15000, 'Orange Money', '2026-08-09 15:00:00');



CREATE TABLE approvisionnement (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    fournisseur_id  INTEGER NOT NULL,
    ref_bl          VARCHAR(50) NOT NULL UNIQUE,
    date_reception  TEXT,
    statut          VARCHAR(20) NOT NULL DEFAULT 'EN COURS'
        CHECK (statut IN ('EN COURS', 'REÇU')),

    FOREIGN KEY (fournisseur_id)
        REFERENCES fournisseur(id)
);



INSERT INTO approvisionnement
(fournisseur_id, ref_bl, date_reception, statut)
VALUES
(1, 'BL-2026-001', '2026-08-01 08:30:00', 'REÇU'),

(2, 'BL-2026-002', '2026-08-03 09:00:00', 'REÇU'),

(3, 'BL-2026-003', NULL, 'EN COURS');


CREATE TABLE ligne_approvisionnement (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    approvisionnement_id  INTEGER NOT NULL,
    produit_id            INTEGER NOT NULL,
    quantite_commandee    INTEGER NOT NULL
        CHECK (quantite_commandee >= 0),
    quantite_livree       INTEGER NOT NULL DEFAULT 0
        CHECK (quantite_livree >= 0),
    cout_unitaire          NUMERIC(12,2) NOT NULL
        CHECK (cout_unitaire >= 0),

    FOREIGN KEY (approvisionnement_id)
        REFERENCES approvisionnement(id)
        ON DELETE CASCADE,

    FOREIGN KEY (produit_id)
        REFERENCES produit(id)
);



INSERT INTO ligne_approvisionnement
(approvisionnement_id, produit_id,
 quantite_commandee, quantite_livree, cout_unitaire)
VALUES

-- Approvisionnement 1
(1, 1, 100, 100, 12000),
(1, 3, 80, 80, 11000),

-- Approvisionnement 2
(2, 2, 60, 60, 15000),
(2, 4, 100, 100, 2800),

-- Approvisionnement 3 : EN COURS
(3, 5, 100, 0, 1800),
(3, 6, 50, 0, 9500);

CREATE INDEX idx_commande_client_id
    ON commande(client_id);

CREATE INDEX idx_ligne_commande_commande_id
    ON ligne_commande(commande_id);

CREATE INDEX idx_ligne_commande_produit_id
    ON ligne_commande(produit_id);

CREATE INDEX idx_dette_commande_id
    ON dette(commande_id);

CREATE INDEX idx_paiement_dette_id
    ON paiement(dette_id);

CREATE INDEX idx_approvisionnement_fournisseur
    ON approvisionnement(fournisseur_id);

CREATE INDEX idx_ligne_appro_approvisionnement
    ON ligne_approvisionnement(approvisionnement_id);

CREATE INDEX idx_ligne_appro_produit_id
    ON ligne_approvisionnement(produit_id);


SELECT * FROM utilisateur;

SELECT * FROM produit;

SELECT * FROM client;

SELECT * FROM fournisseur;

SELECT * FROM commande;

SELECT * FROM ligne_commande;

SELECT * FROM dette;

SELECT * FROM paiement;

SELECT * FROM approvisionnement;

SELECT * FROM ligne_approvisionnement;