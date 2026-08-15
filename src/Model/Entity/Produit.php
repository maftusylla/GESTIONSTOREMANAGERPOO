<?php

declare(strict_types=1);

 class Produit
{
    private int $id;
    private string $nom;
    private float $prixVente;
    private int $quantiteStock;

    public function __construct(
        int $id,
        string $nom,
        float $prixVente,
        int $quantiteStock
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prixVente = $prixVente;
        $this->quantiteStock = $quantiteStock;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrixVente(): float
    {
        return $this->prixVente;
    }

    public function getQuantiteStock(): int
    {
        return $this->quantiteStock;
    }

    public function estEnRuptureDeStock(): bool
    {
        return $this->quantiteStock === 0;
    }
}