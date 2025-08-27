<?php
require_once  __DIR__ . '/../bdd/Connexion.php';
require_once  __DIR__ . '/../model/PointArret.php';

class PointArretController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }

    public function ajouter(array $data): bool
    {
        try {
            if (empty($data['nom'])) {
                throw new Exception("Le nom du point d'arrêt est obligatoire.");
            }

            $point = new PointArret(
                $data['nom'],
                $data['description'] ?? null,
                $data['latitude'] ?? null,
                $data['longitude'] ?? null
            );

            $sql = "INSERT INTO POINT_ARRET (nom, description, latitude, longitude) 
                    VALUES (:nom, :description, :latitude, :longitude)";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':nom' => $point->getNom(),
                ':description' => $point->getDescription(),
                ':latitude' => $point->getLatitude(),
                ':longitude' => $point->getLongitude(),
            ]);
        } catch (Exception $e) {
            error_log("Erreur ajout point d'arrêt : " . $e->getMessage());
            return false;
        }
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM POINT_ARRET";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM POINT_ARRET WHERE id_point = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByNom(string $nom): ?array
    {
        $sql = "SELECT * FROM POINT_ARRET WHERE nom = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => $nom]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function update(int $id, array $data): bool
    {
        try {
            $champs = [];
            $params = [':id' => $id];

            if (!empty($data['nom'])) {
                $champs[] = "nom = :nom";
                $params[':nom'] = $data['nom'];
            }
            if (!empty($data['description'])) {
                $champs[] = "description = :description";
                $params[':description'] = $data['description'];
            }
            if (!empty($data['latitude'])) {
                $champs[] = "latitude = :latitude";
                $params[':latitude'] = $data['latitude'];
            }
            if (!empty($data['longitude'])) {
                $champs[] = "longitude = :longitude";
                $params[':longitude'] = $data['longitude'];
            }

            if (empty($champs)) {
                throw new Exception("Aucun champ à mettre à jour.");
            }

            $sql = "UPDATE POINT_ARRET SET " . implode(', ', $champs) . " WHERE id_point = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Erreur update point d'arrêt : " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM POINT_ARRET WHERE id_point = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur delete point d'arrêt : " . $e->getMessage());
            return false;
        }
    }
}
