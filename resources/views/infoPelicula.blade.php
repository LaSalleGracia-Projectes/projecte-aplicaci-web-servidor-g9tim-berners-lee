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
=======
<main class="pelicula-detalle">
    <!-- Banner grande -->
    <div class="banner">
        <img src="https://image.tmdb.org/t/p/original{{ $pelicula['backdrop_path'] }}" alt="{{ $pelicula['title'] }}">
        <div class="titulo-overlay">
            <h1>{{ $pelicula['title'] }}</h1>
            <p class="tagline">{{ $pelicula['tagline'] }}</p>
        </div>
    </div>

    <div class="info-container">
                    <div class="info">
                <div class="datos-basicos">
                    <p><strong>Fecha de estreno:</strong> {{ $pelicula['release_date'] }}</p>
                    <p><strong>Duración:</strong> {{ $pelicula['runtime'] }} min</p>
                    <p><strong>Géneros:</strong> {{ implode(', ', array_column($pelicula['genres'], 'name')) }}</p>
                    <p><strong>Calificación:</strong> <span class="estrellas">⭐ {{ number_format($pelicula['vote_average'], 1) }}</span></p>
                </div>

                <div class="sinopsis">
                    <h3>Sinopsis</h3>
                    <p>{{ $pelicula['overview'] }}</p>
                </div>

                <div class="produccion">
                    <p><strong>Director:</strong> {{ $director['name'] ?? 'Información no disponible' }}</p>
                    <p><strong>Productora:</strong> {{ implode(', ', array_column($pelicula['production_companies'], 'name')) }}</p>
                </div>

                <br>

                <div class="elenco">
                    <h3>Reparto principal</h3>
                    <div class="actores">
                        @foreach($elenco as $actor)
                        @if($loop->index < 10)
                            <div class="actor">
                                <img src="https://image.tmdb.org/t/p/w138_and_h175_face{{ $actor['profile_path'] }}"
                                    alt="{{ $actor['name'] }}"
                                    onerror="this.src='/img/perfil-default.jpg'">
                                <div class="actor-info">
                                    <p class="actor-nombre">{{ $actor['name'] }}</p>
                                    <p class="actor-personaje">{{ $actor['character'] }}</p>
                                </div>
                            </div>
                            @endif
                            @endforeach
                    </div>
                </div>
            </div>
        <div class="columna-izquierda">
            <div class="poster">
                <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] }}" alt="{{ $pelicula['title'] }}">
            </div>

            <div class="donde-ver">
                <h3>Disponible en:</h3>
                <div class="plataformas">
                    @forelse($watchProviders['flatrate'] ?? [] as $plataforma)
                    <div class="plataforma">
                        <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}" alt="{{ $plataforma['provider_name'] }}">
                        <span>{{ $plataforma['provider_name'] }}</span>
                    </div>
                    @empty
                    <p>Información no disponible</p>
                    @endforelse
                </div>
            </div>
        </div>
</main>

<style>

:root {
    /* Colores principales */
    --verde-neon: #14ff14;
    --verde-principal: #00ff3c;
    --verde-claro: #00ffdd;
    --blanco: #FFFFFF;
    --negro: #000000;
    --negro-suave: #121212;
    --azul-oscuro: #001233;
    /* Colores de estados */
    --verde-pastel: #66BB6A;
    --verde-oscuro: #1B5E20;
    --rojo-suave: #E53935;
}
    .pelicula-detalle {
        max-width: 1000px;
        margin: auto;
        padding: 0 15px;
    }

    .banner {
        position: relative;

    }

    .banner img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        filter: brightness(0.7);
        margin-top: 10px;
    }

    .titulo-overlay {
        left: 30px;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    .titulo-overlay h1 {
        margin-bottom: 5px;
        font-size: 4rem;
        margin-top: 30px;
    }

    .titulo-overlay .tagline {
        font-style: italic;
    }

    .info-container {
    display: flex;
    flex-direction: row-reverse;
    align-items: flex-start;
    gap: 15px;
}
    .columna-izquierda {
        width: 200px;
        flex-shrink: 0;
    }

    .poster img {
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }

    .donde-ver {
        margin-top: 20px;
        padding: 15px;
        background-color:var(--verde-neon);
        border-radius: 10px;
        color:var(--negro)
    }

    .plataformas {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .plataforma {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .plataforma img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .info {
        flex-grow: 1;
    }

    .datos-basicos {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .estrellas {
        color: #FFC107;
        font-weight: bold;
    }

    .sinopsis {
        margin-bottom: 25px;
    }

    .elenco {
        margin-bottom: 25px;
    }

    .actores {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 2fr));
        gap: 15px;
        margin-top: 15px;
    }

    .actor {
        display: flex;
        flex-direction: column;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        transition: 0.3s;
    }

    .actor:hover {
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.5);
        scale: 1.05;
        cursor: pointer;
    }

    .actor img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .actor-info {
        padding: 10px;
        background-color:var(--verde-neon);
    }

    .actor-nombre {
        font-weight: bold;
        margin-bottom: 3px;
        color:var(--negro);
    }

    .actor-personaje {
        font-size: 0.9rem;
        color:var(--negro);
    }

    .produccion {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .info-container {
            flex-direction: column;
        }

        .columna-izquierda {
            width: 100%;
        }

        .poster img {
            max-width: 250px;
            margin: 0 auto;
            display: block;
        }

        .datos-basicos {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
@stack('styles')
  <script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
</script>
<script type="module" src="{{ asset('movie-details.js') }}"></script>

