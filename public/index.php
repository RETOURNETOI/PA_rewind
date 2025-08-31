<?php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Chemin de base ---
define('BASE_PATH', '/PA_rewind/public');

// --- Récupère l'URI demandée ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Retire le préfixe BASE_PATH
if (strpos($uri, BASE_PATH) === 0) {
    $uri = substr($uri, strlen(BASE_PATH));
}

// Normalisation : vide ou index.php → racine
if ($uri === '' || $uri === '/index.php') {
    $uri = '/';
}

// --- Gestion des assets statiques (CSS, JS, images) ---
if (preg_match('/\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|map)$/', $uri)) {
    $filePath = __DIR__ . $uri;

    if (file_exists($filePath)) {
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'map' => 'application/json'
        ];
        $extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        header("Content-Type: $mimeType");

        if (in_array($extension, ['js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot'])) {
            header("Cache-Control: public, max-age=31536000");
            header("Expires: " . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        if ($extension === 'js') {
            header("X-Content-Type-Options: nosniff");
        }
        if (in_array($extension, ['woff', 'woff2', 'ttf', 'eot'])) {
            header("Access-Control-Allow-Origin: *");
        }

        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        exit('Asset non trouvé');
    }
}

// --- Table de redirections ---
/*$redirects = [
    '/ancienne-page' => '/nouvelle-page',
    '/dashboard_admin' => '/dashboardadmin',
];
if (isset($redirects[$uri])) {
    header("Location: " . BASE_PATH . $redirects[$uri], true, 301);
    exit;
}
*/
// --- Router ---
switch ($uri) {
    case '/':
        require __DIR__ . '/../src/template/home.php';
        break;

    case '/connexion':
        require __DIR__ . '/../src/template/connexion.php';
        break;

    case '/traitement_connexion':
        require __DIR__ . '/../src/template/traitement_connexion.php';
        break;

    case '/inscription':
        require __DIR__ . '/../src/template/inscription.php';
        break;

    case '/traiter_ajout_user':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée');
        }
        require __DIR__ . '/../src/template/traiter_ajout_user.php';
        break;

    case '/deco':
        require __DIR__ . '/../src/template/deco.php';
        break;

    case '/dashboardadmin':
        require __DIR__ . '/../src/template/admin/dashboard_admin.php';
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

    case '/traitement_connexion':
        require __DIR__ . '/../src/template/traitement_connexion.php';
        break;
        
    case '/annuler_reservation':
        require __DIR__ . '/../src/template/annuler_reservation.php';
        break;
    
    case '/mes_hebergement':
        require __DIR__ . '/../src/template/mes_hebergement.php';
        break;
    
    case '/gestionuser':
        require __DIR__ . '/../src/template/gestion/gestionuser.php';
        break;
    
    case '/admintest':
        require __DIR__ . '/../src/template/admin/admintest.php';
        break;

    case '/listepointsarret':
        require __DIR__ . '/../src/template/liste_points_arret.php';
        break;
    
    case '/gestionpointsarret':
        require __DIR__ . '/../src/template/gestion/gestion_points_arret.php';
        break;
        
    case '/composer_itineraire':
        require __DIR__ . '/../src/template/composer_itineraire.php';
        break;

    // Pages client
    case '/packs':
        require __DIR__ . '/../src/template/client/packs.php';
        break;
        
    case '/hebergements':
        require __DIR__ . '/../src/template/client/hebergements.php';
        break;
        
    case '/services':
        require __DIR__ . '/../src/template/client/services.php';
        break;
        
    case '/profil':
        require __DIR__ . '/../src/template/client/profil.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page introuvable</h1><p>URI = $uri</p>";
        break;
}