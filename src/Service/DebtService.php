<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Repository/DetteRepository.php';

class DebtService
{
    private const MODES_PAIEMENT_VALIDES = ['Especes', 'Wave', 'Orange Money'];

    private Database $db;
    private DetteRepository $detteRepository;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->detteRepository = new DetteRepository();
    }

    public function enregistrerRemboursement(
        int $detteId,
        float $montant,
        string $modePaiement
    ): Dette {
        if ($montant <= 0) {
            throw new InvalidArgumentException('Le montant du remboursement doit être supérieur à zéro.');
        }

        if (!in_array($modePaiement, self::MODES_PAIEMENT_VALIDES, true)) {
            throw new InvalidArgumentException('Mode de paiement invalide : ' . $modePaiement);
        }

        $dette = $this->detteRepository->findById($detteId);
        if ($dette === null) {
            throw new InvalidArgumentException('Dette introuvable (id ' . $detteId . ').');
        }

        if ($dette->estSoldee()) {
            throw new RuntimeException('Cette dette est déjà soldée, aucun remboursement possible.');
        }

        if ($montant > $dette->getMontantRestant()) {
            throw new InvalidArgumentException(
                'Le remboursement (' . $montant . ' F) dépasse le reste dû (' . $dette->getMontantRestant() . ' F).'
            );
        }

        return $this->db->transaction(function () use ($dette, $montant, $modePaiement): Dette {
            $this->detteRepository->enregistrerPaiement($dette->getId(), $montant, $modePaiement);

            $nouveauMontantRestant = round($dette->getMontantRestant() - $montant, 2);
            $nouveauStatut = $nouveauMontantRestant <= 0.0 ? 'SOLDEE' : 'NON SOLDEE';

            $this->detteRepository->mettreAJourApresRemboursement(
                $dette->getId(),
                $nouveauMontantRestant,
                $nouveauStatut
            );

            return new Dette(
                $dette->getId(),
                $dette->getCommandeId(),
                $dette->getMontantInitial(),
                $nouveauMontantRestant,
                $dette->getDateCreation(),
                $nouveauStatut
            );
        });
    }
}