import config from '../utils/config.js';
import helpers from '../utils/helpers.js';
import favorites from './favorites.js';

const randomizerModule = {
    init() {
        this.setupForm();
        this.populateGenres();
        this.populateYears();
    },

    setupForm() {
        const form = document.querySelector('.cine-randomizer form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.generarRecomendacion();
            });
        }
    },

    async generarRecomendacion() {
        const tipo = document.getElementById("tipoContenido")?.value || 'movie';
        const genero = document.getElementById("genero")?.value || '';
        const duracion = document.getElementById("duracion")?.value || '';
        const anio = document.getElementById("anio")?.value || '';
        const plataforma = document.getElementById("plataforma")?.value || '';

        helpers.showSpinner();

        try {
            let url = `${config.BASE_URL}/discover/${tipo}?api_key=${config.API_KEY}&language=es&sort_by=popularity.desc`;

            if (genero) url += `&with_genres=${genero}`;
            if (anio) url += `&primary_release_year=${anio}`;
            if (plataforma) url += `&with_watch_providers=${plataforma}&watch_region=ES`;

            const res = await fetch(url);
            const data = await res.json();

            if (!data.results || data.results.length === 0) {
                helpers.showToast('No se encontraron películas con esos criterios', 'info');
                helpers.hideSpinner();
                return;
            }

            let filteredResults = data.results;

            if (duracion) {
                const detailsPromises = filteredResults.map(movie =>
                    fetch(`${config.BASE_URL}/${tipo}/${movie.id}?api_key=${config.API_KEY}&language=es`).then(res => res.json())
                );

                const moviesWithDetails = await Promise.all(detailsPromises);

                filteredResults = moviesWithDetails.filter(movie => {
                    if (duracion === 'short') return movie.runtime < 90;
                    if (duracion === 'long') return movie.runtime >= 90;
                    return true;
                });
            }

            if (filteredResults.length === 0) {
                helpers.showToast('No se encontraron películas con esos criterios', 'info');
                helpers.hideSpinner();
                return;
            }

            const randomMovie = filteredResults[Math.floor(Math.random() * filteredResults.length)];

            const [details, credits, providers] = await Promise.all([
                fetch(`${config.BASE_URL}/${tipo}/${randomMovie.id}?api_key=${config.API_KEY}&language=es`).then(r => r.json()),
                fetch(`${config.BASE_URL}/${tipo}/${randomMovie.id}/credits?api_key=${config.API_KEY}`).then(r => r.json()),
                fetch(`${config.BASE_URL}/${tipo}/${randomMovie.id}/watch/providers?api_key=${config.API_KEY}`).then(r => r.json())
            ]);

            this.renderRandomMovie({ ...randomMovie, ...details, credits, providers });
        } catch (error) {
            console.error("Error generando recomendación:", error);
            helpers.showToast('Error al generar recomendación', 'error');
        } finally {
            helpers.hideSpinner();
        }
    },

    renderRandomMovie(movie) {
        const container = document.getElementById("randomContainer");
        if (!container) return;

        const director = movie.credits?.crew?.find(person => person.job === "Director");
        const cast = movie.credits?.cast?.slice(0, 3) || [];

        const posterPath = movie.poster_path
            ? `${config.IMG_URL}${config.POSTER_SIZES.MEDIUM}${movie.poster_path}`
            : '/img/no-poster.jpg';

        // Hacer toda la película clickeable para ir a la página de detalles
        container.innerHTML = `
            <div class="random-movie" onclick="window.location.href='/infoPelicula/${movie.id}'" style="cursor: pointer;">
                <img src="${posterPath}" alt="${movie.title}">
                <div class="random-movie-info">
                    <h3>${movie.title}</h3>
                    <div class="movie-meta">
                        <span><i class="fas fa-star"></i> ${movie.vote_average.toFixed(1)}</span>
                        <span><i class="fas fa-calendar"></i> ${movie.release_date}</span>
                        ${movie.runtime ? `<span><i class="fas fa-clock"></i> ${movie.runtime} min</span>` : ''}
                    </div>
                    <p class="movie-description">${movie.overview}</p>
                    ${director ? `<p><i class="fas fa-video"></i> Director: ${director.name}</p>` : ''}
                    <p><i class="fas fa-users"></i> Reparto: ${cast.map(actor => actor.name).join(', ')}</p>
                    <div class="movie-actions">
                        <button class="movie-btn primary" onclick="event.stopPropagation(); window.open('https://www.youtube.com/results?search_query=${encodeURIComponent(movie.title + ' trailer')}', '_blank')">
                            <i class="fab fa-youtube"></i> Ver Trailer
                        </button>
                        <button class="movie-btn secondary" onclick="event.stopPropagation(); favorites.toggleFavorite(event, ${movie.id})">
                            <i class="fas fa-heart"></i> Añadir a Favoritos
                        </button>
                        <button class="movie-btn secondary" onclick="event.stopPropagation(); randomizerModule.generarRecomendacion()">
                            <i class="fas fa-dice"></i> Nueva Recomendación
                        </button>
                        <a href="/infoPelicula/${movie.id}" class="movie-btn secondary">
                            <i class="fas fa-info-circle"></i> Más Información
                        </a>
                    </div>
                </div>
            </div>
        `;

        // Configurar los botones para evitar la propagación del clic
        document.querySelectorAll('.movie-btn').forEach(btn => {
            btn.addEventListener('click', e => e.stopPropagation());
        });
    },

    populateGenres() {
        const generoSelect = document.getElementById("genero");
        if (!generoSelect) return;

        fetch(`${config.BASE_URL}/genre/movie/list?api_key=${config.API_KEY}&language=es`)
            .then(res => res.json())
            .then(data => {
                data.genres.forEach((genre) => {
                    const option = document.createElement("option");
                    option.value = genre.id;
                    option.textContent = genre.name;
                    generoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar géneros:", error));
    },

    populateYears() {
        const anioSelect = document.getElementById("anio");
        if (!anioSelect) return;

        const currentYear = new Date().getFullYear();
        for (let year = currentYear; year >= 1980; year--) {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            anioSelect.appendChild(option);
        }
    }
};

// Exportar funciones globales para los manejadores de eventos en línea
window.generarRecomendacion = (e) => randomizerModule.generarRecomendacion(e);
window.toggleFavorite = (e, movieId) => favorites.toggleFavorite(e, movieId);

// Exponer el módulo al objeto window para que sea accesible desde HTML
window.randomizerModule = randomizerModule;

export default randomizerModule;
