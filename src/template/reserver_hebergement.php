<?php
session_start();

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Accès interdit');
}

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id_hebergement'])) {
    $_SESSION['flash'] = "Réservation réussie !";
    header("Location: " . BASE_PATH . "/mes_reservation");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un Hébergement</title>
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
            max-width: 500px;
        }

        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 25px;
            font-size: 24px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            font-weight: bold;
            color: #444;
            font-size: 16px;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #d0e7ff;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #1a73e8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2);
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
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
        }

        button[type="submit"]:hover {
            background-color: #155ab6;
            transform: translateY(-2px);
        }

        button[type="submit"]::after {
            content: "\f054";
            font-family: "Font Awesome 6 Free";
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-calendar-alt"></i> Réserver un Hébergement</h1>
        <form action="<?= BASE_PATH ?>/traitement_reservation" method="post">
            <input type="hidden" name="id_hebergement" value="<?= htmlspecialchars($_GET['id_hebergement']) ?>">

            <div class="form-group">
                <label for="date_debut">Date d'arrivée :</label>
                <input type="date" id="date_debut" name="date_debut" required>
            </div>

            <div class="form-group">
                <label for="date_fin">Date de départ :</label>
                <input type="date" id="date_fin" name="date_fin" required>
            </div>

            <div class="form-group">
                <label for="nb_personnes">Nombre de personnes :</label>
                <input type="number" id="nb_personnes" name="nb_personnes" min="1" value="1" required>
            </div>

            <button type="submit">Réserver maintenant</button>
        </form>
    </div>
</body>
</html>
