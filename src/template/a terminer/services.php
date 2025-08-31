<?php 
date_default_timezone_set('Europe/Paris');

session_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/connexion');
    exit;
}

require_once __DIR__ . '/../controller/ServiceController.php';
require_once __DIR__ . '/../controller/PackController.php';
require_once __DIR__ . '/../controller/PanierController.php';

$serviceController = new ServiceController();
$packController = new PackController();
$panierController = new PanierController();

$services = $serviceController->getAll();
$packs = $packController->getAll();

$panierItems = $panierController->getPanierByUserId($_SESSION['user_id']);

$userName = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Complémentaires - Kayak Trip Loire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-gradient-start: #667eea;
            --bg-gradient-end: #764ba2;
            --card-bg: rgba(255, 255, 255, 0.95);
            --card-border: rgba(255, 255, 255, 0.18);
            --text-primary: #333;
            --text-secondary: #666;
            --text-tertiary: #555;
            --border-color: rgba(102, 126, 234, 0.2);
            --shadow-light: rgba(31, 38, 135, 0.37);
            --shadow-hover: rgba(31, 38, 135, 0.5);
            --feature-bg: rgba(255, 255, 255, 0.9);
            --footer-bg: rgba(255, 255, 255, 0.1);
            --footer-text: rgba(255, 255, 255, 0.8);
        }

        body.dark-mode {
            --bg-gradient-start: #1a1a2e;
            --bg-gradient-end: #16213e;
            --card-bg: rgba(40, 44, 63, 0.95);
            --card-border: rgba(100, 120, 180, 0.3);
            --text-primary: #e8e8e8;
            --text-secondary: #b8b8b8;
            --text-tertiary: #a0a0a0;
            --border-color: rgba(150, 150, 200, 0.3);
            --shadow-light: rgba(10, 15, 25, 0.5);
            --shadow-hover: rgba(10, 15, 25, 0.7);
            --feature-bg: rgba(35, 40, 58, 0.9);
            --footer-bg: rgba(20, 25, 40, 0.3);
            --footer-text: rgba(200, 200, 220, 0.9);
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            border-radius: 50px;
            padding: 12px 16px;
            cursor: pointer;
            font-size: 1.2em;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 1000;
            color: var(--text-primary);
            box-shadow: 0 4px 15px var(--shadow-light);
        }

        .theme-toggle:hover {
            background: var(--feature-bg);
            transform: scale(1.1);
            box-shadow: 0 6px 20px var(--shadow-hover);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .message-container {
            margin-bottom: 20px;
        }

        .message {
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            animation: messageSlideIn 0.8s ease-out;
        }

        .message-success {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .message-error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5em;
            font-weight: bold;
            color: var(--text-primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .live-time {
            color: #667eea;
            font-weight: 500;
            font-size: 0.9em;
        }

        body.dark-mode .live-time {
            color: #9ca3f3;
        }

        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        body.dark-mode .btn-primary {
            background: linear-gradient(45deg, #9ca3f3, #b19cd9);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        body.dark-mode .btn-secondary {
            background: rgba(100, 120, 180, 0.2);
            border: 1px solid rgba(150, 150, 200, 0.4);
        }

        .btn-success {
            background: linear-gradient(45deg, #51cf66, #40c057);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
        }

        .page-header h1 {
            font-size: 2.5em;
            color: var(--text-primary);
            margin-bottom: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.dark-mode .page-header h1 {
            background: linear-gradient(45deg, #9ca3f3, #b19cd9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 1.1em;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 40px;
        }

        .content-panel {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
        }

        .sidebar {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .section-title {
            font-size: 1.8em;
            color: var(--text-primary);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .services-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 40px;
        }

        .service-card {
            background: var(--feature-bg);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px var(--shadow-light);
        }

        body.dark-mode .service-card:hover {
            border-color: #9ca3f3;
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .service-info h3 {
            color: var(--text-primary);
            font-size: 1.3em;
            margin-bottom: 8px;
        }

        .service-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .service-price {
            color: #51cf66;
            font-size: 1.4em;
            font-weight: bold;
        }

        .service-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: transparent;
            color: #667eea;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        body.dark-mode .quantity-btn {
            border-color: #9ca3f3;
            color: #9ca3f3;
        }

        .quantity-btn:hover {
            background: #667eea;
            color: white;
        }

        body.dark-mode .quantity-btn:hover {
            background: #9ca3f3;
            color: white;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 5px;
            background: var(--feature-bg);
            color: var(--text-primary);
        }

        .packs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .pack-card {
            background: var(--feature-bg);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
        }

        .pack-card:hover {
            border-color: #ffa726;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px var(--shadow-light);
        }

        .pack-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .pack-info h3 {
            color: var(--text-primary);
            font-size: 1.3em;
            margin-bottom: 8px;
        }

        .pack-price {
            color: #ffa726;
            font-size: 1.4em;
            font-weight: bold;
        }

        .pack-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .pack-duration {
            background: rgba(255, 167, 38, 0.1);
            color: #ffa726;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 15px;
        }

        .panier-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panier-count {
            background: #51cf66;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
        }

        .panier-item {
            background: var(--feature-bg);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #51cf66;
        }

        .panier-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .panier-item h5 {
            color: var(--text-primary);
            font-size: 0.95em;
        }

        .panier-item-price {
            color: #51cf66;
            font-weight: bold;
            font-size: 0.9em;
        }

        .panier-item-details {
            color: var(--text-secondary);
            font-size: 0.8em;
        }

        .remove-item {
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 0.7em;
            margin-left: 10px;
        }

        .panier-total {
            background: linear-gradient(45deg, #51cf66, #40c057);
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 1.1em;
        }

        .empty-panier {
            text-align: center;
            color: var(--text-secondary);
            font-style: italic;
            padding: 20px;
        }

        .footer {
            background: var(--footer-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            color: var(--footer-text);
            margin-top: 40px;
            border: 1px solid var(--card-border);
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .sidebar {
                position: static;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .container {
                padding: 15px;
            }

            .content-panel, .sidebar {
                padding: 20px;
            }

            .theme-toggle {
                top: 10px;
                right: 10px;
                padding: 10px 12px;
                font-size: 1em;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .content-panel, .sidebar {
            animation: fadeInUp 0.6s ease forwards;
        }

        .sidebar {
            animation-delay: 0.2s;
        }

        * {
            transition: background-color 0.3s ease, 
                       color 0.3s ease, 
                       border-color 0.3s ease, 
                       box-shadow 0.3s ease !important;
        }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle">🌙</button>

    <div class="container">
        <?php if ($successMessage || $errorMessage): ?>
        <div class="message-container">
            <?php if ($successMessage): ?>
                <div class="message message-success">
                    ✅ <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="message message-error">
                    ⚠️ <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <header class="header">
            <div class="logo">
                <a href="<?= BASE_PATH ?>/" style="text-decoration: none; color: inherit;">
                    🚣‍♂️ Kayak Trip Loire
                </a>
            </div>
            <div class="user-info">
                <span class="live-time" id="current-time">Chargement...</span>
                <span>Bonjour, <?= htmlspecialchars($userName) ?></span>
                <a href="<?= BASE_PATH ?>/composer_itineraire" class="btn btn-secondary">🗺️ Itinéraires</a>
                <a href="<?= BASE_PATH ?>/profil" class="btn btn-secondary">Mon Profil</a>
                <a href="<?= BASE_PATH ?>/" class="btn btn-primary">Accueil</a>
            </div>
        </header>

        <div class="page-header">
            <h1>🎒 Services Complémentaires</h1>
            <p>Enrichissez votre aventure avec nos services additionnels et découvrez nos packs tout inclus</p>
        </div>

        <div class="main-content">
            <div class="content-panel">
                <section>
                    <h2 class="section-title">⚙️ Services Additionnels</h2>
                    <div class="services-grid">
                        <?php foreach ($services as $service): ?>
                            <div class="service-card" data-service-id="<?= $service['id_service'] ?>">
                                <div class="service-header">
                                    <div class="service-info">
                                        <h3><?= htmlspecialchars($service['nom']) ?></h3>
                                        <div class="service-description">
                                            <?= htmlspecialchars($service['description'] ?? 'Service complémentaire pour votre aventure') ?>
                                        </div>
                                    </div>
                                    <div class="service-price"><?= number_format($service['prix'], 2) ?>€</div>
                                </div>
                                
                                <div class="service-actions">
                                    <div class="quantity-control">
                                        <button class="quantity-btn" onclick="changeQuantity('service', <?= $service['id_service'] ?>, -1)">-</button>
                                        <input type="number" min="0" max="10" value="0" 
                                               class="quantity-input" 
                                               id="service-qty-<?= $service['id_service'] ?>"
                                               onchange="updateServiceQuantity(<?= $service['id_service'] ?>, this.value)">
                                        <button class="quantity-btn" onclick="changeQuantity('service', <?= $service['id_service'] ?>, 1)">+</button>
                                    </div>
                                    <button class="btn btn-success" onclick="addServiceToPanier(<?= $service['id_service'] ?>)">
                                        Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section>
                    <h2 class="section-title">📦 Packs Tout Inclus</h2>
                    <div class="packs-grid">
                        <?php foreach ($packs as $pack): ?>
                            <div class="pack-card" data-pack-id="<?= $pack['id_pack'] ?>">
                                <div class="pack-header">
                                    <div class="pack-info">
                                        <h3><?= htmlspecialchars($pack['nom']) ?></h3>
                                        <div class="pack-duration"><?= $pack['duree'] ?? '3' ?> jours</div>
                                        <div class="pack-description">
                                            <?= htmlspecialchars($pack['description'] ?? 'Pack complet avec hébergements et services inclus') ?>
                                        </div>
                                    </div>
                                    <div class="pack-price"><?= number_format($pack['prix'], 2) ?>€</div>
                                </div>
                                
                                <div class="service-actions">
                                    <button class="btn btn-primary" onclick="addPackToPanier(<?= $pack['id_pack'] ?>)">
                                        🛒 Réserver ce pack
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <div class="sidebar">
                <div class="panier-header">
                    <h3>🛒 Mon Panier</h3>
                    <span class="panier-count" id="panierCount">0</span>
                </div>
                
                <div id="panierItems">
                    <?php if (empty($panierItems)): ?>
                        <div class="empty-panier">
                            Votre panier est vide<br>
                            Ajoutez des services ou un pack pour commencer !
                        </div>
                    <?php else: ?>
                        <?php foreach ($panierItems as $item): ?>
                            <div class="panier-item" data-item-id="<?= $item['id'] ?>">
                                <div class="panier-item-header">
                                    <h5><?= htmlspecialchars($item['nom']) ?></h5>
                                    <span class="panier-item-price"><?= number_format($item['prix_total'], 2) ?>€</span>
                                    <button class="remove-item" onclick="removeFromPanier(<?= $item['id'] ?>)">×</button>
                                </div>
                                <div class="panier-item-details">
                                    <?= $item['type'] === 'service' ? 'Service' : 'Pack' ?> • Qté: <?= $item['quantite'] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($panierItems)): ?>
                    <div class="panier-total" id="panierTotal">
                        Total : <?= number_format(array_sum(array_column($panierItems, 'prix_total')), 2) ?>€
                    </div>
                    
                    <button class="btn btn-success" style="width: 100%; margin-top: 15px;" onclick="proceedToCheckout()">
                        🎯 Finaliser ma commande
                    </button>
                    
                    <button class="btn btn-secondary" style="width: 100%; margin-top: 10px;" onclick="clearPanier()">
                        🗑️ Vider le panier
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <footer class="footer">
            <p>© 2025 Kayak Trip Loire. Enrichissez votre aventure avec nos services personnalisés.</p>
        </footer>
    </div>

    <script>
        function initTheme() {
            const themeToggle = document.getElementById('themeToggle');
            
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    this.textContent = '☀️';
                    try {
                        localStorage.setItem('theme', 'dark');
                    } catch(e) {
                        console.log('localStorage non disponible');
                    }
                } else {
                    this.textContent = '🌙';
                    try {
                        localStorage.setItem('theme', 'light');
                    } catch(e) {
                        console.log('localStorage non disponible');
                    }
                }
            });

            try {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                    themeToggle.textContent = '☀️';
                }
            } catch(e) {
                console.log('localStorage non disponible');
            }
        }

        class TimeManager {
            constructor() {
                this.timezone = 'Europe/Paris';
                this.init();
            }

            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
            }

            updateTime() {
                const now = new Date();
                const timeString = now.toLocaleString('fr-FR', {
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: this.timezone
                });

                const currentTimeEl = document.getElementById('current-time');
                if (currentTimeEl) {
                    currentTimeEl.textContent = timeString;
                }
            }
        }

        class PanierManager {
            constructor() {
                this.init();
            }

            init() {
                this.updatePanierCount();
            }

            updatePanierCount() {
                const items = document.querySelectorAll('.panier-item');
                const count = items.length;
                const countEl = document.getElementById('panierCount');
                if (countEl) {
                    countEl.textContent = count;
                }
            }

            async addItem(type, itemId, quantity = 1) {
                try {
                    const response = await fetch('<?= BASE_PATH ?>/panier/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            type: type,
                            item_id: itemId,
                            quantity: quantity
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showMessage('Ajouté au panier avec succès !', 'success');
                        this.refreshPanier();
                    } else {
                        this.showMessage(result.message || 'Erreur lors de l\'ajout', 'error');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showMessage('Erreur de connexion', 'error');
                }
            }

            async removeItem(itemId) {
                try {
                    const response = await fetch('<?= BASE_PATH ?>/panier/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            item_id: itemId
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showMessage('Article retiré du panier', 'success');
                        this.refreshPanier();
                    } else {
                        this.showMessage(result.message || 'Erreur lors de la suppression', 'error');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showMessage('Erreur de connexion', 'error');
                }
            }

            async clearPanier() {
                if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                    return;
                }

                try {
                    const response = await fetch('<?= BASE_PATH ?>/panier/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showMessage('Panier vidé', 'success');
                        this.refreshPanier();
                    } else {
                        this.showMessage(result.message || 'Erreur', 'error');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showMessage('Erreur de connexion', 'error');
                }
            }

            async refreshPanier() {
                try {
                    const response = await fetch('<?= BASE_PATH ?>/panier/get', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.updatePanierDisplay(result.items, result.total);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            }

            updatePanierDisplay(items, total) {
                const panierItems = document.getElementById('panierItems');
                const panierTotal = document.getElementById('panierTotal');
                const panierCount = document.getElementById('panierCount');

                if (items.length === 0) {
                    panierItems.innerHTML = `
                        <div class="empty-panier">
                            Votre panier est vide<br>
                            Ajoutez des services ou un pack pour commencer !
                        </div>
                    `;
                    
                    if (panierTotal) panierTotal.style.display = 'none';
                    panierCount.textContent = '0';
                } else {
                    panierItems.innerHTML = items.map(item => `
                        <div class="panier-item" data-item-id="${item.id}">
                            <div class="panier-item-header">
                                <h5>${item.nom}</h5>
                                <span class="panier-item-price">${parseFloat(item.prix_total).toFixed(2)}€</span>
                                <button class="remove-item" onclick="removeFromPanier(${item.id})">×</button>
                            </div>
                            <div class="panier-item-details">
                                ${item.type === 'service' ? 'Service' : 'Pack'} • Qté: ${item.quantite}
                            </div>
                        </div>
                    `).join('');

                    if (panierTotal) {
                        panierTotal.style.display = 'block';
                        panierTotal.textContent = `Total : ${parseFloat(total).toFixed(2)}€`;
                    }
                    
                    panierCount.textContent = items.length;
                }

                this.updatePanierCount();
            }

            showMessage(message, type) {
                const messageContainer = document.querySelector('.message-container') || document.body;
                const messageEl = document.createElement('div');
                messageEl.className = `message message-${type}`;
                messageEl.innerHTML = `${type === 'success' ? '✅' : '⚠️'} ${message}`;
                
                if (document.querySelector('.message-container')) {
                    messageContainer.appendChild(messageEl);
                } else {
                    messageContainer.insertBefore(messageEl, messageContainer.firstChild);
                }

                setTimeout(() => {
                    messageEl.style.opacity = '0';
                    messageEl.style.transform = 'translateY(-20px)';
                    setTimeout(() => messageEl.remove(), 300);
                }, 3000);
            }
        }

        const panierManager = new PanierManager();

        function changeQuantity(type, itemId, delta) {
            const input = document.getElementById(`${type}-qty-${itemId}`);
            const currentValue = parseInt(input.value) || 0;
            const newValue = Math.max(0, Math.min(10, currentValue + delta));
            input.value = newValue;
        }

        function updateServiceQuantity(serviceId, quantity) {
            const qty = Math.max(0, Math.min(10, parseInt(quantity) || 0));
            document.getElementById(`service-qty-${serviceId}`).value = qty;
        }

        function addServiceToPanier(serviceId) {
            const quantity = parseInt(document.getElementById(`service-qty-${serviceId}`).value) || 1;
            if (quantity <= 0) {
                panierManager.showMessage('Veuillez sélectionner une quantité', 'error');
                return;
            }
            panierManager.addItem('service', serviceId, quantity);
        }

        function addPackToPanier(packId) {
            panierManager.addItem('pack', packId, 1);
        }

        function removeFromPanier(itemId) {
            panierManager.removeItem(itemId);
        }

        function clearPanier() {
            panierManager.clearPanier();
        }

        function proceedToCheckout() {
            window.location.href = '<?= BASE_PATH ?>/checkout';
        }

        function autoHideMessages() {
            const messages = document.querySelectorAll('.message');
            messages.forEach((message, index) => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        message.remove();
                    }, 300);
                }, 5000 + (index * 500));
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const timeManager = new TimeManager();
            initTheme();
            autoHideMessages();

            setTimeout(() => {
                document.querySelectorAll('.service-card, .pack-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);

            console.log('🎒 Services Complémentaires Kayak Trip Loire initialisés');
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                console.log('Page visible - reprise normale');
            }
        });
    </script>
</body>
</html>