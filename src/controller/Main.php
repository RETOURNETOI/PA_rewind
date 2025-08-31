<?php
Class Main{
    public function main(){
        
    require_once "../bdd/Connexion.php";
    require_once "UtilisateurController.php";

    $connexion = new Connexion();
    $pdo = $connexion->getPDO();
    $uc = new UtilisateurController($pdo);
    
    
    return $uc;
    
    }


    public function createUser(){
        $connexion = new Connexion();
        $pdo = $connexion->getPDO();
        $uc = new UtilisateurController($pdo);

        $utilisateur = new Utilisateur(
            "Martin", 
            "Sophie", 
            "sophie.martin@example.com", 
            "motdepasse123", 
            "0601020304", 
            "client"
        );

        $success = $uc->ajouter($utilisateur);

        if ($success) {
            echo "✅ Utilisateur créé avec succès !\n";
            echo "Nom : ".$utilisateur->getNom()."\n";
            echo "Prénom : ".$utilisateur->getPrenom()."\n";
            echo "Email : ".$utilisateur->getEmail()."\n";
        } else {
            echo "❌ Erreur lors de la création de l'utilisateur.\n";
        }
    }
}

?>