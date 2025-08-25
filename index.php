<?php
// index.php - Page d'accueil
session_start();

require_once 'config/constants.php';
require_once 'includes/functions.php';
require_once 'classes/PointArret.php';
require_once 'classes/Hebergement.php';

// Récupérer les données pour l'affichage
$pointArret = new PointArret();
$hebergement = new Hebergement();

$pointsArret = $pointArret->getAll();
$hebergementsRecents = $hebergement->getAll(['order' => 'h.date_creation DESC LIMIT 6']);

$pageTitle = 'Accueil - Découvrez la Loire en kayak';
$pageDescription = 'Vivez une aventure unique sur la Loire ! Découvrez nos circuits en kayak avec hébergements sélectionnés le long du fleuve royal.';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?></title>
    <meta name="description" content="<?= escape($pageDescription) ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/responsive.css">
    
    <!-- Favicon -->
    <link rel="icon" href="<?= ASSETS_PATH ?>/images/favicon.ico">
    
    <!-- Open Graph pour les réseaux sociaux -->
    <meta property="og:title" content="<?= escape($pageTitle) ?>">
    <meta property="og:description" content="<?= escape($pageDescription) ?>">
    <meta property="og:image" content="<?= SITE_URL . ASSETS_PATH ?>/images/kayak-hero.jpg">
    <meta property="og:url" content="<?= SITE_URL ?>">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>
    
    <!-- Messages flash -->
    <?php if ($flashMessages = getFlashMessages()): ?>
        <div class="flash-messages">
            <?php foreach ($flashMessages as $message): ?>
                <div class="flash-message flash-<?= escape($message['type']) ?>">
                    <?= escape($message['message']) ?>
                    <button class="flash-close">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Découvrez la Loire en kayak</h1>
                <p class="hero-subtitle">
                    Vivez une aventure unique sur le plus long fleuve de France. 
                    Parcourez des paysages exceptionnels, découvrez les châteaux 
                    de la Loire et dormez dans nos hébergements sélectionnés.
                </p>
                <div class="hero-buttons">
                    <a href="/points-arret" class="btn btn-primary btn-large">
                        🗺️ Découvrir les étapes
                    </a>
                    <a href="/reservation" class="btn btn-secondary btn-large">
                        🛶 Réserver maintenant
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?= ASSETS_PATH ?>/images/kayak-hero.jpg" alt="Kayak sur la Loire" loading="lazy">
            </div>
        </div>
        
        <!-- Indicateurs clés -->
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number"><?= count($pointsArret) ?></span>
                <span class="stat-label">Points d'arrêt</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">200</span>
                <span class="stat-label">Km de parcours</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Hébergements</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">⭐⭐⭐⭐⭐</span>
                <span class="stat-label">Satisfaction client</span>
            </div>
        </div>
    </section>

    <!-- Section Fonctionnalités -->
    <section class="features">
        <div class="container">
            <h2>Pourquoi choisir Kayak Trip ?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Parcours personnalisé</h3>
                    <p>Composez votre propre itinéraire en choisissant vos étapes et hébergements selon vos envies.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏰</div>
                    <h3>Châteaux de la Loire</h3>
                    <p>Découvrez le patrimoine exceptionnel : châteaux de Sully, Gien, Beaugency et bien d'autres.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛏️</div>
                    <h3>Hébergements sélectionnés</h3>
                    <p>Dormez dans nos hébergements partenaires : hôtels de charme, gîtes, campings ou châteaux.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎒</div>
                    <h3>Services inclus</h3>
                    <p>Transport des bagages, paniers repas, location de matériel... tout pour votre confort.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Pour tous niveaux</h3>
                    <p>Que vous soyez débutant ou expert, nos parcours s'adaptent à votre niveau d'expérience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Support 24/7</h3>
                    <p>Notre équipe est disponible pour vous accompagner avant et pendant votre séjour.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Parcours populaires -->
    <section class="popular-routes">
        <div class="container">
            <div class="section-header">
                <h2>Nos parcours les plus populaires</h2>
                <a href="/points-arret" class="btn btn-outline">Voir tous les points d'arrêt</a>
            </div>
            
            <div class="routes-grid">
                <?php 
                $routesPopulaires = array_slice($pointsArret, 0, 6);
                foreach ($routesPopulaires as $point): 
                ?>
                    <div class="route-card">
                        <div class="route-image">
                            <?php if (!empty($point['image'])): ?>
                                <img src="<?= UPLOADS_URL ?>/points/<?= escape($point['image']) ?>" 
                                     alt="<?= escape($point['nom']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="route-placeholder">📍</div>
                            <?php endif; ?>
                        </div>
                        <div class="route-content">
                            <h3><?= escape($point['nom']) ?></h3>
                            <p><?= escape(substr((string)$point['description'], 0, 100)) ?>...</p>
                            <div class="route-meta">
                                <span class="route-order">Étape <?= (int)$point['ordre_parcours'] ?></span>
                                <a href="/point/<?= (int)$point['id_point'] ?>" class="btn btn-small">Découvrir</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section Comment ça marche -->
    <section class="how-it-works">
        <div class="container">
            <h2>Comment ça marche ?</h2>
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Choisissez votre parcours</h3>
                        <p>Sélectionnez vos points d'arrêt et composez votre itinéraire idéal sur la Loire.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Réservez vos hébergements</h3>
                        <p>Choisissez parmi notre sélection d'hébergements pour chaque étape de votre voyage.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Ajoutez vos services</h3>
                        <p>Transport des bagages, paniers repas, location de matériel... personnalisez votre séjour.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Partez à l'aventure !</h3>
                        <p>Profitez de votre expérience unique sur la Loire avec l'accompagnement de nos équipes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="testimonials">
        <div class="container">
            <h2>Ce que disent nos clients</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Une expérience inoubliable ! Le parcours était parfaitement organisé et les hébergements de qualité. Nous recommandons vivement !"</p>
                    </div>
                    <div class="testimonial-author">
                        <strong>Marie & Pierre Dubois</strong>
                        <span>Parcours Orléans - Blois</span>
                        <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Parfait pour découvrir la Loire en famille. Les enfants ont adoré et nous avons pu profiter des châteaux en chemin."</p>
                    </div>
                    <div class="testimonial-author">
                        <strong>Famille Moreau</strong>
                        <span>Pack Aventure Familiale</span>
                        <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Le service client est exceptionnel. Ils nous ont aidés à personnaliser notre parcours selon nos envies."</p>
                    </div>
                    <div class="testimonial-author">
                        <strong>Jean-Claude Martin</strong>
                        <span>Parcours personnalisé</span>
                        <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Newsletter -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>Restez informé de nos dernières offres</h2>
                    <p>Recevez nos conseils, itinéraires exclusifs et promotions directement dans votre boîte mail.</p>
                </div>
                <form class="newsletter-form" id="newsletterForm">
                    <div class="form-group">
                        <input type="email" id="newsletter-email" name="email" placeholder="Votre adresse email" required>
                        <button type="submit" class="btn btn-primary">S'abonner</button>
                    </div>
                    <p class="newsletter-disclaimer">
                        En vous abonnant, vous acceptez de recevoir nos communications. 
                        Vous pouvez vous désabonner à tout moment.
                    </p>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="<?= ASSETS_PATH ?>/js/main.js"></script>
    <script>
        // Animation des compteurs dans la section hero
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const heroStats = document.querySelector('.hero-stats');
            if (heroStats) {
                observer.observe(heroStats);
            }

            function animateCounters() {
                const counters = document.querySelectorAll('.stat-number');
                counters.forEach(counter => {
                    const target = parseInt(counter.textContent);
                    if (isNaN(target)) return;
                    
                    let current = 0;
                    const increment = target / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target;
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                    }, 30);
                });
            }

            // Gestion de la newsletter
            const newsletterForm = document.getElementById('newsletterForm');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('newsletter-email').value;
                    const button = this.querySelector('button[type="submit"]');
                    const originalText = button.textContent;
                    
                    button.disabled = true;
                    button.textContent = 'Inscription...';
                    
                    try {
                        const response = await fetch('/api/newsletter.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ email: email })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showMessage('Merci ! Vous êtes maintenant abonné à notre newsletter.', 'success');
                            this.reset();
                        } else {
                            showMessage(result.error || 'Erreur lors de l\'inscription', 'error');
                        }
                    } catch (error) {
                        showMessage('Erreur de connexion. Veuillez réessayer.', 'error');
                    } finally {
                        button.disabled = false;
                        button.textContent = originalText;
                    }
                });
            }
        });
    </script>
 </body>
 </html>