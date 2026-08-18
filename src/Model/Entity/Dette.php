<?php
declare(strict_types=1);


 class Dette
{
    private int $id;
    private Commande $commande;
    private float $montantInitial;
    private float $montantRestant;
    private DateTimeImmutable $dateCreation;
    private string $statut;

    public function __construct(
        int $id,
        Commande $commande,
        float $montantInitial,
        float $montantRestant,
        DateTimeImmutable $dateCreation,
        string $statut
    ) {
        $this->id = $id;
        $this->commande = $commande;
        $this->montantInitial = $montantInitial;
        $this->montantRestant = $montantRestant;
        $this->dateCreation = $dateCreation;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function getMontantInitial(): float
    {
        return $this->montantInitial;
    }

    public function getMontantRestant(): float
    {
        return $this->montantRestant;
    }

    public function getDateCreation(): DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function estSoldee(): bool
    {
        return $this->statut === 'SOLDEE';
    }
}