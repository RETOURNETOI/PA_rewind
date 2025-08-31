<?php
/**
 * PanierModel.php
 * Modèle pour la gestion des données du panier
 */

class PanierModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupérer tous les éléments du panier d'un utilisateur avec détails
     */
    public function getPanierByUserId($userId) {
        try {
            $sql = "
                SELECT 
                    p.id_panier as id,
                    p.type,
                    p.item_id,
                    p.quantite,
                    p.date_ajout,
                    CASE 
                        WHEN p.type = 'service' THEN s.nom
                        WHEN p.type = 'pack' THEN pk.nom
                        ELSE 'Inconnu'
                    END as nom,
                    CASE 
                        WHEN p.type = 'service' THEN s.description
                        WHEN p.type = 'pack' THEN pk.description
                        ELSE NULL
                    END as description,
                    CASE 
                        WHEN p.type = 'service' THEN s.prix * p.quantite
                        WHEN p.type = 'pack' THEN pk.prix * p.quantite
                        ELSE 0
                    END as prix_total,
                    CASE 
                        WHEN p.type = 'service' THEN s.prix
                        WHEN p.type = 'pack' THEN pk.prix
                        ELSE 0
                    END as prix_unitaire
                FROM panier p
                LEFT JOIN services s ON p.type = 'service' AND p.item_id = s.id_service
                LEFT JOIN packs pk ON p.type = 'pack' AND p.item_id = pk.id_pack
                WHERE p.id_utilisateur = ?
                ORDER BY p.date_ajout DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getPanierByUserId: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier si un élément existe déjà dans le panier
     */
    public function getPanierItem($userId, $type, $itemId) {
        try {
            $sql = "SELECT * FROM panier WHERE id_utilisateur = ? AND type = ? AND item_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $type, $itemId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getStatistiquesPanier: " . $e->getMessage());
            throw $e;
        }
    }
}
?>OC);
        } catch (Exception $e) {
            error_log("Erreur getPanierItem: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ajouter un nouvel élément au panier
     */
    public function ajouterItem($userId, $type, $itemId, $quantite) {
        try {
            $sql = "INSERT INTO panier (id_utilisateur, type, item_id, quantite, date_ajout) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$userId, $type, $itemId, $quantite]);
        } catch (Exception $e) {
            error_log("Erreur ajouterItem: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour la quantité d'un élément du panier
     */
    public function updateQuantite($panierId, $quantite) {
        try {
            $sql = "UPDATE panier SET quantite = ?, date_ajout = NOW() WHERE id_panier = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$quantite, $panierId]);
        } catch (Exception $e) {
            error_log("Erreur updateQuantite: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer un élément du panier
     */
    public function supprimerItem($panierId) {
        try {
            $sql = "DELETE FROM panier WHERE id_panier = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$panierId]);
        } catch (Exception $e) {
            error_log("Erreur supprimerItem: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vider complètement le panier d'un utilisateur
     */
    public function viderPanier($userId) {
        try {
            $sql = "DELETE FROM panier WHERE id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Erreur viderPanier: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculer le total du panier
     */
    public function calculerTotal($userId) {
        try {
            $sql = "
                SELECT 
                    SUM(
                        CASE 
                            WHEN p.type = 'service' THEN s.prix * p.quantite
                            WHEN p.type = 'pack' THEN pk.prix * p.quantite
                            ELSE 0
                        END
                    ) as total
                FROM panier p
                LEFT JOIN services s ON p.type = 'service' AND p.item_id = s.id_service
                LEFT JOIN packs pk ON p.type = 'pack' AND p.item_id = pk.id_pack
                WHERE p.id_utilisateur = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Erreur calculerTotal: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Compter le nombre d'éléments dans le panier
     */
    public function compterItems($userId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM panier WHERE id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Erreur compterItems: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier la disponibilité d'un service
     */
    public function verifierDisponibiliteService($serviceId) {
        try {
            $sql = "SELECT id_service, nom, prix FROM services WHERE id_service = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$serviceId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur verifierDisponibiliteService: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier la disponibilité d'un pack
     */
    public function verifierDisponibilitePack($packId) {
        try {
            $sql = "SELECT id_pack, nom, prix FROM packs WHERE id_pack = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$packId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur verifierDisponibilitePack: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Nettoyer les anciens paniers (plus de 7 jours)
     */
    public function nettoyerAnciensPaniers() {
        try {
            $sql = "DELETE FROM panier WHERE date_ajout < DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur nettoyerAnciensPaniers: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir les statistiques du panier pour un utilisateur
     */
    public function getStatistiquesPanier($userId) {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as nombre_items,
                    SUM(quantite) as quantite_totale,
                    COUNT(CASE WHEN type = 'service' THEN 1 END) as nb_services,
                    COUNT(CASE WHEN type = 'pack' THEN 1 END) as nb_packs,
                    MAX(date_ajout) as dernier_ajout
                FROM panier 
                WHERE id_utilisateur = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASS