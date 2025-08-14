/**
 * JAVASCRIPT PRINCIPAL - Kayak Trip
 * Fonctionnalités communes à toutes les pages
 */

// Configuration globale
const KayakTrip = {
    config: {
        apiUrl: '/api',
        assetsPath: '/assets',
        csrfToken: null,
        currentUser: null
    },
    
    // Initialisation
    init() {
        this.setupEventListeners();
        this.initFlashMessages();
        this.initFormValidation();
        this.initLazyLoading();
        this.initSmoothScroll();
        this.loadCSRFToken();
    },
    
    // Configuration des écouteurs d'événements
    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Kayak Trip - Application initialisée');
        });
        
        // Gestion des erreurs JavaScript
        window.addEventListener('error', (e) => {
            console.error('Erreur JavaScript:', e.error);
            this.logError(e.error.message, e.filename, e.lineno);
        });
        
        // Gestion des erreurs de promesses non catchées
        window.addEventListener('unhandledrejection', (e) => {
            console.error('Promise rejetée:', e.reason);
        });
    },
    
    // Gestion des messages flash
    initFlashMessages() {
        const flashMessages = document.querySelectorAll('.flash-message');
        
        flashMessages.forEach(message => {
            // Auto-dismiss après 5 secondes
            setTimeout(() => {
                this.dismissFlashMessage(message);
            }, 5000);
            
            // Bouton de fermeture
            const closeBtn = message.querySelector('.flash-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    this.dismissFlashMessage(message);
                });
            }
        });
    },
    
    // Fermer un message flash
    dismissFlashMessage(element) {
        element.style.transform = 'translateX(100%)';
        element.style.opacity = '0';
        
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, 300);
    },
    
    // Afficher un nouveau message flash
    showMessage(message, type = 'info') {
        const container = document.querySelector('.flash-messages') || this.createFlashContainer();
        
        const messageElement = document.createElement('div');
        messageElement.className = `flash-message flash-${type}`;
        messageElement.innerHTML = `
            ${this.escapeHtml(message)}
            <button class="flash-close">&times;</button>
        `;
        
        container.appendChild(messageElement);
        
        // Animation d'entrée
        setTimeout(() => {
            messageElement.style.animation = 'slideInRight 0.3s ease forwards';
        }, 10);
        
        // Auto-dismiss
        setTimeout(() => {
            this.dismissFlashMessage(messageElement);
        }, 5000);
        
        // Bouton de fermeture
        const closeBtn = messageElement.querySelector('.flash-close');
        closeBtn.addEventListener('click', () => {
            this.dismissFlashMessage(messageElement);
        });
    },
    
    // Créer le conteneur des messages flash s'il n'existe pas
    createFlashContainer() {
        const container = document.createElement('div');
        container.className = 'flash-messages';
        document.body.appendChild(container);
        return container;
    },
    
    // Validation des formulaires
    initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
            
            // Validation en temps réel
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });
        });
    },
    
    // Valider un formulaire complet
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    // Valider un champ spécifique
    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        
        // Effacer les erreurs précédentes
        this.clearFieldError(field);
        
        // Champ requis
        if (required && !value) {
            this.showFieldError(field, 'Ce champ est requis');
            return false;
        }
        
        if (!value) return true; // Champ optionnel vide
        
        // Validation par type
        switch (type) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.showFieldError(field, 'Adresse email invalide');
                    return false;
                }
                break;
                
            case 'tel':
                if (!this.isValidPhone(value)) {
                    this.showFieldError(field, 'Numéro de téléphone invalide');
                    return false;
                }
                break;
                
            case 'password':
                const minLength = field.getAttribute('minlength') || 8;
                if (value.length < minLength) {
                    this.showFieldError(field, `Le mot de passe doit contenir au moins ${minLength} caractères`);
                    return false;
                }
                break;
                
            case 'date':
                if (!this.isValidDate(value)) {
                    this.showFieldError(field, 'Date invalide');
                    return false;
                }
                break;
        }
        
        // Validation personnalisée
        const pattern = field.getAttribute('pattern');
        if (pattern && !new RegExp(pattern).test(value)) {
            const errorMsg = field.getAttribute('data-error') || 'Format invalide';
            this.showFieldError(field, errorMsg);
            return false;
        }
        
        return true;
    },
    
    // Afficher une erreur de champ
    showFieldError(field, message) {
        field.classList.add('error');
        
        // Supprimer l'ancienne erreur
        const existingError = field.parentNode.querySelector('.form-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Ajouter la nouvelle erreur
        const errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);
    },
    
    // Effacer l'erreur d'un champ
    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }
    },
    
    // Lazy loading des images
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },
    
    // Scroll fluide
    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    },
    
    // Chargement du token CSRF
    async loadCSRFToken() {
        try {
            const response = await this.fetch('/api/csrf-token.php');
            if (response.success) {
                this.config.csrfToken = response.token;
            }
        } catch (error) {
            console.warn('Impossible de charger le token CSRF:', error);
        }
    },
    
    // Méthode fetch améliorée
    async fetch(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        // Ajouter le token CSRF aux requêtes POST/PUT/DELETE
        if (this.config.csrfToken && ['POST', 'PUT', 'DELETE'].includes(options.method)) {
            defaultOptions.headers['X-CSRF-Token'] = this.config.csrfToken;
        }
        
        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: { ...defaultOptions.headers, ...options.headers }
        };
        
        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            
            return await response.text();
        } catch (error) {
            console.error('Erreur de requête:', error);
            throw error;
        }
    },
    
    // Utilitaires de validation
    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },
    
    isValidPhone(phone) {
        // Format français
        const regex = /^(?:\+33|0)[1-9](?:[0-9]{8})$/;
        return regex.test(phone.replace(/\s+/g, ''));
    },
    
    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    },
    
    // Échapper le HTML
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    // Formater un prix
    formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    },
    
    // Formater une date
    formatDate(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        
        return new Intl.DateTimeFormat('fr-FR', { ...defaultOptions, ...options })
            .format(new Date(date));
    },
    
    // Logging d'erreurs
    async logError(message, file = '', line = '') {
        try {
            await this.fetch('/api/log-error.php', {
                method: 'POST',
                body: JSON.stringify({
                    message,
                    file,
                    line,
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    timestamp: new Date().toISOString()
                })
            });
        } catch (error) {
            console.error('Impossible de logger l\'erreur:', error);
        }
    },
    
    // Gérer les états de chargement
    setLoading(element, loading = true) {
        if (loading) {
            element.disabled = true;
            element.classList.add('loading');
            
            // Sauvegarder le texte original
            if (!element.dataset.originalText) {
                element.dataset.originalText = element.textContent;
            }
            
            element.innerHTML = '<span class="spinner"></span> Chargement...';
        } else {
            element.disabled = false;
            element.classList.remove('loading');
            element.textContent = element.dataset.originalText || element.textContent;
        }
    },
    
    // Debounce pour optimiser les événements
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
    },
    
    // Gérer le localStorage avec gestion d'erreurs
    storage: {
        set(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (error) {
                console.warn('Erreur localStorage:', error);
                return false;
            }
        },
        
        get(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (error) {
                console.warn('Erreur localStorage:', error);
                return defaultValue;
            }
        },
        
        remove(key) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (error) {
                console.warn('Erreur localStorage:', error);
                return false;
            }
        }
    },
    
    // Détection des capacités du navigateur
    capabilities: {
        get touchSupport() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        },
        
        get isTablet() {
            return this.touchSupport && window.innerWidth >= 768 && window.innerWidth <= 1024;
        },
        
        get isMobile() {
            return this.touchSupport && window.innerWidth < 768;
        },
        
        get supportsWebP() {
            const canvas = document.createElement('canvas');
            return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        },
        
        get supportsServiceWorker() {
            return 'serviceWorker' in navigator;
        }
    }
};

// Modules spécialisés
KayakTrip.Search = {
    init() {
        this.setupSearchForm();
        this.setupAutoComplete();
    },
    
    setupSearchForm() {
        const searchForms = document.querySelectorAll('.search-form');
        
        searchForms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const searchTerm = formData.get('q');
                
                if (searchTerm && searchTerm.trim().length >= 2) {
                    await this.performSearch(searchTerm);
                }
            });
        });
    },
    
    setupAutoComplete() {
        const searchInputs = document.querySelectorAll('input[data-autocomplete]');
        
        searchInputs.forEach(input => {
            const debouncedSearch = KayakTrip.debounce(async (value) => {
                if (value.length >= 2) {
                    await this.showSuggestions(input, value);
                } else {
                    this.hideSuggestions(input);
                }
            }, 300);
            
            input.addEventListener('input', (e) => {
                debouncedSearch(e.target.value);
            });
        });
    },
    
    async performSearch(term) {
        try {
            const response = await KayakTrip.fetch('/api/search.php', {
                method: 'POST',
                body: JSON.stringify({ q: term })
            });
            
            if (response.success) {
                this.displayResults(response.results);
            } else {
                KayakTrip.showMessage('Erreur lors de la recherche', 'error');
            }
        } catch (error) {
            KayakTrip.showMessage('Erreur de connexion', 'error');
        }
    },
    
    async showSuggestions(input, term) {
        try {
            const type = input.dataset.autocomplete;
            const response = await KayakTrip.fetch(`/api/autocomplete.php?type=${type}&q=${encodeURIComponent(term)}`);
            
            if (response.success && response.suggestions.length > 0) {
                this.renderSuggestions(input, response.suggestions);
            } else {
                this.hideSuggestions(input);
            }
        } catch (error) {
            console.warn('Erreur autocomplete:', error);
        }
    },
    
    renderSuggestions(input, suggestions) {
        // Supprimer les anciennes suggestions
        this.hideSuggestions(input);
        
        const container = document.createElement('div');
        container.className = 'autocomplete-suggestions';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = suggestion.label;
            item.addEventListener('click', () => {
                input.value = suggestion.value;
                this.hideSuggestions(input);
                input.dispatchEvent(new Event('input'));
            });
            container.appendChild(item);
        });
        
        input.parentNode.appendChild(container);
    },
    
    hideSuggestions(input) {
        const suggestions = input.parentNode.querySelector('.autocomplete-suggestions');
        if (suggestions) {
            suggestions.remove();
        }
    },
    
    displayResults(results) {
        const container = document.getElementById('search-results');
        if (!container) return;
        
        if (results.length === 0) {
            container.innerHTML = '<p class="no-results">Aucun résultat trouvé.</p>';
            return;
        }
        
        const html = results.map(result => `
            <div class="search-result">
                <h3><a href="${result.url}">${KayakTrip.escapeHtml(result.title)}</a></h3>
                <p>${KayakTrip.escapeHtml(result.description)}</p>
                <small>${result.type}</small>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }
};

// Module de messagerie temps réel
KayakTrip.Messages = {
    init() {
        if (document.querySelector('.chat-container')) {
            this.setupChat();
            this.startPolling();
        }
    },
    
    setupChat() {
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        
        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = chatInput.value.trim();
                
                if (message) {
                    await this.sendMessage(message);
                    chatInput.value = '';
                }
            });
        }
    },
    
    async sendMessage(message) {
        try {
            const response = await KayakTrip.fetch('/api/messages.php', {
                method: 'POST',
                body: JSON.stringify({ message })
            });
            
            if (response.success) {
                this.addMessageToChat(message, 'user');
            } else {
                KayakTrip.showMessage('Erreur lors de l\'envoi du message', 'error');
            }
        } catch (error) {
            KayakTrip.showMessage('Erreur de connexion', 'error');
        }
    },
    
    startPolling() {
        setInterval(async () => {
            await this.checkNewMessages();
        }, 5000); // Vérifier toutes les 5 secondes
    },
    
    async checkNewMessages() {
        try {
            const lastMessageId = this.getLastMessageId();
            const response = await KayakTrip.fetch(`/api/messages.php?since=${lastMessageId}`);
            
            if (response.success && response.messages.length > 0) {
                response.messages.forEach(msg => {
                    this.addMessageToChat(msg.contenu, msg.type, msg.date_envoi);
                });
            }
        } catch (error) {
            console.warn('Erreur polling messages:', error);
        }
    },
    
    addMessageToChat(message, type, timestamp = null) {
        const chatContainer = document.querySelector('.chat-messages');
        if (!chatContainer) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = `chat-message chat-message-${type}`;
        
        const time = timestamp ? new Date(timestamp) : new Date();
        messageElement.innerHTML = `
            <div class="message-content">${KayakTrip.escapeHtml(message)}</div>
            <div class="message-time">${time.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</div>
        `;
        
        chatContainer.appendChild(messageElement);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    },
    
    getLastMessageId() {
        const messages = document.querySelectorAll('.chat-message[data-id]');
        if (messages.length > 0) {
            return messages[messages.length - 1].dataset.id;
        }
        return 0;
    }
};

// Initialisation de l'application
KayakTrip.init();
KayakTrip.Search.init();
KayakTrip.Messages.init();

// Export global pour compatibilité
window.KayakTrip = KayakTrip;