<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}

require_once __DIR__ . '/../controller/PointArretController.php';
$controller = new PointArretController();
$points = $controller->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Points d'Arrêt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-success {
            background: #2ecc71;
            color: white;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f2f2f2;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-bus-stop"></i> Liste des Points d'Arrêt</h1>
        <a href="ajouter_point_arret.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter un Point d'Arrêt</a>

        <?php if (empty($points)): ?>
            <p style="text-align: center; margin-top: 20px;">Aucun point d'arrêt enregistré.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($points as $point): ?>
                        <tr>
                            <td><?= htmlspecialchars($point['id_point']) ?></td>
                            <td><?= htmlspecialchars($point['nom']) ?></td>
                            <td><?= htmlspecialchars($point['description'] ?? 'Aucune description') ?></td>
                            <td class="actions">
                                <a href="modifier_point_arret.php?id=<?= $point['id_point'] ?>" class="btn btn-success"><i class="fas fa-edit"></i> Modifier</a>
                                <a href="supprimer_point_arret.php?id=<?= $point['id_point'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce point d\'arrêt ?')"><i class="fas fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>