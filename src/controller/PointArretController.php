<?php
require_once "PointArret.php";

class PointArretController
{
    private PDO $db;

    public function __construct(PDO $db) { $this->db = $db; }

    public function ajouter(PointArret $point): bool
    {
        $sql = "INSERT INTO POINT_ARRET (nom, description, latitude, longitude) 
                VALUES (:nom, :description, :latitude, :longitude)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $point->getNom(),
            ':description' => $point->getDescription(),
            ':latitude' => $point->getLatitude(),
            ':longitude' => $point->getLongitude()
        ]);
    }

    public function getById(int $id): ?PointArret
    {
        $stmt = $this->db->prepare("SELECT * FROM POINT_ARRET WHERE id_point=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new PointArret($row['nom'], $row['description'], $row['latitude'], $row['longitude']);
    }

    public function update(PointArret $point): bool
    {
        $sql = "UPDATE POINT_ARRET SET nom=:nom, description=:desc, latitude=:lat, longitude=:lng
                WHERE id_point=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $point->getNom(),
            ':desc' => $point->getDescription(),
            ':lat' => $point->getLatitude(),
            ':lng' => $point->getLongitude(),
            ':id' => $point->getIdPoint()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM POINT_ARRET WHERE id_point=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM POINT_ARRET");
        $points = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $points[] = new PointArret($row['nom'], $row['description'], $row['latitude'], $row['longitude']);
        }
        return $points;
    }
}
