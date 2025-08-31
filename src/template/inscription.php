<?php 
// inscription.php - Page d'inscription avec style moderne

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrage de la session
session_start();

// Définir BASE_PATH si elle n'existe pas déjà (pour compatibilité)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Kayak Trip Loire</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .live-time {
            color: #667eea;
            font-weight: 500;
            font-size: 0.9em;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: #333;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Main Content */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }

        .inscription-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            max-width: 500px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }

        .inscription-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-header p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: #999;
        }

        .form-select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .submit-btn {
            width: 100%;
            padding: 15px 30px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .form-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }

        .form-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .required {
            color: #e74c3c;
        }

        .role-info {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
            font-size: 0.85em;
            color: #555;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            color: white;
            margin-top: 40px;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .inscription-card {
                margin: 0 10px;
                padding: 40px 30px;
            }

            .form-header h1 {
                font-size: 2em;
            }

            .container {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .inscription-card {
                padding: 30px 20px;
            }

            .form-header h1 {
                font-size: 1.8em;
            }
        }

        /* Animations */
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

        .form-group {
            animation: fadeInUp 0.6s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }

        .submit-btn {
            animation: fadeInUp 0.6s ease forwards;
            animation-delay: 0.7s;
        }

        .form-links {
            animation: fadeInUp 0.6s ease forwards;
            animation-delay: 0.8s;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/" style="text-decoration: none; color: inherit;">
                    🚣‍♂️ Kayak Trip Loire
                </a>
            </div>
            <div class="user-info">
                <span class="live-time" id="current-time">Chargement...</span>
                <div class="auth-buttons">
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/connexion" class="btn btn-secondary">Se connecter</a>
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/" class="btn btn-primary">Accueil</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="inscription-card">
                <div class="form-header">
                    <h1>🚀 Rejoignez-nous</h1>
                    <p>Créez votre compte pour commencer votre aventure sur la Loire et accéder à tous nos services personnalisés.</p>
                </div>

                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/traiter_ajout_user" method="post">
                    <div class="form-group">
                        <label for="nom">Nom <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" class="form-input" placeholder="Votre nom de famille" required>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom <span class="required">*</span></label>
                        <input type="text" id="prenom" name="prenom" class="form-input" placeholder="Votre prénom" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse e-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="votre.email@exemple.com" required>
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-input" placeholder="Choisissez un mot de passe sécurisé" required>
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-input" placeholder="06 12 34 56 78 (optionnel)">
                    </div>

                    <!-- Rôle forcé à "client" pour sécurité -->
                    <input type="hidden" name="role" value="client">

                    <button type="submit" class="submit-btn">
                        🎯 Créer mon compte
                    </button>
                </form>

                <div class="form-links">
                    Vous avez déjà un compte ? 
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/connexion">Se connecter</a>
                    <br><br>
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/">← Retour à l'accueil</a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p style="color: rgba(255,255,255,0.8);">
                © 2025 Kayak Trip Loire. En créant un compte, vous acceptez nos 
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/conditions">conditions générales</a> et notre 
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/confidentialite">politique de confidentialité</a>.
            </p>
        </footer>
    </div>

    <script>
        // Gestion de l'heure en temps réel
        class TimeManager {
            constructor() {
                this.timezone = 'Europe/Paris';
                this.init();
            }

            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
            }

            updateTime() {
                const now = new Date();
                const timeString = now.toLocaleString('fr-FR', {
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: this.timezone
                });

                const currentTimeEl = document.getElementById('current-time');
                if (currentTimeEl) {
                    currentTimeEl.textContent = timeString;
                }
            }
        }

        // Validation du formulaire
        class FormValidator {
            constructor() {
                this.form = document.querySelector('form');
                this.init();
            }

            init() {
                if (this.form) {
                    this.form.addEventListener('submit', (e) => this.validateForm(e));
                    this.addRealTimeValidation();
                }
            }

            validateForm(e) {
                const nom = document.getElementById('nom').value.trim();
                const prenom = document.getElementById('prenom').value.trim();
                const email = document.getElementById('email').value.trim();
                const motDePasse = document.getElementById('mot_de_passe').value;

                if (!nom || !prenom || !email || !motDePasse) {
                    e.preventDefault();
                    this.showError('Tous les champs marqués d\'un * sont obligatoires.');
                    return false;
                }

                if (!this.validateEmail(email)) {
                    e.preventDefault();
                    this.showError('Veuillez entrer une adresse e-mail valide.');
                    return false;
                }

                if (motDePasse.length < 6) {
                    e.preventDefault();
                    this.showError('Le mot de passe doit contenir au moins 6 caractères.');
                    return false;
                }

                return true;
            }

            validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            addRealTimeValidation() {
                const inputs = this.form.querySelectorAll('.form-input');
                inputs.forEach(input => {
                    input.addEventListener('blur', () => this.validateInput(input));
                    input.addEventListener('input', () => this.clearInputError(input));
                });
            }

            validateInput(input) {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    this.markInputError(input, 'Ce champ est obligatoire');
                } else if (input.type === 'email' && input.value && !this.validateEmail(input.value)) {
                    this.markInputError(input, 'Format d\'e-mail invalide');
                } else if (input.type === 'password' && input.value && input.value.length < 6) {
                    this.markInputError(input, 'Au moins 6 caractères requis');
                } else {
                    this.clearInputError(input);
                }
            }

            markInputError(input, message) {
                input.style.borderColor = '#e74c3c';
                input.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
            }

            clearInputError(input) {
                input.style.borderColor = 'rgba(102, 126, 234, 0.2)';
                input.style.boxShadow = 'none';
            }

            showError(message) {
                alert('⚠️ Erreur de validation\n\n' + message);
            }
        }

        // Amélioration UX
        class UXEnhancer {
            constructor() {
                this.init();
            }

            init() {
                this.addFormInteractions();
                this.addRoleSelectInfo();
            }

            addFormInteractions() {
                const inputs = document.querySelectorAll('.form-input, .form-select');
                inputs.forEach(input => {
                    input.addEventListener('focus', () => {
                        input.parentElement.style.transform = 'scale(1.02)';
                    });

                    input.addEventListener('blur', () => {
                        input.parentElement.style.transform = 'scale(1)';
                    });
                });
            }

            addRoleSelectInfo() {
                const roleSelect = document.getElementById('role');
                const roleInfo = document.querySelector('.role-info');

                if (roleSelect && roleInfo) {
                    roleSelect.addEventListener('change', () => {
                        const selectedRole = roleSelect.value;
                        this.updateRoleInfo(selectedRole, roleInfo);
                    });
                }
            }

            updateRoleInfo(role, infoElement) {
                let infoText = '';
                
                switch(role) {
                    case 'client':
                        infoText = '<strong>Client :</strong> Parfait pour découvrir la Loire ! Accès à la composition d\'itinéraires personnalisés, réservation d\'hébergements, services complémentaires et packs prêts à partir.';
                        break;
                    case 'admin':
                        infoText = '<strong>Administrateur :</strong> Gestion complète de la plateforme, modération des contenus, statistiques avancées. <em>Compte soumis à validation.</em>';
                        break;
                    case 'commercial':
                        infoText = '<strong>Commercial :</strong> Support client, assistance à la réservation, chat en temps réel avec les clients. <em>Compte soumis à validation.</em>';
                        break;
                }

                infoElement.innerHTML = infoText;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            const timeManager = new TimeManager();
            const formValidator = new FormValidator();
            const uxEnhancer = new UXEnhancer();
            
            // Animation d'entrée progressive
            setTimeout(() => {
                document.querySelectorAll('.form-group, .submit-btn, .form-links').forEach((element, index) => {
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);

            console.log('🚀 Page d\'inscription Kayak Trip Loire initialisée');
        });

        // Gestion de la visibilité de la page
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                console.log('Page visible - reprise normale');
            }
        });
    </script>
</body>
</html>