<?php
require_once __DIR__ . '/../bdd/Connexion.php';
require_once __DIR__ . '/../model/PointArret.php';

class PointArretController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }

    private function validerDonnees(array $data): bool
    {
        if (empty($data['nom'])) {
            return false;
        }
        return true;
    }

    public function ajouter(array $data): bool
    {
        try {
            if (!$this->validerDonnees($data)) {
                throw new Exception("Données invalides : le nom est obligatoire.");
            }

            $point = new PointArret(
                htmlspecialchars($data['nom'], ENT_QUOTES, 'UTF-8'),
                isset($data['description']) ? htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8') : null
            );

            $sql = "INSERT INTO POINT_ARRET (nom, description)
                    VALUES (:nom, :description)";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':nom' => $point->getNom(),
                ':description' => $point->getDescription(),
            ]);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de l'ajout : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur générale lors de l'ajout : " . $e->getMessage());
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $sql = "SELECT * FROM POINT_ARRET";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la récupération des points d'arrêt : " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM POINT_ARRET WHERE id_point = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la récupération du point d'arrêt par ID : " . $e->getMessage());
            return null;
        }
    }

    public function getByNom(string $nom): ?array
    {
        try {
            $sql = "SELECT * FROM POINT_ARRET WHERE nom = :nom";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nom' => $nom]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la récupération du point d'arrêt par nom : " . $e->getMessage());
            return null;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            if (empty($data)) {
                throw new Exception("Aucune donnée à mettre à jour.");
            }

            $champs = [];
            $params = [':id' => $id];

            if (isset($data['nom'])) {
                if (empty($data['nom'])) {
                    throw new Exception("Le nom ne peut pas être vide.");
                }
                $champs[] = "nom = :nom";
                $params[':nom'] = htmlspecialchars($data['nom'], ENT_QUOTES, 'UTF-8');
            }

            if (isset($data['description'])) {
                $champs[] = "description = :description";
                $params[':description'] = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
            }

            if (empty($champs)) {
                throw new Exception("Aucun champ valide à mettre à jour.");
            }

            $sql = "UPDATE POINT_ARRET SET " . implode(', ', $champs) . " WHERE id_point = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur générale lors de la mise à jour : " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM POINT_ARRET WHERE id_point = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la suppression : " . $e->getMessage());
            return false;
        }
    }
}
