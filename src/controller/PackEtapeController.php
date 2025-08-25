<?php
require_once "PackEtape.php";

class PackEtapeController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(PackEtape $pe): bool
    {
        $sql = "INSERT INTO PACK_ETAPE (id_pack, id_point, id_hebergement, ordre)
                VALUES (:id_pack, :id_point, :id_hebergement, :ordre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_pack'=>$pe->getIdPack(),
            ':id_point'=>$pe->getIdPoint(),
            ':id_hebergement'=>$pe->getIdHebergement(),
            ':ordre'=>$pe->getOrdre()
        ]);
    }

    public function getByPack(int $id_pack): array
    {
        $stmt = $this->db->prepare("SELECT * FROM PACK_ETAPE WHERE id_pack=:id");
        $stmt->execute([':id'=>$id_pack]);
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $pe = new PackEtape($row['id_pack'],$row['id_point'],$row['id_hebergement'],$row['ordre']);
            $list[] = $pe;
        }
        return $list;
    }

    public function delete(int $id_pack, int $id_point, int $id_hebergement): bool
    {
        $stmt = $this->db->prepare("DELETE FROM PACK_ETAPE 
                                    WHERE id_pack=:id_pack AND id_point=:id_point AND id_hebergement=:id_hebergement");
        return $stmt->execute([
            ':id_pack'=>$id_pack,
            ':id_point'=>$id_point,
            ':id_hebergement'=>$id_hebergement
        ]);
    }
}

