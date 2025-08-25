<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chemins corrigés
require_once __DIR__ . '/../controller/UtilisateurController.php';

// Récupération des données du formulaire
$data = [
    'nom' => $_POST['nom'] ?? null,
    'prenom' => $_POST['prenom'] ?? null,
    'email' => $_POST['email'] ?? null,
    'mot_de_passe' => $_POST['mot_de_passe'] ?? null,
    'telephone' => !empty($_POST['telephone']) ? $_POST['telephone'] : null,
    'role' => $_POST['role'] ?? 'client',
];

// Instanciation du contrôleur
$controller = new UtilisateurController();

// Appel de la méthode ajouter
$resultat = $controller->ajouter($data);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Résultat de l'ajout</h1>
    <?php if ($resultat): ?>
        <p class="success">Utilisateur ajouté avec succès !</p>
    <?php else: ?>
        <p class="error">Erreur lors de l'ajout de l'utilisateur.</p>
    <?php endif; ?>
    <a href="formulaire_ajout_utilisateur.php">Retour au formulaire</a>
</body>
</html>
