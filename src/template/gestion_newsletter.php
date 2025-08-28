<?php
// gestion_newsletter.php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion BDD
$dsn = "mysql:host=localhost;dbname=kayak_trip;charset=utf8";
try {
    $db = new PDO($dsn, "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

$message = "";

// Création des tables
try {
    $db->exec("CREATE TABLE IF NOT EXISTS newsletter_abonnes (
        id_abonne INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        actif BOOLEAN DEFAULT TRUE,
        token_desabonnement VARCHAR(255) UNIQUE
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS newsletter_campagnes (
        id_campagne INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        sujet VARCHAR(255) NOT NULL,
        contenu TEXT NOT NULL,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_envoi DATETIME NULL,
        statut ENUM('brouillon', 'programme', 'envoye') DEFAULT 'brouillon',
        nb_destinataires INT DEFAULT 0,
        nb_ouverts INT DEFAULT 0,
        nb_clics INT DEFAULT 0
    )");
} catch (Exception $e) {
    $message = "Erreur création tables : " . $e->getMessage();
}

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['creer_campagne'])) {
            $stmt = $db->prepare("INSERT INTO newsletter_campagnes (titre, sujet, contenu) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['titre'], $_POST['sujet'], $_POST['contenu']]);
            $message = "Campagne créée avec succès !";
        }
        
        if (isset($_POST['programmer_envoi'])) {
            $stmt = $db->prepare("UPDATE newsletter_campagnes SET statut = 'programme', date_envoi = ? WHERE id_campagne = ?");
            $stmt->execute([$_POST['date_envoi'], $_POST['id_campagne']]);
            $message = "Envoi programmé !";
        }
        
        if (isset($_POST['envoyer_maintenant'])) {
            // Simulation d'envoi - ici vous intégreriez votre service d'email
            $stmt = $db->prepare("UPDATE newsletter_campagnes SET statut = 'envoye', date_envoi = NOW() WHERE id_campagne = ?");
            $stmt->execute([$_POST['id_campagne']]);
            $message = "Newsletter envoyée ! (simulation)";
        }
        
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// Récupération des données
$abonnes = $db->query("SELECT * FROM newsletter_abonnes WHERE actif = 1 ORDER BY date_inscription DESC")->fetchAll(PDO::FETCH_ASSOC);
$campagnes = $db->query("SELECT * FROM newsletter_campagnes ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total_abonnes' => count($abonnes),
    'campagnes_envoyees' => count(array_filter($campagnes, fn($c) => $c['statut'] === 'envoye')),
    'taux_ouverture_moyen' => 0
];

if ($stats['campagnes_envoyees'] > 0) {
    $total_ouverts = array_sum(array_column(array_filter($campagnes, fn($c) => $c['statut'] === 'envoye'), 'nb_ouverts'));
    $total_envoyes = array_sum(array_column(array_filter($campagnes, fn($c) => $c['statut'] === 'envoye'), 'nb_destinataires'));
    $stats['taux_ouverture_moyen'] = $total_envoyes > 0 ? round(($total_ouverts / $total_envoyes) * 100, 1) : 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Newsletter</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .campaigns-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .campaign-card { background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #007BFF; }
        .campaign-card.sent { border-left-color: #28a745; }
        .campaign-card.scheduled { border-left-color: #ffc107; }
        .btn { padding: 8px 16px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 3px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; background: #d4edda; color: #155724; }
        .tabs { display: flex; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .tab { padding: 12px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab.active { border-bottom-color: #007BFF; color: #007BFF; font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .badge-brouillon { background: #6c757d; color: white; }
        .badge-programme { background: #ffc107; color: #212529; }
        .badge-envoye { background: #28a745; color: white; }
        .abonne-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion Newsletter</h1>
            <p>Création de campagnes email et gestion des abonnés</p>
            <a href="dashboardadmin.php" class="btn">← Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= $stats['total_abonnes'] ?></h3>
                <p>Abonnés Actifs</p>
            </div>
            <div class="stat-card">
                <h3><?= count($campagnes) ?></h3>
                <p>Campagnes Créées</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['campagnes_envoyees'] ?></h3>
                <p>Campagnes Envoyées</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['taux_ouverture_moyen'] ?>%</h3>
                <p>Taux d'Ouverture</p>
            </div>
        </div>

        <!-- Onglets -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('campagnes')">Campagnes</div>
            <div class="tab" onclick="showTab('abonnes')">Abonnés</div>
            <div class="tab" onclick="showTab('nouvelle-campagne')">+ Nouvelle Campagne</div>
            <div class="tab" onclick="showTab('templates')">Templates</div>
        </div>

        <!-- Onglet Campagnes -->
        <div class="tab-content active" id="campagnes">
            <div class="campaigns-grid">
                <?php foreach ($campagnes as $campagne): ?>
                <div class="campaign-card <?= $campagne['statut'] === 'envoye' ? 'sent' : ($campagne['statut'] === 'programme' ? 'scheduled' : '') ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4><?= htmlspecialchars($campagne['titre']) ?></h4>
                        <span class="badge badge-<?= $campagne['statut'] ?>"><?= ucfirst($campagne['statut']) ?></span>
                    </div>
                    <p><strong>Sujet:</strong> <?= htmlspecialchars($campagne['sujet']) ?></p>
                    <p><strong>Créée:</strong> <?= date('d/m/Y H:i', strtotime($campagne['date_creation'])) ?></p>
                    <?php if ($campagne['date_envoi']): ?>
                        <p><strong>Envoyée:</strong> <?= date('d/m/Y H:i', strtotime($campagne['date_envoi'])) ?></p>
                    <?php endif; ?>
                    <?php if ($campagne['statut'] === 'envoye'): ?>
                        <p><strong>Destinataires:</strong> <?= $campagne['nb_destinataires'] ?></p>
                        <p><strong>Taux d'ouverture:</strong> <?= $campagne['nb_destinataires'] > 0 ? round(($campagne['nb_ouverts'] / $campagne['nb_destinataires']) * 100, 1) : 0 ?>%</p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 15px;">
                        <?php if ($campagne['statut'] === 'brouillon'): ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="id_campagne" value="<?= $campagne['id_campagne'] ?>">
                                <input type="datetime-local" name="date_envoi" style="width: auto; margin-right: 10px;">
                                <button type="submit" name="programmer_envoi" class="btn btn-warning">Programmer</button>
                            </form>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="id_campagne" value="<?= $campagne['id_campagne'] ?>">
                                <button type="submit" name="envoyer_maintenant" class="btn btn-success" onclick="return confirm('Envoyer maintenant à tous les abonnés ?')">Envoyer Maintenant</button>
                            </form>
                        <?php endif; ?>
                        <button class="btn" onclick="previewCampagne(<?= $campagne['id_campagne'] ?>)">Aperçu</button>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($campagnes)): ?>
                    <div class="campaign-card">
                        <h4>Aucune campagne</h4>
                        <p>Créez votre première newsletter pour communiquer avec vos clients.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet Abonnés -->
        <div class="tab-content" id="abonnes">
            <div class="form-section">
                <h3>Liste des Abonnés (<?= count($abonnes) ?>)</h3>
                <?php if (empty($abonnes)): ?>
                    <p>Aucun abonné pour le moment.</p>
                    <p><em>Note: Les abonnements se font généralement via un formulaire sur le site web.</em></p>
                <?php else: ?>
                    <?php foreach ($abonnes as $abonne): ?>
                        <div class="abonne-item">
                            <div>
                                <strong><?= htmlspecialchars($abonne['email']) ?></strong>
                                <?php if ($abonne['nom']): ?>
                                    - <?= htmlspecialchars($abonne['prenom'] . ' ' . $abonne['nom']) ?>
                                <?php endif; ?>
                                <br><small>Inscrit le <?= date('d/m/Y', strtotime($abonne['date_inscription'])) ?></small>
                            </div>
                            <div>
                                <button class="btn btn-danger btn-sm" onclick="desabonner(<?= $abonne['id_abonne'] ?>)">Désabonner</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet Nouvelle Campagne -->
        <div class="tab-content" id="nouvelle-campagne">
            <div class="form-section">
                <h2>Créer une Nouvelle Campagne</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Titre de la campagne *</label>
                        <input type="text" name="titre" required placeholder="Newsletter Été 2024">
                    </div>
                    <div class="form-group">
                        <label>Sujet de l'email *</label>
                        <input type="text" name="sujet" required placeholder="Découvrez nos nouveaux circuits kayak !">
                    </div>
                    <div class="form-group">
                        <label>Contenu HTML *</label>
                        <textarea name="contenu" rows="15" required placeholder="Contenu HTML de votre newsletter...">
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Newsletter Kayak Trip</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: white;">
        <header style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center;">
            <h1>Kayak Trip</h1>
            <p>Votre aventure sur la Loire commence ici</p>
        </header>
        
        <main style="padding: 20px;">
            <h2>Nouveautés de la semaine</h2>
            <p>Cher abonné,</p>
            <p>Découvrez nos dernières offres et actualités...</p>
            
            <!-- Votre contenu ici -->
            
        </main>
        
        <footer style="background: #f8f9fa; padding: 20px; text-align: center; color: #666;">
            <p>Kayak Trip - Aventures sur la Loire</p>
            <p><a href="[LIEN_DESABONNEMENT]">Se désabonner</a></p>
        </footer>
    </div>
</body>
</html>
                        </textarea>
                    </div>
                    <button type="submit" name="creer_campagne" class="btn btn-success">Créer la Campagne</button>
                </form>
            </div>
        </div>

        <!-- Onglet Templates -->
        <div class="tab-content" id="templates">
            <div class="form-section">
                <h2>Templates Prédéfinis</h2>
                <div class="campaigns-grid">
                    <div class="campaign-card">
                        <h4>Template Promotion</h4>
                        <p>Template pour les offres spéciales et codes promo</p>
                        <button class="btn" onclick="loadTemplate('promotion')">Utiliser</button>
                    </div>
                    <div class="campaign-card">
                        <h4>Template Nouveautés</h4>
                        <p>Présentation des nouveaux packs et services</p>
                        <button class="btn" onclick="loadTemplate('nouveautes')">Utiliser</button>
                    </div>
                    <div class="campaign-card">
                        <h4>Template Saisonnier</h4>
                        <p>Newsletter adaptée aux saisons</p>
                        <button class="btn" onclick="loadTemplate('saisonnier')">Utiliser</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal aperçu -->
        <div id="previewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; max-width: 80%; max-height: 80%; overflow: auto;">
                <h3>Aperçu de la Campagne</h3>
                <div id="previewContent"></div>
                <button onclick="closePreview()" class="btn">Fermer</button>
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

        function previewCampagne(id) {
            // Simulation d'aperçu - ici vous récupéreriez le contenu via AJAX
            document.getElementById('previewContent').innerHTML = '<p>Aperçu de la campagne #' + id + '</p><p><em>Fonctionnalité à implémenter via AJAX</em></p>';
            document.getElementById('previewModal').style.display = 'block';
        }

        function closePreview() {
            document.getElementById('previewModal').style.display = 'none';
        }

        function loadTemplate(type) {
            showTab('nouvelle-campagne');
            const templates = {
                promotion: {
                    titre: 'Offre Spéciale - ' + new Date().getFullYear(),
                    sujet: '🎯 Offre limitée : -20% sur tous nos packs !',
                    contenu: 'Template promotion avec code promo...'
                },
                nouveautes: {
                    titre: 'Nouveautés - ' + new Date().getFullYear(),
                    sujet: '🚣‍♂️ Découvrez nos nouveaux circuits Loire',
                    contenu: 'Template nouveautés avec présentation des packs...'
                },
                saisonnier: {
                    titre: 'Newsletter Saisonnière',
                    sujet: '🌅 La saison kayak bat son plein !',
                    contenu: 'Template adapté à la saison...'
                }
            };
            
            if (templates[type]) {
                document.querySelector('input[name="titre"]').value = templates[type].titre;
                document.querySelector('input[name="sujet"]').value = templates[type].sujet;
                // Le contenu pourrait être plus détaillé ici
            }
        }

        function desabonner(id) {
            if (confirm('Désabonner cet utilisateur ?')) {
                // Ici vous feriez un appel AJAX pour désabonner
                alert('Fonctionnalité à implémenter via AJAX');
            }
        }
    </script>
</body>
</html>