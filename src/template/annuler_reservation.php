<?php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once __DIR__ . '/../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$id_commande = $_POST['id_commande'] ?? null;

if (empty($id_commande)) {
    $_SESSION['flash'] = "ID de réservation non spécifié.";
    header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
}

$controller = new HebergementController();

$resultat = $controller->annulerReservation((int)$id_commande, $_SESSION['user_id']);

if ($resultat) {
    $_SESSION['flash'] = "Réservation annulée avec succès !";
} else {
    $_SESSION['flash'] = "Erreur lors de l'annulation de la réservation.";
}

header("Location: ". BASE_PATH . "/mes_reservation");
exit;
