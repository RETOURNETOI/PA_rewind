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
$id_utilisateur = (int)$_SESSION['user_id'];
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';
$nb_personnes = (int)($_POST['nb_personnes'] ?? 1);

$controller = new HebergementController();
$dispo = $controller->estDisponible($id_hebergement, $date_debut, $date_fin, $nb_personnes);
try {
    $reserverOk = $controller->reserver($id_hebergement, $id_utilisateur, $date_debut, $date_fin, $nb_personnes);
    if ($reserverOk) {
        header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
    } else {
        echo "Échec réservation.";
        print_r($controller->getPDO()->errorInfo());
        exit;
    }
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
?>