<?php
declare(strict_types=1);


class Client
{
    private int $id;
    private string $prenom;
    private string $nom;
    private string $telephone;
    private ?string $email;
    private float $limiteCredit;

    public function __construct(
        int $id,
        string $prenom,
        string $nom,
        string $telephone,
        ?string $email,
        float $limiteCredit
    ) {
        $this->id = $id;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->limiteCredit = $limiteCredit;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLimiteCredit(): float
    {
        return $this->limiteCredit;
    }

    public function peutObtenirCredit(
        float $montantSupplementaire,
        float $encoursDettesActuel
    ): bool {
        return ($encoursDettesActuel + $montantSupplementaire) <= $this->limiteCredit;
    }
}