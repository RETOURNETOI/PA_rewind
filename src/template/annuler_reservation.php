<?php
// annuler_reservation.php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once '../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$id_commande = $_GET['id_commande'] ?? null;

if (empty($id_commande)) {
    $_SESSION['message'] = "ID de réservation non spécifié.";
    header("Location: mes_reservations.php");
    exit;
}

$controller = new HebergementController();
$resultat = $controller->annulerReservation($id_commande);

if ($resultat) {
    $_SESSION['message'] = "Réservation annulée avec succès !";
} else {
    $_SESSION['message'] = "Erreur lors de l'annulation de la réservation.";
}

header("Location: mes_reservations.php");
exit;
?>
