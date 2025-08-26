<?php
require_once '../bdd/Connexion.php';
require_once '../model/PackEtape.php';

class PackEtapeController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }
    
    public function ajouter(array $data): bool
    {
        try {
            if (empty($data['id_pack']) || empty($data['id_point']) || empty($data['id_hebergement']) || empty($data['ordre'])) {
                throw new Exception("Tous les champs sont obligatoires pour ajouter une étape.");
            }

            $packEtape = new PackEtape(
                $data['id_pack'],
                $data['id_point'],
                $data['id_hebergement'],
                $data['ordre']
            );

            $sql = "INSERT INTO PACK_ETAPE (id_pack, id_point, id_hebergement, ordre)
                    VALUES (:id_pack, :id_point, :id_hebergement, :ordre)";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':id_pack' => $packEtape->getIdPack(),
                ':id_point' => $packEtape->getIdPoint(),
                ':id_hebergement' => $packEtape->getIdHebergement(),
                ':ordre' => $packEtape->getOrdre()
            ]);
        } catch (Exception $e) {
            error_log("Erreur ajout pack étape : " . $e->getMessage());
            return false;
        }
    }

    public function getByPack(int $id_pack): array
    {
        $sql = "SELECT * FROM PACK_ETAPE WHERE id_pack = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_pack]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(int $id_pack, int $id_point, int $id_hebergement): ?array
    {
        $sql = "SELECT * FROM PACK_ETAPE 
                WHERE id_pack = :id_pack AND id_point = :id_point AND id_hebergement = :id_hebergement";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_pack' => $id_pack,
            ':id_point' => $id_point,
            ':id_hebergement' => $id_hebergement
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(array $data): bool
    {
        try {
            if (empty($data['id_pack']) || empty($data['id_point']) || empty($data['id_hebergement'])) {
                throw new Exception("Identifiants obligatoires pour modifier une étape.");
            }

            $sql = "UPDATE PACK_ETAPE SET ordre = :ordre
                    WHERE id_pack = :id_pack AND id_point = :id_point AND id_hebergement = :id_hebergement";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':ordre' => $data['ordre'],
                ':id_pack' => $data['id_pack'],
                ':id_point' => $data['id_point'],
                ':id_hebergement' => $data['id_hebergement']
            ]);
        } catch (Exception $e) {
            error_log("Erreur update pack étape : " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id_pack, int $id_point, int $id_hebergement): bool
    {
        try {
            $sql = "DELETE FROM PACK_ETAPE 
                    WHERE id_pack = :id_pack AND id_point = :id_point AND id_hebergement = :id_hebergement";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':id_pack' => $id_pack,
                ':id_point' => $id_point,
                ':id_hebergement' => $id_hebergement
            ]);
        } catch (Exception $e) {
            error_log("Erreur delete pack étape : " . $e->getMessage());
            return false;
        }
    }
}