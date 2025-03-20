@extends('layouts.app')

@section('title', 'Críticos - CritiFlix')


@push('styles')
  <link rel="stylesheet" href="{{ asset('criticos.css') }}">
@endpush

@section('content')
<main class="critiflix-container">
    <!-- HERO SECTION -->
    <section class="hero-section critic-hero">
        <div class="hero-content">
            <h1>CRÍTICOS DESTACADOS</h1>
            <p>Descubre las voces más influyentes en el mundo de la crítica cinematográfica</p>
        </div>
    </section>

    <!-- FILTRO DE CRÍTICOS -->
    <section class="filter-section">
        <div class="filter-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="criticSearch" placeholder="Buscar crítico por nombre o especialidad...">
            </div>
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="genreFilter">Especialidad</label>
                    <select id="genreFilter">
                        <option value="">Todos los géneros</option>
                        <option value="accion">Acción</option>
                        <option value="drama">Drama</option>
                        <option value="comedia">Comedia</option>
                        <option value="ciencia-ficcion">Ciencia Ficción</option>
                        <option value="terror">Terror</option>
                        <option value="documental">Documental</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sortBy">Ordenar por</label>
                    <select id="sortBy">
                        <option value="popular">Más populares</option>
                        <option value="recent">Más recientes</option>
                        <option value="rating">Mejor calificados</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- CRÍTICOS DESTACADOS GRID -->
    <section class="critics-grid-section">
        <h2 class="section-title">Los Más Influyentes</h2>
        <div class="critics-grid" id="criticsGrid">
            @foreach($topCritics as $critic)
                <div class="critic-card" data-genre="{{ $critic->especialidad }}">
                    <div class="critic-card-inner">
                        <div class="critic-image">
                            <img src="{{ $critic->foto_perfil ?? asset('images/default-avatar.jpg') }}" alt="{{ $critic->nombre_usuario }}">
                            @if($critic->verificado)
                                <span class="verified-badge"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </div>
                        <div class="critic-info">
                            <h3>{{ $critic->nombre_usuario }}</h3>
                            <div class="critic-stats">
                                <span class="rating"><i class="fas fa-star"></i> {{ number_format($critic->calificacion, 1) }}</span>
                                <span class="reviews"><i class="fas fa-film"></i> {{ $critic->total_resenas }}</span>
                                <span class="followers"><i class="fas fa-users"></i> {{ $critic->seguidores }}</span>
                            </div>
                            <p class="critic-bio">{{ Str::limit($critic->biografia ?? 'Crítico de cine apasionado por compartir análisis profundos y perspectivas únicas sobre las películas.', 120) }}</p>
                            <div class="critic-specialties">
                                @foreach(explode(',', $critic->especialidad) as $genre)
                                    <span class="specialty-tag">{{ trim($genre) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="critic-actions">
                            <button class="btn btn-secondary follow-btn" data-id="{{ $critic->id }}">
                                <i class="fas fa-user-plus"></i> Seguir
                            </button>
                            <a href="{{ route('criticos.perfil', $critic->id) }}" class="btn btn-primary">Ver Perfil</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pagination-container">
            {{ $topCritics->links() }}
        </div>
    </section>

    <!-- RESEÑAS TRENDING -->
    <section class="trending-reviews-section">
        <h2 class="section-title">Reseñas Trending</h2>
        <div class="trending-reviews-slider" id="trendingReviews">
            @foreach($trendingReviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <img src="{{ $review->usuario->foto_perfil ?? asset('images/default-avatar.jpg') }}" alt="{{ $review->usuario->nombre_usuario }}">
                        <div class="review-meta">
                            <h4>{{ $review->usuario->nombre_usuario }}</h4>
                            <div class="movie-info">
                                <span class="movie-title">{{ $review->pelicula->titulo }}</span>
                                <span class="movie-year">({{ date('Y', strtotime($review->pelicula->fecha_lanzamiento)) }})</span>
                            </div>
                        </div>
                        <div class="review-rating">
                            <span>{{ $review->calificacion }}</span>/5
                        </div>
                    </div>
                    <div class="review-content">
                        <p>{{ Str::limit($review->contenido, 150) }}</p>
                    </div>
                    <div class="review-footer">
                        <div class="review-stats">
                            <span><i class="fas fa-thumbs-up"></i> {{ $review->likes }}</span>
                            <span><i class="fas fa-comment"></i> {{ $review->comentarios->count() }}</span>
                            <span><i class="fas fa-eye"></i> {{ $review->vistas }}</span>
                        </div>
                        <a href="#" class="read-more-link">Leer más <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="slider-controls">
            <button class="nav-btn" id="reviewsPrev"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn" id="reviewsNext"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- CONVIÉRTETE EN CRÍTICO -->
    <section class="become-critic-section">
        <div class="become-critic-content">
            <h2>¿Tienes una voz crítica?</h2>
            <p>Comparte tus opiniones y análisis con nuestra comunidad. Conviértete en un crítico verificado y obtén acceso a estrenos exclusivos.</p>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-ticket-alt"></i>
                    <h4>Preestrenos</h4>
                    <p>Acceso a preestrenos exclusivos</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-trophy"></i>
                    <h4>Reconocimiento</h4>
                    <p>Destaca en la comunidad de críticos</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-video"></i>
                    <h4>Contenido Exclusivo</h4>
                    <p>Material detrás de cámaras</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-comments"></i>
                    <h4>Comunidad</h4>
                    <p>Conecta con otros cinéfilos</p>
                </div>
            </div>
          
        </div>
    </section>
</main>
@endsection

@stack('styles')
  <script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
</script>
<script type="module" src="{{ asset('criticos.js') }}"></script>
