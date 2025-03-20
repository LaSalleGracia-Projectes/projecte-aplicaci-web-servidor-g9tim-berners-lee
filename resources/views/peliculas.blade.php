@extends('layouts.app')

@section('title', 'Crítiflix | Tu portal de películas')

@push('styles')
  <link rel="stylesheet" href="{{ asset('movies.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
@endpush

@section('content')
<main class="critiflix-container">
  <!-- HERO SECTION -->
  <section class="hero-section">
    <div class="hero-content">
      <h1>CrítiFlix</h1>
      <p>Descubre, explora y comparte tus películas favoritas</p>
    </div>
  </section>

  <!-- FILTRADOR AVANZADO -->
  <section class="filter-section">
    <div class="filter-container">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Buscar por título, director o actor...">
      </div>

      <div class="filter-controls">
        <!-- Filtro por Género -->
        <div class="filter-group">
          <label for="genreSelect">Género:</label>
          <select id="genreSelect">
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
          <label for="yearSelect">Año:</label>
          <select id="yearSelect">
            <option value="">Todos los años</option>
            @php
              $currentYear = date('Y');
              for ($year = $currentYear; $year >= 1900; $year--) {
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
          <label for="sortSelect">Ordenar por:</label>
          <select id="sortSelect">
            <option value="popularity.desc">Popularidad</option>
            <option value="vote_average.desc">Mejor valoradas</option>
            <option value="release_date.desc">Más recientes</option>
            <option value="release_date.asc">Más antiguas</option>
          </select>
        </div>
      </div>

      <div class="filter-actions">
        <button id="applyFilters" class="btn btn-primary">Aplicar filtros</button>
        <button id="resetFilters" class="btn btn-secondary">Limpiar filtros</button>
      </div>
    </div>
  </section>

  <!-- SECCIÓN DE PELÍCULAS DESTACADAS -->
  <section class="featured-section">
    <h2>Películas destacadas</h2>
    <div class="featured-slider" id="featuredSlider">
      <!-- Los slides se generarán dinámicamente con JS -->
    </div>
    <div class="slider-controls">
      <button id="prevSlide" class="slide-control"><i class="fas fa-chevron-left"></i></button>
      <button id="nextSlide" class="slide-control"><i class="fas fa-chevron-right"></i></button>
    </div>
  </section>

  <!-- SECCIÓN: TODAS LAS PELÍCULAS -->
  <section class="movies-section">
    <div class="section-header">
      <h2>Explorar películas</h2>
      <div class="view-toggle">
        <button id="gridView" class="toggle-btn active"><i class="fas fa-th"></i></button>
        <button id="listView" class="toggle-btn"><i class="fas fa-list"></i></button>
      </div>
    </div>

    <div class="movies-container grid-view" id="moviesContainer">
      @foreach($movies as $movie)
        @php
          // Intentamos obtener datos desde TMDB
          $apiKey = env('TMDB_API_KEY');
          $tmdbId = $movie->api_id ?? $movie->id;
          $posterUrl = asset('images/no-poster.jpg');
          $backdropUrl = null;
          $rating = 0;
          $genres = [];

          try {
              $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$apiKey}&language=es-ES&append_to_response=credits");
              if ($response->successful()) {
                  $movieData = $response->json();
                  $posterUrl = !empty($movieData['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w500' . $movieData['poster_path']
                    : asset('images/no-poster.jpg');
                  $backdropUrl = !empty($movieData['backdrop_path'])
                    ? 'https://image.tmdb.org/t/p/w1280' . $movieData['backdrop_path']
                    : null;
                  $rating = $movieData['vote_average'] ?? 0;
                  $genreNames = array_map(function($genre) {
                      return $genre['name'];
                  }, $movieData['genres'] ?? []);
                  $genres = implode(', ', array_slice($genreNames, 0, 3));
                  // Se puede obtener director si es necesario, aquí se omite para simplificar
              }
          } catch (\Exception $e) {
              // Mantener valores predeterminados en caso de error
          }

          // Convertir rating a escala de 5 estrellas
          $starRating = round($rating / 2, 1);
        @endphp

        <div class="movie-card"
             data-id="{{ $movie->id }}"
             data-title="{{ strtolower($movie->titulo) }}"
             data-year="{{ $movie->año_estreno }}"
             data-rating="{{ $rating }}"
             data-genres="{{ $genres }}">
          <div class="movie-poster">
            <img src="{{ $posterUrl }}" alt="{{ $movie->titulo }}" loading="lazy">
            <div class="movie-badges">
              @if($movie->año_estreno >= date('Y') - 1)
                <span class="badge new-badge">Nuevo</span>
              @endif
              @if($rating >= 8)
                <span class="badge top-badge">Top</span>
              @endif
            </div>
            <div class="movie-actions">
              <button class="action-btn btn-trailer" data-id="{{ $tmdbId }}">
                <i class="fas fa-play"></i>
              </button>
              <button class="action-btn btn-favorite" data-id="{{ $movie->id }}">
                <i class="far fa-heart"></i>
              </button>
              <button class="action-btn btn-details" data-id="{{ $movie->id }}">
                <i class="fas fa-info-circle"></i>
              </button>
            </div>
          </div>
          <div class="movie-info">
            <h3>{{ $movie->titulo }}</h3>
            <div class="movie-meta">
              <span class="year">{{ $movie->año_estreno }}</span>
              <span class="divider">•</span>
              <span class="rating">
                @for($i = 1; $i <= 5; $i++)
                  @if($i <= floor($starRating))
                    <i class="fas fa-star"></i>
                  @elseif($i - 0.5 <= $starRating)
                    <i class="fas fa-star-half-alt"></i>
                  @else
                    <i class="far fa-star"></i>
                  @endif
                @endfor
                <span class="rating-value">{{ number_format($rating, 1) }}</span>
              </span>
            </div>
            <p class="genres">{{ $genres }}</p>
            <a href="{{ route('pelicula.detail', $movie->id) }}" class="btn-more">Ver más</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="pagination-container">
      <button id="loadMoreBtn" class="load-more-btn">
        <span>Cargar más películas</span>
        <i class="fas fa-spinner fa-spin d-none"></i>
      </button>
    </div>
  </section>

  <!-- MODAL PARA DETALLES DE PELÍCULA -->
  <div id="movieModal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
      <button class="modal-close"><i class="fas fa-times"></i></button>
      <div class="modal-body" id="modalBody">
        <div class="modal-loading">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Cargando información...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL PARA TRAILER -->
  <div id="trailerModal" class="modal trailer-modal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
      <button class="modal-close"><i class="fas fa-times"></i></button>
      <div class="modal-body">
        <div id="trailerContainer"></div>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  const API_KEY = "{{ config('tmdb.api_key') }}";
  const BASE_URL = "{{ config('tmdb.base_url') }}";
  const IMG_URL = "{{ config('tmdb.img_url') }}";
  const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
  const MOVIE_ENDPOINT = "{{ route('pelicula.detail', '') }}";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
<script type="module" src="{{ asset('movies.js') }}"></script>
@endpush
