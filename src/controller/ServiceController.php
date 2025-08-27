<?php
require_once  __DIR__ . '/../bdd/Connexion.php';
require_once  __DIR__ . '/../model/Service.php';

class ServiceController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }

    public function ajouter(array $data): bool
    {
        try {
            if (empty($data['nom']) || empty($data['prix'])) {
                throw new Exception("Nom et prix du service sont obligatoires.");
            }

            $service = new Service(
                $data['nom'],
                $data['description'] ?? null,
                $data['prix']
            );

            $sql = "INSERT INTO SERVICE (nom, description, prix) VALUES (:nom, :desc, :prix)";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':nom' => $service->getNom(),
                ':desc' => $service->getDescription(),
                ':prix' => $service->getPrix()
            ]);
        } catch (Exception $e) {
            error_log("Erreur ajout service : " . $e->getMessage());
            return false;
        }
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM SERVICE";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM SERVICE WHERE id_service = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
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
                $champs[] = "description = :desc";
                $params[':desc'] = $data['description'];
            }
            if (!empty($data['prix'])) {
                $champs[] = "prix = :prix";
                $params[':prix'] = $data['prix'];
            }

            if (empty($champs)) {
                throw new Exception("Aucun champ à mettre à jour.");
            }

            $sql = "UPDATE SERVICE SET " . implode(', ', $champs) . " WHERE id_service = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Erreur update service : " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM SERVICE WHERE id_service = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur delete service : " . $e->getMessage());
            return false;
        }
    }
}