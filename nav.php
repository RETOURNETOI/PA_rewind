<?php
// includes/nav.php - Navigation principale
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['REQUEST_URI'], '?');
?>

<nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <a href="/" class="navbar-brand">
                🛶 Kayak Trip
            </a>
            
            <ul class="navbar-nav" id="navbarNav">
                <li>
                    <a href="/" class="<?= $currentPage === '' || $currentPage === 'index.php' ? 'active' : '' ?>">
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="/points-arret" class="<?= $currentPage === 'points-arret' ? 'active' : '' ?>">
                        Points d'arrêt
                    </a>
                </li>
                <li>
                    <a href="/hebergements" class="<?= $currentPage === 'hebergements' ? 'active' : '' ?>">
                        Hébergements
                    </a>
                </li>
                <li>
                    <a href="/reservation" class="<?= $currentPage === 'reservation' ? 'active' : '' ?>">
                        Réserver
                    </a>
                </li>
                <li>
                    <a href="/contact" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">
                        Contact
                    </a>
                </li>
                
                <?php if ($currentUser): ?>
                    <li>
                        <a href="/profile" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
                            👤 <?= escape($currentUser['prenom']) ?>
                        </a>
                    </li>
                    <?php if ($currentUser['role'] === ROLE_ADMIN): ?>
                        <li>
                            <a href="/admin" class="btn btn-primary btn-small">
                                ⚙️ Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="/deconnexion" class="btn btn-outline btn-small">
                            Déconnexion
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="/connexion" class="btn btn-outline btn-small">
                            Connexion
                        </a>
                    </li>
                    <li>
                        <a href="/inscription" class="btn btn-primary btn-small">
                            Inscription
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <button class="navbar-toggle" id="navbarToggle" aria-label="Menu">
                ☰
            </button>
        </div>
    </div>
</nav>

<script>
// Navigation mobile
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarNav = document.getElementById('navbarNav');
    
    if (navbarToggle && navbarNav) {
        navbarToggle.addEventListener('click', function() {
            navbarNav.classList.toggle('active');
            
            // Changer l'icône
            if (navbarNav.classList.contains('active')) {
                navbarToggle.innerHTML = '✕';
                navbarToggle.setAttribute('aria-expanded', 'true');
            } else {
                navbarToggle.innerHTML = '☰';
                navbarToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Fermer le menu au clic sur un lien
        navbarNav.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                navbarNav.classList.remove('active');
                navbarToggle.innerHTML = '☰';
                navbarToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Fermer le menu au clic à l'extérieur
        document.addEventListener('click', function(e) {
            if (!navbarToggle.contains(e.target) && !navbarNav.contains(e.target)) {
                navbarNav.classList.remove('active');
                navbarToggle.innerHTML = '☰';
                navbarToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>

<?php
// includes/footer.php - Pied de page
?>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Kayak Trip</h3>
                <p>
                    Découvrez la Loire comme jamais auparavant. 
                    Des aventures uniques vous attendent sur le plus long fleuve de France.
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">📘</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Nos services</h3>
                <ul>
                    <li><a href="/points-arret">Points d'arrêt</a></li>
                    <li><a href="/hebergements">Hébergements</a></li>
                    <li><a href="/packs">Packs prédéfinis</a></li>
                    <li><a href="/services">Services complémentaires</a></li>
                    <li><a href="/reservation">Réservation</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Informations</h3>
                <ul>
                    <li><a href="/qui-sommes-nous">Qui sommes-nous ?</a></li>
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/conditions-generales">Conditions générales</a></li>
                    <li><a href="/politique-confidentialite">Politique de confidentialité</a></li>
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <ul>
                    <li>📍 123 Rue de la Loire<br>45000 Orléans, France</li>
                    <li>📞 <a href="tel:+33123456789">01 23 45 67 89</a></li>
                    <li>✉️ <a href="mailto:contact@kayaktrip.fr">contact@kayaktrip.fr</a></li>
                    <li>🕒 Lun-Ven: 9h-18h<br>Sam: 9h-17h</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Kayak Trip. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<?php
// includes/header.php - En-tête HTML commun
function renderHead($title = '', $description = '', $keywords = '', $canonical = '') {
    $siteTitle = !empty($title) ? $title . ' - ' . SITE_NAME : SITE_NAME;
    $metaDescription = !empty($description) ? $description : 'Découvrez la Loire en kayak avec Kayak Trip. Réservez votre aventure unique avec hébergements sélectionnés.';
    $metaKeywords = !empty($keywords) ? $keywords : 'kayak, loire, voyage, hébergement, aventure, nature';
    $canonicalUrl = !empty($canonical) ? $canonical : SITE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Kayak Trip">
    
    <title><?= escape($siteTitle) ?></title>
    <meta name="description" content="<?= escape($metaDescription) ?>">
    <meta name="keywords" content="<?= escape($metaKeywords) ?>">
    <link rel="canonical" href="<?= escape($canonicalUrl) ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= escape($siteTitle) ?>">
    <meta property="og:description" content="<?= escape($metaDescription) ?>">
    <meta property="og:image" content="<?= SITE_URL . ASSETS_PATH ?>/images/kayak-hero.jpg">
    <meta property="og:url" content="<?= escape($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= escape($siteTitle) ?>">
    <meta name="twitter:description" content="<?= escape($metaDescription) ?>">
    <meta name="twitter:image" content="<?= SITE_URL . ASSETS_PATH ?>/images/kayak-hero.jpg">
    
    <!-- Favicon -->
    <link rel="icon" href="<?= ASSETS_PATH ?>/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?= ASSETS_PATH ?>/images/apple-touch-icon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/responsive.css">
    
    <!-- Preload des ressources critiques -->
    <link rel="preload" href="<?= ASSETS_PATH ?>/fonts/main.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    
    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TravelAgency",
        "name": "<?= SITE_NAME ?>",
        "description": "<?= escape($metaDescription) ?>",
        "url": "<?= SITE_URL ?>",
        "logo": "<?= SITE_URL . ASSETS_PATH ?>/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+33123456789",
            "contactType": "customer service",
            "email": "<?= SITE_EMAIL ?>"
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "123 Rue de la Loire",
            "addressLocality": "Orléans",
            "postalCode": "45000",
            "addressCountry": "FR"
        },
        "sameAs": [
            "https://facebook.com/kayaktrip",
            "https://instagram.com/kayaktrip"
        ]
    }
    </script>
    
    <!-- Variables CSS personnalisées -->
    <style>
        :root {
            --current-user-id: <?= isLoggedIn() ? getCurrentUser()['id'] : 'null' ?>;
            --site-url: "<?= SITE_URL ?>";
            --assets-path: "<?= ASSETS_PATH ?>";
        }
    </style>
</head>
<body>
<?php
}
?>

<?php
// includes/pagination.php - Composant de pagination réutilisable
function renderPagination($currentPage, $totalPages, $baseUrl, $queryParams = []) {
    if ($totalPages <= 1) return;
    
    $queryString = '';
    if (!empty($queryParams)) {
        $filteredParams = array_filter($queryParams, function($value) {
            return $value !== '' && $value !== null;
        });
        if (!empty($filteredParams)) {
            $queryString = '?' . http_build_query($filteredParams);
        }
    }
    
    echo '<nav class="pagination" aria-label="Pagination">';
    
    // Bouton Précédent
    if ($currentPage > 1) {
        $prevParams = array_merge($queryParams, ['page' => $currentPage - 1]);
        $prevQuery = !empty($prevParams) ? '?' . http_build_query($prevParams) : '';
        echo '<a href="' . $baseUrl . $prevQuery . '" aria-label="Page précédente">‹</a>';
    } else {
        echo '<span aria-label="Page précédente (indisponible)">‹</span>';
    }
    
    // Numéros de page
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    // Première page
    if ($start > 1) {
        $firstParams = array_merge($queryParams, ['page' => 1]);
        $firstQuery = !empty($firstParams) ? '?' . http_build_query($firstParams) : '';
        echo '<a href="' . $baseUrl . $firstQuery . '">1</a>';
        if ($start > 2) {
            echo '<span>...</span>';
        }
    }
    
    // Pages autour de la page actuelle
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            echo '<span class="current" aria-current="page">' . $i . '</span>';
        } else {
            $pageParams = array_merge($queryParams, ['page' => $i]);
            $pageQuery = !empty($pageParams) ? '?' . http_build_query($pageParams) : '';
            echo '<a href="' . $baseUrl . $pageQuery . '">' . $i . '</a>';
        }
    }
    
    // Dernière page
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            echo '<span>...</span>';
        }
        $lastParams = array_merge($queryParams, ['page' => $totalPages]);
        $lastQuery = !empty($lastParams) ? '?' . http_build_query($lastParams) : '';
        echo '<a href="' . $baseUrl . $lastQuery . '">' . $totalPages . '</a>';
    }
    
    // Bouton Suivant
    if ($currentPage < $totalPages) {
        $nextParams = array_merge($queryParams, ['page' => $currentPage + 1]);
        $nextQuery = !empty($nextParams) ? '?' . http_build_query($nextParams) : '';
        echo '<a href="' . $baseUrl . $nextQuery . '" aria-label="Page suivante">›</a>';
    } else {
        echo '<span aria-label="Page suivante (indisponible)">›</span>';
    }
    
    echo '</nav>';
}

// includes/breadcrumb.php - Fil d'Ariane
function renderBreadcrumb($items) {
    if (empty($items)) return;
    
    echo '<nav class="breadcrumb" aria-label="Fil d\'Ariane">';
    echo '<ol>';
    
    foreach ($items as $index => $item) {
        $isLast = ($index === count($items) - 1);
        
        echo '<li>';
        if (!$isLast && isset($item['url'])) {
            echo '<a href="' . escape($item['url']) . '">' . escape($item['label']) . '</a>';
        } else {
            echo '<span aria-current="page">' . escape($item['label']) . '</span>';
        }
        echo '</li>';
        
        if (!$isLast) {
            echo '<li aria-hidden="true">›</li>';
        }
    }
    
    echo '</ol>';
    echo '</nav>';
}

// includes/alert.php - Composant d'alerte
function renderAlert($message, $type = 'info', $dismissible = true) {
    $types = ['success', 'error', 'warning', 'info'];
    $type = in_array($type, $types) ? $type : 'info';
    
    $icons = [
        'success' => '✓',
        'error' => '✕',
        'warning' => '⚠',
        'info' => 'ℹ'
    ];
    
    echo '<div class="alert alert-' . $type . ($dismissible ? ' alert-dismissible' : '') . '">';
    echo '<span class="alert-icon">' . $icons[$type] . '</span>';
    echo '<span class="alert-message">' . escape($message) . '</span>';
    
    if ($dismissible) {
        echo '<button class="alert-close" onclick="this.parentElement.remove()">&times;</button>';
    }
    
    echo '</div>';
}
?>