<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-message {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .welcome-message h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-message">
            <?php
            session_start();
            if (isset($_SESSION['user_nom'])) {
                echo "<h1>Bienvenue, " . htmlspecialchars($_SESSION['user_nom']) . "!</h1>";
            } else {
                header("Location: connexion.php");
                exit;
            }
            ?>
            <p>Vous êtes maintenant connecté.</p>
        </div>
        <!-- Ajoute ici le contenu spécifique de ton tableau de bord -->
    </div>
</body>
</html>
