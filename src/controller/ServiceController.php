<?php
require_once "Service.php";

class ServiceController
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function ajouter(Service $s): bool
    {
        $sql = "INSERT INTO SERVICE (nom, description, prix) VALUES (:nom, :desc, :prix)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=>$s->getNom(),
            ':desc'=>$s->getDescription(),
            ':prix'=>$s->getPrix()
        ]);
    }

    public function getById(int $id): ?Service
    {
        $stmt = $this->db->prepare("SELECT * FROM SERVICE WHERE id_service=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        return new Service($row['nom'], $row['description'], $row['prix']);
    }

    public function update(Service $s): bool
    {
        $sql = "UPDATE SERVICE SET nom=:nom, description=:desc, prix=:prix WHERE id_service=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=>$s->getNom(),
            ':desc'=>$s->getDescription(),
            ':prix'=>$s->getPrix(),
            ':id'=>$s->getIdService()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM SERVICE WHERE id_service=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM SERVICE");
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $list[] = new Service($row['nom'], $row['description'], $row['prix']);
        }
        return $list;
    }
}
