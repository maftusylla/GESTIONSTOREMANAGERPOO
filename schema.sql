-- Active: 1785147799442@@127.0.0.1@5432@gestionstoremanagerpro
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


INSERT INTO utilisateur ( nom, email, mot_de_passe, role) VALUES
( 'Admin Boutique',    'admin@storemanager.sn',      'root', 'ADMIN'),
( 'Chargé de Vente',   'vente@storemanager.sn',      'root', 'VENTE'),
( 'Chargé de Stock',   'stock@storemanager.sn',      'root', 'STOCK'),
( 'Inventaire',        'inventaire@storemanager.sn', 'root', 'INVENTAIRE');



 select * from utilisateur;



CREATE TABLE produit (
    id               SERIAL PRIMARY KEY,
    nom              VARCHAR(150)   NOT NULL,
    prix_vente       NUMERIC(12,2)  NOT NULL CHECK (prix_vente >= 0),
    quantite_stock   INTEGER        NOT NULL DEFAULT 0 CHECK (quantite_stock >= 0)
);

INSERT INTO produit (nom, prix_vente, quantite_stock) VALUES
('Riz parfumé 25kg', 15000, 50),
('Huile 20L', 18000, 30),
('Sucre 25kg', 14000, 40),
('Lait en poudre 500g', 3500, 100),
('Thé 250g', 2500, 80),
('Farine 25kg', 12000, 25);

CREATE TABLE client (
    id             SERIAL PRIMARY KEY,
    prenom         VARCHAR(100)   NOT NULL,
    nom            VARCHAR(100)   NOT NULL,
    telephone      VARCHAR(20)    NOT NULL UNIQUE,
    email          VARCHAR(150),
    limite_credit  NUMERIC(12,2)  NOT NULL DEFAULT 0 CHECK (limite_credit >= 0)
);


ALTER TABLE client
ALTER COLUMN email SET NOT NULL;

ALTER TABLE client
ADD CONSTRAINT uq_client_email UNIQUE (email);

INSERT INTO client ( prenom, nom, telephone, email, limite_credit) VALUES
( 'Abdou',    'Ndiaye',  '776543210', 'ndiaye@gmail.com', 150000),
( 'Fama',     'Diouf',   '781234567', 'diouf@gmail.com', 200000),
( 'Moussa',   'Sarr',    '769876543', 'sarr@gmail.com', 250000),
( 'Maimouna', 'Diallo',  '701122334', 'diallo@gmail.com', 120000),
( 'Ousmane',  'Sow',     '775554433', 'sow@gmail.com', 180000),
( 'Awa',      'Cisse',   '783332211', 'cisse@gmail.com', 300000),
( 'Babacar',  'Faye',    '762221100', 'faye@gmail.com', 150000),
( 'Khady',    'Mbacke',  '704443322', 'mbacke@gmail.com', 400000),
( 'Ibrahima', 'Gueye',   '778887766', 'gueye@gmail.com', 100000),
( 'Fatou',    'Fall',    '789998877', 'fall@gmail.com', 250000);


CREATE TABLE fournisseur (
    id         SERIAL PRIMARY KEY,
    nom        VARCHAR(150)  NOT NULL,
    telephone  VARCHAR(20),
    adresse    VARCHAR(255),
    email      VARCHAR(150)
);

ALTER TABLE fournisseur
ALTER COLUMN email SET NOT NULL;

ALTER TABLE fournisseur
ADD CONSTRAINT uq_fournisseur_email UNIQUE (email);

INSERT INTO fournisseur ( nom, telephone, adresse, email) VALUES
('Comptoir Céréalier Sénégalais', '338245678', 'ouakam', 'comptoir@gmail.com'),
('Grossiste Diop & Frères',       '773456789', 'sipres', 'diopfreres@gmail.com'),
('Sénégal Import-Export',         '338211010', 'keur massar', 'senegalexportimport@gmail.com');

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

INSERT INTO commande
(client_id, date_commande, montant_total, montant_verse, mode_reglement, statut)
VALUES
(1, '2026-08-01 09:30:00', 30000, 30000, 'Especes', 'COMPTANT'),

(2, '2026-08-02 10:15:00', 36000, 20000, 'Wave', 'CREDIT'),

(3, '2026-08-03 14:20:00', 42000, 42000, 'Orange Money', 'COMPTANT'),

(4, '2026-08-04 11:00:00', 28000, 10000, 'Especes', 'CREDIT'),

(5, '2026-08-05 16:45:00', 35000, 35000, 'Wave', 'COMPTANT'),

(6, '2026-08-06 09:10:00', 54000, 30000, 'Orange Money', 'CREDIT');



CREATE TABLE ligne_commande (
    id              SERIAL PRIMARY KEY,
    commande_id     INTEGER        NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    produit_id      INTEGER        NOT NULL REFERENCES produit(id),
    quantite        INTEGER        NOT NULL CHECK (quantite > 0),
    prix_unitaire   NUMERIC(12,2)  NOT NULL CHECK (prix_unitaire >= 0)
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
    id                SERIAL PRIMARY KEY,
    commande_id       INTEGER        NOT NULL UNIQUE REFERENCES commande(id),
    montant_initial   NUMERIC(12,2)  NOT NULL CHECK (montant_initial >= 0),
    montant_restant   NUMERIC(12,2)  NOT NULL CHECK (montant_restant >= 0),
    date_creation     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut            VARCHAR(20)    NOT NULL DEFAULT 'NON SOLDEE'
        CHECK (statut IN ('NON SOLDEE', 'SOLDEE')),
    CONSTRAINT chk_dette_restant_le_initial CHECK (montant_restant <= montant_initial)
);


INSERT INTO dette
(commande_id, montant_initial, montant_restant, statut)
VALUES
(2, 36000, 16000, 'NON SOLDEE'),
(4, 28000, 18000, 'NON SOLDEE'),
(6, 54000, 24000, 'NON SOLDEE');





CREATE TABLE paiement (
    id              SERIAL PRIMARY KEY,
    dette_id        INTEGER        NOT NULL REFERENCES dette(id),
    montant         NUMERIC(12,2)  NOT NULL CHECK (montant > 0),
    mode_paiement   VARCHAR(20)    NOT NULL
        CHECK (mode_paiement IN ('Especes', 'Wave', 'Orange Money')),
    date_paiement   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
);



INSERT INTO paiement
(dette_id, montant, mode_paiement, date_paiement)
VALUES

(1, 10000, 'Wave', '2026-08-07 10:00:00'),

(2, 5000, 'Especes', '2026-08-08 11:30:00'),

(3, 15000, 'Orange Money', '2026-08-09 15:00:00');

CREATE TABLE approvisionnement (
    id               SERIAL PRIMARY KEY,
    fournisseur_id   INTEGER      NOT NULL REFERENCES fournisseur(id),
    ref_bl           VARCHAR(50)  NOT NULL UNIQUE,
    date_reception   TIMESTAMP,
    statut           VARCHAR(20)  NOT NULL DEFAULT 'EN COURS'
        CHECK (statut IN ('EN COURS', 'REÇU'))
);

INSERT INTO approvisionnement
(fournisseur_id, ref_bl, date_reception, statut)
VALUES
(1, 'BL-2026-001', '2026-08-01 08:30:00', 'REÇU'),

(2, 'BL-2026-002', '2026-08-03 09:00:00', 'REÇU'),

(3, 'BL-2026-003', NULL, 'EN COURS');

CREATE TABLE ligne_approvisionnement (
    id                    SERIAL PRIMARY KEY,
    approvisionnement_id  INTEGER        NOT NULL REFERENCES approvisionnement(id) ON DELETE CASCADE,
    produit_id            INTEGER        NOT NULL REFERENCES produit(id),
    quantite_commandee    INTEGER        NOT NULL CHECK (quantite_commandee >= 0),
    quantite_livree       INTEGER        NOT NULL DEFAULT 0 CHECK (quantite_livree >= 0),
    cout_unitaire         NUMERIC(12,2)  NOT NULL CHECK (cout_unitaire >= 0)
);

INSERT INTO ligne_approvisionnement
(approvisionnement_id, produit_id, quantite_commandee, quantite_livree, cout_unitaire)
VALUES

(1, 1, 100, 100, 12000),
(1, 3, 80, 80, 11000),

(2, 2, 60, 60, 15000),
(2, 4, 100, 100, 2800),

(3, 5, 100, 0, 1800),
(3, 6, 50, 0, 9500);

SELECT * FROM commande;

SELECT * FROM ligne_commande;

SELECT * FROM dette;

SELECT * FROM paiement;

SELECT * FROM approvisionnement;

SELECT * FROM ligne_approvisionnement;







CREATE INDEX idx_commande_client_id            ON commande(client_id);
CREATE INDEX idx_ligne_commande_commande_id     ON ligne_commande(commande_id);
CREATE INDEX idx_ligne_commande_produit_id      ON ligne_commande(produit_id);
CREATE INDEX idx_dette_commande_id              ON dette(commande_id);
CREATE INDEX idx_paiement_dette_id              ON paiement(dette_id);
CREATE INDEX idx_approvisionnement_fournisseur  ON approvisionnement(fournisseur_id);
CREATE INDEX idx_ligne_appro_approvisionnement  ON ligne_approvisionnement(approvisionnement_id);
CREATE INDEX idx_ligne_appro_produit_id         ON ligne_approvisionnement(produit_id);










SELECT
    c.id                              AS id_commande,
    '#CMD-' || c.id                   AS ref,
    cl.prenom || ' ' || cl.nom        AS client,
    cl.telephone,
    c.date_commande,
    c.montant_total,
    c.montant_verse,
    (c.montant_total - c.montant_verse) AS reste,
    c.mode_reglement,
    c.statut
FROM commande c
INNER JOIN client cl ON cl.id = c.client_id
ORDER BY c.id DESC;



SELECT COUNT(*) AS nb_commandes_avant FROM commande;
 
BEGIN;
 
    INSERT INTO commande (client_id, montant_total, montant_verse, mode_reglement, statut)
    VALUES (1, 15000, 15000, 'Especes', 'COMPTANT')
    RETURNING id;
 
    INSERT INTO ligne_commande (commande_id, produit_id, quantite, prix_unitaire)
    VALUES (
        (SELECT MAX(id) FROM commande), 
        1, 1, 15000
    );
 
    UPDATE produit
    SET quantite_stock = quantite_stock - 1
    WHERE id = 1 AND quantite_stock >= 1;
 
COMMIT;


