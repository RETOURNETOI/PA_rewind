<?php

class Connexion
{
    private string $host;
    private string $dbname;
    private string $user;
    private string $pass;
    private ?PDO $pdo = null;

    // ======= Constructeur =======
    public function __construct(
        string $host = "localhost",
        string $dbname = "Kayak_Trip",
        string $user = "root",
        string $pass = ""
    ) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
    }

    // ======= Méthode pour obtenir la connexion PDO =======
    public function getPDO(): PDO
    {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
                $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
            

        }
    
        return $this->pdo;
        
    }
    
}
