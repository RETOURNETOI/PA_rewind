<?php
require_once  __DIR__ . '/../bdd/Connexion.php';
require_once  __DIR__ . '/../model/Commande.php';

class CommandeController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }

    public function ajouter(array $data): bool
    {
        try{
            if(empty($data['id_utilisateur'])) throw new Exception("id_utilisateur requis.");
            $sql="INSERT INTO COMMANDE (id_utilisateur, date_commande, statut)
                 VALUES (:uid, :date, :statut)";
            return $this->pdo->prepare($sql)->execute([
                ':uid'=>$data['id_utilisateur'],
                ':date'=>$data['date_commande'] ?? null, // sinon DEFAULT CURRENT_TIMESTAMP
                ':statut'=>$data['statut'] ?? 'en_attente'
            ]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getById(int $id): ?array
    {
        $st=$this->pdo->prepare("SELECT * FROM COMMANDE WHERE id_commande=:id");
        $st->execute([':id'=>$id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByUser(int $id_utilisateur): array
    {
        $st=$this->pdo->prepare("SELECT * FROM COMMANDE WHERE id_utilisateur=:u ORDER BY date_commande DESC");
        $st->execute([':u'=>$id_utilisateur]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        try{
            $set=[]; $p=[':id'=>$id];
            if(isset($data['id_utilisateur'])){ $set[]="id_utilisateur=:uid"; $p[':uid']=$data['id_utilisateur']; }
            if(isset($data['date_commande'])){ $set[]="date_commande=:dc"; $p[':dc']=$data['date_commande']; }
            if(isset($data['statut'])){ $set[]="statut=:st"; $p[':st']=$data['statut']; }
            if(!$set) throw new Exception("Aucun champ à mettre à jour.");
            $sql="UPDATE COMMANDE SET ".implode(', ',$set)." WHERE id_commande=:id";
            return $this->pdo->prepare($sql)->execute($p);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function delete(int $id): bool
    {
        try{
            return $this->pdo->prepare("DELETE FROM COMMANDE WHERE id_commande=:id")->execute([':id'=>$id]);
        }catch(Exception $e){ error_log($e->getMessage()); return false; }
    }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM COMMANDE ORDER BY date_commande DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}