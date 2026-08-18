<?php

declare(strict_types=1);

 class Commande
{
    private int $id;
    private Client $client;
    private DateTimeImmutable $dateCommande;
    private float $montantTotal;
    private float $montantVerse;
    private string $modeReglement;
    private string $statut;

    public function __construct(
        int $id,
        Client $client,
        DateTimeImmutable $dateCommande,
        float $montantTotal,
        float $montantVerse,
        string $modeReglement,
        string $statut
    ) {
        $this->id = $id;
        $this->client = $client;
        $this->dateCommande = $dateCommande;
        $this->montantTotal = $montantTotal;
        $this->montantVerse = $montantVerse;
        $this->modeReglement = $modeReglement;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getDateCommande(): DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function getMontantVerse(): float
    {
        return $this->montantVerse;
    }

    public function getModeReglement(): string
    {
        return $this->modeReglement;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function montantRestantAPayer(): float
    {
        return $this->montantTotal - $this->montantVerse;
    }

    public function estPayeeIntegralement(): bool
    {
        return $this->montantVerse >= $this->montantTotal;
    }
}