<?php
// controller/UtilisateurController.php
require_once __DIR__ . '/../bdd/Connexion.php';
require_once __DIR__ . '/../model/Utilisateur.php';

class UtilisateurController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }

    public function ajouter(array $data): bool
    {
        try {
            // Validation basique
            if (empty($data['email']) || empty($data['mot_de_passe'])) {
                throw new Exception("Email et mot de passe sont obligatoires.");
            }

            // Création de l'objet Utilisateur
            $utilisateur = new Utilisateur(
                $data['nom'] ?? null,
                $data['prenom'] ?? null,
                $data['email'],
                $data['mot_de_passe'],
                $data['telephone'] ?? null,
                $data['role'] ?? 'client'
            );

            // Requête SQL
            $sql = "INSERT INTO utilisateur
                    (nom, prenom, email, mot_de_passe, telephone, date_inscription, role)
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :date_inscription, :role)";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':nom' => $utilisateur->getNom(),
                ':prenom' => $utilisateur->getPrenom(),
                ':email' => $utilisateur->getEmail(),
                ':mot_de_passe' => $utilisateur->getMotDePasse(),
                ':telephone' => $utilisateur->getTelephone(),
                ':date_inscription' => $utilisateur->getDateInscription(),
                ':role' => $utilisateur->getRole(),
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
