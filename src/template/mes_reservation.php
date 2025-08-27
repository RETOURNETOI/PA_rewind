<?php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once __DIR__.'/../controller/HebergementController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (isset($_SESSION['flash'])) {
    echo "<p class='flash'>{$_SESSION['flash']}</p>";
    unset($_SESSION['flash']);
}

$controller = new HebergementController();
$reservations = $controller->getReservationsByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .flash {
            background: #fffae6;
            color: #856404;
            padding: 12px 20px;
            border-left: 5px solid #ffe066;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #d9f0d9;
        }

        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            border-radius: 5px;
            transition: background 0.3s;
        }

        a.button:hover {
            background-color: #45a049;
        }

        p.no-reservation {
            text-align: center;
            font-style: italic;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mes Réservations</h1>

        <?php if (empty($reservations)) : ?>
            <p class="no-reservation">Vous n'avez aucune réservation.</p>
        <?php else : ?>
            <table>
                <tr>
                    <th>Hébergement</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Nombre de personnes</th>
                </tr>
                
                <?php foreach ($reservations as $r) : ?>
                    <tr>
                        <td><?= htmlspecialchars($r['hebergement_nom']) ?></td>
                        <td><?= htmlspecialchars($r['date_debut']) ?></td>
                        <td><?= htmlspecialchars($r['date_fin']) ?></td>
                        <td><?= htmlspecialchars($r['nb_personnes']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <p style="text-align:center;"><a href="<?= BASE_PATH ?>/user_dashboard" class="button">Retour au tableau de bord</a></p>
    </div>
</body>
</html>
