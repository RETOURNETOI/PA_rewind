<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id_hebergement'])) {
    header("Location: mes_reservation.php");
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
    <form action="traitement_reservation.php" method="post">
        <input type="hidden" name="id_hebergement" value="<?= htmlspecialchars($_GET['id_hebergement']) ?>">
        
        <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" required>
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" required>
        <label for="nb_personnes">Nombre de personnes:</label>
        <input type="number" id="nb_personnes" name="nb_personnes" min="1" required>
        <button type="submit">Réserver</button>
    </form>
</body>
</html>
