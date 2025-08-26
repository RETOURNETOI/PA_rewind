<?php
session_start();
require_once '../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mes_hebergements.php");
    exit;
}

$id_hebergement = (int)($_POST['id_hebergement'] ?? 0);
$id_utilisateur = (int)$_SESSION['user_id']; // Toujours depuis la session
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';
$nb_personnes = (int)($_POST['nb_personnes'] ?? 1);

$controller = new HebergementController();
$dispo = $controller->estDisponible($id_hebergement, $date_debut, $date_fin, $nb_personnes);
var_dump($dispo);
// ------------------- Le if de réservation -------------------
try {
    // Tente de créer la réservation
    $reserverOk = $controller->reserver($id_hebergement, $id_utilisateur, $date_debut, $date_fin, $nb_personnes);
    if ($controller->reserver($id_hebergement, $id_utilisateur, $date_debut, $date_fin, $nb_personnes)) {
        echo "Réservation réussie !";
    } else {
        echo "Échec réservation.";
        print_r($controller->getPDO()->errorInfo());
    }
    exit;
    if ($reserverOk) {
        // ✅ Réservation réussie, redirection vers mes_reservations
        header("Location: mes_reservations.php");
        exit;
    } else {
        // ❌ Échec réservation : affiche l'erreur PDO pour debug
        echo "Échec de la réservation.<br>";
        print_r($controller->getPDO()->errorInfo());
        exit;
    }
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
// ------------------------------------------------------------
