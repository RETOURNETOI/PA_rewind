<?php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id_hebergement'])) {
    $_SESSION['flash'] = "Réservation réussie !";
    header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un Hébergement</title>
</head>
<body>
    <h1>Réserver un Hébergement</h1>
    <form action="<?= BASE_PATH ?>/traitement_reservation" method="post">
    <input type="hidden" name="id_hebergement" value="<?= htmlspecialchars($_GET['id_hebergement']) ?>">
    
    <label>Date de début:
        <input type="date" name="date_debut" required>
    </label>
    
    <label>Date de fin:
        <input type="date" name="date_fin" required>
    </label>
    
    <label>Nombre de personnes:
        <input type="number" name="nb_personnes" min="1" value="1" required>
    </label>
    
    <button type="submit">Réserver</button>
</form>
</body>
</html>
