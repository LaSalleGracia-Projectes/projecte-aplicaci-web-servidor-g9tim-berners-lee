// ========================================
// VARIABLES GLOBALES Y CONFIGURACIÓN
// ========================================
let bannerSlides = [];
let currentSlide = 0;
let sliderInterval;

const FAVORITES_KEY = "critiflixFavorites";

// ========================================
// FUNCIONES DE UTILIDAD
// ========================================
function showSpinner() {
    document.getElementById("loadingSpinner").classList.remove("hidden");
}

function hideSpinner() {
    document.getElementById("loadingSpinner").classList.add("hidden");
}

// ========================================
// SECCIÓN BANNER / SLIDER
// ========================================
async function cargarBanner() {
    showSpinner();
    try {
        const res = await fetch(`${BASE_URL}/movie/upcoming?api_key=${API_KEY}&language=es`);
        const data = await res.json();
        bannerSlides = data.results.slice(0, 5);
        renderBannerSlides();
        startSlider();
    } catch (error) {
        console.error("Error cargando banner:", error);
    } finally {
        hideSpinner();
    }
}

function renderBannerSlides() {
    const slidesContainer = document.getElementById("bannerSlides");
    slidesContainer.innerHTML = "";
    bannerSlides.forEach((movie, index) => {
        const slide = document.createElement("div");
        slide.classList.add("slide");
        slide.style.backgroundImage = `url(${IMG_URL}${movie.backdrop_path})`;
        slide.innerHTML = `
            <div class="slide-content">
                <h2>${movie.title}</h2>
                <p>${movie.overview.substring(0, 150)}...</p>
            </div>
        `;
        slide.style.opacity = index === currentSlide ? "1" : "0";
        slidesContainer.appendChild(slide);
    });
    renderSliderIndicators();
}

function renderSliderIndicators() {
    const indicatorsContainer = document.getElementById("sliderIndicators");
    indicatorsContainer.innerHTML = "";
    bannerSlides.forEach((_, index) => {
        const dot = document.createElement("span");
        dot.classList.add("dot");
        if (index === currentSlide) dot.classList.add("active");
        dot.addEventListener("click", () => {
            currentSlide = index;
            updateSlider();
            resetSliderInterval();
        });
        indicatorsContainer.appendChild(dot);
    });
}

function updateSlider() {
    const slides = document.querySelectorAll(".slide");
    slides.forEach((slide, index) => {
        slide.style.opacity = index === currentSlide ? "1" : "0";
    });
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot, index) => {
        dot.classList.toggle("active", index === currentSlide);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % bannerSlides.length;
    updateSlider();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + bannerSlides.length) % bannerSlides.length;
    updateSlider();
}

function startSlider() {
    sliderInterval = setInterval(nextSlide, 5000);
}

function resetSliderInterval() {
    clearInterval(sliderInterval);
    startSlider();
}

// Eventos para controles del slider
document.getElementById("nextSlide").addEventListener("click", () => {
    nextSlide();
    resetSliderInterval();
});
document.getElementById("prevSlide").addEventListener("click", () => {
    prevSlide();
    resetSliderInterval();
});

// ========================================
// SECCIÓN TENDENCIAS (TRENDING)
// ========================================
async function cargarTendencias() {
    showSpinner();
    try {
        const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}&language=es`);
        const data = await res.json();
        renderTrending(data.results);
    } catch (error) {
        console.error("Error cargando tendencias:", error);
    } finally {
        hideSpinner();
    }
}

function assignRandomPlatforms() {
    const platforms = ['netflix', 'prime', 'disney', 'hbo'];
    const numPlatforms = Math.floor(Math.random() * 3) + 1;
    const assigned = new Set();

    while (assigned.size < numPlatforms) {
        assigned.add(platforms[Math.floor(Math.random() * platforms.length)]);
    }

    return Array.from(assigned);
}

function getPlatformIcon(platform) {
    const icons = {
        netflix: '<i class="fab fa-netflix" style="color: #E50914;"></i>',
        prime: '<i class="fab fa-amazon" style="color: #00A8E1;"></i>',
        disney: '<i class="fas fa-star" style="color: #113CCF;"></i>',
        hbo: '<i class="fas fa-play" style="color: #8A2BE2;"></i>'
    };
    return icons[platform] || '';
}

function renderTrending(movies) {
    const trendingContainer = document.getElementById("trendingContainer");
    trendingContainer.innerHTML = "";

    // Agregar botones de filtrado
    const filterContainer = document.querySelector('.filter-platform');
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
            filterMoviesByPlatform(btn.dataset.platform);
        });
    });

    // Renderizar tarjetas de películas
    movies.forEach((movie) => {
        const card = document.createElement("div");
        card.classList.add("movie-card");

        const platforms = assignRandomPlatforms();
        card.dataset.platforms = platforms.join(',');

        const isFavorite = checkIfFavorite(movie.id);

        card.innerHTML = `
            <div class="card-image">
                <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}">
            </div>
            <div class="card-info">
                <h3>${movie.title}</h3>
                <div class="card-meta">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <span>${movie.vote_average.toFixed(1)}</span>
                    </div>
                    <div class="platforms">
                        ${platforms.map(p => getPlatformIcon(p)).join('')}
                    </div>
                    <button class="favorite-btn" data-movie-id="${movie.id}">
                        ${isFavorite ? '❤️' : '🤍'}
                    </button>
                </div>
            </div>
        `;

        setupCardInteractions(card, movie);
        trendingContainer.appendChild(card);
    });
}

function filterMoviesByPlatform(platform) {
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
}

// ========================================
// SECCIÓN FAVORITOS
// ========================================
function checkIfFavorite(movieId) {
    const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];
    return favorites.includes(movieId);
}

function toggleFavorite(e, movieId) {
    e.stopPropagation();
    let favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];
    const isFavorite = favorites.includes(movieId);

    if (isFavorite) {
        favorites = favorites.filter(id => id !== movieId);
        showToast('Película eliminada de favoritos', 'info');
    } else {
        favorites.push(movieId);
        showToast('Película añadida a favoritos', 'success');
    }

    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));

    // Actualizar todos los botones de favoritos en la página
    document.querySelectorAll(`[data-movie-id="${movieId}"]`).forEach(btn => {
        btn.innerHTML = favorites.includes(movieId) ? '❤️' : '🤍';
    });

    renderFavorites();
}

function removeFavorite(movieId) {
    let favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];

    // Filtrar la película eliminada y actualizar localStorage
    favorites = favorites.filter(id => id !== movieId);
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));

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
                if (JSON.parse(localStorage.getItem(FAVORITES_KEY)).length === 0) {
                    renderFavorites(); // Llamamos a renderFavorites() solo si no quedan películas
                }
            }, 300);
        }
    });

    showToast('Película eliminada de favoritos', 'info');
}


async function renderFavorites() {
    const favoritesContainer = document.getElementById("favoritesContainer");
    const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];

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
    showSpinner();

    try {
        const moviePromises = favorites.map(id =>
            fetch(`${BASE_URL}/movie/${id}?api_key=${API_KEY}&language=es`).then(res => res.json())
        );
        const movies = await Promise.all(moviePromises);

        movies.forEach(movie => {
            const card = document.createElement("div");
            card.classList.add("favorite-card");

            card.innerHTML = `
                <div class="favorite-image">
                    <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}">
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

            setupFavoriteCardInteractions(card, movie);
            favoritesContainer.appendChild(card);
        });
    } catch (error) {
        console.error("Error cargando favoritos:", error);
        showToast("Error al cargar favoritos", "error");
    } finally {
        hideSpinner();
    }
}

// Funciones para la interacción en las tarjetas
function setupCardInteractions(card, movie) {
    card.addEventListener("click", (e) => {
        if (!e.target.closest('.favorite-btn')) {
            openMovieDetail(movie.id);
        }
    });

    const favBtn = card.querySelector('.favorite-btn');
    favBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleFavorite(e, movie.id);
        favBtn.innerHTML = checkIfFavorite(movie.id) ? '❤️' : '🤍';
    });
}

function setupFavoriteCardInteractions(card, movie) {
    card.addEventListener("click", (e) => {
        if (!e.target.closest('.remove-favorite')) {
            openMovieDetail(movie.id);
        }
    });

    const removeBtn = card.querySelector('.remove-favorite');
    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        removeFavorite(movie.id);
    });
}

// ========================================
// SECCIÓN DETALLE DE PELÍCULA (MODAL)
// ========================================
async function openMovieDetail(movieId) {
    showSpinner();
    try {
        const res = await fetch(`${BASE_URL}/movie/${movieId}?api_key=${API_KEY}&language=es`);
        const movie = await res.json();
        const detailContainer = document.getElementById("movieDetailContent");
        detailContainer.innerHTML = `
            <h2>${movie.title}</h2>
            <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}" style="width:200px; float:left; margin-right:20px;">
            <p><strong>Fecha de lanzamiento:</strong> ${movie.release_date}</p>
            <p><strong>Rating:</strong> ${movie.vote_average} (${renderStars(movie.vote_average)})</p>
            <p>${movie.overview}</p>
            <button class="action-btn" onclick="toggleFavorite(event, ${movie.id})">Favorito</button>
            <a href="/infoPelicula/${movie.id}" class="action-btn">Ver más detalles</a>
        `;
        document.getElementById("movieDetailModal").style.display = "block";
    } catch (error) {
        console.error("Error abriendo detalle:", error);
    } finally {
        hideSpinner();
    }
}

function renderStars(vote) {
    const stars = Math.round(vote / 2);
    let html = "";
    for (let i = 1; i <= 5; i++) {
        html += i <= stars ? "★" : "☆";
    }
    return html;
}

document.getElementById("closeMovieDetail").addEventListener("click", () => {
    document.getElementById("movieDetailModal").style.display = "none";
});
// ========================================
// SECCIÓN BÚSQUEDA Y SUGERENCIAS
// ========================================
let searchTimeout;
let currentSuggestionIndex = -1;
let lastQuery = '';
const searchInput = document.getElementById("search");
const suggestionsContainer = document.getElementById("suggestions");

// Manejo de entrada en el campo de búsqueda
searchInput.addEventListener("input", function() {
    const query = this.value.trim();

    // Reiniciar índice de selección
    currentSuggestionIndex = -1;

    // Cancelar búsqueda anterior si está en progreso
    if (searchTimeout) clearTimeout(searchTimeout);

    if (query.length > 2) {
        // Añadir indicador de carga
        suggestionsContainer.innerHTML = '<div class="loading-suggestions">Buscando...</div>';

        // Retraso para evitar muchas peticiones
        searchTimeout = setTimeout(() => {
            // Solo buscar si el query ha cambiado
            if (query !== lastQuery) {
                lastQuery = query;
                buscarSugerencias(query);
            }
        }, 300);
    } else {
        suggestionsContainer.innerHTML = "";
    }
});

// Manejo de navegación con teclado
searchInput.addEventListener("keydown", function(e) {
    const suggestions = document.querySelectorAll(".suggestion-item");

    if (suggestions.length === 0) return;

    if (e.key === "ArrowDown") {
        e.preventDefault();
        currentSuggestionIndex = (currentSuggestionIndex + 1) % suggestions.length;
        updateSuggestionActive(suggestions);
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        currentSuggestionIndex = (currentSuggestionIndex - 1 + suggestions.length) % suggestions.length;
        updateSuggestionActive(suggestions);
    } else if (e.key === "Enter") {
        if (currentSuggestionIndex >= 0) {
            e.preventDefault();
            suggestions[currentSuggestionIndex].click();
        } else if (this.value.trim().length > 2) {
            // Si no hay sugerencia seleccionada pero hay texto válido
            buscarSugerencias(this.value.trim()).then((results) => {
                if (results && results.length > 0) {
                    window.location.href = `/infoPelicula/${results[0].id}`;
                }
            });
        }
    } else if (e.key === "Escape") {
        // Cerrar sugerencias con ESC
        suggestionsContainer.innerHTML = "";
        this.blur();
    }
});

// Limpiar al doble clic en el campo
searchInput.addEventListener("dblclick", function() {
    this.value = "";
    suggestionsContainer.innerHTML = "";
    lastQuery = '';
});

// Cerrar sugerencias al hacer clic fuera
document.addEventListener("click", function(e) {
    if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
        suggestionsContainer.innerHTML = "";
    }
});

// Actualizar el estado activo de las sugerencias
function updateSuggestionActive(suggestions) {
    suggestions.forEach((item, idx) => {
        if (idx === currentSuggestionIndex) {
            item.classList.add("active");
            // Hacer scroll si es necesario
            if (item.offsetTop < suggestionsContainer.scrollTop ||
                item.offsetTop + item.offsetHeight > suggestionsContainer.scrollTop + suggestionsContainer.offsetHeight) {
                item.scrollIntoView({ block: "nearest" });
            }
        } else {
            item.classList.remove("active");
        }
    });
}

// Buscar sugerencias en la API
async function buscarSugerencias(query) {
    try {
        const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}&language=es`);

        if (!res.ok) {
            throw new Error(`Error de API: ${res.status}`);
        }

        const data = await res.json();
        renderSugerencias(data.results);
        return data.results;
    } catch (error) {
        console.error("Error buscando sugerencias:", error);
        suggestionsContainer.innerHTML = '<div class="error-message">Error al buscar. Inténtalo de nuevo.</div>';
        return [];
    }
}

// Renderizar las sugerencias
function renderSugerencias(results) {
    suggestionsContainer.innerHTML = "";

    if (results.length === 0) {
        suggestionsContainer.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
        return;
    }

    // Agregar estilos CSS necesarios dinámicamente si no existen
    if (!document.getElementById('search-suggestion-styles')) {
        const style = document.createElement('style');
        style.id = 'search-suggestion-styles';
        style.textContent = `
            .search-suggestions {
                max-height: 350px;
                overflow-y: auto;
                background: rgba(20, 20, 20, 0.95);
                border: 1px solid var(--verde-neon);
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            }
            .suggestion-item {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .suggestion-item:hover, .suggestion-item.active {
                background: rgba(0, 255, 135, 0.1);
            }
            .suggestion-poster {
                width: 40px;
                height: 60px;
                margin-right: 10px;
                overflow: hidden;
                border-radius: 5px;
                flex-shrink: 0;
            }
            .suggestion-poster img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .suggestion-info {
                flex-grow: 1;
            }
            .suggestion-title {
                font-weight: 600;
                margin-bottom: 3px;
            }
            .suggestion-rating {
                font-size: 0.8em;
                color: #888;
            }
            .suggestion-rating i {
                color: #ffc107;
                margin-right: 3px;
            }
            .ver-todos-resultados {
                padding: 12px 15px;
                background: rgba(0, 255, 135, 0.15);
                text-align: center;
                font-weight: 600;
                cursor: pointer;
                border-top: 1px solid rgba(0, 255, 135, 0.3);
            }
            .ver-todos-resultados:hover {
                background: rgba(0, 255, 135, 0.25);
            }
            .loading-suggestions, .no-results, .error-message {
                padding: 15px;
                text-align: center;
                color: #888;
                font-style: italic;
            }
            .error-message {
                color: #ff6b6b;
            }
        `;
        document.head.appendChild(style);
    }

    results.slice(0, 5).forEach((movie) => {
        const item = document.createElement("div");
        item.classList.add("suggestion-item");

        // Crear estructura para incluir imagen y título
        const posterPath = movie.poster_path
            ? `https://image.tmdb.org/t/p/w92${movie.poster_path}`
            : 'img/no-poster.jpg';

        const year = movie.release_date ? ` (${movie.release_date.substring(0, 4)})` : '';

        item.innerHTML = `
            <div class="suggestion-poster">
                <img src="${posterPath}" alt="${movie.title}" onerror="this.src='img/no-poster.jpg'">
            </div>
            <div class="suggestion-info">
                <div class="suggestion-title">${movie.title}${year}</div>
                <div class="suggestion-rating">
                    <i class="fas fa-star"></i> ${movie.vote_average || 'N/A'}
                </div>
            </div>
        `;

        item.addEventListener("click", () => {
            window.location.href = `/infoPelicula/${movie.id}`;
        });

        suggestionsContainer.appendChild(item);
    });

    // Añadir enlace para ver todos los resultados
    const verTodos = document.createElement("div");
    verTodos.classList.add("ver-todos-resultados");
    verTodos.innerHTML = `<i class="fas fa-search"></i> Ver todos los resultados`;
    verTodos.addEventListener("click", () => {
        window.location.href = `/busqueda?q=${encodeURIComponent(searchInput.value.trim())}`;
    });

    suggestionsContainer.appendChild(verTodos);
}

// ========================================
// SECCIÓN CINE RANDOMIZER (RECOMENDACIÓN)
// ========================================
async function generarRecomendacion(e) {
    if (e) e.preventDefault();

    const tipo = document.getElementById("tipoContenido").value;
    const genero = document.getElementById("genero").value;
    const duracion = document.getElementById("duracion").value;
    const anio = document.getElementById("anio").value;
    const plataforma = document.getElementById("plataforma").value;

    showSpinner();

    try {
        let url = `${BASE_URL}/discover/${tipo}?api_key=${API_KEY}&language=es&sort_by=popularity.desc`;

        if (genero) url += `&with_genres=${genero}`;
        if (anio) url += `&primary_release_year=${anio}`;
        if (plataforma) url += `&with_watch_providers=${plataforma}&watch_region=ES`;

        const res = await fetch(url);
        const data = await res.json();

        if (!data.results || data.results.length === 0) {
            showToast('No se encontraron películas con esos criterios', 'info');
            hideSpinner();
            return;
        }

        let filteredResults = data.results;

        if (duracion) {
            const detailsPromises = filteredResults.map(movie =>
                fetch(`${BASE_URL}/${tipo}/${movie.id}?api_key=${API_KEY}&language=es`).then(res => res.json())
            );

            const moviesWithDetails = await Promise.all(detailsPromises);

            filteredResults = moviesWithDetails.filter(movie => {
                if (duracion === 'short') return movie.runtime < 90;
                if (duracion === 'long') return movie.runtime >= 90;
                return true;
            });
        }

        if (filteredResults.length === 0) {
            showToast('No se encontraron películas con esos criterios', 'info');
            hideSpinner();
            return;
        }

        const randomMovie = filteredResults[Math.floor(Math.random() * filteredResults.length)];

        const [details, credits, providers] = await Promise.all([
            fetch(`${BASE_URL}/${tipo}/${randomMovie.id}?api_key=${API_KEY}&language=es`).then(r => r.json()),
            fetch(`${BASE_URL}/${tipo}/${randomMovie.id}/credits?api_key=${API_KEY}`).then(r => r.json()),
            fetch(`${BASE_URL}/${tipo}/${randomMovie.id}/watch/providers?api_key=${API_KEY}`).then(r => r.json())
        ]);

        renderRandomMovie({ ...randomMovie, ...details, credits, providers });
    } catch (error) {
        console.error("Error generando recomendación:", error);
        showToast('Error al generar recomendación', 'error');
    } finally {
        hideSpinner();
    }
}

function renderRandomMovie(movie) {
    const container = document.getElementById("randomContainer");
    const director = movie.credits.crew.find(person => person.job === "Director");
    const cast = movie.credits.cast.slice(0, 3);

    container.innerHTML = `
        <div class="random-movie">
            <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}">
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
                    <button class="movie-btn primary" onclick="window.open('https://www.youtube.com/results?search_query=${encodeURIComponent(movie.title + ' trailer')}', '_blank')">
                        <i class="fab fa-youtube"></i> Ver Trailer
                    </button>
                    <button class="movie-btn secondary" onclick="toggleFavorite(event, ${movie.id})">
                        <i class="fas fa-heart"></i> Añadir a Favoritos
                    </button>
                    <button class="movie-btn secondary" onclick="generarRecomendacion(event)">
                        <i class="fas fa-dice"></i> Nueva Recomendación
                    </button>
                    <button class="movie-btn secondary" onclick="openMovieDetail(${movie.id})">
                        <i class="fas fa-info-circle"></i> Más Información
                    </button>
                </div>
            </div>
        </div>
    `;
}

function populateGenres() {
    fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}&language=es`)
        .then(res => res.json())
        .then(data => {
            const generoSelect = document.getElementById("genero");
            data.genres.forEach((genre) => {
                const option = document.createElement("option");
                option.value = genre.id;
                option.textContent = genre.name;
                generoSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar géneros:", error));
}

function populateYears() {
    const anioSelect = document.getElementById("anio");
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 1980; year--) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        anioSelect.appendChild(option);
    }
}

// ========================================
// SECCIÓN MODALES (LOGIN / REGISTRO)
// ========================================
function setupModals() {
    const loginLink = document.getElementById("loginLink");
    const registerLink = document.getElementById("registerLink");
    const loginModal = document.getElementById("loginModal");
    const registerModal = document.getElementById("registerModal");
    const closeLogin = document.getElementById("closeLogin");
    const closeRegister = document.getElementById("closeRegister");
    const logoutButton = document.getElementById("logoutButton");
    const movieDetailModal = document.getElementById("movieDetailModal");
    // Asegúrate de que "movieDetailModal" existe en tu HTML
    // y que también tenga la clase .modal en el CSS.

    // Abrir modales
    loginLink.addEventListener("click", (e) => {
        e.preventDefault();
        loginModal.classList.add("show");
    });

    registerLink.addEventListener("click", (e) => {
        e.preventDefault();
        registerModal.classList.add("show");
    });

    // Cerrar modales con el botón [X]
    closeLogin.addEventListener("click", () => {
        loginModal.classList.remove("show");
    });

    closeRegister.addEventListener("click", () => {
        registerModal.classList.remove("show");
    });

    // Cerrar modales al hacer clic fuera del contenido
    window.addEventListener("click", (e) => {
        if (e.target === loginModal) {
            loginModal.classList.remove("show");
        }
        if (e.target === registerModal) {
            registerModal.classList.remove("show");
        }
        if (movieDetailModal && e.target === movieDetailModal) {
            movieDetailModal.classList.remove("show");
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const loginLink = document.getElementById("loginLink");
    const registerLink = document.getElementById("registerLink");
    const logoutButton = document.getElementById("logoutButton");

    // Ocultar enlaces si hay token en localStorage
    if (localStorage.getItem("token")) {
        loginLink.style.display = "none";
        registerLink.style.display = "none";
    }

    // Botón de logout
    logoutButton.addEventListener("click", function () {
        localStorage.removeItem("token");
        window.location.reload();
    });

    // Formulario de registro
    document.getElementById("registerForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita la redirección por defecto

        const name = document.querySelector("#registerModal input[name='name']").value;
        const email = document.querySelector("#registerModal input[name='email']").value;
        const password = document.querySelector("#registerModal input[name='password']").value;
        const password_confirmation = document.querySelector("#registerModal input[name='password_confirmation']").value;

        fetch("/api/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ name, email, password, password_confirmation }),
        })
        .then((response) => response.json())
        .then((data) => {
            console.log("Respuesta del servidor:", data);

            if (data.errors) {
                alert("Error en el registro: " + JSON.stringify(data.errors));
            } else if (data.token) {
                localStorage.setItem("token", data.token);
                registerModal.classList.remove("show"); // Cierra el modal
                window.location.href = "/"; // Redirige a la página principal
            } else {
                alert("Error en el registro");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    });


    // Formulario de login
    document.getElementById("loginForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const email = document.querySelector("#loginModal input[name='correo']").value;
        const password = document.querySelector("#loginModal input[name='contrasena']").value;

        fetch("/api/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ email, password }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.errors) {
                    alert("Error en el login: " + JSON.stringify(data.errors));
                } else if (data.token) {
                    localStorage.setItem("token", data.token);
                    window.location.href = "/";
                } else {
                    alert("Error en el login");
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});

// ========================================
// SECCIÓN CRÍTICOS
// ========================================
function renderCriticos() {
    const criticosContainer = document.querySelector('.criticos-container');
    const criticos = [
        {
            nombre: "Ana García",
            imagen: "https://randomuser.me/api/portraits/women/1.jpg",
            rol: "Crítico Experto",
            reviews: 156,
            seguidores: 2.3,
            especialidad: "Cine de Autor",
            bio: "Especialista en cine de autor y documentales. Más de 10 años analizando el séptimo arte.",
            peliculasFavoritas: ["El Padrino", "Cinema Paradiso", "8½"],
            ultimasReviews: [
                { pelicula: "Oppenheimer", rating: 4.5, texto: "Una obra maestra del cine moderno..." },
                { pelicula: "Barbie", rating: 4.0, texto: "Una sorprendente sátira social..." }
            ]
        },
        {
            nombre: "Carlos Ruiz",
            imagen: "https://randomuser.me/api/portraits/men/2.jpg",
            rol: "Crítico Verificado",
            reviews: 98,
            seguidores: 1.8,
            especialidad: "Ciencia Ficción",
            bio: "Amante del cine de acción y ciencia ficción. Especializado en efectos visuales.",
            peliculasFavoritas: ["Blade Runner", "Matrix", "Inception"],
            ultimasReviews: [
                { pelicula: "Dune", rating: 4.8, texto: "Una experiencia visual extraordinaria..." }
            ]
        },
        {
            nombre: "Laura Martínez",
            imagen: "https://randomuser.me/api/portraits/women/3.jpg",
            rol: "Crítico Destacado",
            reviews: 203,
            seguidores: 3.1,
            especialidad: "Cine Independiente",
            bio: "Experta en cine independiente y festivales internacionales.",
            peliculasFavoritas: ["Lost in Translation", "Moonlight", "Lady Bird"],
            ultimasReviews: [
                { pelicula: "Past Lives", rating: 4.7, texto: "Una joya del cine independiente..." }
            ]
        }
    ];

    criticosContainer.innerHTML = criticos.map(critico => `
        <div class="critico" data-reviews='${JSON.stringify(critico.ultimasReviews)}'>
            <div class="critico-imagen">
                <img src="${critico.imagen}" alt="${critico.nombre}">
            </div>
            <div class="critico-info">
                <h3 class="critico-nombre">${critico.nombre}</h3>
                <span class="badge">${critico.rol}</span>
                <div class="critico-stats">
                    <div class="stat">
                        <i class="fas fa-star"></i>
                        <span>${critico.reviews} reviews</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-users"></i>
                        <span>${critico.seguidores}k</span>
                    </div>
                </div>
                <div class="critico-especialidad">
                    <i class="fas fa-film"></i>
                    <span>${critico.especialidad}</span>
                </div>
                <p class="critico-bio">${critico.bio}</p>
                <div class="critico-favoritas">
                    <small>Películas favoritas:</small>
                    <div class="favoritas-tags">
                        ${critico.peliculasFavoritas.map(peli => `<span class="favorita-tag">${peli}</span>`).join('')}
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    // Navegación en críticos
    document.getElementById('criticosPrev').addEventListener('click', () => {
        criticosContainer.scrollBy({ left: -320, behavior: 'smooth' });
    });
    document.getElementById('criticosNext').addEventListener('click', () => {
        criticosContainer.scrollBy({ left: 320, behavior: 'smooth' });
    });

    // Interactividad en las tarjetas de críticos
    document.querySelectorAll('.critico').forEach(critico => {
        critico.addEventListener('click', () => {
            if (document.getElementById('spoilerEnabled')?.checked) {
                showCriticoDetail(critico);
            } else {
                showToast('Activa los spoilers para ver las reseñas detalladas', 'info');
            }
        });
    });
}

function showCriticoDetail(criticoElement) {
    const reviews = JSON.parse(criticoElement.dataset.reviews);
    const nombre = criticoElement.querySelector('.critico-nombre').textContent;

    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reseñas de ${nombre}</h2>
            <div class="reviews-list">
                ${reviews.map(review => `
                    <div class="review-item">
                        <h4>${review.pelicula}</h4>
                        <div class="review-rating">
                            ${renderStars(review.rating)}
                        </div>
                        <p>${review.texto}</p>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.style.display = 'block';

    modal.querySelector('.close').addEventListener('click', () => {
        modal.style.display = 'none';
        setTimeout(() => modal.remove(), 300);
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            setTimeout(() => modal.remove(), 300);
        }
    });
}

// ========================================
// NAVEGACIÓN Y OTROS EVENTOS
// ========================================
// Navegación en tendencias
document.getElementById("trendingPrev").addEventListener("click", () => {
    document.getElementById("trendingContainer").scrollBy({
        left: -300,
        behavior: "smooth",
    });
});

document.getElementById("trendingNext").addEventListener("click", () => {
    document.getElementById("trendingContainer").scrollBy({
        left: 300,
        behavior: "smooth",
    });
});

// Botón Back to Top
const backToTopBtn = document.getElementById("backToTop");
window.addEventListener("scroll", () => {
    backToTopBtn.style.display = window.scrollY > 300 ? "block" : "none";
});
backToTopBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
});

// Acciones en sección críticos
document.getElementById("spoilerBtn").addEventListener("click", () => {
    if (confirm("Al hacer clic en los perfiles de críticos podrías ver spoilers. ¿Deseas continuar?")) {
        alert("Spoilers activados.");
    }
});

document.getElementById("hazteCritico").addEventListener("click", () => {
    alert("Formulario para hacerse crítico. (Simulado)");
});

// ========================================
// INICIALIZACIÓN
// ========================================
window.onload = () => {
    cargarBanner();
    cargarTendencias();
    setupModals();
    populateGenres();
    populateYears();
    renderFavorites();
    renderCriticos();
};

document.getElementById("generarRandom").addEventListener("click", generarRecomendacion);

// ========================================
// FUNCIONES DE TOAST (NOTIFICACIONES)
// ========================================
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 2000);
    }, 100);
}
