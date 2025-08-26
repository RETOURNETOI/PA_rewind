<?php
// gestion_messagerie.php
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

// Création table si nécessaire (amélioration de la table MESSAGE existante)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS conversations (
        id_conversation INT AUTO_INCREMENT PRIMARY KEY,
        id_client INT NOT NULL,
        id_commercial INT NULL,
        sujet VARCHAR(255),
        statut ENUM('ouvert', 'en_cours', 'ferme') DEFAULT 'ouvert',
        priorite ENUM('basse', 'normale', 'haute') DEFAULT 'normale',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_derniere_activite DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_client) REFERENCES utilisateur(id_utilisateur),
        FOREIGN KEY (id_commercial) REFERENCES utilisateur(id_utilisateur)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS messages_conversation (
        id_message INT AUTO_INCREMENT PRIMARY KEY,
        id_conversation INT NOT NULL,
        id_expediteur INT NOT NULL,
        contenu TEXT NOT NULL,
        date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
        lu BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (id_conversation) REFERENCES conversations(id_conversation),
        FOREIGN KEY (id_expediteur) REFERENCES utilisateur(id_utilisateur)
    )");
} catch (Exception $e) {
    $message = "Erreur création tables : " . $e->getMessage();
}

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assigner_commercial'])) {
        $stmt = $db->prepare("UPDATE conversations SET id_commercial = ?, statut = 'en_cours' WHERE id_conversation = ?");
        $stmt->execute([$_POST['id_commercial'], $_POST['id_conversation']]);
        $message = "Commercial assigné à la conversation.";
    }
    
    if (isset($_POST['changer_statut'])) {
        $stmt = $db->prepare("UPDATE conversations SET statut = ? WHERE id_conversation = ?");
        $stmt->execute([$_POST['nouveau_statut'], $_POST['id_conversation']]);
        $message = "Statut de la conversation modifié.";
    }
}

// Récupération des données
$conversations = $db->query("
    SELECT c.*, 
           client.nom as client_nom, client.prenom as client_prenom, client.email as client_email,
           commercial.nom as commercial_nom, commercial.prenom as commercial_prenom,
           (SELECT COUNT(*) FROM messages_conversation mc WHERE mc.id_conversation = c.id_conversation AND mc.lu = FALSE AND mc.id_expediteur != c.id_commercial) as messages_non_lus
    FROM conversations c
    JOIN utilisateur client ON c.id_client = client.id_utilisateur
    LEFT JOIN utilisateur commercial ON c.id_commercial = commercial.id_utilisateur
    ORDER BY c.date_derniere_activite DESC
")->fetchAll(PDO::FETCH_ASSOC);

$commerciaux = $db->query("SELECT id_utilisateur, nom, prenom FROM utilisateur WHERE role = 'commercial'")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$stats = [
    'total' => count($conversations),
    'ouvert' => count(array_filter($conversations, fn($c) => $c['statut'] === 'ouvert')),
    'en_cours' => count(array_filter($conversations, fn($c) => $c['statut'] === 'en_cours')),
    'ferme' => count(array_filter($conversations, fn($c) => $c['statut'] === 'ferme')),
    'non_lus' => array_sum(array_column($conversations, 'messages_non_lus'))
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Messagerie Service Commercial</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .conversations-section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .conversation-card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #007BFF; }
        .conversation-card.priority-haute { border-left-color: #dc3545; }
        .conversation-card.priority-normale { border-left-color: #ffc107; }
        .conversation-card.status-ferme { opacity: 0.7; }
        .btn { padding: 8px 16px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 3px; font-size: 0.9em; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; } .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; } .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; } .btn-danger:hover { background: #c82333; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .badge-ouvert { background: #d4edda; color: #155724; }
        .badge-en_cours { background: #fff3cd; color: #856404; }
        .badge-ferme { background: #f8d7da; color: #721c24; }
        .badge-priority { margin-left: 10px; }
        .badge-priority-haute { background: #dc3545; color: white; }
        .badge-priority-normale { background: #ffc107; color: #212529; }
        .badge-priority-basse { background: #6c757d; color: white; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; background: #d4edda; color: #155724; }
        .conversation-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .conversation-meta { font-size: 0.9em; color: #6c757d; }
        .actions-form { display: inline-flex; gap: 10px; align-items: center; }
        .unread-count { background: #dc3545; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.8em; font-weight: bold; margin-left: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion Messagerie Service Commercial</h1>
            <p>Supervision des conversations clients et assignation des commerciaux</p>
            <a href="dashboard_admin.php" class="btn">← Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Conversations</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['ouvert'] ?></h3>
                <p>Nouvelles</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['en_cours'] ?></h3>
                <p>En Cours</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['ferme'] ?></h3>
                <p>Fermées</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['non_lus'] ?></h3>
                <p>Messages Non Lus</p>
            </div>
        </div>

        <!-- Liste des conversations -->
        <div class="conversations-section">
            <h2>Conversations Clients</h2>
            
            <?php if (empty($conversations)): ?>
                <p>Aucune conversation pour le moment.</p>
                <p><em>Note: Cette fonctionnalité nécessite l'implémentation du système de chat temps réel côté client.</em></p>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                <div class="conversation-card priority-<?= $conv['priorite'] ?> status-<?= $conv['statut'] ?>">
                    <div class="conversation-header">
                        <div>
                            <h4>
                                <?= htmlspecialchars($conv['client_prenom'] . ' ' . $conv['client_nom']) ?>
                                <span class="badge badge-<?= $conv['statut'] ?>"><?= ucfirst($conv['statut']) ?></span>
                                <span class="badge badge-priority badge-priority-<?= $conv['priorite'] ?>"><?= ucfirst($conv['priorite']) ?></span>
                                <?php if ($conv['messages_non_lus'] > 0): ?>
                                    <span class="unread-count"><?= $conv['messages_non_lus'] ?></span>
                                <?php endif; ?>
                            </h4>
                            <div class="conversation-meta">
                                <strong>Sujet:</strong> <?= htmlspecialchars($conv['sujet'] ?: 'Conversation générale') ?>
                            </div>
                            <div class="conversation-meta">
                                <strong>Email:</strong> <?= htmlspecialchars($conv['client_email']) ?>
                                • <strong>Créé:</strong> <?= date('d/m/Y H:i', strtotime($conv['date_creation'])) ?>
                                • <strong>Dernière activité:</strong> <?= date('d/m/Y H:i', strtotime($conv['date_derniere_activite'])) ?>
                            </div>
                            <?php if ($conv['commercial_nom']): ?>
                                <div class="conversation-meta">
                                    <strong>Commercial assigné:</strong> <?= htmlspecialchars($conv['commercial_prenom'] . ' ' . $conv['commercial_nom']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <div class="actions-form">
                                <!-- Assignation commercial -->
                                <?php if (!$conv['commercial_nom']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="id_conversation" value="<?= $conv['id_conversation'] ?>">
                                        <select name="id_commercial" required>
                                            <option value="">Assigner à...</option>
                                            <?php foreach ($commerciaux as $commercial): ?>
                                                <option value="<?= $commercial['id_utilisateur'] ?>">
                                                    <?= htmlspecialchars($commercial['prenom'] . ' ' . $commercial['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="assigner_commercial" class="btn btn-success">Assigner</button>
                                    </form>
                                <?php endif; ?>
                                
                                <!-- Changement de statut -->
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id_conversation" value="<?= $conv['id_conversation'] ?>">
                                    <select name="nouveau_statut">
                                        <option value="ouvert" <?= $conv['statut'] === 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                                        <option value="en_cours" <?= $conv['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                        <option value="ferme" <?= $conv['statut'] === 'ferme' ? 'selected' : '' ?>>Fermé</option>
                                    </select>
                                    <button type="submit" name="changer_statut" class="btn">Changer</button>
                                </form>
                                
                                <a href="conversation_detail.php?id=<?= $conv['id_conversation'] ?>" class="btn">Voir Messages</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Section configuration -->
        <div class="conversations-section" style="margin-top: 20px;">
            <h2>Configuration du Chat</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>Fonctionnalités à implémenter :</h4>
                    <ul>
                        <li>Widget de chat en temps réel côté client</li>
                        <li>Notifications push pour les commerciaux</li>
                        <li>Interface de réponse en temps réel</li>
                        <li>Historique des conversations</li>
                        <li>Système de tickets automatique</li>
                    </ul>
                </div>
                <div>
                    <h4>Technologies recommandées :</h4>
                    <ul>
                        <li>WebSockets ou Server-Sent Events</li>
                        <li>JavaScript pour l'interface temps réel</li>
                        <li>Système de notifications par email</li>
                        <li>API REST pour la gestion des messages</li>
                        <li>Base de données pour l'historique</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section commerciaux -->
        <div class="conversations-section" style="margin-top: 20px;">
            <h2>Équipe Commerciale</h2>
            <?php if (empty($commerciaux)): ?>
                <p>Aucun commercial enregistré. <a href="gestionuser.php" class="btn">Ajouter des commerciaux</a></p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    <?php foreach ($commerciaux as $commercial): ?>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <h4><?= htmlspecialchars($commercial['prenom'] . ' ' . $commercial['nom']) ?></h4>
                            <div class="conversation-meta">
                                Conversations assignées: 
                                <?php 
                                $assigned = count(array_filter($conversations, fn($c) => $c['id_commercial'] == $commercial['id_utilisateur']));
                                echo $assigned;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>