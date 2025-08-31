<?php

class CommandeHebergement
{
    private int $id_commande;
    private int $id_hebergement;
    private string $date_debut;
    private string $date_fin;
    private int $nb_personnes;

    public function __construct(int $id_commande = 0, int $id_hebergement = 0, string $date_debut = '', string $date_fin = '', int $nb_personnes = 1)
    {
        $this->id_commande = $id_commande;
        $this->id_hebergement = $id_hebergement;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->nb_personnes = $nb_personnes;
    }

    public function getIdCommande(): int { return $this->id_commande; }
    public function setIdCommande(int $id): void { $this->id_commande = $id; }

    public function getIdHebergement(): int { return $this->id_hebergement; }
    public function setIdHebergement(int $id): void { $this->id_hebergement = $id; }

    public function getDateDebut(): string { return $this->date_debut; }
    public function setDateDebut(string $date): void { $this->date_debut = $date; }

    public function getDateFin(): string { return $this->date_fin; }
    public function setDateFin(string $date): void { $this->date_fin = $date; }

    public function getNbPersonnes(): int { return $this->nb_personnes; }
    public function setNbPersonnes(int $nb): void { $this->nb_personnes = $nb; }
}