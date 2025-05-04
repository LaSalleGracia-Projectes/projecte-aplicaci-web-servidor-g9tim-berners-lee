@extends('layouts.app')

@section('title', 'CritFlix | Explora el mejor cine')

@push('styles')
  <link rel="stylesheet" href="{{ asset('movies.css') }}">
  <link rel="stylesheet" href="{{ asset('js/components/movieDetail.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>
@endpush

@section('content')
<!-- Indicador de carga inicial -->
<div id="pageLoader" class="page-loader">
  <div class="spinner-container">
    <div class="spinner"></div>
    <p>Cargando CritFlix...</p>
  </div>
</div>

<a href="#moviesSection" class="skip-link">Saltar al contenido principal</a>
<div class="critiflix-container">
  <!-- HERO SECTION DINAMICA CON PELÍCULA DESTACADA -->
  <section class="hero-dynamic">
    <div class="hero-backdrop"></div>
    <div class="hero-gradient"></div>
    <div class="hero-content">
      <div class="hero-tagline">BIENVENIDO A</div>
      <h1 class="hero-title">CRIT<span>FLIX</span></h1>
      <p class="hero-description">Descubre, valora y comparte tus películas favoritas en la mejor comunidad cinéfila</p>
      <div class="hero-actions">
        <button class="btn-primary btn-discover" id="discoverMovies">
          <i class="fas fa-film"></i> Descubrir Películas
        </button>
        <button class="btn-secondary btn-featured" id="scrollToFeatured">
          <i class="fas fa-star"></i> Destacadas
        </button>
      </div>
    </div>
    <div class="scroll-indicator">
      <span>Explora</span>
      <i class="fas fa-chevron-down"></i>
    </div>
  </section>

  <!-- BARRA DE HERRAMIENTAS FLOTANTE -->
  <div class="toolbar-wrapper" id="toolbarWrapper">
    <div class="tools-container">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Buscar película..." aria-label="Buscar película">
      </div>
      <div class="filter-quick-actions">
        <button class="quick-filter active" data-filter="all">Todas</button>
        <button class="quick-filter" data-filter="trending">Tendencias</button>
        <button class="quick-filter" data-filter="toprated">Mejor valoradas</button>
        <button class="quick-filter" data-filter="new">Recientes</button>
        <button class="advanced-filter-toggle" id="advancedFilterToggle">
          <i class="fas fa-sliders-h"></i> Filtros
        </button>
      </div>
      <div class="view-options">
        <button id="gridView" class="view-toggle active" aria-label="Vista de cuadrícula">
          <i class="fas fa-th-large"></i>
        </button>
        <button id="listView" class="view-toggle" aria-label="Vista de lista">
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- FILTROS AVANZADOS (Panel desplegable) -->
  <section class="advanced-filters" id="advancedFilters">
    <div class="filter-header">
      <h3>Filtros Avanzados</h3>
      <button class="close-filters" id="closeFilters" aria-label="Cerrar filtros">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="filters-grid">
      <!-- Filtro por Género -->
      <div class="filter-group">
        <label for="genreSelect">Género</label>
        <select id="genreSelect" class="custom-select">
          <option value="">Todos los géneros</option>
          <option value="28">Acción</option>
          <option value="12">Aventura</option>
          <option value="16">Animación</option>
          <option value="35">Comedia</option>
          <option value="80">Crimen</option>
          <option value="99">Documental</option>
          <option value="18">Drama</option>
          <option value="10751">Familiar</option>
          <option value="14">Fantasía</option>
          <option value="36">Historia</option>
          <option value="27">Terror</option>
          <option value="10402">Música</option>
          <option value="9648">Misterio</option>
          <option value="10749">Romance</option>
          <option value="878">Ciencia ficción</option>
          <option value="10770">Película de TV</option>
          <option value="53">Thriller</option>
          <option value="10752">Bélica</option>
          <option value="37">Western</option>
        </select>
      </div>

      <!-- Filtro por Año -->
      <div class="filter-group">
        <label for="yearSelect">Año</label>
        <select id="yearSelect" class="custom-select">
          <option value="">Todos los años</option>
          @php
            $currentYear = date('Y');
            for ($year = $currentYear; $year >= 1980; $year--) {
                echo "<option value=\"{$year}\">{$year}</option>";
            }
          @endphp
        </select>
      </div>

      <!-- Filtro por Rating -->
      <div class="filter-group rating-filter">
        <label for="minRating">Rating mínimo: <span id="ratingValue">0</span></label>
        <div class="range-container">
          <input type="range" id="minRating" min="0" max="10" step="0.5" value="0" class="range-slider">
          <div class="rating-stars">
            <i class="far fa-star"></i>
            <i class="far fa-star"></i>
            <i class="far fa-star"></i>
            <i class="far fa-star"></i>
            <i class="far fa-star"></i>
          </div>
        </div>
      </div>

      <!-- Filtro de Orden -->
      <div class="filter-group">
        <label for="sortSelect">Ordenar por</label>
        <select id="sortSelect" class="custom-select">
          <option value="popularity.desc">Popularidad</option>
          <option value="vote_average.desc">Mejor valoradas</option>
          <option value="release_date.desc">Más recientes</option>
          <option value="release_date.asc">Más antiguas</option>
          <option value="original_title.asc">Título A-Z</option>
          <option value="original_title.desc">Título Z-A</option>
        </select>
      </div>
    </div>

    <div class="filter-actions">
      <button id="applyFilters" class="btn-apply">
        <i class="fas fa-check"></i> Aplicar filtros
      </button>
      <button id="resetFilters" class="btn-reset">
        <i class="fas fa-undo"></i> Restablecer
      </button>
    </div>
  </section>

  <!-- CARRUSEL DE PELÍCULAS DESTACADAS -->
  <section class="featured-section" id="featuredSection">
    <div class="section-header">
      <h2 class="section-title">Películas destacadas</h2>
      <div class="section-actions">
        <a href="#" class="see-all">Ver todas <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="swiper featured-swiper">
      <div class="swiper-wrapper" id="featuredSlider">
        <!-- Se rellenará dinámicamente con JS -->
        <div class="swiper-slide">
          <div class="featured-slide">
            <div class="featured-overlay" style="background-image: url('https://via.placeholder.com/1280x720/121212/00ff3c?text=Cargando...')"></div>
            <div class="featured-content">
              <div class="featured-poster">
                <img src="https://via.placeholder.com/500x750/121212/00ff3c?text=Cargando..." alt="Cargando...">
              </div>
              <div class="featured-info">
                <h2>Cargando películas destacadas...</h2>
                <div class="featured-meta">
                  <span class="release-date">Por favor espera</span>
                </div>
                <p class="featured-overview">Estamos obteniendo las últimas películas para ti.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </section>

  <!-- CATEGORÍAS RÁPIDAS -->
  <section class="category-chips">
    <div class="category-chip" data-genre="28">
      <i class="fas fa-fire-alt"></i> Acción
    </div>
    <div class="category-chip" data-genre="878">
      <i class="fas fa-robot"></i> Sci-Fi
    </div>
    <div class="category-chip" data-genre="27">
      <i class="fas fa-ghost"></i> Terror
    </div>
    <div class="category-chip" data-genre="35">
      <i class="fas fa-grin-squint"></i> Comedia
    </div>
    <div class="category-chip" data-genre="10749">
      <i class="fas fa-heart"></i> Romance
    </div>
    <div class="category-chip" data-genre="18">
      <i class="fas fa-theater-masks"></i> Drama
    </div>
    <div class="category-chip" data-genre="12">
      <i class="fas fa-compass"></i> Aventura
    </div>
  </section>

  <!-- SECCIÓN DE TODAS LAS PELÍCULAS -->
  <section class="movies-section" id="moviesSection">
    <div class="section-header">
      <h2 class="section-title">Explora películas</h2>
      <span class="results-counter" id="resultsCounter">Mostrando <span id="resultCount">0</span> resultados</span>
    </div>

    <div class="movies-container grid-view" id="moviesContainer">
      <!-- Películas cargadas dinámicamente con JavaScript -->
      <div class="loading-placeholder">
        <div class="spinner"></div>
        <p>Cargando películas...</p>
      </div>
    </div>

    <div class="pagination-container">
      <button id="loadMoreBtn" class="load-more-btn">
        <span>Cargar más películas</span>
        <i class="fas fa-spinner fa-spin d-none"></i>
      </button>
    </div>
  </section>

  <!-- MODALES ESTÁTICOS -->
  <div id="trailerModalStatic" class="trailer-modal">
    <div class="trailer-modal-content">
      <button id="closeTrailerBtn" class="trailer-close-btn">&times;</button>
      <div id="trailerContainerStatic" class="trailer-container">
        <div class="trailer-loading">
          <div class="spinner"></div>
          <span>Cargando trailer...</span>
        </div>
      </div>
    </div>
  </div>

  <div id="movieDetailModalStatic" class="movie-detail-modal">
    <div class="movie-detail-content">
      <button id="closeMovieDetailStatic" class="movie-detail-close">&times;</button>
      <div id="movieDetailContentStatic">
        <div class="loading-indicator">
          <div class="spinner"></div>
          <p>Cargando detalles...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenedor para notificaciones -->
  <div class="notification-container" id="notificationContainer"></div>

  <!-- BOTÓN SCROLL TO TOP -->
  <button class="scroll-top" id="scrollTop" aria-label="Volver arriba">
    <i class="fas fa-chevron-up"></i>
  </button>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="{{ asset('movies.js') }}"></script>
<script src="{{ asset('js/ui-interactions.js') }}"></script>
@endpush
