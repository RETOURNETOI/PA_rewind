<?php
require_once '../bdd/Connexion.php';
require_once '../model/ItineraireEtape.php';

class ItineraireEtapeController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            foreach (['id_itineraire','id_point','id_hebergement','ordre'] as $k)
                if(!isset($data[$k])) throw new Exception("Champ requis: $k");
            $sql="INSERT INTO ITINERAIRE_ETAPE (id_itineraire, id_point, id_hebergement, ordre)
                  VALUES (:iid, :pid, :hid, :ord)";
            return $this->pdo->prepare($sql)->execute([
                ':iid'=>$data['id_itineraire'],
                ':pid'=>$data['id_point'],
                ':hid'=>$data['id_hebergement'],
                ':ord'=>$data['ordre']
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getByItineraire(int $id_itineraire): array
    {
        $st=$this->pdo->prepare("SELECT * FROM ITINERAIRE_ETAPE WHERE id_itineraire=:id ORDER BY ordre ASC");
        $st->execute([':id'=>$id_itineraire]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(int $id_itineraire, int $id_point, int $ordre): ?array
    {
        $st=$this->pdo->prepare("SELECT * FROM ITINERAIRE_ETAPE WHERE id_itineraire=:iid AND id_point=:pid AND ordre=:ord");
        $st->execute([':iid'=>$id_itineraire, ':pid'=>$id_point, ':ord'=>$ordre]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Met uniquement à jour l’hébergement et/ou l’ordre */
    public function update(int $id_itineraire, int $id_point, int $ordre, array $data): bool
    {
        try{
            $set=[]; $p=[':iid'=>$id_itineraire, ':pid'=>$id_point, ':ord'=>$ordre];
            if(isset($data['id_hebergement'])){ $set[]="id_hebergement=:hid"; $p[':hid']=$data['id_hebergement']; }
            if(isset($data['nouvel_ordre'])){ $set[]="ordre=:nord"; $p[':nord']=$data['nouvel_ordre']; }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE ITINERAIRE_ETAPE SET ".implode(', ',$set)." WHERE id_itineraire=:iid AND id_point=:pid AND ordre=:ord";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id_itineraire, int $id_point, int $ordre): bool
    {
        try{
            $sql="DELETE FROM ITINERAIRE_ETAPE WHERE id_itineraire=:iid AND id_point=:pid AND ordre=:ord";
            return $this->pdo->prepare($sql)->execute([':iid'=>$id_itineraire, ':pid'=>$id_point, ':ord'=>$ordre]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }
}