<?php

declare(strict_types=1);

 class LigneCommande
{
    private int $id;
    private int $commandeId;
    private int $produitId;
    private int $quantite;
    private float $prixUnitaire;

    public function __construct(
        int $id,
        int $commandeId,
        int $produitId,
        int $quantite,
        float $prixUnitaire
    ) {
        $this->id = $id;
        $this->commandeId = $commandeId;
        $this->produitId = $produitId;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCommandeId(): int
    {
        return $this->commandeId;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function sousTotal(): float
    {
        return $this->quantite * $this->prixUnitaire;
    }
}