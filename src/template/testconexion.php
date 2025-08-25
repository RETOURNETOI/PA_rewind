<?php
// test_connexion.php
require_once  '../bdd/Connexion.php';

try {
    $connexion = new Connexion();
    $pdo = $connexion->getPDO();
    echo "<p style='color: green;'>Connexion à la base de données réussie !</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur de connexion : " . $e->getMessage() . "</p>";
}
