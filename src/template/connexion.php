<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .login-form input {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .login-form button {
            padding: 10px;
            border-radius: 4px;
            border: none;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .login-form button:hover {
            background-color: #0056b3;
        }
        .login-form a {
            color: #007BFF;
            text-decoration: none;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Connexion</h1>
        <?php
        if (isset($_GET['erreur'])) {
            $erreur = $_GET['erreur'];
            if ($erreur === 'champs_manquants') {
                echo '<p class="error-message">Email et mot de passe sont obligatoires.</p>';
            } elseif ($erreur === 'identifiants_invalides') {
                echo '<p class="error-message">Email ou mot de passe incorrect.</p>';
            }
        }
        ?>
        <form class="login-form" action="<?= BASE_PATH ?>/traitement_connexion" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p><a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a></p>
        <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
    </div>
</body>
</html>
