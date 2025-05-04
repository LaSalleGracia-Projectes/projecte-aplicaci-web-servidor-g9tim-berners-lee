// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f"; // Reemplazar con tu API key real
const IMG_URL = "https://image.tmdb.org/t/p/w500";
const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
const DEFAULT_POSTER = "https://placehold.co/500x750/121212/00ff3c?text=Sin+Imagen";
const DEFAULT_BACKDROP = "https://placehold.co/1280x720/121212/00ff3c?text=Sin+Imagen+de+Fondo";

let allSeriesPage = 1;
let isLoading = false;
let activeFilters = {
  search: "",
  year: "",
  genre: "",
  rating: 0,
  sort: "popularity.desc"
};
let featuredSeries = [];
let currentSlide = 0;
let searchTimeout;
let lastScrollPosition = 0;

// Variable para controlar si los manejadores de eventos ya se configuraron
let trailerEventsConfigured = false;
let serieDetailEventsConfigured = false;

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
    10765: "Ciencia ficción y Fantasía",
    10766: "Soap",
    10767: "Talk",
    10768: "Guerra y Política",
    37: "Western"
  };
  return genres[genreId] || "Desconocido";
}

// ========================================================
// SCROLL TO TOP Y EFECTOS DE UI
// ========================================================
function setupScrollToTop() {
  const scrollTopBtn = document.getElementById("scrollTop");

  if (!scrollTopBtn) return;

  // Mostrar/ocultar botón al hacer scroll
  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 500) {
      scrollTopBtn.classList.add("visible");
    } else {
      scrollTopBtn.classList.remove("visible");
    }
  });

  // Evento de clic para scroll hacia arriba
  scrollTopBtn.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  });
}

// Función para manejar el scroll de la página
function handleScroll() {
  const currentScrollPos = window.pageYOffset;
  const toolbarWrapper = document.getElementById("toolbarWrapper");

  // Aplicar clase al toolbar cuando se hace scroll
  if (toolbarWrapper) {
    if (currentScrollPos > 150) {
      toolbarWrapper.classList.add("scrolled");
    } else {
      toolbarWrapper.classList.remove("scrolled");
    }
  }

  // Guardar la posición actual para la próxima vez
  lastScrollPosition = currentScrollPos;
}

// ========================================================
// GESTOR DE SERIES DESTACADAS
// ========================================================
async function loadFeaturedSeries() {
  showSpinner();
  try {
    const res = await fetch(`${BASE_URL}/tv/on_the_air?api_key=${API_KEY}&language=es-ES&page=1`);
    const data = await res.json();
    featuredSeries = data.results.slice(0, 5);
    renderFeaturedSlider();
  } catch (error) {
    console.error("Error cargando series destacadas:", error);
    showNotification("Error al cargar series destacadas. Por favor, inténtalo de nuevo más tarde.", "error");
  } finally {
    hideSpinner();
  }
}

function renderFeaturedSlider() {
  const slider = document.getElementById("featuredSlider");
  if (!slider) return;

  slider.innerHTML = "";

  featuredSeries.forEach((serie, index) => {
    const backdropUrl = serie.backdrop_path ?
      `${BACKDROP_URL}${serie.backdrop_path}` :
      DEFAULT_BACKDROP;

    const posterUrl = serie.poster_path ?
      `${IMG_URL}${serie.poster_path}` :
      DEFAULT_POSTER;

    const slide = document.createElement("div");
    slide.className = "swiper-slide";

    slide.innerHTML = `
      <div class="featured-slide">
        <div class="featured-overlay" style="background-image: url('${backdropUrl}')"></div>
        <div class="featured-content">
          <div class="featured-poster">
            <img src="${posterUrl}" alt="${serie.name}" loading="lazy">
          </div>
          <div class="featured-info">
            <h2>${serie.name}</h2>
            <div class="featured-meta">
              <span class="release-date">${formatDate(serie.first_air_date)}</span>
              <span class="divider">•</span>
              <span class="rating">
                <i class="fas fa-star"></i> ${serie.vote_average.toFixed(1)}
              </span>
            </div>
            <p class="featured-overview">${serie.overview || 'No hay descripción disponible para esta serie.'}</p>
            <div class="featured-actions">
              <button class="btn-primary btn-trailer" data-id="${serie.id}" aria-label="Ver trailer de ${serie.name}">
                <i class="fas fa-play"></i> Ver Trailer
              </button>
              <button class="btn-secondary btn-details" data-id="${serie.id}" aria-label="Ver más información de ${serie.name}">
                <i class="fas fa-info-circle"></i> Más Información
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

    slider.appendChild(slide);
  });

  // Inicializar Swiper
  initSwiperSlider();
}

function initSwiperSlider() {
  // Si Swiper no está cargado, esperamos 300ms e intentamos de nuevo
  if (typeof Swiper === "undefined") {
    setTimeout(() => initSwiperSlider(), 300);
    return;
  }

  new Swiper(".featured-swiper", {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    effect: "fade",
    fadeEffect: {
      crossFade: true
    }
  });

  // Configurar los botones en los slides destacados
  document.querySelectorAll(".featured-actions .btn-trailer").forEach(btn => {
    btn.addEventListener("click", function() {
      const serieId = this.getAttribute("data-id");
      loadSerieTrailer(serieId);
    });
  });

  document.querySelectorAll(".featured-actions .btn-details").forEach(btn => {
    btn.addEventListener("click", function() {
      const serieId = this.getAttribute("data-id");
      loadSerieDetails(serieId);
    });
  });
}

// ========================================================
// CONFIGURACIÓN DE FILTROS
// ========================================================
function setupFilters() {
  const filterButtons = document.querySelectorAll(".quick-filter");
  const advancedFilterToggle = document.getElementById("advancedFilterToggle");
  const closeFiltersBtn = document.getElementById("closeFilters");
  const applyFiltersBtn = document.getElementById("applyFilters");
  const resetFiltersBtn = document.getElementById("resetFilters");
  const minRatingSlider = document.getElementById("minRating");
  const ratingValue = document.getElementById("ratingValue");
  const genreSelect = document.getElementById("genreSelect");
  const yearSelect = document.getElementById("yearSelect");
  const sortSelect = document.getElementById("sortSelect");
  const searchInput = document.getElementById("searchInput");
  const categoryChips = document.querySelectorAll(".category-chip");

  // Configurar filtros rápidos
  if (filterButtons) {
    filterButtons.forEach(button => {
      button.addEventListener("click", function() {
        // Quitar clase active de todos los botones
        filterButtons.forEach(btn => btn.classList.remove("active"));
        // Añadir clase active al botón seleccionado
        this.classList.add("active");

        // Aplicar filtro según el valor del atributo data-filter
        const filter = this.getAttribute("data-filter");

        // Resetear filtros actuales sin activar el botón "Todas"
        resetAllFilters(false);

        // Aplicar filtro específico
        switch(filter) {
          case "trending":
            activeFilters.sort = "popularity.desc";
            break;
          case "toprated":
            activeFilters.sort = "vote_average.desc";
            activeFilters.rating = 7;
            if (minRatingSlider) minRatingSlider.value = 7;
            if (ratingValue) ratingValue.textContent = "7";
            updateRatingStars();
            break;
          case "new":
            const currentYear = new Date().getFullYear();
            activeFilters.year = currentYear.toString();
            if (yearSelect) yearSelect.value = currentYear;
            break;
          default:
            // 'all' - mantener valores por defecto
            break;
        }

        // Cargar series con los nuevos filtros
        loadSeries(true);

        // Aplicar animación al botón
        this.classList.add("animated");
        setTimeout(() => {
          this.classList.remove("animated");
        }, 600);
      });
    });
  }

  // Configurar toggle de filtros avanzados
  if (advancedFilterToggle) {
    advancedFilterToggle.addEventListener("click", function() {
      document.getElementById("advancedFilters").classList.add("active");
    });
  }

  // Configurar cierre de filtros avanzados
  if (closeFiltersBtn) {
    closeFiltersBtn.addEventListener("click", function() {
      document.getElementById("advancedFilters").classList.remove("active");
    });
  }

  // Configurar slider de rating
  if (minRatingSlider && ratingValue) {
    minRatingSlider.addEventListener("input", function() {
      ratingValue.textContent = this.value;
      updateRatingStars();
    });

    function updateRatingStars() {
      const stars = document.querySelectorAll(".rating-stars i");
      const rating = parseFloat(minRatingSlider.value);
      const starsCount = Math.floor(rating / 2);
      const hasHalfStar = (rating / 2) % 1 !== 0;

      stars.forEach((star, index) => {
        star.className = "far fa-star"; // Resetear todas las estrellas

        if (index < starsCount) {
          star.className = "fas fa-star"; // Estrella completa
        } else if (index === starsCount && hasHalfStar) {
          star.className = "fas fa-star-half-alt"; // Media estrella
        }
      });
    }
  }

  // Configurar botón de aplicar filtros
  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", function() {
      // Guardar valores de filtros
      if (genreSelect) activeFilters.genre = genreSelect.value;
      if (yearSelect) activeFilters.year = yearSelect.value;
      if (minRatingSlider) activeFilters.rating = parseFloat(minRatingSlider.value);
      if (sortSelect) activeFilters.sort = sortSelect.value;

      // Desactivar todos los botones de filtro rápido y chips cuando se aplican filtros avanzados
      document.querySelectorAll(".quick-filter").forEach(btn => {
        btn.classList.remove("active");
      });

      document.querySelectorAll(".category-chip").forEach(chip => {
        chip.classList.remove("active");
      });

      // Cerrar panel de filtros avanzados
      document.getElementById("advancedFilters").classList.remove("active");

      // Cargar series con los nuevos filtros
      loadSeries(true);

      // Notificación al usuario
      showNotification("Filtros aplicados correctamente", "success");
    });
  }

  // Configurar botón de resetear filtros
  if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", function() {
      resetAllFilters(true);

      // Actualizar UI
      if (genreSelect) genreSelect.value = "";
      if (yearSelect) yearSelect.value = "";
      if (minRatingSlider) {
        minRatingSlider.value = 0;
        ratingValue.textContent = "0";
      }
      if (sortSelect) sortSelect.value = "popularity.desc";

      updateRatingStars();

      // Notificación al usuario
      showNotification("Filtros restablecidos", "info");
    });
  }

  // Configurar categorías rápidas (chips)
  if (categoryChips) {
    categoryChips.forEach(chip => {
      chip.addEventListener("click", function() {
        const genreId = this.getAttribute("data-genre");

        // Desactivar chips activos
        document.querySelectorAll(".category-chip.active").forEach(activeChip => {
          activeChip.classList.remove("active");
        });

        // Activar el chip seleccionado
        this.classList.add("active");

        // Aplicar filtro de género
        resetAllFilters(false);

        // Desactivar los botones de filtro rápido cuando se usa un chip
        document.querySelectorAll(".quick-filter").forEach(btn => {
          btn.classList.remove("active");
        });

        activeFilters.genre = genreId;

        // Si tenemos el panel avanzado, actualizar el select
        if (genreSelect) genreSelect.value = genreId;

        // Cargar series con el filtro aplicado
        loadSeries(true);
      });
    });
  }

  // Configurar búsqueda con debounce
  if (searchInput) {
    searchInput.addEventListener("input", debounce(function() {
      activeFilters.search = this.value.trim();
      loadSeries(true);

      // Añadir clase cuando hay búsqueda activa
      if (this.value.trim() !== "") {
        this.parentElement.classList.add("searching");
      } else {
        this.parentElement.classList.remove("searching");
      }
    }, 500));

    // Configurar evento para tecla Enter
    searchInput.addEventListener("keydown", function(e) {
      if (e.key === "Enter") {
        activeFilters.search = this.value.trim();
        loadSeries(true);
      }
    });
  }

  function resetAllFilters(activateAllButton = true) {
    activeFilters = {
      search: activeFilters.search, // Mantener búsqueda activa
      year: "",
      genre: "",
      rating: 0,
      sort: "popularity.desc"
    };

    // Resetear botones de filtro rápido
    if (activateAllButton) {
      document.querySelectorAll(".quick-filter").forEach((btn, index) => {
        btn.classList.remove("active");
        // Activar "Todas" (primer botón) solo cuando se solicita explícitamente
        if (index === 0) btn.classList.add("active");
      });
    }

    // Desactivar chips de categoría
    document.querySelectorAll(".category-chip").forEach(chip => {
      chip.classList.remove("active");
    });
  }
}

// ========================================================
// CONFIGURACIÓN DE VISTAS (GRID/LIST)
// ========================================================
function setupViewToggle() {
  const gridViewBtn = document.getElementById("gridView");
  const listViewBtn = document.getElementById("listView");
  const seriesContainer = document.getElementById("seriesContainer");

  if (!gridViewBtn || !listViewBtn || !seriesContainer) return;

  gridViewBtn.addEventListener("click", function() {
    if (!this.classList.contains("active")) {
      listViewBtn.classList.remove("active");
      this.classList.add("active");

      // Cambiar clase del contenedor
      seriesContainer.classList.remove("list-view");
      seriesContainer.classList.add("grid-view");

      // Animar el cambio
      animateCardsOnViewChange();

      // Guardar preferencia en localStorage
      localStorage.setItem("critflix-view-mode", "grid");
    }
  });

  listViewBtn.addEventListener("click", function() {
    if (!this.classList.contains("active")) {
      gridViewBtn.classList.remove("active");
      this.classList.add("active");

      // Cambiar clase del contenedor
      seriesContainer.classList.remove("grid-view");
      seriesContainer.classList.add("list-view");

      // Animar el cambio
      animateCardsOnViewChange();

      // Guardar preferencia en localStorage
      localStorage.setItem("critflix-view-mode", "list");
    }
  });

  // Cargar preferencia guardada (si existe)
  const savedViewMode = localStorage.getItem("critflix-view-mode");
  if (savedViewMode === "list") {
    listViewBtn.click();
  }
}

function animateCardsOnViewChange() {
  const cards = document.querySelectorAll(".movie-card");

  cards.forEach((card, index) => {
    // Pequeña animación para cada tarjeta con delay progresivo
    card.style.opacity = 0;
    card.style.transform = "translateY(20px)";

    setTimeout(() => {
      card.style.transition = "opacity 0.3s ease, transform 0.4s ease";
      card.style.opacity = 1;
      card.style.transform = "translateY(0)";
    }, 50 * index);
  });
}

// ========================================================
// CONFIGURACIÓN DE MODALES
// ========================================================
function setupModals() {
  // Evitar configurar eventos múltiples veces
  if (trailerEventsConfigured && serieDetailEventsConfigured) return;

  const trailerModal = document.getElementById("trailerModalStatic");
  const serieDetailModal = document.getElementById("serieDetailModalStatic");

  // Configurar cierre de modal de trailer
  if (trailerModal && !trailerEventsConfigured) {
    const closeTrailerBtn = document.getElementById("closeTrailerBtn");

    if (closeTrailerBtn) {
      closeTrailerBtn.addEventListener("click", function() {
        const trailerContainer = document.getElementById("trailerContainerStatic");
        if (trailerContainer) {
          // Limpiar iframe para detener reproducciones
          trailerContainer.innerHTML = `
            <div class="trailer-loading">
              <div class="spinner"></div>
              <span>Cargando trailer...</span>
            </div>
          `;
        }
        trailerModal.style.display = "none";
        document.body.classList.remove("modal-open");
      });

      // Cerrar al hacer clic fuera del contenido
      trailerModal.addEventListener("click", function(e) {
        if (e.target === trailerModal) {
          closeTrailerBtn.click();
        }
      });

      trailerEventsConfigured = true;
    }
  }

  // Configurar cierre de modal de detalles
  if (serieDetailModal && !serieDetailEventsConfigured) {
    const closeDetailBtn = document.getElementById("closeSerieDetailStatic");

    if (closeDetailBtn) {
      closeDetailBtn.addEventListener("click", function() {
        serieDetailModal.style.display = "none";
        document.body.classList.remove("modal-open");
      });

      // Cerrar al hacer clic fuera del contenido
      serieDetailModal.addEventListener("click", function(e) {
        if (e.target === serieDetailModal) {
          closeDetailBtn.click();
        }
      });

      serieDetailEventsConfigured = true;
    }
  }

  // Configurar tecla ESC para cerrar modales
  document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
      closeModals();
    }
  });

  // Configurar botones de trailer en las tarjetas
  document.querySelectorAll(".btn-trailer").forEach(btn => {
    if (!btn.hasAttribute("data-event-configured")) {
      btn.addEventListener("click", function(e) {
        // Prevenir navegación
        e.preventDefault();
        e.stopPropagation();

        const serieId = this.getAttribute("data-id");
        loadSerieTrailer(serieId);
      });

      btn.setAttribute("data-event-configured", "true");
    }
  });

  // Configurar botones de detalles en las tarjetas
  document.querySelectorAll(".btn-details").forEach(btn => {
    if (!btn.hasAttribute("data-event-configured") && btn.tagName.toLowerCase() !== 'a') {
      btn.addEventListener("click", function(e) {
        // Prevenir navegación
        e.preventDefault();
        e.stopPropagation();

        const serieId = this.getAttribute("data-id");
        loadSerieDetails(serieId);
      });

      btn.setAttribute("data-event-configured", "true");
    }
  });
}

function closeModals() {
  // Cerrar modal de trailer
  const trailerModal = document.getElementById("trailerModalStatic");
  if (trailerModal && trailerModal.style.display === "block") {
    const closeBtn = document.getElementById("closeTrailerBtn");
    if (closeBtn) closeBtn.click();
  }

  // Cerrar modal de detalles
  const serieDetailModal = document.getElementById("serieDetailModalStatic");
  if (serieDetailModal && serieDetailModal.style.display === "block") {
    const closeBtn = document.getElementById("closeSerieDetailStatic");
    if (closeBtn) closeBtn.click();
  }

  document.body.classList.remove("modal-open");
}

// ========================================================
// RENDERIZADO DE SERIES
// ========================================================
function renderSeries(series, container) {
  if (!container) return;

  // Crear fragmento para mejorar rendimiento
  const fragment = document.createDocumentFragment();

  // Crear tarjetas de series
  series.forEach(serie => {
    // Obtener información necesaria
    const posterUrl = serie.poster_path ?
      `${IMG_URL}${serie.poster_path}` :
      DEFAULT_POSTER;

    const title = serie.name;
    const year = serie.first_air_date ? new Date(serie.first_air_date).getFullYear() : '';
    const id = serie.id;
    const tmdbId = serie.id;
    const rating = serie.vote_average || 0;

    // Convertir rating a escala de 5 estrellas
    const starRating = rating / 2;

    // Géneros (obtener nombres)
    let genres = '';
    if (serie.genre_ids && serie.genre_ids.length > 0) {
      const genreNames = serie.genre_ids.map(id => getGenreNameById(id));
      genres = genreNames.slice(0, 3).join(', ');
    }

    // Crear elemento
    const card = document.createElement('div');
    card.className = 'movie-card';
    card.setAttribute('data-id', id);
    card.setAttribute('data-title', title.toLowerCase());
    card.setAttribute('data-year', year);
    card.setAttribute('data-rating', rating);
    card.setAttribute('data-genres', genres.toLowerCase());
    card.setAttribute('data-tmdb', tmdbId);

    // Determinar si es nueva (menos de 1 año)
    const isNew = year >= new Date().getFullYear() - 1;

    // Determinar si es bien valorada (8 o más)
    const isTopRated = rating >= 8;

    // Verificar si es favorita
    const favorites = JSON.parse(localStorage.getItem('critflix-favorites-series') || '[]');
    const isFavorite = favorites.includes(id.toString());

    if (isFavorite) {
      card.classList.add('is-favorite');
    }

    card.innerHTML = `
      <div class="movie-poster">
        <img src="${posterUrl}" alt="${title}" loading="lazy">
        <div class="movie-overlay">
          <div class="movie-badges">
            ${isNew ? '<span class="badge new-badge">Nueva</span>' : ''}
            ${isTopRated ? `<span class="badge top-badge"><i class="fas fa-trophy"></i> ${rating.toFixed(1)}</span>` : ''}
          </div>
          <div class="movie-actions">
            <button class="action-btn btn-trailer" data-id="${tmdbId}" aria-label="Ver trailer">
              <i class="fas fa-play"></i>
              <span class="action-btn-tooltip">Ver trailer</span>
            </button>
            <button class="action-btn btn-favorite" data-id="${id}" aria-label="Añadir a favoritos">
              <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>
              <span class="action-btn-tooltip">${isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'}</span>
            </button>
            <button class="action-btn btn-details" data-id="${tmdbId}" aria-label="Ver detalles">
              <i class="fas fa-info-circle"></i>
              <span class="action-btn-tooltip">Ver detalles</span>
            </button>
          </div>
        </div>
      </div>
      <div class="movie-info">
        <h3 class="movie-title">${title}</h3>
        <div class="movie-meta">
          <span class="year">${year}</span>
          <span class="divider">•</span>
          <span class="rating">
            ${generateStarRating(starRating)}
          </span>
        </div>
        <p class="genres">${genres}</p>
        <a href="/serie/${id}" class="btn-more">
          Ver más <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    `;

    // Configurar acciones de la tarjeta
    setupCardActions(card);

    // Añadir al fragmento
    fragment.appendChild(card);
  });

  // Añadir todas las tarjetas al contenedor
  container.appendChild(fragment);

  // Configurar modales después de añadir las nuevas tarjetas
  setupModals();
}

function setupCardActions(card) {
  const favoriteBtn = card.querySelector('.btn-favorite');
  const trailerBtn = card.querySelector('.btn-trailer');
  const detailsBtn = card.querySelector('.btn-details');

  if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const serieId = this.getAttribute('data-id');
      toggleFavorite(serieId, this);
    });
  }

  if (trailerBtn) {
    trailerBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const serieId = this.getAttribute('data-id');
      loadSerieTrailer(serieId);
    });
  }

  if (detailsBtn) {
    detailsBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const serieId = this.getAttribute('data-id');
      loadSerieDetails(serieId);
    });
  }
}

// ========================================================
// GESTIÓN DE MODALES
// ========================================================
async function loadSerieDetails(serieId) {
  showSpinner();
  try {
    const [serieRes, creditsRes, similarRes] = await Promise.all([
      fetch(`${BASE_URL}/tv/${serieId}?api_key=${API_KEY}&language=es-ES`),
      fetch(`${BASE_URL}/tv/${serieId}/credits?api_key=${API_KEY}&language=es-ES`),
      fetch(`${BASE_URL}/tv/${serieId}/similar?api_key=${API_KEY}&language=es-ES&page=1`)
    ]);

    const serieData = await serieRes.json();
    const creditsData = await creditsRes.json();
    const similarData = await similarRes.json();

    renderSerieModalStatic(serieData, creditsData, similarData.results.slice(0, 4));
  } catch (error) {
    console.error("Error cargando detalles de serie:", error);
    const modalDetailContent = document.getElementById("serieDetailContentStatic");
    if (modalDetailContent) {
      modalDetailContent.innerHTML = `
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

function renderSerieModalStatic(serie, credits, similarSeries) {
  const modal = document.getElementById("serieDetailModalStatic");
  const contentContainer = document.getElementById("serieDetailContentStatic");

  if (!modal || !contentContainer) return;

  // Extraer datos relevantes
  const title = serie.name || "Sin título";
  const overview = serie.overview || "No hay descripción disponible.";
  const releaseDate = serie.first_air_date ? formatDate(serie.first_air_date) : "Fecha desconocida";
  const backdropUrl = serie.backdrop_path ?
    `${BACKDROP_URL}${serie.backdrop_path}` :
    DEFAULT_BACKDROP;
  const posterUrl = serie.poster_path ?
    `${IMG_URL}${serie.poster_path}` :
    DEFAULT_POSTER;
  const rating = serie.vote_average || 0;

  // Extraer elenco principal (5 primeros)
  const cast = credits.cast && credits.cast.length > 0 ?
    credits.cast.slice(0, 5) :
    [];

  // Extraer creadores
  const creators = serie.created_by && serie.created_by.length > 0 ?
    serie.created_by.map(c => c.name).join(', ') :
    "Datos no disponibles";

  // Obtener showrunner (puede ser creador o productor ejecutivo)
  const showrunner = credits.crew && credits.crew.length > 0 ?
    credits.crew.find(p => p.job === "Executive Producer" || p.department === "Production") : null;

  // Obtener casa productora principal
  const productionCompany = serie.production_companies && serie.production_companies.length > 0 ?
    serie.production_companies[0].name :
    "No disponible";

  // Obtener redes de emisión
  const networks = serie.networks && serie.networks.length > 0 ?
    serie.networks.map(n => n.name).join(', ') :
    "No disponible";

  // Calcular valoración en estrellas (de 5)
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

  // Estado de la serie
  const status = serie.status === "Ended" ? "Finalizada" :
                serie.status === "Returning Series" ? "En emisión" :
                serie.status === "Canceled" ? "Cancelada" :
                serie.status === "In Production" ? "En producción" :
                serie.status || "Desconocido";

  // Verificar si es favorita
  const favorites = JSON.parse(localStorage.getItem('critflix-favorites-series') || '[]');
  const isFavorite = favorites.includes(serie.id.toString());

  // Construir HTML
  let content = `
    <div class="modal-movie">
      ${backdropUrl ? `<div class="modal-backdrop-image" style="background-image: url('${backdropUrl}')"></div>` : ''}

      <div class="modal-content-wrapper">
        <div class="modal-poster">
          <img src="${posterUrl}" alt="${title}" loading="lazy">
          <div class="poster-actions">
            <button class="btn-primary btn-trailer" data-id="${serie.id}">
              <i class="fas fa-play"></i> Ver Trailer
            </button>
            <button class="btn-favorite ${isFavorite ? 'active' : ''}" data-id="${serie.id}" data-favorited="${isFavorite}">
              <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i> ${isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'}
            </button>
          </div>
        </div>

        <div class="modal-details">
          <h2>${title}</h2>

          <div class="modal-meta">
            <span class="year">${serie.first_air_date ? serie.first_air_date.split("-")[0] : "N/A"}</span>
            <span class="divider">•</span>
            <span class="episodes">${serie.number_of_seasons ? `${serie.number_of_seasons} temporada${serie.number_of_seasons !== 1 ? 's' : ''}` : "N/A"}</span>
            <span class="divider">•</span>
            <span class="rating">
              ${starsHTML}
              <span class="rating-value">${rating.toFixed(1)}</span>
            </span>
          </div>

          <div class="modal-genres">
            ${serie.genres && serie.genres.length > 0 ? serie.genres.map(genre =>
              `<span class="genre-badge">${genre.name}</span>`).join('') : ''}
          </div>

          <div class="modal-section">
            <h3>Sinopsis</h3>
            <p>${overview}</p>
          </div>

          <div class="modal-section">
            <h3>Reparto</h3>
            <div class="cast-list">
              ${creators ? `<div class="cast-item director">
                <span class="role">Creador${serie.created_by && serie.created_by.length > 1 ? 'es' : ''}:</span>
                <span class="name">${creators}</span>
              </div>` : ''}

              ${showrunner ? `<div class="cast-item director">
                <span class="role">Showrunner:</span>
                <span class="name">${showrunner.name}</span>
              </div>` : ''}

              ${cast.map(actor => `
                <div class="cast-item">
                  <span class="name">${actor.name}</span>
                  <span class="character">${actor.character || ''}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="modal-info-grid">
            <div class="info-item">
              <i class="fas fa-calendar-alt"></i>
              <div>
                <span class="label">Fecha de estreno</span>
                <span class="value">${releaseDate}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-signal"></i>
              <div>
                <span class="label">Estado</span>
                <span class="value">${status}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-tv"></i>
              <div>
                <span class="label">Episodios</span>
                <span class="value">${serie.number_of_episodes || 'N/A'}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-broadcast-tower"></i>
              <div>
                <span class="label">Red</span>
                <span class="value">${networks}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-building"></i>
              <div>
                <span class="label">Productora</span>
                <span class="value">${productionCompany}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-globe"></i>
              <div>
                <span class="label">Idioma original</span>
                <span class="value">${serie.original_language === 'en' ? 'Inglés' : serie.original_language === 'es' ? 'Español' : serie.original_language}</span>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <a href="/serie/${serie.id}" class="btn-primary btn-ver-mas">
              <i class="fas fa-external-link-alt"></i> Ver página completa
            </a>
          </div>
        </div>
      </div>

      ${similarSeries && similarSeries.length > 0 ? `
        <div class="similar-movies">
          <h3>Series similares</h3>
          <div class="similar-grid">
            ${similarSeries.map(similarSerie => `
              <div class="similar-movie">
                <img src="${similarSerie.poster_path ? IMG_URL + similarSerie.poster_path : DEFAULT_POSTER}" alt="${similarSerie.name}">
                <div class="similar-info">
                  <h4>${similarSerie.name}</h4>
                  <p>${similarSerie.first_air_date ? similarSerie.first_air_date.split('-')[0] : 'N/A'} • ${similarSerie.vote_average.toFixed(1)} <i class="fas fa-star"></i></p>
                  <button class="btn-details" data-id="${similarSerie.id}">Ver detalles</button>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      ` : ''}
    </div>
  `;

  // Actualizar contenido y mostrar modal
  contentContainer.innerHTML = content;
  modal.style.display = "block";
  document.body.classList.add("modal-open");

  // Configurar eventos para los nuevos botones
  const trailerBtn = contentContainer.querySelector(".btn-trailer");
  if (trailerBtn) {
    trailerBtn.addEventListener("click", function() {
      loadSerieTrailer(serie.id);
    });
  }

  const closeBtn = contentContainer.querySelector(".btn-close-detail");
  if (closeBtn) {
    closeBtn.addEventListener("click", function() {
      modal.style.display = "none";
      document.body.classList.remove("modal-open");
    });
  }

  const favoriteBtn = contentContainer.querySelector(".btn-favorite");
  if (favoriteBtn) {
    // Verificar si ya es favorita
    const favorites = JSON.parse(localStorage.getItem("critflix-favorites-series") || "[]");
    const isFavorite = favorites.includes(serie.id.toString());

    // Actualizar botón según estado
    if (isFavorite) {
      favoriteBtn.innerHTML = '<i class="fas fa-heart"></i> Quitar de favoritos';
      favoriteBtn.classList.add("favorited");
    }

    // Configurar evento
    favoriteBtn.addEventListener("click", function() {
      toggleFavorite(serie.id, this);
    });
  }

  // Configurar los botones de detalles de series similares
  const detailBtns = contentContainer.querySelectorAll(".similar-info .btn-details");
  if (detailBtns.length > 0) {
    detailBtns.forEach(btn => {
      btn.addEventListener("click", function() {
        const serieId = this.getAttribute("data-id");
        // Cerrar este modal primero
        modal.style.display = "none";
        // Cargar los detalles de la nueva serie
        loadSerieDetails(serieId);
      });
    });
  }

  // Configurar cierre general al hacer clic fuera del contenido
  modal.addEventListener("click", function(e) {
    if (e.target === modal) {
      modal.style.display = "none";
      document.body.classList.remove("modal-open");
    }
  });
}

async function loadSerieTrailer(serieId) {
  const trailerModal = document.getElementById("trailerModalStatic");
  const trailerContainer = document.getElementById("trailerContainerStatic");

  if (!trailerModal || !trailerContainer) return;

  // Mostrar modal con spinner
  trailerModal.style.display = "block";
  document.body.classList.add("modal-open");

  trailerContainer.innerHTML = `
    <div class="trailer-loading">
      <div class="spinner"></div>
      <span>Cargando trailer...</span>
    </div>
  `;

  try {
    const response = await fetch(`${BASE_URL}/tv/${serieId}/videos?api_key=${API_KEY}&language=es-ES`);
    const data = await response.json();

    // Si no hay videos en español, buscar en inglés
    let trailerKey = null;

    if (data.results && data.results.length > 0) {
      // Buscar trailer u otros tipos de videos
      const trailer = data.results.find(v => v.type === "Trailer");
      const teaser = data.results.find(v => v.type === "Teaser");

      trailerKey = trailer ? trailer.key : (teaser ? teaser.key : null);
    }

    // Si no se encontró trailer en español, buscar en inglés
    if (!trailerKey) {
      const enResponse = await fetch(`${BASE_URL}/tv/${serieId}/videos?api_key=${API_KEY}&language=en-US`);
      const enData = await enResponse.json();

      if (enData.results && enData.results.length > 0) {
        const trailer = enData.results.find(v => v.type === "Trailer");
        const teaser = enData.results.find(v => v.type === "Teaser");

        trailerKey = trailer ? trailer.key : (teaser ? teaser.key : null);
      }
    }

    // Mostrar trailer o mensaje de error
    if (trailerKey) {
      renderTrailerModal(trailerKey);
    } else {
      trailerContainer.innerHTML = `
        <div class="trailer-error">
          <i class="fas fa-exclamation-circle"></i>
          <p>No se encontró un trailer para esta serie.</p>
        </div>
      `;
    }
  } catch (error) {
    console.error("Error cargando trailer:", error);
    trailerContainer.innerHTML = `
      <div class="trailer-error">
        <i class="fas fa-exclamation-circle"></i>
        <p>Ocurrió un error al cargar el trailer. Por favor, inténtalo de nuevo más tarde.</p>
      </div>
    `;
  }
}

function renderTrailerModal(trailerKey) {
  const trailerContainer = document.getElementById("trailerContainerStatic");

  if (!trailerContainer) return;

  // Insertar iframe
  trailerContainer.innerHTML = `
    <iframe
      src="https://www.youtube.com/embed/${trailerKey}?autoplay=1&mute=0"
      title="YouTube video player"
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      allowfullscreen
      style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
    ></iframe>
  `;

  // Configurar eventos para cerrar el modal con Esc
  const handleEsc = (e) => {
    if (e.key === "Escape") {
      closeModals();
      document.removeEventListener("keydown", handleEsc);
    }
  };

  document.addEventListener("keydown", handleEsc);
}

// ========================================================
// CARGA DE SERIES
// ========================================================
async function loadSeries(reset = false) {
  if (isLoading) return;
  isLoading = true;

  const loadMoreBtn = document.getElementById('loadMoreBtn');
  if (loadMoreBtn) {
    loadMoreBtn.disabled = true;
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
  }

  try {
    // Si es un reset, resetear el contenedor y la página
    if (reset) {
      allSeriesPage = 1;

      const container = document.getElementById('seriesContainer');
      if (container) {
        container.innerHTML = '';

        // Mostrar spinner mientras carga
        container.innerHTML = `
          <div class="loading-placeholder">
            <div class="spinner"></div>
            <p>Cargando series...</p>
          </div>
        `;
      }
    }

    // Construir URL de la API con filtros activos y limitar a 20 resultados por página
    let apiUrl = `${BASE_URL}/discover/tv?api_key=${API_KEY}&language=es-ES&page=${allSeriesPage}`;

    // Aplicar filtros
    if (activeFilters.genre) {
      apiUrl += `&with_genres=${activeFilters.genre}`;
    }

    if (activeFilters.year) {
      apiUrl += `&first_air_date_year=${activeFilters.year}`;
    }

    if (activeFilters.rating > 0) {
      apiUrl += `&vote_average.gte=${activeFilters.rating}`;
    }

    if (activeFilters.sort) {
      apiUrl += `&sort_by=${activeFilters.sort}`;
    }

    // Si hay término de búsqueda, usar search en lugar de discover
    if (activeFilters.search && activeFilters.search.trim() !== '') {
      apiUrl = `${BASE_URL}/search/tv?api_key=${API_KEY}&language=es-ES&query=${encodeURIComponent(activeFilters.search)}&page=${allSeriesPage}`;
    }

    // Hacer petición a la API
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error('Error al cargar series');
    }

    const data = await response.json();

    // Remover el placeholder de carga
    const container = document.getElementById('seriesContainer');
    if (container && reset) {
      container.innerHTML = '';
    }

    // Renderizar las series
    renderSeries(data.results, container);

    // Actualizar contador de resultados
    updateResultCounter(data.total_results);

    // Habilitar o deshabilitar botón según haya más páginas
    if (loadMoreBtn) {
      if (allSeriesPage < data.total_pages) {
        loadMoreBtn.disabled = false;
        loadMoreBtn.innerHTML = 'Cargar más series';
        loadMoreBtn.style.display = 'block';
        allSeriesPage++;
      } else {
        loadMoreBtn.style.display = 'none';
      }
    }

    // Emitir evento para notificar que se han cargado nuevas tarjetas
    document.dispatchEvent(new CustomEvent('cardsLoaded'));

  } catch (error) {
    console.error('Error cargando series:', error);
    showNotification('Error al cargar series. Intenta de nuevo más tarde.', 'error');

    const container = document.getElementById('seriesContainer');
    if (container && reset) {
      container.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <h3>No se pudieron cargar las series</h3>
          <p>Ha ocurrido un error. Por favor, intenta de nuevo más tarde.</p>
          <button class="btn-link" onclick="loadSeries(true)">
            <i class="fas fa-redo"></i> Reintentar
          </button>
        </div>
      `;
    }

  } finally {
    isLoading = false;

    if (loadMoreBtn && loadMoreBtn.style.display !== 'none') {
      loadMoreBtn.disabled = false;
      loadMoreBtn.innerHTML = 'Cargar más series';
    }
  }
}

function updateResultCounter(total) {
  const counter = document.getElementById('resultCount');
  if (counter) {
    counter.textContent = total;
  }
}

function generateStarRating(rating) {
  let starsHtml = '';

  for (let i = 1; i <= 5; i++) {
    if (i <= Math.floor(rating)) {
      starsHtml += '<i class="fas fa-star"></i>';
    } else if (i - 0.5 <= rating) {
      starsHtml += '<i class="fas fa-star-half-alt"></i>';
    } else {
      starsHtml += '<i class="far fa-star"></i>';
    }
  }

  return starsHtml;
}

// ========================================================
// EFECTOS DE SCROLL Y ANIMACIONES
// ========================================================
function setupScrollEffects() {
  window.addEventListener('scroll', handleScroll);

  // Animación de entrada para las tarjetas
  const animateOnScroll = debounce(() => {
    const cards = document.querySelectorAll('.movie-card:not(.animated)');

    cards.forEach(card => {
      const cardTop = card.getBoundingClientRect().top;
      const cardBottom = card.getBoundingClientRect().bottom;

      // Si la tarjeta es visible en el viewport
      if (cardTop < window.innerHeight - 100 && cardBottom > 0) {
        card.classList.add('animated');

        // Animación de entrada
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
          card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 50);
      }
    });
  }, 100);

  window.addEventListener('scroll', animateOnScroll);

  // Animación de entrada para las tarjetas iniciales
  setTimeout(() => {
    const animateCards = () => {
      const cards = document.querySelectorAll('.movie-card:not(.animated)');

      cards.forEach((card, index) => {
        setTimeout(() => {
          card.classList.add('animated');
          card.style.opacity = '0';
          card.style.transform = 'translateY(20px)';

          setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 50);
        }, index * 50);
      });
    };

    animateCards();
  }, 500);
}

// ========================================================
// GESTIÓN DE FAVORITOS
// ========================================================
function setupFavorites() {
  // Cargar favoritos al iniciar
  updateFavoriteButtons();
}

function toggleFavorite(serieId, button) {
  // Obtener lista actual de favoritos
  const favorites = JSON.parse(localStorage.getItem('critflix-favorites-series') || '[]');

  // Verificar si ya está en favoritos
  const index = favorites.indexOf(serieId.toString());

  if (index === -1) {
    // Añadir a favoritos si no existe
    favorites.push(serieId.toString());
    showNotification('Serie añadida a favoritos', 'success');

    // Actualizar botón
    if (button) {
      if (button.querySelector('i')) {
        button.querySelector('i').className = 'fas fa-heart';
      }
      if (button.querySelector('.action-btn-tooltip')) {
        button.querySelector('.action-btn-tooltip').textContent = 'Quitar de favoritos';
      }
      button.classList.add('favorited');
    }

    // Añadir clase a la tarjeta
    const card = document.querySelector(`.movie-card[data-id="${serieId}"]`);
    if (card) {
      card.classList.add('is-favorite');
    }

  } else {
    // Quitar de favoritos si existe
    favorites.splice(index, 1);
    showNotification('Serie eliminada de favoritos', 'info');

    // Actualizar botón
    if (button) {
      if (button.querySelector('i')) {
        button.querySelector('i').className = 'far fa-heart';
      }
      if (button.querySelector('.action-btn-tooltip')) {
        button.querySelector('.action-btn-tooltip').textContent = 'Añadir a favoritos';
      }
      button.classList.remove('favorited');
    }

    // Quitar clase de la tarjeta
    const card = document.querySelector(`.movie-card[data-id="${serieId}"]`);
    if (card) {
      card.classList.remove('is-favorite');
    }
  }

  // Guardar cambios en localStorage
  localStorage.setItem('critflix-favorites-series', JSON.stringify(favorites));

  // Emitir evento que puede ser escuchado por otros componentes
  document.dispatchEvent(new CustomEvent('favoritesChanged', {
    detail: { serieId, isFavorite: index === -1 }
  }));
}

function updateFavoriteButtons() {
  const favorites = JSON.parse(localStorage.getItem('critflix-favorites-series') || '[]');

  // Actualizar botones en tarjetas
  document.querySelectorAll('.btn-favorite').forEach(button => {
    const serieId = button.getAttribute('data-id');

    if (favorites.includes(serieId)) {
      button.querySelector('i').className = 'fas fa-heart';
      if (button.querySelector('.action-btn-tooltip')) {
        button.querySelector('.action-btn-tooltip').textContent = 'Quitar de favoritos';
      }
      button.classList.add('favorited');

      // Actualizar clase en la tarjeta padre
      const card = button.closest('.movie-card');
      if (card) {
        card.classList.add('is-favorite');
      }
    } else {
      button.querySelector('i').className = 'far fa-heart';
      if (button.querySelector('.action-btn-tooltip')) {
        button.querySelector('.action-btn-tooltip').textContent = 'Añadir a favoritos';
      }
      button.classList.remove('favorited');

      // Actualizar clase en la tarjeta padre
      const card = button.closest('.movie-card');
      if (card) {
        card.classList.remove('is-favorite');
      }
    }
  });
}

// ========================================================
// SISTEMA DE NOTIFICACIONES
// ========================================================
function showNotification(message, type = "info") {
  let container = document.getElementById("notificationContainer");

  if (!container) {
    container = createNotificationContainer();
  }

  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;

  // Icono según tipo
  const iconClass = type === "success" ? "fas fa-check-circle" :
                    type === "error" ? "fas fa-exclamation-circle" :
                    type === "warning" ? "fas fa-exclamation-triangle" :
                    "fas fa-info-circle";

  notification.innerHTML = `
    <div class="notification-icon">
      <i class="${iconClass}"></i>
    </div>
    <div class="notification-content">
      <p>${message}</p>
    </div>
    <button class="notification-close">
      <i class="fas fa-times"></i>
    </button>
  `;

  // Añadir al contenedor
  container.appendChild(notification);

  // Configurar cierre automático
  setTimeout(() => {
    notification.classList.add("hide");
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 5000);

  // Configurar cierre manual
  notification.querySelector(".notification-close").addEventListener("click", () => {
    notification.classList.add("hide");
    setTimeout(() => {
      notification.remove();
    }, 300);
  });

  return notification;
}

function createNotificationContainer() {
  const container = document.createElement("div");
  container.id = "notificationContainer";
  container.className = "notification-container";
  document.body.appendChild(container);
  return container;
}

// ========================================================
// INICIALIZACIÓN Y CONFIGURACIÓN INICIAL
// ========================================================
document.addEventListener("DOMContentLoaded", function() {
  // Ocultar precargador
  const pageLoader = document.getElementById("pageLoader");
  if (pageLoader) {
    pageLoader.classList.add("loaded");
    setTimeout(() => {
      pageLoader.style.display = "none";
    }, 500);
  }

  // Configurar scroll y UI
  setupScrollToTop();
  setupViewToggle();
  setupFilters();
  setupModals();
  setupFavorites();
  setupScrollEffects();

  // Cargar datos iniciales
  loadFeaturedSeries();
  loadSeries(true);

  // Configurar botón "cargar más"
  const loadMoreBtn = document.getElementById("loadMoreBtn");
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", function() {
      loadSeries();
    });
  }

  // Configurar botón "descubrir series"
  const discoverBtn = document.getElementById("discoverSeries");
  if (discoverBtn) {
    discoverBtn.addEventListener("click", function() {
      const seriesSection = document.getElementById("seriesSection");
      if (seriesSection) {
        window.scrollTo({
          top: seriesSection.offsetTop - 100,
          behavior: "smooth"
        });
      }
    });
  }

  // Configurar botón "destacadas"
  const featuredBtn = document.getElementById("scrollToFeatured");
  if (featuredBtn) {
    featuredBtn.addEventListener("click", function() {
      const featuredSection = document.getElementById("featuredSection");
      if (featuredSection) {
        window.scrollTo({
          top: featuredSection.offsetTop - 100,
          behavior: "smooth"
        });
      }
    });
  }

  // Manejar errores global para evitar fallos en la página
  window.addEventListener('error', function(e) {
    console.error('Error en la página:', e.message);
    showNotification('Ha ocurrido un error inesperado. Por favor, recarga la página.', 'error');
  });

  // Capturar errores en promesas no manejadas
  window.addEventListener('unhandledrejection', function(e) {
    console.error('Promesa no manejada:', e.reason);
    showNotification('Ha ocurrido un error de conexión. Verifica tu conexión a internet.', 'error');
  });
});
