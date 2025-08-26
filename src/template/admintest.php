<?php
// admin_test.php

// --- Connexion à la BDD ---
$dsn = "mysql:host=localhost;dbname=kayak_trip;charset=utf8";
$user = "root";
$pass = "";
try {
    $db = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// --- Inclusion des classes & contrôleurs ---
require_once "../model/Pack.php";
require_once "../controller/PackController.php";
require_once "../model/Service.php";
require_once "../controller/ServiceController.php";
require_once "../model/Hebergement.php";
require_once "../controller/HebergementController.php";
require_once "../model/PointArret.php";
require_once "../controller/PointArretController.php";
require_once "../model/PackEtape.php";
require_once "../controller/PackEtapeController.php";

// Instanciation des contrôleurs
$packCtrl = new PackController($db);
$serviceCtrl = new ServiceController($db);
$hebergementCtrl = new HebergementController($db);
$pointCtrl = new PointArretController($db);
$packEtapeCtrl = new PackEtapeController($db);

// --- Gestion des actions ---
$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === "add_pack") {
        $p = new Pack($_POST['nom'], $_POST['description'], $_POST['prix']);
        if ($packCtrl->ajouter($p)) $msg = "Pack ajouté avec succès !";
    }
    if (isset($_POST['action']) && $_POST['action'] === "delete_pack") {
        if ($packCtrl->delete($_POST['id_pack'])) $msg = "Pack supprimé !";
    }
    if (isset($_POST['action']) && $_POST['action'] === "add_service") {
        $s = new Service($_POST['nom'], $_POST['description'], $_POST['prix']);
        if ($serviceCtrl->ajouter($s)) $msg = "Service ajouté avec succès !";
    }
    if (isset($_POST['action']) && $_POST['action'] === "delete_service") {
        if ($serviceCtrl->delete($_POST['id_service'])) $msg = "Service supprimé !";
    }
    // tu peux compléter de la même façon pour Hebergement, Point, PackEtape
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin CRUD Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { margin-top: 40px; color: #333; }
        form { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; }
        input, textarea { display:block; margin:5px 0; padding:5px; width: 300px; }
        input[type=submit] { width: auto; background:#007BFF; color:white; border:none; cursor:pointer; }
        input[type=submit]:hover { background:#0056b3; }
        table { border-collapse: collapse; margin-top:10px; }
        td,th { border:1px solid #ddd; padding:8px; }
    </style>
</head>
<body>
    <h1>Interface d'administration - Test CRUD</h1>
    <p style="color:green;"><?php echo $msg; ?></p>

    <!-- CRUD Pack -->
    <h2>Gestion des Packs</h2>
    <form method="post">
        <input type="hidden" name="action" value="add_pack">
        <input type="text" name="nom" placeholder="Nom du pack" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" step="0.01" name="prix" placeholder="Prix">
        <input type="submit" value="Ajouter Pack">
    </form>
    <h3>Liste des Packs</h3>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Description</th><th>Prix</th><th>Action</th></tr>
        <?php foreach($packCtrl->getAll() as $id => $p): ?>
        <tr>
            <td><?= $p['id_pack'] ?? "-" ?></td>
            <!-- Formulaire pour modification -->
            <form method="post">
                <td>
                    <input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>" required>
                </td>
                <td>
                    <textarea name="description"><?= htmlspecialchars($p['description']) ?></textarea>
                </td>
                <td>
                    <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($p['prix']) ?>">
                </td>
                <td>
                    <input type="hidden" name="id_pack" value="<?= $p['id_pack'] ?>">
                    <input type="hidden" name="action" value="update_pack">
                    <input type="submit" value="Modifier">
            </form>
            <!-- Formulaire pour suppression -->
            <form method="post" style="display:inline">
                <input type="hidden" name="action" value="delete_pack">
                <input type="hidden" name="id_pack" value="<?= $p['id_pack'] ?>">
                <input type="submit" value="Supprimer">
            </form>
                </td>
        </tr>
        <?php endforeach; ?>
    </table>



    <!-- CRUD Services -->
    <h2>Gestion des Services</h2>
    <form method="post">
        <input type="hidden" name="action" value="add_service">
        <input type="text" name="nom" placeholder="Nom du service" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" step="0.01" name="prix" placeholder="Prix">
        <input type="submit" value="Ajouter Service">
    </form>
    <h3>Liste des Services</h3>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Description</th><th>Prix</th><th>Action</th></tr>
        <?php foreach($serviceCtrl->getAll() as $s): ?>
        <tr>
            <td><?= $s->getIdService() ?? "-" ?></td>
            <td><?= htmlspecialchars($s->getNom()) ?></td>
            <td><?= htmlspecialchars($s->getDescription()) ?></td>
            <td><?= htmlspecialchars($s->getPrix()) ?></td>
            <td>
                <form method="post" style="display:inline">
                    <input type="hidden" name="action" value="delete_service">
                    <input type="hidden" name="id_service" value="<?= $s->getIdService() ?>">
                    <input type="submit" value="Supprimer">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Tu peux répliquer la même logique pour : Hebergement, Point, PackEtape -->

</body>
</html>
