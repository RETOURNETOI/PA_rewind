<?php
// admin_test.php


// --- Inclusion des classes & contrôleurs ---
require_once  __DIR__ . '/../../bdd/Connexion.php';
require_once  __DIR__ . '/../../controller/PackController.php';
require_once  __DIR__ . '/../../controller/ServiceController.php';
require_once  __DIR__ . '/../../model/Service.php';
require_once  __DIR__ . '/../../controller/DashboardStats.php';
require_once  __DIR__ . '/../../model/Pack.php';
// --- Connexion à la BDD ---
$connexion = new Connexion();
$db = $connexion->getPDO();


// --- Instanciation des contrôleurs ---
$packCtrl = new PackController();
$serviceCtrl = new ServiceController();

// --- Variables pour conserver les valeurs postées ---
$packNom = $_POST['nom_pack'] ?? '';
$packDesc = $_POST['description_pack'] ?? '';
$packPrix = $_POST['prix_pack'] ?? '';
$serviceNom = $_POST['nom_service'] ?? '';
$serviceDesc = $_POST['description_service'] ?? '';
$servicePrix = $_POST['prix_service'] ?? '';

// --- Gestion des actions ---
$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {

            case "add_pack":
                $p = new Pack($_POST['nom_pack'], $_POST['description_pack'], (float)$_POST['prix_pack']);
                if ($packCtrl->ajouter($p)) $msg = "Pack ajouté avec succès !";
                else $msg = "Erreur lors de l'ajout du pack.";
                // Conserver valeurs
                $packNom = $_POST['nom_pack'];
                $packDesc = $_POST['description_pack'];
                $packPrix = $_POST['prix_pack'];
                break;

            case "update_pack":
                $id = (int)$_POST['id_pack'];
                $data = [
                    'nom' => $_POST['nom'],
                    'description' => $_POST['description'],
                    'prix' => (float)$_POST['prix']
                ];
                if ($packCtrl->update($id, $data)) $msg = "Pack modifié avec succès !";
                else $msg = "Erreur lors de la modification du pack.";
                break;

            case "delete_pack":
                if ($packCtrl->delete((int)$_POST['id_pack'])) $msg = "Pack supprimé !";
                else $msg = "Erreur lors de la suppression du pack.";
                break;

            case "add_service":
                $data = [
                    'nom' => $_POST['nom_service'],
                    'description' => $_POST['description_service'],
                    'prix' => (float)$_POST['prix_service']
                ];
                if ($serviceCtrl->ajouter($data)) $msg = "Service ajouté avec succès !";
                else $msg = "Erreur lors de l'ajout du service.";
                // Conserver valeurs
                $serviceNom = $_POST['nom_service'];
                $serviceDesc = $_POST['description_service'];
                $servicePrix = $_POST['prix_service'];
                break;

            case "update_service":
                $id = (int)$_POST['id_service'];
                $data = [
                    'nom' => $_POST['nom'],
                    'description' => $_POST['description'],
                    'prix' => (float)$_POST['prix']
                ];
                if ($serviceCtrl->update($id, $data)) $msg = "Service modifié avec succès !";
                else $msg = "Erreur lors de la modification du service.";
                break;

            case "delete_service":
                if ($serviceCtrl->delete((int)$_POST['id_service'])) $msg = "Service supprimé !";
                else $msg = "Erreur lors de la suppression du service.";
                break;
        }
    }
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
<h1>Interface d'administration - Packs et Services</h1>
<p style="color:green;"><?= $msg ?></p>

<!-- ------------------- GESTION PACKS ------------------- -->
<h2>Ajouter un Pack</h2>
<form method="post">
    <input type="hidden" name="action" value="add_pack">
    <input type="text" name="nom_pack" placeholder="Nom du pack" value="<?= htmlspecialchars($packNom) ?>" required>
    <textarea name="description_pack" placeholder="Description"><?= htmlspecialchars($packDesc) ?></textarea>
    <input type="number" step="0.01" name="prix_pack" placeholder="Prix" value="<?= htmlspecialchars($packPrix) ?>">
    <input type="submit" value="Ajouter Pack">
</form>

<h2>Liste des Packs</h2>
<table>
    <tr><th>ID</th><th>Nom</th><th>Description</th><th>Prix</th><th>Actions</th></tr>
    <?php foreach($packCtrl->getAll() as $p): ?>
    <tr>
        <td><?= $p['id_pack'] ?></td>
        <form method="post">
            <td><input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>" required></td>
            <td><textarea name="description"><?= htmlspecialchars($p['description']) ?></textarea></td>
            <td><input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($p['prix']) ?>"></td>
            <td>
                <input type="hidden" name="id_pack" value="<?= $p['id_pack'] ?>">
                <input type="hidden" name="action" value="update_pack">
                <input type="submit" value="Modifier">
        </form>
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="delete_pack">
            <input type="hidden" name="id_pack" value="<?= $p['id_pack'] ?>">
            <input type="submit" value="Supprimer">
        </form>
            </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- ------------------- GESTION SERVICES ------------------- -->
<h2>Ajouter un Service</h2>
<form method="post">
    <input type="hidden" name="action" value="add_service">
    <input type="text" name="nom_service" placeholder="Nom du service" value="<?= htmlspecialchars($serviceNom) ?>" required>
    <textarea name="description_service" placeholder="Description"><?= htmlspecialchars($serviceDesc) ?></textarea>
    <input type="number" step="0.01" name="prix_service" placeholder="Prix" value="<?= htmlspecialchars($servicePrix) ?>">
    <input type="submit" value="Ajouter Service">
</form>

<h2>Liste des Services</h2>
<table>
    <tr><th>ID</th><th>Nom</th><th>Description</th><th>Prix</th><th>Actions</th></tr>
    <?php foreach($serviceCtrl->getAll() as $s): ?>
    <tr>
        <td><?= $s['id_service'] ?></td>
        <form method="post">
            <td><input type="text" name="nom" value="<?= htmlspecialchars($s['nom']) ?>" required></td>
            <td><textarea name="description"><?= htmlspecialchars($s['description']) ?></textarea></td>
            <td><input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($s['prix']) ?>"></td>
            <td>
                <input type="hidden" name="id_service" value="<?= $s['id_service'] ?>">
                <input type="hidden" name="action" value="update_service">
                <input type="submit" value="Modifier">
        </form>
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="delete_service">
            <input type="hidden" name="id_service" value="<?= $s['id_service'] ?>">
            <input type="submit" value="Supprimer">
        </form>
            </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
