<?php
// template/traiter_connexion.php
session_start();
require_once '../controller/UtilisateurController.php';

// Récupération des données du formulaire
$email = $_POST['email'] ?? null;
$mot_de_passe = $_POST['mot_de_passe'] ?? null;

// Vérification basique
if (empty($email) || empty($mot_de_passe)) {
    die("Email et mot de passe sont obligatoires.");
}

// Ici, tu devrais avoir une méthode dans UtilisateurController pour vérifier les identifiants
$controller = new UtilisateurController();
$user = $controller->verifierConnexion($email, $mot_de_passe);

if ($user) {
    $_SESSION['user_id'] = $user->getIdUtilisateur();
    $_SESSION['user_role'] = $user->getRole();
    header("Location: gestionuser.php");
    exit;
} else {
    header("Location: connexion.php?erreur=1");
    exit;
}
