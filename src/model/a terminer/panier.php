<?php

class PanierModel {
    
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
}
?>