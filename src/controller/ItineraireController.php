<?php
require_once '../bdd/Connexion.php';
require_once '../model/Itineraire.php';

class ItineraireController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            if(empty($data['id_utilisateur'])) throw new Exception("id_utilisateur requis.");
            $sql="INSERT INTO ITINERAIRE (id_utilisateur, nom) VALUES (:uid, :nom)";
            return $this->pdo->prepare($sql)->execute([
                ':uid'=>$data['id_utilisateur'],
                ':nom'=>$data['nom'] ?? null
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getById(int $id): ?array
    {
        $st=$this->pdo->prepare("SELECT * FROM ITINERAIRE WHERE id_itineraire=:id");
        $st->execute([':id'=>$id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByUser(int $id_utilisateur): array
    {
        $st=$this->pdo->prepare("SELECT * FROM ITINERAIRE WHERE id_utilisateur=:u ORDER BY id_itineraire DESC");
        $st->execute([':u'=>$id_utilisateur]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        try{
            $set=[]; $p=[':id'=>$id];
            if(isset($data['id_utilisateur'])){ $set[]="id_utilisateur=:uid"; $p[':uid']=$data['id_utilisateur']; }
            if(array_key_exists('nom',$data)){ $set[]="nom=:nom"; $p[':nom']=$data['nom']; }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE ITINERAIRE SET ".implode(', ',$set)." WHERE id_itineraire=:id";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id): bool
    {
        try{
            return $this->pdo->prepare("DELETE FROM ITINERAIRE WHERE id_itineraire=:id")
                             ->execute([':id'=>$id]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM ITINERAIRE ORDER BY id_itineraire DESC")
                         ->fetchAll(PDO::FETCH_ASSOC);
    }
}