/**
 * Animation Manager for Page Transitions
 * Supports: 'fade', 'slide-up', 'zoom', 'blur'
 */
class PageTransitionManager {
    constructor(defaultStyle = 'fade') {
        // Kunin sa localStorage kung may na-save na preference, kung wala, gamitin ang default
        this.animationStyle = localStorage.getItem('preferredAnimation') || defaultStyle;
        this.isAnimating = false;
        
        // Target element para hindi masira ang Sidebar (Ideally 'main' tag o '.flex-1')
        this.targetElement = document.querySelector('main') || document.querySelector('.flex-1') || document.body;

        this.init();
    }

    init() {
        this.injectCSS();
        
        // Trigger enter animation pagka-load
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => this.playEnter());
        });

        // Back/Forward cache fix (Safari/Firefox)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) this.playEnter();
        });

        // Intercept link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && this.isValidNavigation(link, e)) {
                e.preventDefault();
                this.transitionTo(link.href);
            }
        });

        // Intercept form submissions (optional, para sa login/signup)
        document.addEventListener('submit', (e) => {
            // Pwede mong i-filter kung anong forms lang ang may exit animation
            if (!e.target.hasAttribute('data-no-transition')) {
                this.playExit(300);
            }
        });
    }

    injectCSS() {
        if (document.getElementById('transition-styles')) return;

        const style = document.createElement('style');
        style.id = 'transition-styles';
        style.innerHTML = `
            /* BASE SETUP */
            .trans-target {
                transition: opacity 0.25s ease-in-out, transform 0.25s ease-in-out, filter 0.25s ease-in-out;
            }

            /* --- FADE --- */
            .fade-enter { opacity: 0; }
            .fade-loaded { opacity: 1; }
            .fade-exit { opacity: 0; }

            /* --- SLIDE UP (Safe small movement) --- */
            .slide-up-enter { opacity: 0; transform: translateY(10px); }
            .slide-up-loaded { opacity: 1; transform: translateY(0); }
            .slide-up-exit { opacity: 0; transform: translateY(-5px); }

            /* --- ZOOM --- */
            .zoom-enter { opacity: 0; transform: scale(0.98); }
            .zoom-loaded { opacity: 1; transform: scale(1); }
            .zoom-exit { opacity: 0; transform: scale(0.98); }

            /* --- BLUR --- */
            .blur-enter { opacity: 0; filter: blur(5px); }
            .blur-loaded { opacity: 1; filter: blur(0); }
            .blur-exit { opacity: 0; filter: blur(5px); }

            /* FORM STAGGER ANIMATION */
            @keyframes slideInUp {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
        
        // Set initial classes based on active style
        this.targetElement.classList.add('trans-target', `${this.animationStyle}-enter`);
    }

    playEnter() {
        this.targetElement.classList.remove(`${this.animationStyle}-exit`, `${this.animationStyle}-enter`);
        this.targetElement.classList.add(`${this.animationStyle}-loaded`);
        
        this.animateFormFields();
    }

    playExit(duration = 250) {
        if (this.isAnimating) return;
        
        this.isAnimating = true;
        this.targetElement.classList.remove(`${this.animationStyle}-loaded`);
        this.targetElement.classList.add(`${this.animationStyle}-exit`);

        setTimeout(() => {
            this.isAnimating = false;
        }, duration);
    }

    transitionTo(url, delay = 250) {
        this.playExit(delay);
        setTimeout(() => {
            window.location.href = url;
        }, delay);
    }

    isValidNavigation(link, event) {
        return !(
            link.target === '_blank' ||
            link.getAttribute('href').startsWith('#') ||
            link.href.startsWith('javascript:') ||
            link.hasAttribute('download') ||
            link.hostname !== window.location.hostname ||
            event.ctrlKey || event.metaKey || event.shiftKey
        );
    }

    animateFormFields() {
        // Hanapin ang mga form groups (tulad ng nasa design mo)
        const fields = document.querySelectorAll('form .mb-4, form .mb-5');
        fields.forEach((field, index) => {
            field.style.opacity = '0';
            field.style.animation = `slideInUp 0.4s cubic-bezier(0.25, 1, 0.5, 1) ${0.05 * index}s forwards`;
        });
    }

    setAnimationStyle(style) {
        const validStyles = ['fade', 'slide-up', 'zoom', 'blur'];
        if (validStyles.includes(style)) {
            // Alisin yung luma
            this.targetElement.classList.remove(`${this.animationStyle}-loaded`, `${this.animationStyle}-enter`, `${this.animationStyle}-exit`);
            
            // I-apply yung bago
            this.animationStyle = style;
            localStorage.setItem('preferredAnimation', style);
            
            // I-play ulit para makita yung effect
            this.targetElement.classList.add(`${this.animationStyle}-enter`);
            requestAnimationFrame(() => this.playEnter());
        }
    }
}

// Initialize animation manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Gawing global para pwede mong tawagin sa console or ibang scripts:
    // Pwede mong palitan yung 'fade' to 'slide-up', 'zoom', o 'blur'
    window.animationManager = new PageTransitionManager('fade');
});

// Utility function kung gusto mong magpalit via button click
window.setAnimationStyle = function(style) {
    if (window.animationManager) {
        window.animationManager.setAnimationStyle(style);
    }
};