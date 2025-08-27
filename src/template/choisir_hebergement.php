<?php
if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}
require_once __DIR__ . '/../controller/HebergementController.php';
$controller = new HebergementController();
$hebergements = $controller->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un Hébergement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e6f0fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 30px;
        }
        .hebergement-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .hebergement-item {
            background: #f9fbfd;
            border: 1px solid #d0e7ff;
            border-radius: 10px;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.3s;
        }
        .hebergement-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .hebergement-info {
            font-size: 18px;
            color: #333;
        }
        .hebergement-info h3 {
            margin: 0 0 5px 0;
            color: #1a73e8;
        }
        .hebergement-info p {
            margin: 0;
            color: #555;
        }
        .reserver-btn {
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }
        .reserver-btn:hover {
            background-color: #155ab6;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Choisir un Hébergement</h1>
        <div class="hebergement-list">
            <?php foreach ($hebergements as $h): ?>
                <div class="hebergement-item">
                    <div class="hebergement-info">
                        <h3><?= htmlspecialchars($h['nom']) ?></h3>
                        <p><?= htmlspecialchars($h['type']) ?></p>
                    </div>
                    <a href="<?= BASE_PATH ?>/reserver_hebergement?id_hebergement=<?= $h['id_hebergement'] ?>" class="reserver-btn">Réserver</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
