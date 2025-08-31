<?php 

date_default_timezone_set('Europe/Paris');

session_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/connexion');
    exit;
}

$resumeItineraire = $_SESSION['itineraire_success'] ?? null;
$successMessage = $_SESSION['success_message'] ?? null;

unset($_SESSION['itineraire_success'], $_SESSION['success_message']);

if (!$resumeItineraire) {
    header('Location: ' . BASE_PATH . '/composer_itineraire');
    exit;
}

$userName = $_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'itinéraire - Kayak Trip Loire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .confirmation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-align: center;
            margin-bottom: 30px;
        }

        .success-icon {
            font-size: 4em;
            color: #51cf66;
            margin-bottom: 20px;
            animation: bounce 1s ease-in-out;
        }

        .confirmation-title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 15px;
            background: linear-gradient(45deg, #51cf66, #40c057);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .confirmation-message {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
        }

        .details-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        .detail-value {
            color: #666;
            font-weight: 500;
        }

        .total-row {
            background: linear-gradient(45deg, rgba(81, 207, 102, 0.1), rgba(64, 192, 87, 0.1));
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .total-row .detail-label,
        .total-row .detail-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #2e7d32;
        }

        .actions-section {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1em;
            margin: 0 10px;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: #333;
            border: 2px solid rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(45deg, #51cf66, #40c057);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .info-box {
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .info-box h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #666;
            line-height: 1.6;
            margin: 5px 0;
        }

        .next-steps {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            margin-bottom: 30px;
        }

        .next-steps h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .steps-list {
            list-style: none;
            padding: 0;
        }

        .steps-list li {
            padding: 10px 0;
            border-left: 3px solid #667eea;
            padding-left: 15px;
            margin-bottom: 10px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 0 8px 8px 0;
        }

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            padding: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .confirmation-card,
            .details-section {
                padding: 25px 20px;
            }

            .confirmation-title {
                font-size: 2em;
            }

            .detail-row {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }

            .btn {
                display: block;
                margin: 10px 0;
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirmation-card,
        .details-section,
        .next-steps {
            animation: fadeInUp 0.6s ease forwards;
        }

        .details-section {
            animation-delay: 0.2s;
        }

        .next-steps {
            animation-delay: 0.4s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="success-icon">✅</div>
            <h1 class="confirmation-title">Itinéraire confirmé !</h1>
            <p class="confirmation-message">
                <?= $successMessage ? htmlspecialchars($successMessage) : 'Votre itinéraire a été créé avec succès !' ?>
            </p>
        </div>

        <div class="details-section">
            <h2 style="color: #333; margin-bottom: 20px; text-align: center;">📋 Résumé de votre réservation</h2>
            
            <div class="detail-row">
                <span class="detail-label">Nom de l'itinéraire :</span>
                <span class="detail-value"><?= htmlspecialchars($resumeItineraire['nom']) ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Nombre d'étapes :</span>
                <span class="detail-value"><?= $resumeItineraire['nb_etapes'] ?> étapes</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Nombre de personnes :</span>
                <span class="detail-value"><?= $resumeItineraire['nb_personnes'] ?> personne<?= $resumeItineraire['nb_personnes'] > 1 ? 's' : '' ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date de début :</span>
                <span class="detail-value"><?= date('d/m/Y', strtotime($resumeItineraire['date_debut'])) ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Prix hébergements :</span>
                <span class="detail-value"><?= number_format($resumeItineraire['prix_hebergements'], 2) ?>€</span>
            </div>

            <?php if ($resumeItineraire['prix_services'] > 0): ?>
            <div class="detail-row">
                <span class="detail-label">Prix services :</span>
                <span class="detail-value"><?= number_format($resumeItineraire['prix_services'], 2) ?>€</span>
            </div>
            <?php endif; ?>

            <div class="detail-row total-row">
                <span class="detail-label">Prix total :</span>
                <span class="detail-value"><?= number_format($resumeItineraire['prix_total'], 2) ?>€</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Numéro de commande :</span>
                <span class="detail-value">#<?= str_pad($resumeItineraire['commande_id'], 6, '0', STR_PAD_LEFT) ?></span>
            </div>
        </div>

        <div class="info-box">
            <h3>📧 Confirmation par email</h3>
            <p>Un email de confirmation a été envoyé à votre adresse email avec tous les détails de votre réservation.</p>
            <p>Vous y trouverez vos bons de réservation à présenter lors de votre arrivée dans chaque hébergement.</p>
        </div>

        <div class="next-steps">
            <h3>🚀 Prochaines étapes</h3>
            <ul class="steps-list">
                <li>
                    <strong>Vérifiez votre email :</strong> Consultez votre boîte de réception pour la confirmation détaillée
                </li>
                <li>
                    <strong>Préparez votre matériel :</strong> Vérifiez que vous avez tout l'équipement nécessaire pour votre aventure
                </li>
                <li>
                    <strong>Contact hébergements :</strong> Les coordonnées de chaque hébergement sont dans votre email de confirmation
                </li>
                <li>
                    <strong>Météo :</strong> Consultez la météo quelques jours avant votre départ
                </li>
                <li>
                    <strong>Questions ?</strong> Notre équipe reste disponible pour vous accompagner
                </li>
            </ul>
        </div>

        <div class="actions-section">
            <a href="<?= BASE_PATH ?>/profil" class="btn btn-primary">
                📱 Voir mes réservations
            </a>
            <a href="<?= BASE_PATH ?>/composer_itineraire" class="btn btn-secondary">
                🗺️ Créer un autre itinéraire
            </a>
            <a href="<?= BASE_PATH ?>/" class="btn btn-success">
                🏠 Retour à l'accueil
            </a>
        </div>

        <div class="info-box">
            <h3>📞 Besoin d'aide ?</h3>
            <p><strong>Email :</strong> contact@kayaktriploire.fr</p>
            <p><strong>Téléphone :</strong> 02 41 XX XX XX (du lundi au vendredi, 9h-18h)</p>
            <p><strong>En cas d'urgence :</strong> 06 XX XX XX XX (disponible 24h/24 pendant votre séjour)</p>
        </div>

        <footer class="footer">
            <p>© 2025 Kayak Trip Loire - Votre aventure commence maintenant !</p>
            <p>Merci de nous faire confiance pour votre séjour sur la Loire.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.confirmation-card, .details-section, .next-steps, .info-box');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });

            console.log('🎉 Félicitations ! Votre itinéraire Kayak Trip Loire a été confirmé !');
            
            setTimeout(() => {
                const detailsSection = document.querySelector('.details-section');
                if (detailsSection) {
                    detailsSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }, 1500);
        });

        function partagerReservation() {
            if (navigator.share) {
                navigator.share({
                    title: 'Mon itinéraire Kayak Trip Loire',
                    text: 'J\'ai réservé mon aventure sur la Loire ! <?= htmlspecialchars($resumeItineraire['nom']) ?>',
                    url: window.location.href
                }).catch(console.error);
            }
        }

        function imprimerConfirmation() {
            window.print();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const actionsSection = document.querySelector('.actions-section');
            
            if (navigator.share) {
                const shareBtn = document.createElement('button');
                shareBtn.className = 'btn btn-secondary';
                shareBtn.innerHTML = '📤 Partager';
                shareBtn.onclick = partagerReservation;
                actionsSection.appendChild(shareBtn);
            }
            
            const printBtn = document.createElement('button');
            printBtn.className = 'btn btn-secondary';
            printBtn.innerHTML = '🖨️ Imprimer';
            printBtn.onclick = imprimerConfirmation;
            actionsSection.appendChild(printBtn);
        });
    </script>
</body>
</html>