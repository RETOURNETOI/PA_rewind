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
    $flashMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$controller = new HebergementController();
$reservations = $controller->getReservationsByUser($_SESSION['user_id']);
// var_dump($reservations);
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
            background: #e6f0fa; /* bleu clair */
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 950px;
            margin: 40px auto;
            padding: 25px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #1a73e8; /* bleu plus vif */
            margin-bottom: 20px;
        }

        .flash {
            background: #d0e7ff; /* bleu pâle */
            color: #0b3d91;
            padding: 12px 20px;
            border-left: 5px solid #1a73e8;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            transition: background 0.3s;
        }

        th {
            background-color: #1a73e8;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f2f6fc; /* bleu très clair */
        }

        tr:hover {
            background-color: #d0e7ff;
        }

        .btn-annuler {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s, background 0.3s;
        }

        .btn-annuler:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background-color: #1a73e8;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }

        a.button:hover {
            background-color: #155ab6;
            transform: scale(1.05);
        }

        p.no-reservation {
            text-align: center;
            font-style: italic;
            color: #555;
            margin-top: 20px;
        }

        td form {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mes Réservations</h1>

        <?php if (!empty($flashMessage)) : ?>
            <div class="flash"><?= htmlspecialchars($flashMessage) ?></div>
        <?php endif; ?>

        <?php if (empty($reservations)) : ?>
            <p class="no-reservation">Vous n'avez aucune réservation.</p>
        <?php else : ?>
            <table>
                <tr>
                    <th>Hébergement</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Nombre de personnes</th>
                    <th>Action</th>
                </tr>
                
                <?php foreach ($reservations as $r) : ?>
                    <tr>
                        <td><?= htmlspecialchars($r['hebergement_nom']) ?></td>
                        <td><?= htmlspecialchars($r['date_debut']) ?></td>
                        <td><?= htmlspecialchars($r['date_fin']) ?></td>
                        <td><?= htmlspecialchars($r['nb_personnes']) ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_PATH ?>/annuler_reservation" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                <input type="hidden" name="id_commande" value="<?= $r['id_commande'] ?>">
                                <button type="submit" class="btn-annuler">Annuler</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <p style="text-align:center;"><a href="<?= BASE_PATH ?>/" class="button">Retour au tableau de bord</a></p>
    </div>
</body>
</html>
