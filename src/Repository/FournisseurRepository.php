<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Fournisseur.php';

class FournisseurRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Fournisseur
    {
        $ligne = $this->db->executeQuery(
            'SELECT id, nom, telephone, adresse, email FROM fournisseur WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        return new Fournisseur(
            (int) $ligne['id'],
            (string) $ligne['nom'],
            $ligne['telephone'] !== null ? (string) $ligne['telephone'] : null,
            $ligne['adresse'] !== null ? (string) $ligne['adresse'] : null,
            $ligne['email'] !== null ? (string) $ligne['email'] : null
        );
    }

    public function findAll(): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT id, nom, telephone, adresse, email FROM fournisseur ORDER BY nom',
            [],
            false
        );

        $fournisseurs = [];

        foreach ($lignes as $ligne) {
            $fournisseurs[] = new Fournisseur(
                (int) $ligne['id'],
                (string) $ligne['nom'],
                $ligne['telephone'] !== null ? (string) $ligne['telephone'] : null,
                $ligne['adresse'] !== null ? (string) $ligne['adresse'] : null,
                $ligne['email'] !== null ? (string) $ligne['email'] : null
            );
        }

        return $fournisseurs;
    }

    
}
