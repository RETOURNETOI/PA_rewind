<?php 
// --- Configuration du fuseau horaire ---
date_default_timezone_set('Europe/Paris');

// --- Vérification des droits admin ---
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}

// Ajout d’un fallback si BASE_PATH n’existe pas
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

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

// --- Connexion et contrôleurs ---
$connexion = new Connexion();
$db = $connexion->getPDO();
$packCtrl = new PackController();
$serviceCtrl = new ServiceController();
$userCtrl = new UtilisateurController();
$hebergementCtrl = new HebergementController();
$pointCtrl = new PointArretController();
$commandeCtrl = new CommandeController();

try {
    $stats = new DashboardStats($db);
    $totalPacks = count($packCtrl->getAll());
    $totalServices = count($serviceCtrl->getAll());
    $totalUsers = count($userCtrl->AfficherTous());
    $totalHebergements = count($hebergementCtrl->getAll());
    $totalPointsArret = count($pointCtrl->getAll());
    $totalCommandes = count($commandeCtrl->getAll());
    $commandeStats = $stats->getCommandeStats();
    $inscriptionsRecentes = $stats->getRecentInscriptions();
    $totalReservations = $stats->getTotalReservations();
    $mostExpensivePack = $stats->getMostExpensivePack();
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
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/assets/css/dashboard.css">
</head>
<body>
    <div class="container">

        <!-- Barre du haut -->
        <div class="top-bar">
            <div class="admin-info">
                Bienvenue <?= isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'Admin' ?>
                • <span class="current-time live-time" id="header-time">Chargement...</span>
            </div>
            <div>
                <a href="<?= BASE_PATH ?>/" class="action-btn">Home</a>
                <a href="<?= BASE_PATH ?>/gestionuser" class="action-btn">⚙️ Utilisateurs</a>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>Dashboard Admin</h1>
            <p>Panneau de contrôle - Kayak Trip</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?= $totalPacks ?? 0 ?></div>
                <div class="stat-label">Packs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚙️</div>
                <div class="stat-number"><?= $totalServices ?? 0 ?></div>
                <div class="stat-label">Services</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
        </div>

        <!-- Footer infos -->
        <div class="recent-activity" style="margin-top: 30px;">
            <h3>Informations Système</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div style="text-align: center;">
                    <div id="system-time" class="current-time live-time" style="font-size: 1.5em;">Chargement...</div>
                    <div>Heure locale</div>
                </div>
                <div style="text-align: center;">
                    <div id="footer-time" class="current-time" style="font-size: 1.2em;">Chargement...</div>
                    <div>Mise à jour</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script chargé après HTML (chemin absolu vers /assets/js) -->
    <script src="<?= BASE_PATH ?>/assets/js/KayakDashboardManager.js"></script>
</body>
</html>