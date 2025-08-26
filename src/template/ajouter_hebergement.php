<?php
session_start();
require_once '../controller/HebergementController.php';

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new HebergementController();
    $ok = $controller->ajouter($_POST);

    if ($ok) {
        header("Location: mes_hebergements.php");
        exit;
    } else {
        $message = "❌ Erreur lors de l'ajout de l'hébergement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Hébergement</title>
</head>
<body>
    <h1>Ajouter un Hébergement</h1>

    <?php if (isset($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form method="post">
        <label for="id_point">ID Point :</label>
        <input type="number" name="id_point" id="id_point" required><br><br>

        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required><br><br>

        <label for="type">Type :</label>
        <input type="text" name="type" id="type" required><br><br>

        <label for="capacite">Capacité :</label>
        <input type="number" name="capacite" id="capacite" required><br><br>

        <label for="prix_nuit">Prix par nuit (€) :</label>
        <input type="number" name="prix_nuit" id="prix_nuit" step="0.01" required><br><br>

        <label for="description">Description :</label>
        <textarea name="description" id="description"></textarea><br><br>

        <button type="submit">Ajouter</button>
    </form>

    <p><a href="<?= BASE_PATH ?>/choisir_hebergement">Retour à mes hébergements</a></p>
</body>
</html>
