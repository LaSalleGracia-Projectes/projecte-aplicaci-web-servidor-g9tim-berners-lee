// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f";
const IMG_URL = "https://image.tmdb.org/t/p/w500";
const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
const SERIE_ENDPOINT = "/serie";

let currentView = 'grid';
let currentPage = 1;
let isLoading = false;
let activeFilters = {
    search: '',
    genre: '',
    year: '',
    rating: 0,
    sortBy: 'popularity.desc'
};
let featuredSeries = [];
let currentSlide = 0;
let autoSlideInterval;

// ========================================================
// UTILIDADES Y FUNCIONES DE AYUDA
// ========================================================
function showSpinner() {
    const spinner = document.querySelector(".spinner-overlay") || createSpinner();
    spinner.classList.add("active");
    document.body.classList.add("loading");
}

function hideSpinner() {
    const spinner = document.querySelector(".spinner-overlay");
    if (spinner) {
        spinner.classList.remove("active");
        document.body.classList.remove("loading");
    }
}

function createSpinner() {
    const spinner = document.createElement("div");
    spinner.className = "spinner-overlay";
    spinner.innerHTML = `
        <div class="spinner">
            <div class="spinner-inner"></div>
        </div>
    `;
    document.body.appendChild(spinner);
    return spinner;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function getGenreNameById(genreId) {
    const genres = {
        10759: "Acción y Aventura",
        16: "Animación",
        35: "Comedia",
        80: "Crimen",
        99: "Documental",
        18: "Drama",
        10751: "Familiar",
        10762: "Infantil",
        9648: "Misterio",
        10763: "Noticias",
        10764: "Reality",
        10765: "Ciencia Ficción y Fantasía",
        10766: "Telenovela",
        10767: "Talk Show",
        10768: "Guerra y Política",
        37: "Western"
    };
    return genres[genreId] || "Desconocido";
}

// ========================================================
// GESTOR DE SERIES DESTACADAS
// ========================================================
async function loadFeaturedSeries() {
    showSpinner();
    try {
        // Obtener series populares y al aire
        const [popularRes, airingRes] = await Promise.all([
            fetch(`${BASE_URL}/tv/popular?api_key=${API_KEY}&language=es-ES&page=1`),
            fetch(`${BASE_URL}/tv/on_the_air?api_key=${API_KEY}&language=es-ES&page=1`)
        ]);

        const [popularData, airingData] = await Promise.all([
            popularRes.json(),
            airingRes.json()
        ]);

        // Combinar y ordenar por popularidad
        const allSeries = [...popularData.results, ...airingData.results]
            .sort((a, b) => b.popularity - a.popularity)
            .filter((serie, index, self) =>
                index === self.findIndex((s) => s.id === serie.id)
            )
            .slice(0, 5);

        // Obtener detalles adicionales para cada serie
        featuredSeries = await Promise.all(
            allSeries.map(async (serie) => {
                const detailsRes = await fetch(`${BASE_URL}/tv/${serie.id}?api_key=${API_KEY}&language=es-ES&append_to_response=videos,credits`);
                const details = await detailsRes.json();
                return {
                    ...serie,
                    ...details
                };
            })
        );

        renderFeaturedSlider();
        startAutoSlide();
    } catch (error) {
        console.error("Error cargando series destacadas:", error);
        showNotification("Error al cargar las series destacadas", "error");
    } finally {
        hideSpinner();
    }
}

function renderFeaturedSlider() {
    const slider = document.querySelector(".featured-slider");
    if (!slider) return;

    slider.innerHTML = "";

    featuredSeries.forEach((serie, index) => {
        const slide = document.createElement("div");
        slide.className = `featured-item ${index === 0 ? "active" : ""}`;

        // Precargar la imagen de fondo
        const img = new Image();
        img.src = `${BACKDROP_URL}${serie.backdrop_path}`;
        img.onload = () => {
            slide.style.backgroundImage = `url(${img.src})`;
        };

        const content = document.createElement("div");
        content.className = "featured-content";

        // Preparar información del próximo episodio
        let nextEpisodeInfo = "";
        if (serie.next_episode_to_air) {
            const nextAirDate = new Date(serie.next_episode_to_air.air_date);
            const today = new Date();
            const daysUntilNext = Math.ceil((nextAirDate - today) / (1000 * 60 * 60 * 24));
            if (daysUntilNext > 0) {
                nextEpisodeInfo = `<span class="days-until">Próximo episodio en ${daysUntilNext} días</span>`;
            }
        }

        // Preparar géneros
        const genres = serie.genres?.map(g => g.name).join(", ");

        content.innerHTML = `
            <h2>${serie.name}</h2>
            <div class="featured-meta">
                <span class="rating">${serie.vote_average.toFixed(1)}/10</span>
                <span class="year">${serie.first_air_date.split("-")[0]}</span>
                ${nextEpisodeInfo}
            </div>
            <p class="featured-description">${serie.overview || ''}</p>
            <div class="featured-actions">
                <a href="${SERIE_ENDPOINT}/${serie.id}" class="btn-watch">
                    <i class="fas fa-play"></i> VER DETALLES
                </a>
                ${serie.videos?.results?.length > 0 ? `
                    <button class="btn-trailer" data-id="${serie.id}">
                        <i class="fas fa-film"></i> VER TRAILER
                    </button>
                ` : ''}
            </div>
        `;

        slide.appendChild(content);
        slider.appendChild(slide);
    });

    initSliderControls();
}

function initSliderControls() {
    const prevBtn = document.querySelector(".nav-btn.prev");
    const nextBtn = document.querySelector(".nav-btn.next");
    const slides = document.querySelectorAll(".featured-item");

    if (!prevBtn || !nextBtn || !slides.length) return;

    prevBtn.addEventListener("click", () => {
        changeSlide(-1);
    });

    nextBtn.addEventListener("click", () => {
        changeSlide(1);
    });

    // Pausar auto-avance al hover
    const slider = document.querySelector(".featured-slider");
    if (slider) {
        slider.addEventListener("mouseenter", stopAutoSlide);
        slider.addEventListener("mouseleave", startAutoSlide);
    }
}

function changeSlide(direction) {
    const slides = document.querySelectorAll(".featured-item");
    if (!slides.length) return;

    slides[currentSlide].classList.remove("active");
    currentSlide = (currentSlide + direction + slides.length) % slides.length;
    slides[currentSlide].classList.add("active");
}

function startAutoSlide() {
    stopAutoSlide();
    autoSlideInterval = setInterval(() => {
        changeSlide(1);
    }, 5000);
}

function stopAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
}

// ========================================================
// GESTOR DE VISTA GRID/LISTA
// ========================================================
function initViewToggle() {
    const gridViewBtn = document.getElementById("gridView");
    const listViewBtn = document.getElementById("listView");
    const container = document.getElementById("seriesContainer");

    if (!gridViewBtn || !listViewBtn || !container) return;

    gridViewBtn.addEventListener("click", () => {
        currentView = 'grid';
        container.classList.remove("list-view");
        gridViewBtn.classList.add("active");
        listViewBtn.classList.remove("active");
        localStorage.setItem("seriesView", "grid");
    });

    listViewBtn.addEventListener("click", () => {
        currentView = 'list';
        container.classList.add("list-view");
        listViewBtn.classList.add("active");
        gridViewBtn.classList.remove("active");
        localStorage.setItem("seriesView", "list");
    });

    // Cargar vista guardada
    const savedView = localStorage.getItem("seriesView");
    if (savedView === "list") {
        listViewBtn.click();
    }
}

// ========================================================
// CARGA Y RENDERIZADO DE SERIES
// ========================================================
async function loadSeries(reset = false) {
    if (isLoading) return;
    isLoading = true;

    const loadMoreBtn = document.getElementById("loadMoreBtn");
    if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.querySelector("span").textContent = "Cargando...";
        loadMoreBtn.querySelector("i").classList.remove("d-none");
    }

    const seriesContainer = document.getElementById("seriesContainer");

    if (reset && seriesContainer) {
        seriesContainer.innerHTML = "";
        currentPage = 1;
    }

    try {
        const url = new URL(`${BASE_URL}/discover/tv`);
        url.searchParams.append("api_key", API_KEY);
        url.searchParams.append("language", "es-ES");
        url.searchParams.append("page", currentPage);
        url.searchParams.append("sort_by", activeFilters.sortBy);

        if (activeFilters.genre) {
            url.searchParams.append("with_genres", activeFilters.genre);
        }

        if (activeFilters.year) {
            url.searchParams.append("first_air_date_year", activeFilters.year);
        }

        if (activeFilters.rating > 0) {
            url.searchParams.append("vote_average.gte", activeFilters.rating);
        }

        const response = await fetch(url);
        const data = await response.json();

        renderSeries(data.results, seriesContainer);
        currentPage++;

        // Animar las tarjetas cuando aparecen
        gsap.fromTo(
            ".serie-card:nth-last-child(-n+20)",
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: "power2.out" }
        );
    } catch (error) {
        console.error("Error cargando series:", error);
        showNotification("Error al cargar las series", "error");
    } finally {
        isLoading = false;
        if (loadMoreBtn) {
            loadMoreBtn.disabled = false;
            loadMoreBtn.querySelector("span").textContent = "Cargar más";
            loadMoreBtn.querySelector("i").classList.add("d-none");
        }
    }
}

function renderSeries(series, container) {
    if (!container) return;

    series.forEach(serie => {
        const posterUrl = serie.poster_path ?
            `${IMG_URL}${serie.poster_path}` :
            "images/no-poster.jpg";

        const year = serie.first_air_date ? serie.first_air_date.split("-")[0] : "N/A";
        const rating = serie.vote_average;
        const isNew = serie.first_air_date && new Date(serie.first_air_date) > new Date(Date.now() - 90 * 24 * 60 * 60 * 1000);
        const isTopRated = rating >= 8;

        const genreIds = serie.genre_ids || [];
        const genreNames = genreIds.map(id => getGenreNameById(id)).filter(name => name !== "Desconocido");
        const genres = genreNames.slice(0, 3).join(", ");

        const serieEl = document.createElement("div");
        serieEl.className = "serie-card";
        serieEl.setAttribute("data-id", serie.id);
        serieEl.setAttribute("data-title", serie.name.toLowerCase());
        serieEl.setAttribute("data-year", year);
        serieEl.setAttribute("data-rating", rating);
        serieEl.setAttribute("data-genres", genres.toLowerCase());

        serieEl.innerHTML = `
            <div class="serie-poster">
                <img src="${posterUrl}" alt="${serie.name}" loading="lazy">
                <div class="serie-badges">
                    ${isNew ? '<span class="badge new-badge">Nueva</span>' : ''}
                    ${isTopRated ? '<span class="badge top-badge">Top</span>' : ''}
                </div>
                <div class="serie-actions">
                    <button class="action-btn btn-trailer" data-id="${serie.id}" title="Ver Trailer">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="action-btn btn-favorite" data-id="${serie.id}" title="Añadir a Favoritos">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn btn-details" data-id="${serie.id}" title="Ver Detalles">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
            </div>
            <div class="serie-info">
                <h3>${serie.name}</h3>
                <div class="serie-meta">
                    <span class="year">${year}</span>
                    <span class="rating">
                        <i class="fas fa-star"></i>
                        ${rating.toFixed(1)}
                    </span>
                </div>
                <p class="genres">${genres}</p>
            </div>
        `;

        container.appendChild(serieEl);
    });

    // Añadir event listeners a los botones
    addSerieCardEventListeners();
}

function addSerieCardEventListeners() {
    document.querySelectorAll(".btn-trailer").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const serieId = btn.dataset.id;
            loadTrailer(serieId);
        });
    });

    document.querySelectorAll(".btn-favorite").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const serieId = btn.dataset.id;
            toggleFavorite(serieId, btn);
        });
    });

    document.querySelectorAll(".btn-details").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const serieId = btn.dataset.id;
            window.location.href = `${SERIE_ENDPOINT}/${serieId}`;
        });
    });
}

// ========================================================
// GESTOR DE TRAILER
// ========================================================
async function loadTrailer(serieId) {
    showSpinner();
    try {
        const response = await fetch(`${BASE_URL}/tv/${serieId}/videos?api_key=${API_KEY}&language=es-ES`);
        const data = await response.json();
        const trailers = data.results.filter(video => video.type === "Trailer");

        if (trailers.length > 0) {
            const trailer = trailers[0];
            showTrailerModal(trailer.key);
        } else {
            showNotification("No hay trailer disponible", "error");
        }
    } catch (error) {
        console.error("Error cargando trailer:", error);
        showNotification("Error al cargar el trailer", "error");
    } finally {
        hideSpinner();
    }
}

function showTrailerModal(videoKey) {
    // Crear el modal sin el iframe primero
    const modal = document.createElement("div");
    modal.className = "trailer-modal";
    modal.innerHTML = `
        <div class="trailer-modal-content">
            <button class="close-modal">&times;</button>
            <div id="serieTrailerContainer"></div>
        </div>
    `;

    // Añadir el modal al DOM
    document.body.appendChild(modal);
    document.body.style.overflow = "hidden";

    // Configurar el cierre del modal
    const closeBtn = modal.querySelector(".close-modal");
    const closeModal = () => {
        modal.remove();
        document.body.style.overflow = "";
    };

    closeBtn.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Cargar el iframe después de que el modal esté en el DOM
    requestAnimationFrame(() => {
        const trailerContainer = document.getElementById("serieTrailerContainer");
        trailerContainer.innerHTML = `
            <iframe
                width="100%"
                height="100%"
                src="https://www.youtube.com/embed/${videoKey}?autoplay=1"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
    `;
    });
}

// ========================================================
// GESTOR DE FAVORITOS
// ========================================================
function toggleFavorite(serieId, btn) {
    const favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
    const isFavorite = favorites.includes(serieId);
    const icon = btn.querySelector("i");

    if (isFavorite) {
        const index = favorites.indexOf(serieId);
        favorites.splice(index, 1);
        icon.classList.remove("fas");
        icon.classList.add("far");
        showNotification("Serie eliminada de favoritos", "success");
    } else {
        favorites.push(serieId);
        icon.classList.remove("far");
        icon.classList.add("fas");
        showNotification("Serie añadida a favoritos", "success");
    }

    localStorage.setItem("favorites", JSON.stringify(favorites));
}

// ========================================================
// NOTIFICACIONES
// ========================================================
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === "success" ? "fa-check-circle" : "fa-info-circle"}"></i>
            <span>${message}</span>
        </div>
        <button class="close-notification">&times;</button>
    `;

    document.body.appendChild(notification);

    gsap.fromTo(notification,
        { y: -50, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" }
    );

    setTimeout(() => {
        gsap.to(notification, {
            y: -50,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => notification.remove()
        });
    }, 3000);

    const closeBtn = notification.querySelector(".close-notification");
    closeBtn.addEventListener("click", () => {
        gsap.to(notification, {
            y: -50,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => notification.remove()
        });
    });
}

// ========================================================
// BÚSQUEDA Y FILTROS
// ========================================================
function setupSearchAutocomplete() {
    const searchInput = document.getElementById("searchInput");
    const suggestionsContainer = document.getElementById("suggestions");

    if (!searchInput || !suggestionsContainer) return;

    searchInput.addEventListener("input", debounce(async () => {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            suggestionsContainer.style.display = "none";
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/search/tv?api_key=${API_KEY}&language=es-ES&query=${encodeURIComponent(query)}`);
            const data = await response.json();
            const results = data.results.slice(0, 5);

            suggestionsContainer.innerHTML = "";

            if (results.length === 0) {
                suggestionsContainer.style.display = "none";
                return;
            }

            results.forEach(serie => {
                const suggestionItem = document.createElement("div");
                suggestionItem.className = "suggestion-item";

                const posterUrl = serie.poster_path ?
                    `${IMG_URL}${serie.poster_path}` :
                    "images/no-poster.jpg";

                const year = serie.first_air_date ? serie.first_air_date.split("-")[0] : "N/A";

                suggestionItem.innerHTML = `
                    <img src="${posterUrl}" alt="${serie.name}" loading="lazy">
                    <div class="suggestion-info">
                        <h4>${serie.name}</h4>
                        <p>${year}</p>
                    </div>
                `;

                suggestionItem.addEventListener("click", () => {
                    window.location.href = `${SERIE_ENDPOINT}/${serie.id}`;
                    suggestionsContainer.style.display = "none";
                });

                suggestionsContainer.appendChild(suggestionItem);
            });

            suggestionsContainer.style.display = "block";
        } catch (error) {
            console.error("Error en búsqueda:", error);
            suggestionsContainer.style.display = "none";
        }
    }, 300));

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".search-box")) {
            suggestionsContainer.style.display = "none";
        }
    });
}

function setupFilters() {
    const yearFilter = document.getElementById("yearFilter");
    const genreFilter = document.getElementById("genreFilter");
    const ratingFilter = document.getElementById("ratingFilter");
    const sortFilter = document.getElementById("sortFilter");

    if (yearFilter) {
        yearFilter.addEventListener("change", () => {
            activeFilters.year = yearFilter.value;
            loadSeries(true);
        });
    }

    if (genreFilter) {
        genreFilter.addEventListener("change", () => {
            activeFilters.genre = genreFilter.value;
            loadSeries(true);
        });
    }

    if (ratingFilter) {
        ratingFilter.addEventListener("change", () => {
            activeFilters.rating = parseFloat(ratingFilter.value);
            loadSeries(true);
        });
    }

    if (sortFilter) {
        sortFilter.addEventListener("change", () => {
            activeFilters.sortBy = sortFilter.value;
            loadSeries(true);
        });
    }
}

// ========================================================
// INICIALIZACIÓN
// ========================================================
document.addEventListener("DOMContentLoaded", () => {
    // Cargar series destacadas
    loadFeaturedSeries();

    // Inicializar toggle de vista
    initViewToggle();

    // Cargar series iniciales
    loadSeries();

    // Configurar búsqueda
    setupSearchAutocomplete();

    // Configurar filtros
    setupFilters();

    // Configurar infinite scroll
    setupInfiniteScroll();
});

// ========================================================
// INFINITE SCROLL
// ========================================================
function setupInfiniteScroll() {
    const options = {
        root: null,
        rootMargin: "0px",
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading) {
                loadSeries();
            }
        });
    }, options);

    const loadMoreTrigger = document.createElement("div");
    loadMoreTrigger.id = "loadMoreTrigger";
    document.getElementById("seriesContainer").appendChild(loadMoreTrigger);

    observer.observe(loadMoreTrigger);
}
