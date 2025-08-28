<?php
require_once __DIR__.'/../bdd/Connexion.php';
require_once __DIR__.'/../model/Hebergement.php';

class HebergementController
{
    private PDO $pdo;
    public function __construct(){ $this->pdo = (new Connexion())->getPDO(); }
    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function ajouter(array $data): bool
    {
        try {
            // Vérifiez que les champs requis (y compris id_point) sont présents
            foreach (['id_point', 'nom', 'type', 'capacite', 'prix_nuit'] as $k) {
                if (!isset($data[$k])) {
                    throw new Exception("Champ requis: $k");
                }
            }
    
            $sql = "INSERT INTO HEBERGEMENT (id_point, nom, type, capacite, prix_nuit, description)
                    VALUES (:pid, :nom, :type, :cap, :prix, :desc)";
    
            return $this->pdo->prepare($sql)->execute([
                ':pid' => $data['id_point'],
                ':nom' => $data['nom'],
                ':type' => $data['type'],
                ':cap' => $data['capacite'],
                ':prix' => $data['prix_nuit'],
                ':desc' => $data['description'] ?? null
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getDefaultIdPoint(): ?int
{
    try {
        // Exemple : Récupérer le premier id_point disponible dans la table POINT
        $sql = "SELECT id_point FROM POINT LIMIT 1";
        $st = $this->pdo->query($sql);
        $result = $st->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id_point'] : null;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
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


    // int $id_hebergement, int $id_utilisateur, string $date_debut, string $date_fin, int $nb_personnes
    // $id_hebergement, $date_debut, $date_fin, $nb_personnes
    public function reserver(
        int $id_hebergement,
        int $id_utilisateur,
        string $date_debut,
        string $date_fin,
        int $nb_personnes
    ) : bool {
        try {
            // Vérifie la disponibilité
            if (!$this->estDisponible($id_hebergement, $date_debut, $date_fin, $nb_personnes)) {
                return false;
            }
    
            $sql = "INSERT INTO COMMANDE_HEBERGEMENT 
                    (id_hebergement, id_utilisateur, date_debut, date_fin, nb_personnes)
                    VALUES (:id_hebergement, :id_utilisateur, :date_debut, :date_fin, :nb_personnes)";
            
            $stmt = $this->pdo->prepare($sql);
    
            return $stmt->execute([
                ':id_hebergement' => $id_hebergement,
                ':id_utilisateur' => $id_utilisateur,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin,
                ':nb_personnes' => $nb_personnes
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur réservation : " . $e->getMessage());
            return false;
        }
    }
    



    // Dans HebergementController.php
    public function getReservationsByUser(int $id_utilisateur): array
    {
        try {
            $sql = "SELECT ch.*, h.nom as hebergement_nom 
                    FROM COMMANDE_HEBERGEMENT ch
                    JOIN HEBERGEMENT h ON ch.id_hebergement = h.id_hebergement
                    WHERE ch.id_utilisateur = :id_utilisateur
                    ORDER BY ch.date_debut DESC";
            $st = $this->pdo->prepare($sql);
            $st->execute([':id_utilisateur' => $id_utilisateur]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Dans HebergementController.php
    public function annulerReservation(int $id_commande, int $id_utilisateur): bool
    {
        try {
            $sql = "DELETE FROM COMMANDE_HEBERGEMENT 
                    WHERE id_commande = :id_commande AND id_utilisateur = :id_utilisateur";
            return $this->pdo->prepare($sql)->execute([
                ':id_commande' => $id_commande,
                ':id_utilisateur' => $id_utilisateur
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    



}