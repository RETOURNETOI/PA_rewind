<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

session_start();

$userName = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

session_unset();
session_destroy();

session_start();

$_SESSION['deco_message'] = "Au revoir " . $userName . " ! Vous avez été déconnecté avec succès.";

header('Location: ' . BASE_PATH . '/');
exit;
?>