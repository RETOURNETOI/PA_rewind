<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion");
    exit;
}

require_once __DIR__.'/../controller/HebergementController.php';
require_once __DIR__.'/../controller/PointArretController.php';

$hebergementCtrl = new HebergementController();
$pointCtrl = new PointArretController();

$message = "";
$point_id = $_GET['point_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $success = $hebergementCtrl->ajouter($_POST);
        $message = $success ? "✅ Hébergement ajouté." : "❌ Erreur lors de l'ajout.";
    } elseif (isset($_POST['modifier'])) {
        $success = $hebergementCtrl->update($_POST['id'], $_POST);
        $message = $success ? "✅ Hébergement modifié." : "❌ Erreur lors de la modification.";
    } elseif (isset($_POST['planifier_fermeture'])) {
        $message = "🔧 Fermeture planifiée (fonctionnalité à développer)";
    }
}

if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $success = $hebergementCtrl->delete($id);
    $message = $success ? "✅ Hébergement supprimé." : "❌ Erreur lors de la suppression.";
}

$points = $pointCtrl->getAll();
$hebergements = $point_id ? $hebergementCtrl->getByPoint($point_id) : $hebergementCtrl->getAll();
$selectedPoint = $point_id ? $pointCtrl->getById($point_id) : null;

$totalHebergements = count($hebergements);
$typesStats = [];
foreach ($hebergements as $heb) {
    $typesStats[$heb['type']] = ($typesStats[$heb['type']] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Hébergements</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .hebergements-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .hebergement-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: relative; }
        .btn { padding: 8px 16px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 3px; font-size: 0.9em; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; } .btn-secondary:hover { background: #5a6268; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        .tabs { display: flex; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .tab { padding: 12px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab.active { border-bottom-color: #007BFF; color: #007BFF; font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status-open { background: #d4edda; color: #155724; }
        .status-closed { background: #f8d7da; color: #721c24; }
        .status-maintenance { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Gestion des Hébergements</h1>
            <?php if ($selectedPoint): ?>
                <h2>Point: <?= htmlspecialchars($selectedPoint['nom']) ?></h2>
                <a href="gestion_hebergements.php" class="btn btn-secondary">Voir tous les hébergements</a>
            <?php endif; ?>
            <a href="<?= BASE_PATH ?>/dashboardadmin" class="btn">← Dashboard</a>
            <a href="<?= BASE_PATH ?>/gestionpointsarret" class="btn">Points d'Arrêt</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= $totalHebergements ?></h3>
                <p>Hébergements Total</p>
            </div>
            <?php foreach ($typesStats as $type => $count): ?>
            <div class="stat-card">
                <h3><?= $count ?></h3>
                <p><?= ucfirst($type) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showTab('liste')">📋 Liste Hébergements</div>
            <div class="tab" onclick="showTab('ajouter')">➕ Ajouter</div>
            <div class="tab" onclick="showTab('fermetures')">🔧 Planifier Fermetures</div>
            <div class="tab" onclick="showTab('statistiques')">📊 Statistiques</div>
        </div>

        <div class="tab-content active" id="liste">
            <div class="hebergements-grid">
                <?php foreach ($hebergements as $heb): ?>
                <div class="hebergement-card">
                    <div class="status-badge status-open">Ouvert</div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $heb['id_hebergement'] ?>">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($heb['nom']) ?>" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type">
                                    <option value="hotel" <?= $heb['type']=='hotel'?'selected':'' ?>>Hôtel</option>
                                    <option value="gite" <?= $heb['type']=='gite'?'selected':'' ?>>Gîte</option>
                                    <option value="camping" <?= $heb['type']=='camping'?'selected':'' ?>>Camping</option>
                                    <option value="auberge" <?= $heb['type']=='auberge'?'selected':'' ?>>Auberge</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Capacité</label>
                                <input type="number" name="capacite" value="<?= $heb['capacite'] ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Prix par nuit (€)</label>
                            <input type="number" step="0.01" name="prix_nuit" value="<?= $heb['prix_nuit'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2"><?= htmlspecialchars($heb['description']) ?></textarea>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                            <button type="submit" name="modifier" class="btn">Modifier</button>
                            <a href="?supprimer=<?= $heb['id_hebergement'] ?>" 
                               onclick="return confirm('Supprimer cet hébergement ?')" 
                               class="btn btn-danger">Supprimer</a>
                            <button type="button" class="btn btn-warning" onclick="planifierFermeture(<?= $heb['id_hebergement'] ?>)">Fermeture</button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content" id="ajouter">
            <div class="form-section">
                <h2>➕ Nouvel Hébergement</h2>
                <form method="post">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Point d'Arrêt *</label>
                            <select name="id_point" required>
                                <option value="">Sélectionner un point</option>
                                <?php foreach ($points as $point): ?>
                                <option value="<?= $point['id_point'] ?>" <?= $point_id == $point['id_point'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($point['nom']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nom de l'hébergement *</label>
                            <input type="text" name="nom" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Type *</label>
                            <select name="type" required>
                                <option value="hotel">Hôtel</option>
                                <option value="gite">Gîte</option>
                                <option value="camping">Camping</option>
                                <option value="auberge">Auberge</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Capacité *</label>
                            <input type="number" name="capacite" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Prix par nuit (€) *</label>
                            <input type="number" step="0.01" name="prix_nuit" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Décrivez l'hébergement, ses services, équipements..."></textarea>
                    </div>
                    <button type="submit" name="ajouter" class="btn btn-success">Ajouter l'Hébergement</button>
                </form>
            </div>
        </div>

        <div class="tab-content" id="fermetures">
            <div class="form-section">
                <h2>🔧 Planifier des Fermetures</h2>
                <p><strong>Note:</strong> Cette fonctionnalité nécessite une table supplémentaire "fermetures_hebergement" en base de données.</p>
                <form method="post">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Hébergement</label>
                            <select name="id_hebergement" required>
                                <option value="">Sélectionner un hébergement</option>
                                <?php foreach ($hebergements as $heb): ?>
                                <option value="<?= $heb['id_hebergement'] ?>">
                                    <?= htmlspecialchars($heb['nom']) ?> (<?= ucfirst($heb['type']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Raison de la fermeture</label>
                            <select name="raison" required>
                                <option value="travaux">Travaux de rénovation</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="saisonnier">Fermeture saisonnière</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Date début fermeture</label>
                            <input type="date" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label>Date fin fermeture</label>
                            <input type="date" name="date_fin" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" rows="3" placeholder="Détails sur la fermeture..."></textarea>
                    </div>
                    <button type="submit" name="planifier_fermeture" class="btn btn-warning">Planifier la Fermeture</button>
                </form>
            </div>
        </div>

        <div class="tab-content" id="statistiques">
            <div class="form-section">
                <h2>📊 Statistiques Hébergements</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h3>Répartition par type</h3>
                        <?php foreach ($typesStats as $type => $count): ?>
                        <div style="margin: 10px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span><?= ucfirst($type) ?></span>
                                <span><?= $count ?></span>
                            </div>
                            <div style="background: #eee; height: 20px; border-radius: 10px; overflow: hidden;">
                                <div style="background: #007BFF; height: 100%; width: <?= $totalHebergements > 0 ? ($count / $totalHebergements) * 100 : 0 ?>%; border-radius: 10px;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <h3>Informations générales</h3>
                        <p><strong>Total hébergements:</strong> <?= $totalHebergements ?></p>
                        <p><strong>Types différents:</strong> <?= count($typesStats) ?></p>
                        <p><strong>Points d'arrêt couverts:</strong> <?= count($points) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function planifierFermeture(id) {
            showTab('fermetures');
            const select = document.querySelector('select[name="id_hebergement"]');
            if (select) {
                select.value = id;
            }
        }
    </script>
</body>
</html>