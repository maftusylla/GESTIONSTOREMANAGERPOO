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
- une dette est associée à un paiement ;
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