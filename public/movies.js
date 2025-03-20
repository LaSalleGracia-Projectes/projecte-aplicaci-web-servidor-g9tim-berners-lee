// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f"; // Reemplazar con tu API key real
const IMG_URL = "https://image.tmdb.org/t/p/w500";
const BACKDROP_URL = "https://image.tmdb.org/t/p/original";

let allMoviesPage = 1;
let isLoading = false;
let activeFilters = {
  search: "",
  year: "",
  genre: "",
  rating: 0,
  sort: "popularity.desc"
};
let featuredMovies = [];
let currentSlide = 0;

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
    <div class="spinner-container">
      <div class="spinner"></div>
      <p>Cargando...</p>
    </div>
  `;
  document.body.appendChild(spinner);
  return spinner;
}

function debounce(func, delay) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

function formatDate(dateString) {
  if (!dateString) return "N/A";
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

function getGenreNameById(genreId) {
  const genres = {
    28: "Acción",
    12: "Aventura",
    16: "Animación",
    35: "Comedia",
    80: "Crimen",
    99: "Documental",
    18: "Drama",
    10751: "Familiar",
    14: "Fantasía",
    36: "Historia",
    27: "Terror",
    10402: "Música",
    9648: "Misterio",
    10749: "Romance",
    878: "Ciencia ficción",
    10770: "Película de TV",
    53: "Thriller",
    10752: "Bélica",
    37: "Western"
  };
  return genres[genreId] || "Desconocido";
}

// ========================================================
// GESTOR DE PELÍCULAS DESTACADAS
// ========================================================
async function loadFeaturedMovies() {
  showSpinner();
  try {
    const res = await fetch(`${BASE_URL}/movie/now_playing?api_key=${API_KEY}&language=es-ES&page=1`);
    const data = await res.json();
    featuredMovies = data.results.slice(0, 5);
    renderFeaturedSlider();
  } catch (error) {
    console.error("Error cargando películas destacadas:", error);
  } finally {
    hideSpinner();
  }
}

function renderFeaturedSlider() {
  const slider = document.getElementById("featuredSlider");
  if (!slider) return;

  slider.innerHTML = "";

  featuredMovies.forEach((movie, index) => {
    const backdropUrl = movie.backdrop_path ?
      `${BACKDROP_URL}${movie.backdrop_path}` :
      "images/no-backdrop.jpg";

    const posterUrl = movie.poster_path ?
      `${IMG_URL}${movie.poster_path}` :
      "images/no-poster.jpg";

    const slide = document.createElement("div");
    slide.className = `featured-slide ${index === 0 ? 'active' : ''}`;
    slide.style.backgroundImage = `url('${backdropUrl}')`;

    slide.innerHTML = `
      <div class="featured-overlay"></div>
      <div class="featured-content">
        <div class="featured-poster">
          <img src="${posterUrl}" alt="${movie.title}" loading="lazy">
        </div>
        <div class="featured-info">
          <h2>${movie.title}</h2>
          <div class="featured-meta">
            <span class="release-date">${formatDate(movie.release_date)}</span>
            <span class="divider">•</span>
            <span class="rating">
              <i class="fas fa-star"></i> ${movie.vote_average.toFixed(1)}
            </span>
          </div>
          <p class="featured-overview">${movie.overview}</p>
          <div class="featured-actions">
            <button class="btn-primary btn-trailer" data-id="${movie.id}">
              <i class="fas fa-play"></i> Ver Trailer
            </button>
            <button class="btn-secondary btn-details" data-id="${movie.id}">
              <i class="fas fa-info-circle"></i> Más Información
            </button>
          </div>
        </div>
      </div>
    `;

    slider.appendChild(slide);
  });

  initFeaturedSlider();
}

function initFeaturedSlider() {
  const prevBtn = document.getElementById("prevSlide");
  const nextBtn = document.getElementById("nextSlide");

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      changeSlide(-1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      changeSlide(1);
    });
  }

  // Auto-slide cada 6 segundos
  setInterval(() => {
    changeSlide(1);
  }, 6000);
}

function changeSlide(direction) {
  const slides = document.querySelectorAll(".featured-slide");
  if (!slides.length) return;

  // Eliminar la clase active de la diapositiva actual
  slides[currentSlide].classList.remove("active");

  // Calcular la nueva diapositiva
  currentSlide = (currentSlide + direction + slides.length) % slides.length;

  // Agregar la clase active a la nueva diapositiva
  slides[currentSlide].classList.add("active");

  // Animación de transición con GSAP
  gsap.fromTo(
    slides[currentSlide],
    { opacity: 0.7, scale: 0.95 },
    { opacity: 1, scale: 1, duration: 0.7, ease: "power2.out" }
  );
}

// ========================================================
// FILTRADO DE PELÍCULAS
// ========================================================
function setupFilters() {
  const searchInput = document.getElementById("searchInput");
  const genreSelect = document.getElementById("genreSelect");
  const yearSelect = document.getElementById("yearSelect");
  const minRatingInput = document.getElementById("minRating");
  const ratingValue = document.getElementById("ratingValue");
  const sortSelect = document.getElementById("sortSelect");
  const applyFiltersBtn = document.getElementById("applyFilters");
  const resetFiltersBtn = document.getElementById("resetFilters");

  // Establecer listeners para los filtros
  if (searchInput) {
    searchInput.addEventListener("input", debounce(() => {
      activeFilters.search = searchInput.value.toLowerCase();
      updateRatingStars();
      filterMovies();
    }, 300));
  }

  if (genreSelect) {
    genreSelect.addEventListener("change", () => {
      activeFilters.genre = genreSelect.value;
    });
  }

  if (yearSelect) {
    yearSelect.addEventListener("change", () => {
      activeFilters.year = yearSelect.value;
    });
  }

  if (minRatingInput && ratingValue) {
    minRatingInput.addEventListener("input", function() {
      activeFilters.rating = parseFloat(this.value);
      ratingValue.textContent = this.value;
      updateRatingStars();
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener("change", () => {
      activeFilters.sort = sortSelect.value;
    });
  }

  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", () => {
      filterMovies();

      // Animación de filtros
      gsap.fromTo(
        "#moviesContainer .movie-card",
        { opacity: 0, y: 20 },
        {
          opacity: 1,
          y: 0,
          duration: 0.5,
          stagger: 0.05,
          ease: "power2.out"
        }
      );
    });
  }

  if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", resetFilters);
  }
}

function updateRatingStars() {
  const ratingStars = document.querySelector(".rating-stars");
  const ratingValue = activeFilters.rating;

  if (!ratingStars) return;

  ratingStars.innerHTML = "";

  // Crear estrellas según el valor actual
  for (let i = 0; i < 5; i++) {
    const star = document.createElement("i");
    if (i < Math.floor(ratingValue / 2)) {
      star.className = "fas fa-star";
    } else if (i < Math.ceil(ratingValue / 2) && ratingValue % 2 !== 0) {
      star.className = "fas fa-star-half-alt";
    } else {
      star.className = "far fa-star";
    }
    ratingStars.appendChild(star);
  }
}

function resetFilters() {
  activeFilters = {
    search: "",
    year: "",
    genre: "",
    rating: 0,
    sort: "popularity.desc"
  };

  // Resetear inputs
  const inputs = {
    searchInput: "",
    genreSelect: "",
    yearSelect: "",
    minRating: "0",
    sortSelect: "popularity.desc"
  };

  for (const [id, value] of Object.entries(inputs)) {
    const element = document.getElementById(id);
    if (element) element.value = value;
  }

  // Actualizar valor de rating
  const ratingValue = document.getElementById("ratingValue");
  if (ratingValue) ratingValue.textContent = "0";

  updateRatingStars();
  filterMovies();
}

function filterMovies() {
  const movieCards = document.querySelectorAll("#moviesContainer .movie-card");

  movieCards.forEach(card => {
    const title = card.getAttribute("data-title")?.toLowerCase() || "";
    const year = card.getAttribute("data-year") || "";
    const rating = parseFloat(card.getAttribute("data-rating") || "0");
    const genres = card.getAttribute("data-genres")?.toLowerCase() || "";

    const matchesSearch = activeFilters.search === "" ||
                          title.includes(activeFilters.search);
    const matchesYear = activeFilters.year === "" ||
                         year === activeFilters.year;
    const matchesGenre = activeFilters.genre === "" ||
                          genres.includes(getGenreNameById(parseInt(activeFilters.genre)).toLowerCase());
    const matchesRating = rating >= activeFilters.rating;

    if (matchesSearch && matchesYear && matchesGenre && matchesRating) {
      card.style.display = "";
    } else {
      card.style.display = "none";
    }
  });

  // Verificar si no hay resultados y mostrar mensaje
  const visibleCards = Array.from(movieCards).filter(card => card.style.display !== "none");
  const container = document.getElementById("moviesContainer");

  if (visibleCards.length === 0 && container) {
    let noResults = container.querySelector(".no-results");
    if (!noResults) {
      noResults = document.createElement("div");
      noResults.className = "no-results";
      noResults.innerHTML = `
        <i class="fas fa-film"></i>
        <h3>No se encontraron películas</h3>
        <p>Intenta con otros filtros o <button id="resetFiltersInline" class="btn-link">resetea los filtros</button></p>
      `;
      container.appendChild(noResults);

      document.getElementById("resetFiltersInline").addEventListener("click", resetFilters);
    }
  } else {
    const noResults = container?.querySelector(".no-results");
    if (noResults) noResults.remove();
  }
}

// ========================================================
// ALTERNANCIA DE VISTAS (GRID/LISTA)
// ========================================================
function setupViewToggle() {
  const gridViewBtn = document.getElementById("gridView");
  const listViewBtn = document.getElementById("listView");
  const moviesContainer = document.getElementById("moviesContainer");

  if (gridViewBtn && listViewBtn && moviesContainer) {
    gridViewBtn.addEventListener("click", () => {
      moviesContainer.classList.remove("list-view");
      moviesContainer.classList.add("grid-view");
      gridViewBtn.classList.add("active");
      listViewBtn.classList.remove("active");

      // Animación de transición
      gsap.fromTo(
        ".movie-card",
        { scale: 0.95, opacity: 0.8 },
        { scale: 1, opacity: 1, duration: 0.3, stagger: 0.02, ease: "power1.out" }
      );
    });

    listViewBtn.addEventListener("click", () => {
      moviesContainer.classList.remove("grid-view");
      moviesContainer.classList.add("list-view");
      listViewBtn.classList.add("active");
      gridViewBtn.classList.remove("active");

      // Animación de transición
      gsap.fromTo(
        ".movie-card",
        { x: -20, opacity: 0.8 },
        { x: 0, opacity: 1, duration: 0.3, stagger: 0.02, ease: "power1.out" }
      );
    });
  }
}

// ========================================================
// GESTIÓN DE MODALES
// ========================================================
function setupModals() {
  // Delegación para abrir el modal de detalles
  document.body.addEventListener("click", (e) => {
    // Botón de detalles
    if (e.target.closest(".btn-details")) {
      e.preventDefault();
      const button = e.target.closest(".btn-details");
      const movieId = button.getAttribute("data-id");
      if (movieId) loadMovieDetails(movieId);
    }

   // Botón de trailer
    if (e.target.closest(".btn-trailer")) {
      e.preventDefault();
      const button = e.target.closest(".btn-trailer");
      const movieId = button.getAttribute("data-id");
      if (movieId) loadMovieTrailer(movieId);
    }

    // Cerrar modal
    if (e.target.closest(".modal-close") || e.target.classList.contains("modal-backdrop")) {
      closeModals();
    }
  });

  // Cerrar modal con Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModals();
  });
}

function closeModals() {
  const modals = document.querySelectorAll(".modal");
  modals.forEach(modal => {
    modal.classList.remove("active");

    // Si es un modal de trailer, detener la reproducción
    const trailerContainer = modal.querySelector("#trailerContainer");
    if (trailerContainer) {
      trailerContainer.innerHTML = "";
    }
  });
  document.body.classList.remove("modal-open");
}

async function loadMovieDetails(movieId) {
  showSpinner();
  try {
    const [movieRes, creditsRes, similarRes] = await Promise.all([
      fetch(`${BASE_URL}/movie/${movieId}?api_key=${API_KEY}&language=es-ES`),
      fetch(`${BASE_URL}/movie/${movieId}/credits?api_key=${API_KEY}&language=es-ES`),
      fetch(`${BASE_URL}/movie/${movieId}/similar?api_key=${API_KEY}&language=es-ES&page=1`)
    ]);

    const movieData = await movieRes.json();
    const creditsData = await creditsRes.json();
    const similarData = await similarRes.json();

    renderMovieModal(movieData, creditsData, similarData.results.slice(0, 4));
  } catch (error) {
    console.error("Error cargando detalles de película:", error);
    const modalBody = document.getElementById("modalBody");
    if (modalBody) {
      modalBody.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <p>Ha ocurrido un error al cargar los detalles. Por favor, inténtalo de nuevo.</p>
        </div>
      `;
    }
  } finally {
    hideSpinner();
  }
}

function renderMovieModal(movie, credits, similarMovies) {
  const modalBody = document.getElementById("modalBody");
  if (!modalBody) return;

  const backdropUrl = movie.backdrop_path ?
    `${BACKDROP_URL}${movie.backdrop_path}` :
    null;

  const posterUrl = movie.poster_path ?
    `${IMG_URL}${movie.poster_path}` :
    "images/no-poster.jpg";

  // Obtener director
  const director = credits.crew.find(person => person.job === "Director");

  // Obtener actores principales (máximo 5)
  const mainCast = credits.cast.slice(0, 5);

  // Calcular valoración en estrellas (de 5)
  const rating = movie.vote_average;
  const starRating = rating / 2;
  let starsHTML = "";

  for (let i = 1; i <= 5; i++) {
    if (i <= Math.floor(starRating)) {
      starsHTML += `<i class="fas fa-star"></i>`;
    } else if (i - 0.5 <= starRating) {
      starsHTML += `<i class="fas fa-star-half-alt"></i>`;
    } else {
      starsHTML += `<i class="far fa-star"></i>`;
    }
  }

  // Crear HTML para detalles de la película
  modalBody.innerHTML = `
    <div class="modal-movie">
      ${backdropUrl ? `<div class="modal-backdrop-image" style="background-image: url('${backdropUrl}')"></div>` : ''}

      <div class="modal-content-wrapper">
        <div class="modal-poster">
          <img src="${posterUrl}" alt="${movie.title}" loading="lazy">
          <button class="btn-primary btn-trailer" data-id="${movie.id}">
            <i class="fas fa-play"></i> Ver Trailer
          </button>
        </div>

        <div class="modal-details">
          <h2>${movie.title}</h2>

          <div class="modal-meta">
            <span class="year">${movie.release_date ? movie.release_date.split("-")[0] : "N/A"}</span>
            <span class="divider">•</span>
            <span class="runtime">${movie.runtime ? `${movie.runtime} min` : "N/A"}</span>
            <span class="divider">•</span>
            <span class="rating">
              ${starsHTML}
              <span class="rating-value">${rating.toFixed(1)}</span>
            </span>
          </div>

          <div class="modal-genres">
            ${movie.genres.map(genre => `<span class="genre-badge">${genre.name}</span>`).join('')}
          </div>

          <div class="modal-section">
            <h3>Sinopsis</h3>
            <p>${movie.overview || "No hay sinopsis disponible."}</p>
          </div>

          <div class="modal-section">
            <h3>Reparto</h3>
            <div class="cast-list">
              ${director ? `<div class="cast-item director">
                <span class="role">Director:</span>
                <span class="name">${director.name}</span>
              </div>` : ''}

              ${mainCast.map(actor => `
                <div class="cast-item">
                  <span class="name">${actor.name}</span>
                  <span class="character">${actor.character}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="modal-info-grid">
            <div class="info-item">
              <i class="fas fa-calendar-alt"></i>
              <div>
                <span class="label">Fecha de estreno</span>
                <span class="value">${formatDate(movie.release_date)}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-globe"></i>
              <div>
                <span class="label">Idioma original</span>
                <span class="value">${movie.original_language === 'en' ? 'Inglés' : movie.original_language === 'es' ? 'Español' : movie.original_language}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-money-bill-wave"></i>
              <div>
                <span class="label">Presupuesto</span>
                <span class="value">${movie.budget ? `$${(movie.budget / 1000000).toFixed(1)} M` : 'No disponible'}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-chart-line"></i>
              <div>
                <span class="label">Recaudación</span>
                <span class="value">${movie.revenue ? `$${(movie.revenue / 1000000).toFixed(1)} M` : 'No disponible'}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      ${similarMovies.length > 0 ? `
        <div class="similar-movies">
          <h3>Películas similares</h3>
          <div class="similar-grid">
            ${similarMovies.map(similar => `
              <div class="similar-movie">
                <img src="${similar.poster_path ? IMG_URL + similar.poster_path : 'images/no-poster.jpg'}" alt="${similar.title}">
                <div class="similar-info">
                  <h4>${similar.title}</h4>
                  <p>${similar.release_date ? similar.release_date.split('-')[0] : 'N/A'} • ${similar.vote_average.toFixed(1)} <i class="fas fa-star"></i></p>
                  <button class="btn-details" data-id="${similar.id}">Ver detalles</button>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      ` : ''}
    </div>
  `;

  // Mostrar el modal con animación
  const movieModal = document.getElementById("movieModal");
  if (movieModal) {
    movieModal.classList.add("active");
    document.body.classList.add("modal-open");

    // Animación de entrada
    gsap.fromTo(
      ".modal-content",
      { y: 50, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
    );
  }
}

async function loadMovieTrailer(movieId) {
  showSpinner();
  try {
    const res = await fetch(`${BASE_URL}/movie/${movieId}/videos?api_key=${API_KEY}&language=es-ES`);
    const data = await res.json();

    // Buscar un trailer oficial o teaser
    let trailerKey = null;
    const videos = data.results;

    // Primero intentar encontrar un trailer en español
    const spanishTrailer = videos.find(video =>
      (video.type === "Trailer" || video.type === "Teaser") &&
      video.site === "YouTube" &&
      (video.name.toLowerCase().includes("español") || video.name.toLowerCase().includes("spanish"))
    );

    if (spanishTrailer) {
      trailerKey = spanishTrailer.key;
    } else {
      // Si no hay trailer en español, buscar cualquier trailer
      const anyTrailer = videos.find(video =>
        (video.type === "Trailer" || video.type === "Teaser") &&
        video.site === "YouTube"
      );

      if (anyTrailer) {
        trailerKey = anyTrailer.key;
      }
    }

    renderTrailerModal(trailerKey);
  } catch (error) {
    console.error("Error cargando trailer:", error);
    renderTrailerModal(null);
  } finally {
    hideSpinner();
  }
}

function renderTrailerModal(trailerKey) {
  const trailerContainer = document.getElementById("trailerContainer");
  if (!trailerContainer) return;

  if (trailerKey) {
    trailerContainer.innerHTML = `
      <iframe
        width="100%"
        height="100%"
        src="https://www.youtube.com/embed/${trailerKey}?autoplay=1&rel=0"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
      </iframe>
    `;
  } else {
    trailerContainer.innerHTML = `
      <div class="no-trailer">
        <i class="fas fa-film"></i>
        <p>No se encontró ningún trailer disponible para esta película.</p>
      </div>
    `;
  }

  // Mostrar el modal
  const trailerModal = document.getElementById("trailerModal");
  if (trailerModal) {
    trailerModal.classList.add("active");
    document.body.classList.add("modal-open");
  }
}

// ========================================================
// CARGA Y RENDERIZADO DE PELÍCULAS
// ========================================================
async function loadMovies(reset = false) {
  if (isLoading) return;
  isLoading = true;

  const loadMoreBtn = document.getElementById("loadMoreBtn");
  if (loadMoreBtn) {
    loadMoreBtn.disabled = true;
    loadMoreBtn.querySelector("span").textContent = "Cargando...";
    loadMoreBtn.querySelector("i").classList.remove("d-none");
  }

  const moviesContainer = document.getElementById("moviesContainer");

  // Si es un reset, vaciar el contenedor y resetear la página
  if (reset && moviesContainer) {
    moviesContainer.innerHTML = "";
    allMoviesPage = 1;
  }

  try {
    const url = new URL(`${BASE_URL}/discover/movie`);
    url.searchParams.append("api_key", API_KEY);
    url.searchParams.append("language", "es-ES");
    url.searchParams.append("page", allMoviesPage);
    url.searchParams.append("sort_by", activeFilters.sort);

    if (activeFilters.genre) {
      url.searchParams.append("with_genres", activeFilters.genre);
    }

    if (activeFilters.year) {
      url.searchParams.append("primary_release_year", activeFilters.year);
    }

    if (activeFilters.rating > 0) {
      url.searchParams.append("vote_average.gte", activeFilters.rating);
    }

    const response = await fetch(url);
    const data = await response.json();

    renderMovies(data.results, moviesContainer);
    allMoviesPage++;

    // Animar las tarjetas cuando aparecen
    gsap.fromTo(
      ".movie-card:nth-last-child(-n+20)",
      { y: 30, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: "power2.out" }
    );
  } catch (error) {
    console.error("Error cargando películas:", error);
    if (moviesContainer && allMoviesPage === 1) {
      moviesContainer.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-triangle"></i>
          <p>Ha ocurrido un error al cargar las películas.</p>
          <button id="retryBtn" class="btn btn-primary">Reintentar</button>
        </div>
      `;
      document.getElementById("retryBtn").addEventListener("click", () => loadMovies(true));
    }
  } finally {
    isLoading = false;
    if (loadMoreBtn) {
      loadMoreBtn.disabled = false;
      loadMoreBtn.querySelector("span").textContent = "Cargar más películas";
      loadMoreBtn.querySelector("i").classList.add("d-none");
    }
  }
}
function renderMovies(movies, container) {
    if (!container) return;

    movies.forEach(movie => {
      const posterUrl = movie.poster_path ?
        `${IMG_URL}${movie.poster_path}` :
        "images/no-poster.jpg";

      const year = movie.release_date ? movie.release_date.split("-")[0] : "N/A";
      const rating = movie.vote_average;
      const isNew = movie.release_date && new Date(movie.release_date) > new Date(Date.now() - 90 * 24 * 60 * 60 * 1000);
      const isTopRated = rating >= 8;

     //
      // Generar estrellas para la calificación
      const starRating = rating / 2;
      let starsHTML = "";

      for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(starRating)) {
          starsHTML += `<i class="fas fa-star"></i>`;
        } else if (i - 0.5 <= starRating) {
          starsHTML += `<i class="fas fa-star-half-alt"></i>`;
        } else {
          starsHTML += `<i class="far fa-star"></i>`;
        }
      }

      // Obtener géneros de la película
      const genreIds = movie.genre_ids || [];
      const genreNames = genreIds.map(id => getGenreNameById(id)).filter(name => name !== "Desconocido");
      const genres = genreNames.slice(0, 3).join(", ");

      // Crear tarjeta de película
      const movieEl = document.createElement("div");
      movieEl.className = "movie-card";
      movieEl.setAttribute("data-id", movie.id);
      movieEl.setAttribute("data-title", movie.title.toLowerCase());
      movieEl.setAttribute("data-year", year);
      movieEl.setAttribute("data-rating", rating);
      movieEl.setAttribute("data-genres", genres.toLowerCase());

      movieEl.innerHTML = `
        <div class="movie-poster">
          <img src="${posterUrl}" alt="${movie.title}" loading="lazy">
          <div class="movie-badges">
            ${isNew ? '<span class="badge new-badge">Nuevo</span>' : ''}
            ${isTopRated ? '<span class="badge top-badge">Top</span>' : ''}
          </div>
          <div class="movie-actions">
            <button class="action-btn btn-trailer" data-id="${movie.id}" title="Ver trailer">
              <i class="fas fa-play"></i>
            </button>
            <button class="action-btn btn-favorite" data-id="${movie.id}" title="Añadir a favoritos">
              <i class="far fa-heart"></i>
            </button>
            <button class="action-btn btn-details" data-id="${movie.id}" title="Ver detalles">
              <i class="fas fa-info-circle"></i>
            </button>
          </div>
        </div>
        <div class="movie-info">
          <h3>${movie.title}</h3>
          <div class="movie-meta">
            <span class="year">${year}</span>
            <span class="divider">•</span>
            <span class="rating">
              ${starsHTML}
              <span class="rating-value">${rating.toFixed(1)}</span>
            </span>
          </div>
          <p class="genres">${genres || "Sin información de géneros"}</p>
          <p class="overview">${movie.overview.substring(0, 100)}${movie.overview.length > 100 ? '...' : ''}</p>
          <button class="btn-more btn-details" data-id="${movie.id}">Ver más</button>
        </div>
      `;

      container.appendChild(movieEl);
    });
  }

  // ========================================================
  // SCROLL INFINITO Y EFECTOS VISUALES
  // ========================================================
  function setupScrollEffects() {
    // Detectar cuando el usuario está cerca del final de la página
    const handleScroll = debounce(() => {
      const loadMoreBtn = document.getElementById("loadMoreBtn");
      if (!loadMoreBtn || isLoading) return;

      const rect = loadMoreBtn.getBoundingClientRect();
      const isVisible = (
        rect.top >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
      );

      if (isVisible) {
        loadMovies();
      }
    }, 200);

    window.addEventListener("scroll", handleScroll);

    // Animación para elementos que aparecen al hacer scroll
    const animateOnScroll = () => {
      const elements = document.querySelectorAll(".animate-on-scroll:not(.animated)");

      elements.forEach(el => {
        const rect = el.getBoundingClientRect();
        const isVisible = (
          rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8
        );

        if (isVisible) {
          el.classList.add("animated");

          gsap.fromTo(
            el,
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" }
          );
        }
      });
    };

    window.addEventListener("scroll", debounce(animateOnScroll, 100));
    animateOnScroll(); // Ejecutar la primera vez

    // Efecto parallax para el hero
    const heroSection = document.querySelector(".hero-section");
    if (heroSection) {
      window.addEventListener("scroll", () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop <= window.innerHeight) {
          heroSection.style.backgroundPositionY = `${scrollTop * 0.5}px`;
          heroSection.querySelector(".hero-content").style.transform = `translateY(${scrollTop * 0.3}px)`;
          heroSection.style.opacity = 1 - (scrollTop / (window.innerHeight * 0.6));
        }
      });
    }
  }

  // ========================================================
  // BÚSQUEDA AVANZADA CON AUTOCOMPLETADO
  // ========================================================
  function setupSearchAutocomplete() {
    const searchInput = document.getElementById("searchInput");

    if (!searchInput) return;

    // Crear contenedor para sugerencias
    const suggestionsContainer = document.createElement("div");
    suggestionsContainer.className = "search-suggestions";
    suggestionsContainer.style.display = "none";
    searchInput.parentNode.appendChild(suggestionsContainer);

    searchInput.addEventListener("input", debounce(async () => {
      const query = searchInput.value.trim();

      if (query.length < 3) {
        suggestionsContainer.style.display = "none";
        return;
      }

      try {
        const response = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&language=es-ES&query=${encodeURIComponent(query)}`);
        const data = await response.json();
        const results = data.results.slice(0, 5);

        // Limpiar sugerencias anteriores
        suggestionsContainer.innerHTML = "";

        if (results.length === 0) {
          suggestionsContainer.style.display = "none";
          return;
        }

        // Mostrar sugerencias
        results.forEach(movie => {
          const suggestionItem = document.createElement("div");
          suggestionItem.className = "suggestion-item";

          const posterUrl = movie.poster_path ?
            `${IMG_URL}${movie.poster_path}` :
            "images/no-poster.jpg";

          const year = movie.release_date ? movie.release_date.split("-")[0] : "N/A";

          suggestionItem.innerHTML = `
            <img src="${posterUrl}" alt="${movie.title}" loading="lazy">
            <div class="suggestion-info">
              <h4>${movie.title}</h4>
              <p>${year}</p>
            </div>
          `;

          suggestionItem.addEventListener("click", () => {
            loadMovieDetails(movie.id);
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

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".search-box")) {
        suggestionsContainer.style.display = "none";
      }
    });
  }

  // ========================================================
  // FAVORITOS Y ALMACENAMIENTO LOCAL
  // ========================================================
  function setupFavorites() {
    document.body.addEventListener("click", (e) => {
      if (e.target.closest(".btn-favorite")) {
        const button = e.target.closest(".btn-favorite");
        const movieId = button.getAttribute("data-id");

        if (movieId) {
          toggleFavorite(movieId, button);
        }
      }
    });

    // Actualizar estado inicial de los botones de favoritos
    updateFavoriteButtons();
  }

  function toggleFavorite(movieId, button) {
    let favorites = JSON.parse(localStorage.getItem("critiflix_favorites") || "[]");

    const index = favorites.indexOf(movieId);

    if (index === -1) {
      // Añadir a favoritos
      favorites.push(movieId);

      // Mostrar notificación
      showNotification("Película añadida a favoritos", "success");

      // Animar corazón
      if (button) {
        button.querySelector("i").className = "fas fa-heart";
        gsap.fromTo(
          button,
          { scale: 0.5 },
          { scale: 1, duration: 0.3, ease: "back.out(1.7)" }
        );
      }
    } else {
      // Quitar de favoritos
      favorites.splice(index, 1);

      // Mostrar notificación
      showNotification("Película eliminada de favoritos", "info");

      // Animar corazón
      if (button) {
        button.querySelector("i").className = "far fa-heart";
      }
    }

    localStorage.setItem("critiflix_favorites", JSON.stringify(favorites));
    updateFavoriteButtons();
  }

  function updateFavoriteButtons() {
    const favorites = JSON.parse(localStorage.getItem("critiflix_favorites") || "[]");
    const buttons = document.querySelectorAll(".btn-favorite");

    buttons.forEach(button => {
      const movieId = button.getAttribute("data-id");
      const icon = button.querySelector("i");

      if (icon) {
        if (favorites.includes(movieId)) {
          icon.className = "fas fa-heart";
        } else {
          icon.className = "far fa-heart";
        }
      }
    });
  }

  function showNotification(message, type = "info") {
    // Eliminar notificaciones existentes
    const existingNotifications = document.querySelectorAll(".notification");
    existingNotifications.forEach(notification => {
      notification.remove();
    });

    // Crear nueva notificación
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;

    let icon = "info-circle";
    if (type === "success") icon = "check-circle";
    if (type === "error") icon = "exclamation-circle";

    notification.innerHTML = `
      <i class="fas fa-${icon}"></i>
      <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Animar entrada
    gsap.fromTo(
      notification,
      { y: -50, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" }
    );

    // Desaparecer después de 3 segundos
    setTimeout(() => {
      gsap.to(notification, {
        opacity: 0,
        y: -20,
        duration: 0.3,
        onComplete: () => notification.remove()
      });
    }, 3000);
  }


