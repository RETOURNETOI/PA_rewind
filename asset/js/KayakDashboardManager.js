// Dashboard Kayak Trip Management System - JavaScript Enhanced
// Version corrigée avec gestion d'heure française et fonctionnalités avancées

class KayakDashboardManager {
    constructor() {
        this.config = {
            animationDelay: 500,
            progressAnimationDuration: 1000,
            timeUpdateInterval: 1000,
            notificationDuration: 4000,
            cardHoverScale: 1.02,
            cardHoverTranslate: -8,
            timezone: 'Europe/Paris' // Fuseau horaire français
        };
        
        this.state = {
            isAnimating: false,
            notifications: [],
            realTimeData: {
                users: 0,
                reservations: 0,
                lastUpdate: null
            },
            timeInterval: null
        };
        
        this.elements = {};
        this.intervals = {};
        
        this.init();
    }

    init() {
        this.bindElements();
        this.setupEventListeners();
        this.startRealTimeUpdates();
        console.log('🚣‍♂️ Dashboard Kayak initialisé avec succès');
    }

    bindElements() {
        this.elements = {
            progressBars: document.querySelectorAll('.progress-fill'),
            timeElements: document.querySelectorAll('.current-time, #header-time, #system-time'),
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

        // Nettoyage avant fermeture
        window.addEventListener('beforeunload', () => {
            this.destroy();
        });
    }

    handleDOMReady() {
        this.setupProgressAnimations();
        this.setupCardEffects();
        this.setupActionButtons();
        this.startTimeUpdates();
        this.checkSystemStatus();
    }

    // ===== GESTION DU TEMPS =====
    startTimeUpdates() {
        // Mise à jour immédiate
        this.updateTime();
        
        // Nettoyage de l'ancien interval si existant
        if (this.state.timeInterval) {
            clearInterval(this.state.timeInterval);
        }
        
        // Démarrage du nouvel interval
        this.state.timeInterval = setInterval(() => {
            this.updateTime();
        }, this.config.timeUpdateInterval);
    }

    updateTime() {
        const now = new Date();
        
        // Format pour l'en-tête (complet avec date)
        const fullTimeString = now.toLocaleString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: this.config.timezone
        });

        // Format court pour l'heure système
        const shortTimeString = now.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: this.config.timezone
        });

        // Mise à jour spécifique des éléments
        const headerTime = document.getElementById('header-time');
        const systemTime = document.getElementById('system-time');

        if (headerTime) {
            headerTime.textContent = fullTimeString;
            this.animateTimeUpdate(headerTime);
        }

        if (systemTime) {
            systemTime.textContent = shortTimeString;
            this.animateTimeUpdate(systemTime);
        }

        // Mise à jour des autres éléments de temps
        document.querySelectorAll('.current-time:not(#header-time):not(#system-time)').forEach(el => {
            el.textContent = shortTimeString;
            this.animateTimeUpdate(el);
        });

        // Mise à jour de l'état
        this.state.realTimeData.lastUpdate = now;
        
        // Effet visuel subtil à chaque minute
        this.handleMinuteChange(now);
    }

    animateTimeUpdate(element) {
        // Effet de couleur lors de la mise à jour
        const originalColor = element.style.color || '';
        element.style.color = '#764ba2';
        element.style.transition = 'color 0.2s ease';
        
        setTimeout(() => {
            element.style.color = '#667eea';
        }, 100);
        
        setTimeout(() => {
            element.style.color = originalColor;
        }, 200);
    }

    handleMinuteChange(now) {
        // Stockage de la minute précédente
        if (!this.lastMinute) {
            this.lastMinute = now.getMinutes();
            return;
        }

        // Détection du changement de minute
        if (now.getMinutes() !== this.lastMinute) {
            this.onMinuteChange(now);
            this.lastMinute = now.getMinutes();
        }
    }

    onMinuteChange(now) {
        // Effet visuel subtil lors du changement de minute
        document.querySelectorAll('.live-time').forEach(el => {
            el.style.transform = 'scale(1.1)';
            el.style.transition = 'transform 0.3s ease';
            
            setTimeout(() => {
                el.style.transform = 'scale(1)';
            }, 300);
        });

        // Log optionnel
        console.log('⏰ Minute changée:', now.toLocaleTimeString('fr-FR', {
            timeZone: this.config.timezone
        }));
    }

    // ===== ANIMATIONS DES BARRES DE PROGRESSION =====
    setupProgressAnimations() {
        if (!this.elements.progressBars.length) return;

        this.state.isAnimating = true;
        
        this.elements.progressBars.forEach((bar, index) => {
            const targetWidth = bar.style.width || bar.dataset.width || '0%';
            const targetValue = bar.textContent || '';
            
            // Préparation de l'animation
            bar.style.width = '0%';
            bar.style.transition = 'none';
            
            // Animation échelonnée
            setTimeout(() => {
                bar.style.transition = `width ${this.config.progressAnimationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                bar.style.width = targetWidth;
                
                // Animation du texte si nécessaire
                if (targetValue.includes('%')) {
                    this.animateProgressText(bar, targetValue, this.config.progressAnimationDuration);
                }
                
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

    // ===== EFFETS DES CARTES =====
    setupCardEffects() {
        this.elements.allCards.forEach((card, index) => {
            // Animation d'entrée progressive
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100 + 300);
            
            // Stockage des propriétés originales
            const originalTransform = getComputedStyle(card).transform;
            card.dataset.originalTransform = originalTransform;
            
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
        });
    }

    animateCardEnter(card) {
        if (this.state.isAnimating) return;
        
        const { cardHoverScale, cardHoverTranslate } = this.config;
        
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = `translateY(${cardHoverTranslate}px) scale(${cardHoverScale})`;
        card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
        card.style.zIndex = '10';
    }

    animateCardLeave(card) {
        const originalTransform = card.dataset.originalTransform || 'none';
        
        card.style.transform = originalTransform;
        card.style.boxShadow = '';
        card.style.zIndex = '';
    }

    // ===== GESTION DES BOUTONS =====
    setupActionButtons() {
        this.elements.actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleActionClick(e);
            });
        });

        this.elements.managementButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.animateButtonClick(button);
            });
        });
    }

    handleActionClick(e) {
        const button = e.currentTarget;
        this.animateButtonClick(button);
        
        // Analyse de l'action basée sur le contenu du bouton
        const action = this.extractActionFromButton(button);
        
        switch (action) {
            case 'notifications':
                this.showNotifications();
                e.preventDefault();
                break;
            case 'parametres':
                this.showSettings();
                e.preventDefault();
                break;
        }
    }

    extractActionFromButton(button) {
        const text = button.textContent.toLowerCase();
        if (text.includes('notification')) return 'notifications';
        if (text.includes('paramètre')) return 'parametres';
        return 'unknown';
    }

    animateButtonClick(button) {
        button.style.transform = 'scale(0.95)';
        button.style.transition = 'transform 0.1s ease';
        
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
    }

    // ===== SYSTÈME DE NOTIFICATIONS =====
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
            
            const index = this.state.notifications.indexOf(notification);
            if (index > -1) {
                this.state.notifications.splice(index, 1);
            }
        }, 300);
    }

    clearAllNotifications() {
        this.state.notifications.forEach(notification => {
            this.removeNotification(notification);
        });
    }

    // ===== RACCOURCIS CLAVIER =====
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

        // Ctrl/Cmd + T : Afficher l'heure complète
        if ((e.ctrlKey || e.metaKey) && e.key === 't') {
            e.preventDefault();
            const now = new Date();
            const timeString = now.toLocaleString('fr-FR', {
                timeZone: this.config.timezone,
                timeZoneName: 'long'
            });
            this.showNotification(`Heure locale: ${timeString}`, 'info', 6000);
            return;
        }
    }

    // ===== MISE À JOUR DES DONNÉES =====
    refreshDashboard() {
        this.showNotification('Actualisation du dashboard...', 'info');
        this.updateRealTimeData();
        
        setTimeout(() => {
            this.showNotification('Dashboard actualisé!', 'success');
        }, 1000);
    }

    updateRealTimeData() {
        // Simulation de données en temps réel
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(number => {
            const currentValue = parseInt(number.textContent) || 0;
            
            // Simulation d'une légère variation
            if (Math.random() > 0.8) {
                const variation = Math.random() > 0.5 ? 1 : -1;
                const newValue = Math.max(0, currentValue + variation);
                this.animateNumberChange(number, newValue);
            }
        });

        console.log('Données mises à jour:', new Date().toLocaleTimeString('fr-FR', {
            timeZone: this.config.timezone
        }));
    }

    animateNumberChange(element, newValue) {
        element.style.transform = 'scale(1.1)';
        element.style.color = '#51cf66';
        element.style.transition = 'all 0.2s ease';
        
        setTimeout(() => {
            element.textContent = newValue;
            element.style.transform = 'scale(1)';
            element.style.color = '';
        }, 200);
    }

    // ===== GESTION DES ÉVÉNEMENTS =====
    handleResize() {
        console.log('Redimensionnement détecté');
        // Réajustement des notifications si nécessaire
        this.state.notifications.forEach(notification => {
            if (window.innerWidth < 768) {
                notification.style.right = '10px';
                notification.style.left = '10px';
                notification.style.minWidth = 'auto';
            } else {
                notification.style.right = '20px';
                notification.style.left = 'auto';
                notification.style.minWidth = '300px';
            }
        });
    }

    handleVisibilityChange() {
        if (document.hidden) {
            this.pauseAnimations();
        } else {
            this.resumeAnimations();
            // Mise à jour immédiate de l'heure au retour
            this.updateTime();
        }
    }

    pauseAnimations() {
        if (this.state.timeInterval) {
            clearInterval(this.state.timeInterval);
            this.state.timeInterval = null;
        }
        console.log('Animations mises en pause');
    }

    resumeAnimations() {
        this.startTimeUpdates();
        console.log('Animations reprises');
    }

    handleGlobalError(e) {
        console.error('Erreur JavaScript détectée:', e.error);
        this.showNotification('Une erreur est survenue', 'error');
    }

    // ===== AUTRES MÉTHODES =====
    showSettings() {
        this.showNotification('Paramètres à implémenter', 'info');
    }

    checkSystemStatus() {
        // Vérification du support des fonctionnalités
        const checks = {
            localStorage: this.checkLocalStorage(),
            dateSupport: this.checkDateSupport(),
            timezone: this.checkTimezoneSupport()
        };

        let message = 'Système initialisé';
        let type = 'success';

        if (!checks.timezone) {
            message = 'Support des fuseaux horaires limité';
            type = 'warning';
        }

        this.showNotification(message, type, 2000);
        console.log('Status système:', checks);
    }

    checkLocalStorage() {
        try {
            localStorage.setItem('test', 'test');
            localStorage.removeItem('test');
            return true;
        } catch (e) {
            return false;
        }
    }

    checkDateSupport() {
        try {
            const date = new Date();
            return date.toLocaleString && typeof date.toLocaleString === 'function';
        } catch (e) {
            return false;
        }
    }

    checkTimezoneSupport() {
        try {
            const date = new Date();
            const options = { timeZone: this.config.timezone };
            date.toLocaleString('fr-FR', options);
            return true;
        } catch (e) {
            return false;
        }
    }

    onProgressAnimationComplete(bar, index) {
        console.log(`Animation barre ${index + 1} terminée`);
        
        if (index === this.elements.progressBars.length - 1) {
            this.showNotification('Dashboard chargé!', 'success', 2000);
        }
    }

    startRealTimeUpdates() {
        // Mise à jour périodique des données (toutes les 30 secondes)
        this.intervals.dataUpdate = setInterval(() => {
            if (!document.hidden) {
                this.updateRealTimeData();
            }
        }, 30000);
    }

    // ===== FONCTIONS UTILITAIRES =====
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

    // ===== NETTOYAGE =====
    destroy() {
        // Nettoyage des intervals
        if (this.state.timeInterval) {
            clearInterval(this.state.timeInterval);
        }
        
        Object.values(this.intervals).forEach(interval => {
            if (interval) clearInterval(interval);
        });
        
        // Suppression des notifications
        this.clearAllNotifications();
        
        console.log('Dashboard nettoyé');
    }
}

// ===== STYLES CSS ADDITIONNELS =====
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
        flex-shrink: 0;
    }
    
    .notification-message {
        flex: 1;
        font-weight: 500;
        line-height: 1.4;
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
        flex-shrink: 0;
    }
    
    .notification-close:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
    }
    
    /* Animations pour les éléments de temps */
    .live-time {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    /* Styles responsives pour les notifications */
    @media (max-width: 768px) {
        .dashboard-notification {
            right: 10px !important;
            left: 10px !important;
            min-width: auto !important;
            max-width: none !important;
        }
    }
    
    /* Effets de focus pour l'accessibilité */
    .stat-card:focus,
    .management-card:focus,
    .chart-card:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }
`;

// Injection des styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// ===== INITIALISATION =====
// Attendre que le DOM soit chargé
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.KayakDashboard = new KayakDashboardManager();
    });
} else {
    // Le DOM est déjà chargé
    window.KayakDashboard = new KayakDashboardManager();
}

// Export pour utilisation externe
if (typeof module !== 'undefined' && module.exports) {
    module.exports = KayakDashboardManager;
}