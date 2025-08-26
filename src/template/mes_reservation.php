<?php
session_start();
require_once '../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$controller = new HebergementController();
$reservations = $controller->getReservationsByUser($_SESSION['user_id']);
?>

<h1>Mes Réservations</h1>

<?php if (empty($reservations)) : ?>
    <p>Vous n'avez aucune réservation.</p>
<?php else : ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Hébergement</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Nombre de personnes</th>
        </tr>
        
        <?php echo "<pre>";
                var_dump($reservations);
                echo "</pre>";
        foreach ($reservations as $r) : ?>
            <tr>
                <td><?= htmlspecialchars($r['hebergement_nom']) ?></td>
                <td><?= htmlspecialchars($r['date_debut']) ?></td>
                <td><?= htmlspecialchars($r['date_fin']) ?></td>
                <td><?= htmlspecialchars($r['nb_personnes']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p><a href="user_dashboard.php">Retour au tableau de bord</a></p>
