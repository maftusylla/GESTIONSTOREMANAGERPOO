<?php

declare(strict_types=1);

 class Commande
{
    private int $id;
    private int $clientId;
    private DateTimeImmutable $dateCommande;
    private float $montantTotal;
    private float $montantVerse;
    private string $modeReglement;
    private string $statut;

    public function __construct(
        int $id,
        int $clientId,
        DateTimeImmutable $dateCommande,
        float $montantTotal,
        float $montantVerse,
        string $modeReglement,
        string $statut
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
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

    public function getClientId(): int
    {
        return $this->clientId;
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