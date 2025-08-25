<?php
require_once "Itineraire.php";

class ItineraireController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(Itineraire $i): bool
    {
        $sql = "INSERT INTO ITINERAIRE (id_utilisateur, nom) VALUES (:id_user, :nom)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_user'=>$i->getIdUtilisateur(),
            ':nom'=>$i->getNom()
        ]);
    }

    public function getById(int $id): ?Itineraire
    {
        $stmt = $this->db->prepare("SELECT * FROM ITINERAIRE WHERE id_itineraire=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        $i = new Itineraire($row['id_utilisateur'],$row['nom']);
        $i->setIdItineraire($row['id_itineraire']);
        return $i;
    }

    public function update(Itineraire $i): bool
    {
        $sql = "UPDATE ITINERAIRE SET id_utilisateur=:id_user, nom=:nom WHERE id_itineraire=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_user'=>$i->getIdUtilisateur(),
            ':nom'=>$i->getNom(),
            ':id'=>$i->getIdItineraire()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ITINERAIRE WHERE id_itineraire=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM ITINERAIRE");
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $i = new Itineraire($row['id_utilisateur'],$row['nom']);
            $i->setIdItineraire($row['id_itineraire']);
            $list[] = $i;
        }
        return $list;
    }
}
