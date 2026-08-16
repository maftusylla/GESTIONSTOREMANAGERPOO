<?php

declare(strict_types=1);

require_once __DIR__ . '/Session.php';
require_once dirname(__DIR__) . '/Controller/POSController.php';
require_once dirname(__DIR__) . '/Controller/DetteController.php';

class Router
{
    public function dispatch(): void
    {
        Session::init();

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = rtrim((string) $uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        switch ($uri) {
            case '/':
                (new POSController())->afficherVue();
                break;

            case '/panier':
                (new POSController())->ajouterAuPanier();
                break;

            case '/ventes':
                (new POSController())->creerVente();
                break;

            case '/dettes':
                (new DetteController())->afficherVue();
                break;

            case '/dettes/payer':
                (new DetteController())->payerDette();
                break;

            default:
                http_response_code(404);
                echo 'Page introuvable';
                break;
        }
    }
}