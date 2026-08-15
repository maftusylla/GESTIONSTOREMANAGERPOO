<?php


declare(strict_types=1);

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Model/Entity/Client.php';


class ClientRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Client
    {
        $ligne = $this->db->executeQuery(
            'SELECT id, prenom, nom, telephone, email, limite_credit FROM client WHERE id = :id',
            ['id' => $id]
        );

        if ($ligne === []) {
            return null;
        }

        return new Client(
            (int) $ligne['id'],
            (string) $ligne['prenom'],
            (string) $ligne['nom'],
            (string) $ligne['telephone'],
            $ligne['email'] !== null ? (string) $ligne['email'] : null,
            (float) $ligne['limite_credit']
        );
    }

    public function findAll(): array
    {
        $lignes = $this->db->executeQuery(
            'SELECT id, prenom, nom, telephone, email, limite_credit FROM client ORDER BY nom, prenom',
            [],
            false
        );

        $clients = [];

        foreach ($lignes as $ligne) {
            $clients[] = new Client(
                (int) $ligne['id'],
                (string) $ligne['prenom'],
                (string) $ligne['nom'],
                (string) $ligne['telephone'],
                $ligne['email'] !== null ? (string) $ligne['email'] : null,
                (float) $ligne['limite_credit']
            );
        }

        return $clients;
    }

    public function calculerEncoursDettes(int $clientId): float
    {
        $ligne = $this->db->executeQuery(
            'SELECT COALESCE(SUM(d.montant_restant), 0) AS encours
             FROM dette d
             INNER JOIN commande c ON c.id = d.commande_id
             WHERE c.client_id = :clientId',
            ['clientId' => $clientId]
        );

        return (float) ($ligne['encours'] ?? 0);
    }

    
}
