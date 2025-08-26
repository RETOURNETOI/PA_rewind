<?php
declare(strict_types=1);

// --- Chemin de base ---
define('BASE_PATH', '/PA_rewind/public');

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
        require '../src/template/home.php';
        break;

    case '/connexion':
        require '../src/template/connexion.php';
        break;

    case '/inscription':
        require '../src/template/inscription.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page introuvable</h1><p>URI = $uri</p>";
        break;
}
