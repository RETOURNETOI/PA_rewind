<?php
if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once __DIR__ . '/../controller/HebergementController.php';
$controller = new HebergementController();
$hebergements = $controller->getAll();
?>

<h1>Choisir un hébergement</h1>
<?php foreach ($hebergements as $h): ?>
    <p>
        <?= htmlspecialchars($h['nom']) ?> - <?= $h['type'] ?>
        <a href="<?= BASE_PATH ?>/reserver_hebergement?id_hebergement=<?= $h['id_hebergement'] ?>">Réserver</a>
    </p>
<?php endforeach; ?>
