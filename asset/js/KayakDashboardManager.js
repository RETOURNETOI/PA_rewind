// Dashboard Kayak Trip Management System - JavaScript Enhanced
// Version externe complète avec gestion d'heure française et fonctionnalités avancées

// Dashboard Kayak Trip Management System

class KayakDashboardManager {
    constructor() {
        this.config = {
            timeUpdateInterval: 1000,
            timezone: 'Europe/Paris'
        };
        this.state = { timeInterval: null };
        this.init();
    }

    init() {
        this.startTimeUpdates();
        console.log('Dashboard Kayak initialisé');
    }

    startTimeUpdates() {
        this.updateTime();
        if (this.state.timeInterval) clearInterval(this.state.timeInterval);
        this.state.timeInterval = setInterval(() => this.updateTime(), this.config.timeUpdateInterval);
    }

    updateTime() {
        const now = new Date();

        const fullTimeString = now.toLocaleString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            timeZone: this.config.timezone
        });

        const shortTimeString = now.toLocaleTimeString('fr-FR', {
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            timeZone: this.config.timezone
        });

        const headerTime = document.getElementById('header-time');
        const systemTime = document.getElementById('system-time');
        const footerTime = document.getElementById('footer-time');

        if (headerTime) headerTime.textContent = fullTimeString;
        if (systemTime) systemTime.textContent = shortTimeString;
        if (footerTime) footerTime.textContent = `Mis à jour: ${shortTimeString}`;
    }
}

// Init auto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.KayakDashboard = new KayakDashboardManager();
    });
} else {
    window.KayakDashboard = new KayakDashboardManager();
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
    
    .live-time {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    @media (max-width: 768px) {
        .dashboard-notification {
            right: 10px !important;
            left: 10px !important;
            min-width: auto !important;
            max-width: none !important;
        }
    }
    
    .stat-card:focus,
    .management-card:focus,
    .chart-card:focus,
    .service-card:focus,
    .feature-card:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }
`;

// Injection des styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// ===== INITIALISATION =====
// Variable globale pour exposer les fonctions utilitaires
window.showComingSoon = function() {
    if (window.KayakDashboard) {
        window.KayakDashboard.showComingSoon();
    } else {
        alert('Cette fonctionnalité sera bientôt disponible!');
    }
};

// Initialisation automatique
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.KayakDashboard = new KayakDashboardManager();
        window.KayakDashboard.handleDOMReady();
    });
} else {
    window.KayakDashboard = new KayakDashboardManager();
    window.KayakDashboard.handleDOMReady();
}

// Export pour utilisation externe
if (typeof module !== 'undefined' && module.exports) {
    module.exports = KayakDashboardManager;
}