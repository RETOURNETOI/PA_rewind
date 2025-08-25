<?php
Class Main{
    public function main(){

        
    require_once "../bdd/Connexion.php";
    require_once "UtilisateurController.php";

    // Créer un objet connexion
    $connexion = new Connexion();
    
    // Récupérer l'objet PDO
    $pdo = $connexion->getPDO();
    
    // Exemple avec un controller
    $uc = new UtilisateurController($pdo);
    
    
    return $uc;
    
    }


    public function createUser(){
        // 1️⃣ Connexion à la base Kayak_Trip
    $connexion = new Connexion();
    $pdo = $connexion->getPDO();

    // 2️⃣ Création du controller
    $uc = new UtilisateurController($pdo);

    // 3️⃣ Création d'un nouvel utilisateur
    $utilisateur = new Utilisateur(
        "Martin", 
        "Sophie", 
        "sophie.martin@example.com", 
        "motdepasse123", 
        "0601020304", 
        "client"
    );

    // 4️⃣ Ajouter l'utilisateur
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