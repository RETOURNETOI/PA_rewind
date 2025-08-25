<?php

class CommandeService
{
    private int $id_commande;
    private int $id_service;
    private int $quantite;

    public function __construct(int $id_commande = 0, int $id_service = 0, int $quantite = 1)
    {
        $this->id_commande = $id_commande;
        $this->id_service = $id_service;
        $this->quantite = $quantite;
    }

    public function getIdCommande(): int { return $this->id_commande; }
    public function setIdCommande(int $id): void { $this->id_commande = $id; }

    public function getIdService(): int { return $this->id_service; }
    public function setIdService(int $id): void { $this->id_service = $id; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $q): void { $this->quantite = $q; }
}
