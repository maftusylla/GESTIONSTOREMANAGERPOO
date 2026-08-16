<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Commande.php';
require_once dirname(__DIR__) . '/Model/Entity/LigneCommande.php';

class CommandeRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function creerCommande(
        int $clientId,
        float $montantTotal,
        float $montantVerse,
        string $modeReglement,
        string $statut
    ): int {
        $this->db->executeUpdate(
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

        return (int) $this->db->lastInsertId();
    }

    public function ajouterLigneCommande(
        int $commandeId,
        int $produitId,
        int $quantite,
        float $prixUnitaire
    ): int {
        $this->db->executeUpdate(
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

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Commande
    {
        $ligne = $this->db->executeQuery(
            'SELECT id, client_id, date_commande, montant_total, montant_verse, mode_reglement, statut
             FROM commande WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        return $this->hydrater($ligne);
    }

    public function findAll(): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT id, client_id, date_commande, montant_total, montant_verse, mode_reglement, statut
             FROM commande ORDER BY date_commande DESC',
            [],
            false
        );

        return array_map(
            fn (array $ligne): Commande => $this->hydrater($ligne),
            $lignes
        );
    }
    public function sommeTotalMontantVerse(): float
    {
        $ligne = $this->db->executeQuery(
            'SELECT COALESCE(SUM(montant_verse), 0) AS total FROM commande'
        );

        return (float) ($ligne['total'] ?? 0);
    }
    public function findLignesByCommande(int $commandeId): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT lc.id, lc.commande_id, lc.produit_id, lc.quantite, lc.prix_unitaire, p.nom AS produit_nom
             FROM ligne_commande lc
             INNER JOIN produit p ON p.id = lc.produit_id
             WHERE lc.commande_id = :commandeId',
            ['commandeId' => $commandeId],
            false
        );

        return $lignes;
    }

    private function hydrater(array $ligne): Commande
    {
        return new Commande(
            (int) $ligne['id'],
            (int) $ligne['client_id'],
            new DateTimeImmutable((string) $ligne['date_commande']),
            (float) $ligne['montant_total'],
            (float) $ligne['montant_verse'],
            (string) $ligne['mode_reglement'],
            (string) $ligne['statut']
        );
    }
}