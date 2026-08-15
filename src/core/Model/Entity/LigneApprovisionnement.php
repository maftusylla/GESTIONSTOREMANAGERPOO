<?php

declare(strict_types=1);

final class LigneApprovisionnement
{
    private int $id;
    private int $approvisionnementId;
    private int $produitId;
    private int $quantiteCommandee;
    private int $quantiteLivree;
    private float $coutUnitaire;

    public function __construct(
        int $id,
        int $approvisionnementId,
        int $produitId,
        int $quantiteCommandee,
        int $quantiteLivree,
        float $coutUnitaire
    ) {
        $this->id = $id;
        $this->approvisionnementId = $approvisionnementId;
        $this->produitId = $produitId;
        $this->quantiteCommandee = $quantiteCommandee;
        $this->quantiteLivree = $quantiteLivree;
        $this->coutUnitaire = $coutUnitaire;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getApprovisionnementId(): int
    {
        return $this->approvisionnementId;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantiteCommandee(): int
    {
        return $this->quantiteCommandee;
    }

    public function getQuantiteLivree(): int
    {
        return $this->quantiteLivree;
    }

    public function getCoutUnitaire(): float
    {
        return $this->coutUnitaire;
    }

    public function coutTotal(): float
    {
        return $this->quantiteLivree * $this->coutUnitaire;
    }
}