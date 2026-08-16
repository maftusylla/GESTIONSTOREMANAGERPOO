<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Dette.php';
require_once dirname(__DIR__) . '/Model/Entity/Paiement.php';

class DetteRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Dette
    {
        $ligne = $this->db->executeQuery(
            'SELECT id, commande_id, montant_initial, montant_restant, date_creation, statut
             FROM dette WHERE id = :id',
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
            'SELECT id, commande_id, montant_initial, montant_restant, date_creation, statut
             FROM dette ORDER BY date_creation DESC',
            [],
            false
        );

        return array_map(
            fn (array $ligne): Dette => $this->hydrater($ligne),
            $lignes
        );
    }

    public function findPaiementsByDette(int $detteId): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT id, dette_id, montant, mode_paiement, date_paiement
             FROM paiement WHERE dette_id = :detteId ORDER BY date_paiement DESC',
            ['detteId' => $detteId],
            false
        );

        return array_map(
            fn (array $ligne): Paiement => new Paiement(
                (int) $ligne['id'],
                (int) $ligne['dette_id'],
                (float) $ligne['montant'],
                (string) $ligne['mode_paiement'],
                new DateTimeImmutable((string) $ligne['date_paiement'])
            ),
            $lignes
        );
    }

    public function enregistrerPaiement(int $detteId, float $montant, string $modePaiement): int
    {
        $this->db->executeUpdate(
            'INSERT INTO paiement (dette_id, montant, mode_paiement)
             VALUES (:detteId, :montant, :modePaiement)',
            [
                'detteId' => $detteId,
                'montant' => $montant,
                'modePaiement' => $modePaiement,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function mettreAJourApresRemboursement(
        int $detteId,
        float $nouveauMontantRestant,
        string $nouveauStatut
    ): int {
        return $this->db->executeUpdate(
            'UPDATE dette
             SET montant_restant = :montantRestant, statut = :statut
             WHERE id = :id',
            [
                'montantRestant' => $nouveauMontantRestant,
                'statut' => $nouveauStatut,
                'id' => $detteId,
            ]
        );
    }
    public function sommeTotalPaiements(): float
    {
        $ligne = $this->db->executeQuery(
            'SELECT COALESCE(SUM(montant), 0) AS total FROM paiement'
        );

        return (float) ($ligne['total'] ?? 0);
    }

    public function sommeEncoursActif(): float
    {
        $ligne = $this->db->executeQuery(
            "SELECT COALESCE(SUM(montant_restant), 0) AS total FROM dette WHERE statut = 'NON SOLDEE'"
        );

        return (float) ($ligne['total'] ?? 0);
    }

    private function hydrater(array $ligne): Dette
    {
        return new Dette(
            (int) $ligne['id'],
            (int) $ligne['commande_id'],
            (float) $ligne['montant_initial'],
            (float) $ligne['montant_restant'],
            new DateTimeImmutable((string) $ligne['date_creation']),
            (string) $ligne['statut']
        );
    }
}