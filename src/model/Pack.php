<?php
class Pack {
    private int $id_pack;
    private string $nom;
    private string $description;
    private float $prix;

    public function __construct(string $nom, string $description, float $prix) {
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
    }

    public function getIdPack(): int { return $this->id_pack; }
    public function setIdPack(int $id): void { $this->id_pack = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $d): void { $this->description = $d; }

    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $p): void { $this->prix = $p; }
}