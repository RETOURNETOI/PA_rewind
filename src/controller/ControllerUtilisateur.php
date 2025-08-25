<?php
require_once "../model/Utilisateur.php";

$u = new Utilisateur(
    "Dupont", 
    "Jean", 
    "jean.dupont@example.com", 
    "monmotdepasse123", 
    "0612345678", 
    "client"
);

echo $u->getNom(); // Dupont
echo $u->getDateInscription(); // 2025-08-25 14:30:12 (exemple)
