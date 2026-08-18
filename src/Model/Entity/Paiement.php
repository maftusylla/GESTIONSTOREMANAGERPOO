<?php

declare(strict_types=1);

 class Paiement
{
    private int $id;
    private Dette $dette;
    private float $montant;
    private string $modePaiement;
    private DateTimeImmutable $datePaiement;

    public function __construct(
        int $id,
        Dette $dette,
        float $montant,
        string $modePaiement,
        DateTimeImmutable $datePaiement
    ) {
        $this->id = $id;
        $this->dette = $dette;
        $this->montant = $montant;
        $this->modePaiement = $modePaiement;
        $this->datePaiement = $datePaiement;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDette(): Dette
    {
        return $this->dette;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function getDatePaiement(): DateTimeImmutable
    {
        return $this->datePaiement;
    }
}