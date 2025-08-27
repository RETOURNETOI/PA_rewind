<?php
// annuler_reservation.php
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

// On récupère l'ID de la réservation depuis POST
$id_commande = $_POST['id_commande'] ?? null;

if (empty($id_commande)) {
    $_SESSION['flash'] = "ID de réservation non spécifié.";
    header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
}

$controller = new HebergementController();

// Annulation sécurisée : on passe l'id utilisateur
$resultat = $controller->annulerReservation((int)$id_commande, $_SESSION['user_id']);

if ($resultat) {
    $_SESSION['flash'] = "Réservation annulée avec succès !";
} else {
    $_SESSION['flash'] = "Erreur lors de l'annulation de la réservation.";
}

// Redirection vers la page des réservations
header("Location: ". BASE_PATH . "/mes_reservation");
exit;
