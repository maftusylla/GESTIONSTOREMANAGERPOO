<?php

declare(strict_types=1);

final class Paiement
{
    private int $id;
    private int $detteId;
    private float $montant;
    private string $modePaiement;
    private DateTimeImmutable $datePaiement;

    public function __construct(
        int $id,
        int $detteId,
        float $montant,
        string $modePaiement,
        DateTimeImmutable $datePaiement
    ) {
        $this->id = $id;
        $this->detteId = $detteId;
        $this->montant = $montant;
        $this->modePaiement = $modePaiement;
        $this->datePaiement = $datePaiement;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDetteId(): int
    {
        return $this->detteId;
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