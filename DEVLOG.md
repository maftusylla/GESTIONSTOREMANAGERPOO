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




 📌 Step 1.3 — Singleton Database & Fallback Automatique

**Heure de réalisation :** 22h00 - 23h00

**Ce qui a été fait** :

Mise en place de la classe `Database` permettant de centraliser la gestion des connexions à la base de données.

La connexion principale de l'application est réalisée avec PostgreSQL. Un mécanisme de fallback automatique vers SQLite a également été mis en place afin que l'application puisse continuer à fonctionner lorsque la connexion PostgreSQL échoue.

Le fichier créé est :

```text
src/Core/Database.php



1. Objectif — pourquoi le Singleton et pas une connexion classique

L'objectif de cette classe est d'éviter de créer plusieurs connexions à la base de données au cours de l'exécution de l'application.

Une connexion classique pourrait conduire plusieurs composants de l'application à créer chacun leur propre objet PDO.

Le pattern Singleton permet de garantir qu'une seule instance de Database est utilisée dans l'application.

Le principe retenu est donc :

Application
     |
     v
 Database
     |
     +------ PostgreSQL
     |
     +------ SQLite (fallback)

Tous les composants qui ont besoin d'accéder à la base passent par la même instance de Database.

Cela permet notamment de :

centraliser la configuration de la connexion ;
éviter la multiplication des connexions ;
centraliser le mécanisme de fallback ;
fournir une seule connexion PDO aux repositories et services ;
simplifier la gestion de la base de données dans l'application.
2. Structure du fichier — namespace et strict_types

La classe Database est placée dans le namespace correspondant à l'architecture du projet :

namespace App\Core;

Le fichier utilise également :

declare(strict_types=1);

L'utilisation de strict_types permet d'avoir un typage plus strict dans le fichier et d'éviter certaines conversions implicites de types.

La classe Database est responsable uniquement de la gestion de la connexion à la base de données.

Elle contient notamment :

l'instance unique du Singleton ;
la connexion PDO ;
la connexion PostgreSQL ;
la connexion SQLite ;
les méthodes permettant de récupérer la connexion ;
la méthode permettant d'identifier le driver utilisé.
3. Les 4 mécanismes du pattern Singleton

Le Singleton repose sur plusieurs éléments complémentaires.

3.1 $instance statique

Une propriété statique permet de conserver l'unique instance de la classe.

Le principe est :

private static ?self $instance = null;

Au départ, aucune instance n'existe :

$instance = null

Lors du premier appel à getInstance(), une instance est créée puis stockée dans cette propriété.

Les appels suivants récupèrent cette même instance.

3.2 Constructeur privé

Le constructeur est déclaré privé :

private function __construct()
{
}

Cela empêche le code extérieur de créer directement un objet avec :

new Database();

La création de l'objet doit donc obligatoirement passer par :

Database::getInstance();

Cela permet de contrôler la création de l'unique instance.

3.3 __clone() privé

La méthode de clonage est également rendue privée :

private function __clone()
{
}

Cela empêche de contourner le Singleton avec :

$clone = clone $database;

Sans cette protection, il serait possible de créer une deuxième instance à partir de la première.

3.4 __wakeup()

La méthode __wakeup() est également protégée afin d'empêcher la création d'une nouvelle instance via la désérialisation.

Le principe est de bloquer une tentative de reconstruction de l'objet à partir d'une donnée sérialisée.

Le Singleton protège ainsi les principaux moyens permettant de créer ou recréer une deuxième instance :

new
 |
 +--> constructeur privé


clone
 |
 +--> __clone() privé


unserialize()
 |
 +--> __wakeup()
4. getInstance() — déroulement pas à pas

La méthode getInstance() constitue le point d'entrée permettant d'obtenir l'instance unique.

Son fonctionnement est différent lors du premier appel et lors des appels suivants.

Premier appel

Lors du premier appel :

Database::getInstance();

la propriété :

self::$instance

est encore null.

La méthode crée donc l'objet Database.

Le processus est :

getInstance()
      |
      v
instance existe ?
      |
     NON
      |
      v
création de Database
      |
      v
connexion PostgreSQL
      |
      | échec
      v
connexion SQLite
      |
      v
stockage de l'instance

L'instance créée est ensuite conservée dans :

self::$instance
Appels suivants

Lorsqu'un autre composant appelle :

Database::getInstance();

la propriété self::$instance contient déjà l'objet.

Aucune nouvelle instance n'est donc créée.

Le même objet est retourné.

getInstance()
      |
      v
instance existe ?
      |
     OUI
      |
      v
retour de la même instance

Cela garantit le principe du Singleton.

5. connectPostgreSQL() — variables d'environnement et options PDO

La méthode connectPostgreSQL() est chargée d'établir la connexion principale à PostgreSQL.

Les informations de connexion sont récupérées depuis les variables d'environnement afin de ne pas inscrire directement les identifiants de connexion dans le code source.

Les paramètres utilisés concernent notamment :

l'hôte ;
le port ;
la base de données ;
l'utilisateur ;
le mot de passe.

Le principe est de construire le DSN PostgreSQL à partir de ces informations.

La connexion est réalisée avec PDO.

Les options PDO permettent notamment d'avoir une gestion plus sûre des erreurs et un comportement adapté aux requêtes préparées.

L'utilisation de :

PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

permet notamment de faire remonter les erreurs sous forme d'exceptions.

Cela est important pour le mécanisme de fallback : si PostgreSQL ne peut pas être utilisé, l'exception permet de détecter l'échec et de passer à SQLite.

L'utilisation des requêtes préparées permet également de limiter les risques liés à l'injection SQL lorsque les données provenant de l'utilisateur sont utilisées dans les requêtes.

6. connectSQLite() — mécanisme de fallback

Si la connexion PostgreSQL échoue, la classe tente automatiquement une connexion SQLite.

La base utilisée comme fallback est :

erp.db

Le principe est donc :

        PostgreSQL
             |
        connexion OK ?
          /       \
        OUI       NON
        |          |
        v          v
   PostgreSQL    SQLite

L'objectif est de permettre à l'application de continuer à fonctionner même si PostgreSQL n'est pas disponible.

PRAGMA foreign_keys

Lors de la connexion SQLite, les clés étrangères doivent être explicitement activées.

La commande utilisée est :

PRAGMA foreign_keys = ON;

Cette activation est importante car les relations définies dans le schéma doivent être réellement contrôlées par SQLite.

Par exemple, les relations :

commande -> client
dette -> commande
paiement -> dette
ligne_commande -> commande
ligne_commande -> produit

doivent respecter les contraintes de clés étrangères.

Sans l'activation de foreign_keys, SQLite pourrait ne pas appliquer ces contraintes comme attendu.

Auto-initialisation de SQLite

Le fallback doit également pouvoir utiliser la base SQLite lorsque celle-ci n'existe pas encore.

Le mécanisme prévoit donc l'utilisation du fichier :

erp.db

et l'initialisation du schéma SQLite lorsque cela est nécessaire.

Cela permet d'obtenir le comportement suivant :

PostgreSQL disponible
        |
        v
utilisation de PostgreSQL




PostgreSQL indisponible
        |
        v
connexion SQLite
        |
        v
erp.db existe ?
      /       \
    OUI       NON
    |          |
    v          v
utiliser   initialiser
erp.db     le schéma

Cette partie permet au fallback d'être réellement utilisable et pas seulement de changer de driver.

7. getConnection() / getDriver()
getConnection()

La méthode :

getConnection()

permet aux autres composants de récupérer la connexion PDO déjà établie.

Les repositories et services n'ont donc pas besoin de recréer eux-mêmes une connexion.

Ils peuvent utiliser la connexion centralisée par Database.

Le principe est :

Repository
     |
     v
Database::getInstance()
     |
     v
getConnection()
     |
     v
PDO
getDriver()

La méthode :

getDriver()

permet d'identifier le moteur de base de données actuellement utilisé.

Elle permet notamment de savoir si l'application fonctionne avec :

PostgreSQL

ou :

SQLite

Cette information est utile pour vérifier le fonctionnement du mécanisme de fallback et pour adapter certains comportements spécifiques au moteur si nécessaire.

8. Tests réalisés

Plusieurs tests ont été effectués afin de vérifier le bon fonctionnement de la classe Database.

Test 1 — Vérification de la syntaxe PHP

Le fichier Database.php a été vérifié afin de s'assurer qu'il ne contient pas d'erreur de syntaxe.

Objectif :

Database.php
     |
     v
syntaxe correcte

Résultat attendu :

Aucune erreur de syntaxe.
Test 2 — Vérification du Singleton

Plusieurs appels à :

Database::getInstance();

ont été effectués.

L'objectif était de vérifier que les appels retournent la même instance.

Le principe testé est :

$db1 = Database::getInstance();
$db2 = Database::getInstance();


$db1 === $db2

Résultat attendu :

true

Cela confirme que le Singleton fonctionne.

Test 3 — Test du fallback PostgreSQL → SQLite

La connexion PostgreSQL a été testée dans une situation où elle n'est pas disponible.

L'objectif était de vérifier que l'exception PostgreSQL est correctement interceptée et que la connexion SQLite est ensuite utilisée.

Résultat attendu :

PostgreSQL
    ↓ échec
SQLite
    ↓
connexion réussie

Le driver retourné par getDriver() doit alors correspondre à SQLite.

Test 4 — Test de l'auto-initialisation SQLite

Le fonctionnement du fichier :

erp.db

a été vérifié lorsque la base SQLite n'est pas encore présente.

L'objectif était de vérifier que le fallback peut créer/initialiser la base et utiliser le schéma prévu.

Résultat attendu :

erp.db absente
     ↓
initialisation
     ↓
base SQLite utilisable
Test 5 — Test d'une requête préparée

Une requête utilisant PDO et une requête préparée a été exécutée afin de vérifier que la connexion fournie par Database peut être utilisée par les repositories.

L'objectif était notamment de vérifier que les paramètres sont transmis séparément de la requête SQL.

Résultat attendu :

connexion PDO
     ↓
prepare()
     ↓
execute()
     ↓
résultat correct
Test 6 — Test des clés étrangères SQLite

La commande suivante a été vérifiée :

PRAGMA foreign_keys = ON;

L'objectif était de vérifier que SQLite applique effectivement les relations définies dans le schéma.

Une opération violant une clé étrangère doit être refusée.

Ce test permet de vérifier que le fallback SQLite respecte les contraintes relationnelles du schéma.

Test 7 — Test du clonage

Une tentative de clonage de l'instance Database a été vérifiée.

Le but était de confirmer que le mécanisme :

private function __clone()

empêche la création d'une seconde instance par clonage.

Résultat attendu :

clone Database
      ↓
opération interdite

Ce test complète la vérification du pattern Singleton.

9. Difficultés / Obstacles
Difficulté 1 — Garantir une seule instance

La première difficulté était de garantir qu'aucun composant ne puisse créer directement une nouvelle connexion.

Solution retenue :

Utilisation du pattern Singleton avec :

propriété $instance statique ;
constructeur privé ;
méthode getInstance() ;
__clone() privé ;
__wakeup() protégé.
Difficulté 2 — Gérer l'échec de PostgreSQL

La connexion PostgreSQL peut échouer lorsque le serveur n'est pas disponible ou lorsque les paramètres de connexion sont incorrects.

Solution retenue :

Encapsuler la connexion PostgreSQL dans un mécanisme de gestion d'exception afin de déclencher automatiquement la connexion SQLite en cas d'échec.

Difficulté 3 — Conserver les contraintes SQL avec SQLite

SQLite ne se comporte pas exactement comme PostgreSQL sur certains aspects, notamment concernant l'application des clés étrangères.

Solution retenue :

Activer explicitement :

PRAGMA foreign_keys = ON;

lors de la connexion SQLite.

Difficulté 4 — Initialiser automatiquement SQLite

Le fallback n'aurait pas été réellement utile si erp.db devait être créée manuellement avant chaque exécution.

Solution retenue :

Prévoir l'auto-initialisation de la base SQLite à partir du schéma prévu afin que le fallback puisse être utilisé directement.

Difficulté 5 — Ne pas mélanger la responsabilité de Database avec les repositories

La classe Database ne doit pas contenir la logique métier des produits, clients, commandes ou dettes.

Solution retenue :

Limiter la classe à la gestion de la connexion et laisser les repositories gérer les requêtes correspondant à leurs entités.

Difficulté 6 — Vérifier que le fallback utilise réellement SQLite

Il ne suffisait pas de tenter une deuxième connexion : il fallait pouvoir identifier le driver effectivement utilisé.

Solution retenue :

Mise en place de :

getDriver()

afin de vérifier le moteur utilisé pendant les tests.

Difficulté 7 — Protéger le Singleton contre les contournements

Le Singleton peut être contourné par le clonage ou la désérialisation si ces mécanismes ne sont pas protégés.

Solution retenue :

Protection des méthodes :

__clone()
__wakeup()

afin d'empêcher la création d'une seconde instance par ces mécanismes.

Livrable

Le fichier suivant a été créé :

src/Core/Database.php

Il contient :

l'implémentation du Singleton ;
la connexion PostgreSQL ;
le fallback SQLite ;
l'activation des clés étrangères SQLite ;
l'auto-initialisation de la base SQLite ;
getConnection() ;
getDriver() ;
la gestion des erreurs de connexion.
Commit
git add src/Core/Database.php
git commit -m "feat(core): implementation de Database Singleton avec fallback automatique PostgreSQL vers SQLite"


### ☀️ PHASE 2 : SAMEDI (09h00 - 20h00) — Cœur POO & Ventes POS

--- 📌 Step 1.2  Schéma SQL PostgreSQL / SQLite

**Ce qui a été fait** :
j'ai fais des modifications dans mes fichier schema.sql et shema_sqlite.sql pour inserer des données de base  à mes tables .

 git commit -m "modification(database): insertion de données d'initialisation SQL avec dans  schema.sql et shema_sqlite.sql "

#### 📌 Step 1.3 (22h00 - 23h00) : Singleton Database & Fallback Automatique

**Ce qui a été fait** :

# Migration et amélioration de la gestion de la base de données

## Objectif

La gestion de la base de données a été améliorée afin de rendre l'application plus flexible, plus propre et plus facile à maintenir. Le projet utilisait initialement une connexion PostgreSQL procédurale avec plusieurs fonctions globales (`connexionDB()`, `query()`, `prepare()`, `executeQuery()` et `executeUpdate()`).

La gestion de la connexion a été progressivement centralisée dans une classe `Database`.

## Passage à une classe `Database`

Une classe `Database` utilisant le pattern **Singleton** a été mise en place.

Elle centralise désormais :

* la création de la connexion PDO ;
* la configuration de PDO ;
* la gestion des requêtes préparées ;
* l'exécution des requêtes `SELECT` ;
* l'exécution des requêtes `INSERT`, `UPDATE` et `DELETE` ;
* la récupération du dernier identifiant inséré ;
* la gestion des transactions ;
* la détection du moteur de base de données utilisé.

L'accès à la connexion se fait maintenant avec :

```php
$db = Database::getInstance();
```

puis :

```php
$pdo = $db->getConnection();
```

ou directement avec les méthodes de la classe pour exécuter les requêtes.

## Support de PostgreSQL et SQLite

La classe `Database` a été conçue pour supporter deux moteurs :

* **PostgreSQL**, utilisé pour l'environnement principal ;
* **SQLite**, utilisé notamment pour simplifier le développement local.

Le moteur utilisé est maintenant déterminé explicitement par la configuration `DB_DRIVER`.

Exemple :

```text
DB_DRIVER=sqlite
```

ou :

```text
DB_DRIVER=pgsql
```

Cette approche est préférable au fallback automatique, car une erreur de connexion PostgreSQL ne doit pas provoquer silencieusement un changement de base de données.

##resultat
La gestion de la base de données est maintenant plus structurée et plus facilement maintenable.

Le projet peut fonctionner avec PostgreSQL ou SQLite sans modifier toute la logique applicative. La connexion PDO, les requêtes préparées, les transactions et l'initialisation SQLite sont centralisées dans une seule classe.

Cette évolution prépare également le projet à une architecture plus propre basée sur la séparation entre :

Database → Repository → Service → Controller

Cette architecture pourra ensuite être appliquée aux différents modules, notamment les commandes, les paiements, les dettes, les approvisionnements et la gestion des produits.


##  Finalisation de la classe Database
Après avoir testé ma version avec DB_DRIVER (qui permettait de choisir explicitement PostgreSQL ou SQLite), j'ai remarqué en la testant que le fallback automatique ne fonctionnait plus. Si PostgreSQL n'était pas disponible et que DB_DRIVER=pgsql était défini, l'application plantait carrément avec une exception au lieu de basculer sur SQLite comme prévu au départ.

J'ai donc remis en place le vrai fallback automatique, tout en gardant les méthodes que j'avais ajoutées.

Ce qui a changé

Le constructeur ne choisit plus le moteur à partir de DB_DRIVER. Il essaie maintenant toujours PostgreSQL en premier, et si ça échoue, il bascule automatiquement sur SQLite, sans que j'aie besoin de configurer quoi que ce soit. J'ai aussi ajouté un deuxième try/catch autour de la connexion SQLite : si jamais même SQLite n'arrive pas à se connecter (cas rare, genre problème de droits sur le fichier), l'erreur est levée clairement au lieu de planter sans explication.

Les méthodes que j'avais ajoutées avant restent toutes là :

query() pour les requêtes simples sans paramètre ;
prepare() pour préparer une requête avec des paramètres ;
executeQuery() pour exécuter une requête préparée et récupérer directement le résultat ;
executeUpdate() pour les INSERT/UPDATE/DELETE, qui renvoie le nombre de lignes modifiées ;
transaction() qui gère automatiquement le commit et le rollback en cas d'erreur ;
lastInsertId() pour récupérer l'id généré après un INSERT.
Ce que j'ai testé
J'ai revérifié que sans PostgreSQL disponible, l'application bascule bien sur SQLite automatiquement, que DB_DRIVER soit défini ou non.
J'ai testé transaction() avec un cas qui réussit (les données sont bien enregistrées) et un cas où je fais volontairement échouer le traitement au milieu (les données ne sont pas enregistrées du tout, le rollback fonctionne).
J'ai testé executeUpdate() suivi de lastInsertId() pour vérifier que je récupère bien le bon id après une insertion.
Résultat
La classe Database fonctionne maintenant comme prévu au départ : connexion PostgreSQL en priorité, bascule automatique et transparente sur SQLite si besoin



## Création des entités POO avec encapsulation et méthodes métier

Cette étape consiste à mettre en place les principales entités métier du projet StoreManagerPro en programmation orientée objet.


Les entités suivantes ont été mises en place dans :

src/Model/Entity/
Utilisateur
Role
Produit
Client
Fournisseur
Commande
LigneCommande
Dette
Paiement
Approvisionnement
LigneApprovisionnement

Ces entités correspondent directement aux principales tables du modèle de données.

Encapsulation

Les propriétés des entités ont été rendues privées (private).

Exemple :

private int $id;
private string $nom;
private float $prixVente;
private int $quantiteStock;

L'accès aux données se fait désormais par des méthodes publiques dédiées lorsque cela est nécessaire :

public function getNom(): string
{
    return $this->nom;
}

Cette modification permet d'éviter qu'une partie quelconque de l'application puisse modifier directement l'état interne d'une entité.

Par exemple, il n'est plus possible de faire directement :

$produit->quantiteStock = -10;

L'état de l'objet est ainsi mieux contrôlé.

Méthodes métier

Les méthodes métier déjà identifiées dans les entités ont été conservées afin que les objets ne soient pas uniquement de simples conteneurs de données.

Quelques exemples :

Produit
public function estEnRuptureDeStock(): bool
{
    return $this->quantiteStock === 0;
}

Cette méthode permet à l'entité Produit de déterminer directement si son stock est épuisé.

Client
public function peutObtenirCredit(
    float $montantSupplementaire,
    float $encoursDettesActuel
): bool {
    return (
        $encoursDettesActuel + $montantSupplementaire
    ) <= $this->limiteCredit;
}

La règle liée à la limite de crédit est ainsi portée par l'entité Client.

Commande
public function montantRestantAPayer(): float
{
    return $this->montantTotal - $this->montantVerse;
}

La commande est capable de déterminer elle-même le montant restant à payer.

Une autre méthode permet de vérifier si elle est intégralement payée :

public function estPayeeIntegralement(): bool
{
    return $this->montantVerse >= $this->montantTotal;
}
Ligne de commande
public function sousTotal(): float
{
    return $this->quantite * $this->prixUnitaire;
}
Dette
public function estSoldee(): bool
{
    return $this->statut === 'SOLDEE';
}
Approvisionnement
public function estRecu(): bool
{
    return $this->statut === 'REÇU';
}
Ligne d'approvisionnement
public function coutTotal(): float
{
    return $this->quantiteLivree * $this->coutUnitaire;
}
Typage des propriétés

Les propriétés et les méthodes utilisent le typage PHP afin de rendre le modèle plus fiable.

Exemple :

private int $id;
private string $nom;
private float $prixVente;
private int $quantiteStock;

Les constructeurs sont également typés afin que les objets soient créés avec des données correspondant à leur modèle.

Enumération des rôles

Le rôle utilisateur est représenté par un enum PHP :

enum Role: string
{
    case ADMIN = 'ADMIN';
    case VENTE = 'VENTE';
    case STOCK = 'STOCK';
    case INVENTAIRE = 'INVENTAIRE';
}

Cela évite de manipuler librement des chaînes de caractères pour les rôles et permet de conserver les valeurs prévues par le modèle de données.

Principe appliqué

La couche Entity représente désormais les objets métier du système.

L'organisation retenue est :

Entity
│
├── Utilisateur
├── Produit
├── Client
├── Fournisseur
├── Commande
├── LigneCommande
├── Dette
├── Paiement
├── Approvisionnement
└── LigneApprovisionnement

Chaque entité possède :

ses données internes ;
un constructeur ;
des propriétés encapsulées ;
des méthodes d'accès lorsque nécessaires ;
les méthodes métier déjà définies pour son domaine.
Résultat de l'étape
