<?php
require_once __DIR__ . '/../bdd/Connexion.php';
require_once __DIR__ .'/../model/Utilisateur.php';

class UtilisateurController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Connexion())->getPDO();
    }

    public function ajouter(array $data): bool
    {
        try {
            if (empty($data['email']) || empty($data['mot_de_passe'])) {
                throw new Exception("Email et mot de passe sont obligatoires.");
            }

            $utilisateur = new Utilisateur(
                $data['nom'] ?? null,
                $data['prenom'] ?? null,
                $data['email'],
                $data['mot_de_passe'],
                $data['telephone'] ?? null,
                $data['role'] ?? 'client'
            );

            $sql = "INSERT INTO utilisateur
                    (nom, prenom, email, mot_de_passe, telephone, date_inscription, role)
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :date_inscription, :role)";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':nom' => $utilisateur->getNom(),
                ':prenom' => $utilisateur->getPrenom(),
                ':email' => $utilisateur->getEmail(),
                ':mot_de_passe' => $utilisateur->getMotDePasse(),
                ':telephone' => $utilisateur->getTelephone(),
                ':date_inscription' => $utilisateur->getDateInscription(),
                ':role' => $utilisateur->getRole(),
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function AfficherTous(): array
    {
        $sql = "SELECT * FROM UTILISATEUR";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function AfficherparId(int $id): ?array
    {
        $sql = "SELECT * FROM UTILISATEUR WHERE id_utilisateur = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function AfficherparNom(string $nom): ?array
    {
        $sql = "SELECT * FROM UTILISATEUR WHERE nom = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => $nom]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function modifier(int $id, array $data): bool
    {
        try {
            $champs = [];
            $params = [':id' => $id];

            if (!empty($data['nom'])) {
                $champs[] = "nom = :nom";
                $params[':nom'] = $data['nom'];
            }
            if (!empty($data['prenom'])) {
                $champs[] = "prenom = :prenom";
                $params[':prenom'] = $data['prenom'];
            }
            if (!empty($data['email'])) {
                $champs[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            if (!empty($data['mot_de_passe'])) {
                $champs[] = "mot_de_passe = :mot_de_passe";
                $params[':mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
            }
            if (!empty($data['telephone'])) {
                $champs[] = "telephone = :telephone";
                $params[':telephone'] = $data['telephone'];
            }
            if (!empty($data['role'])) {
                $champs[] = "role = :role";
                $params[':role'] = $data['role'];
            }

            if (empty($champs)) {
                throw new Exception("Aucun champ à mettre à jour.");
            }

            $sql = "UPDATE UTILISATEUR SET " . implode(', ', $champs) . " WHERE id_utilisateur = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function supprimer(int $id): bool
    {
        try {
            $sql = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function verifierConnexion(string $email, string $mot_de_passe): ?Utilisateur
    {
        try {
            $sql = "SELECT * FROM utilisateur WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($mot_de_passe, $userData['mot_de_passe'])) {
                $utilisateur = new Utilisateur(
                    $userData['nom'],
                    $userData['prenom'],
                    $userData['email'],
                    $userData['mot_de_passe'],
                    $userData['telephone'],
                    $userData['role']
                );
                $utilisateur->setIdUtilisateur($userData['id_utilisateur']);
                $utilisateur->setDateInscription($userData['date_inscription']);
                return $utilisateur;
            }
            return null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}