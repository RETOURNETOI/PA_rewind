<?php
// classes/Database.php - Classe de gestion de la base de données

require_once '../config/database.php';

class Database {
    private static $instance = null;
    private $pdo;
    
    /**
     * Constructeur privé pour le pattern Singleton
     */
    private function __construct() {
        try {
            $config = DatabaseConfig::getConfig();
            $dsn = DatabaseConfig::getDSN();
            
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"
            ]);
            
        } catch (PDOException $e) {
            logError("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    /**
     * Récupérer l'instance unique de la base de données
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Récupérer la connexion PDO
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Exécuter une requête SELECT et retourner tous les résultats
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logError("Erreur dans query(): " . $e->getMessage() . " - SQL: $sql");
            return false;
        }
    }
    
    /**
     * Exécuter une requête SELECT et retourner un seul résultat
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logError("Erreur dans queryOne(): " . $e->getMessage() . " - SQL: $sql");
            return false;
        }
    }
    
    /**
     * Exécuter une requête INSERT, UPDATE ou DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("Erreur dans execute(): " . $e->getMessage() . " - SQL: $sql");
            return false;
        }
    }
    
    /**
     * Insérer un enregistrement et retourner l'ID généré
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            logError("Erreur dans insert(): " . $e->getMessage() . " - Table: $table");
            return false;
        }
    }
    
    /**
     * Mettre à jour un enregistrement
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "$column = :$column";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $params = array_merge($data, $whereParams);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("Erreur dans update(): " . $e->getMessage() . " - Table: $table");
            return false;
        }
    }
    
    /**
     * Supprimer un enregistrement
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("Erreur dans delete(): " . $e->getMessage() . " - Table: $table");
            return false;
        }
    }
    
    /**
     * Compter le nombre d'enregistrements
     */
    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE $where";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            logError("Erreur dans count(): " . $e->getMessage() . " - Table: $table");
            return 0;
        }
    }
    
    /**
     * Vérifier si un enregistrement existe
     */
    public function exists($table, $where, $params = []) {
        return $this->count($table, $where, $params) > 0;
    }
    
    /**
     * Commencer une transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Récupérer des enregistrements avec pagination
     */
    public function paginate($table, $page = 1, $limit = ITEMS_PER_PAGE, $where = '1=1', $params = [], $orderBy = 'id DESC') {
        $offset = ($page - 1) * $limit;
        
        // Compter le total
        $total = $this->count($table, $where, $params);
        $totalPages = ceil($total / $limit);
        
        // Récupérer les données
        $sql = "SELECT * FROM $table WHERE $where ORDER BY $orderBy LIMIT $limit OFFSET $offset";
        $data = $this->query($sql, $params);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $total,
                'items_per_page' => $limit,
                'has_next' => $page < $totalPages,
                'has_previous' => $page > 1
            ]
        ];
    }
    
    /**
     * Recherche full-text dans plusieurs colonnes
     */
    public function search($table, $columns, $searchTerm, $limit = ITEMS_PER_PAGE) {
        $searchColumns = [];
        foreach ($columns as $column) {
            $searchColumns[] = "$column LIKE :search";
        }
        $whereClause = implode(' OR ', $searchColumns);
        
        $sql = "SELECT * FROM $table WHERE ($whereClause) LIMIT $limit";
        $params = ['search' => "%$searchTerm%"];
        
        return $this->query($sql, $params);
    }
    
    /**
     * Nettoyer les ressources
     */
    public function __destruct() {
        $this->pdo = null;
    }
}

// classes/User.php - Classe de gestion des utilisateurs
<?php

require_once 'Database.php';

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function create($data) {
        // Validation des données
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Vérifier si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Cette adresse email est déjà utilisée']];
        }
        
        // Hasher le mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Générer un token de vérification
        $data['token_verification'] = bin2hex(random_bytes(32));
        
        // Définir les valeurs par défaut
        $data['role'] = $data['role'] ?? ROLE_CLIENT;
        $data['actif'] = true;
        $data['email_verifie'] = false;
        
        $userId = $this->db->insert($this->table, $data);
        
        if ($userId) {
            // Envoyer l'email de vérification
            $this->sendVerificationEmail($data['email'], $data['token_verification']);
            return ['success' => true, 'user_id' => $userId];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Erreur lors de la création du compte']];
    }
    
    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password) {
        $user = $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE email = ? AND actif = 1",
            [$email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            // Mettre à jour la dernière connexion
            $this->db->update(
                $this->table,
                ['date_modification' => date('Y-m-d H:i:s')],
                'id_user = ?',
                [$user['id_user']]
            );
            
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'error' => 'Email ou mot de passe incorrect'];
    }
    
    /**
     * Démarrer une session utilisateur
     */
    public function startSession($user) {
        session_start();
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
    }
    
    /**
     * Terminer une session utilisateur
     */
    public function endSession() {
        session_start();
        session_destroy();
    }
    
    /**
     * Récupérer un utilisateur par ID
     */
    public function getById($id) {
        return $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE id_user = ? AND actif = 1",
            [$id]
        );
    }
    
    /**
     * Récupérer un utilisateur par email
     */
    public function getByEmail($email) {
        return $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE email = ? AND actif = 1",
            [$email]
        );
    }
    
    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile($userId, $data) {
        $allowedFields = ['nom', 'prenom', 'telephone', 'date_naissance'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return ['success' => false, 'error' => 'Aucune donnée à mettre à jour'];
        }
        
        $updateData['date_modification'] = date('Y-m-d H:i:s');
        
        $result = $this->db->update(
            $this->table,
            $updateData,
            'id_user = ?',
            [$userId]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->getById($userId);
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'error' => 'Mot de passe actuel incorrect'];
        }
        
        $passwordCheck = checkPasswordStrength($newPassword);
        if (!$passwordCheck['strong']) {
            return ['success' => false, 'errors' => $passwordCheck['errors']];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $result = $this->db->update(
            $this->table,
            ['password' => $hashedPassword, 'date_modification' => date('Y-m-d H:i:s')],
            'id_user = ?',
            [$userId]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Vérifier l'email avec le token
     */
    public function verifyEmail($token) {
        $user = $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE token_verification = ? AND actif = 1",
            [$token]
        );
        
        if (!$user) {
            return ['success' => false, 'error' => 'Token de vérification invalide'];
        }
        
        $result = $this->db->update(
            $this->table,
            [
                'email_verifie' => true,
                'token_verification' => null,
                'date_modification' => date('Y-m-d H:i:s')
            ],
            'id_user = ?',
            [$user['id_user']]
        );
        
        return ['success' => $result, 'user' => $user];
    }
    
    /**
     * Demande de réinitialisation de mot de passe
     */
    public function requestPasswordReset($email) {
        $user = $this->getByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Aucun compte associé à cette adresse email'];
        }
        
        $resetToken = bin2hex(random_bytes(32));
        
        $result = $this->db->update(
            $this->table,
            [
                'token_verification' => $resetToken,
                'date_modification' => date('Y-m-d H:i:s')
            ],
            'id_user = ?',
            [$user['id_user']]
        );
        
        if ($result) {
            $this->sendPasswordResetEmail($email, $resetToken);
            return ['success' => true];
        }
        
        return ['success' => false, 'error' => 'Erreur lors de la demande de réinitialisation'];
    }
    
    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword($token, $newPassword) {
        $user = $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE token_verification = ? AND actif = 1",
            [$token]
        );
        
        if (!$user) {
            return ['success' => false, 'error' => 'Token de réinitialisation invalide'];
        }
        
        $passwordCheck = checkPasswordStrength($newPassword);
        if (!$passwordCheck['strong']) {
            return ['success' => false, 'errors' => $passwordCheck['errors']];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $result = $this->db->update(
            $this->table,
            [
                'password' => $hashedPassword,
                'token_verification' => null,
                'date_modification' => date('Y-m-d H:i:s')
            ],
            'id_user = ?',
            [$user['id_user']]
        );
        
        return ['success' => $result];
    }
    
    /**
     * Lister les utilisateurs (pour l'admin)
     */
    public function getAll($page = 1, $search = '') {
        $where = 'actif = 1';
        $params = [];
        
        if (!empty($search)) {
            $where .= ' AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)';
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        return $this->db->paginate($this->table, $page, ITEMS_PER_PAGE, $where, $params, 'date_creation DESC');
    }
    
    /**
     * Validation des données utilisateur
     */
    private function validate($data) {
        $errors = [];
        
        // Nom
        if (empty($data['nom']) || strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        // Prénom
        if (empty($data['prenom']) || strlen($data['prenom']) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères';
        }
        
        // Email
        if (empty($data['email']) || !isValidEmail($data['email'])) {
            $errors['email'] = 'Adresse email invalide';
        }
        
        // Mot de passe
        if (isset($data['password'])) {
            $passwordCheck = checkPasswordStrength($data['password']);
            if (!$passwordCheck['strong']) {
                $errors['password'] = $passwordCheck['errors'];
            }
        }
        
        // Téléphone (optionnel)
        if (!empty($data['telephone']) && !isValidPhoneFR($data['telephone'])) {
            $errors['telephone'] = 'Numéro de téléphone invalide';
        }
        
        return $errors;
    }
    
    /**
     * Vérifier si l'email existe déjà
     */
    private function emailExists($email) {
        return $this->db->exists($this->table, 'email = ?', [$email]);
    }
    
    /**
     * Envoyer l'email de vérification
     */
    private function sendVerificationEmail($email, $token) {
        $verificationUrl = SITE_URL . "/verification?token=$token";
        
        $subject = "Vérification de votre compte " . SITE_NAME;
        $message = "
        <h2>Bienvenue sur " . SITE_NAME . " !</h2>
        <p>Merci de vous être inscrit. Pour activer votre compte, cliquez sur le lien ci-dessous :</p>
        <p><a href='$verificationUrl'>Vérifier mon compte</a></p>
        <p>Si le lien ne fonctionne pas, copiez-collez cette URL dans votre navigateur :</p>
        <p>$verificationUrl</p>
        <p>Ce lien expire dans 24 heures.</p>
        ";
        
        return sendEmail($email, $subject, $message);
    }
    
    /**
     * Envoyer l'email de réinitialisation de mot de passe
     */
    private function sendPasswordResetEmail($email, $token) {
        $resetUrl = SITE_URL . "/reset-password?token=$token";
        
        $subject = "Réinitialisation de votre mot de passe - " . SITE_NAME;
        $message = "
        <h2>Réinitialisation de mot de passe</h2>
        <p>Vous avez demandé une réinitialisation de votre mot de passe.</p>
        <p>Cliquez sur le lien ci-dessous pour créer un nouveau mot de passe :</p>
        <p><a href='$resetUrl'>Réinitialiser mon mot de passe</a></p>
        <p>Si le lien ne fonctionne pas, copiez-collez cette URL dans votre navigateur :</p>
        <p>$resetUrl</p>
        <p>Ce lien expire dans 24 heures.</p>
        <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
        ";
        
        return sendEmail($email, $subject, $message);
    }
}
                '