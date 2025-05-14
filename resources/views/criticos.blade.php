@extends('layouts.app')

@section('title', __('messages.critics') . ' - CritFlix')


@push('styles')
  <link rel="stylesheet" href="{{ asset('criticos.css') }}">
@endpush

@section('content')
<main class="critiflix-container">
    <!-- HERO SECTION -->
    <section class="hero-section critic-hero">
        <div class="hero-content">
            <h1>{{ __('messages.featured_critics_caps') }}</h1>
            <p>{{ __('messages.discover_influential_voices') }}</p>
        </div>
    </section>

    <!-- FILTRO DE CRÍTICOS -->
    <section class="filter-section">
        <div class="filter-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="criticSearch" placeholder="{{ __('messages.search_critic_placeholder') }}">
            </div>
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="genreFilter">{{ __('messages.specialty') }}</label>
                    <select id="genreFilter">
                        <option value="">{{ __('messages.all_genres') }}</option>
                        <option value="accion">{{ __('messages.action') }}</option>
                        <option value="drama">{{ __('messages.drama') }}</option>
                        <option value="comedia">{{ __('messages.comedy') }}</option>
                        <option value="ciencia-ficcion">{{ __('messages.sci_fi') }}</option>
                        <option value="terror">{{ __('messages.horror') }}</option>
                        <option value="documental">{{ __('messages.documentary') }}</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sortBy">{{ __('messages.sort_by') }}</label>
                    <select id="sortBy">
                        <option value="popular">{{ __('messages.most_popular') }}</option>
                        <option value="recent">{{ __('messages.most_recent') }}</option>
                        <option value="rating">{{ __('messages.highest_rated') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- CRÍTICOS DESTACADOS GRID -->
    <section class="critics-grid-section">
        <h2 class="section-title">{{ __('messages.most_influential') }}</h2>
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
                            <p class="critic-bio">{{ Str::limit($critic->biografia ?? __('messages.default_critic_bio'), 120) }}</p>
                            <div class="critic-specialties">
                                @foreach(explode(',', $critic->especialidad) as $genre)
                                    <span class="specialty-tag">{{ trim($genre) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="critic-actions">
                            <button class="btn btn-secondary follow-btn" data-id="{{ $critic->id }}">
                                <i class="fas fa-user-plus"></i> {{ __('messages.follow') }}
                            </button>
                            <a href="{{ route('criticos.perfil', $critic->id) }}" class="btn btn-primary">{{ __('messages.view_profile') }}</a>
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
        <h2 class="section-title">{{ __('messages.trending_reviews') }}</h2>
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
                        <a href="#" class="read-more-link">{{ __('messages.read_more') }} <i class="fas fa-chevron-right"></i></a>
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
            <h2>{{ __('messages.have_critical_voice') }}</h2>
            <p>{{ __('messages.share_opinions') }}</p>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-ticket-alt"></i>
                    <h4>{{ __('messages.previews') }}</h4>
                    <p>{{ __('messages.exclusive_previews') }}</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-trophy"></i>
                    <h4>{{ __('messages.recognition') }}</h4>
                    <p>{{ __('messages.stand_out') }}</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-video"></i>
                    <h4>{{ __('messages.exclusive_content') }}</h4>
                    <p>{{ __('messages.behind_scenes') }}</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-comments"></i>
                    <h4>{{ __('messages.community') }}</h4>
                    <p>{{ __('messages.connect_cinephiles') }}</p>
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
