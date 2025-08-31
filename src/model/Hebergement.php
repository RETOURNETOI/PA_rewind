<?php
class Hebergement {
    private int $id_hebergement;
    private string $nom;
    private string $type;
    private int $capacite;
    private float $prix;
    private int $id_point;

    public function __construct(string $nom, string $type, int $capacite, float $prix, int $id_point) {
        $this->nom = $nom;
        $this->type = $type;
        $this->capacite = $capacite;
        $this->prix = $prix;
        $this->id_point = $id_point;
    }

    public function getIdHebergement(): int { return $this->id_hebergement; }
    public function setIdHebergement(int $id): void { $this->id_hebergement = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getType(): string { return $this->type; }
    public function setType(string $t): void { $this->type = $t; }

    public function getCapacite(): int { return $this->capacite; }
    public function setCapacite(int $c): void { $this->capacite = $c; }

    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $p): void { $this->prix = $p; }

    public function getIdPoint(): int { return $this->id_point; }
    public function setIdPoint(int $idp): void { $this->id_point = $idp; }
}