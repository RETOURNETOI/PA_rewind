<?php
require_once "Commande.php";

class CommandeController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(Commande $c): bool
    {
        $sql = "INSERT INTO COMMANDE (id_utilisateur, date_commande, statut)
                VALUES (:id_user, :date, :statut)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_user'=>$c->getIdUtilisateur(),
            ':date'=>$c->getDateCommande(),
            ':statut'=>$c->getStatut()
        ]);
    }

    public function getById(int $id): ?Commande
    {
        $stmt = $this->db->prepare("SELECT * FROM COMMANDE WHERE id_commande=:id");
        $stmt->execute([':id'=>$id]);
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return null;
        $c = new Commande($row['id_utilisateur'],$row['statut']);
        $c->setIdCommande($row['id_commande']);
        $c->setDateCommande($row['date_commande']);
        return $c;
    }

    public function update(Commande $c): bool
    {
        $sql = "UPDATE COMMANDE SET id_utilisateur=:id_user, date_commande=:date, statut=:statut
                WHERE id_commande=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_user'=>$c->getIdUtilisateur(),
            ':date'=>$c->getDateCommande(),
            ':statut'=>$c->getStatut(),
            ':id'=>$c->getIdCommande()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM COMMANDE WHERE id_commande=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM COMMANDE");
        $list = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $c = new Commande($row['id_utilisateur'],$row['statut']);
            $c->setIdCommande($row['id_commande']);
            $c->setDateCommande($row['date_commande']);
            $list[] = $c;
        }
        return $list;
    }
}
