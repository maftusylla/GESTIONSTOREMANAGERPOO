<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Repository/ClientRepository.php';
require_once dirname(__DIR__) . '/Repository/CommandeRepository.php';
require_once dirname(__DIR__) . '/Repository/DetteRepository.php';
require_once dirname(__DIR__) . '/Service/DebtService.php';

class DetteController
{
    private ClientRepository $clientRepository;
    private CommandeRepository $commandeRepository;
    private DetteRepository $detteRepository;
    private DebtService $debtService;

    public function __construct()
    {
        Session::init();

        $this->clientRepository = new ClientRepository();
        $this->commandeRepository = new CommandeRepository();
        $this->detteRepository = new DetteRepository();
        $this->debtService = new DebtService();
    }

    public function afficherVue(): void
    {
        $erreur = Session::getFlash('erreur');
        $succes = Session::getFlash('succes');

        $lignesDettes = $this->construireRegistreDettes();

        $creancesActives = 0.0;
        $clientsDebiteurIds = [];
        $totalRecouvrements = 0.0;

        foreach ($lignesDettes as $ligne) {
            foreach ($ligne['paiements'] as $paiement) {
                $totalRecouvrements += $paiement->getMontant();
            }

            if (!$ligne['dette']->estSoldee()) {
                $creancesActives += $ligne['dette']->getMontantRestant();

                if ($ligne['client'] !== null) {
                    $clientsDebiteurIds[$ligne['client']->getId()] = true;
                }
            }
        }

        $nombreClientsDebiteurs = count($clientsDebiteurIds);

        require dirname(__DIR__) . '/Views/dettes/index.php';
    }

    public function payerDette(): void
    {
        try {
            $detteId = (int) ($_POST['dette_id'] ?? 0);
            $montantVerse = (float) ($_POST['montant_verse'] ?? 0);
            $modePaiement = (string) ($_POST['mode_paiement'] ?? '');

            $dette = $this->debtService->enregistrerRemboursement($detteId, $montantVerse, $modePaiement);

            Session::setFlash('succes', $dette->estSoldee()
                ? 'Remboursement enregistré : dette #' . $dette->getId() . ' intégralement soldée.'
                : 'Remboursement enregistré : reste dû ' . number_format($dette->getMontantRestant(), 0, ',', ' ') . ' F.');
        } catch (InvalidArgumentException|RuntimeException $e) {
            Session::setFlash('erreur', $e->getMessage());
        }

        header('Location: /dettes');
        exit;
    }

    private function construireRegistreDettes(): array
    {
        $dettes = $this->detteRepository->findAll();
        $lignes = [];

        foreach ($dettes as $dette) {
            $commande = $this->commandeRepository->findById($dette->getCommandeId());
            $client = $commande !== null ? $this->clientRepository->findById($commande->getClientId()) : null;
            $articles = $commande !== null
                ? $this->commandeRepository->findLignesByCommande($commande->getId())
                : [];
            $paiements = $this->detteRepository->findPaiementsByDette($dette->getId());

            $lignes[] = [
                'dette' => $dette,
                'commande' => $commande,
                'client' => $client,
                'articles' => $articles,
                'paiements' => $paiements,
            ];
        }

        return $lignes;
    }
}