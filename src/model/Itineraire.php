<?php

class Itineraire
{
    private int $id_itineraire;
    private int $id_utilisateur;
    private string $nom;

    // ======= CONSTRUCTEUR =======
    public function __construct(int $id_utilisateur = 0, string $nom = '')
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom = $nom;
    }

    // ======= GETTERS & SETTERS =======
    public function getIdItineraire(): int { return $this->id_itineraire; }
    public function setIdItineraire(int $id): void { $this->id_itineraire = $id; }

    public function getIdUtilisateur(): int { return $this->id_utilisateur; }
    public function setIdUtilisateur(int $id): void { $this->id_utilisateur = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }
}
