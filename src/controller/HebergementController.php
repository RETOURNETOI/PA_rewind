<?php
require_once '../bdd/Connexion.php';
require_once '../model/Hebergement.php';

class HebergementController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            foreach(['id_point','nom','type','capacite','prix_nuit'] as $k)
                if(!isset($data[$k])) throw new Exception("Champ requis: $k");
            $sql="INSERT INTO HEBERGEMENT (id_point, nom, type, capacite, prix_nuit, description)
                  VALUES (:pid, :nom, :type, :cap, :prix, :desc)";
            return $this->pdo->prepare($sql)->execute([
                ':pid'=>$data['id_point'],
                ':nom'=>$data['nom'],
                ':type'=>$data['type'],
                ':cap'=>$data['capacite'],
                ':prix'=>$data['prix_nuit'],
                ':desc'=>$data['description'] ?? null
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getById(int $id): ?array
    {
        $st=$this->pdo->prepare("SELECT * FROM HEBERGEMENT WHERE id_hebergement=:id");
        $st->execute([':id'=>$id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByPoint(int $id_point): array
    {
        $st=$this->pdo->prepare("SELECT * FROM HEBERGEMENT WHERE id_point=:p ORDER BY nom");
        $st->execute([':p'=>$id_point]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        try{
            $set=[]; $p=[':id'=>$id];
            foreach(['id_point'=>'pid','nom'=>'nom','type'=>'type','capacite'=>'cap','prix_nuit'=>'prix','description'=>'desc'] as $field=>$ph){
                if(array_key_exists($field,$data)){
                    $set[]="$field=:{$ph}";
                    $p[":{$ph}"]=$data[$field];
                }
            }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE HEBERGEMENT SET ".implode(', ',$set)." WHERE id_hebergement=:id";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id): bool
    {
        try{
            return $this->pdo->prepare("DELETE FROM HEBERGEMENT WHERE id_hebergement=:id")->execute([':id'=>$id]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM HEBERGEMENT ORDER BY id_hebergement DESC")
                         ->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Vérification simple : capacité + chevauchement d’intervalle pour le même hébergement
     *  Note: le schéma ne stocke pas le stock de chambres, on considère 1 réservation à la fois.
     */
    public function estDisponible(int $id_hebergement, string $date_debut, string $date_fin, int $nb_personnes): bool
    {
        try{
            $heb = $this->getById($id_hebergement);
            if(!$heb) return false;
            if($nb_personnes > (int)$heb['capacite']) return false;

            $sql="SELECT 1 FROM COMMANDE_HEBERGEMENT
                  WHERE id_hebergement=:h
                    AND NOT (date_fin <= :debut OR date_debut >= :fin)
                  LIMIT 1";
            $st=$this->pdo->prepare($sql);
            $st->execute([':h'=>$id_hebergement, ':debut'=>$date_debut, ':fin'=>$date_fin]);
            return $st->fetch() ? false : true;
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }
}