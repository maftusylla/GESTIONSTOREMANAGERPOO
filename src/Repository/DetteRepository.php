<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Dette.php';
require_once dirname(__DIR__) . '/Model/Entity/Paiement.php';
require_once dirname(__DIR__) . '/Repository/CommandeRepository.php';

class DetteRepository
{

    public static function findById(int $id): ?Dette
    {
        $ligne = Database::executeQuery(
            'SELECT id, commande_id, montant_initial, montant_restant, date_creation, statut
             FROM dette WHERE id = :id',
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
            'SELECT id, commande_id, montant_initial, montant_restant, date_creation, statut
             FROM dette ORDER BY date_creation DESC',
            [],
            false
        );

        return array_map(
            fn (array $ligne): Dette => self::hydrater($ligne),
            $lignes
        );
    }

    public static function findPaiementsByDette(int $detteId): array
    {
        $lignes = Database::executeQuery(
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

    public static function enregistrerPaiement(int $detteId, float $montant, string $modePaiement): int
    {
        Database::executeUpdate(
            'INSERT INTO paiement (dette_id, montant, mode_paiement)
             VALUES (:detteId, :montant, :modePaiement)',
            [
                'detteId' => $detteId,
                'montant' => $montant,
                'modePaiement' => $modePaiement,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function mettreAJourApresRemboursement(
        int $detteId,
        float $nouveauMontantRestant,
        string $nouveauStatut
    ): int {
        return Database::executeUpdate(
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

    public static function sommeTotalPaiements(): float
    {
        $ligne = Database::executeQuery(
            'SELECT COALESCE(SUM(montant), 0) AS total FROM paiement'
        );

        return (float) ($ligne['total'] ?? 0);
    }

    public static function sommeEncoursActif(): float
    {
        $ligne = Database::executeQuery(
            "SELECT COALESCE(SUM(montant_restant), 0) AS total FROM dette WHERE statut = 'NON SOLDEE'"
        );

        return (float) ($ligne['total'] ?? 0);
    }

    private static function hydrater(array $ligne): Dette
    {
        // Dette attend désormais un vrai objet Commande (et non plus un int) :
        // on va le chercher via CommandeRepository avant de construire la Dette.
        $commande = CommandeRepository::findById((int) $ligne['commande_id']);

        if ($commande === null) {
            throw new RuntimeException(
                'Dette #' . $ligne['id'] . ' référence une commande introuvable (id ' . $ligne['commande_id'] . ').'
            );
        }

        return new Dette(
            (int) $ligne['id'],
            $commande,
            (float) $ligne['montant_initial'],
            (float) $ligne['montant_restant'],
            new DateTimeImmutable((string) $ligne['date_creation']),
            (string) $ligne['statut']
        );
    }
}