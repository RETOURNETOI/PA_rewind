<?php
class PointArret {
    private int $id_point;
    private string $nom;
    private string $description;

    public function __construct(string $nom, string $description) {
        $this->nom = $nom;
        $this->description = $description;
    }

    public function getIdPoint(): int { return $this->id_point; }
    public function setIdPoint(int $id): void { $this->id_point = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $d): void { $this->description = $d; }
}