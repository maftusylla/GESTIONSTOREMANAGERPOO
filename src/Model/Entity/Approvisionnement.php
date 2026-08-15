<?php

declare(strict_types=1);

 class Approvisionnement
{
    private int $id;
    private int $fournisseurId;
    private string $refBl;
    private ?DateTimeImmutable $dateReception;
    private string $statut;

    public function __construct(
        int $id,
        int $fournisseurId,
        string $refBl,
        ?DateTimeImmutable $dateReception,
        string $statut
    ) {
        $this->id = $id;
        $this->fournisseurId = $fournisseurId;
        $this->refBl = $refBl;
        $this->dateReception = $dateReception;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFournisseurId(): int
    {
        return $this->fournisseurId;
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