<?php
require_once "Hebergement.php";

class HebergementController
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function ajouter(Hebergement $h): bool
    {
        $sql = "INSERT INTO HEBERGEMENT (id_point, nom, type, capacite, prix_nuit, description)
                VALUES (:id_point, :nom, :type, :capacite, :prix, :desc)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_point'=>$h->getIdPoint(),
            ':nom'=>$h->getNom(),
            ':type'=>$h->getType(),
            ':capacite'=>$h->getCapacite(),
            ':prix'=>$h->getPrixNuit(),
            ':desc'=>$h->getDescription()
        ]);
    }

    public function getById(int $id): ?Hebergement
    {
        $stmt = $this->db->prepare("SELECT * FROM HEBERGEMENT WHERE id_hebergement=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        return new Hebergement($row['id_point'], $row['nom'], $row['type'], $row['capacite'], $row['prix_nuit'], $row['description']);
    }

    public function update(Hebergement $h): bool
    {
        $sql = "UPDATE HEBERGEMENT SET id_point=:id_point, nom=:nom, type=:type, capacite=:cap, prix_nuit=:prix, description=:desc
                WHERE id_hebergement=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_point'=>$h->getIdPoint(),
            ':nom'=>$h->getNom(),
            ':type'=>$h->getType(),
            ':cap'=>$h->getCapacite(),
            ':prix'=>$h->getPrixNuit(),
            ':desc'=>$h->getDescription(),
            ':id'=>$h->getIdHebergement()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM HEBERGEMENT WHERE id_hebergement=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM HEBERGEMENT");
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new Hebergement($row['id_point'], $row['nom'], $row['type'], $row['capacite'], $row['prix_nuit'], $row['description']);
        }
        return $list;
    }
}
