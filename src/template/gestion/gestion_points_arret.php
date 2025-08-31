<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__.'/../../controller/PointArretController.php';
$controller = new PointArretController();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $success = $controller->ajouter($_POST);
        $message = $success ? "✅ Point d'arrêt ajouté." : "❌ Erreur lors de l'ajout.";
    } elseif (isset($_POST['modifier'])) {
        $success = $controller->update($_POST['id'], $_POST);
        $message = $success ? "✅ Point d'arrêt modifié." : "❌ Erreur lors de la modification.";
    }
}

if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $success = $controller->delete($id);
    $message = $success ? "✅ Point d'arrêt supprimé." : "❌ Erreur lors de la suppression.";
}

$points = $controller->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Points d'Arrêt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .points-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .point-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 10px 20px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗺️ Gestion des Points d'Arrêt Loire</h1>
            <p>Administrez les étapes le long de la Loire</p>
            <a href="<?= BASE_PATH ?>/dashboardadmin" class="btn">← Retour Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>➕ Ajouter un Point d'Arrêt</h2>
            <form method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="nom">Nom du point *</label>
                        <input type="text" name="nom" id="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="0.000001" name="latitude" id="latitude" placeholder="47.123456">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude" placeholder="2.123456">
                    </div>
                    <div></div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Décrivez ce point d'arrêt, ses attractions, services disponibles..."></textarea>
                </div>
                <button type="submit" name="ajouter" class="btn">Ajouter le Point</button>
            </form>
        </div>

        <div class="form-section">
            <h2>📍 Points d'Arrêt Existants (<?= count($points) ?>)</h2>
            <div class="points-grid">
                <?php foreach ($points as $point): ?>
                <div class="point-card">
                    <form method="post" style="margin-bottom: 15px;">
                        <input type="hidden" name="id" value="<?= $point['id_point'] ?>">
                        <div class="form-group">
                            <label>Nom du point</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($point['nom']) ?>" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Latitude</label>
                                <input type="number" step="0.000001" name="latitude" value="<?= $point['latitude'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="number" step="0.000001" name="longitude" value="<?= $point['longitude'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"><?= htmlspecialchars($point['description']) ?></textarea>
                        </div>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="submit" name="modifier" class="btn">Modifier</button>
                            <a href="?supprimer=<?= $point['id_point'] ?>" 
                               onclick="return confirm('Supprimer ce point d\'arrêt ?')" 
                               class="btn btn-danger">Supprimer</a>
                            <a href="gestion_hebergements.php?point_id=<?= $point['id_point'] ?>" class="btn">Gérer Hébergements</a>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>