<?php

class DashboardStats {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getUsersByRole(): array {
        $stmt = $this->pdo->query("SELECT role, COUNT(*) as count FROM utilisateur GROUP BY role");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCommandeStats(): array {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'payée' THEN 1 ELSE 0 END) as payees,
            SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN statut = 'confirmée' THEN 1 ELSE 0 END) as confirmees,
            SUM(CASE WHEN statut = 'annulée' THEN 1 ELSE 0 END) as annulees
            FROM commande");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRecentInscriptions(int $days = 7): array {
        $stmt = $this->pdo->prepare(
            "SELECT DATE(date_inscription) as date, COUNT(*) as count 
                    FROM utilisateur 
                    WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL :days DAY)
                    GROUP BY DATE(date_inscription) 
                    ORDER BY date DESC");
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTopHebergements(int $limit = 3): array {
        $stmt = $this->pdo->prepare(
            "SELECT h.nom, h.type, h.prix_nuit, pa.nom as point_nom
                    FROM hebergement h 
                    JOIN point_arret pa ON h.id_point = pa.id_point 
                    ORDER BY h.prix_nuit DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMostExpensivePack(): ?array {
        $stmt = $this->pdo->query("SELECT nom, prix FROM pack ORDER BY prix DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function getMostExpensiveService(): ?array {
        $stmt = $this->pdo->query("SELECT nom, prix FROM service ORDER BY prix DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function getRecentUsers(int $limit = 5): array {
        $stmt = $this->pdo->prepare(
            "SELECT nom, prenom, email, role, date_inscription,DATEDIFF(NOW(), date_inscription) as jours_depuis_inscription
                    FROM utilisateur 
                    ORDER BY id_utilisateur DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getHebergementsByType(): array {
        $stmt = $this->pdo->query("SELECT type, COUNT(*) as count FROM hebergement GROUP BY type");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function checkReservationsTable(): bool {
        $stmt = $this->pdo->query(
        "SELECT COUNT(*) as count FROM information_schema.tables 
        WHERE table_schema = DATABASE() AND table_name = 'commande_hebergement'"
        );
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    public function getTotalReservations(): int {
        if (!$this->checkReservationsTable()) {
            return 0;
        }
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM commande_hebergement");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>