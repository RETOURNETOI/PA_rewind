<?php
require_once "CommandeHebergement.php";

class CommandeHebergementController
{
    private PDO $db;
    public function __construct(PDO $db){ $this->db = $db; }

    public function ajouter(CommandeHebergement $ch): bool
    {
        $sql = "INSERT INTO COMMANDE_HEBERGEMENT (id_commande, id_hebergement, date_debut, date_fin, nb_personnes)
                VALUES (:id_commande, :id_hebergement, :date_debut, :date_fin, :nb_personnes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_commande'=>$ch->getIdCommande(),
            ':id_hebergement'=>$ch->getIdHebergement(),
            ':date_debut'=>$ch->getDateDebut(),
            ':date_fin'=>$ch->getDateFin(),
            ':nb_personnes'=>$ch->getNbPersonnes()
        ]);
    }

    public function getByCommande(int $id_commande): array
    {
        $stmt = $this->db->prepare("SELECT * FROM COMMANDE_HEBERGEMENT WHERE id_commande=:id");
        $stmt->execute([':id'=>$id_commande]);
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $ch = new CommandeHebergement($row['id_commande'],$row['id_hebergement'],$row['date_debut'],$row['date_fin'],$row['nb_personnes']);
            $list[] = $ch;
        }
        return $list;
    }

    public function delete(int $id_commande, int $id_hebergement, string $date_debut): bool
    {
        $stmt = $this->db->prepare("DELETE FROM COMMANDE_HEBERGEMENT 
                                    WHERE id_commande=:id_commande AND id_hebergement=:id_hebergement AND date_debut=:date_debut");
        return $stmt->execute([
            ':id_commande'=>$id_commande,
            ':id_hebergement'=>$id_hebergement,
            ':date_debut'=>$date_debut
        ]);
    }
}
