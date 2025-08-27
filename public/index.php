<?php
declare(strict_types=1);

// --- Chemin de base ---
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// --- Récupère l’URI demandée ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Retire le préfixe BASE_PATH
if (strpos($uri, BASE_PATH) === 0) {
    $uri = substr($uri, strlen(BASE_PATH));
}

// Normalisation : vide ou index.php → racine
if ($uri === '' || $uri === '/index.php') {
    $uri = '/';
}
// var_dump($uri);
// exit;

// --- Table de redirections ---
$redirects = [
    '/ancienne-page' => '/nouvelle-page',
];
if (isset($redirects[$uri])) {
    header("Location: " . BASE_PATH . $redirects[$uri], true, 301);
    exit;
}

// --- Router ---
switch ($uri) {
    case '/':
        require __DIR__ . '/../src/template/home.php';
        break;

    case '/connexion':
        require __DIR__ . '/../src/template/connexion.php';
        break;

    case '/inscription':
        require __DIR__ . '/../src/template/inscription.php';
        break;

    case '/traitement_connexion':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée');
        }
        require __DIR__ . '/../src/template/traitement_connexion.php';
        break;

    case '/dashboard_admin':
        require __DIR__ . '/../src/template/dashboard_admin.php';
        break;

    case '/user_dashboard':
        require __DIR__ . '/../src/template/user_dashboard.php';
        break;

    case '/commercial_dashboard':
        require __DIR__ . '/../src/template/commercial_dashboard.php';
        break;

    case '/choisir_hebergement':
        require __DIR__ . '/../src/template/choisir_hebergement.php';
        break;
        
    case '/reserver_hebergement':
        require __DIR__ . '/../src/template/reserver_hebergement.php';
        break;

    case '/ajouter_hebergement':
        require __DIR__ . '/../src/template/ajouter_hebergement.php';
        break;

    case '/mes_reservation':
        require __DIR__ . '/../src/template/mes_reservation.php';
        break;

        case '/traitement_reservation':
            require __DIR__ . '/../src/template/traitement_reservation.php';
            break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page introuvable</h1><p>URI = $uri</p>";
        break;
}

