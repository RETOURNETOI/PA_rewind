<?php
// config/database.php - Configuration de la base de données
class DatabaseConfig {
    // Configuration pour l'environnement de développement
    const DB_HOST = 'localhost';
    const DB_NAME = 'kayak_trip';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // Pour la production, utilisez des variables d'environnement
    public static function getConfig() {
        // En production, récupérer depuis les variables d'environnement
        return [
            'host' => $_ENV['DB_HOST'] ?? self::DB_HOST,
            'dbname' => $_ENV['DB_NAME'] ?? self::DB_NAME,
            'username' => $_ENV['DB_USER'] ?? self::DB_USER,
            'password' => $_ENV['DB_PASS'] ?? self::DB_PASS,
            'charset' => self::DB_CHARSET
        ];
    }
    
    public static function getDSN() {
        $config = self::getConfig();
        return "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    }
}

// config/constants.php - Constantes de l'application
<?php
// Configuration générale du site
define('SITE_NAME', 'Kayak Trip');
define('SITE_URL', 'https://votre-domaine.fr'); // À modifier en production
define('SITE_EMAIL', 'contact@kayaktrip.fr');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_PATH', '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', '/uploads');

// Sécurité
define('SALT', 'votre_sel_unique_ici_' . md5(SITE_URL)); // À changer en production
define('SESSION_LIFETIME', 3600 * 24 * 7); // 7 jours
define('TOKEN_EXPIRY', 3600 * 24); // 24h pour les tokens de vérification

// Email
define('SMTP_HOST', 'smtp.gmail.com'); // À configurer selon votre fournisseur
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe');

// Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Messages flash
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');

// Statuts de réservation
define('RESERVATION_PENDING', 1);
define('RESERVATION_CONFIRMED', 2);
define('RESERVATION_CANCELLED', 3);
define('RESERVATION_COMPLETED', 4);

// Types de services
define('SERVICE_TRANSPORT', 'transport');
define('SERVICE_REPAS', 'repas');
define('SERVICE_MATERIEL', 'materiel');
define('SERVICE_AUTRE', 'autre');

// Rôles utilisateur
define('ROLE_CLIENT', 'client');
define('ROLE_ADMIN', 'admin');

// includes/functions.php - Fonctions utilitaires
<?php
/**
 * Fonctions utilitaires pour Kayak Trip
 */

/**
 * Echapper les données pour l'affichage HTML
 */
function escape($data) {
    if (is_array($data)) {
        return array_map('escape', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirection sécurisée
 */
function redirect($url, $code = 302) {
    if (!headers_sent()) {
        header("Location: $url", true, $code);
        exit();
    }
    // Fallback si les headers sont déjà envoyés
    echo "<script>window.location.href='$url';</script>";
    exit();
}

/**
 * Générer un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier le token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Ajouter un message flash
 */
function addFlashMessage($message, $type = FLASH_INFO) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Récupérer et supprimer les messages flash
 */
function getFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifier si l'utilisateur est admin
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === ROLE_ADMIN;
}

/**
 * Obtenir l'utilisateur actuel
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'nom' => $_SESSION['user_nom'] ?? '',
        'prenom' => $_SESSION['user_prenom'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? ROLE_CLIENT
    ];
}

/**
 * Formater un prix
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y') {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

/**
 * Calculer la distance entre deux points (en km)
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Rayon de la Terre en km
    
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    
    $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
    $c = 2 * asin(sqrt($a));
    
    return $earth_radius * $c;
}

/**
 * Générer un slug à partir d'une chaîne
 */
function slugify($text) {
    $text = trim($text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-zA-Z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return strtolower(trim($text, '-'));
}

/**
 * Valider une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valider un numéro de téléphone français
 */
function isValidPhoneFR($phone) {
    $phone = preg_replace('/\s+/', '', $phone);
    return preg_match('/^(?:\+33|0)[1-9](?:[0-9]{8})$/', $phone);
}

/**
 * Générer un mot de passe aléatoire
 */
function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Logger une erreur
 */
function logError($message, $file = '', $line = '') {
    $log = date('Y-m-d H:i:s') . " - ";
    if ($file && $line) {
        $log .= "[$file:$line] ";
    }
    $log .= $message . PHP_EOL;
    
    error_log($log, 3, ROOT_PATH . '/logs/error.log');
}

/**
 * Envoyer un email simple
 */
function sendEmail($to, $subject, $message, $from = SITE_EMAIL) {
    $headers = [
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=UTF-8',
        'From' => $from,
        'Reply-To' => $from,
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    
    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= "$key: $value\r\n";
    }
    
    return mail($to, $subject, $message, $headerString);
}

/**
 * Vérifier la force d'un mot de passe
 */
function checkPasswordStrength($password) {
    $score = 0;
    $errors = [];
    
    if (strlen($password) >= 8) {
        $score++;
    } else {
        $errors[] = "Au moins 8 caractères";
    }
    
    if (preg_match('/[a-z]/', $password)) {
        $score++;
    } else {
        $errors[] = "Au moins une minuscule";
    }
    
    if (preg_match('/[A-Z]/', $password)) {
        $score++;
    } else {
        $errors[] = "Au moins une majuscule";
    }
    
    if (preg_match('/[0-9]/', $password)) {
        $score++;
    } else {
        $errors[] = "Au moins un chiffre";
    }
    
    if (preg_match('/[^a-zA-Z0-9]/', $password)) {
        $score++;
    } else {
        $errors[] = "Au moins un caractère spécial";
    }
    
    return [
        'score' => $score,
        'max' => 5,
        'errors' => $errors,
        'strong' => $score >= 4
    ];
}

/**
 * Nettoyer et valider un upload de fichier
 */
function validateFileUpload($file, $allowedTypes = ALLOWED_EXTENSIONS, $maxSize = MAX_FILE_SIZE) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }
    
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'error' => 'Type de fichier non autorisé'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Fichier trop volumineux'];
    }
    
    return ['success' => true, 'extension' => $extension];
}
?>