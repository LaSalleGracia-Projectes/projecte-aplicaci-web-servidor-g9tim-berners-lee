@extends('layouts.app')

@section('title', isset($pelicula['title']) ? $pelicula['title'] : ($pelicula->titulo ?? 'Detalle de película'))

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="tmdb-api-key" content="{{ env('TMDB_API_KEY') }}">
<meta name="movie-id" content="{{ $pelicula['id'] ?? $pelicula->tmdb_id ?? '' }}">
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('movie-details.css') }}">
<link rel="stylesheet" href="{{ asset('movie-details-fixed.css') }}">
<link rel="stylesheet" href="{{ asset('css/movie-details-modal.css') }}">
<style>
/* Estilos básicos para películas */
.pelicula-detalle {
    max-width: 1200px;
    margin: auto;
    padding: 0 15px;
}

/* Estilos para el sistema de notificaciones */
#notifications-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    max-width: 400px;
}

.notification {
    margin-bottom: 10px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    font-weight: 500;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 280px;
    max-width: 400px;
}

.notification.success {
    background-color: #14ff14;
    color: #000;
}

.notification.error {
    background-color: #ff4d4d;
}

.notification.warning {
    background-color: #ffbb33;
    color: #000;
}

.notification.info {
    background-color: #33b5e5;
}

/* Estilos para el modal de trailers */
#trailerModalStatic {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.9);
    z-index: 9999;
}

.modal-content {
    position: relative;
    width: 90%;
    max-width: 1000px;
    height: 90%;
    max-height: 600px;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

#trailerContainerStatic {
    position: relative;
    width: 100%;
    height: 100%;
    background-color: #000;
}

#closeTrailerBtn {
    position: absolute;
    top: -40px;
    right: 0;
    background: transparent;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    z-index: 10;
}

/* Estilos para el sistema de comentarios */
.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.review {
    background-color: #121212;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #14ff14;
    box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #14ff14;
}

.username {
    color: #00ffdd;
    font-weight: bold;
}

.review-date {
    color: #FFFFFF;
    font-size: 0.8rem;
    opacity: 0.7;
}

.review-rating {
    color: #FFC107;
}

.review-content {
    color: #FFFFFF;
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Estilos para respuestas a comentarios */
.respuestas-container {
    margin-top: 10px;
    margin-left: 30px;
    border-left: 2px solid var(--verde-neon);
    padding-left: 15px;
}

.respuesta {
    background-color: rgba(0, 255, 135, 0.05);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
}

.respuesta-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.respuesta-content {
    margin-top: 10px;
    line-height: 1.5;
}

.respuesta-form {
    margin-top: 15px;
    background: rgba(0, 0, 0, 0.3);
    padding: 15px;
    border-radius: 8px;
}

.respuesta-form textarea {
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    border: 1px solid var(--verde-neon);
    color: var(--blanco);
    padding: 10px;
    border-radius: 5px;
    min-height: 80px;
    margin-bottom: 10px;
}

.respuesta-form button {
    background: var(--verde-neon);
    color: var(--negro);
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease;
}

.respuesta-form button:hover {
    background: var(--verde-claro);
    transform: translateY(-2px);
}

.respuesta-text {
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    border: 1px solid var(--verde-neon);
    color: var(--blanco);
    padding: 10px;
    border-radius: 5px;
    min-height: 80px;
    margin-bottom: 10px;
}

.respuesta-form-container {
    margin-top: 10px;
    margin-bottom: 15px;
}

.btn-reply {
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn-reply i {
    font-size: 0.9em;
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-10px); }
}

.notification.hide {
    animation: fadeOut 0.3s ease-out forwards;
}
</style>
@endpush

@section('content')
<main class="pelicula-detalle">
    <!-- Banner grande con título -->
    <div class="banner">
        <img src="{{ $posterBackgroundUrl ?? $pelicula['backdrop_path'] ? 'https://image.tmdb.org/t/p/original'.$pelicula['backdrop_path'] : asset('img/banner-default.jpg') }}" alt="{{ $pelicula['title'] ?? $pelicula->titulo ?? '' }}">
        <div class="titulo-overlay">
            <h1>{{ $pelicula['title'] ?? $pelicula->titulo ?? '' }}</h1>
            @if(!empty($pelicula['tagline'] ?? ''))
                <p class="tagline">{{ $pelicula['tagline'] }}</p>
            @endif
        </div>
    </div>

    <div class="movie-content-container">
        <!-- Columna izquierda: poster y servicios -->
        <div class="columna-izquierda">
            <!-- Poster de la película -->
            <div class="poster">
                <img src="{{ isset($pelicula['poster_path']) && $pelicula['poster_path']
                    ? 'https://image.tmdb.org/t/p/w500'.$pelicula['poster_path']
                    : ($pelicula->poster_url ?? asset('img/no-poster.jpg')) }}"
                     alt="{{ $pelicula['title'] ?? $pelicula->titulo ?? '' }}">
            </div>

            <!-- Botones de acción -->
            @auth
            <div class="movie-actions">
                <div class="action-buttons">
                    <button class="btn-favorite" data-id="{{ $pelicula['id'] ?? $pelicula->tmdb_id ?? '' }}" title="Añadir a favoritos">
                        <i class="far fa-heart"></i> <span>Favorito</span>
                    </button>
                    <button class="btn-watchlist" title="Añadir a lista de visualización">
                        <i class="far fa-bookmark"></i> <span>Ver más tarde</span>
                    </button>
                    <button class="btn-share" title="Compartir">
                        <i class="fas fa-share-alt"></i> <span>Compartir</span>
                    </button>
                    <button class="btn-goto-reviews" id="btn-ver-criticas" title="Ver críticas">
                        <i class="fas fa-comments"></i> Ver críticas
                    </button>
                </div>
            </div>
            @endauth

            <!-- Dónde ver (servicios de streaming) -->
            @if(isset($watchProviders['flatrate']) || isset($watchProviders['rent']) || isset($watchProviders['buy']))
            <div class="streaming-services">
                <h2>Dónde ver</h2>

                @if(isset($watchProviders['flatrate']))
                <div class="service-section">
                    <h3>Streaming</h3>
                    <div class="services-grid">
                        @foreach($watchProviders['flatrate'] as $provider)
                        <div class="service-item">
                            <img src="https://image.tmdb.org/t/p/original{{ $provider['logo_path'] }}" alt="{{ $provider['provider_name'] }}">
                            <span>{{ $provider['provider_name'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($watchProviders['rent']))
                <div class="service-section">
                    <h3>Alquiler</h3>
                    <div class="services-grid">
                        @foreach($watchProviders['rent'] as $provider)
                        <div class="service-item">
                            <img src="https://image.tmdb.org/t/p/original{{ $provider['logo_path'] }}" alt="{{ $provider['provider_name'] }}">
                            <span>{{ $provider['provider_name'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($watchProviders['buy']))
                <div class="service-section">
                    <h3>Compra</h3>
                    <div class="services-grid">
                        @foreach($watchProviders['buy'] as $provider)
                        <div class="service-item">
                            <img src="https://image.tmdb.org/t/p/original{{ $provider['logo_path'] }}" alt="{{ $provider['provider_name'] }}">
                            <span>{{ $provider['provider_name'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Columna central: información detallada -->
        <div class="columna-central">
            <!-- Metadatos -->
            <div class="movie-meta">
                @if(isset($pelicula['vote_average']) || isset($pelicula->tmdb_rating))
                <span class="rating" title="Puntuación">
                    <i class="fas fa-star"></i>
                    {{ $pelicula['vote_average'] ? number_format($pelicula['vote_average'], 1) : ($pelicula->tmdb_rating ?? 'N/A') }}/10
                </span>
                @endif

                @if(isset($pelicula['release_date']) || isset($pelicula->año_estreno))
                <span class="year" title="Año de estreno">
                    <i class="far fa-calendar-alt"></i>
                    {{ isset($pelicula['release_date']) ? substr($pelicula['release_date'], 0, 4) : $pelicula->año_estreno }}
                </span>
                @endif

                @if(isset($pelicula['runtime']) || isset($pelicula->duracion))
                <span class="duration" title="Duración">
                    <i class="far fa-clock"></i>
                    {{ $pelicula['runtime'] ?? $pelicula->duracion ?? 'N/A' }} min
                </span>
                @endif

                @if(isset($pelicula['genres']) && is_array($pelicula['genres']))
                <span class="genres" title="Géneros">
                    <i class="fas fa-film"></i>
                    {{ implode(', ', array_column($pelicula['genres'], 'name')) }}
                </span>
                @endif
            </div>

            <!-- Sinopsis -->
            <div class="sinopsis">
                <h2>Sinopsis</h2>
                <p>{{ $pelicula['overview'] ?? $pelicula->sinopsis ?? 'No hay sinopsis disponible.' }}</p>
            </div>

            <!-- Producción y detalles técnicos -->
            <div class="produccion">
                <h2>Detalles técnicos</h2>
                <div class="production-details">
                    @if(isset($director) && !empty($director))
                    <div class="detail-item">
                        <span class="detail-label">Director:</span>
                        <span class="detail-value">{{ $director['name'] ?? 'No disponible' }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['production_companies']) && count($pelicula['production_companies']) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Productora:</span>
                        <span class="detail-value">
                            @foreach($pelicula['production_companies'] as $index => $company)
                                {{ $company['name'] }}{{ $index < count($pelicula['production_companies']) - 1 ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                    @endif

                    @if(isset($pelicula['production_countries']) && count($pelicula['production_countries']) > 0)
                    <div class="detail-item">
                        <span class="detail-label">País:</span>
                        <span class="detail-value">
                            @foreach($pelicula['production_countries'] as $index => $country)
                                {{ $country['name'] }}{{ $index < count($pelicula['production_countries']) - 1 ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                    @endif

                    @if(isset($pelicula['budget']) && $pelicula['budget'] > 0)
                    <div class="detail-item">
                        <span class="detail-label">Presupuesto:</span>
                        <span class="detail-value">${{ number_format($pelicula['budget'], 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['revenue']) && $pelicula['revenue'] > 0)
                    <div class="detail-item">
                        <span class="detail-label">Recaudación:</span>
                        <span class="detail-value">${{ number_format($pelicula['revenue'], 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['id']) || isset($pelicula->api_id))
                    <div class="detail-item">
                        <span class="detail-label">ID de TMDB:</span>
                        <span class="detail-value">{{ $pelicula['id'] ?? $pelicula->api_id ?? 'No disponible' }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['status']))
                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">{{ $pelicula['status'] }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reparto principal -->
            <div class="elenco">
                <h2>Reparto principal</h2>
                <div class="actores-container">
                    @if(isset($elenco) && is_array($elenco) && count($elenco) > 0)
                        @foreach($elenco as $actor)
                            @if($loop->index < 12)
                            <div class="actor" data-actor-id="{{ $actor['id'] ?? '' }}">
                                <img src="{{ isset($actor['profile_path']) && $actor['profile_path']
                                    ? 'https://image.tmdb.org/t/p/w138_and_h175_face'.$actor['profile_path']
                                    : asset('img/perfil-default.jpg') }}"
                                    alt="{{ $actor['name'] }}"
                                    onerror="this.src='{{ asset('img/perfil-default.jpg') }}'">
                                <div class="actor-info">
                                    <p class="actor-nombre">{{ $actor['name'] }}</p>
                                    <p class="actor-personaje">{{ $actor['character'] }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @elseif(isset($pelicula->elenco))
                        <p>{{ $pelicula->elenco }}</p>
                    @else
                        <p>No hay información de elenco disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Tabs de navegación -->
            <div class="movie-tabs">
                <button class="tab-button active" data-tab="reviews">Críticas</button>
                <button class="tab-button" data-tab="similar">Películas similares</button>
                @if(isset($pelicula['videos']) && count($pelicula['videos']['results'] ?? []) > 0)
                <button class="tab-button" data-tab="videos">Trailers</button>
                @endif
            </div>

            <!-- Contenido de los tabs -->
            <div class="tab-content">
                <!-- Críticas -->
                <section id="reviews" class="tab-panel active">
                    <h2>Críticas de usuarios</h2>
                    <div class="add-review">
                        <h3>¿Ya la viste? Deja tu crítica</h3>
                        <form id="comentarioForm">
                            <input type="hidden" name="id_pelicula" value="{{ $pelicula['id'] ?? $pelicula->tmdb_id }}">
                            <div class="rating-selector">
                                <span>Tu puntuación:</span>
                                <div class="stars" role="radiogroup" aria-label="Puntuación">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="far fa-star" data-rating="{{ $i }}" role="radio" aria-label="{{ $i }} estrellas" tabindex="0"></i>
                                    @endfor
                                </div>
                            </div>
                            <textarea name="comentario" placeholder="Escribe tu opinión sobre esta película..." aria-label="Tu crítica" required></textarea>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="es_spoiler" name="es_spoiler">
                                <label class="form-check-label" for="es_spoiler">Esta crítica contiene spoilers</label>
                            </div>
                            <button type="submit" class="btn-submit-review">Publicar crítica</button>
                        </form>
                    </div>

                    <div class="reviews-list">
                        <!-- Los comentarios se cargarán dinámicamente aquí -->
                    </div>
                </section>

                <!-- Películas similares -->
                <section id="similar" class="tab-panel">
                    <h2 class="section-title">
                        <i class="fas fa-film"></i> Películas similares
                    </h2>
                    <div class="related-movies-container">
                        @if(isset($peliculasSimilares) && count($peliculasSimilares) > 0)
                            @foreach($peliculasSimilares as $relatedMovie)
                                @php
                                    // Obtener datos de la película similar
                                    $posterUrl = isset($relatedMovie['poster_path'])
                                        ? 'https://image.tmdb.org/t/p/w500'.$relatedMovie['poster_path']
                                        : asset('images/no-poster.jpg');
                                    $rating = isset($relatedMovie['vote_average']) ? $relatedMovie['vote_average'] : 0;
                                    $title = isset($relatedMovie['title']) ? $relatedMovie['title'] : '';
                                @endphp
                                <div class="movie-card">
                                    <div class="movie-poster">
                                        <img src="{{ $posterUrl }}" alt="{{ $title }}" loading="lazy">
                                        <div class="movie-overlay">
                                            <div class="movie-badges">
                                                @if(isset($relatedMovie['release_date']) && substr($relatedMovie['release_date'], 0, 4) >= date('Y') - 1)
                                                    <span class="badge new-badge">Nuevo</span>
                                                @endif
                                                @if($rating >= 8)
                                                    <span class="badge top-badge">
                                                        <i class="fas fa-trophy"></i> {{ number_format($rating, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="movie-actions">
                                                <button class="action-btn btn-trailer" data-id="{{ $relatedMovie['id'] }}" aria-label="Ver trailer">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <button class="action-btn btn-favorite" data-id="{{ $relatedMovie['id'] }}" aria-label="Añadir a favoritos">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                                <a href="{{ route('pelicula.detail', $relatedMovie['id']) }}" class="action-btn btn-details" aria-label="Ver detalles">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="movie-info">
                                        <h3>{{ $title }}</h3>
                                        <div class="movie-meta">
                                            <span class="year">{{ isset($relatedMovie['release_date']) ? substr($relatedMovie['release_date'], 0, 4) : 'N/A' }}</span>
                                            <span class="rating">{{ number_format($rating, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-content">
                                <i class="fas fa-film"></i>
                                <p>No se encontraron películas similares para esta película.</p>
                                <span>Puedes explorar nuestro catálogo para descubrir más películas.</span>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Videos/Trailers -->
                @if(isset($pelicula['videos']) && count($pelicula['videos']['results'] ?? []) > 0)
                <section id="videos" class="tab-panel">
                    <h2>Trailers y videos</h2>
                    <div class="videos-container">
                        @foreach($pelicula['videos']['results'] as $video)
                            @if($video['site'] == 'YouTube' && in_array($video['type'], ['Trailer', 'Teaser']))
                            <div class="video-item">
                                <h3>{{ $video['name'] }}</h3>
                                <div class="video-wrapper">
                                    <iframe
                                        width="100%"
                                        height="315"
                                        src="https://www.youtube.com/embed/{{ $video['key'] }}"
                                        title="{{ $video['name'] }}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </section>
                @endif
            </div>
        </div>
    </div>

    <div class="back-button">
        <a href="{{ route('peliculas') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Volver a películas
        </a>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="{{ asset('movie-details.js') }}"></script>
<script>
    // Configurar botón para ir a los comentarios
    document.addEventListener('DOMContentLoaded', function() {
        const btnVerCriticas = document.getElementById('btn-ver-criticas');
        if (btnVerCriticas) {
            btnVerCriticas.addEventListener('click', function() {
                // Cambiar a la pestaña de críticas si no está activa
                const reviewsTab = document.querySelector('.tab-button[data-tab="reviews"]');
                if (reviewsTab && !reviewsTab.classList.contains('active')) {
                    reviewsTab.click();
                }

                // Scroll suave hasta la sección de comentarios
                const reviewsSection = document.getElementById('reviews');
                if (reviewsSection) {
                    reviewsSection.scrollIntoView({ behavior: 'smooth' });
                }

                // Recargar comentarios con desplazamiento
                if (typeof cargarComentarios === 'function') {
                    cargarComentarios(true);
                }
            });
        }
    });
</script>
@endpush

<!-- Modal para trailers -->
<div id="trailerModalStatic">
    <div class="modal-content">
        <button id="closeTrailerBtn"><i class="fas fa-times"></i></button>
        <div id="trailerContainerStatic"></div>
    </div>
</div>
