<?php 
date_default_timezone_set('Europe/Paris');

session_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

$errorMessage = null;
if (isset($_GET['erreur'])) {
    $erreur = $_GET['erreur'];
    switch ($erreur) {
        case 'champs_manquants':
            $errorMessage = 'Email et mot de passe sont obligatoires.';
            break;
        case 'identifiants_invalides':
            $errorMessage = 'Email ou mot de passe incorrect.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue lors de la connexion.';
    }
}

$successMessage = $_SESSION['logout_message'] ?? null;
unset($_SESSION['logout_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Kayak Trip Loire</title>
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

        .message-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .message {
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            animation: messageSlideIn 0.8s ease-out;
            max-width: 500px;
            width: 100%;
        }

        .message-error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .message-info {
            background: linear-gradient(135deg, #2196f3, #42a5f5);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

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

        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }

        .connexion-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            max-width: 450px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }

        .connexion-card::before {
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
            display: inline-block;
            margin: 5px 0;
        }

        .form-links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .forgot-password {
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .required {
            color: #e74c3c;
        }

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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .connexion-card {
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
            .connexion-card {
                padding: 30px 20px;
            }

            .form-header h1 {
                font-size: 1.8em;
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

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .form-group {
            animation: fadeInUp 0.6s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }

        .submit-btn {
            animation: fadeInUp 0.6s ease forwards;
            animation-delay: 0.3s;
        }

        .form-links {
            animation: fadeInUp 0.6s ease forwards;
            animation-delay: 0.4s;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($errorMessage || $successMessage): ?>
        <div class="message-container">
            <?php if ($errorMessage): ?>
                <div class="message message-error">
                    ⚠️ <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($successMessage): ?>
                <div class="message message-info">
                    👋 <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <header class="header">
            <div class="logo">
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/" style="text-decoration: none; color: inherit;">
                    🚣‍♂️ Kayak Trip Loire
                </a>
            </div>
            <div class="user-info">
                <span class="live-time" id="current-time">Chargement...</span>
                <div class="auth-buttons">
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/inscription" class="btn btn-secondary">S'inscrire</a>
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/" class="btn btn-primary">Accueil</a>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="connexion-card">
                <div class="form-header">
                    <h1>🔐 Connexion</h1>
                    <p>Accédez à votre espace personnel pour gérer vos réservations et composer vos itinéraires sur la Loire.</p>
                </div>

                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/traitement_connexion" method="post">
                    <div class="form-group">
                        <label for="email">Adresse e-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="votre.email@exemple.com" required>
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-input" placeholder="Votre mot de passe" required>
                    </div>

                    <button type="submit" class="submit-btn">
                        🚀 Se connecter
                    </button>
                </form>

                <div class="form-links">
                    <div class="forgot-password">
                        <a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a>
                    </div>
                    
                    Vous n'avez pas encore de compte ?<br>
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/inscription">Créer un compte gratuitement</a>
                    <br><br>
                    <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/">← Retour à l'accueil</a>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p style="color: rgba(255,255,255,0.8);">
                © 2025 Kayak Trip Loire. En vous connectant, vous acceptez nos 
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/conditions">conditions générales</a> et notre 
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/confidentialite">politique de confidentialité</a>.
            </p>
        </footer>
    </div>

    <script>
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
<<<<<<< HEAD

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
                const email = document.getElementById('email').value.trim();
                const motDePasse = document.getElementById('mot_de_passe').value;

                if (!email || !motDePasse) {
                    e.preventDefault();
                    this.showError('Email et mot de passe sont obligatoires.');
                    return false;
                }

                if (!this.validateEmail(email)) {
                    e.preventDefault();
                    this.showError('Veuillez entrer une adresse e-mail valide.');
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

        class UXEnhancer {
            constructor() {
                this.init();
            }

            init() {
                this.addFormInteractions();
                this.autoHideMessages();
            }

            addFormInteractions() {
                const inputs = document.querySelectorAll('.form-input');
                inputs.forEach(input => {
                    input.addEventListener('focus', () => {
                        input.parentElement.style.transform = 'scale(1.02)';
                    });

                    input.addEventListener('blur', () => {
                        input.parentElement.style.transform = 'scale(1)';
                    });
                });
            }

            autoHideMessages() {
                const messages = document.querySelectorAll('.message');
                messages.forEach((message, index) => {
                    setTimeout(() => {
                        message.style.opacity = '0';
                        message.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            message.remove();
                        }, 300);
                    }, 5000 + (index * 500));
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const timeManager = new TimeManager();
            const formValidator = new FormValidator();
            const uxEnhancer = new UXEnhancer();
            
            setTimeout(() => {
                document.querySelectorAll('.form-group, .submit-btn, .form-links').forEach((element, index) => {
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);

            console.log('🔐 Page de connexion Kayak Trip Loire initialisée');
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                console.log('Page visible - reprise normale');
            }
        });
    </script>
</body>
</html>