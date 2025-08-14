<?php
// classes/PointArret.php - Classe de gestion des points d'arrêt

require_once 'Database.php';

class PointArret {
    private $db;
    private $table = 'points_arret';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupérer tous les points d'arrêt actifs
     */
    public function getAll($orderBy = 'ordre_parcours ASC') {
        return $this->db->query(
            "SELECT * FROM {$this->table} WHERE actif = 1 ORDER BY $orderBy"
        );
    }
    
    /**
     * Récupérer un point d'arrêt par ID
     */
    public function getById($id) {
        return $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE id_point = ? AND actif = 1",
            [$id]
        );
    }
    
    /**
     * Récupérer un point d'arrêt avec ses hébergements
     */
    public function getWithHebergements($id) {
        $point = $this->getById($id);
        if (!$point) return null;
        
        $hebergements = $this->db->query(
            "SELECT * FROM hebergements WHERE id_point = ? AND disponible = 1 ORDER BY prix_base ASC",
            [$id]
        );
        
        $point['hebergements'] = $hebergements;
        return $point;
    }
    
    /**
     * Créer un nouveau point d'arrêt
     */
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $data['actif'] = true;
        $pointId = $this->db->insert($this->table, $data);
        
        return $pointId ? 
            ['success' => true, 'point_id' => $pointId] : 
            ['success' => false, 'error' => 'Erreur lors de la création'];
    }
    
    /**
     * Mettre à jour un point d'arrêt
     */
    public function update($id, $data) {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $result = $this->db->update(
            $this->table,
            $data,
            'id_point = ?',
            [$id]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Supprimer (désactiver) un point d'arrêt
     */
    public function delete($id) {
        $result = $this->db->update(
            $this->table,
            ['actif' => false],
            'id_point = ?',
            [$id]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Rechercher des points d'arrêt
     */
    public function search($term) {
        return $this->db->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 AND (nom LIKE ? OR description LIKE ?)
             ORDER BY ordre_parcours ASC",
            ["%$term%", "%$term%"]
        );
    }
    
    /**
     * Calculer la distance entre deux points
     */
    public function getDistance($point1Id, $point2Id) {
        $point1 = $this->getById($point1Id);
        $point2 = $this->getById($point2Id);
        
        if (!$point1 || !$point2) return null;
        
        return calculateDistance(
            $point1['latitude'], $point1['longitude'],
            $point2['latitude'], $point2['longitude']
        );
    }
    
    /**
     * Validation des données
     */
    private function validate($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['nom']) || strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractères';
        }
        
        if (isset($data['latitude']) && ($data['latitude'] < -90 || $data['latitude'] > 90)) {
            $errors['latitude'] = 'Latitude invalide';
        }
        
        if (isset($data['longitude']) && ($data['longitude'] < -180 || $data['longitude'] > 180)) {
            $errors['longitude'] = 'Longitude invalide';
        }
        
        return $errors;
    }
}

// classes/Hebergement.php - Classe de gestion des hébergements
<?php

require_once 'Database.php';

class Hebergement {
    private $db;
    private $table = 'hebergements';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupérer tous les hébergements avec infos du point d'arrêt
     */
    public function getAll($filters = []) {
        $where = 'h.disponible = 1 AND p.actif = 1';
        $params = [];
        
        // Filtres
        if (!empty($filters['point_id'])) {
            $where .= ' AND h.id_point = ?';
            $params[] = $filters['point_id'];
        }
        
        if (!empty($filters['capacite_min'])) {
            $where .= ' AND h.capacite >= ?';
            $params[] = $filters['capacite_min'];
        }
        
        if (!empty($filters['prix_max'])) {
            $where .= ' AND h.prix_base <= ?';
            $params[] = $filters['prix_max'];
        }
        
        $orderBy = $filters['order'] ?? 'p.ordre_parcours ASC, h.prix_base ASC';
        
        return $this->db->query(
            "SELECT h.*, p.nom as point_nom, p.description as point_description 
             FROM {$this->table} h
             JOIN points_arret p ON h.id_point = p.id_point
             WHERE $where
             ORDER BY $orderBy",
            $params
        );
    }
    
    /**
     * Récupérer un hébergement par ID
     */
    public function getById($id) {
        return $this->db->queryOne(
            "SELECT h.*, p.nom as point_nom, p.description as point_description,
                    p.latitude, p.longitude
             FROM {$this->table} h
             JOIN points_arret p ON h.id_point = p.id_point
             WHERE h.id_hebergement = ? AND h.disponible = 1 AND p.actif = 1",
            [$id]
        );
    }
    
    /**
     * Récupérer les hébergements d'un point d'arrêt
     */
    public function getByPoint($pointId) {
        return $this->db->query(
            "SELECT * FROM {$this->table} 
             WHERE id_point = ? AND disponible = 1 
             ORDER BY prix_base ASC",
            [$pointId]
        );
    }
    
    /**
     * Vérifier la disponibilité d'un hébergement
     */
    public function checkAvailability($hebergementId, $dateDebut, $dateFin, $nbPersonnes) {
        $hebergement = $this->getById($hebergementId);
        if (!$hebergement) {
            return ['available' => false, 'error' => 'Hébergement non trouvé'];
        }
        
        // Vérifier la capacité
        if ($nbPersonnes > $hebergement['capacite']) {
            return [
                'available' => false, 
                'error' => "Capacité insuffisante (max {$hebergement['capacite']} personnes)"
            ];
        }
        
        // Vérifier les réservations existantes
        $conflictingReservations = $this->db->query(
            "SELECT COUNT(*) as count FROM reservations r
             JOIN reservation_etapes re ON r.id_reservation = re.id_reservation
             WHERE re.id_hebergement = ? 
             AND r.id_statut IN (?, ?) 
             AND (
                 (r.date_debut <= ? AND r.date_fin >= ?) OR
                 (r.date_debut <= ? AND r.date_fin >= ?) OR
                 (r.date_debut >= ? AND r.date_fin <= ?)
             )",
            [
                $hebergementId,
                RESERVATION_PENDING, RESERVATION_CONFIRMED,
                $dateDebut, $dateDebut,
                $dateFin, $dateFin,
                $dateDebut, $dateFin
            ]
        );
        
        $conflicts = $conflictingReservations[0]['count'] ?? 0;
        
        if ($conflicts > 0) {
            return ['available' => false, 'error' => 'Hébergement non disponible pour ces dates'];
        }
        
        return ['available' => true, 'price' => $this->calculatePrice($hebergementId, $dateDebut, $dateFin)];
    }
    
    /**
     * Calculer le prix selon la saisonnalité
     */
    public function calculatePrice($hebergementId, $dateDebut, $dateFin) {
        $hebergement = $this->getById($hebergementId);
        if (!$hebergement) return 0;
        
        $prixBase = $hebergement['prix_base'];
        
        // Chercher des tarifs spécifiques pour la période
        $tarifsSpecifiques = $this->db->query(
            "SELECT * FROM tarifs_specifiques 
             WHERE id_hebergement = ? 
             AND date_debut <= ? AND date_fin >= ?
             ORDER BY date_debut ASC",
            [$hebergementId, $dateFin, $dateDebut]
        );
        
        if (!empty($tarifsSpecifiques)) {
            // Prendre le premier tarif spécifique trouvé
            return $tarifsSpecifiques[0]['prix'];
        }
        
        return $prixBase;
    }
    
    /**
     * Créer un nouvel hébergement
     */
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $data['disponible'] = true;
        
        // Gérer les équipements en JSON
        if (isset($data['equipements']) && is_array($data['equipements'])) {
            $data['equipements'] = json_encode($data['equipements']);
        }
        
        $hebergementId = $this->db->insert($this->table, $data);
        
        return $hebergementId ? 
            ['success' => true, 'hebergement_id' => $hebergementId] : 
            ['success' => false, 'error' => 'Erreur lors de la création'];
    }
    
    /**
     * Mettre à jour un hébergement
     */
    public function update($id, $data) {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Gérer les équipements en JSON
        if (isset($data['equipements']) && is_array($data['equipements'])) {
            $data['equipements'] = json_encode($data['equipements']);
        }
        
        $result = $this->db->update(
            $this->table,
            $data,
            'id_hebergement = ?',
            [$id]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Supprimer (désactiver) un hébergement
     */
    public function delete($id) {
        $result = $this->db->update(
            $this->table,
            ['disponible' => false],
            'id_hebergement = ?',
            [$id]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Rechercher des hébergements
     */
    public function search($term, $filters = []) {
        $where = 'h.disponible = 1 AND p.actif = 1 AND (h.nom LIKE ? OR h.description LIKE ? OR p.nom LIKE ?)';
        $params = ["%$term%", "%$term%", "%$term%"];
        
        // Ajouter les filtres
        if (!empty($filters['point_id'])) {
            $where .= ' AND h.id_point = ?';
            $params[] = $filters['point_id'];
        }
        
        if (!empty($filters['capacite_min'])) {
            $where .= ' AND h.capacite >= ?';
            $params[] = $filters['capacite_min'];
        }
        
        if (!empty($filters['prix_max'])) {
            $where .= ' AND h.prix_base <= ?';
            $params[] = $filters['prix_max'];
        }
        
        return $this->db->query(
            "SELECT h.*, p.nom as point_nom 
             FROM {$this->table} h
             JOIN points_arret p ON h.id_point = p.id_point
             WHERE $where
             ORDER BY h.prix_base ASC",
            $params
        );
    }
    
    /**
     * Validation des données
     */
    private function validate($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['nom']) || strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractères';
        }
        
        if (empty($data['capacite']) || $data['capacite'] < 1) {
            $errors['capacite'] = 'La capacité doit être supérieure à 0';
        }
        
        if (empty($data['prix_base']) || $data['prix_base'] < 0) {
            $errors['prix_base'] = 'Le prix de base doit être positif';
        }
        
        if (!empty($data['id_point'])) {
            $pointExists = $this->db->exists('points_arret', 'id_point = ? AND actif = 1', [$data['id_point']]);
            if (!$pointExists) {
                $errors['id_point'] = 'Point d\'arrêt invalide';
            }
        }
        
        return $errors;
    }
}

// classes/Reservation.php - Classe de gestion des réservations
<?php

require_once 'Database.php';
require_once 'User.php';
require_once 'Hebergement.php';

class Reservation {
    private $db;
    private $table = 'reservations';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Créer une nouvelle réservation
     */
    public function create($userId, $data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Vérifier les disponibilités
            $availability = $this->checkAllAvailability($data);
            if (!$availability['available']) {
                throw new Exception($availability['error']);
            }
            
            // Calculer le total
            $total = $this->calculateTotal($data);
            
            // Créer la réservation principale
            $reservationData = [
                'id_user' => $userId,
                'id_pack' => $data['id_pack'] ?? null,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'nb_personnes' => $data['nb_personnes'],
                'total' => $total,
                'id_statut' => RESERVATION_PENDING,
                'commentaires' => $data['commentaires'] ?? ''
            ];
            
            $reservationId = $this->db->insert($this->table, $reservationData);
            
            if (!$reservationId) {
                throw new Exception('Erreur lors de la création de la réservation');
            }
            
            // Ajouter les étapes d'hébergement
            if (!empty($data['etapes'])) {
                foreach ($data['etapes'] as $etape) {
                    $hebergement = new Hebergement();
                    $prix = $hebergement->calculatePrice(
                        $etape['id_hebergement'], 
                        $data['date_debut'], 
                        $data['date_fin']
                    );
                    
                    $etapeData = [
                        'id_reservation' => $reservationId,
                        'id_point' => $etape['id_point'],
                        'id_hebergement' => $etape['id_hebergement'],
                        'jour_etape' => $etape['jour_etape'],
                        'prix_unitaire' => $prix
                    ];
                    
                    $this->db->insert('reservation_etapes', $etapeData);
                }
            }
            
            // Ajouter les services
            if (!empty($data['services'])) {
                foreach ($data['services'] as $service) {
                    $serviceData = [
                        'id_reservation' => $reservationId,
                        'id_service' => $service['id_service'],
                        'quantite' => $service['quantite'] ?? 1,
                        'prix_unitaire' => $service['prix_unitaire']
                    ];
                    
                    $this->db->insert('reservation_services', $serviceData);
                }
            }
            
            // Appliquer la promotion si présente
            if (!empty($data['code_promo'])) {
                $this->applyPromotion($reservationId, $userId, $data['code_promo']);
            }
            
            $this->db->commit();
            
            // Envoyer l'email de confirmation
            $this->sendConfirmationEmail($reservationId);
            
            return ['success' => true, 'reservation_id' => $reservationId];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Récupérer une réservation par ID
     */
    public function getById($id, $userId = null) {
        $where = 'r.id_reservation = ?';
        $params = [$id];
        
        if ($userId) {
            $where .= ' AND r.id_user = ?';
            $params[] = $userId;
        }
        
        $reservation = $this->db->queryOne(
            "SELECT r.*, u.nom, u.prenom, u.email, s.nom as statut_nom
             FROM {$this->table} r
             JOIN users u ON r.id_user = u.id_user
             JOIN statuts_reservation s ON r.id_statut = s.id_statut
             WHERE $where",
            $params
        );
        
        if (!$reservation) return null;
        
        // Récupérer les étapes
        $reservation['etapes'] = $this->db->query(
            "SELECT re.*, p.nom as point_nom, h.nom as hebergement_nom
             FROM reservation_etapes re
             JOIN points_arret p ON re.id_point = p.id_point
             JOIN hebergements h ON re.id_hebergement = h.id_hebergement
             WHERE re.id_reservation = ?
             ORDER BY re.jour_etape ASC",
            [$id]
        );
        
        // Récupérer les services
        $reservation['services'] = $this->db->query(
            "SELECT rs.*, s.nom as service_nom
             FROM reservation_services rs
             JOIN services s ON rs.id_service = s.id_service
             WHERE rs.id_reservation = ?",
            [$id]
        );
        
        return $reservation;
    }
    
    /**
     * Récupérer les réservations d'un utilisateur
     */
    public function getByUser($userId, $page = 1, $statut = null) {
        $where = 'r.id_user = ?';
        $params = [$userId];
        
        if ($statut) {
            $where .= ' AND r.id_statut = ?';
            $params[] = $statut;
        }
        
        return $this->db->paginate(
            "{$this->table} r JOIN statuts_reservation s ON r.id_statut = s.id_statut",
            $page,
            ITEMS_PER_PAGE,
            $where,
            $params,
            'r.date_creation DESC'
        );
    }
    
    /**
     * Récupérer toutes les réservations (admin)
     */
    public function getAll($page = 1, $filters = []) {
        $where = '1=1';
        $params = [];
        
        if (!empty($filters['statut'])) {
            $where .= ' AND r.id_statut = ?';
            $params[] = $filters['statut'];
        }
        
        if (!empty($filters['date_debut'])) {
            $where .= ' AND r.date_debut >= ?';
            $params[] = $filters['date_debut'];
        }
        
        if (!empty($filters['date_fin'])) {
            $where .= ' AND r.date_fin <= ?';
            $params[] = $filters['date_fin'];
        }
        
        if (!empty($filters['search'])) {
            $where .= ' AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)';
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $result = $this->db->paginate(
            "{$this->table} r 
             JOIN users u ON r.id_user = u.id_user 
             JOIN statuts_reservation s ON r.id_statut = s.id_statut",
            $page,
            ITEMS_PER_PAGE,
            $where,
            $params,
            'r.date_creation DESC'
        );
        
        // Ajouter les informations complètes pour chaque réservation
        foreach ($result['data'] as &$reservation) {
            $reservation = $this->getById($reservation['id_reservation']);
        }
        
        return $result;
    }
    
    /**
     * Mettre à jour le statut d'une réservation
     */
    public function updateStatus($id, $newStatus, $adminId = null) {
        $validStatuses = [
            RESERVATION_PENDING,
            RESERVATION_CONFIRMED,
            RESERVATION_CANCELLED,
            RESERVATION_COMPLETED
        ];
        
        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'error' => 'Statut invalide'];
        }
        
        $updateData = [
            'id_statut' => $newStatus,
            'date_modification' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->db->update(
            $this->table,
            $updateData,
            'id_reservation = ?',
            [$id]
        );
        
        if ($result) {
            // Envoyer un email de notification si nécessaire
            $this->sendStatusUpdateEmail($id, $newStatus);
        }
        
        return ['success' => $result];
    }
    
    /**
     * Annuler une réservation
     */
    public function cancel($id, $userId = null) {
        $reservation = $this->getById($id, $userId);
        
        if (!$reservation) {
            return ['success' => false, 'error' => 'Réservation non trouvée'];
        }
        
        if ($reservation['id_statut'] == RESERVATION_CANCELLED) {
            return ['success' => false, 'error' => 'Réservation déjà annulée'];
        }
        
        if ($reservation['id_statut'] == RESERVATION_COMPLETED) {
            return ['success' => false, 'error' => 'Impossible d\'annuler une réservation terminée'];
        }
        
        return $this->updateStatus($id, RESERVATION_CANCELLED);
    }
    
    /**
     * Ajouter une signature numérique
     */
    public function addSignature($id, $signature, $userId = null) {
        $where = 'id_reservation = ?';
        $params = [$id];
        
        if ($userId) {
            $where .= ' AND id_user = ?';
            $params[] = $userId;
        }
        
        $result = $this->db->update(
            $this->table,
            ['signature_numerique' => $signature],
            $where,
            $params
        );
        
        return ['success' => $result];
    }
    
    /**
     * Calculer le total d'une réservation
     */
    private function calculateTotal($data) {
        $total = 0;
        $hebergement = new Hebergement();
        
        // Prix des hébergements
        if (!empty($data['etapes'])) {
            foreach ($data['etapes'] as $etape) {
                $prix = $hebergement->calculatePrice(
                    $etape['id_hebergement'],
                    $data['date_debut'],
                    $data['date_fin']
                );
                $total += $prix * $data['nb_personnes'];
            }
        }
        
        // Prix des services
        if (!empty($data['services'])) {
            foreach ($data['services'] as $service) {
                $total += $service['prix_unitaire'] * ($service['quantite'] ?? 1);
            }
        }
        
        return $total;
    }
    
    /**
     * Vérifier la disponibilité de tous les hébergements
     */
    private function checkAllAvailability($data) {
        if (empty($data['etapes'])) {
            return ['available' => false, 'error' => 'Aucune étape sélectionnée'];
        }
        
        $hebergement = new Hebergement();
        
        foreach ($data['etapes'] as $etape) {
            $availability = $hebergement->checkAvailability(
                $etape['id_hebergement'],
                $data['date_debut'],
                $data['date_fin'],
                $data['nb_personnes']
            );
            
            if (!$availability['available']) {
                return $availability;
            }
        }
        
        return ['available' => true];
    }
    
    /**
     * Appliquer une promotion
     */
    private function applyPromotion($reservationId, $userId, $codePromo) {
        $promo = $this->db->queryOne(
            "SELECT * FROM promotions 
             WHERE code = ? AND actif = 1 
             AND date_debut <= CURDATE() AND date_fin >= CURDATE()",
            [$codePromo]
        );
        
        if (!$promo) return false;
        
        // Vérifier si l'utilisateur a déjà utilisé cette promotion
        $alreadyUsed = $this->db->exists(
            'promotion_usage',
            'id_promo = ? AND id_user = ?',
            [$promo['id_promo'], $userId]
        );
        
        if ($alreadyUsed) return false;
        
        // Vérifier le nombre d'utilisations
        if ($promo['usage_max'] && $promo['usage_actuel'] >= $promo['usage_max']) {
            return false;
        }
        
        // Enregistrer l'utilisation
        $this->db->insert('promotion_usage', [
            'id_promo' => $promo['id_promo'],
            'id_user' => $userId,
            'id_reservation' => $reservationId
        ]);
        
        // Mettre à jour le compteur d'usage
        $this->db->execute(
            "UPDATE promotions SET usage_actuel = usage_actuel + 1 WHERE id_promo = ?",
            [$promo['id_promo']]
        );
        
        // Recalculer le total de la réservation
        $reservation = $this->db->queryOne(
            "SELECT total FROM {$this->table} WHERE id_reservation = ?",
            [$reservationId]
        );
        
        $reduction = 0;
        if ($promo['type'] === 'pourcentage') {
            $reduction = $reservation['total'] * ($promo['reduction'] / 100);
        } else {
            $reduction = $promo['reduction'];
        }
        
        $nouveauTotal = max(0, $reservation['total'] - $reduction);
        
        $this->db->update(
            $this->table,
            ['total' => $nouveauTotal],
            'id_reservation = ?',
            [$reservationId]
        );
        
        return true;
    }
    
    /**
     * Envoyer l'email de confirmation
     */
    private function sendConfirmationEmail($reservationId) {
        $reservation = $this->getById($reservationId);
        if (!$reservation) return false;
        
        $subject = "Confirmation de réservation - " . SITE_NAME;
        $message = "
        <h2>Confirmation de votre réservation</h2>
        <p>Bonjour {$reservation['prenom']} {$reservation['nom']},</p>
        <p>Votre réservation #{$reservationId} a bien été enregistrée.</p>
        
        <h3>Détails de votre séjour :</h3>
        <ul>
            <li><strong>Dates :</strong> du " . formatDate($reservation['date_debut']) . " au " . formatDate($reservation['date_fin']) . "</li>
            <li><strong>Nombre de personnes :</strong> {$reservation['nb_personnes']}</li>
            <li><strong>Total :</strong> " . formatPrice($reservation['total']) . "</li>
        </ul>
        
        <p>Nous vous contacterons prochainement pour finaliser votre réservation.</p>
        <p>Merci de votre confiance !</p>
        ";
        
        return sendEmail($reservation['email'], $subject, $message);
    }
    
    /**
     * Envoyer un email de mise à jour du statut
     */
    private function sendStatusUpdateEmail($reservationId, $newStatus) {
        $reservation = $this->getById($reservationId);
        if (!$reservation) return false;
        
        $statusMessages = [
            RESERVATION_CONFIRMED => 'confirmée',
            RESERVATION_CANCELLED => 'annulée',
            RESERVATION_COMPLETED => 'terminée'
        ];
        
        if (!isset($statusMessages[$newStatus])) return false;
        
        $statusText = $statusMessages[$newStatus];
        
        $subject = "Mise à jour de votre réservation - " . SITE_NAME;
        $message = "
        <h2>Mise à jour de votre réservation</h2>
        <p>Bonjour {$reservation['prenom']} {$reservation['nom']},</p>
        <p>Votre réservation #{$reservationId} a été <strong>$statusText</strong>.</p>
        ";
        
        if ($newStatus == RESERVATION_CONFIRMED) {
            $message .= "<p>Votre séjour est maintenant confirmé ! Nous avons hâte de vous accueillir.</p>";
        } elseif ($newStatus == RESERVATION_CANCELLED) {
            $message .= "<p>Nous sommes désolés que votre séjour ait été annulé. N'hésitez pas à nous contacter pour toute question.</p>";
        }
        
        return sendEmail($reservation['email'], $subject, $message);
    }
    
    /**
     * Validation des données de réservation
     */
    private function validate($data) {
        $errors = [];
        
        if (empty($data['date_debut'])) {
            $errors['date_debut'] = 'Date de début requise';
        }
        
        if (empty($data['date_fin'])) {
            $errors['date_fin'] = 'Date de fin requise';
        }
        
        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            if (strtotime($data['date_debut']) >= strtotime($data['date_fin'])) {
                $errors['dates'] = 'La date de fin doit être postérieure à la date de début';
            }
            
            if (strtotime($data['date_debut']) < strtotime('+1 day')) {
                $errors['date_debut'] = 'La réservation doit être faite au moins 24h à l\'avance';
            }
        }
        
        if (empty($data['nb_personnes']) || $data['nb_personnes'] < 1) {
            $errors['nb_personnes'] = 'Le nombre de personnes doit être supérieur à 0';
        }
        
        if (empty($data['etapes']) && empty($data['id_pack'])) {
            $errors['etapes'] = 'Vous devez sélectionner au moins une étape ou un pack';
        }
        
        return $errors;
    }
    
    /**
     * Obtenir les statistiques des réservations (pour l'admin)
     */
    public function getStatistics($dateDebut = null, $dateFin = null) {
        $where = '1=1';
        $params = [];
        
        if ($dateDebut) {
            $where .= ' AND date_creation >= ?';
            $params[] = $dateDebut;
        }
        
        if ($dateFin) {
            $where .= ' AND date_creation <= ?';
            $params[] = $dateFin;
        }
        
        $stats = [];
        
        // Nombre total de réservations
        $stats['total_reservations'] = $this->db->count($this->table, $where, $params);
        
        // Réservations par statut
        $statsStatut = $this->db->query(
            "SELECT s.nom, COUNT(*) as count 
             FROM {$this->table} r
             JOIN statuts_reservation s ON r.id_statut = s.id_statut
             WHERE $where
             GROUP BY r.id_statut",
            $params
        );
        $stats['par_statut'] = $statsStatut;
        
        // Chiffre d'affaires
        $ca = $this->db->queryOne(
            "SELECT SUM(total) as total FROM {$this->table} 
             WHERE id_statut = ? AND $where",
            array_merge([RESERVATION_CONFIRMED], $params)
        );
        $stats['chiffre_affaires'] = $ca['total'] ?? 0;
        
        // Évolution mensuelle
        $evolutionMensuelle = $this->db->query(
            "SELECT DATE_FORMAT(date_creation, '%Y-%m') as mois, 
                    COUNT(*) as reservations,
                    SUM(CASE WHEN id_statut = ? THEN total ELSE 0 END) as ca
             FROM {$this->table}
             WHERE $where
             GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
             ORDER BY mois DESC
             LIMIT 12",
            array_merge([RESERVATION_CONFIRMED], $params)
        );
        $stats['evolution_mensuelle'] = $evolutionMensuelle;
        
        return $stats;
    }
}