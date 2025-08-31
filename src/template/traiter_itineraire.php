<?php
// traiter_itineraire.php - Traitement de la composition d'itinéraire

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/composer_itineraire');
    exit('Méthode non autorisée');
}

// Définir BASE_PATH si pas déjà défini
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

// Démarrer la session
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/connexion');
    exit;
}

// Inclure les contrôleurs nécessaires
require_once __DIR__ . '/../controller/ItineraireController.php';
require_once __DIR__ . '/../controller/ItineraireEtapeController.php';
require_once __DIR__ . '/../controller/CommandeController.php';
require_once __DIR__ . '/../controller/CommandeHebergementController.php';
require_once __DIR__ . '/../controller/CommandeServiceController.php';
require_once __DIR__ . '/../controller/HebergementController.php';

// Validation des données POST
$errors = [];
$userId = $_SESSION['user_id'];

// Validation des champs obligatoires
if (empty(trim($_POST['nom_itineraire'] ?? ''))) {
    $errors[] = 'Le nom de l\'itinéraire est obligatoire';
}
if (empty($_POST['date_debut'] ?? '')) {
    $errors[] = 'La date de début est obligatoire';
}
if (empty($_POST['nb_personnes'] ?? '') || !is_numeric($_POST['nb_personnes'])) {
    $errors[] = 'Le nombre de personnes est obligatoire et doit être un nombre';
}
if (empty($_POST['etapes_data'] ?? '')) {
    $errors[] = 'Vous devez sélectionner au moins une étape';
}

// Validation de la date
$dateDebut = $_POST['date_debut'] ?? '';
if ($dateDebut && strtotime($dateDebut) < strtotime('today')) {
    $errors[] = 'La date de début ne peut pas être dans le passé';
}

// Décoder les données JSON
$etapesData = json_decode($_POST['etapes_data'] ?? '[]', true);
$servicesData = json_decode($_POST['services_data'] ?? '[]', true);

if (empty($etapesData)) {
    $errors[] = 'Aucune étape sélectionnée';
}

// Vérifier que toutes les étapes ont un hébergement
foreach ($etapesData as $etape) {
    if (empty($etape['hebergement_id'])) {
        $errors[] = 'Toutes les étapes doivent avoir un hébergement sélectionné';
        break;
    }
}

// Si erreurs de validation, rediriger
if (!empty($errors)) {
    $_SESSION['itineraire_errors'] = $errors;
    $_SESSION['itineraire_data'] = $_POST;
    header('Location: ' . BASE_PATH . '/composer_itineraire');
    exit;
}

// Données validées
$nomItineraire = trim($_POST['nom_itineraire']);
$nbPersonnes = (int)$_POST['nb_personnes'];

try {
    // Instanciation des contrôleurs
    $itineraireController = new ItineraireController();
    $etapeController = new ItineraireEtapeController();
    $commandeController = new CommandeController();
    $commandeHebergementController = new CommandeHebergementController();
    $commandeServiceController = new CommandeServiceController();
    $hebergementController = new HebergementController();

    // Obtenir la connexion PDO pour la transaction
    $pdo = $hebergementController->getPDO();
    
    // Commencer une transaction
    $pdo->beginTransaction();

    // 1. Créer l'itinéraire
    $itineraireData = [
        'id_utilisateur' => $userId,
        'nom' => $nomItineraire
    ];

    if (!$itineraireController->ajouter($itineraireData)) {
        throw new Exception('Erreur lors de la création de l\'itinéraire');
    }

    // Récupérer l'ID de l'itinéraire créé
    $itineraireId = $pdo->lastInsertId();

    // 2. Ajouter les étapes à l'itinéraire
    foreach ($etapesData as $etape) {
        $etapeData = [
            'id_itineraire' => $itineraireId,
            'id_point' => $etape['point_id'],
            'id_hebergement' => $etape['hebergement_id'],
            'ordre' => $etape['ordre']
        ];

        if (!$etapeController->ajouter($etapeData)) {
            throw new Exception('Erreur lors de l\'ajout de l\'étape : ' . $etape['point_nom']);
        }
    }

    // 3. Créer une commande pour les réservations
    $commandeData = [
        'id_utilisateur' => $userId,
        'date_commande' => date('Y-m-d H:i:s'),
        'statut' => 'en_attente'
    ];

    if (!$commandeController->ajouter($commandeData)) {
        throw new Exception('Erreur lors de la création de la commande');
    }

    $commandeId = $pdo->lastInsertId();

    // 4. Effectuer les réservations d'hébergements
    $totalPrixHebergements = 0;
    $dateActuelle = new DateTime($dateDebut);

    foreach ($etapesData as $index => $etape) {
        $dateFin = clone $dateActuelle;
        $dateFin->add(new DateInterval('P1D')); // Ajouter 1 jour

        // Vérifier la disponibilité
        if (!$hebergementController->estDisponible(
            $etape['hebergement_id'], 
            $dateActuelle->format('Y-m-d'), 
            $dateFin->format('Y-m-d'), 
            $nbPersonnes
        )) {
            throw new Exception('L\'hébergement "' . $etape['hebergement_nom'] . '" n\'est plus disponible pour les dates sélectionnées');
        }

        // Réserver l'hébergement via CommandeHebergement
        $reservationData = [
            'id_commande' => $commandeId,
            'id_hebergement' => $etape['hebergement_id'],
            'date_debut' => $dateActuelle->format('Y-m-d'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'nb_personnes' => $nbPersonnes
        ];

        if (!$commandeHebergementController->ajouter($reservationData)) {
            throw new Exception('Erreur lors de la réservation de l\'hébergement : ' . $etape['hebergement_nom']);
        }

        $totalPrixHebergements += $etape['hebergement_prix'] * $nbPersonnes;
        
        // Passer à l'étape suivante (jour suivant)
        $dateActuelle->add(new DateInterval('P1D'));
    }

    // 5. Ajouter les services sélectionnés
    $totalPrixServices = 0;
    foreach ($servicesData as $service) {
        $serviceCommandeData = [
            'id_commande' => $commandeId,
            'id_service' => $service['service_id'],
            'quantite' => 1
        ];

        if (!$commandeServiceController->ajouter($serviceCommandeData)) {
            throw new Exception('Erreur lors de l\'ajout du service : ' . $service['service_nom']);
        }

        $totalPrixServices += $service['service_prix'];
    }

    // Calculer le prix total
    $prixTotal = $totalPrixHebergements + $totalPrixServices;

    // Valider la transaction
    $pdo->commit();

    // Préparer le message de succès avec détails
    $resumeItineraire = [
        'nom' => $nomItineraire,
        'nb_etapes' => count($etapesData),
        'nb_personnes' => $nbPersonnes,
        'date_debut' => $dateDebut,
        'prix_hebergements' => $totalPrixHebergements,
        'prix_services' => $totalPrixServices,
        'prix_total' => $prixTotal,
        'commande_id' => $commandeId,
        'itineraire_id' => $itineraireId
    ];

    $_SESSION['itineraire_success'] = $resumeItineraire;
    $_SESSION['success_message'] = 'Votre itinéraire "' . $nomItineraire . '" a été créé avec succès !';

    // Redirection vers la page de confirmation ou le profil
    header('Location: ' . BASE_PATH . '/confirmation_itineraire?id=' . $itineraireId);
    exit;

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    error_log('Erreur création itinéraire: ' . $e->getMessage());
    
    $_SESSION['itineraire_errors'] = ['Une erreur est survenue : ' . $e->getMessage()];
    $_SESSION['itineraire_data'] = $_POST;
    
    header('Location: ' . BASE_PATH . '/composer_itineraire');
    exit;
}
?>