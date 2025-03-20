import config from '../utils/config.js';
import helpers from '../utils/helpers.js';
import favorites from './favorites.js';

const movieDetailModule = {
    async openMovieDetail(movieId) {
        helpers.showSpinner();
        try {
            const res = await fetch(`${config.BASE_URL}/movie/${movieId}?api_key=${config.API_KEY}&language=es`);
            const movie = await res.json();
            this.renderMovieDetail(movie);
        } catch (error) {
            console.error("Error abriendo detalle:", error);
            helpers.showToast("Error al cargar los detalles de la película", "error");
        } finally {
            helpers.hideSpinner();
        }
    },

    renderMovieDetail(movie) {
        const detailContainer = document.getElementById("movieDetailContent");
        if (!detailContainer) return;

        const posterPath = movie.poster_path
            ? `${config.IMG_URL}${config.POSTER_SIZES.MEDIUM}${movie.poster_path}`
            : '/img/no-poster.jpg';

        detailContainer.innerHTML = `
            <h2>${movie.title}</h2>
            <img src="${posterPath}" alt="${movie.title}" style="width:200px; float:left; margin-right:20px;">
            <p><strong>Fecha de lanzamiento:</strong> ${movie.release_date}</p>
            <p><strong>Rating:</strong> ${movie.vote_average} (${helpers.renderStars(movie.vote_average)})</p>
            <p>${movie.overview}</p>
            <button class="action-btn" onclick="favorites.toggleFavorite(event, ${movie.id})">Favorito</button>
            <a href="/infoPelicula/${movie.id}" class="action-btn">Ver más detalles</a>
        `;
        document.getElementById("movieDetailModal").style.display = "block";

        // Configurar el botón de cierre
        this.setupCloseButton();
    },

    setupCloseButton() {
        const closeBtn = document.getElementById("closeMovieDetail");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                document.getElementById("movieDetailModal").style.display = "none";
            });
        }
    }
};

// Exportar para uso global
window.openMovieDetail = (movieId) => movieDetailModule.openMovieDetail(movieId);

export default movieDetailModule;
