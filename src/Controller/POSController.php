<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Repository/ClientRepository.php';
require_once dirname(__DIR__) . '/Repository/ProduitRepository.php';
require_once dirname(__DIR__) . '/Repository/CommandeRepository.php';
require_once dirname(__DIR__) . '/Repository/DetteRepository.php';
require_once dirname(__DIR__) . '/Service/VenteService.php';

class POSController
{
    private ClientRepository $clientRepository;
    private ProduitRepository $produitRepository;
    private CommandeRepository $commandeRepository;
    private DetteRepository $detteRepository;
    private VenteService $venteService;

    public function __construct()
    {
        Session::init();

        $this->clientRepository = new ClientRepository();
        $this->produitRepository = new ProduitRepository();
        $this->commandeRepository = new CommandeRepository();
        $this->detteRepository = new DetteRepository();
        $this->venteService = new VenteService();

        $this->initPanier();
    }

    public function afficherVue(): void
    {
        $erreur = Session::getFlash('erreur');
        $succes = Session::getFlash('succes');

        $panier = $this->getPanier();
        $clients = $this->clientRepository->findAll();
        $produits = $this->produitRepository->findAll();
        $ventes = $this->construireRegistreVentes();
        $clientIdSelectionne = (int) Session::get('client_id_selectionne', 0);

        $caEncaisseNet = $this->commandeRepository->sommeTotalMontantVerse()
            + $this->detteRepository->sommeTotalPaiements();
        $encoursClientTotal = $this->detteRepository->sommeEncoursActif();

        require dirname(__DIR__) . '/Views/pos/index.php';
    }

    public function ajouterAuPanier(): void
    {
        try {
            $produitId = (int) ($_POST['produit_id'] ?? 0);
            $quantite = (int) ($_POST['quantite'] ?? 0);

            if ($produitId <= 0) {
                throw new InvalidArgumentException('Produit invalide.');
            }
            if ($quantite <= 0) {
                throw new InvalidArgumentException('La quantité doit être supérieure à zéro.');
            }

            $produit = $this->produitRepository->findById($produitId);
            if ($produit === null) {
                throw new InvalidArgumentException('Produit introuvable.');
            }
            $clientId = (int) ($_POST['client_id'] ?? 0);
            if ($clientId > 0) {
                Session::set('client_id_selectionne', $clientId);
            }

            $panier = $this->getPanier();
            $panier[] = ['produitId' => $produitId, 'quantite' => $quantite];
            Session::set('panier', $panier);
        } catch (InvalidArgumentException|RuntimeException $e) {
            Session::setFlash('erreur', $e->getMessage());
        }

        header('Location: /');
        exit;
    }

    public function creerVente(): void
    {
        try {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            $modeReglement = (string) ($_POST['mode_reglement'] ?? '');
            $montantVerse = (float) ($_POST['montant_verse'] ?? 0);

            $commandeId = $this->venteService->validerVente(
                $clientId,
                $this->getPanier(),
                $modeReglement,
                $montantVerse
            );

            $this->viderPanier();
            Session::unset('client_id_selectionne');
            Session::setFlash('succes', 'Vente enregistrée (commande #' . $commandeId . ').');
        } catch (InvalidArgumentException|RuntimeException $e) {
            Session::setFlash('erreur', $e->getMessage());
        }

        header('Location: /');
        exit;
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
                'client' => $this->clientRepository->findById($commande->getClient()),
                'articles' => $this->commandeRepository->findLignesByCommande($commande->getId()),
            ];
        }

        return $ventes;
    }
}