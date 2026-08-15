<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Repository/ClientRepository.php';
require_once dirname(__DIR__) . '/Repository/ProduitRepository.php';
require_once dirname(__DIR__) . '/Repository/CommandeRepository.php';
require_once dirname(__DIR__) . '/Service/VenteService.php';

class POSController
{
    private ClientRepository $clientRepository;
    private ProduitRepository $produitRepository;
    private CommandeRepository $commandeRepository;
    private VenteService $venteService;

    public function __construct()
    {
        Session::init();

        $this->clientRepository = new ClientRepository();
        $this->produitRepository = new ProduitRepository();
        $this->commandeRepository = new CommandeRepository();
        $this->venteService = new VenteService();

        $this->initPanier();
    }

    public function afficher(): void
    {
        $erreur = null;
        $commandeCreeeId = null;

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && ($_POST['action'] ?? '') === 'add_to_cart'
        ) {
            try {
                $this->ajouterAuPanier();
            } catch (InvalidArgumentException|RuntimeException $e) {
                $erreur = $e->getMessage();
            }
        }

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && ($_POST['action'] ?? '') === 'create_order'
        ) {
            try {
                $commandeCreeeId = $this->traiterCreationCommande();
            } catch (InvalidArgumentException|RuntimeException $e) {
                $erreur = $e->getMessage();
            }
        }

        $panier = $this->getPanier();

        $clients = $this->clientRepository->findAll();
        $produits = $this->produitRepository->findAll();
        $ventes = $this->construireRegistreVentes();

        require dirname(__DIR__) . '/Views/pos/index.php';
    }

    private function initPanier(): void
    {
        if (Session::get('panier') === null) {
            Session::set('panier', []);
        }
    }

    private function getPanier(): array
    {
        $this->initPanier();

        return Session::get('panier', []);
    }

    private function ajouterAuPanier(): void
    {
        $produitId = (int) ($_POST['produit_id'] ?? 0);
        $quantite = (int) ($_POST['quantite'] ?? 0);

        if ($produitId <= 0) {
            throw new InvalidArgumentException('Produit invalide.');
        }

        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                'La quantité doit être supérieure à zéro.'
            );
        }

        $produit = $this->produitRepository->findById($produitId);

        if ($produit === null) {
            throw new InvalidArgumentException('Produit introuvable.');
        }

        $panier = $this->getPanier();

        $panier[] = [
            'produitId' => $produitId,
            'quantite' => $quantite,
        ];

        Session::set('panier', $panier);
    }

    private function traiterCreationCommande(): int
    {
        $clientId = (int) ($_POST['client_id'] ?? 0);
        $modeReglement = (string) ($_POST['mode_reglement'] ?? '');
        $montantVerse = (float) ($_POST['montant_verse'] ?? 0);

        $lignesPanier = $this->getPanier();

        $commandeId = $this->venteService->validerVente(
            $clientId,
            $lignesPanier,
            $modeReglement,
            $montantVerse
        );

        $this->viderPanier();

        return $commandeId;
    }

    private function viderPanier(): void
    {
        Session::set('panier', []);
    }

    private function construireRegistreVentes(): array
    {
        $commandes = $this->commandeRepository->findAll();

        $ventes = [];

        foreach ($commandes as $commande) {
            $ventes[] = [
                'commande' => $commande,
                'client' => $this->clientRepository->findById(
                    $commande->getClientId()
                ),
            ];
        }

        return $ventes;
    }
}

$controller = new POSController();
$controller->afficher();