<?php
require_once "Pack.php";

class PackController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(Pack $p): bool
    {
        $sql = "INSERT INTO PACK (nom, description, prix) VALUES (:nom, :desc, :prix)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=>$p->getNom(),
            ':desc'=>$p->getDescription(),
            ':prix'=>$p->getPrix()
        ]);
    }

    public function getById(int $id): ?Pack
    {
        $stmt = $this->db->prepare("SELECT * FROM PACK WHERE id_pack=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        return new Pack($row['nom'], $row['description'], $row['prix']);
    }

    public function update(Pack $p): bool
    {
        $sql = "UPDATE PACK SET nom=:nom, description=:desc, prix=:prix WHERE id_pack=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=>$p->getNom(),
            ':desc'=>$p->getDescription(),
            ':prix'=>$p->getPrix(),
            ':id'=>$p->getIdPack()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM PACK WHERE id_pack=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM PACK");
        $list = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $list[] = new Pack($row['nom'],$row['description'],$row['prix']);
        }
        return $list;
    }
}
