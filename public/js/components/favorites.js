import config from '../utils/config.js';
import helpers from '../utils/helpers.js';

const favoritesModule = {
    init() {
        this.renderFavorites();
    },

    toggleFavorite(e, movieId) {
        e.stopPropagation();
        let favorites = JSON.parse(localStorage.getItem(config.FAVORITES_KEY)) || [];
        const isFavorite = favorites.includes(movieId);

        if (isFavorite) {
            favorites = favorites.filter(id => id !== movieId);
            helpers.showToast('Película eliminada de favoritos', 'info');
        } else {
            favorites.push(movieId);
            helpers.showToast('Película añadida a favoritos', 'success');
        }

        localStorage.setItem(config.FAVORITES_KEY, JSON.stringify(favorites));

        // Actualizar todos los botones de favoritos en la página
        document.querySelectorAll(`[data-movie-id="${movieId}"]`).forEach(btn => {
            btn.innerHTML = favorites.includes(movieId) ? '❤️' : '🤍';
        });

        this.renderFavorites();
    },

    removeFavorite(movieId) {
        let favorites = JSON.parse(localStorage.getItem(config.FAVORITES_KEY)) || [];

        // Filtrar la película eliminada y actualizar localStorage
        favorites = favorites.filter(id => id !== movieId);
        localStorage.setItem(config.FAVORITES_KEY, JSON.stringify(favorites));

        // Buscar todas las tarjetas de la película eliminada y quitarlas del DOM
        document.querySelectorAll(`[data-movie-id="${movieId}"]`).forEach(card => {
            const favoriteCard = card.closest('.favorite-card');
            if (favoriteCard) {
                favoriteCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                favoriteCard.style.opacity = '0';
                favoriteCard.style.transform = 'scale(0.8)';

                setTimeout(() => {
                    favoriteCard.remove(); // Eliminamos del DOM después de la animación

                    // Si ya no hay favoritos, actualizamos el contenedor
                    if (JSON.parse(localStorage.getItem(config.FAVORITES_KEY)).length === 0) {
                        this.renderFavorites(); // Llamamos a renderFavorites() solo si no quedan películas
                    }
                }, 300);
            }
        });

        helpers.showToast('Película eliminada de favoritos', 'info');
    },

    async renderFavorites() {
        const favoritesContainer = document.getElementById("favoritesContainer");
        if (!favoritesContainer) return;

        const favorites = JSON.parse(localStorage.getItem(config.FAVORITES_KEY)) || [];

        if (favorites.length === 0) {
            favoritesContainer.innerHTML = `
                <div class="no-favorites">
                    <i class="fas fa-heart-broken" style="font-size: 2em; margin-bottom: 15px;"></i>
                    <p>No tienes películas favoritas aún.</p>
                    <p style="font-size: 0.9em; margin-top: 10px;">
                        Explora el catálogo y marca tus películas favoritas para verlas aquí
                    </p>
                </div>
            `;
            return;
        }

        favoritesContainer.innerHTML = "";
        helpers.showSpinner();

        try {
            const moviePromises = favorites.map(id =>
                fetch(`${config.BASE_URL}/movie/${id}?api_key=${config.API_KEY}&language=es`).then(res => res.json())
            );
            const movies = await Promise.all(moviePromises);

            movies.forEach(movie => {
                const card = document.createElement("div");
                card.classList.add("favorite-card");

                const posterPath = movie.poster_path
                    ? `${config.IMG_URL}${config.POSTER_SIZES.MEDIUM}${movie.poster_path}`
                    : '/img/no-poster.jpg';

                card.innerHTML = `
                    <div class="favorite-image">
                        <img src="${posterPath}" alt="${movie.title}">
                    </div>
                    <div class="favorite-info">
                        <h3>${movie.title}</h3>
                        <div class="favorite-meta">
                            <div class="favorite-rating">
                                <i class="fas fa-star"></i>
                                <span>${movie.vote_average.toFixed(1)}</span>
                            </div>
                            <button class="remove-favorite" data-movie-id="${movie.id}" title="Eliminar de favoritos">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                this.setupFavoriteCardInteractions(card, movie);
                favoritesContainer.appendChild(card);
            });
        } catch (error) {
            console.error("Error cargando favoritos:", error);
            helpers.showToast("Error al cargar favoritos", "error");
        } finally {
            helpers.hideSpinner();
        }
    },

    setupFavoriteCardInteractions(card, movie) {
        card.addEventListener("click", (e) => {
            if (!e.target.closest('.remove-favorite')) {
                window.location.href = `/infoPelicula/${movie.id}`;
            }
        });

        const removeBtn = card.querySelector('.remove-favorite');
        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeFavorite(movie.id);
            });
        }
    }
};

export default favoritesModule;
