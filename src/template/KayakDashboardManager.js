// Dashboard Kayak Trip Management System - JavaScript Enhanced
// Version améliorée avec gestion d'état et fonctionnalités avancées

class KayakDashboardManager {
    constructor() {
        this.config = {
            animationDelay: 500,
            progressAnimationDuration: 1000,
            timeUpdateInterval: 1000,
            notificationDuration: 4000,
            cardHoverScale: 1.02,
            cardHoverTranslate: -8
        };
        
        this.state = {
            isAnimating: false,
            notifications: [],
            realTimeData: {
                users: 0,
                reservations: 0,
                lastUpdate: null
            }
        };
        
        this.elements = {};
        this.intervals = {};
        
        this.init();
    }

    init() {
        this.bindElements();
        this.setupEventListeners();
        this.startAnimations();
        this.startRealTimeUpdates();
        console.log('🚣‍♂️ Dashboard Kayak initialisé avec succès');
    }

    bindElements() {
        this.elements = {
            progressBars: document.querySelectorAll('.progress-fill'),
            timeElements: document.querySelectorAll('.current-time'),
            dateElements: document.querySelectorAll('.current-date'),
            statCards: document.querySelectorAll('.stat-card'),
            managementCards: document.querySelectorAll('.management-card'),
            chartCards: document.querySelectorAll('.chart-card'),
            allCards: document.querySelectorAll('.stat-card, .management-card, .chart-card'),
            actionButtons: document.querySelectorAll('.action-btn'),
            managementButtons: document.querySelectorAll('.btn')
        };
    }

    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.handleDOMReady();
        });

        // Gestion du redimensionnement
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // Gestion de la visibilité de la page
        document.addEventListener('visibilitychange', () => {
            this.handleVisibilityChange();
        });

        // Raccourcis clavier
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardShortcuts(e);
        });

        // Gestion des erreurs JavaScript
        window.addEventListener('error', (e) => {
            this.handleGlobalError(e);
        });
    }

    handleDOMReady() {
        this.setupProgressAnimations();
        this.setupCardEffects();
        this.setupActionButtons();
        this.loadUserPreferences();
        this.checkSystemStatus();
    }

    setupProgressAnimations() {
        if (!this.elements.progressBars.length) return;

        this.state.isAnimating = true;
        
        this.elements.progressBars.forEach((bar, index) => {
            const targetWidth = bar.style.width || bar.dataset.width || '0%';
            const targetValue = bar.textContent || '';
            
            // Préparation de l'animation
            bar.style.width = '0%';
            bar.textContent = '0%';
            bar.style.transition = 'none';
            
            // Animation échelonnée
            setTimeout(() => {
                bar.style.transition = `width ${this.config.progressAnimationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                bar.style.width = targetWidth;
                
                // Animation du texte
                this.animateProgressText(bar, targetValue, this.config.progressAnimationDuration);
                
                // Callback de fin d'animation
                setTimeout(() => {
                    bar.classList.add('animation-complete');
                    this.onProgressAnimationComplete(bar, index);
                }, this.config.progressAnimationDuration);
                
            }, index * 150 + this.config.animationDelay);
        });

        setTimeout(() => {
            this.state.isAnimating = false;
        }, this.config.progressAnimationDuration + (this.elements.progressBars.length * 150) + this.config.animationDelay);
    }

    animateProgressText(element, targetText, duration) {
        if (!targetText.includes('%')) return;
        
        const targetValue = parseFloat(targetText);
        const startTime = Date.now();
        
        const updateText = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const currentValue = Math.round(targetValue * this.easeOutCubic(progress));
            
            element.textContent = `${currentValue}%`;
            
            if (progress < 1) {
                requestAnimationFrame(updateText);
            }
        };
        
        requestAnimationFrame(updateText);
    }

    setupCardEffects() {
        this.elements.allCards.forEach(card => {
            const originalTransform = getComputedStyle(card).transform;
            
            // Stockage des propriétés originales
            card.dataset.originalTransform = originalTransform;
            card.dataset.originalZIndex = getComputedStyle(card).zIndex;
            
            // Événements de survol
            card.addEventListener('mouseenter', (e) => {
                this.animateCardEnter(e.currentTarget);
            });
            
            card.addEventListener('mouseleave', (e) => {
                this.animateCardLeave(e.currentTarget);
            });
            
            // Accessibilité
            card.addEventListener('focus', (e) => {
                this.animateCardEnter(e.currentTarget);
            });
            
            card.addEventListener('blur', (e) => {
                this.animateCardLeave(e.currentTarget);
            });
            
            // Effet de clic
            card.addEventListener('mousedown', (e) => {
                this.animateCardPress(e.currentTarget);
            });
            
            card.addEventListener('mouseup', (e) => {
                this.animateCardRelease(e.currentTarget);
            });
        });
    }

    animateCardEnter(card) {
        if (this.state.isAnimating) return;
        
        const { cardHoverScale, cardHoverTranslate } = this.config;
        
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = `translateY(${cardHoverTranslate}px) scale(${cardHoverScale})`;
        card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.2)';
        card.style.zIndex = '10';
        
        // Effet de brillance subtil
        this.addShineEffect(card);
    }

    animateCardLeave(card) {
        const originalTransform = card.dataset.originalTransform || 'none';
        const originalZIndex = card.dataset.originalZIndex || '';
        
        card.style.transform = originalTransform;
        card.style.boxShadow = '';
        card.style.zIndex = originalZIndex;
        
        this.removeShineEffect(card);
    }

    animateCardPress(card) {
        card.style.transform = 'translateY(-6px) scale(0.98)';
    }

    animateCardRelease(card) {
        setTimeout(() => {
            this.animateCardEnter(card);
        }, 100);
    }

    addShineEffect(card) {
        if (card.querySelector('.shine-effect')) return;
        
        const shine = document.createElement('div');
        shine.className = 'shine-effect';
        shine.style.cssText = `
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shine 0.6s ease-in-out;
            pointer-events: none;
            z-index: 1;
        `;
        
        card.style.position = 'relative';
        card.style.overflow = 'hidden';
        card.appendChild(shine);
        
        // Suppression automatique après animation
        setTimeout(() => {
            if (shine.parentNode) {
                shine.parentNode.removeChild(shine);
            }
        }, 600);
    }

    removeShineEffect(card) {
        const shine = card.querySelector('.shine-effect');
        if (shine) {
            shine.remove();
        }
    }

    startRealTimeUpdates() {
        setInterval(() => {
            const now = new Date();
            const timeElements = document.querySelectorAll('.time');
            timeElements.forEach(el => {
                el.textContent = now.toLocaleTimeString();
            });
        }, 1000);
    }

    updateTime() {
        const now = new Date();
        
        // Mise à jour des éléments de temps
        this.elements.timeElements.forEach(el => {
            el.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        });

        // Mise à jour des éléments de date
        this.elements.dateElements.forEach(el => {
            el.textContent = now.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        });

        // Mise à jour de l'état
        this.state.realTimeData.lastUpdate = now;
    }

    updateRealTimeData() {
        // Simulation de données en temps réel
        // Dans un vrai projet, ceci ferait des appels AJAX
        
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(number => {
            const currentValue = parseInt(number.textContent) || 0;
            
            // Simulation d'une légère variation
            if (Math.random() > 0.7) {
                const variation = Math.random() > 0.5 ? 1 : -1;
                const newValue = Math.max(0, currentValue + variation);
                this.animateNumberChange(number, newValue);
            }
        });

        console.log('🔄 Données mises à jour:', new Date().toLocaleTimeString('fr-FR'));
    }

    animateNumberChange(element, newValue) {
        element.style.transform = 'scale(1.1)';
        element.style.color = '#51cf66';
        
        setTimeout(() => {
            element.textContent = newValue;
            element.style.transform = 'scale(1)';
            element.style.color = '';
        }, 200);
    }

    setupActionButtons() {
        // Gestion des boutons de navigation
        this.elements.actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleActionClick(e);
            });
        });

        // Gestion des boutons de gestion
        this.elements.managementButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleManagementClick(e);
            });
        });
    }

    handleActionClick(e) {
        const button = e.currentTarget;
        const action = this.extractActionFromButton(button);
        
        // Animation du bouton
        this.animateButtonClick(button);
        
        // Traitement de l'action
        switch (action) {
            case 'notifications':
                this.showNotifications();
                e.preventDefault();
                break;
            case 'parametres':
                this.showSettings();
                e.preventDefault();
                break;
            case 'gestion-pack':
                this.navigateWithLoading(button.href || 'admintest.php');
                e.preventDefault();
                break;
            case 'gestion-utilisateur':
                this.navigateWithLoading(button.href || 'gestionuser.php');
                e.preventDefault();
                break;
            case 'rapport-complet':
                this.generateReport();
                e.preventDefault();
                break;
        }
    }

    extractActionFromButton(button) {
        const text = button.textContent.toLowerCase();
        if (text.includes('notification')) return 'notifications';
        if (text.includes('paramètre')) return 'parametres';
        if (text.includes('pack')) return 'gestion-pack';
        if (text.includes('utilisateur')) return 'gestion-utilisateur';
        if (text.includes('rapport')) return 'rapport-complet';
        return 'unknown';
    }

    animateButtonClick(button) {
        button.style.transform = 'scale(0.95)';
        button.style.transition = 'transform 0.1s ease';
        
        setTimeout(() => {
            button.style.transform = '';
        }, 100);
    }

    navigateWithLoading(url) {
        this.showNotification('Redirection en cours...', 'info');
        
        // Simulation d'un délai de chargement
        setTimeout(() => {
            window.location.href = url;
        }, 500);
    }

    generateReport() {
        this.showNotification('Génération du rapport en cours...', 'info');
        
        // Simulation de la génération
        setTimeout(() => {
            this.showNotification('Rapport généré avec succès!', 'success');
        }, 2000);
    }

    showNotifications() {
        const notifications = [
            { type: 'info', message: '3 nouvelles réservations aujourd\'hui' },
            { type: 'warning', message: 'Vérification système programmée demain' },
            { type: 'success', message: 'Sauvegarde automatique effectuée' }
        ];

        notifications.forEach((notif, index) => {
            setTimeout(() => {
                this.showNotification(notif.message, notif.type);
            }, index * 500);
        });
    }

    showSettings() {
        this.showNotification('Paramètres à implémenter', 'info');
    }

    showNotification(message, type = 'info', duration = null) {
        duration = duration || this.config.notificationDuration;
        
        const notification = document.createElement('div');
        notification.className = `dashboard-notification notification-${type}`;
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${icons[type] || icons.info}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" aria-label="Fermer">&times;</button>
            </div>
            <div class="notification-progress"></div>
        `;

        // Styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-left: 4px solid ${colors[type]};
            z-index: 10000;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        `;

        const content = notification.querySelector('.notification-content');
        content.style.cssText = `
            display: flex;
            align-items: center;
            padding: 16px 20px;
            gap: 12px;
        `;

        const progress = notification.querySelector('.notification-progress');
        progress.style.cssText = `
            height: 3px;
            background: ${colors[type]};
            transform: scaleX(1);
            transform-origin: left;
            transition: transform ${duration}ms linear;
        `;

        // Ajout au DOM
        document.body.appendChild(notification);
        this.state.notifications.push(notification);

        // Animation d'entrée
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });

        // Animation de la barre de progression
        setTimeout(() => {
            progress.style.transform = 'scaleX(0)';
        }, 100);

        // Gestionnaire de fermeture
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.removeNotification(notification);
        });

        // Auto-suppression
        setTimeout(() => {
            this.removeNotification(notification);
        }, duration);

        return notification;
    }

    removeNotification(notification) {
        if (!notification.parentNode) return;

        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            
            // Suppression de l'état
            const index = this.state.notifications.indexOf(notification);
            if (index > -1) {
                this.state.notifications.splice(index, 1);
            }
        }, 300);
    }

    handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + R : Actualiser les données
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            this.refreshDashboard();
            return;
        }

        // Escape : Fermer toutes les notifications
        if (e.key === 'Escape') {
            this.clearAllNotifications();
            return;
        }

        // Ctrl/Cmd + N : Nouvelle notification test
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            this.showNotification('Notification de test', 'info');
            return;
        }
    }

    refreshDashboard() {
        this.showNotification('Actualisation du dashboard...', 'info');
        this.updateRealTimeData();
        
        // Simulation d'un rafraîchissement
        setTimeout(() => {
            this.showNotification('Dashboard actualisé!', 'success');
        }, 1000);
    }

    clearAllNotifications() {
        this.state.notifications.forEach(notification => {
            this.removeNotification(notification);
        });
    }

    handleResize() {
        // Réajustement responsive si nécessaire
        console.log('🔄 Redimensionnement détecté');
    }

    handleVisibilityChange() {
        if (document.hidden) {
            // Pause des animations quand l'onglet n'est pas visible
            this.pauseAnimations();
        } else {
            // Reprise des animations
            this.resumeAnimations();
        }
    }

    pauseAnimations() {
        Object.values(this.intervals).forEach(interval => {
            if (interval) clearInterval(interval);
        });
        console.log('⏸️ Animations mises en pause');
    }

    resumeAnimations() {
        this.startRealTimeUpdates();
        console.log('▶️ Animations reprises');
    }

    handleGlobalError(e) {
        console.error('🚨 Erreur JavaScript détectée:', e.error);
        this.showNotification('Une erreur est survenue', 'error');
    }

    loadUserPreferences() {
        // Chargement des préférences utilisateur depuis localStorage
        // (si disponible dans le contexte)
        try {
            const prefs = localStorage.getItem('kayak-dashboard-prefs');
            if (prefs) {
                const preferences = JSON.parse(prefs);
                this.applyUserPreferences(preferences);
            }
        } catch (e) {
            // localStorage non disponible ou erreur
            console.log('Préférences utilisateur non disponibles');
        }
    }

    applyUserPreferences(prefs) {
        if (prefs.animationSpeed) {
            this.config.progressAnimationDuration = prefs.animationSpeed;
        }
        if (prefs.notificationDuration) {
            this.config.notificationDuration = prefs.notificationDuration;
        }
    }

    checkSystemStatus() {
        // Vérification du statut du système
        const memoryUsage = this.getMemoryUsage();
        if (memoryUsage > 80) {
            this.showNotification('Utilisation mémoire élevée détectée', 'warning');
        }
        
        this.showNotification('Système initialisé avec succès', 'success', 2000);
    }

    getMemoryUsage() {
        // Simulation d'un check mémoire
        return Math.random() * 100;
    }

    // Fonction utilitaires
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    onProgressAnimationComplete(bar, index) {
        // Callback appelé quand une animation de barre de progression est terminée
        console.log(`✅ Animation barre ${index + 1} terminée`);
        
        if (index === this.elements.progressBars.length - 1) {
            // Toutes les animations sont terminées
            this.showNotification('Dashboard chargé!', 'success', 2000);
        }
    }

    // Méthode de nettoyage
    destroy() {
        // Nettoyage des intervals
        Object.values(this.intervals).forEach(interval => {
            if (interval) clearInterval(interval);
        });
        
        // Suppression des notifications
        this.clearAllNotifications();
        
        console.log('🧹 Dashboard nettoyé');
    }
}

// Styles CSS additionnels injectés dynamiquement
const additionalStyles = `
    @keyframes shine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .dashboard-notification {
        font-family: inherit;
    }
    
    .notification-content {
        user-select: none;
    }
    
    .notification-icon {
        font-size: 1.2em;
    }
    
    .notification-message {
        flex: 1;
        font-weight: 500;
    }
    
    .notification-close {
        background: none;
        border: none;
        font-size: 1.5em;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    
    .notification-close:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
    }
`;

// Injection des styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// Initialisation automatique
const kayakDashboard = new KayakDashboardManager();

// Export pour utilisation externe si nécessaire
window.KayakDashboard = kayakDashboard;