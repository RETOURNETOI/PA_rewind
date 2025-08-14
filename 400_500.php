<?php
// 404.php - Page d'erreur 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - Kayak Trip</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .error-content {
            max-width: 500px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        .error-description {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .btn-home {
            display: inline-block;
            padding: 15px 30px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            border: 2px solid white;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn-home:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }
        .kayak-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="kayak-icon">🛶</div>
            <div class="error-code">404</div>
            <h1 class="error-message">Oups ! Page non trouvée</h1>
            <p class="error-description">
                Il semblerait que cette page ait dérivé au fil de l'eau... 
                Mais ne vous inquiétez pas, nous allons vous ramener en sécurité !
            </p>
            <a href="/" class="btn-home">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>

<?php
// 500.php - Page d'erreur 500
if (!defined('ERROR_500_DISPLAYED')) {
    define('ERROR_500_DISPLAYED', true);
    http_response_code(500);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - Kayak Trip</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .error-content {
            max-width: 500px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        .error-description {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .btn-home {
            display: inline-block;
            padding: 15px 30px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            border: 2px solid white;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn-home:hover {
            background: white;
            color: #f5576c;
            transform: translateY(-2px);
        }
        .kayak-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="kayak-icon">⚠️</div>
            <div class="error-code">500</div>
            <h1 class="error-message">Erreur technique</h1>
            <p class="error-description">
                Notre serveur rencontre quelques turbulences en ce moment. 
                Nos équipes techniques naviguent pour résoudre le problème !
            </p>
            <a href="/" class="btn-home">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
<?php
}
?>