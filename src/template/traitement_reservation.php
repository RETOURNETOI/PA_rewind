<?php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once __DIR__.'/../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_PATH . "/connexion");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_PATH . "/mes_hebergement");
    exit;
}

$id_hebergement = (int)($_POST['id_hebergement'] ?? 0);
$id_utilisateur = (int)$_SESSION['user_id']; // Toujours depuis la session
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';
$nb_personnes = (int)($_POST['nb_personnes'] ?? 1);

$controller = new HebergementController();
$dispo = $controller->estDisponible($id_hebergement, $date_debut, $date_fin, $nb_personnes);
// var_dump($dispo);
// ------------------- Le if de réservation -------------------
try {
    // Tente de créer la réservation
    $reserverOk = $controller->reserver($id_hebergement, $id_utilisateur, $date_debut, $date_fin, $nb_personnes);
    if ($reserverOk) {
        // ✅ Redirection vers la liste des réservations
        header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
    } else {
        // ❌ Affiche un message d'erreur
        echo "Échec réservation.";
        print_r($controller->getPDO()->errorInfo());
        exit;
    }
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
// ------------------------------------------------------------
