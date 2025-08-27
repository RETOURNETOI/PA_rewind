<?php
require_once  __DIR__ . '/../bdd/Connexion.php';
require_once  __DIR__ . '/../model/Pack.php';

class PackController
{
    private PDO $pdo;
    public function __construct(){ 
        $this->pdo = (new Connexion())->getPDO(); 
    }

    public function ajouter(Pack $pack): bool {
        $sql = "INSERT INTO PACK (nom, description, prix) VALUES (:nom, :description, :prix)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $pack->getNom(),
            ':description' => $pack->getDescription(),
            ':prix' => $pack->getPrix()
        ]);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM PACK WHERE id_pack=:id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getByNom(string $nom): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM PACK WHERE nom=:nom");
        $stmt->execute([':nom'=>$nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        try{
            $set=[]; $p=[':id'=>$id];
            if(isset($data['nom'])){ $set[]="nom=:nom"; $p[':nom']=$data['nom']; }
            if(array_key_exists('description',$data)){ $set[]="description=:desc"; $p[':desc']=$data['description']; }
            if(isset($data['prix'])){ $set[]="prix=:prix"; $p[':prix']=$data['prix']; }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE PACK SET ".implode(', ',$set)." WHERE id_pack=:id";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id): bool
    {
        try{
            $stmt=$this->pdo->prepare("DELETE FROM PACK WHERE id_pack=:id");
            return $stmt->execute([':id'=>$id]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM PACK ORDER BY id_pack DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
