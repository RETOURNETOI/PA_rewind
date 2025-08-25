<?php

class Service
{
    private int $id_service;
    private string $nom;
    private ?string $description;
    private float $prix;

    public function __construct(string $nom = '', ?string $description = null, float $prix = 0.0)
    {
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
    }

    public function getIdService(): int { return $this->id_service; }
    public function setIdService(int $id): void { $this->id_service = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): void { $this->description = $desc; }

    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $prix): void { $this->prix = $prix; }
}
