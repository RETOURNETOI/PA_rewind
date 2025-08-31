<?php

class Utilisateur
{
    private int $id_utilisateur;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;
    private ?string $telephone;
    private string $date_inscription;
    private string $role;

    public function __construct(
        ?string $nom = null,
        ?string $prenom = null,
        ?string $email = null,
        ?string $mot_de_passe = null,
        ?string $telephone = null,
        ?string $role = 'client'
    ) {
        if ($nom) $this->nom = $nom;
        if ($prenom) $this->prenom = $prenom;
        if ($email) $this->email = $email;
        if ($mot_de_passe) $this->setMotDePasse($mot_de_passe);
        $this->telephone = $telephone;
        $this->role = $role;
        $this->date_inscription = date('Y-m-d H:i:s');
    }

    public function getIdUtilisateur(): int { return $this->id_utilisateur; }
    public function setIdUtilisateur(int $id): void { $this->id_utilisateur = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getMotDePasse(): string { return $this->mot_de_passe; }
    public function setMotDePasse(string $mot_de_passe): void {
        $this->mot_de_passe = password_hash($mot_de_passe, PASSWORD_BCRYPT);
    }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): void { $this->telephone = $telephone; }

    public function getDateInscription(): string { return $this->date_inscription; }
    public function setDateInscription(string $date_inscription): void { $this->date_inscription = $date_inscription; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void {
        $roles = ['client', 'admin', 'commercial'];
        if (in_array($role, $roles)) {
            $this->role = $role;
        } else {
            throw new Exception("Rôle invalide : $role");
        }
    }
}
