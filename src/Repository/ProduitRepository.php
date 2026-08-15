<?php

declare(strict_types=1);

 
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Produit.php';

class ProduitRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Produit
    {
        $ligne = $this->db->executeQuery(
            'SELECT id, nom, prix_vente, quantite_stock FROM produit WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        // Construction de l'objet Produit à partir du tableau $ligne
        return new Produit(
            (int) $ligne['id'],
            (string) $ligne['nom'],
            (float) $ligne['prix_vente'],
            (int) $ligne['quantite_stock']
        );
    }

    public function findAll(): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT id, nom, prix_vente, quantite_stock FROM produit ORDER BY nom',
            [],
            false
        );

        $produits = [];

        foreach ($lignes as $ligne) {
            // Même bloc de construction que dans findById() ci-dessus
            $produits[] = new Produit(
                (int) $ligne['id'],
                (string) $ligne['nom'],
                (float) $ligne['prix_vente'],
                (int) $ligne['quantite_stock']
            );
        }

        return $produits;
    }

    public function decrementerStock(int $produitId, int $quantite): int
    {
        return $this->db->executeUpdate(
            'UPDATE produit
             SET quantite_stock = quantite_stock - :quantiteADecrementer
             WHERE id = :id AND quantite_stock >= :quantiteMinimale',
            [
                'quantiteADecrementer' => $quantite,
                'id' => $produitId,
                'quantiteMinimale' => $quantite,
            ]
        );
    }

    
}
