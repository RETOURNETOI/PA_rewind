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

    
}

?>