<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_PATH . "/connexion");
    exit;
}

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

require_once __DIR__.'/../controller/HebergementController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new HebergementController();
    $id_point = $controller->getDefaultIdPoint();
    if ($id_point === null) {
        $message = "❌ Aucun point valide disponible.";
    } else {
        $_POST['id_point'] = $id_point;
        $ok = $controller->ajouter($_POST);
        if ($ok) {
            header("Location: " . BASE_PATH . "/mes_hebergement");
            exit;
        } else {
            $message = "❌ Erreur lors de l'ajout de l'hébergement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Hébergement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e6f0fa;
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
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 25px;
            font-size: 24px;
        }

        h1::before {
            content: "\f590";
            font-family: "Font Awesome 6 Free";
            margin-right: 10px;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
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
            font-size: 16px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #d0e7ff;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #1a73e8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2);
        }

        .form-group input[type="number"] {
            -moz-appearance: textfield;
        }

        .form-group input[type="number"]::-webkit-inner-spin-button,
        .form-group input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        button[type="submit"] {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #155ab6;
            transform: translateY(-2px);
        }

        button[type="submit"]::after {
            content: "\f067";
            font-family: "Font Awesome 6 Free";
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #1a73e8;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Hébergement</h1>
        <?php if (isset($message)) echo "<p class='error-message'>$message</p>"; ?>
        <form method="post">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>

            <div class="form-group">
                <label for="type">Type :</label>
                <input type="text" name="type" id="type" required>
            </div>

            <div class="form-group">
                <label for="capacite">Capacité :</label>
                <input type="number" name="capacite" id="capacite" required>
            </div>

            <div class="form-group">
                <label for="prix_nuit">Prix par nuit (€) :</label>
                <input type="number" name="prix_nuit" id="prix_nuit" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description"></textarea>
            </div>

            <button type="submit">Ajouter</button>
        </form>
        <a href="<?= BASE_PATH ?>/mes_hebergement" class="back-link">Retour à mes hébergements</a>
    </div>
</body>
</html>