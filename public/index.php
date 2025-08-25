<?php
declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Récupère le chemin "base" (public)
$basePath = '/PA_rewind/public';

// Supprime le préfixe /PA_rewind/public
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Si après ça $uri est vide, on est à la racine "/"
if ($uri === '' || $uri === false) {
    $uri = '/';
}

// --- Table de redirections (301) ---
$redirects = [
    '/ancienne-page' => '/nouvelle-page',
    '/vieux-lien'    => '/nouveau-lien',
];

if (isset($redirects[$uri])) {
    header("Location: {$redirects[$uri]}", true, 301);
    exit;
}

// --- Routing basique ---
switch ($uri) {
    case '/':
        require __DIR__ . '/../src/template/home.php';
        break;

    case '/contact':
        require __DIR__ . '/../template/contact.php';
        break;

    default:
        phpinfo(INFO_MODULES);
        break;
}
