@extends('layouts.app')

@section('title', __('messages.series') . ' - CritFlix')

@push('styles')
  <link rel="stylesheet" href="{{ asset('movies.css') }}">
  <link rel="stylesheet" href="{{ asset('js/components/movieDetail.css') }}">
  <link rel="stylesheet" href="{{ asset('css/movie-details-modal.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>
@endpush

@section('content')
<!-- Meta para CSRF y autenticación -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@auth
<meta name="auth-token" content="{{ auth()->user()->api_token ?? '' }}">
@endauth

<!-- Indicador de carga inicial -->
<div id="pageLoader" class="page-loader">
  <div class="spinner-container">
    <div class="spinner"></div>
    <p>{{ __('messages.loading') }}...</p>
  </div>
</div>

<a href="#seriesSection" class="skip-link">{{ __('messages.skip_to_content') }}</a>
<div class="critiflix-container">
  <!-- HERO SECTION DINAMICA CON SERIE DESTACADA -->
  <section class="hero-dynamic">
    <div class="hero-backdrop"></div>
    <div class="hero-gradient"></div>
    <div class="hero-content">
      <div class="hero-tagline">{{ __('messages.welcome_to') }}</div>
      <h1 class="hero-title">CRIT<span>FLIX</span></h1>
      <p class="hero-description">{{ __('messages.discover_rate_share_series') }}</p>
      <div class="hero-actions">
        <button class="btn-primary btn-discover" id="discoverSeries">
          <i class="fas fa-tv"></i> {{ __('messages.discover_series') }}
        </button>
        <button class="btn-secondary btn-featured" id="scrollToFeatured">
          <i class="fas fa-star"></i> {{ __('messages.featured') }}
        </button>
      </div>
    </div>
    <div class="scroll-indicator">
      <span>{{ __('messages.explore') }}</span>
      <i class="fas fa-chevron-down"></i>
    </div>
  </section>

  <!-- BARRA DE HERRAMIENTAS FLOTANTE -->
  <div class="toolbar-wrapper" id="toolbarWrapper">
    <div class="tools-container">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="{{ __('messages.search_series') }}..." aria-label="{{ __('messages.search_series') }}">
      </div>
      <div class="filter-quick-actions">
        <button class="quick-filter active" data-filter="all">{{ __('messages.all') }}</button>
        <button class="quick-filter" data-filter="trending">{{ __('messages.trends') }}</button>
        <button class="quick-filter" data-filter="toprated">{{ __('messages.top_rated') }}</button>
        <button class="quick-filter" data-filter="new">{{ __('messages.recent') }}</button>
        <button class="advanced-filter-toggle" id="advancedFilterToggle">
          <i class="fas fa-sliders-h"></i> {{ __('messages.filters') }}
        </button>
      </div>
      <div class="view-options">
        <button id="gridView" class="view-toggle active" aria-label="{{ __('messages.grid_view') }}">
          <i class="fas fa-th-large"></i>
        </button>
        <button id="listView" class="view-toggle" aria-label="{{ __('messages.list_view') }}">
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- FILTROS AVANZADOS (Panel desplegable) -->
  <section class="advanced-filters" id="advancedFilters">
    <div class="filter-header">
      <h3>{{ __('messages.advanced_filters') }}</h3>
      <button class="close-filters" id="closeFilters" aria-label="{{ __('messages.close_filters') }}">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="filters-grid">
      <!-- Filtro por Género -->
      <div class="filter-group">
        <label for="genreSelect">{{ __('messages.genre') }}</label>
        <select id="genreSelect" class="custom-select">
          <option value="">{{ __('messages.all_genres') }}</option>
          <option value="10759">{{ __('messages.action_adventure') }}</option>
          <option value="16">{{ __('messages.animation') }}</option>
          <option value="35">{{ __('messages.comedy') }}</option>
          <option value="80">{{ __('messages.crime') }}</option>
          <option value="99">{{ __('messages.documentary') }}</option>
          <option value="18">{{ __('messages.drama') }}</option>
          <option value="10751">{{ __('messages.family') }}</option>
          <option value="10762">{{ __('messages.kids') }}</option>
          <option value="9648">{{ __('messages.mystery') }}</option>
          <option value="10763">{{ __('messages.news') }}</option>
          <option value="10764">{{ __('messages.reality') }}</option>
          <option value="10765">{{ __('messages.scifi_fantasy') }}</option>
          <option value="10766">{{ __('messages.soap') }}</option>
          <option value="10767">{{ __('messages.talk') }}</option>
          <option value="10768">{{ __('messages.war_politics') }}</option>
          <option value="37">{{ __('messages.western') }}</option>
        </select>
      </div>

      <!-- Filtro por Año -->
      <div class="filter-group">
        <label for="yearSelect">{{ __('messages.year') }}</label>
        <select id="yearSelect" class="custom-select">
          <option value="">{{ __('messages.all_years') }}</option>
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
        <label for="minRating">{{ __('messages.min_rating') }}: <span id="ratingValue">0</span></label>
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
        <label for="sortSelect">{{ __('messages.sort_by') }}</label>
        <select id="sortSelect" class="custom-select">
          <option value="popularity.desc">{{ __('messages.popularity') }}</option>
          <option value="vote_average.desc">{{ __('messages.highest_rated') }}</option>
          <option value="first_air_date.desc">{{ __('messages.newest') }}</option>
          <option value="first_air_date.asc">{{ __('messages.oldest') }}</option>
          <option value="name.asc">{{ __('messages.title_asc') }}</option>
          <option value="name.desc">{{ __('messages.title_desc') }}</option>
        </select>
      </div>
    </div>

    <div class="filter-actions">
      <button id="applyFilters" class="btn-apply">
        <i class="fas fa-check"></i> {{ __('messages.apply_filters') }}
      </button>
      <button id="resetFilters" class="btn-reset">
        <i class="fas fa-undo"></i> {{ __('messages.reset') }}
      </button>
    </div>
  </section>

  <!-- CARRUSEL DE SERIES DESTACADAS -->
  <section class="featured-section" id="featuredSection">
    <div class="section-header">
      <h2 class="section-title">{{ __('messages.featured_series') }}</h2>
      <div class="section-actions">
        <a href="#" class="see-all">{{ __('messages.see_all') }} <i class="fas fa-arrow-right"></i></a>
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
                <img src="https://via.placeholder.com/500x750/121212/00ff3c?text=Cargando..." alt="{{ __('messages.loading') }}">
              </div>
              <div class="featured-info">
                <h2>{{ __('messages.loading_featured_series') }}...</h2>
                <div class="featured-meta">
                  <span class="release-date">{{ __('messages.please_wait') }}</span>
                </div>
                <p class="featured-overview">{{ __('messages.getting_latest_series') }}</p>
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
    <div class="category-chip" data-genre="10759">
      <i class="fas fa-running"></i> {{ __('messages.action') }}
    </div>
    <div class="category-chip" data-genre="10765">
      <i class="fas fa-robot"></i> {{ __('messages.sci_fi') }}
    </div>
    <div class="category-chip" data-genre="9648">
      <i class="fas fa-mask"></i> {{ __('messages.mystery') }}
    </div>
    <div class="category-chip" data-genre="35">
      <i class="fas fa-grin-squint"></i> {{ __('messages.comedy') }}
    </div>
    <div class="category-chip" data-genre="18">
      <i class="fas fa-theater-masks"></i> {{ __('messages.drama') }}
    </div>
    <div class="category-chip" data-genre="10768">
      <i class="fas fa-flag"></i> {{ __('messages.war_politics') }}
    </div>
    <div class="category-chip" data-genre="16">
      <i class="fas fa-child"></i> {{ __('messages.animation') }}
    </div>
  </section>

  <!-- SECCIÓN DE TODAS LAS SERIES -->
  <section class="movies-section" id="seriesSection">
    <div class="section-header">
      <h2 class="section-title">{{ __('messages.discover_series') }}</h2>
      <span class="results-counter" id="resultsCounter">{{ __('messages.showing') }} <span id="resultCount">0</span> {{ __('messages.results') }}</span>
    </div>

    <div class="movies-container grid-view" id="seriesContainer">
      <!-- Series cargadas dinámicamente con JavaScript -->
      <div class="loading-placeholder">
        <div class="spinner"></div>
        <p>{{ __('messages.loading_featured_series') }}...</p>
      </div>
    </div>

    <div class="pagination-container">
      <button id="loadMoreBtn" class="load-more-btn">
        <span>{{ __('messages.load_more_series') }}</span>
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
          <span>{{ __('messages.loading_trailer') }}...</span>
        </div>
      </div>
    </div>
  </div>

  <div id="serieDetailModalStatic" class="movie-detail-modal">
    <div class="movie-detail-content">
      <button id="closeSerieDetailStatic" class="movie-detail-close">&times;</button>
      <div id="serieDetailContentStatic">
        <div class="loading-indicator">
          <div class="spinner"></div>
          <p>{{ __('messages.loading_details') }}...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenedor para notificaciones -->
  <div class="notification-container" id="notificationContainer"></div>

  <!-- BOTÓN SCROLL TO TOP -->
  <button class="scroll-top" id="scrollTop" aria-label="{{ __('messages.back_to_top') }}">
    <i class="fas fa-chevron-up"></i>
  </button>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="{{ asset('series.js') }}"></script>
<script src="{{ asset('js/ui-interactions.js') }}"></script>
@endpush
