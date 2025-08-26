<?php
// template/traiter_connexion.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


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
// var_dump($user);
// exit;
if ($user) {
    $_SESSION['user_id'] = $user->getIdUtilisateur();
    $_SESSION['user_role'] = $user->getRole();
    $_SESSION['user_nom'] = $user->getNom();
    $_SESSION['user_email'] = $user->getEmail();
    echo "Redirection vers le tableau de bord...";
    // var_dump($_SESSION);
    // exit;
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: gestionuser.php");
            break;
        case 'client':
            header("Location: user_dashboard.php");
            break;
        case 'commercial':
            header("Location: commercial_dashboard.php");
            break;
        default:
            header("Location: user_dashboard.php");
            break;
    }
    exit;
} else {
    header("Location: connexion.php?erreur=identifiants_invalides");
    exit;
}

