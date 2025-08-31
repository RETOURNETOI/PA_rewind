<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/inscription');
    exit('Méthode non autorisée');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

session_start();

require_once __DIR__ . '/../controller/UtilisateurController.php';

$_POST['role'] = 'client';

$errors = [];

if (empty(trim($_POST['nom'] ?? ''))) {
    $errors[] = 'Le nom est obligatoire';
}
if (empty(trim($_POST['prenom'] ?? ''))) {
    $errors[] = 'Le prénom est obligatoire';
}
if (empty(trim($_POST['email'] ?? ''))) {
    $errors[] = 'L\'email est obligatoire';
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}
if (empty($_POST['mot_de_passe'] ?? '') || strlen($_POST['mot_de_passe']) < 6) {
    $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
}

if (!empty($errors)) {
    $_SESSION['inscription_errors'] = $errors;
    $_SESSION['inscription_data'] = [
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['telephone'] ?? ''
    ];
    header('Location: ' . BASE_PATH . '/inscription');
    exit;
}

$data = [
    'nom' => trim($_POST['nom']),
    'prenom' => trim($_POST['prenom']),
    'email' => strtolower(trim($_POST['email'])),
    'mot_de_passe' => $_POST['mot_de_passe'],
    'telephone' => !empty(trim($_POST['telephone'])) ? trim($_POST['telephone']) : null,
    'role' => 'client'
];

try {
    $controller = new UtilisateurController();
    
    $resultat = $controller->ajouter($data);
    
    if ($resultat) {
        $utilisateur = $controller->verifierConnexion($data['email'], $data['mot_de_passe']);
        
        if ($utilisateur) {
            $_SESSION['user_id'] = $utilisateur->getIdUtilisateur();
            $_SESSION['user_nom'] = $utilisateur->getNom();
            $_SESSION['user_prenom'] = $utilisateur->getPrenom();
            $_SESSION['user_email'] = $utilisateur->getEmail();
            $_SESSION['user_role'] = $utilisateur->getRole();
            
            $_SESSION['success_message'] = 'Bienvenue ' . $utilisateur->getPrenom() . ' ! Votre compte a été créé avec succès.';
            
            header('Location: ' . BASE_PATH . '/');
            exit;
        } else {
            $_SESSION['success_message'] = 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.';
            header('Location: ' . BASE_PATH . '/connexion');
            exit;
        }
    } else {
        $_SESSION['inscription_errors'] = ['Une erreur est survenue lors de la création du compte. Veuillez réessayer.'];
        $_SESSION['inscription_data'] = [
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'] ?? ''
        ];
        header('Location: ' . BASE_PATH . '/inscription');
        exit;
    }
    
} catch (Exception $e) {
    error_log('Erreur inscription: ' . $e->getMessage());
    
    $errorMessage = 'Une erreur est survenue lors de la création du compte.';
    
    if (strpos($e->getMessage(), 'email') !== false || strpos($e->getMessage(), 'duplicate') !== false) {
        $errorMessage = 'Cette adresse email est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse.';
    }
    
    $_SESSION['inscription_errors'] = [$errorMessage];
    $_SESSION['inscription_data'] = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'] ?? ''
    ];
    
    header('Location: ' . BASE_PATH . '/');
    exit;
}
?>