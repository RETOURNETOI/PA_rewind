<?php

session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion?erreur=acces_refuse");
    exit;
}

require_once __DIR__ . '/../controller/PointArretController.php';
$message = null;
$controller = new PointArretController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->ajouter($_POST);
    if ($result) {
        header("Location: liste_points_arret.php");
        exit;
    } else {
        $message = "<p class='error'>Erreur lors de l'ajout du point d'arrêt.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Point d'Arrêt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .error {
            color: #e74c3c;
            background: #fdecea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group label {
            font-weight: bold;
            color: #444;
        }
        .form-group input,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        button[type="submit"] {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background: #2980b9;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus"></i> Ajouter un Point d'Arrêt</h1>
        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description"></textarea>
            </div>
            <button type="submit">Ajouter</button>
        </form>
        <a href="<?= BASE_PATH ?>liste_points_arret" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    </div>
</body>
</html>
