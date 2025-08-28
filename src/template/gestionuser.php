<?php
require_once __DIR__.'/../controller/UtilisateurController.php';

$controller = new UtilisateurController();

// ===================
// Gestion des actions
// ===================
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $success = $controller->ajouter($_POST);
        $message = $success ? "✅ Utilisateur ajouté." : "❌ Erreur lors de l'ajout.";
    } elseif (isset($_POST['modifier'])) {
        $success = $controller->modifier($_POST['id'], $_POST);
        $message = $success ? "✅ Utilisateur modifié." : "❌ Erreur lors de la modification.";
    }
}

if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $success = $controller->supprimer($id);
    $message = $success ? "✅ Utilisateur supprimé." : "❌ Erreur lors de la suppression.";
}

// Récupération de la liste
$utilisateurs = $controller->AfficherTous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f2f2f2; }
        form { margin: 15px 0; }
        input, select { padding: 5px; margin: 5px; }
        button { padding: 5px 10px; cursor: pointer; }
        .message { margin: 10px 0; font-weight: bold; color: green; }
    </style>
</head>
<body>
    <h1>👤 Gestion des utilisateurs</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <h2>➕ Ajouter un utilisateur</h2>
    <form method="post">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="text" name="telephone" placeholder="Téléphone">
        <select name="role">
            <option value="client">Client</option>
            <option value="admin">Admin</option>
            <option value="commercial">Commercial</option>
        </select>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <!-- Tableau des utilisateurs -->
    <h2>📋 Liste des utilisateurs</h2>
    <table>
        <tr>
            <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th>
            <th>Téléphone</th><th>Rôle</th><th>Actions</th>
        </tr>
        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['id_utilisateur'] ?></td>
                <td><?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['prenom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['telephone'] !== null ? htmlspecialchars($u['telephone']) : '' ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td>
                    <!-- Formulaire de modification inline -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= $u['id_utilisateur'] ?>">
                        <input type="text" name="nom" value="<?= htmlspecialchars($u['nom']) ?>">
                        <input type="text" name="prenom" value="<?= htmlspecialchars($u['prenom']) ?>">
                        <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>">
                        <input type="text" name="telephone" value="<?= $u['telephone'] !== null ? htmlspecialchars($u['telephone']) : '' ?>">
                        <select name="role">
                            <option value="client" <?= $u['role']=='client'?'selected':'' ?>>Client</option>
                            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                            <option value="commercial" <?= $u['role']=='commercial'?'selected':'' ?>>Commercial</option>
                        </select>
                        <button type="submit" name="modifier">Modifier</button>
                    </form>
                    <!-- Bouton suppression -->
                    <a href="?supprimer=<?= $u['id_utilisateur'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
