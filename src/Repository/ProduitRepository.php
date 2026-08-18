<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Produit.php';

class ProduitRepository
{
   

    public static function findById(int $id): ?Produit
    {
        $ligne = Database::executeQuery(
            'SELECT id, nom, prix_vente, quantite_stock FROM produit WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        return new Produit(
            (int) $ligne['id'],
            (string) $ligne['nom'],
            (float) $ligne['prix_vente'],
            (int) $ligne['quantite_stock']
        );
    }

    public static function findAll(): array
    {
        $lignes = Database::executeQuery(
            'SELECT id, nom, prix_vente, quantite_stock FROM produit ORDER BY nom',
            [],
            false
        );

        $produits = [];

        foreach ($lignes as $ligne) {
            $produits[] = new Produit(
                (int) $ligne['id'],
                (string) $ligne['nom'],
                (float) $ligne['prix_vente'],
                (int) $ligne['quantite_stock']
            );
        }

        return $produits;
    }

    public static function decrementerStock(int $produitId, int $quantite): int
    {
        return Database::executeUpdate(
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