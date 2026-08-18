<?php
declare(strict_types=1);


class Fournisseur
{
    private int $id;
    private string $nom;
    private ?string $telephone;
    private ?string $adresse;
    private ?string $email;

    public function __construct(
        int $id,
        string $nom,
        ?string $telephone,
        ?string $adresse,
        ?string $email
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}