<?php

class Commande
{
    private int $id_commande;
    private int $id_utilisateur;
    private string $date_commande;
    private string $statut;

    public function __construct(int $id_utilisateur = 0, string $statut = 'en_attente')
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->statut = $statut;
        $this->date_commande = date('Y-m-d H:i:s');
    }

    public function getIdCommande(): int { return $this->id_commande; }
    public function setIdCommande(int $id): void { $this->id_commande = $id; }

    public function getIdUtilisateur(): int { return $this->id_utilisateur; }
    public function setIdUtilisateur(int $id): void { $this->id_utilisateur = $id; }

    public function getDateCommande(): string { return $this->date_commande; }
    public function setDateCommande(string $date): void { $this->date_commande = $date; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
}
