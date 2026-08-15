Nom & Prénom : SYLLA Mame Fatou  
Projet: StoreManager Pro (ERP PHP/POO) 


- **Heure de réalisation** : 19h-20h30


**Ce qui a été fait** :

Réalisation des diagrammes UML du système :

- Diagrammes de cas d'utilisation par acteur.
- Diagramme de classes métier.

Les cas d'utilisation ont été organisés selon les différents profils utilisateurs :
- Admin Boutique
- Chargé de Vente
- Chargé de Stock
- Inventaire

Les fonctionnalités ont été regroupées par domaines fonctionnels :

- Dashboard
- Ventes / POS
- Dettes
- Approvisionnements
- Produits & Tiers

Une distinction a été faite entre :

- les cas d'utilisation correspondant à un véritable objectif métier ;
- les étapes techniques ou éléments d'interface qui ne constituent pas nécessairement des cas d'utilisation ;
- les fonctionnalités obligatoires représentées avec `<<include>>` ;
- les fonctionnalités conditionnelles représentées avec `<<extend>>`.

Le diagramme de classes comprend notamment :

- `Utilisateur`
- `Role`
- `Produit`
- `Client`
- `Fournisseur`
- `Commande`
- `LigneCommande`
- `Dette`
- `Paiement`
- `Approvisionnement`
- `LigneApprovisionnement`

Les principales associations métier ont également été définies :

- un client possède plusieurs commandes ;
- une commande possède une ou plusieurs lignes de commande ;
- une ligne de commande concerne un produit ;
- une commande peut être associée à une dette ;
- une dette est associée à N paiement  ;
- un fournisseur possède plusieurs approvisionnements ;
- un approvisionnement possède une ou plusieurs lignes d'approvisionnement ;
- une ligne d'approvisionnement concerne un produit ;
- un utilisateur possède un rôle.


**Difficultés / Obstacles** :
- Trancher si Dette devait être reliée directement à Client ou uniquement via Commande — choix final : uniquement via Commande, car une dette correspond toujours à une seule vente à crédit.


### Livrable

Diagrammes UML ajoutés dans :

`/docs/`

### Commit

```bash
git commit -m "docs(uml): ajout des diagrammes de cas d'utilisation et de classes POO"

#### 📌 Step 1.2 (20h30 - 22h00) : Schéma SQL PostgreSQL / SQLite


**Heure de réalisation** :20h30-22h


**Ce qui a été fait** :

Réalisation des diagrammes UML du système :

- Diagrammes de cas d'utilisation par acteur.
- Diagramme de classes métier.

---

# 📌 Step 1.2 — Schéma SQL PostgreSQL / SQLite

**Heure de réalisation :** 20h30 - 22h00

**Ce qui a été fait** :

Création du schéma de base de données à partir du diagramme de classes UML
réalisé lors de l'étape précédente.

Deux scripts SQL ont été préparés afin de permettre le fonctionnement du
projet avec PostgreSQL et avec SQLite en cas de fallback :

- `schema.sql` pour PostgreSQL ;
- `schema_sqlite.sql` pour SQLite.

Les tables correspondent aux principales entités métier du diagramme de
classes :

- `utilisateur`
- `produit`
- `client`
- `fournisseur`
- `commande`
- `ligne_commande`
- `dette`
- `paiement`
- `approvisionnement`
- `ligne_approvisionnement`

Les relations définies dans le diagramme UML ont été traduites en clés
étrangères SQL.

Les principales relations sont :

- un client possède plusieurs commandes ;
- une commande possède une ou plusieurs lignes de commande ;
- une ligne de commande concerne un produit ;
- une commande peut être associée à une dette ;
- une dette peut recevoir plusieurs paiements ;
- un fournisseur possède plusieurs approvisionnements ;
- un approvisionnement possède une ou plusieurs lignes d'approvisionnement ;
- une ligne d'approvisionnement concerne un produit.

Une attention particulière a été portée à la relation entre `Dette` et
`Paiement`.

Dans le modèle initial, cette relation avait été représentée comme une
relation `1..1`. Après analyse du fonctionnement métier du remboursement, elle
a été corrigée afin de permettre plusieurs remboursements pour une même dette.

La cardinalité retenue est donc :

```text
Dette 1 ---- 0..N Paiement



### Et surtout, pour le **Step 1.1**, j'ai fais  une petite correction

Dans ton ancien Step 1.1, tu as actuellement :

> une dette est associée à N paiement

Si **le diagramme UML corrigé est maintenant `Dette 1 ---- 0..N Paiement`**, écris plutôt :

```markdown
- une dette peut recevoir plusieurs paiements ;


et j'ai des ajoutés des captures d'ecrans des differentes classes