<?php
require_once '../controller/HebergementController.php';
$controller = new HebergementController();
$hebergements = $controller->getAll();
?>

<h1>Choisir un hébergement</h1>
<?php foreach ($hebergements as $h): ?>
    <p>
        <?= htmlspecialchars($h['nom']) ?> - <?= $h['type'] ?>
        <a href="reserver_hebergement.php?id_hebergement=<?= $h['id_hebergement'] ?>">Réserver</a>
    </p>
<?php endforeach; ?>
