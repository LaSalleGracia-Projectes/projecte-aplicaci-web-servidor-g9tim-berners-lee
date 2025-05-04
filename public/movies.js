// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f"; // Reemplazar con tu API key real
const IMG_URL = "https://image.tmdb.org/t/p/w500";
const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
const DEFAULT_POSTER = "https://via.placeholder.com/500x750/121212/00ff3c?text=Sin+Imagen";
const DEFAULT_BACKDROP = "https://via.placeholder.com/1280x720/121212/00ff3c?text=Sin+Imagen+de+Fondo";

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
let searchTimeout;
let lastScrollPosition = 0;

// Variable para controlar si los manejadores de eventos ya se configuraron
let trailerEventsConfigured = false;
let movieDetailEventsConfigured = false;

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
    showNotification("Error al cargar películas destacadas. Por favor, inténtalo de nuevo más tarde.", "error");
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
      DEFAULT_BACKDROP;

    const posterUrl = movie.poster_path ?
      `${IMG_URL}${movie.poster_path}` :
      DEFAULT_POSTER;

    const slide = document.createElement("div");
    slide.className = "swiper-slide";

    slide.innerHTML = `
      <div class="featured-slide">
        <div class="featured-overlay" style="background-image: url('${backdropUrl}')"></div>
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
            <p class="featured-overview">${movie.overview || 'No hay descripción disponible para esta película.'}</p>
            <div class="featured-actions">
              <button class="btn-primary btn-trailer" data-id="${movie.id}" aria-label="Ver trailer de ${movie.title}">
                <i class="fas fa-play"></i> Ver Trailer
              </button>
              <button class="btn-secondary btn-details" data-id="${movie.id}" aria-label="Ver más información de ${movie.title}">
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
  // Destruir instancia previa si existe
  if (window.featuredSwiper) {
    window.featuredSwiper.destroy(true, true);
  }

  // Inicializar Swiper con mejoras de accesibilidad
  window.featuredSwiper = new Swiper('.featured-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      renderBullet: function (index, className) {
        return `<span class="${className}" aria-label="Ir a la película destacada ${index + 1}" role="button" tabindex="0"></span>`;
      }
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    a11y: {
      prevSlideMessage: 'Película anterior',
      nextSlideMessage: 'Película siguiente',
      firstSlideMessage: 'Primera película',
      lastSlideMessage: 'Última película',
      paginationBulletMessage: 'Ir a la película {{index}}'
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true
    }
  });
}

// ========================================================
// FILTRADO DE PELÍCULAS
// ========================================================
function setupFilters() {
  // Referencia a los elementos del DOM
  const advancedFilterToggle = document.getElementById('advancedFilterToggle');
  const advancedFilters = document.getElementById('advancedFilters');
  const closeFilters = document.getElementById('closeFilters');
  const applyFilters = document.getElementById('applyFilters');
  const resetFilters = document.getElementById('resetFilters');

  // Selectores y sliders
  const genreSelect = document.getElementById('genreSelect');
  const yearSelect = document.getElementById('yearSelect');
  const minRatingSlider = document.getElementById('minRating');
  const sortSelect = document.getElementById('sortSelect');
  const ratingValue = document.getElementById('ratingValue');
  const categoryChips = document.querySelectorAll('.category-chip');
  const quickFilters = document.querySelectorAll('.quick-filter');

  // Variable para almacenar los filtros activos
  activeFilters = {
    genre: '',
    year: '',
    rating: 0,
    sort: 'popularity.desc',
    search: ''
  };

  // Apertura y cierre del panel de filtros avanzados
  if (advancedFilterToggle) {
    advancedFilterToggle.addEventListener('click', () => {
      advancedFilters.classList.add('active');
    });
  }

  if (closeFilters) {
    closeFilters.addEventListener('click', () => {
      advancedFilters.classList.remove('active');
    });

    // También cerrar al hacer clic fuera
    document.addEventListener('click', (e) => {
      if (advancedFilters.classList.contains('active') &&
          !advancedFilters.contains(e.target) &&
          e.target !== advancedFilterToggle &&
          !advancedFilterToggle.contains(e.target)) {
        advancedFilters.classList.remove('active');
      }
    });
  }

  // Controladores para filtros rápidos
  quickFilters.forEach(filter => {
    filter.addEventListener('click', () => {
      // Quitar clase active de todos los filtros
      quickFilters.forEach(f => f.classList.remove('active'));

      // Añadir clase active al filtro seleccionado
      filter.classList.add('active');
      filter.classList.add('animated');

      // Quitar la clase animated después de la animación
      setTimeout(() => {
        filter.classList.remove('animated');
      }, 600);

      const filterType = filter.dataset.filter;

      // Aplicar filtro según el tipo
      switch(filterType) {
        case 'all':
          resetFilters();
          break;
        case 'trending':
          activeFilters.sort = 'popularity.desc';
          activeFilters.genre = '';
          activeFilters.year = '';
          activeFilters.rating = 0;
          break;
        case 'toprated':
          activeFilters.sort = 'vote_average.desc';
          activeFilters.rating = 7;
          break;
        case 'new':
          activeFilters.sort = 'release_date.desc';
          activeFilters.year = new Date().getFullYear().toString();
          break;
      }

      // Cargar películas con los nuevos filtros
      loadMovies(true);
    });
  });

  // Mostrar valor actual del slider de rating
  if (minRatingSlider && ratingValue) {
    minRatingSlider.addEventListener('input', () => {
      const value = parseFloat(minRatingSlider.value);
      ratingValue.textContent = value;
      updateRatingStars();
    });
  }

  // Actualizar estrellas según el rating seleccionado
  function updateRatingStars() {
    const rating = parseFloat(minRatingSlider.value);
    const stars = document.querySelectorAll('.rating-filter .rating-stars i');

    stars.forEach((star, index) => {
      // Convertir escala 0-10 a 0-5 estrellas
      const starIndex = index + 1;
      const starValue = starIndex * 2;

      if (rating >= starValue) {
        star.className = 'fas fa-star'; // Estrella completa
      } else if (rating >= starValue - 1) {
        star.className = 'fas fa-star-half-alt'; // Media estrella
      } else {
        star.className = 'far fa-star'; // Estrella vacía
      }
    });
  }

  // Aplicar filtros
  if (applyFilters) {
    applyFilters.addEventListener('click', () => {
      // Recoger valores de los filtros
      if (genreSelect) activeFilters.genre = genreSelect.value;
      if (yearSelect) activeFilters.year = yearSelect.value;
      if (minRatingSlider) activeFilters.rating = parseFloat(minRatingSlider.value);
      if (sortSelect) activeFilters.sort = sortSelect.value;

      // Cargar películas con los nuevos filtros
      loadMovies(true);

      // Cerrar panel de filtros
      advancedFilters.classList.remove('active');

      // Mostrar notificación
      showNotification('Filtros aplicados', 'success');
    });
  }

  // Restablecer filtros
  if (resetFilters) {
    resetFilters.addEventListener('click', () => {
      resetAllFilters();
    });
  }

  // Categorías rápidas (chips)
  categoryChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const genreId = chip.dataset.genre;

      // Si ya está seleccionado, deseleccionar
      if (chip.classList.contains('active')) {
        chip.classList.remove('active');
        activeFilters.genre = '';
      } else {
        // Quitar active de todos los chips
        categoryChips.forEach(c => c.classList.remove('active'));

        // Añadir active al seleccionado
        chip.classList.add('active');
      activeFilters.genre = genreId;
      }

      // Actualizar select de género en el panel de filtros
      if (genreSelect) genreSelect.value = activeFilters.genre;

      // Cargar películas con el nuevo filtro
      loadMovies(true);
    });
  });

  // Función para restablecer todos los filtros
  function resetAllFilters() {
    activeFilters = {
      genre: '',
      year: '',
      rating: 0,
      sort: 'popularity.desc',
      search: activeFilters.search // Mantener búsqueda
    };

    // Restablecer controles de la interfaz
  if (genreSelect) genreSelect.value = '';
  if (yearSelect) yearSelect.value = '';
    if (minRatingSlider) {
      minRatingSlider.value = 0;
    updateRatingStars();
      if (ratingValue) ratingValue.textContent = '0';
  }
  if (sortSelect) sortSelect.value = 'popularity.desc';

    // Quitar active de todos los chips
    categoryChips.forEach(c => c.classList.remove('active'));

    // Seleccionar el filtro "Todas"
    quickFilters.forEach(f => {
      f.classList.remove('active');
      if (f.dataset.filter === 'all') {
        f.classList.add('active');
      }
    });

    // Cargar películas con los filtros restablecidos
  loadMovies(true);

  // Mostrar notificación
    showNotification('Filtros restablecidos', 'info');
  }

  // Búsqueda avanzada
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
      activeFilters.search = searchInput.value.trim();

      if (activeFilters.search.length > 2 || activeFilters.search.length === 0) {
        loadMovies(true);
      }
    }, 500));
  }

  // Inicializar estrellas
  updateRatingStars();
}

// ========================================================
// TOGGLES DE VISTA
// ========================================================
function setupViewToggle() {
  const gridViewBtn = document.getElementById('gridView');
  const listViewBtn = document.getElementById('listView');
  const moviesContainer = document.getElementById('moviesContainer');

  if (!gridViewBtn || !listViewBtn || !moviesContainer) return;

  // Cargar preferencia guardada
  const savedView = localStorage.getItem('critflix-view-preference') || 'grid';

  if (savedView === 'list') {
    moviesContainer.classList.remove('grid-view');
    moviesContainer.classList.add('list-view');
    gridViewBtn.classList.remove('active');
    listViewBtn.classList.add('active');
  } else {
    moviesContainer.classList.add('grid-view');
    moviesContainer.classList.remove('list-view');
    gridViewBtn.classList.add('active');
    listViewBtn.classList.remove('active');
  }

  // Event listeners
  gridViewBtn.addEventListener('click', () => {
    moviesContainer.classList.add('grid-view');
    moviesContainer.classList.remove('list-view');
    gridViewBtn.classList.add('active');
    listViewBtn.classList.remove('active');
    localStorage.setItem('critflix-view-preference', 'grid');

    // Aplicar animación a las tarjetas
    animateCardsOnViewChange();
  });

  listViewBtn.addEventListener('click', () => {
    moviesContainer.classList.remove('grid-view');
    moviesContainer.classList.add('list-view');
    gridViewBtn.classList.remove('active');
    listViewBtn.classList.add('active');
    localStorage.setItem('critflix-view-preference', 'list');

    // Aplicar animación a las tarjetas
    animateCardsOnViewChange();
  });
}

// Función para animar tarjetas al cambiar de vista
function animateCardsOnViewChange() {
  const cards = document.querySelectorAll('.movie-card');
  cards.forEach((card) => {
    // Primero escondemos
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';

    // Luego mostramos con un retraso pequeño
    setTimeout(() => {
      card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 50);
  });
}

// ========================================================
// CONFIGURACIÓN DE MODALES Y ACCIONES DE TARJETAS
// ========================================================
function setupModals() {
  // Referencias a modales estáticos
  const trailerModalStatic = document.getElementById('trailerModalStatic');
  const movieDetailModalStatic = document.getElementById('movieDetailModalStatic');

  // Asegurarse de que los modales estén cerrados al inicio
  if (trailerModalStatic) {
    trailerModalStatic.style.display = 'none';
  }

  if (movieDetailModalStatic) {
    movieDetailModalStatic.style.display = 'none';
  }

  // Configurar modal estático de trailer
  if (trailerModalStatic && !trailerEventsConfigured) {
    const closeTrailerBtn = document.getElementById('closeTrailerBtn');
    if (closeTrailerBtn) {
      closeTrailerBtn.addEventListener('click', () => {
        const trailerContainerStatic = document.getElementById('trailerContainerStatic');
        if (trailerContainerStatic) {
          trailerContainerStatic.innerHTML = '';
        }
        trailerModalStatic.style.display = 'none';
        document.body.style.overflow = '';
      });
    }

  // Cerrar al hacer clic en el backdrop
    trailerModalStatic.addEventListener('click', (e) => {
      if (e.target === trailerModalStatic) {
        const trailerContainerStatic = document.getElementById('trailerContainerStatic');
        if (trailerContainerStatic) {
          trailerContainerStatic.innerHTML = '';
        }
        trailerModalStatic.style.display = 'none';
        document.body.style.overflow = '';
      }
    });

    // Marcar como configurado
    trailerEventsConfigured = true;
  }

  // Configurar modal estático de detalles
  if (movieDetailModalStatic && !movieDetailEventsConfigured) {
    const closeMovieDetailBtn = document.getElementById('closeMovieDetailStatic');
    if (closeMovieDetailBtn) {
      closeMovieDetailBtn.addEventListener('click', () => {
        movieDetailModalStatic.style.display = 'none';
        document.body.style.overflow = '';
      });
    }

    // Cerrar al hacer clic en el backdrop
    movieDetailModalStatic.addEventListener('click', (e) => {
      if (e.target === movieDetailModalStatic) {
        movieDetailModalStatic.style.display = 'none';
        document.body.style.overflow = '';
      }
    });

    // Marcar como configurado
    movieDetailEventsConfigured = true;
  }

  // Cerrar todos los modales con ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModals();
    }
  });

  // Configurar eventos para los botones de trailer en el documento
  document.addEventListener('click', (e) => {
    const trailerBtn = e.target.closest('.btn-trailer');
    if (trailerBtn) {
      e.preventDefault();
      e.stopPropagation();
      const movieId = trailerBtn.getAttribute('data-id');
      if (movieId) {
        loadMovieTrailer(movieId);

        // Efecto visual
        trailerBtn.classList.add('pulse-effect');
        setTimeout(() => {
          trailerBtn.classList.remove('pulse-effect');
        }, 700);
      }
    }
  });

  // Configurar eventos para los botones de detalles en el documento
  document.addEventListener('click', (e) => {
    const detailsBtn = e.target.closest('.btn-details:not(a)');
    if (detailsBtn) {
      e.preventDefault();
      e.stopPropagation();
      const movieId = detailsBtn.getAttribute('data-id');
      if (movieId) {
        loadMovieDetails(movieId);

        // Efecto visual
        detailsBtn.classList.add('pulse-effect');
        setTimeout(() => {
          detailsBtn.classList.remove('pulse-effect');
        }, 700);
      }
    }
  });
}

function closeModals() {
  // Cerrar todos los modales
  document.querySelectorAll('.modal').forEach(modal => {
    modal.classList.remove('active');
  });

  // Cerrar modales estáticos también
  const trailerModalStatic = document.getElementById('trailerModalStatic');
  const movieDetailModalStatic = document.getElementById('movieDetailModalStatic');

  if (trailerModalStatic) {
    trailerModalStatic.style.display = 'none';
    const trailerContainerStatic = document.getElementById('trailerContainerStatic');
    if (trailerContainerStatic) {
      trailerContainerStatic.innerHTML = '';
    }
  }

  if (movieDetailModalStatic) {
    movieDetailModalStatic.style.display = 'none';
  }

  // Permitir scroll en el body de nuevo
  document.body.style.overflow = '';
}

// ========================================================
// RENDERIZADO DE PELÍCULAS
// ========================================================
function renderMovies(movies, container) {
  if (!container) return;

  // Crear fragmento para mejorar rendimiento
  const fragment = document.createDocumentFragment();

  // Crear tarjetas de películas
  movies.forEach(movie => {
    // Obtener información necesaria
    const posterUrl = movie.poster_path ?
      `${IMG_URL}${movie.poster_path}` :
      DEFAULT_POSTER;

    const title = movie.title;
    const year = movie.release_date ? new Date(movie.release_date).getFullYear() : '';
    const id = movie.id;
    const tmdbId = movie.id;
    const rating = movie.vote_average || 0;

    // Convertir rating a escala de 5 estrellas
    const starRating = rating / 2;

    // Géneros (obtener nombres)
    let genres = '';
    if (movie.genre_ids && movie.genre_ids.length > 0) {
      const genreNames = movie.genre_ids.map(id => getGenreNameById(id));
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
    const favorites = JSON.parse(localStorage.getItem('critflix-favorites') || '[]');
    const isFavorite = favorites.includes(id.toString());

    if (isFavorite) {
      card.classList.add('is-favorite');
    }

    // HTML de la tarjeta
    card.innerHTML = `
      <div class="movie-poster">
        <img src="${posterUrl}" alt="${title}" loading="lazy">
        <div class="movie-overlay">
          <div class="movie-badges">
            ${isNew ? '<span class="badge new-badge">Nuevo</span>' : ''}
            ${isTopRated ? `<span class="badge top-badge"><i class="fas fa-trophy"></i> ${rating.toFixed(1)}</span>` : ''}
          </div>
          <div class="movie-actions">
            <button class="action-btn btn-trailer" data-id="${tmdbId}" aria-label="Ver trailer de ${title}">
              <i class="fas fa-play"></i>
            </button>
            <button class="action-btn btn-favorite ${isFavorite ? 'active' : ''}" data-id="${id}" data-favorited="${isFavorite}" aria-label="${isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'}">
              <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>
            </button>
            <button class="action-btn btn-details" data-id="${id}" aria-label="Ver detalles de ${title}">
              <i class="fas fa-info-circle"></i>
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
        <a href="javascript:void(0)" class="btn-more" data-id="${id}">
          Ver más <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    `;

    // Configurar eventos para los botones de acción
    setupCardActions(card);

    // Agregar al fragmento
    fragment.appendChild(card);
  });

  // Agregar fragmento al contenedor
  container.appendChild(fragment);

  // Aplicar animaciones a las nuevas tarjetas
  const newCards = container.querySelectorAll('.movie-card:not(.in-view)');
  newCards.forEach((card, index) => {
    card.style.animationDelay = `${0.05 * (index % 10)}s`;
    card.classList.add('in-view');
  });
}

// Función para configurar eventos en los botones de acción de cada tarjeta
function setupCardActions(card) {
  // Botón de favoritos
  const favoriteBtn = card.querySelector('.btn-favorite');
  if (favoriteBtn) {
    favoriteBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const movieId = favoriteBtn.getAttribute('data-id');
      toggleFavorite(movieId, favoriteBtn);

      // Efecto visual
      favoriteBtn.classList.add('pulse-effect');
      setTimeout(() => {
        favoriteBtn.classList.remove('pulse-effect');
      }, 700);
    });
  }

  // Enlace "Ver más"
  const btnMore = card.querySelector('.btn-more');
  if (btnMore) {
    btnMore.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const movieId = btnMore.getAttribute('data-id');
      if (movieId) {
        // Redireccionar a la página de detalle de la película
        window.location.href = `/pelicula/${movieId}`;
      }
    });
  }

  // Hacer clic en la tarjeta también abre los detalles
  card.addEventListener('click', (e) => {
    // Si se hizo clic en un botón o elemento interactivo, no hacer nada
    if (e.target.closest('.action-btn') || e.target.closest('.btn-more')) {
      return;
    }

    const movieId = card.getAttribute('data-id');
    if (movieId) {
      loadMovieDetails(movieId);
    }
  });
}

// ========================================================
// GESTIÓN DE MODALES
// ========================================================
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

    renderMovieModalStatic(movieData, creditsData, similarData.results.slice(0, 4));
  } catch (error) {
    console.error("Error cargando detalles de película:", error);
    const modalDetailContent = document.getElementById("movieDetailContentStatic");
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

function renderMovieModalStatic(movie, credits, similarMovies) {
  const modalDetailContent = document.getElementById("movieDetailContentStatic");
  const modalDetail = document.getElementById("movieDetailModalStatic");

  if (!modalDetailContent || !modalDetail) return;

  const backdropUrl = movie.backdrop_path ?
    `${BACKDROP_URL}${movie.backdrop_path}` :
    DEFAULT_BACKDROP;

  const posterUrl = movie.poster_path ?
    `${IMG_URL}${movie.poster_path}` :
    DEFAULT_POSTER;

  // Obtener director
  const director = credits.crew.find(person => person.job === "Director");

  // Obtener guionistas (escritores)
  const writers = credits.crew.filter(person =>
    person.job === "Screenplay" ||
    person.job === "Writer" ||
    person.job === "Story"
  ).slice(0, 3);

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

  // Estado de la película
  const status = movie.status === "Released" ? "Estrenada" :
                movie.status === "Post Production" ? "En post-producción" :
                movie.status === "In Production" ? "En producción" :
                movie.status === "Planned" ? "Planificada" :
                movie.status || "Desconocido";

  // Verificar si es favorita
  const favorites = JSON.parse(localStorage.getItem('critflix-favorites') || '[]');
  const isFavorite = favorites.includes(movie.id.toString());

  // Crear HTML para detalles de la película
  modalDetailContent.innerHTML = `
    <div class="modal-movie">
      ${backdropUrl ? `<div class="modal-backdrop-image" style="background-image: url('${backdropUrl}')"></div>` : ''}

      <div class="modal-content-wrapper">
        <div class="modal-poster">
          <img src="${posterUrl}" alt="${movie.title}" loading="lazy">
          <div class="poster-actions">
            <button class="btn-primary btn-trailer" data-id="${movie.id}">
              <i class="fas fa-play"></i> Ver Trailer
            </button>
            <button class="btn-favorite ${isFavorite ? 'active' : ''}" data-id="${movie.id}" data-favorited="${isFavorite}">
              <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i> ${isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'}
            </button>
          </div>
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

              ${writers && writers.length > 0 ? `<div class="cast-item director">
                <span class="role">Guionista${writers.length > 1 ? 's' : ''}:</span>
                <span class="name">${writers.map(writer => writer.name).join(', ')}</span>
              </div>` : ''}

              ${mainCast.map(actor => `
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
                <span class="value">${formatDate(movie.release_date)}</span>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-film"></i>
              <div>
                <span class="label">Estado</span>
                <span class="value">${status}</span>
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
              <i class="fas fa-globe"></i>
              <div>
                <span class="label">Idioma original</span>
                <span class="value">${movie.original_language === 'en' ? 'Inglés' : movie.original_language === 'es' ? 'Español' : movie.original_language}</span>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <a href="/pelicula/${movie.id}" class="btn-primary btn-ver-mas">
              <i class="fas fa-external-link-alt"></i> Ver página completa
            </a>
          </div>
        </div>
      </div>

      ${similarMovies.length > 0 ? `
        <div class="similar-movies">
          <h3>Películas similares</h3>
          <div class="similar-grid">
            ${similarMovies.map(similar => `
              <div class="similar-movie">
                <img src="${similar.poster_path ? IMG_URL + similar.poster_path : DEFAULT_POSTER}" alt="${similar.title}">
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
  modalDetail.style.display = "flex";
    document.body.style.overflow = 'hidden'; // Asegurar que el fondo no se desplace

  // Configurar eventos solo si no se han configurado antes
  if (!movieDetailEventsConfigured) {
    // Configurar el botón de cierre
    const closeBtn = document.getElementById("closeMovieDetailStatic");
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        modalDetail.style.display = 'none';
        document.body.style.overflow = '';
      });
    }

    // Cerrar al hacer clic fuera
    modalDetail.addEventListener('click', (e) => {
      if (e.target === modalDetail) {
        modalDetail.style.display = 'none';
        document.body.style.overflow = '';
      }
    });

    // Manejar tecla Escape para este modal específico
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modalDetail.style.display === 'flex') {
        modalDetail.style.display = 'none';
        document.body.style.overflow = '';
      }
    });

    // Marcar que los eventos ya se configuraron
    movieDetailEventsConfigured = true;
  }

  // Configurar eventos para elementos dinámicos dentro del modal
  setTimeout(() => {
    const trailerBtns = modalDetailContent.querySelectorAll('.btn-trailer');
    trailerBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const movieId = btn.getAttribute('data-id');
        if (movieId) {
          loadMovieTrailer(movieId);
        }
      });
    });

    // Configurar eventos para los botones de favoritos dentro del modal
    const favoriteBtns = modalDetailContent.querySelectorAll('.btn-favorite');
    favoriteBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const movieId = btn.getAttribute('data-id');
        if (movieId) {
          toggleFavorite(movieId, btn);
        }
      });
    });

    // Configurar eventos para los botones de detalles de películas similares
    const detailsBtns = modalDetailContent.querySelectorAll('.btn-details');
    detailsBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const movieId = btn.getAttribute('data-id');
        if (movieId) {
          // Cerrar el modal actual
          modalDetail.style.display = 'none';
          // Cargar los detalles de la nueva película
          loadMovieDetails(movieId);
        }
      });
    });
  }, 100);
}

async function loadMovieTrailer(movieId) {
  try {
    // Usar el modal de trailer estático que existe en el HTML
    const trailerModalStatic = document.getElementById('trailerModalStatic');
    const trailerContainerStatic = document.getElementById('trailerContainerStatic');

    if (!trailerModalStatic || !trailerContainerStatic) {
      console.error('No se encontraron los elementos del modal de trailer');
      return;
    }

    // Mostrar el modal y el spinner
    trailerModalStatic.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Mostrar spinner de carga
    trailerContainerStatic.innerHTML = `
      <div class="trailer-loading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; background-color: #000;">
        <div class="spinner" style="width: 40px; height: 40px; border: 3px solid rgba(255, 255, 255, 0.2); border-top: 3px solid #14ff14; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
        <span style="font-size: 0.9rem; margin-top: 10px; color: rgba(255, 255, 255, 0.8);">Cargando trailer...</span>
      </div>
    `;

    // Configurar eventos solo si no se han configurado antes
    if (!trailerEventsConfigured) {
      // Configurar el botón de cierre
      const closeBtn = document.getElementById('closeTrailerBtn');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          // Limpiar contenido primero para detener el video
          trailerContainerStatic.innerHTML = '';
          trailerModalStatic.style.display = 'none';
          document.body.style.overflow = '';
        });
      }

      // Cerrar al hacer clic en el fondo
      trailerModalStatic.addEventListener('click', (e) => {
        if (e.target === trailerModalStatic) {
          trailerContainerStatic.innerHTML = '';
          trailerModalStatic.style.display = 'none';
          document.body.style.overflow = '';
        }
      });

      // Manejar tecla Escape para este modal específico
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && trailerModalStatic.style.display === 'flex') {
          trailerContainerStatic.innerHTML = '';
          trailerModalStatic.style.display = 'none';
          document.body.style.overflow = '';
        }
      });

      // Marcar que los eventos ya se configuraron
      trailerEventsConfigured = true;
    }

    // Cargar datos del trailer con tiempo máximo
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 7000);

    const response = await fetch(`${BASE_URL}/movie/${movieId}/videos?api_key=${API_KEY}&language=es-ES`, {
      signal: controller.signal
    });
    clearTimeout(timeoutId);

    const data = await response.json();

    // Verificar si el modal sigue abierto
    if (!document.body.contains(trailerContainerStatic)) {
      return; // El usuario ya cerró el modal
    }

    // Buscar trailer en español primero, luego cualquier trailer
    const videos = data.results || [];
    const trailer = videos.find(v =>
      v.site === 'YouTube' &&
      v.type === 'Trailer' &&
      (v.name.toLowerCase().includes('español') || v.name.toLowerCase().includes('spanish'))
    ) ||
    videos.find(v => v.site === 'YouTube' && v.type === 'Trailer') ||
    videos.find(v => v.site === 'YouTube' && v.type === 'Teaser');

    if (trailer) {
      // Crear iframe
      trailerContainerStatic.innerHTML = `
      <iframe
        width="100%"
        height="100%"
          src="https://www.youtube.com/embed/${trailer.key}?autoplay=1&rel=0"
          title="YouTube video player"
        frameborder="0"
          style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
      </iframe>
    `;
    } else {
      trailerContainerStatic.innerHTML = `
        <div class="no-trailer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #121212; color: white; text-align: center; padding: 20px;">
          <i class="fas fa-film" style="font-size: 3rem; color: #14ff14; margin-bottom: 20px; opacity: 0.7;"></i>
          <p style="max-width: 80%; line-height: 1.5;">No se encontró ningún trailer disponible para esta película.</p>
        </div>
      `;
    }
  } catch (error) {
    console.error("Error cargando trailer:", error);

    // Mostrar error solo si el modal sigue abierto
    const trailerContainerStatic = document.getElementById('trailerContainerStatic');
    if (trailerContainerStatic && document.body.contains(trailerContainerStatic)) {
      trailerContainerStatic.innerHTML = `
        <div class="no-trailer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #121212; color: white; text-align: center; padding: 20px;">
          <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff4d4d; margin-bottom: 20px; opacity: 0.7;"></i>
          <p style="max-width: 80%; line-height: 1.5;">Error al cargar el trailer. Por favor, inténtalo de nuevo más tarde.</p>
        </div>
      `;
    }
  }
}

function renderTrailerModal(trailerKey) {
  const trailerContainer = document.getElementById("trailerContainer");
  if (!trailerContainer) return;

  const trailerModal = document.getElementById("trailerModal");
  if (!trailerModal) return;

  if (trailerKey) {
    // Crear un fragment para mejorar rendimiento
    const fragment = document.createDocumentFragment();

    // Crear el iframe de manera directa
    const iframe = document.createElement('iframe');
    iframe.width = '100%';
    iframe.height = '100%';
    iframe.src = `https://www.youtube.com/embed/${trailerKey}?autoplay=1&rel=0`;
    iframe.title = 'YouTube video player';
    iframe.frameBorder = '0';
    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
    iframe.allowFullscreen = true;

    // Añadir el iframe al fragmento
    fragment.appendChild(iframe);

    // Limpiar e insertar todo de una vez para menos reflows
    trailerContainer.innerHTML = '';
    trailerContainer.appendChild(fragment);
  } else {
    trailerContainer.innerHTML = `
      <div class="no-trailer">
        <i class="fas fa-film"></i>
        <p>No se encontró ningún trailer disponible para esta película.</p>
      </div>
    `;
  }

  // Mostrar el modal
    trailerModal.classList.add("active");
  document.body.style.overflow = 'hidden';

  // Añadir manejador para la tecla ESC
  const handleEsc = (e) => {
    if (e.key === 'Escape') {
      trailerModal.classList.remove("active");
      document.body.style.overflow = '';
      document.removeEventListener('keydown', handleEsc);
    }
  };
  document.addEventListener('keydown', handleEsc);
}

// ========================================================
// CARGA DE PELÍCULAS
// ========================================================
async function loadMovies(reset = false) {
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
      allMoviesPage = 1;

      const container = document.getElementById('moviesContainer');
      if (container) {
        container.innerHTML = '';

        // Mostrar spinner mientras carga
        container.innerHTML = `
          <div class="loading-placeholder">
            <div class="spinner"></div>
            <p>Cargando películas...</p>
          </div>
        `;
      }
    }

    // Construir URL de la API con filtros activos y limitar a 20 resultados por página
    let apiUrl = `${BASE_URL}/discover/movie?api_key=${API_KEY}&language=es-ES&page=${allMoviesPage}`;

    // Aplicar filtros
    if (activeFilters.genre) {
      apiUrl += `&with_genres=${activeFilters.genre}`;
    }

    if (activeFilters.year) {
      apiUrl += `&primary_release_year=${activeFilters.year}`;
    }

    if (activeFilters.rating > 0) {
      apiUrl += `&vote_average.gte=${activeFilters.rating}`;
    }

    if (activeFilters.sort) {
      apiUrl += `&sort_by=${activeFilters.sort}`;
    }

    // Si hay término de búsqueda, usar search en lugar de discover
    if (activeFilters.search && activeFilters.search.trim() !== '') {
      apiUrl = `${BASE_URL}/search/movie?api_key=${API_KEY}&language=es-ES&query=${encodeURIComponent(activeFilters.search)}&page=${allMoviesPage}`;
    }

    // Hacer petición a la API
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error('Error al cargar películas');
    }

    const data = await response.json();

    // Remover el placeholder de carga
    const container = document.getElementById('moviesContainer');
    if (container && reset) {
      container.innerHTML = '';
    }

    // Renderizar las películas
    renderMovies(data.results, container);

    // Actualizar contador de resultados
    updateResultCounter(data.total_results);

    // Habilitar o deshabilitar botón según haya más páginas
    if (loadMoreBtn) {
      if (allMoviesPage < data.total_pages) {
        loadMoreBtn.disabled = false;
        loadMoreBtn.innerHTML = 'Cargar más películas';
        loadMoreBtn.style.display = 'block';
        allMoviesPage++;
      } else {
        loadMoreBtn.style.display = 'none';
      }
    }

    // Emitir evento para notificar que se han cargado nuevas tarjetas
    document.dispatchEvent(new CustomEvent('cardsLoaded'));

  } catch (error) {
    console.error('Error cargando películas:', error);
    showNotification('Error al cargar películas. Intenta de nuevo más tarde.', 'error');

    const container = document.getElementById('moviesContainer');
    if (container && reset) {
      container.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <h3>No se pudieron cargar las películas</h3>
          <p>Ha ocurrido un error. Por favor, intenta de nuevo más tarde.</p>
          <button class="btn-link" onclick="loadMovies(true)">
            <i class="fas fa-redo"></i> Reintentar
          </button>
        </div>
      `;
    }

  } finally {
    isLoading = false;

    if (loadMoreBtn && loadMoreBtn.style.display !== 'none') {
      loadMoreBtn.disabled = false;
      loadMoreBtn.innerHTML = 'Cargar más películas';
    }
  }
}

// Actualizar contador de resultados
function updateResultCounter(total) {
  const counter = document.getElementById('resultCount');
  if (counter) {
    counter.textContent = total;
  }
}

// Generar estrellas según valoración
function generateStarRating(rating) {
  let stars = '';

  for (let i = 1; i <= 5; i++) {
    if (i <= Math.floor(rating)) {
      // Estrella completa
      stars += '<i class="fas fa-star"></i>';
    } else if (i - 0.5 <= rating) {
      // Media estrella
      stars += '<i class="fas fa-star-half-alt"></i>';
    } else {
      // Estrella vacía
      stars += '<i class="far fa-star"></i>';
    }
  }

  return stars;
}

// ========================================================
// ANIMACIONES Y EFECTOS DE SCROLL
// ========================================================
function setupScrollEffects() {
  // Configurar el indicador de scroll para que funcione correctamente
  const scrollIndicator = document.querySelector('.scroll-indicator');
  if (scrollIndicator) {
    scrollIndicator.addEventListener('click', () => {
      const moviesSection = document.getElementById('moviesSection');
      if (moviesSection) {
        moviesSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  // Observador de intersección para animaciones al scroll
  const options = {
    threshold: 0.1,
    rootMargin: '0px 0px -10% 0px'
  };

  const appearOnScroll = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;

      // Animar el elemento cuando es visible
      if (entry.target.classList.contains('movie-card')) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }

      // Dejar de observar una vez aplicada la animación
      observer.unobserve(entry.target);
    });
  }, options);

  // Aplicar efectos a las tarjetas de películas
  const animateCards = () => {
    // Capturar todas las tarjetas no animadas
    const cards = document.querySelectorAll('.movie-card:not(.animated)');

    cards.forEach((card, index) => {
      // Establecer estado inicial
      card.style.opacity = '0';
      card.style.transform = 'translateY(30px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        card.classList.add('animated');

      // Observar para animar al scroll
      appearOnScroll.observe(card);
    });
  };

  // Ejecutar inicialmente
  animateCards();

  // Re-ejecutar cuando se cargan nuevas tarjetas
  document.addEventListener('cardsLoaded', animateCards);
}

// ========================================================
// AUTOCOMPLETADO DE BÚSQUEDA
// ========================================================
function setupSearchAutocomplete() {
  const searchInput = document.getElementById('searchInput');
  const searchBox = document.querySelector('.search-box');

  if (!searchInput) return;

  // Búsqueda con debounce para mejorar rendimiento
  searchInput.addEventListener('input', debounce(function() {
    const query = this.value.toLowerCase().trim();

    // Actualizar filtro de búsqueda
    activeFilters.search = query;

    // Actualizar filtro actual y refrescar películas
    allMoviesPage = 1;

    // Aplicar filtro inmediatamente
    loadMovies(true);

    // Animación de feedback visual
    if (searchBox) {
      searchBox.classList.add('searching');
    }

    // Animación sutil del borde del input para mostrar actividad
    searchInput.style.borderColor = 'var(--verde-neon)';
    searchInput.style.boxShadow = '0 0 10px var(--verde-neon)';

    setTimeout(() => {
      searchInput.style.borderColor = '';
      searchInput.style.boxShadow = '';
      if (searchBox) {
        searchBox.classList.remove('searching');
      }
    }, 500);

    // Mostrar mensaje de búsqueda si el query tiene al menos 2 caracteres
    if (query.length >= 2) {
      showNotification(`Buscando: "${query}"`, "info");
    }
  }, 400)); // Reducir tiempo para mejor respuesta

  // Cerrar búsqueda con Escape
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchInput.value = '';
      activeFilters.search = '';
      loadMovies(true);
      searchInput.blur();
      if (searchBox) {
        searchBox.classList.remove('searching');
      }
      showNotification("Búsqueda limpiada", "info");
    }
  });

  // Aplicar búsqueda al presionar Enter
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const query = searchInput.value.toLowerCase().trim();
      if (query.length > 0) {
        activeFilters.search = query;
        allMoviesPage = 1;
        loadMovies(true);
        if (searchBox) {
          // Efecto de búsqueda
          searchBox.classList.add('searching');
          setTimeout(() => {
            searchBox.classList.remove('searching');
          }, 1500);
        }
        showNotification(`Mostrando resultados para: "${query}"`, "success");
      }
    }
  });
}

// ========================================================
// GESTIÓN DE FAVORITOS
// ========================================================
function setupFavorites() {
  // No necesitamos delegación de eventos aquí ya que configuramos los eventos
  // directamente en cada botón al renderizar las tarjetas

  // Inicializar favoritos
  updateFavoriteButtons();
}

function toggleFavorite(movieId, button) {
  let favorites = JSON.parse(localStorage.getItem('critflix-favorites') || '[]');
  const card = button.closest('.movie-card');
  const isFavorite = favorites.includes(movieId.toString());

  // Mostrar spinner para indicar procesamiento
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  button.disabled = true;

  // Preparar los datos para enviar a la API
  const formData = new FormData();
  formData.append('movie_id', movieId);
  formData.append('action', isFavorite ? 'remove' : 'add');

  // Realizar la petición al servidor para guardar en la base de datos
  fetch('/api/favorites', {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Error al procesar la solicitud');
    }
    return response.json();
  })
  .then(data => {
    // Si la operación fue exitosa, actualizar la interfaz
    if (isFavorite) {
      // Eliminar de favoritos
      favorites = favorites.filter(id => id !== movieId.toString());

      // Actualizar icono
      button.innerHTML = '<i class="far fa-heart"></i>';

      // Actualizar atributos del botón
      button.classList.remove('active');
      button.setAttribute('data-favorited', 'false');
      button.setAttribute('aria-label', 'Añadir a favoritos');

      // Mostrar feedback
      showNotification('Película eliminada de favoritos', 'info');

      // Quitar clase de favorito de la tarjeta si existe
      if (card) {
        card.classList.remove('is-favorite');
      }
    } else {
      // Añadir a favoritos
      favorites.push(movieId.toString());

      // Actualizar icono con corazón rojo
      button.innerHTML = '<i class="fas fa-heart"></i>';

      // Actualizar atributos del botón
      button.classList.add('active');
      button.setAttribute('data-favorited', 'true');
      button.setAttribute('aria-label', 'Quitar de favoritos');

      // Mostrar feedback
      showNotification('Película añadida a favoritos', 'success');

      // Añadir clase de favorito a la tarjeta
      if (card) {
        card.classList.add('is-favorite');

        // Añadir animación para destacar que se ha añadido a favoritos
        card.classList.add('pulse-effect');
        setTimeout(() => {
          card.classList.remove('pulse-effect');
        }, 700);
      }
    }

    // Guardar en localStorage (respaldo local)
    localStorage.setItem('critflix-favorites', JSON.stringify(favorites));

    // Habilitar nuevamente el botón
    button.disabled = false;
  })
  .catch(error => {
    console.error('Error al actualizar favoritos:', error);
    showNotification('Ha ocurrido un error. Por favor, inténtalo de nuevo.', 'error');

    // Restaurar estado del botón en caso de error
    button.disabled = false;
    button.innerHTML = `<i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>`;
  });
}

function updateFavoriteButtons() {
  const favorites = JSON.parse(localStorage.getItem('critflix-favorites') || '[]');

  // Actualizar todos los botones de favoritos
  document.querySelectorAll('.btn-favorite').forEach(button => {
    const movieId = button.getAttribute('data-id');
    const icon = button.querySelector('i');
    const card = button.closest('.movie-card');
    const isFavorite = favorites.includes(movieId.toString());

    if (isFavorite) {
      // Es favorito
      if (icon) {
        icon.className = 'fas fa-heart';
      }

      if (card) {
        card.classList.add('is-favorite');
      }

      button.classList.add('active');
      button.setAttribute('data-favorited', 'true');
      button.setAttribute('aria-label', 'Quitar de favoritos');
    } else {
      // No es favorito
      if (icon) {
        icon.className = 'far fa-heart';
      }

      if (card) {
        card.classList.remove('is-favorite');
      }

      button.classList.remove('active');
      button.setAttribute('data-favorited', 'false');
      button.setAttribute('aria-label', 'Añadir a favoritos');
    }
  });
}

function showNotification(message, type = "info") {
  // Crear contenedor si no existe
  const container = document.getElementById('notificationContainer') || createNotificationContainer();

  // Crear notificación
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;

  // Icono según tipo
  let icon;
  switch (type) {
    case 'success':
      icon = '<i class="fas fa-check-circle"></i>';
      break;
    case 'error':
      icon = '<i class="fas fa-exclamation-circle"></i>';
      break;
    default:
      icon = '<i class="fas fa-info-circle"></i>';
  }

  notification.innerHTML = `
    ${icon}
    <div class="notification-message">${message}</div>
  `;

  // Añadir al contenedor
  container.appendChild(notification);

  // Mostrar con animación
  setTimeout(() => notification.classList.add('show'), 10);

  // Eliminar después de 3 segundos
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300); // Esperar a que termine la animación
  }, 3000);
}

function createNotificationContainer() {
  const container = document.createElement('div');
  container.id = 'notificationContainer';
  container.className = 'notification-container';
  document.body.appendChild(container);
  return container;
}

// ========================================================
// OPTIMIZACIÓN DE RENDIMIENTO
// ========================================================
function optimizePagePerformance() {
  // Prefetch de imágenes para mejorar la carga
  prefetchMovieImages();

  // Optimizar animaciones
  optimizeAnimations();

  // Desactivar animaciones en móviles de baja gama
  detectLowEndDevices();
}

// Prefetch de imágenes para mejorar la experiencia
function prefetchMovieImages() {
  // Prefetch solo las primeras 10 imágenes visibles en la pantalla
  const movieCards = document.querySelectorAll('.movie-card img');
  let count = 0;

  movieCards.forEach(img => {
    if (count < 10 && !img.dataset.prefetched) {
      const imgSrc = img.getAttribute('src');

      if (imgSrc && !imgSrc.includes('placeholder')) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = imgSrc;
        link.as = 'image';
        document.head.appendChild(link);

        img.dataset.prefetched = 'true';
        count++;
      }
    }
  });
}

// Optimizar animaciones según rendimiento del dispositivo
function optimizeAnimations() {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    // Desactivar animaciones para usuarios que prefieren menos movimiento
    document.documentElement.classList.add('reduce-motion');

    // Desactivar autoplay del carrusel
    if (window.featuredSwiper) {
      window.featuredSwiper.autoplay.stop();
    }
  }
}

// Detectar dispositivos de baja gama
function detectLowEndDevices() {
  // Usar Memory y CPU cores como indicador de rendimiento
  const isLowEndDevice = navigator.hardwareConcurrency < 4;

  if (isLowEndDevice) {
    document.documentElement.classList.add('low-end-device');

    // Reducir calidad de imágenes
    document.querySelectorAll('.movie-poster img').forEach(img => {
      const src = img.getAttribute('src');
      if (src && src.includes('w500')) {
        // Cargar versión de menor resolución
        img.setAttribute('src', src.replace('w500', 'w200'));
      }
    });
  }
}

// ========================================================
// INICIALIZACIÓN
// ========================================================
document.addEventListener('DOMContentLoaded', () => {
  // Configurar captura de errores global para evitar fallos en la página
  window.addEventListener('error', function(e) {
    console.error('Error en la página:', e.message);
    showNotification('Ha ocurrido un error inesperado. Por favor, recarga la página.', 'error');
  });

  // Capturar errores en promesas no manejadas
  window.addEventListener('unhandledrejection', function(e) {
    console.error('Promesa no manejada:', e.reason);
    showNotification('Ha ocurrido un error de conexión. Verifica tu conexión a internet.', 'error');
  });

  // Asegurarse de que no haya modales abiertos al inicio
  closeModals();

  // Permitir scroll normal al inicio
  document.body.style.overflow = '';

  // Ocultar loader de página
  hidePageLoader();

  // Cargar películas destacadas
  loadFeaturedMovies();

  // Inicializar filtros
  setupFilters();

  // Inicializar toggles de vista
  setupViewToggle();

  // Configurar modales
  setupModals();

  // Configurar botón scroll top
  setupScrollToTop();

  // Configurar animaciones de scroll
  setupScrollEffects();

  // Configurar autocompletado de búsqueda
  setupSearchAutocomplete();

  // Configurar eventos de scroll
  window.addEventListener('scroll', handleScroll);

  // Configurar el indicador de scroll
  const scrollIndicator = document.querySelector('.scroll-indicator');
  if (scrollIndicator) {
    scrollIndicator.addEventListener('click', () => {
      const moviesSection = document.getElementById('moviesSection');
      if (moviesSection) {
        moviesSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  // Cargar películas iniciales
  loadMovies(true);

  // Cargar favoritos del usuario desde la base de datos
  loadUserFavorites();

  // Configurar botón "Cargar más"
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      allMoviesPage++;
      loadMovies(false);
    });
  }

  // Configurar eventos de scroll to sections
  const discoverMoviesBtn = document.getElementById('discoverMovies');
  const scrollToFeaturedBtn = document.getElementById('scrollToFeatured');

  if (discoverMoviesBtn) {
    discoverMoviesBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const moviesSection = document.getElementById('moviesSection');
      if (moviesSection) {
        moviesSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  if (scrollToFeaturedBtn) {
    scrollToFeaturedBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const featuredSection = document.getElementById('featuredSection');
      if (featuredSection) {
        featuredSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  // Configurar gestión de favoritos
  setupFavorites();

  // Reparar elementos del header (para asegurar que la barra de búsqueda funcione correctamente)
  fixHeaderElements();

  // Optimizar rendimiento de la página
  optimizePagePerformance();

  // Mostrar notificación de bienvenida después de un breve delay
  setTimeout(() => {
    showNotification("¡Bienvenido a CritFlix! Explora el mejor cine.", "success");
  }, 1500);

  // Asegurar que ningún modal se abra automáticamente
  setTimeout(() => {
    const trailerModalStatic = document.getElementById('trailerModalStatic');
    const movieDetailModalStatic = document.getElementById('movieDetailModalStatic');

    if (trailerModalStatic && trailerModalStatic.style.display === 'flex') {
      console.log('Cerrando trailer modal que estaba abierto automáticamente');
      trailerModalStatic.style.display = 'none';
    }

    if (movieDetailModalStatic && movieDetailModalStatic.style.display === 'flex') {
      console.log('Cerrando movie detail modal que estaba abierto automáticamente');
      movieDetailModalStatic.style.display = 'none';
    }

    document.body.style.overflow = '';
  }, 500);
});

// Ocultar el loader de página
function hidePageLoader() {
  const pageLoader = document.getElementById('pageLoader');
  if (pageLoader) {
    // Pequeño delay para asegurar transición suave
    setTimeout(() => {
      pageLoader.classList.add('hidden');
      // Eliminar completamente después de la transición
      setTimeout(() => {
        pageLoader.remove();
      }, 500);
    }, 500);
  }
}

// Función para corregir elementos del header
function fixHeaderElements() {
  // Asegurarse de que la barra de búsqueda del header tenga los estilos correctos
  const headerSearchBoxes = document.querySelectorAll('header .search-box, nav .search-box');
  headerSearchBoxes.forEach(box => {
    // Aplicar estilos específicos para la barra de búsqueda del header
    box.style.maxWidth = '250px';

    // Asegurarse de que el input tenga el estilo correcto
    const input = box.querySelector('input');
    if (input) {
      input.style.width = '100%';
      input.style.padding = '10px 12px 10px 40px';
      input.style.borderRadius = '50px';
      input.style.background = 'rgba(0, 0, 0, 0.7)';
      input.style.border = '2px solid rgba(20, 255, 20, 0.2)';
    }

    // Corregir el ícono
    const icon = box.querySelector('i');
    if (icon) {
      icon.style.position = 'absolute';
      icon.style.left = '15px';
      icon.style.top = '50%';
      icon.style.transform = 'translateY(-50%)';
    }
  });

  // Corregir otros elementos del header si es necesario
  const navLinks = document.querySelectorAll('nav a, header a');
  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      // No prevenir la navegación, solo asegurarse de que se manejen los errores
      try {
        // Si es un enlace interno (hash), hacer scroll suave
        if (link.getAttribute('href').startsWith('#')) {
          e.preventDefault();
          const targetId = link.getAttribute('href').substring(1);
          const targetElement = document.getElementById(targetId);
          if (targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth' });
          }
        }
      } catch (err) {
        console.error('Error al manejar clic en enlace:', err);
      }
    });
  });
}

// Función para cargar los favoritos del usuario desde la base de datos
function loadUserFavorites() {
  // Verificar si hay un token de autenticación (usuario logueado)
  const authToken = document.querySelector('meta[name="auth-token"]')?.content;

  if (!authToken) {
    console.log('Usuario no autenticado, usando favoritos locales');
    return; // Usar favoritos locales si no hay sesión
  }

  fetch('/api/user/favorites', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      'Authorization': `Bearer ${authToken}`
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Error al cargar favoritos');
    }
    return response.json();
  })
  .then(data => {
    // Guardar IDs de favoritos en localStorage
    if (data.favorites && Array.isArray(data.favorites)) {
      const favoriteIds = data.favorites.map(movie => movie.pelicula_id.toString());
      localStorage.setItem('critflix-favorites', JSON.stringify(favoriteIds));

      // Actualizar UI de favoritos
      updateFavoriteButtons();

      console.log('Favoritos cargados desde la base de datos:', favoriteIds.length);
    }
  })
  .catch(error => {
    console.error('Error al cargar favoritos del usuario:', error);
  });
}


