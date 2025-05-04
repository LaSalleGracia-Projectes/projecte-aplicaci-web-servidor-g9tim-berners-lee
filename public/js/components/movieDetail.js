import config from '../utils/config.js';
import helpers from '../utils/helpers.js';
import favorites from './favorites.js';

const movieDetailModule = {
    // Método principal para abrir el detalle de película - versión simplificada usando modal estático
    openMovieDetail(movieId) {
        // Usar el modal estático predefinido en el HTML
        const movieDetailModal = document.getElementById('movieDetailModalStatic');
        const detailContainer = document.getElementById('movieDetailContentStatic');

        if (!movieDetailModal || !detailContainer) {
            console.error('No se encontraron los elementos del modal');
            return;
        }

        // Mostrar el modal
        movieDetailModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Mostrar indicador de carga
        detailContainer.innerHTML = `
            <div class="loading-indicator" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: #ffffff;">
                <div class="spinner" style="width: 40px; height: 40px; border: 3px solid rgba(255, 255, 255, 0.1); border-top: 3px solid #14ff14; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
                <p>Cargando detalles...</p>
            </div>
        `;

        // Configurar botón de cierre
        const closeBtn = document.getElementById('closeMovieDetailStatic');
        if (closeBtn) {
            // Eliminar manejadores existentes
            const newCloseBtn = closeBtn.cloneNode(true);
            closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);

            // Añadir nuevo manejador
            newCloseBtn.addEventListener('click', () => {
                movieDetailModal.style.display = 'none';
                document.body.style.overflow = '';
            });
        }

        // Añadir manejador ESC
        const handleEsc = (e) => {
            if (e.key === 'Escape') {
                movieDetailModal.style.display = 'none';
                document.body.style.overflow = '';
                document.removeEventListener('keydown', handleEsc);
            }
        };
        document.addEventListener('keydown', handleEsc);

        // Cargar datos de la película
        this.fetchMovieDetailsSimple(movieId, detailContainer);
    },

    // Versión simplificada para cargar datos
    async fetchMovieDetailsSimple(movieId, container) {
        try {
            // Obtener datos de la película
            const response = await fetch(`${config.BASE_URL}/movie/${movieId}?api_key=${config.API_KEY}&language=es`);

            if (!response.ok) {
                throw new Error('Error al cargar los detalles');
            }

            const movie = await response.json();

            // Comprobar si el contenedor sigue existiendo
            if (!container || !document.body.contains(container)) {
                return; // El modal ya se cerró
            }

            // Renderizar contenido
            const posterPath = movie.poster_path
                ? `${config.IMG_URL}${config.POSTER_SIZES.MEDIUM}${movie.poster_path}`
                : '/img/no-poster.jpg';

            container.innerHTML = `
                <h2>${movie.title}</h2>
                <div class="movie-main-content" style="display: flex; gap: 20px;">
                    <img src="${posterPath}" alt="${movie.title}"
                         style="width: 200px; border-radius: 6px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);">
                    <div class="movie-info" style="flex: 1;">
                        <p><strong>Fecha de lanzamiento:</strong> ${movie.release_date || 'No disponible'}</p>
                        <p><strong>Rating:</strong> ${movie.vote_average} (${helpers.renderStars(movie.vote_average)})</p>
                        <p>${movie.overview || 'No hay descripción disponible'}</p>
                        <div class="movie-actions" style="display: flex; gap: 10px; margin-top: 20px;">
                            <button class="action-btn favorite-btn"
                                    style="padding: 8px 16px; background-color: rgba(0, 255, 0, 0.1); color: #14ff14; border: 1px solid #14ff14; border-radius: 4px; cursor: pointer;">
                                <i class="far fa-heart"></i> Favorito
                            </button>
                            <a href="/infoPelicula/${movie.id}" class="action-btn"
                               style="padding: 8px 16px; background-color: rgba(0, 255, 0, 0.1); color: #14ff14; border: 1px solid #14ff14; border-radius: 4px; cursor: pointer; text-decoration: none;">
                                <i class="fas fa-info-circle"></i> Ver más detalles
                            </a>
                        </div>
                    </div>
                </div>
            `;

            // Configurar botón de favoritos
            const favoriteBtn = container.querySelector('.favorite-btn');
            if (favoriteBtn) {
                favoriteBtn.addEventListener('click', (event) => {
                    favorites.toggleFavorite(event, movie.id);
                });
            }

        } catch (error) {
            console.error('Error cargando detalles:', error);

            if (container && document.body.contains(container)) {
                container.innerHTML = `
                    <div class="error-message" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: #ffffff; text-align: center;">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #e53935; margin-bottom: 20px;"></i>
                        <p>No se pudieron cargar los detalles. Inténtalo de nuevo más tarde.</p>
                    </div>
                `;
            }
        }
    }
};

// Exportar para uso global
window.openMovieDetail = (movieId) => movieDetailModule.openMovieDetail(movieId);

export default movieDetailModule;
