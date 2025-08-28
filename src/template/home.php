<!--<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
    <title>Accueil</title>
    <style>
       body { font-family: Arial; background: #f4f4f9; margin:0; display:flex; justify-content:center; align-items:center; height:100vh; }
       .container { text-align:center; background:white; padding:40px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.15); }
        a { display:inline-block; margin:10px; padding:12px 20px; text-decoration:none; background:#007BFF; color:white; border-radius:8px; }
        a:hover { background:#0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>Salam 👋</h1>
        <p>Bienvenue sur notre site</p>
    <a href="<?= BASE_PATH ?>/connexion">Se connecter</a>
    <a href="<?= BASE_PATH ?>/inscription">S’inscrire</a>
</div>
</body>
</html>-->
<?php 
// home.php - Page d'accueil client

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrage de la session pour vérifier si l'utilisateur est connecté
session_start();

// Variables pour l'affichage conditionnel
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayak Trip Loire - Votre aventure sur la Loire vous attend</title>
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
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
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

        .auth-buttons {
            display: flex;
            gap: 10px;
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .hero h1 {
            font-size: 3em;
            color: #333;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 25px;
        }

        /* Services Grid */
        .services {
            margin-bottom: 40px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5em;
            color: white;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(31, 38, 135, 0.5);
        }

        .service-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }

        .service-card h3 {
            color: #333;
            font-size: 1.4em;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .service-features {
            list-style: none;
            margin-bottom: 25px;
        }

        .service-features li {
            color: #555;
            padding: 5px 0;
            position: relative;
            padding-left: 20px;
        }

        .service-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #51cf66;
            font-weight: bold;
        }

        /* Chat Section */
        .chat-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-align: center;
        }

        .chat-section h2 {
            color: #333;
            font-size: 2em;
            margin-bottom: 15px;
        }

        .chat-section p {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 25px;
        }

        .chat-button {
            background: linear-gradient(45deg, #51cf66, #40c057);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .chat-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(64, 192, 87, 0.4);
        }

        .chat-button::before {
            content: '💬';
            margin-right: 10px;
        }

        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
        }

        .feature-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: block;
        }

        .feature-card h4 {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            font-size: 0.9em;
        }

        /* Footer */
        .footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            color: white;
            margin-top: 40px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 20px;
        }

        .footer-section h4 {
            margin-bottom: 15px;
            color: #fff;
        }

        .footer-section p, .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 2.2em;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2em;
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

        .service-card, .feature-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }
        .service-card:nth-child(5) { animation-delay: 0.5s; }
        .service-card:nth-child(6) { animation-delay: 0.6s; }

        /* Status indicators */
        .status-indicator {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
            margin-left: 10px;
        }

        .status-available { background: #e8f5e8; color: #2e7d2e; }
        .status-coming-soon { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                🚣‍♂️ Kayak Trip Loire
            </div>
            <div class="user-info">
                <span class="live-time" id="current-time">Chargement...</span>
                <?php if ($isLoggedIn): ?>
                    <span>Bonjour, <?= htmlspecialchars($userName) ?></span>
                    <a href="<?= BASE_PATH ?>/profil" class="btn btn-secondary">Mon Profil</a>
                    <a href="<?= BASE_PATH ?>/logout" class="btn btn-secondary">Déconnexion</a>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="<?= BASE_PATH ?>/connexion" class="btn btn-secondary">Se connecter</a>
                        <a href="<?= BASE_PATH ?>/inscription" class="btn btn-primary">S'inscrire</a>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <h1>🌊 Découvrez la Loire en Kayak</h1>
            <p>Vivez une aventure inoubliable le long de la Loire. Composez votre itinéraire, réservez vos hébergements et profitez de nos services personnalisés pour une expérience unique.</p>
            
            <div class="hero-actions">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= BASE_PATH ?>/composer-itineraire" class="btn btn-primary btn-large">🗺️ Composer mon itinéraire</a>
                    <a href="<?= BASE_PATH ?>/packs" class="btn btn-secondary btn-large">📦 Voir les packs</a>
                <?php else: ?>
                    <a href="<?= BASE_PATH ?>/inscription" class="btn btn-primary btn-large">🚀 Commencer l'aventure</a>
                    <a href="<?= BASE_PATH ?>/packs" class="btn btn-secondary btn-large">📦 Découvrir nos packs</a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services">
            <h2 class="section-title">🎯 Nos Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <span class="service-icon">🗺️</span>
                    <h3>Composition d'Itinéraires</h3>
                    <p>Créez votre parcours sur-mesure en choisissant vos points d'arrêt le long de la Loire.</p>
                    <ul class="service-features">
                        <li>Visualisation interactive des points d'arrêt</li>
                        <li>Calcul automatique des distances</li>
                        <li>Suggestions d'étapes recommandées</li>
                        <li>Adaptation selon votre niveau</li>
                    </ul>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= BASE_PATH ?>/composer-itineraire" class="btn btn-primary">Composer maintenant</a>
                    <?php else: ?>
                        <a href="<?= BASE_PATH ?>/inscription" class="btn btn-secondary">S'inscrire pour accéder</a>
                    <?php endif; ?>
                </div>

                <div class="service-card">
                    <span class="service-icon">🏨</span>
                    <h3>Réservation d'Hébergements</h3>
                    <p>Réservez vos nuits d'étape dans une sélection d'hébergements de qualité.</p>
                    <ul class="service-features">
                        <li>Vérification des disponibilités en temps réel</li>
                        <li>Choix selon le nombre de personnes</li>
                        <li>Différents types : hôtels, gîtes, campings</li>
                        <li>Photos et descriptions détaillées</li>
                    </ul>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= BASE_PATH ?>/hebergements" class="btn btn-primary">Voir les hébergements</a>
                    <?php else: ?>
                        <a href="<?= BASE_PATH ?>/inscription" class="btn btn-secondary">S'inscrire pour réserver</a>
                    <?php endif; ?>
                </div>

                <div class="service-card">
                    <span class="service-icon">🎒</span>
                    <h3>Services Complémentaires</h3>
                    <p>Complétez votre aventure avec nos services additionnels pour plus de confort.</p>
                    <ul class="service-features">
                        <li>Transport de bagages entre étapes</li>
                        <li>Paniers repas et collations</li>
                        <li>Location de matériel (gilets, pagaies...)</li>
                        <li>Assurance voyage</li>
                    </ul>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= BASE_PATH ?>/services" class="btn btn-primary">Découvrir les services</a>
                    <?php else: ?>
                        <a href="<?= BASE_PATH ?>/services" class="btn btn-secondary">Voir les services</a>
                    <?php endif; ?>
                </div>

                <div class="service-card">
                    <span class="service-icon">📦</span>
                    <h3>Packs Préconstruits</h3>
                    <p>Optez pour la simplicité avec nos circuits déjà organisés selon vos dates.</p>
                    <ul class="service-features">
                        <li>Itinéraires testés et approuvés</li>
                        <li>Hébergements présélectionnés</li>
                        <li>Services inclus</li>
                        <li>Tarifs préférentiels</li>
                    </ul>
                    <a href="<?= BASE_PATH ?>/packs" class="btn btn-primary">Voir nos packs</a>
                </div>

                <?php if ($isLoggedIn): ?>
                <div class="service-card">
                    <span class="service-icon">👤</span>
                    <h3>Gestion de Profil</h3>
                    <p>Suivez vos réservations et gérez votre compte en toute simplicité.</p>
                    <ul class="service-features">
                        <li>Historique de vos commandes</li>
                        <li>Gestion de vos réservations</li>
                        <li>Modifications et annulations</li>
                        <li>Téléchargement de documents</li>
                    </ul>
                    <a href="<?= BASE_PATH ?>/profil" class="btn btn-primary">Accéder au profil</a>
                </div>
                <?php endif; ?>

                <div class="service-card">
                    <span class="service-icon">💬</span>
                    <h3>Support Commercial <span class="status-coming-soon">Bientôt</span></h3>
                    <p>Discutez en temps réel avec notre équipe commerciale pour tous vos besoins.</p>
                    <ul class="service-features">
                        <li>Chat en direct</li>
                        <li>Conseils personnalisés</li>
                        <li>Support réactif</li>
                        <li>Assistance réservation</li>
                    </ul>
                    <button class="btn btn-secondary" disabled style="opacity: 0.6;">Bientôt disponible</button>
                </div>
            </div>
        </section>

        <!-- Chat Section -->
        <section class="chat-section">
            <h2>🤝 Besoin d'aide pour votre projet ?</h2>
            <p>Notre équipe commerciale est là pour vous accompagner dans la préparation de votre aventure sur la Loire.</p>
            <button class="chat-button" onclick="showComingSoon()">Discuter avec un conseiller</button>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="feature-card">
                <span class="feature-icon">⭐</span>
                <h4>Expérience Personnalisée</h4>
                <p>Chaque parcours est unique et s'adapte à vos envies et votre niveau</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">🛡️</span>
                <h4>Réservation Sécurisée</h4>
                <p>Paiements protégés et confirmation immédiate de vos réservations</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">🌟</span>
                <h4>Qualité Garantie</h4>
                <p>Partenaires sélectionnés et services de qualité tout au long de votre parcours</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">📱</span>
                <h4>Suivi en Temps Réel</h4>
                <p>Gérez vos réservations et suivez votre itinéraire depuis votre espace personnel</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>🚣‍♂️ Kayak Trip Loire</h4>
                    <p>Votre spécialiste du kayak sur la Loire. Découvrez les plus beaux paysages de France au fil de l'eau.</p>
                </div>
                <div class="footer-section">
                    <h4>📞 Contact</h4>
                    <p>Téléphone: 02 XX XX XX XX</p>
                    <p>Email: contact@kayaktriploirecom</p>
                    <p>Adresse: Loire Valley, France</p>
                </div>
                <div class="footer-section">
                    <h4>🔗 Liens utiles</h4>
                    <p><a href="<?= BASE_PATH ?>/conditions">Conditions générales</a></p>
                    <p><a href="<?= BASE_PATH ?>/confidentialite">Politique de confidentialité</a></p>
                    <p><a href="<?= BASE_PATH ?>/faq">Questions fréquentes</a></p>
                </div>
                <div class="footer-section">
                    <h4>⏰ Informations</h4>
                    <p>Saison: Avril à Octobre</p>
                    <p>Réservation: Toute l'année</p>
                    <p id="footer-time" class="live-time">Chargement...</p>
                </div>
            </div>
            <hr style="border: none; height: 1px; background: rgba(255,255,255,0.2); margin: 20px 0;">
            <p style="text-align: center; color: rgba(255,255,255,0.8);">
                © 2025 Kayak Trip Loire. Tous droits réservés.
            </p>
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

                const shortTimeString = now.toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: this.timezone
                });

                const currentTimeEl = document.getElementById('current-time');
                const footerTimeEl = document.getElementById('footer-time');

                if (currentTimeEl) {
                    currentTimeEl.textContent = timeString;
                }
                if (footerTimeEl) {
                    footerTimeEl.textContent = `Mis à jour: ${shortTimeString}`;
                }
            }
        }

        // Fonctions utilitaires
        function showComingSoon() {
            alert('💬 Le chat en temps réel sera bientôt disponible!\n\nEn attendant, vous pouvez nous contacter par téléphone ou email.');
        }

        // Animation des cartes au scroll
        function animateOnScroll() {
            const cards = document.querySelectorAll('.service-card, .feature-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            cards.forEach(card => {
                observer.observe(card);
            });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            const timeManager = new TimeManager();
            animateOnScroll();
            
            // Animation d'entrée pour les éléments
            setTimeout(() => {
                document.querySelectorAll('.service-card, .feature-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);

            console.log('🚣‍♂️ Kayak Trip Loire - Interface client initialisée');
        });

        // Gestion de la visibilité de la page
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                console.log('Page visible - reprise normale');
            }
        });
    </script>
</body>
</html>