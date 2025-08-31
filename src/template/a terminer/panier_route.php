<?php
/**
 * panier_routes.php - Gestionnaire des routes AJAX pour le panier
 * À placer dans le dossier src/view/ ou src/api/
 */

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrage de la session
session_start();

// Définir BASE_PATH si elle n'existe pas déjà
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

// Headers pour les requêtes AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Inclure le contrôleur du panier
require_once __DIR__ . '/../controller/PanierController.php';

$panierController = new PanierController();
$userId = $_SESSION['user_id'];

// Récupérer l'action depuis l'URL
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Extraire l'action (dernière partie de l'URL)
$action = end($pathParts);

// Traitement selon la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($panierController, $userId, $action);
            break;
        case 'POST':
            handlePostRequest($panierController, $userId, $action);
            break;
        case 'OPTIONS':
            // Réponse pour les requêtes CORS preflight
            http_response_code(200);
            exit;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    error_log("Erreur dans panier_routes.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur interne']);
}

/**
 * Traiter les requêtes GET
 */
function handleGetRequest($panierController, $userId, $action) {
    switch ($action) {
        case 'get':
        case 'panier':
            // Récupérer le contenu du panier
            $items = $panierController->getPanierByUserId($userId);
            $total = $panierController->calculerTotal($userId);
            $count = $panierController->compterItems($userId);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'total' => number_format($total, 2),
                'count' => $count
            ]);
            break;

        case 'count':
            // Récupérer juste le nombre d'éléments
            $count = $panierController->compterItems($userId);
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            break;

        case 'total':
            // Récupérer juste le total
            $total = $panierController->calculerTotal($userId);
            echo json_encode([
                'success' => true,
                'total' => number_format($total, 2)
            ]);
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
    }
}

/**
 * Traiter les requêtes POST
 */
function handlePostRequest($panierController, $userId, $action) {
    // Récupérer les données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Données JSON invalides']);
        return;
    }

    switch ($action) {
        case 'add':
            handleAddItem($panierController, $userId, $input);
            break;

        case 'remove':
            handleRemoveItem($panierController, $input);
            break;

        case 'update':
            handleUpdateQuantity($panierController, $input);
            break;

        case 'clear':
            handleClearPanier($panierController, $userId);
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
    }
}

/**
 * Ajouter un élément au panier
 */
function handleAddItem($panierController, $userId, $input) {
    $type = $input['type'] ?? '';
    $itemId = $input['item_id'] ?? 0;
    $quantite = $input['quantity'] ?? 1;

    // Validation des données
    if (!in_array($type, ['service', 'pack'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Type d\'élément invalide']);
        return;
    }

    if (!$itemId || !is_numeric($itemId) || $itemId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID d\'élément invalide']);
        return;
    }

    if (!is_numeric($quantite) || $quantite <= 0 || $quantite > 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Quantité invalide (1-10)']);
        return;
    }

    // Ajouter au panier
    if ($panierController->ajouterAuPanier($userId, $type, $itemId, $quantite)) {
        $newTotal = $panierController->calculerTotal($userId);
        $newCount = $panierController->compterItems($userId);

        echo json_encode([
            'success' => true,
            'message' => 'Élément ajouté au panier',
            'total' => number_format($newTotal, 2),
            'count' => $newCount
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout au panier']);
    }
}

/**
 * Supprimer un élément du panier
 */
function handleRemoveItem($panierController, $input) {
    $itemId = $input['item_id'] ?? 0;

    if (!$itemId || !is_numeric($itemId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID d\'élément manquant']);
        return;
    }

    if ($panierController->supprimerDuPanier($itemId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Élément supprimé du panier'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}

/**
 * Mettre à jour la quantité d'un élément
 */
function handleUpdateQuantity($panierController, $input) {
    $itemId = $input['item_id'] ?? 0;
    $quantite = $input['quantity'] ?? 1;

    if (!$itemId || !is_numeric($itemId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID d\'élément manquant']);
        return;
    }

    if (!is_numeric($quantite) || $quantite < 0 || $quantite > 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Quantité invalide (0-10)']);
        return;
    }

    if ($panierController->updateQuantite($itemId, $quantite)) {
        echo json_encode([
            'success' => true,
            'message' => 'Quantité mise à jour'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
}

/**
 * Vider le panier
 */
function handleClearPanier($panierController, $userId) {
    if ($panierController->viderPanier($userId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Panier vidé avec succès'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors du vidage du panier']);
    }
}
?>