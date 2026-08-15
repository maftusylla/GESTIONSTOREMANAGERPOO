<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Repository/ClientRepository.php';
require_once dirname(__DIR__) . '/Repository/ProduitRepository.php';
require_once dirname(__DIR__) . '/Repository/CommandeRepository.php';

class VenteService
{
    private const MODES_REGLEMENT_VALIDES = ['Especes', 'Wave', 'Orange Money'];

    private Database $db;
    private ClientRepository $clientRepository;
    private ProduitRepository $produitRepository;
    private CommandeRepository $commandeRepository;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->clientRepository = new ClientRepository();
        $this->produitRepository = new ProduitRepository();
        $this->commandeRepository = new CommandeRepository();
    }

   
    public function validerVente(
        int $clientId,
        array $lignesPanier,
        string $modeReglement,
        float $montantVerse
    ): int {
        if ($lignesPanier === []) {
            throw new InvalidArgumentException('Le panier ne peut pas être vide.');
        }

        if (!in_array($modeReglement, self::MODES_REGLEMENT_VALIDES, true)) {
            throw new InvalidArgumentException('Mode de règlement invalide : ' . $modeReglement);
        }

        if ($montantVerse < 0) {
            throw new InvalidArgumentException('Le montant versé ne peut pas être négatif.');
        }

        $client = $this->clientRepository->findById($clientId);
        if ($client === null) {
            throw new InvalidArgumentException('Client introuvable (id ' . $clientId . ').');
        }

        return $this->db->transaction(function () use ($client, $lignesPanier, $modeReglement, $montantVerse): int {


            $lignesAEnregistrer = [];
            $montantTotal = 0.0;

            foreach ($lignesPanier as $ligne) {
                $produit = $this->produitRepository->findById((int) $ligne['produitId']);
                if ($produit === null) {
                    throw new RuntimeException('Produit introuvable (id ' . $ligne['produitId'] . ').');
                }

                $quantite = (int) $ligne['quantite'];
                if ($quantite <= 0) {
                    throw new InvalidArgumentException('Quantité invalide pour ' . $produit->getNom() . '.');
                }

                $sousTotal = $produit->getPrixVente() * $quantite;
                $montantTotal += $sousTotal;

                $lignesAEnregistrer[] = [
                    'produitId' => $produit->getId(),
                    'quantite' => $quantite,
                    'prixUnitaire' => $produit->getPrixVente(),
                ];
            }


            if ($montantVerse > $montantTotal) {
                throw new InvalidArgumentException('Le montant versé ne peut pas dépasser le montant total.');
            }

            $statut = $montantVerse >= $montantTotal ? 'COMPTANT' : 'CREDIT';

            if ($statut === 'CREDIT') {
                $montantACredit = $montantTotal - $montantVerse;
                $encoursActuel = $this->clientRepository->calculerEncoursDettes($client->getId());

                if (!$client->peutObtenirCredit($montantACredit, $encoursActuel)) {
                    throw new RuntimeException(
                        'Limite de crédit dépassée pour '
                        . $client->getPrenom() . ' ' . $client->getNom() . '.'
                    );
                }
            }

            $commandeId = $this->commandeRepository->creerCommande(
                $client->getId(),
                $montantTotal,
                $montantVerse,
                $modeReglement,
                $statut
            );


            foreach ($lignesAEnregistrer as $ligne) {
                $this->commandeRepository->ajouterLigneCommande(
                    $commandeId,
                    $ligne['produitId'],
                    $ligne['quantite'],
                    $ligne['prixUnitaire']
                );

                $lignesAffectees = $this->produitRepository->decrementerStock(
                    $ligne['produitId'],
                    $ligne['quantite']
                );

                if ($lignesAffectees === 0) {
                    throw new RuntimeException(
                        'Stock insuffisant pour le produit id ' . $ligne['produitId'] . '.'
                    );
                }
            }


            if ($statut === 'CREDIT') {
                $montantACredit = $montantTotal - $montantVerse;

                $this->db->executeUpdate(
                    'INSERT INTO dette (commande_id, montant_initial, montant_restant, statut)
                     VALUES (:commandeId, :montantInitial, :montantRestant, :statut)',
                    [
                        'commandeId' => $commandeId,
                        'montantInitial' => $montantACredit,
                        'montantRestant' => $montantACredit,
                        'statut' => 'NON SOLDEE',
                    ]
                );
            }

            return $commandeId;
        });
    }
}