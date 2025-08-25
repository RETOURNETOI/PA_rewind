<?php

class Hebergement
{
    private int $id_hebergement;
    private int $id_point;
    private string $nom;
    private string $type;
    private int $capacite;
    private float $prix_nuit;
    private ?string $description;

    public function __construct(
        int $id_point = 0,
        string $nom = '',
        string $type = 'hotel',
        int $capacite = 1,
        float $prix_nuit = 0.0,
        ?string $description = null
    ) {
        $this->id_point = $id_point;
        $this->nom = $nom;
        $this->type = $type;
        $this->capacite = $capacite;
        $this->prix_nuit = $prix_nuit;
        $this->description = $description;
    }

    public function getIdHebergement(): int { return $this->id_hebergement; }
    public function setIdHebergement(int $id): void { $this->id_hebergement = $id; }

    public function getIdPoint(): int { return $this->id_point; }
    public function setIdPoint(int $id): void { $this->id_point = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    public function getCapacite(): int { return $this->capacite; }
    public function setCapacite(int $cap): void { $this->capacite = $cap; }

    public function getPrixNuit(): float { return $this->prix_nuit; }
    public function setPrixNuit(float $prix): void { $this->prix_nuit = $prix; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): void { $this->description = $desc; }
}
