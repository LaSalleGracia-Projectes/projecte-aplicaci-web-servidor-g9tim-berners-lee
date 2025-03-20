import config from '../utils/config.js';
import helpers from '../utils/helpers.js';
import movieDetail from './movieDetail.js';
import favorites from './favorites.js';

const trendingModule = {
    async init() {
        await this.cargarTendencias();
        this.setupNavigation();
    },

    async cargarTendencias() {
        helpers.showSpinner();
        try {
            const res = await fetch(`${config.BASE_URL}/trending/movie/week?api_key=${config.API_KEY}&language=es`);
            const data = await res.json();
            this.renderTrending(data.results);
        } catch (error) {
            console.error("Error cargando tendencias:", error);
        } finally {
            helpers.hideSpinner();
        }
    },

    renderTrending(movies) {
        const trendingContainer = document.getElementById("trendingContainer");
        if (!trendingContainer) return;

        trendingContainer.innerHTML = "";

        // Agregar botones de filtrado
        const filterContainer = document.querySelector('.filter-platform');
        if (filterContainer) {
            filterContainer.innerHTML = `
                <div class="platform-buttons">
                    <button class="platform-btn active" data-platform="all">
                        <i class="fas fa-globe"></i> Todas
                    </button>
                    <button class="platform-btn" data-platform="netflix">
                        <i class="fab fa-netflix"></i> Netflix
                    </button>
                    <button class="platform-btn" data-platform="prime">
                        <i class="fab fa-amazon"></i> Prime
                    </button>
                    <button class="platform-btn" data-platform="disney">
                        <i class="fas fa-star"></i> Disney+
                    </button>
                    <button class="platform-btn" data-platform="hbo">
                        <i class="fas fa-play"></i> HBO
                    </button>
                </div>
            `;

            // Eventos para botones de filtrado
            const platformButtons = document.querySelectorAll('.platform-btn');
            platformButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    platformButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.filterMoviesByPlatform(btn.dataset.platform);
                });
            });
        }

        // Renderizar tarjetas de películas
        movies.forEach((movie) => {
            const card = document.createElement("div");
            card.classList.add("movie-card");

            const platforms = helpers.assignRandomPlatforms();
            card.dataset.platforms = platforms.join(',');

            const isFavorite = helpers.checkIfFavorite(movie.id);

            card.innerHTML = `
                <div class="card-image">
                    <img src="${config.IMG_URL}${config.POSTER_SIZES.MEDIUM}${movie.poster_path}" alt="${movie.title}">
                </div>
                <div class="card-info">
                    <h3>${movie.title}</h3>
                    <div class="card-meta">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>${movie.vote_average.toFixed(1)}</span>
                        </div>
                        <div class="platforms">
                            ${platforms.map(p => helpers.getPlatformIcon(p)).join('')}
                        </div>
                        <button class="favorite-btn" data-movie-id="${movie.id}">
                            ${isFavorite ? '❤️' : '🤍'}
                        </button>
                    </div>
                </div>
            `;

            this.setupCardInteractions(card, movie);
            trendingContainer.appendChild(card);
        });
    },

    filterMoviesByPlatform(platform) {
        const movies = document.querySelectorAll('.movie-card');
        let visibleCount = 0;

        movies.forEach(movie => {
            const platforms = movie.dataset.platforms.split(',');
            if (platform === 'all' || platforms.includes(platform)) {
                movie.classList.remove('filtered-out');
                visibleCount++;
            } else {
                movie.classList.add('filtered-out');
            }
        });

        // Mostrar mensaje si no hay resultados
        const noResults = document.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!noResults) {
                const message = document.createElement('div');
                message.className = 'no-results';
                message.innerHTML = `
                    <i class="fas fa-film"></i>
                    <p>No se encontraron películas en esta plataforma</p>
                `;
                document.getElementById('trendingContainer').appendChild(message);
            }
        } else if (noResults) {
            noResults.remove();
        }
    },

    setupCardInteractions(card, movie) {
        card.addEventListener("click", (e) => {
            if (!e.target.closest('.favorite-btn')) {
                // Modificación principal: Redirigir a la página de información detallada
                window.location.href = `/infoPelicula/${movie.id}`;
            }
        });

        const favBtn = card.querySelector('.favorite-btn');
        if (favBtn) {
            favBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                favorites.toggleFavorite(e, movie.id);
                favBtn.innerHTML = helpers.checkIfFavorite(movie.id) ? '❤️' : '🤍';
            });
        }
    },

    setupNavigation() {
        const trendingPrev = document.getElementById("trendingPrev");
        const trendingNext = document.getElementById("trendingNext");
        const trendingContainer = document.getElementById("trendingContainer");

        if (trendingPrev && trendingContainer) {
            trendingPrev.addEventListener("click", () => {
                trendingContainer.scrollBy({
                    left: -300,
                    behavior: "smooth",
                });
            });
        }

        if (trendingNext && trendingContainer) {
            trendingNext.addEventListener("click", () => {
                trendingContainer.scrollBy({
                    left: 300,
                    behavior: "smooth",
                });
            });
        }
    }
};

export default trendingModule;
