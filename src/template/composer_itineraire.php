<?php 
// composer_itineraire.php - Page de composition d'itinéraire

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrage de la session
session_start();

// Définir BASE_PATH si elle n'existe pas déjà
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/connexion');
    exit;
}

// Inclure les contrôleurs nécessaires
require_once __DIR__ . '/../controller/PointArretController.php';
require_once __DIR__ . '/../controller/HebergementController.php';
require_once __DIR__ . '/../controller/ServiceController.php';

// Récupérer les données
$pointController = new PointArretController();
$hebergementController = new HebergementController();
$serviceController = new ServiceController();

$pointsArret = $pointController->getAll();
$services = $serviceController->getAll();

// Variables pour l'affichage
$userName = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Composer votre itinéraire - Kayak Trip Loire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Variables CSS pour le thème */
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Toggle bouton mode sombre */
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

        /* Header */
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: #333;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        body.dark-mode .btn-secondary {
            background: rgba(100, 120, 180, 0.2);
            color: var(--text-primary);
            border: 1px solid rgba(150, 150, 200, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-bottom: 40px;
        }

        .composition-panel {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
        }

        .sidebar {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--shadow-light);
            border: 1px solid var(--card-border);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.5em;
            color: var(--text-primary);
            margin-bottom: 15px;
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.dark-mode .page-header h1 {
            background: linear-gradient(45deg, #9ca3f3, #b19cd9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: white;
            font-size: 1.1em;
        }

        .section-title {
            font-size: 1.5em;
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        /* Points d'arrêt */
        .points-grid {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }

        .point-card {
            background: var(--feature-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .point-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px var(--shadow-light);
        }

        .point-card.selected {
            border-color: #667eea;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            box-shadow: 0 5px 20px var(--shadow-light);
        }

        .point-card h4 {
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .point-card p {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .point-order {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: none;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8em;
        }

        .point-card.selected .point-order {
            display: flex;
        }

        /* Hébergements */
        .hebergements-section {
            margin-top: 30px;
            display: none;
        }

        .hebergements-section.active {
            display: block;
        }

        .hebergement-card {
            background: var(--feature-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .hebergement-card:hover {
            box-shadow: 0 3px 15px var(--shadow-light);
            transform: translateY(-1px);
        }

        .hebergement-card.selected {
            border-color: #51cf66;
            background: linear-gradient(45deg, rgba(81, 207, 102, 0.1), rgba(64, 192, 87, 0.1));
        }

        .hebergement-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .hebergement-nom {
            font-weight: 600;
            color: var(--text-primary);
        }

        .hebergement-prix {
            color: #51cf66;
            font-weight: bold;
        }

        .hebergement-details {
            font-size: 0.9em;
            color: var(--text-secondary);
        }

        /* Services */
        .services-section {
            margin-top: 30px;
        }

        .service-card {
            background: var(--feature-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .service-card:hover {
            box-shadow: 0 3px 15px var(--shadow-light);
        }

        .service-card.selected {
            border-color: #ffa726;
            background: linear-gradient(45deg, rgba(255, 167, 38, 0.1), rgba(255, 193, 7, 0.1));
        }

        .service-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-nom {
            font-weight: 600;
            color: var(--text-primary);
        }

        .service-prix {
            color: #ffa726;
            font-weight: bold;
        }

        /* Sidebar résumé */
        .resume-section h3 {
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .resume-item {
            background: var(--feature-bg);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 3px solid #667eea;
        }

        .resume-item h5 {
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .resume-item p {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .total-price {
            background: linear-gradient(45deg, #51cf66, #40c057);
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 1.1em;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95em;
            transition: all 0.3s ease;
            background: var(--feature-bg);
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Instructions */
        .instructions {
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .instructions h3 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .instructions ol {
            color: var(--text-secondary);
            padding-left: 20px;
        }

        .instructions li {
            margin: 5px 0;
        }

        /* Footer simplifié */
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

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .sidebar {
                position: static;
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

            .composition-panel, .sidebar {
                padding: 20px;
            }
        }

        /* Animations */
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

        .composition-panel, .sidebar {
            animation: fadeInUp 0.6s ease forwards;
        }

        .sidebar {
            animation-delay: 0.2s;
        }

        /* Transitions fluides */
        * {
            transition: background-color 0.3s ease, 
                       color 0.3s ease, 
                       border-color 0.3s ease, 
                       box-shadow 0.3s ease !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Bouton toggle mode sombre -->
        <button class="theme-toggle" id="themeToggle">🌙</button>

        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="<?= BASE_PATH ?>/" style="text-decoration: none; color: inherit;">
                    🚣‍♂️ Kayak Trip Loire
                </a>
            </div>
            <div class="user-info">
                <span class="live-time" id="current-time">Chargement...</span>
                <span>Bonjour, <?= htmlspecialchars($userName) ?></span>
                <a href="<?= BASE_PATH ?>/profil" class="btn btn-secondary">Mon Profil</a>
                <a href="<?= BASE_PATH ?>/" class="btn btn-primary">Accueil</a>
            </div>
        </header>

        <!-- Page Header -->
        <div class="page-header">
            <h1>Composer votre itinéraire</h1>
            <p>Créez votre parcours personnalisé sur la Loire en sélectionnant vos étapes et hébergements</p>
        </div>

        <div class="main-content">
            <!-- Panel principal de composition -->
            <div class="composition-panel">
                <!-- Instructions -->
                <div class="instructions">
                    <h3>📋 Comment composer votre itinéraire :</h3>
                    <ol>
                        <li>Sélectionnez vos points d'arrêt dans l'ordre souhaité</li>
                        <li>Choisissez un hébergement pour chaque étape</li>
                        <li>Ajoutez des services complémentaires si souhaité</li>
                        <li>Validez votre itinéraire personnalisé</li>
                    </ol>
                </div>

                <!-- Sélection des points d'arrêt -->
                <section>
                    <h2 class="section-title">📍 Sélectionnez vos étapes</h2>
                    <div class="points-grid" id="pointsGrid">
                        <?php foreach ($pointsArret as $point): ?>
                            <div class="point-card" 
                                 data-id="<?= $point['id_point'] ?>" 
                                 data-nom="<?= htmlspecialchars($point['nom']) ?>">
                                <h4><?= htmlspecialchars($point['nom']) ?></h4>
                                <p><?= htmlspecialchars($point['description'] ?? 'Point d\'arrêt sur la Loire') ?></p>
                                <div class="point-order"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Sélection des hébergements -->
                <section class="hebergements-section" id="hebergementsSection">
                    <h2 class="section-title">🏨 Choisissez vos hébergements</h2>
                    <div id="hebergementsContainer">
                        <!-- Les hébergements seront chargés dynamiquement -->
                    </div>
                </section>

                <!-- Services complémentaires -->
                <section class="services-section">
                    <h2 class="section-title">🎒 Services complémentaires</h2>
                    <div class="services-grid">
                        <?php foreach ($services as $service): ?>
                            <div class="service-card" 
                                 data-id="<?= $service['id_service'] ?>" 
                                 data-prix="<?= $service['prix'] ?>">
                                <div class="service-info">
                                    <div>
                                        <div class="service-nom"><?= htmlspecialchars($service['nom']) ?></div>
                                        <div style="font-size: 0.8em; color: var(--text-secondary);">
                                            <?= htmlspecialchars($service['description'] ?? '') ?>
                                        </div>
                                    </div>
                                    <div class="service-prix"><?= number_format($service['prix'], 2) ?>€</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar - Résumé -->
            <div class="sidebar">
                <div class="resume-section">
                    <h3>📋 Résumé de votre itinéraire</h3>
                    
                    <form id="itineraireForm" action="<?= BASE_PATH ?>/traiter_itineraire" method="post">
                        <label for="nom_itineraire" style="color: var(--text-primary); font-weight: 500; display: block; margin-bottom: 8px;">
                            Nom de votre itinéraire :
                        </label>
                        <input type="text" id="nom_itineraire" name="nom_itineraire" class="form-input" 
                               placeholder="Ex: Loire Adventure 2025" required>

                        <label for="date_debut" style="color: var(--text-primary); font-weight: 500; display: block; margin-bottom: 8px;">
                            Date de début :
                        </label>
                        <input type="date" id="date_debut" name="date_debut" class="form-input" required>

                        <label for="nb_personnes" style="color: var(--text-primary); font-weight: 500; display: block; margin-bottom: 8px;">
                            Nombre de personnes :
                        </label>
                        <input type="number" id="nb_personnes" name="nb_personnes" class="form-input" 
                               min="1" max="10" value="2" required>

                        <div id="etapesResume">
                            <h4 style="color: var(--text-secondary); margin: 15px 0 10px 0;">Étapes sélectionnées :</h4>
                            <div id="etapesList">
                                <p style="color: var(--text-secondary); font-style: italic;">Aucune étape sélectionnée</p>
                            </div>
                        </div>

                        <div id="servicesResume" style="display: none;">
                            <h4 style="color: var(--text-secondary); margin: 15px 0 10px 0;">Services ajoutés :</h4>
                            <div id="servicesList"></div>
                        </div>

                        <div class="total-price" id="totalPrice">
                            Total estimé : 0€
                        </div>

                        <!-- Champs cachés pour les données -->
                        <input type="hidden" name="etapes_data" id="etapes_data">
                        <input type="hidden" name="services_data" id="services_data">

                        <button type="submit" class="submit-btn" id="validerBtn" disabled>
                            🎯 Valider mon itinéraire
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2025 Kayak Trip Loire. Composez votre aventure personnalisée sur la Loire.</p>
        </footer>
    </div>

    <script>
        // Gestion de l'heure en temps réel
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

        // Gestionnaire d'itinéraire
        class ItineraireManager {
            constructor() {
                this.etapes = [];
                this.services = [];
                this.hebergements = {};
                this.totalPrice = 0;
                this.init();
            }

            init() {
                this.bindEvents();
                this.loadHebergements();
            }

            bindEvents() {
                // Sélection des points
                document.querySelectorAll('.point-card').forEach(card => {
                    card.addEventListener('click', () => this.togglePoint(card));
                });

                // Sélection des services
                document.querySelectorAll('.service-card').forEach(card => {
                    card.addEventListener('click', () => this.toggleService(card));
                });

                // Mise à jour du prix quand nb personnes change
                document.getElementById('nb_personnes').addEventListener('input', () => {
                    this.updateTotalPrice();
                });
            }

            togglePoint(card) {
                const pointId = parseInt(card.dataset.id);
                const pointNom = card.dataset.nom;

                if (card.classList.contains('selected')) {
                    // Désélectionner
                    this.removeEtape(pointId);
                    card.classList.remove('selected');
                } else {
                    // Sélectionner
                    this.addEtape(pointId, pointNom);
                    card.classList.add('selected');
                }

                this.updatePointOrders();
                this.updateResume();
                this.showHebergementsSection();
            }

            addEtape(pointId, pointNom) {
                this.etapes.push({
                    id: pointId,
                    nom: pointNom,
                    ordre: this.etapes.length + 1,
                    hebergement: null
                });
            }

            removeEtape(pointId) {
                this.etapes = this.etapes.filter(e => e.id !== pointId);
                delete this.hebergements[pointId];
                // Réorganiser les ordres
                this.etapes.forEach((etape, index) => {
                    etape.ordre = index + 1;
                });
            }

            updatePointOrders() {
                document.querySelectorAll('.point-card').forEach(card => {
                    const pointId = parseInt(card.dataset.id);
                    const etape = this.etapes.find(e => e.id === pointId);
                    const orderEl = card.querySelector('.point-order');
                    
                    if (etape) {
                        orderEl.textContent = etape.ordre;
                    }
                });
            }

            toggleService(card) {
                const serviceId = parseInt(card.dataset.id);
                const servicePrix = parseFloat(card.dataset.prix);
                const serviceNom = card.querySelector('.service-nom').textContent;

                if (card.classList.contains('selected')) {
                    // Désélectionner
                    this.services = this.services.filter(s => s.id !== serviceId);
                    card.classList.remove('selected');
                } else {
                    // Sélectionner
                    this.services.push({
                        id: serviceId,
                        nom: serviceNom,
                        prix: servicePrix
                    });
                    card.classList.add('selected');
                }

                this.updateResume();
            }

            async loadHebergements() {
                // Cette fonction sera appelée pour chaque point sélectionné
                // Pour l'instant, on simule des hébergements
                this.hebergementsData = <?= json_encode($hebergementController->getAll()) ?>;
            }

            showHebergementsSection() {
                const section = document.getElementById('hebergementsSection');
                const container = document.getElementById('hebergementsContainer');

                if (this.etapes.length > 0) {
                    section.classList.add('active');
                    this.renderHebergements(container);
                } else {
                    section.classList.remove('active');
                }
            }

            renderHebergements(container) {
                container.innerHTML = '';

                this.etapes.forEach(etape => {
                    const etapeDiv = document.createElement('div');
                    etapeDiv.style.marginBottom = '25px';
                    
                    etapeDiv.innerHTML = `
                        <h4 style="color: var(--text-primary); margin-bottom: 15px;">
                            Étape ${etape.ordre}: ${etape.nom}
                        </h4>
                        <div class="hebergements-etape" data-point-id="${etape.id}">
                            ${this.renderHebergementsForPoint(etape.id)}
                        </div>
                    `;
                    
                    container.appendChild(etapeDiv);
                });

                // Bind events pour les hébergements
                this.bindHebergementEvents();
            }

            renderHebergementsForPoint(pointId) {
                const hebergementsPoint = this.hebergementsData.filter(h => h.id_point == pointId);
                
                if (hebergementsPoint.length === 0) {
                    return '<p style="color: var(--text-secondary); font-style: italic;">Aucun hébergement disponible pour cette étape</p>';
                }

                return hebergementsPoint.map(heb => `
                    <div class="hebergement-card" data-id="${heb.id_hebergement}" data-point="${pointId}" data-prix="${heb.prix_nuit}">
                        <div class="hebergement-info">
                            <div class="hebergement-nom">${heb.nom}</div>
                            <div class="hebergement-prix">${parseFloat(heb.prix_nuit).toFixed(2)}€/nuit</div>
                        </div>
                        <div class="hebergement-details">
                            ${heb.type} - Capacité: ${heb.capacite} personnes
                            ${heb.description ? '<br>' + heb.description : ''}
                        </div>
                    </div>
                `).join('');
            }

            bindHebergementEvents() {
                document.querySelectorAll('.hebergement-card').forEach(card => {
                    card.addEventListener('click', () => this.selectHebergement(card));
                });
            }

            selectHebergement(card) {
                const pointId = parseInt(card.dataset.point);
                const hebId = parseInt(card.dataset.id);
                const hebPrix = parseFloat(card.dataset.prix);
                const hebNom = card.querySelector('.hebergement-nom').textContent;

                // Désélectionner les autres hébergements de cette étape
                document.querySelectorAll(`[data-point="${pointId}"] .hebergement-card`).forEach(c => {
                    c.classList.remove('selected');
                });

                // Sélectionner cet hébergement
                card.classList.add('selected');

                // Mettre à jour les données
                this.hebergements[pointId] = {
                    id: hebId,
                    nom: hebNom,
                    prix: hebPrix
                };

                // Mettre à jour l'étape
                const etape = this.etapes.find(e => e.id === pointId);
                if (etape) {
                    etape.hebergement = {
                        id: hebId,
                        nom: hebNom,
                        prix: hebPrix
                    };
                }

                this.updateResume();
            }

            updateResume() {
                this.updateEtapesList();
                this.updateServicesList();
                this.updateTotalPrice();
                this.updateFormData();
                this.updateValidateButton();
            }

            updateEtapesList() {
                const container = document.getElementById('etapesList');
                
                if (this.etapes.length === 0) {
                    container.innerHTML = '<p style="color: var(--text-secondary); font-style: italic;">Aucune étape sélectionnée</p>';
                    return;
                }

                container.innerHTML = this.etapes.map(etape => `
                    <div class="resume-item">
                        <h5>Étape ${etape.ordre}: ${etape.nom}</h5>
                        <p>${etape.hebergement ? 
                            `Hébergement: ${etape.hebergement.nom} (${etape.hebergement.prix}€/nuit)` : 
                            'Hébergement non sélectionné'}</p>
                    </div>
                `).join('');
            }

            updateServicesList() {
                const container = document.getElementById('servicesList');
                const section = document.getElementById('servicesResume');

                if (this.services.length === 0) {
                    section.style.display = 'none';
                    return;
                }

                section.style.display = 'block';
                container.innerHTML = this.services.map(service => `
                    <div class="resume-item">
                        <h5>${service.nom}</h5>
                        <p>${service.prix}€</p>
                    </div>
                `).join('');
            }

            updateTotalPrice() {
                const nbPersonnes = parseInt(document.getElementById('nb_personnes').value) || 1;
                let total = 0;

                // Prix des hébergements (par nuit * nb personnes)
                Object.values(this.hebergements).forEach(heb => {
                    total += heb.prix * nbPersonnes;
                });

                // Prix des services
                this.services.forEach(service => {
                    total += service.prix;
                });

                this.totalPrice = total;
                document.getElementById('totalPrice').textContent = `Total estimé : ${total.toFixed(2)}€`;
            }

            updateFormData() {
                // Préparer les données pour le formulaire
                const etapesData = this.etapes.map(etape => ({
                    point_id: etape.id,
                    point_nom: etape.nom,
                    ordre: etape.ordre,
                    hebergement_id: etape.hebergement?.id || null,
                    hebergement_nom: etape.hebergement?.nom || null,
                    hebergement_prix: etape.hebergement?.prix || null
                }));

                const servicesData = this.services.map(service => ({
                    service_id: service.id,
                    service_nom: service.nom,
                    service_prix: service.prix
                }));

                document.getElementById('etapes_data').value = JSON.stringify(etapesData);
                document.getElementById('services_data').value = JSON.stringify(servicesData);
            }

            updateValidateButton() {
                const btn = document.getElementById('validerBtn');
                const hasEtapes = this.etapes.length > 0;
                const allEtapesHaveHebergement = this.etapes.every(e => e.hebergement !== null);

                btn.disabled = !hasEtapes || !allEtapesHaveHebergement;
                
                if (hasEtapes && !allEtapesHaveHebergement) {
                    btn.textContent = 'Sélectionnez tous les hébergements';
                } else if (hasEtapes && allEtapesHaveHebergement) {
                    btn.textContent = '🎯 Valider mon itinéraire';
                } else {
                    btn.textContent = 'Sélectionnez au moins une étape';
                }
            }
        }

        // Gestionnaire de thème
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

            // Charger le thème sauvegardé
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

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            const timeManager = new TimeManager();
            const itineraireManager = new ItineraireManager();
            initTheme();

            console.log('🗺️ Composer itinéraire Kayak Trip Loire initialisé');
        });
    </script>
</body>
</html>