<?php
// deco.php - Gestion de la déconnexion

// Définir BASE_PATH si pas déjà défini
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

// Démarrer la session
session_start();

// Sauvegarder le nom de l'utilisateur pour le message d'au revoir
$userName = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

// Détruire toutes les données de session
session_unset();
session_destroy();

// Redémarrer une nouvelle session pour le message
session_start();

// Message de déconnexion
$_SESSION['deco_message'] = "Au revoir " . $userName . " ! Vous avez été déconnecté avec succès.";

// Redirection vers la page d'accueil
header('Location: ' . BASE_PATH . '/');
exit;
?>