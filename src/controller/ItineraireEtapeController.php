<?php
require_once "ItineraireEtape.php";

class ItineraireEtapeController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(ItineraireEtape $ie): bool
    {
        $sql = "INSERT INTO ITINERAIRE_ETAPE (id_itineraire, id_point, id_hebergement, ordre)
                VALUES (:id_itineraire, :id_point, :id_hebergement, :ordre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_itineraire'=>$ie->getIdItineraire(),
            ':id_point'=>$ie->getIdPoint(),
            ':id_hebergement'=>$ie->getIdHebergement(),
            ':ordre'=>$ie->getOrdre()
        ]);
    }

    public function getByItineraire(int $id_itineraire): array
    {
        $stmt = $this->db->prepare("SELECT * FROM ITINERAIRE_ETAPE WHERE id_itineraire=:id");
        $stmt->execute([':id'=>$id_itineraire]);
        $list = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $ie = new ItineraireEtape($row['id_itineraire'],$row['id_point'],$row['id_hebergement'],$row['ordre']);
            $list[] = $ie;
        }
        return $list;
    }

    public function delete(int $id_itineraire, int $id_point, int $ordre): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ITINERAIRE_ETAPE 
                                    WHERE id_itineraire=:id_itineraire AND id_point=:id_point AND ordre=:ordre");
        return $stmt->execute([
            ':id_itineraire'=>$id_itineraire,
            ':id_point'=>$id_point,
            ':ordre'=>$ordre
        ]);
    }
}
