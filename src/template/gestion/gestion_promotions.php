<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

$dsn = "mysql:host=localhost;dbname=kayak_trip;charset=utf8";
try {
    $db = new PDO($dsn, "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

$message = "";

try {
    $db->exec("CREATE TABLE IF NOT EXISTS codes_promo (
        id_code INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) UNIQUE NOT NULL,
        description VARCHAR(255),
        type_reduction ENUM('pourcentage', 'montant') DEFAULT 'pourcentage',
        valeur_reduction DECIMAL(8,2) NOT NULL,
        date_debut DATE,
        date_fin DATE,
        usage_max INT DEFAULT NULL,
        usage_actuel INT DEFAULT 0,
        premiere_reservation_uniquement BOOLEAN DEFAULT FALSE,
        actif BOOLEAN DEFAULT TRUE,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS plages_tarifaires (
        id_plage INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        multiplicateur DECIMAL(4,2) DEFAULT 1.0,
        description TEXT,
        actif BOOLEAN DEFAULT TRUE,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    $message = "Erreur création tables : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['ajouter_code'])) {
            $stmt = $db->prepare("INSERT INTO codes_promo 
                (code, description, type_reduction, valeur_reduction, date_debut, date_fin, usage_max, premiere_reservation_uniquement)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['code'],
                $_POST['description'],
                $_POST['type_reduction'],
                $_POST['valeur_reduction'],
                $_POST['date_debut'] ?: null,
                $_POST['date_fin'] ?: null,
                $_POST['usage_max'] ?: null,
                isset($_POST['premiere_reservation_uniquement']) ? 1 : 0
            ]);
            $message = "Code promo ajouté avec succès !";
        }
        
        if (isset($_POST['ajouter_plage'])) {
            $stmt = $db->prepare("INSERT INTO plages_tarifaires 
                (nom, date_debut, date_fin, multiplicateur, description)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nom'],
                $_POST['date_debut'],
                $_POST['date_fin'], 
                $_POST['multiplicateur'],
                $_POST['description']
            ]);
            $message = "Plage tarifaire ajoutée avec succès !";
        }
        
        if (isset($_POST['desactiver_code'])) {
            $stmt = $db->prepare("UPDATE codes_promo SET actif = 0 WHERE id_code = ?");
            $stmt->execute([$_POST['id_code']]);
            $message = "Code promo désactivé !";
        }
        
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

$codes_promo = $db->query("SELECT * FROM codes_promo ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
$plages_tarifaires = $db->query("SELECT * FROM plages_tarifaires ORDER BY date_debut DESC")->fetchAll(PDO::FETCH_ASSOC);

$codes_actifs = count(array_filter($codes_promo, fn($c) => $c['actif']));
$codes_expires = count(array_filter($codes_promo, fn($c) => $c['date_fin'] && $c['date_fin'] < date('Y-m-d')));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Offres Promotionnelles</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .codes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .code-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #007BFF; }
        .code-card.inactive { border-left-color: #dc3545; opacity: 0.7; }
        .btn { padding: 8px 16px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 3px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; background: #d4edda; color: #155724; }
        .tabs { display: flex; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .tab { padding: 12px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab.active { border-bottom-color: #007BFF; color: #007BFF; font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion Offres Promotionnelles</h1>
            <p>Codes de réduction et plages tarifaires saisonnières</p>
            <a href="dashboardadmin.php" class="btn">← Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= count($codes_promo) ?></h3>
                <p>Codes Promo Total</p>
            </div>
            <div class="stat-card">
                <h3><?= $codes_actifs ?></h3>
                <p>Codes Actifs</p>
            </div>
            <div class="stat-card">
                <h3><?= $codes_expires ?></h3>
                <p>Codes Expirés</p>
            </div>
            <div class="stat-card">
                <h3><?= count($plages_tarifaires) ?></h3>
                <p>Plages Tarifaires</p>
            </div>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showTab('codes')">Codes Promo</div>
            <div class="tab" onclick="showTab('plages')">Plages Tarifaires</div>
            <div class="tab" onclick="showTab('nouveau-code')">+ Nouveau Code</div>
            <div class="tab" onclick="showTab('nouvelle-plage')">+ Nouvelle Plage</div>
        </div>

        <div class="tab-content active" id="codes">
            <div class="codes-grid">
                <?php foreach ($codes_promo as $code): ?>
                <div class="code-card <?= $code['actif'] ? '' : 'inactive' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3><?= htmlspecialchars($code['code']) ?></h3>
                        <span class="badge <?= $code['actif'] ? 'badge-success' : 'badge-danger' ?>">
                            <?= $code['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </div>
                    <p><strong>Description:</strong> <?= htmlspecialchars($code['description']) ?></p>
                    <p><strong>Réduction:</strong> 
                        <?php if ($code['type_reduction'] == 'pourcentage'): ?>
                            <?= $code['valeur_reduction'] ?>%
                        <?php else: ?>
                            <?= $code['valeur_reduction'] ?>€
                        <?php endif; ?>
                    </p>
                    <?php if ($code['date_debut']): ?>
                        <p><strong>Période:</strong> <?= date('d/m/Y', strtotime($code['date_debut'])) ?> 
                        <?= $code['date_fin'] ? '→ ' . date('d/m/Y', strtotime($code['date_fin'])) : '' ?></p>
                    <?php endif; ?>
                    <?php if ($code['usage_max']): ?>
                        <p><strong>Usage:</strong> <?= $code['usage_actuel'] ?>/<?= $code['usage_max'] ?></p>
                    <?php endif; ?>
                    <?php if ($code['premiere_reservation_uniquement']): ?>
                        <p><strong>🎯 Première réservation uniquement</strong></p>
                    <?php endif; ?>
                    
                    <?php if ($code['actif']): ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="id_code" value="<?= $code['id_code'] ?>">
                            <button type="submit" name="desactiver_code" class="btn btn-warning">Désactiver</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content" id="plages">
            <div class="codes-grid">
                <?php foreach ($plages_tarifaires as $plage): ?>
                <div class="code-card">
                    <h3><?= htmlspecialchars($plage['nom']) ?></h3>
                    <p><strong>Période:</strong> <?= date('d/m/Y', strtotime($plage['date_debut'])) ?> 
                        → <?= date('d/m/Y', strtotime($plage['date_fin'])) ?></p>
                    <p><strong>Multiplicateur:</strong> x<?= $plage['multiplicateur'] ?></p>
                    <p><?= htmlspecialchars($plage['description']) ?></p>
                    
                    <?php
                    $aujourd_hui = date('Y-m-d');
                    $status = '';
                    if ($plage['date_fin'] < $aujourd_hui) {
                        $status = '<span class="badge badge-danger">Terminé</span>';
                    } elseif ($plage['date_debut'] <= $aujourd_hui && $plage['date_fin'] >= $aujourd_hui) {
                        $status = '<span class="badge badge-success">En cours</span>';
                    } else {
                        $status = '<span class="badge badge-warning">À venir</span>';
                    }
                    ?>
                    <?= $status ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content" id="nouveau-code">
            <div class="form-section">
                <h2>Créer un Code Promo</h2>
                <form method="post">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Code promo *</label>
                            <input type="text" name="code" required placeholder="BIENVENUE2024">
                        </div>
                        <div class="form-group">
                            <label>Type de réduction *</label>
                            <select name="type_reduction" required>
                                <option value="pourcentage">Pourcentage (%)</option>
                                <option value="montant">Montant fixe (€)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Valeur réduction *</label>
                            <input type="number" step="0.01" name="valeur_reduction" required>
                        </div>
                        <div class="form-group">
                            <label>Usage maximum</label>
                            <input type="number" name="usage_max" placeholder="Laissez vide pour illimité">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Date début</label>
                            <input type="date" name="date_debut">
                        </div>
                        <div class="form-group">
                            <label>Date fin</label>
                            <input type="date" name="date_fin">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Description de l'offre..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="premiere_reservation_uniquement" value="1">
                            Réservé à la première réservation uniquement
                        </label>
                    </div>
                    <button type="submit" name="ajouter_code" class="btn btn-success">Créer le Code</button>
                </form>
            </div>
        </div>

        <div class="tab-content" id="nouvelle-plage">
            <div class="form-section">
                <h2>Créer une Plage Tarifaire</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Nom de la plage *</label>
                        <input type="text" name="nom" required placeholder="Été 2024 - Haute saison">
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Date début *</label>
                            <input type="date" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label>Date fin *</label>
                            <input type="date" name="date_fin" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Multiplicateur de prix *</label>
                        <input type="number" step="0.01" name="multiplicateur" value="1.0" required>
                        <small>1.0 = prix normal, 1.5 = +50%, 0.8 = -20%</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Période de haute/basse saison..."></textarea>
                    </div>
                    <button type="submit" name="ajouter_plage" class="btn btn-success">Créer la Plage</button>
                </form>
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
    </script>
</body>
</html>