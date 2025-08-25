<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, select, button {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Ajouter un utilisateur</h1>
    <form action="traiter_ajout_utilisateur.php" method="post">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="text" name="telephone" placeholder="Téléphone (optionnel)">
        <select name="role">
            <option value="client">Client</option>
            <option value="admin">Admin</option>
            <option value="commercial">Commercial</option>
        </select>
        <button type="submit">Ajouter l'utilisateur</button>
    </form>
</body>
</html>
