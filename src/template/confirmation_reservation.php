<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation réservation</title>
</head>
<body>
    <h1>✅ Votre réservation a bien été enregistrée !</h1>
    <p><a href="mes_reservations.php">Voir mes réservations</a></p>
    <p><a href="mes_hebergements.php">Retour aux hébergements</a></p>
</body>
</html>
