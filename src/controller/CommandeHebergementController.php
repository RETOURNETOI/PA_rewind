<?php
require_once '../bdd/Connexion.php';
require_once '../model/CommandeHebergement.php';

class CommandeHebergementController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            foreach(['id_commande','id_hebergement','date_debut','date_fin','nb_personnes'] as $k)
                if(!isset($data[$k])) throw new Exception("Champ requis: $k");

            $sql="INSERT INTO COMMANDE_HEBERGEMENT (id_commande, id_hebergement, date_debut, date_fin, nb_personnes)
                  VALUES (:cid, :hid, :dd, :df, :nb)";
            return $this->pdo->prepare($sql)->execute([
                ':cid'=>$data['id_commande'],
                ':hid'=>$data['id_hebergement'],
                ':dd'=>$data['date_debut'],
                ':df'=>$data['date_fin'],
                ':nb'=>$data['nb_personnes']
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getByCommande(int $id_commande): array
    {
        $st=$this->pdo->prepare("SELECT * FROM COMMANDE_HEBERGEMENT WHERE id_commande=:id ORDER BY date_debut");
        $st->execute([':id'=>$id_commande]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Mise à jour de la période/nb_personnes */
    public function update(int $id_commande, int $id_hebergement, string $date_debut, array $data): bool
    {
        try{
            $set=[]; $p=[':cid'=>$id_commande, ':hid'=>$id_hebergement, ':dd'=>$date_debut];
            if(isset($data['nouvelle_date_debut'])){ $set[]="date_debut=:ndd"; $p[':ndd']=$data['nouvelle_date_debut']; }
            if(isset($data['date_fin'])){ $set[]="date_fin=:df"; $p[':df']=$data['date_fin']; }
            if(isset($data['nb_personnes'])){ $set[]="nb_personnes=:nb"; $p[':nb']=$data['nb_personnes']; }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE COMMANDE_HEBERGEMENT SET ".implode(', ',$set)." 
                 WHERE id_commande=:cid AND id_hebergement=:hid AND date_debut=:dd";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id_commande, int $id_hebergement, string $date_debut): bool
    {
        try{
            $sql="DELETE FROM COMMANDE_HEBERGEMENT 
                  WHERE id_commande=:cid AND id_hebergement=:hid AND date_debut=:dd";
            return $this->pdo->prepare($sql)->execute([
                ':cid'=>$id_commande, ':hid'=>$id_hebergement, ':dd'=>$date_debut
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }
}