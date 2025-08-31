<?php

session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}
require_once __DIR__ . '/../controller/PointArretController.php';
$controller = new PointArretController();

if (!isset($_GET['id'])) {
    header("Location: liste_points_arret.php");
    exit;
}

$result = $controller->delete($_GET['id']);
header("Location: liste_points_arret.php");
exit;
?>