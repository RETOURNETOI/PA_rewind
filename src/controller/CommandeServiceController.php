<?php
require_once '../bdd/Connexion.php';
require_once '../model/CommandeService.php';

class CommandeServiceController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            foreach(['id_commande','id_service'] as $k)
                if(!isset($data[$k])) throw new Exception("Champ requis: $k");
            $sql="INSERT INTO COMMANDE_SERVICE (id_commande, id_service, quantite)
                  VALUES (:cid, :sid, :qte)";
            return $this->pdo->prepare($sql)->execute([
                ':cid'=>$data['id_commande'],
                ':sid'=>$data['id_service'],
                ':qte'=>$data['quantite'] ?? 1
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getByCommande(int $id_commande): array
    {
        $st=$this->pdo->prepare("SELECT * FROM COMMANDE_SERVICE WHERE id_commande=:id");
        $st->execute([':id'=>$id_commande]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantite(int $id_commande, int $id_service, int $quantite): bool
    {
        try{
            $sql="UPDATE COMMANDE_SERVICE SET quantite=:qte WHERE id_commande=:cid AND id_service=:sid";
            return $this->pdo->prepare($sql)->execute([
                ':qte'=>$quantite, ':cid'=>$id_commande, ':sid'=>$id_service
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id_commande, int $id_service): bool
    {
        try{
            $sql="DELETE FROM COMMANDE_SERVICE WHERE id_commande=:cid AND id_service=:sid";
            return $this->pdo->prepare($sql)->execute([':cid'=>$id_commande, ':sid'=>$id_service]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }
}
?>