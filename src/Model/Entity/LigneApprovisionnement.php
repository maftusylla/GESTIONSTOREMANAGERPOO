<?php

declare(strict_types=1);

 class LigneApprovisionnement
{
    private int $id;
    private Approvisionnement $approvisionnement;
    private Produit $produit;
    private int $quantiteCommandee;
    private int $quantiteLivree;
    private float $coutUnitaire;

    public function __construct(
        int $id,
        Approvisionnement  $approvisionnement,
        Produit $produit,
        int $quantiteCommandee,
        int $quantiteLivree,
        float $coutUnitaire
    ) {
        $this->id = $id;
        $this->approvisionnement = $approvisionnement;
        $this->produit = $produit;
        $this->quantiteCommandee = $quantiteCommandee;
        $this->quantiteLivree = $quantiteLivree;
        $this->coutUnitaire = $coutUnitaire;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getApprovisionnement(): Approvisionnement
    {
        return $this->approvisionnement;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
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