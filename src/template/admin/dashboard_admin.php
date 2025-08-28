<?php
// dashboard_admin.php

// --- Configuration du fuseau horaire ---
date_default_timezone_set('Europe/Paris');

// --- Vérification des droits admin ---
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}

require_once  __DIR__ . '/../../bdd/Connexion.php';
require_once  __DIR__ . '/../../controller/PackController.php';
require_once  __DIR__ . '/../../controller/ServiceController.php';
require_once  __DIR__ . '/../../controller/UtilisateurController.php';
require_once  __DIR__ . '/../../controller/HebergementController.php';
require_once  __DIR__ . '/../../controller/PointArretController.php';
require_once  __DIR__ . '/../../controller/CommandeController.php';
require_once  __DIR__ . '/../../controller/DashboardStats.php';

// --- Utilisation de la classe Connexion pour obtenir PDO ---
$connexion = new Connexion();
$db = $connexion->getPDO();

// --- Instanciation des contrôleurs ---
$packCtrl = new PackController();
$serviceCtrl = new ServiceController();
$userCtrl = new UtilisateurController();
$hebergementCtrl = new HebergementController();
$pointCtrl = new PointArretController();
$commandeCtrl = new CommandeController();

// --- Récupération des statistiques avancées ---
try {
    // Instanciation de la classe de statistiques
    $stats = new DashboardStats($db);
    
    // Statistiques générales depuis les contrôleurs
    $totalPacks = count($packCtrl->getAll());
    $totalServices = count($serviceCtrl->getAll());
    $totalUsers = count($userCtrl->AfficherTous());
    $totalHebergements = count($hebergementCtrl->getAll());
    $totalPointsArret = count($pointCtrl->getAll());
    $totalCommandes = count($commandeCtrl->getAll());
    
    // Statistiques avancées depuis la classe helper
    $usersByRole = $stats->getUsersByRole();
    $commandeStats = $stats->getCommandeStats();
    $inscriptionsRecentes = $stats->getRecentInscriptions();
    $topHebergements = $stats->getTopHebergements();
    $mostExpensivePack = $stats->getMostExpensivePack();
    $mostExpensiveService = $stats->getMostExpensiveService();
    $recentUsers = $stats->getRecentUsers();
    $hebergementsByType = $stats->getHebergementsByType();
    $totalReservations = $stats->getTotalReservations();
    
} catch (Exception $e) {
    $error = "Erreur lors du chargement des statistiques : " . $e->getMessage();
    error_log($error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kayak Trip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }

        .admin-info {
            color: #333;
            font-weight: 500;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #ff5252;
            transform: translateY(-1px);
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            text-align: center;
        }

        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
        }

        .stat-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1em;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 0.9em;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .trend-up { background: #e8f5e8; color: #2e7d2e; }
        .trend-down { background: #ffe8e8; color: #d63384; }
        .trend-neutral { background: #f0f0f0; color: #666; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .chart-title {
            color: #333;
            font-size: 1.3em;
            margin-bottom: 20px;
            text-align: center;
        }

        .progress-bar {
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8em;
            font-weight: bold;
        }

        .management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .management-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s ease;
        }

        .management-card:hover {
            transform: translateY(-3px);
        }

        .management-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .management-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
        }

        .recent-activity {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 20px;
        }

        .recent-activity h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 1.2em;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .activity-subtitle {
            color: #666;
            font-size: 0.9em;
        }

        .activity-meta {
            color: #999;
            font-size: 0.8em;
            margin-top: 3px;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
            margin-left: 10px;
        }

        .role-admin { background: #ff6b6b; color: white; }
        .role-client { background: #51cf66; color: white; }
        .role-commercial { background: #339af0; color: white; }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-error {
            background: #ffe8e8;
            color: #d63384;
            border-color: #ff6b6b;
        }

        .alert-success {
            background: #e8f5e8;
            color: #2e7d2e;
            border-color: #51cf66;
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .action-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: #333;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.9em;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .live-time {
            font-weight: bold;
            color: #667eea;
            transition: color 0.3s ease;
        }

        .current-time, .current-date {
            color: #999;
            transition: color 0.2s ease;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .management-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Barre du haut -->
        <div class="top-bar">
            <div class="admin-info">
                Bienvenue <?= isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'Admin' ?>
                <span style="color: #999;">• <span class="current-time live-time" id="header-time">Chargement...</span></span>
            </div>
            <div>
                <a href="<?= BASE_PATH ?>/" class="action-btn">home</a>
                <a href="<?= BASE_PATH ?>/gestionuser" class="action-btn">⚙️ gestion utilisateur</a>
                <a href="logout.php" class="logout-btn">Se déconnecter</a>
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>Dashboard Admin</h1>
            <p>Panneau de contrôle - Kayak Trip Management System</p>
            <div class="quick-actions">
                <a href="<?= BASE_PATH ?>/admintest" class="action-btn">Gestion Pack</a>
                <a href="<?= BASE_PATH ?>/gestionuser" class="action-btn">Gestion Utilisateur</a>
                <a href="<?= BASE_PATH ?>/listepointsarret" class="action-btn">Rapport Complet</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?= $totalPacks ?? 0 ?></div>
                <div class="stat-label">Packs Disponibles</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚙️</div>
                <div class="stat-number"><?= $totalServices ?? 0 ?></div>
                <div class="stat-label">Services Proposés</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
                <div class="stat-label">Utilisateurs Inscrits</div>
                <?php if (!empty($inscriptionsRecentes)): ?>
                    <div class="stat-trend trend-up">
                        +<?= array_sum(array_column($inscriptionsRecentes, 'count')) ?> cette semaine
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛏️</div>
                <div class="stat-number"><?= $totalHebergements ?? 0 ?></div>
                <div class="stat-label">Hébergements</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📍</div>
                <div class="stat-number"><?= $totalPointsArret ?? 0 ?></div>
                <div class="stat-label">Points d'Arrêt</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-number"><?= $commandeStats['total'] ?? 0 ?></div>
                <div class="stat-label">Total Commandes</div>
                <?php if (isset($commandeStats['payees']) && $commandeStats['total'] > 0): ?>
                    <div class="stat-trend <?= $commandeStats['payees']/$commandeStats['total'] > 0.5 ? 'trend-up' : 'trend-down' ?>">
                        <?= round(($commandeStats['payees']/$commandeStats['total']) * 100, 1) ?>% payées
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (isset($totalReservations)): ?>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number"><?= $totalReservations ?></div>
                <div class="stat-label">Réservations</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($mostExpensivePack) && $mostExpensivePack): ?>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number"><?= number_format($mostExpensivePack['prix'], 0) ?>€</div>
                <div class="stat-label">Pack Premium</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section Management Grid et tout le reste du contenu identique au fichier original... -->
        <!-- Je vais juste montrer la fin avec le script externe -->

        <!-- Footer avec informations système -->
        <div class="recent-activity" style="margin-top: 30px;">
            <h3>Informations Système</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 15px;">
                <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    <div style="font-size: 1.5em; font-weight: bold; color: #333;" class="current-time live-time" id="system-time">
                        Chargement...
                    </div>
                    <div style="color: #666; font-size: 0.9em;">Heure locale</div>
                </div>
                <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    <div style="font-size: 1.2em; font-weight: bold; color: #333;">
                        PHP <?= phpversion() ?>
                    </div>
                    <div style="color: #666; font-size: 0.9em;">Version PHP</div>
                </div>
                <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    <div style="font-size: 1.2em; font-weight: bold; color: #333;">
                        <?= round(memory_get_usage(true) / 1024 / 1024, 2) ?> MB
                    </div>
                    <div style="color: #666; font-size: 0.9em;">Mémoire utilisée</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Inclusion du fichier JavaScript externe -->
    <script src="<?= BASE_PATH ?>/assets/js/KayakDashboardManager.js"></script>
</body>
</html>