<?php
declare(strict_types=1);

// Récupération du chemin demandé (sans query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// --- Table de redirections (301) ---
$redirects = [
    '/ancienne-page' => '/nouvelle-page',
    '/vieux-lien'    => '/nouveau-lien',
];

// Si l’URL correspond à une redirection, on renvoie vers la nouvelle
if (isset($redirects[$uri])) {
    header("Location: {$redirects[$uri]}", true, 301);
    exit;
}

// --- Routing basique ---
var_dump($uri);
switch ($uri) {
    case '/':
        require '../src/template/home.php';
        break;

    case '/contact':
        require '../template/contact.php';
        break;

    default:
        phpinfo(INFO_MODULES);
        break;
}