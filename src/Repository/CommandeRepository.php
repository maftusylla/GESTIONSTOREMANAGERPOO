<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Commande.php';
require_once dirname(__DIR__) . '/Model/Entity/LigneCommande.php';
require_once dirname(__DIR__) . '/Repository/ClientRepository.php';

class CommandeRepository
{
    
    public static function creerCommande(
        int $clientId,
        float $montantTotal,
        float $montantVerse,
        string $modeReglement,
        string $statut
    ): int {
        Database::executeUpdate(
            'INSERT INTO commande
                (client_id, montant_total, montant_verse, mode_reglement, statut)
             VALUES
                (:clientId, :montantTotal, :montantVerse, :modeReglement, :statut)',
            [
                'clientId' => $clientId,
                'montantTotal' => $montantTotal,
                'montantVerse' => $montantVerse,
                'modeReglement' => $modeReglement,
                'statut' => $statut,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function ajouterLigneCommande(
        int $commandeId,
        int $produitId,
        int $quantite,
        float $prixUnitaire
    ): int {
        Database::executeUpdate(
            'INSERT INTO ligne_commande
                (commande_id, produit_id, quantite, prix_unitaire)
             VALUES
                (:commandeId, :produitId, :quantite, :prixUnitaire)',
            [
                'commandeId' => $commandeId,
                'produitId' => $produitId,
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function findById(int $id): ?Commande
    {
        $ligne = Database::executeQuery(
            'SELECT id, client_id, date_commande, montant_total, montant_verse, mode_reglement, statut
             FROM commande WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        return self::hydrater($ligne);
    }

    public static function findAll(): array
    {
        $lignes = Database::executeQuery(
            'SELECT id, client_id, date_commande, montant_total, montant_verse, mode_reglement, statut
             FROM commande ORDER BY date_commande DESC',
            [],
            false
        );

        return array_map(
            fn (array $ligne): Commande => self::hydrater($ligne),
            $lignes
        );
    }

    public static function sommeTotalMontantVerse(): float
    {
        $ligne = Database::executeQuery(
            'SELECT COALESCE(SUM(montant_verse), 0) AS total FROM commande'
        );

        return (float) ($ligne['total'] ?? 0);
    }

    public static function findLignesByCommande(int $commandeId): array
    {
        $lignes = Database::executeQuery(
            'SELECT lc.id, lc.commande_id, lc.produit_id, lc.quantite, lc.prix_unitaire, p.nom AS produit_nom
             FROM ligne_commande lc
             INNER JOIN produit p ON p.id = lc.produit_id
             WHERE lc.commande_id = :commandeId',
            ['commandeId' => $commandeId],
            false
        );

        return $lignes;
    }

    private static function hydrater(array $ligne): Commande
    {
        // Commande attend désormais un vrai objet Client (et non plus un int) :
        // on va le chercher via ClientRepository avant de construire la Commande.
        $client = ClientRepository::findById((int) $ligne['client_id']);

        if ($client === null) {
            throw new RuntimeException(
                'Commande #' . $ligne['id'] . ' référence un client introuvable (id ' . $ligne['client_id'] . ').'
            );
        }

        return new Commande(
            (int) $ligne['id'],
            $client,
            new DateTimeImmutable((string) $ligne['date_commande']),
            (float) $ligne['montant_total'],
            (float) $ligne['montant_verse'],
            (string) $ligne['mode_reglement'],
            (string) $ligne['statut']
        );
    }
}