<?php
/**
 * PanierController.php
 * Contrôleur pour la gestion du panier de l'utilisateur
 */

require_once __DIR__ . '/../bdd/Connexion.php';
require_once __DIR__ . '/../model/PanierModel.php';

class PanierController {
    private $panierModel;
    private $pdo;

    public function __construct() {
        $connexion = new Connexion();
        $this->pdo = $connexion->getPDO();
        $this->panierModel = new PanierModel($this->pdo);
    }

    /**
     * Récupérer tous les éléments du panier d'un utilisateur
     */
    public function getPanierByUserId($userId) {
        try {
            return $this->panierModel->getPanierByUserId($userId);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du panier: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ajouter un élément au panier
     */
    public function ajouterAuPanier($userId, $type, $itemId, $quantite = 1) {
        try {
            // Vérifier si l'élément existe déjà dans le panier
            $existingItem = $this->panierModel->getPanierItem($userId, $type, $itemId);
            
            if ($existingItem) {
                // Mettre à jour la quantité
                $nouvelleQuantite = $existingItem['quantite'] + $quantite;
                return $this->panierModel->updateQuantite($existingItem['id_panier'], $nouvelleQuantite);
            } else {
                // Ajouter un nouvel élément
                return $this->panierModel->ajouterItem($userId, $type, $itemId, $quantite);
            }
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout au panier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un élément du panier
     */
    public function supprimerDuPanier($panierId) {
        try {
            return $this->panierModel->supprimerItem($panierId);
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression du panier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vider complètement le panier d'un utilisateur
     */
    public function viderPanier($userId) {
        try {
            return $this->panierModel->viderPanier($userId);
        } catch (Exception $e) {
            error_log("Erreur lors du vidage du panier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour la quantité d'un élément du panier
     */
    public function updateQuantite($panierId, $quantite) {
        try {
            if ($quantite <= 0) {
                return $this->supprimerDuPanier($panierId);
            }
            return $this->panierModel->updateQuantite($panierId, $quantite);
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour de la quantité: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculer le total du panier
     */
    public function calculerTotal($userId) {
        try {
            return $this->panierModel->calculerTotal($userId);
        } catch (Exception $e) {
            error_log("Erreur lors du calcul du total: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compter le nombre d'éléments dans le panier
     */
    public function compterItems($userId) {
        try {
            return $this->panierModel->compterItems($userId);
        } catch (Exception $e) {
            error_log("Erreur lors du comptage des items: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Traiter les requêtes AJAX pour le panier
     */
    public function handleAjaxRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'add':
                $this->handleAddItem($userId, $input);
                break;
            case 'remove':
                $this->handleRemoveItem($input);
                break;
            case 'clear':
                $this->handleClearPanier($userId);
                break;
            case 'update':
                $this->handleUpdateQuantity($input);
                break;
            case 'get':
                $this->handleGetPanier($userId);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
    }

    private function handleAddItem($userId, $input) {
        $type = $input['type'] ?? '';
        $itemId = $input['item_id'] ?? 0;
        $quantite = $input['quantity'] ?? 1;

        if (!in_array($type, ['service', 'pack']) || !$itemId) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        if ($this->ajouterAuPanier($userId, $type, $itemId, $quantite)) {
            echo json_encode([
                'success' => true,
                'message' => 'Ajouté au panier',
                'total' => $this->calculerTotal($userId),
                'count' => $this->compterItems($userId)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    private function handleRemoveItem($input) {
        $itemId = $input['item_id'] ?? 0;

        if (!$itemId) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            return;
        }

        if ($this->supprimerDuPanier($itemId)) {
            echo json_encode(['success' => true, 'message' => 'Supprimé du panier']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    private function handleClearPanier($userId) {
        if ($this->viderPanier($userId)) {
            echo json_encode(['success' => true, 'message' => 'Panier vidé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du vidage']);
        }
    }

    private function handleUpdateQuantity($input) {
        $itemId = $input['item_id'] ?? 0;
        $quantite = $input['quantity'] ?? 1;

        if (!$itemId) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            return;
        }

        if ($this->updateQuantite($itemId, $quantite)) {
            echo json_encode(['success' => true, 'message' => 'Quantité mise à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    private function handleGetPanier($userId) {
        $items = $this->getPanierByUserId($userId);
        $total = $this->calculerTotal($userId);

        echo json_encode([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => count($items)
        ]);
    }

    /**
     * Transformer le panier en commande
     */
    public function transformerEnCommande($userId) {
        try {
            $items = $this->getPanierByUserId($userId);
            
            if (empty($items)) {
                return false;
            }

            // Commencer une transaction
            $this->pdo->beginTransaction();

            // Créer une nouvelle commande
            $stmt = $this->pdo->prepare(
                "INSERT INTO commandes (id_utilisateur, date_commande, statut, prix_total) 
                 VALUES (?, NOW(), 'en_attente', ?)"
            );
            
            $total = $this->calculerTotal($userId);
            $stmt->execute([$userId, $total]);
            $commandeId = $this->pdo->lastInsertId();

            // Transférer les items du panier vers les tables de commande
            foreach ($items as $item) {
                if ($item['type'] === 'service') {
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO commande_services (id_commande, id_service, quantite) 
                         VALUES (?, ?, ?)"
                    );
                    $stmt->execute([$commandeId, $item['item_id'], $item['quantite']]);
                } elseif ($item['type'] === 'pack') {
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO commande_packs (id_commande, id_pack, quantite) 
                         VALUES (?, ?, ?)"
                    );
                    $stmt->execute([$commandeId, $item['item_id'], $item['quantite']]);
                }
            }

            // Vider le panier
            $this->viderPanier($userId);

            // Valider la transaction
            $this->pdo->commit();

            return $commandeId;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->pdo->rollBack();
            error_log("Erreur lors de la transformation en commande: " . $e->getMessage());
            return false;
        }
    }
}
?>