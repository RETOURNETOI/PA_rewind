<?php
// dashboard_admin.php

// --- Définition du chemin de base de l'application ---
// Adapter ce chemin à votre structure de projet et URL
// define('BASE_PATH', '/PA_rewind/src');

// --- Configuration du fuseau horaire ---
date_default_timezone_set('Europe/Paris');

// --- Vérification des droits admin ---
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirection vers la page de connexion si l'utilisateur n'est pas admin
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}

// --- Inclusion des fichiers nécessaires ---
require_once __DIR__ . '/../../bdd/Connexion.php';
require_once __DIR__ . '/../../controller/PackController.php';
require_once __DIR__ . '/../../controller/ServiceController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/HebergementController.php';
require_once __DIR__ . '/../../controller/PointArretController.php';
require_once __DIR__ . '/../../controller/CommandeController.php';
require_once __DIR__ . '/../../controller/DashboardStats.php';

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
    $stats = new DashboardStats($db);
    
    $totalPacks = count($packCtrl->getAll());
    $totalServices = count($serviceCtrl->getAll());
    $totalUsers = count($userCtrl->AfficherTous());
    $totalHebergements = count($hebergementCtrl->getAll());
    $totalPointsArret = count($pointCtrl->getAll());
    $totalCommandes = count($commandeCtrl->getAll());

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

        .deco-btn {
            padding: 8px 16px;
            background: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .deco-btn:hover {
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

        /* Style pour l'heure en temps réel */
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
                👋 Bienvenue <?= isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'Admin' ?>
                <span style="color: #999;">• <span class="current-time live-time" id="header-time">Chargement...</span></span>
            </div>
            <div>
                <a href="<?= BASE_PATH ?>/" class="action-btn">home</a>
                <a href="<?= BASE_PATH ?>/gestionuser" class="action-btn">⚙️ gestion utilisateur</a>
                <a href="<?= BASE_PATH ?>/deco" class="btn btn-secondary">Déconnexion</a>
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>🚣‍♂️ Dashboard Admin</h1>
            <p>Panneau de contrôle - Kayak Trip Management System</p>
            <div class="quick-actions">
                <a href="<?= BASE_PATH ?>/admintest" class="action-btn">➕ Gestion Pack</a>
                <a href="<?= BASE_PATH ?>/gestionuser" class="action-btn">👤 Gestion Utilisateur</a>
                <a href="<?= BASE_PATH ?>/listepointsarret" class="action-btn">📊 Rapport Complet</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                ⚠️ <?= htmlspecialchars($error) ?>
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

        <!-- Alertes et notifications importantes -->
        <div class="charts-grid" style="margin-bottom: 20px;">
            <div class="chart-card" style="border-left: 4px solid #ffd43b;">
                <h3 class="chart-title">⚠️ Actions Requises</h3>
                <div style="space-y: 10px;">
                    <div style="padding: 10px; background: rgba(255, 212, 59, 0.1); border-radius: 8px; margin-bottom: 10px;">
                        <strong>Chat en temps réel :</strong> Système de messagerie client-commercial à implémenter
                    </div>
                    <div style="padding: 10px; background: rgba(255, 107, 107, 0.1); border-radius: 8px; margin-bottom: 10px;">
                        <strong>Vérification email :</strong> Système de validation par email manquant
                    </div>
                    <div style="padding: 10px; background: rgba(51, 154, 240, 0.1); border-radius: 8px; margin-bottom: 10px;">
                        <strong>Moteur recherche :</strong> Fonction de recherche avec fetch à développer
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="chart-title">📊 Graphique Taux d'Occupation</h3>
                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 8px;">
                    <div style="text-align: center; color: #666;">
                        <div style="font-size: 48px; margin-bottom: 10px;">📈</div>
                        <div>Graphique à implémenter</div>
                        <div style="font-size: 0.8em; margin-top: 5px;">Visualisation occupation hébergements</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <!-- Répartition des utilisateurs -->
            <?php if (isset($usersByRole) && !empty($usersByRole)): ?>
            <div class="chart-card">
                <h3 class="chart-title">👥 Répartition des Utilisateurs</h3>
                <?php foreach ($usersByRole as $role): 
                    $percentage = ($totalUsers > 0) ? ($role['count'] / $totalUsers) * 100 : 0;
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span><?= ucfirst($role['role']) ?></span>
                        <span><?= $role['count'] ?> (<?= round($percentage, 1) ?>%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Statut des commandes -->
            <?php if (isset($commandeStats)): ?>
            <div class="chart-card">
                <h3 class="chart-title">📊 Statut des Commandes</h3>
                <?php 
                $statuts = [
                    'payees' => ['label' => 'Payées', 'color' => '#51cf66'],
                    'confirmees' => ['label' => 'Confirmées', 'color' => '#339af0'], 
                    'en_attente' => ['label' => 'En attente', 'color' => '#ffd43b'],
                    'annulees' => ['label' => 'Annulées', 'color' => '#ff6b6b']
                ];
                foreach ($statuts as $key => $statut):
                    if ($commandeStats[$key] > 0):
                        $percentage = ($commandeStats['total'] > 0) ? ($commandeStats[$key] / $commandeStats['total']) * 100 : 0;
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span><?= $statut['label'] ?></span>
                        <span><?= $commandeStats[$key] ?> (<?= round($percentage, 1) ?>%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percentage ?>%; background: <?= $statut['color'] ?>"></div>
                    </div>
                </div>
                <?php 
                    endif;
                endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Types d'hébergements -->
            <?php if (isset($hebergementsByType) && !empty($hebergementsByType)): ?>
            <div class="chart-card">
                <h3 class="chart-title">🏠 Types d'Hébergements</h3>
                <?php foreach ($hebergementsByType as $type): 
                    $percentage = ($totalHebergements > 0) ? ($type['count'] / $totalHebergements) * 100 : 0;
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span><?= ucfirst($type['type']) ?></span>
                        <span><?= $type['count'] ?> (<?= round($percentage, 1) ?>%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section de gestion principale -->
        <div class="management-grid">
            <div class="management-card">
                <h3>📦 Gestion des Packs</h3>
                <p>Créez des itinéraires préconstruits avec hébergements inclus. Gérez les prix et descriptions pour vos offres complètes.</p>
                <a href="admintest.php" class="btn">Gérer les Packs</a>
                <a href="#" class="btn btn-secondary">Créer un Pack</a>
            </div>
            
            <div class="management-card">
                <h3>⚙️ Services Complémentaires</h3>
                <p>Transport bagages, paniers garnis, location matériel... Gérez tous vos services additionnels.</p>
                <a href="admintest.php#services" class="btn">Gérer les Services</a>
            </div>
            
            <div class="management-card">
                <h3>👥 Gestion des Utilisateurs</h3>
                <p>Supervisez les comptes clients, commerciaux et admin. Gérez les rôles et permissions.</p>
                <a href="gestionuser.php" class="btn">Gérer les Utilisateurs</a>
            </div>
            
            <div class="management-card">
                <h3>🛏️ Hébergements & Disponibilités</h3>
                <p>Gérez les hébergements par point d'arrêt. Contrôlez les fermetures pour travaux et disponibilités.</p>
                <a href="/mes_hebergement" class="btn">Gérer les Hébergements</a>
                <a href="/mes_hebergement" class="btn btn-secondary">Planifier Fermetures</a>
            </div>
            
            <div class="management-card">
                <h3>📍 Points d'Arrêt Loire</h3>
                <p>Administrez les étapes le long de la Loire. Ajoutez descriptions, coordonnées GPS et attractions.</p>
                <a href="/gestionpointsarret" class="btn">Gérer les Points</a>
            </div>
            
            <div class="management-card">
                <h3>🛒 Commandes & Réservations</h3>
                <p>Suivez toutes les réservations clients : packs, hébergements individuels et services complémentaires.</p>
                <a href="#" class="btn">Voir les Commandes</a>
                <a href="#" class="btn btn-secondary">Graphique Occupation</a>
            </div>

            <div class="management-card">
                <h3>💰 Offres Promotionnelles</h3>
                <p>Créez des codes de réduction première réservation et offres saisonnières. Gérez les plages tarifaires été.</p>
                <a href="#" class="btn">Gérer les Promos</a>
                <a href="#" class="btn btn-secondary">Tarifs Saisonniers</a>
            </div>
            
            <div class="management-card">
                <h3>💬 Service Commercial & Chat</h3>
                <p>Gérez la messagerie temps réel avec les clients. Supervisez les conversations commerciales.</p>
                <a href="#" class="btn">Messages Clients</a>
                <a href="#" class="btn btn-secondary">Statut Commercial</a>
            </div>

            <div class="management-card">
                <h3>📧 Newsletter & Communication</h3>
                <p>Gérez l'envoi de newsletters et la communication marketing vers vos clients inscrits.</p>
                <a href="#" class="btn">Gérer Newsletter</a>
                <a href="#" class="btn btn-secondary">Campagnes Email</a>
            </div>
        </div>

        <!-- Top hébergements -->
        <?php if (isset($topHebergements) && !empty($topHebergements)): ?>
        <div class="recent-activity">
            <h3>🏆 Top Hébergements Premium</h3>
            <?php foreach ($topHebergements as $heb): ?>
            <div class="activity-item">
                <div class="activity-icon">🛏️</div>
                <div class="activity-content">
                    <div class="activity-title"><?= htmlspecialchars($heb['nom']) ?></div>
                    <div class="activity-subtitle">
                        <?= ucfirst($heb['type']) ?> • <?= htmlspecialchars($heb['point_nom']) ?>
                    </div>
                    <div class="activity-meta"><?= number_format($heb['prix_nuit'], 2) ?>€ / nuit</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Activité récente -->
        <?php if (isset($recentUsers) && !empty($recentUsers)): ?>
        <div class="recent-activity">
            <h3>🆕 Derniers utilisateurs inscrits</h3>
            <?php foreach ($recentUsers as $user): ?>
            <div class="activity-item">
                <div class="activity-icon">👤</div>
                <div class="activity-content">
                    <div class="activity-title">
                        <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                        <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
                    </div>
                    <div class="activity-subtitle">
                        <?= htmlspecialchars($user['email']) ?>
                        • <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                    </div>
                    <div class="activity-meta">
                        Il y a <?= $user['jours_depuis_inscription'] ?> jour<?= $user['jours_depuis_inscription'] > 1 ? 's' : '' ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Progression du projet -->
        <div class="recent-activity">
            <h3>🚀 État d'avancement du projet</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                <div style="padding: 15px; background: rgba(51, 207, 102, 0.1); border-radius: 10px; border-left: 4px solid #33cf66;">
                    <h4 style="color: #2e7d2e; margin-bottom: 10px;">✅ Fonctionnalités complètes</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #666;">
                        <li>Architecture MVC</li>
                        <li>Gestion utilisateurs avec rôles</li>
                        <li>CRUD Packs & Services</li>
                        <li>Système réservation basique</li>
                        <li>Interface admin fonctionnelle</li>
                    </ul>
                </div>
                <div style="padding: 15px; background: rgba(255, 212, 59, 0.1); border-radius: 10px; border-left: 4px solid #ffd43b;">
                    <h4 style="color: #b8860b; margin-bottom: 10px;">⚠️ En développement</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #666;">
                        <li>Interface client responsive</li>
                        <li>Composition itinéraires libres</li>
                        <li>Gestion des disponibilités</li>
                        <li>Système de recherche</li>
                    </ul>
                </div>
                <div style="padding: 15px; background: rgba(255, 107, 107, 0.1); border-radius: 10px; border-left: 4px solid #ff6b6b;">
                    <h4 style="color: #d63384; margin-bottom: 10px;">❌ À implémenter</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #666;">
                        <li>Chat temps réel</li>
                        <li>Vérification email</li>
                        <li>Codes promotionnels</li>
                        <li>Graphiques occupation</li>
                        <li>Newsletter</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <?php if (isset($mostExpensivePack, $mostExpensiveService)): ?>
        <div class="recent-activity" style="margin-top: 30px;">
            <h3>💎 Produits Premium</h3>
            <div class="activity-item">
                <div class="activity-icon">📦</div>
                <div class="activity-content">
                    <div class="activity-title">Pack le plus cher</div>
                    <div class="activity-subtitle">
                        <?= htmlspecialchars($mostExpensivePack['nom']) ?> - <?= number_format($mostExpensivePack['prix'], 2) ?>€
                    </div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">⚙️</div>
                <div class="activity-content">
                    <div class="activity-title">Service le plus cher</div>
                    <div class="activity-subtitle">
                        <?= htmlspecialchars($mostExpensiveService['nom']) ?> - <?= number_format($mostExpensiveService['prix'], 2) ?>€
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer avec informations système -->
        <div class="recent-activity" style="margin-top: 30px;">
            <h3>ℹ️ Informations Système</h3>
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

    <!-- Script pour la gestion du temps et animations -->
    <script>
        // Configuration du fuseau horaire et mise à jour de l'heure
        class TimeManager {
            constructor() {
                this.timezone = 'Europe/Paris';
                this.timeElements = [];
                this.updateInterval = null;
                this.init();
            }

            init() {
                this.findTimeElements();
                this.startTimeUpdates();
                this.updateTime(); // Mise à jour immédiate
            }

            findTimeElements() {
                this.timeElements = [
                    document.getElementById('header-time'),
                    document.getElementById('system-time'),
                    ...document.querySelectorAll('.current-time')
                ].filter(el => el !== null);
            }

            updateTime() {
                const now = new Date();
                
                // Format pour l'en-tête (complet)
                const fullTimeFormat = now.toLocaleString('fr-FR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: this.timezone
                });

                // Format pour l'heure système (court)
                const shortTimeFormat = now.toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: this.timezone
                });

                // Mise à jour des éléments
                const headerTime = document.getElementById('header-time');
                const systemTime = document.getElementById('system-time');

                if (headerTime) {
                    headerTime.textContent = fullTimeFormat;
                    this.animateTimeUpdate(headerTime);
                }

                if (systemTime) {
                    systemTime.textContent = shortTimeFormat;
                    this.animateTimeUpdate(systemTime);
                }

                // Mise à jour des autres éléments de temps
                document.querySelectorAll('.current-time:not(#header-time):not(#system-time)').forEach(el => {
                    el.textContent = shortTimeFormat;
                    this.animateTimeUpdate(el);
                });
            }

            animateTimeUpdate(element) {
                element.style.color = '#764ba2';
                setTimeout(() => {
                    element.style.color = '';
                }, 150);
            }

            startTimeUpdates() {
                if (this.updateInterval) {
                    clearInterval(this.updateInterval);
                }
                
                this.updateInterval = setInterval(() => {
                    this.updateTime();
                }, 1000);
            }

            destroy() {
                if (this.updateInterval) {
                    clearInterval(this.updateInterval);
                }
            }
        }

        // Gestionnaire des animations des barres de progression
        class ProgressBarManager {
            constructor() {
                this.progressBars = document.querySelectorAll('.progress-fill');
                this.init();
            }

            init() {
                this.animateProgressBars();
            }

            animateProgressBars() {
                this.progressBars.forEach((bar, index) => {
                    const targetWidth = bar.style.width;
                    
                    // Préparation de l'animation
                    bar.style.width = '0%';
                    bar.style.transition = 'none';
                    
                    // Animation échelonnée
                    setTimeout(() => {
                        bar.style.transition = 'width 1s cubic-bezier(0.4, 0, 0.2, 1)';
                        bar.style.width = targetWidth;
                    }, index * 150 + 500);
                });
            }
        }

        // Gestionnaire des effets de cartes
        class CardEffectsManager {
            constructor() {
                this.cards = document.querySelectorAll('.stat-card, .management-card, .chart-card');
                this.init();
            }

            init() {
                this.setupCardHoverEffects();
                this.animateCardsOnLoad();
            }

            setupCardHoverEffects() {
                this.cards.forEach(card => {
                    card.addEventListener('mouseenter', () => this.animateCardEnter(card));
                    card.addEventListener('mouseleave', () => this.animateCardLeave(card));
                });
            }

            animateCardEnter(card) {
                card.style.transform = 'translateY(-8px) scale(1.02)';
                card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            }

            animateCardLeave(card) {
                card.style.transform = '';
                card.style.boxShadow = '';
            }

            animateCardsOnLoad() {
                this.cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    
                    setTimeout(() => {
                        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100 + 300);
                });
            }
        }

        // Gestionnaire principal du dashboard
        class DashboardManager {
            constructor() {
                this.timeManager = null;
                this.progressBarManager = null;
                this.cardEffectsManager = null;
                this.init();
            }

            init() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.initializeComponents();
                });
            }

            initializeComponents() {
                this.timeManager = new TimeManager();
                this.progressBarManager = new ProgressBarManager();
                this.cardEffectsManager = new CardEffectsManager();
                
                console.log('Dashboard Kayak Trip initialisé avec succès');
            }

            destroy() {
                if (this.timeManager) this.timeManager.destroy();
            }
        }

        // Initialisation automatique
        const dashboardManager = new DashboardManager();

        // Gestion de la visibilité de la page pour économiser les ressources
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Page cachée - économie d\'énergie');
            } else {
                console.log('Page visible - reprise normale');
            }
        });

        // Nettoyage avant fermeture
        window.addEventListener('beforeunload', () => {
            dashboardManager.destroy();
        });
    </script>
</body>
</html>