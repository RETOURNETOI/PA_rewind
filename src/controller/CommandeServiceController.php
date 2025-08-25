<?php
require_once "CommandeService.php";

class CommandeServiceController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(CommandeService $cs): bool
    {
        $sql = "INSERT INTO COMMANDE_SERVICE (id_commande, id_service, quantite)
                VALUES (:id_commande, :id_service, :quantite)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_commande'=>$cs->getIdCommande(),
            ':id_service'=>$cs->getIdService(),
            ':quantite'=>$cs->getQuantite()
        ]);
    }

    public function getByCommande(int $id_commande): array
    {
        $stmt = $this->db->prepare("SELECT * FROM COMMANDE_SERVICE WHERE id_commande=:id");
        $stmt->execute([':id'=>$id_commande]);
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $cs = new CommandeService($row['id_commande'],$row['id_service'],$row['quantite']);
            $list[] = $cs;
        }
        return $list;
    }

    public function delete(int $id_commande, int $id_service): bool
    {
        $stmt = $this->db->prepare("DELETE FROM COMMANDE_SERVICE 
                                    WHERE id_commande=:id_commande AND id_service=:id_service");
        return $stmt->execute([
            ':id_commande'=>$id_commande,
            ':id_service'=>$id_service
        ]);
    }
}
