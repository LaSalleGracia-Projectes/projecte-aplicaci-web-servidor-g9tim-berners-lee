import config from './config.js';

/**
 * Utilidades y funciones helper para la aplicación
 */
const helpers = {
    /**
     * Muestra el spinner de carga
     */
    showSpinner() {
        document.getElementById("loadingSpinner")?.classList.remove("hidden");
    },

    /**
     * Oculta el spinner de carga
     */
    hideSpinner() {
        document.getElementById("loadingSpinner")?.classList.add("hidden");
    },

    /**
     * Muestra un toast de notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de toast (info, success, error, warning)
     */
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }, 100);
    },

    /**
     * Renderiza estrellas basadas en la puntuación
     * @param {number} vote - Puntuación (de 0 a 10)
     * @returns {string} HTML con las estrellas
     */
    renderStars(vote) {
        const stars = Math.round(vote / 2);
        let html = "";
        for (let i = 1; i <= 5; i++) {
            html += i <= stars ? "★" : "☆";
        }
        return html;
    },

    /**
     * Obtiene el icono HTML para una plataforma
     * @param {string} platform - Nombre de la plataforma
     * @returns {string} HTML con el icono
     */
    getPlatformIcon(platform) {
        const icons = {
            netflix: '<i class="fab fa-netflix" style="color: #E50914;"></i>',
            prime: '<i class="fab fa-amazon" style="color: #00A8E1;"></i>',
            disney: '<i class="fas fa-star" style="color: #113CCF;"></i>',
            hbo: '<i class="fas fa-play" style="color: #8A2BE2;"></i>'
        };
        return icons[platform] || '';
    },

    /**
     * Asigna plataformas aleatorias a una película
     * @returns {Array} Lista de plataformas asignadas
     */
    assignRandomPlatforms() {
        const platforms = ['netflix', 'prime', 'disney', 'hbo'];
        const numPlatforms = Math.floor(Math.random() * 3) + 1;
        const assigned = new Set();

        while (assigned.size < numPlatforms) {
            assigned.add(platforms[Math.floor(Math.random() * platforms.length)]);
        }
        return Array.from(assigned);
    },

    /**
     * Verifica si una película está en favoritos
     * @param {number} movieId - ID de la película
     * @returns {boolean} True si está en favoritos
     */
    checkIfFavorite(movieId) {
        const favorites = JSON.parse(localStorage.getItem(config.FAVORITES_KEY)) || [];
        return favorites.includes(Number(movieId));
    },

    /**
     * Formatea una fecha al formato localizado
     * @param {string} dateString - Fecha en formato ISO
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
        if (!dateString) return 'Fecha desconocida';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
};

export default helpers;
