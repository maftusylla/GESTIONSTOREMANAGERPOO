<?php

declare(strict_types=1);

 class Approvisionnement
{
    private int $id;
    private Fournisseur $fournisseur;
    private string $refBl;
    private ?DateTimeImmutable $dateReception;
    private string $statut;

    public function __construct(
        int $id,
        Fournisseur $fournisseur,
        string $refBl,
        ?DateTimeImmutable $dateReception,
        string $statut
    ) {
        $this->id = $id;
        $this->fournisseur = $fournisseur;
        $this->refBl = $refBl;
        $this->dateReception = $dateReception;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFournisseur(): Fournisseur
    {
        return $this->fournisseur;
    }

    public function getRefBl(): string
    {
        return $this->refBl;
    }

    public function getDateReception(): ?DateTimeImmutable
    {
        return $this->dateReception;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function estRecu(): bool
    {
        return $this->statut === 'REÇU';
    }
}