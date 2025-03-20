@extends('layouts.app')

@section('title', $pelicula->titulo . ' - CrítiFlix')

@push('styles')
    <link rel="stylesheet" href="{{ asset('movie-details.css') }}">
@endpush

@section('content')
<main class="movie-detail">
    <div class="movie-header">
        <div class="movie-poster">
            <img src="{{ $pelicula->poster_url ?? asset('images/no-poster.jpg') }}" alt="{{ $pelicula->titulo }}">
        </div>
        <div class="movie-info">
            <h1>{{ $pelicula->titulo }}</h1>
            <div class="movie-meta">
                <span class="rating">
                    <i class="fas fa-star"></i>
                    {{ $pelicula->tmdb_rating ?? 'N/A' }}/10
                </span>
                <span class="year">
                    <i class="far fa-calendar-alt"></i>
                    {{ $pelicula->año_estreno }}
                </span>
                @if($pelicula->duracion)
                    <span class="duration">
                        <i class="far fa-clock"></i>
                        {{ $pelicula->duracion }} min
                    </span>
                @endif
            </div>
            <div class="movie-description">
                <h2>Sinopsis</h2>
                <p>{{ $pelicula->sinopsis ?? 'No hay sinopsis disponible.' }}</p>
            </div>
            @if($pelicula->elenco)
                <div class="movie-cast">
                    <h2>Reparto</h2>
                    <p>{{ $pelicula->elenco }}</p>
                </div>
            @endif

            <!-- Botones de acción -->
            <div class="action-buttons">
                <button class="btn-favorite">
                    <i class="far fa-heart"></i> Favorito
                </button>
                <button class="btn-watchlist">
                    <i class="far fa-bookmark"></i> Ver más tarde
                </button>
                <button class="btn-share">
                    <i class="fas fa-share-alt"></i> Compartir
                </button>
            </div>
        </div>
    </div>

    <div class="movie-content">
        <!-- Tabs de navegación -->
        <div class="movie-tabs">
            <button class="tab-button active" data-tab="reviews">Críticas</button>
            <button class="tab-button" data-tab="similar">Películas similares</button>
            <button class="tab-button" data-tab="details">Detalles técnicos</button>
        </div>

        <!-- Contenido de los tabs -->
        <div class="tab-content">
            <!-- Críticas -->
            <section id="reviews" class="tab-panel active">
                <h2>Críticas de usuarios</h2>
                <div class="add-review">
                    <h3>¿Ya la viste? Deja tu crítica</h3>
                    <div class="rating-selector">
                        <span>Tu puntuación:</span>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="far fa-star" data-rating="{{ $i }}"></i>
                            @endfor
                        </div>
                    </div>
                    <textarea placeholder="Escribe tu opinión sobre esta película..."></textarea>
                    <button class="btn-submit-review">Publicar crítica</button>
                </div>

                <div class="reviews-list">
                    <p class="no-reviews">Aún no hay críticas para esta película. ¡Sé el primero en opinar!</p>
                    <!-- Aquí se cargarán dinámicamente las críticas -->
                </div>
            </section>

            <!-- Películas similares -->
            <section id="similar" class="tab-panel">
                <h2>Películas similares</h2>
                <div class="related-movies-container">
                    @php
                        // Obtener películas del mismo año
                        $relatedMovies = App\Models\PeliculasSeries::where('tipo', 'pelicula')
                                        ->where('año_estreno', $pelicula->año_estreno)
                                        ->where('id', '!=', $pelicula->id)
                                        ->limit(4)
                                        ->get();

                        // Si no hay suficientes, mostrar algunas aleatorias
                        if ($relatedMovies->count() < 4) {
                            $moreMovies = App\Models\PeliculasSeries::where('tipo', 'pelicula')
                                        ->where('id', '!=', $pelicula->id)
                                        ->whereNotIn('id', $relatedMovies->pluck('id')->toArray())
                                        ->inRandomOrder()
                                        ->limit(4 - $relatedMovies->count())
                                        ->get();

                            $relatedMovies = $relatedMovies->concat($moreMovies);
                        }
                    @endphp

                    @if($relatedMovies->count() > 0)
                        @foreach($relatedMovies as $relatedMovie)
                            @php
                                // Obtener poster de TMDB
                                $apiKey = env('TMDB_API_KEY');
                                $tmdbId = $relatedMovie->api_id ?? $relatedMovie->id;
                                $posterUrl = asset('images/no-poster.jpg');
                                $rating = 0;

                                try {
                                    $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$apiKey}&language=es-ES");
                                    if (!$response->failed()) {
                                        $movieData = $response->json();
                                        $posterUrl = 'https://image.tmdb.org/t/p/w500' . ($movieData['poster_path'] ?? '');
                                        $rating = $movieData['vote_average'] ?? 0;
                                    }
                                } catch (\Exception $e) {
                                    // Usar poster predeterminado si hay error
                                }
                            @endphp
                            <div class="movie-card">
                                <img src="{{ $posterUrl }}" alt="{{ $relatedMovie->titulo }}">
                                <div class="movie-info">
                                    <h3>{{ $relatedMovie->titulo }}</h3>
                                    <p class="rating">{{ $rating }}/10</p>
                                    <a href="{{ route('pelicula.detail', $relatedMovie->id) }}" class="btn-details">Ver Detalles</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No se encontraron películas similares.</p>
                    @endif
                </div>
            </section>

            <!-- Detalles técnicos -->
            <section id="details" class="tab-panel">
                <h2>Detalles técnicos</h2>
                <div class="technical-details">
                    <div class="detail-item">
                        <span class="detail-label">Título original:</span>
                        <span class="detail-value">{{ $pelicula->titulo }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Año de estreno:</span>
                        <span class="detail-value">{{ $pelicula->año_estreno }}</span>
                    </div>
                    @if($pelicula->duracion)
                    <div class="detail-item">
                        <span class="detail-label">Duración:</span>
                        <span class="detail-value">{{ $pelicula->duracion }} minutos</span>
                    </div>
                    @endif
                    @if($pelicula->elenco)
                    <div class="detail-item">
                        <span class="detail-label">Reparto principal:</span>
                        <span class="detail-value">{{ $pelicula->elenco }}</span>
                    </div>
                    @endif
                    <div class="detail-item">
                        <span class="detail-label">ID de TMDB:</span>
                        <span class="detail-value">{{ $pelicula->api_id ?? 'No disponible' }}</span>
                    </div>

                </div>
            </section>
        </div>
    </div>

    <div class="back-button">
        <a href="{{ route('peliculas') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Volver a películas
        </a>
    </div>
</main>
@endsection
@stack('styles')
  <script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
</script>
<script type="module" src="{{ asset('movie-details.js') }}"></script>

