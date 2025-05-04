@extends('layouts.app')

@section('title', $pelicula['title'] ?? $pelicula->titulo ?? 'Detalle de película')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/movie-details.css') }}">
    <style>
        .comentarios-section {
            margin-top: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .nuevo-comentario {
            margin-bottom: 2rem;
        }

        .comentario {
            background: white;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .comentario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .usuario-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .spoiler-warning {
            background: #fff3cd;
            padding: 0.5rem;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }

        .contenido-spoiler {
            display: none;
        }

        .spoiler .contenido-comentario {
            display: none;
        }
    </style>
@endpush

@section('content')

@php
    // Obtener API key para el frontend
    $tmdbApiKey = env('TMDB_API_KEY');
@endphp

<meta name="tmdb-api-key" content="{{ $tmdbApiKey }}">

<main class="pelicula-detalle">
    <!-- Banner grande con título -->
    <div class="banner">
    <img src="{{ $pelicula->backdrop_url}}" alt="{{ $pelicula['title'] ?? $pelicula->titulo }}">
        <div class="titulo-overlay">
            <h1>{{ $pelicula['title'] ?? $pelicula->titulo }}</h1>
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
                <img src="{{ isset($pelicula['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w500'.$pelicula['poster_path']
                    : ($pelicula->poster_url ?? asset('images/no-poster.jpg')) }}"
                     alt="{{ $pelicula['title'] ?? $pelicula->titulo }}">
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons">
                <button class="btn-favorite" title="Añadir a favoritos">
                    <i class="far fa-heart"></i> <span>Favorito</span>
                </button>
                <button class="btn-watchlist" title="Añadir a lista de visualización">
                    <i class="far fa-bookmark"></i> <span>Ver más tarde</span>
                </button>
                <button class="btn-share" title="Compartir">
                    <i class="fas fa-share-alt"></i> <span>Compartir</span>
                </button>
            </div>

<!-- Dónde ver (servicios de streaming) -->
<div class="donde-ver">
    <h3>Disponible en:</h3>
    <div class="plataformas">
        @if(isset($watchProviders['flatrate']) && count($watchProviders['flatrate']) > 0)
            @foreach($watchProviders['flatrate'] as $plataforma)
            <div class="plataforma">
                <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}" alt="{{ $plataforma['provider_name'] }}">
                <span>{{ $plataforma['provider_name'] }}</span>
            </div>
            @endforeach
        @elseif(isset($watchProviders['rent']) && count($watchProviders['rent']) > 0)
            <h4>Alquiler:</h4>
            @foreach($watchProviders['rent'] as $plataforma)
            <div class="plataforma">
                <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}" alt="{{ $plataforma['provider_name'] }}">
                <span>{{ $plataforma['provider_name'] }}</span>
            </div>
            @endforeach
        @elseif(isset($watchProviders['buy']) && count($watchProviders['buy']) > 0)
            <h4>Compra:</h4>
            @foreach($watchProviders['buy'] as $plataforma)
            <div class="plataforma">
                <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}" alt="{{ $plataforma['provider_name'] }}">
                <span>{{ $plataforma['provider_name'] }}</span>
            </div>
            @endforeach
        @else
            <p>Información no disponible</p>
        @endif
    </div>
</div>

        </div>


        <!-- Columna central: información detallada -->
        <div class="columna-central">
            <!-- Metadatos -->
            <div class="movie-meta">
                @if(isset($pelicula['vote_average']) || isset($pelicula->tmdb_rating))
                <span class="rating" title="Puntuación">
                    <i class="fas fa-star"></i>
                    {{ isset($pelicula['vote_average']) ? number_format($pelicula['vote_average'], 1) : ($pelicula->tmdb_rating ?? 'N/A') }}/10
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
                <div class="produccion-grid">
                    @if(isset($director['name']) || isset($pelicula->director))
                    <div class="detail-item">
                        <span class="detail-label">Director:</span>
                        <span class="detail-value">{{ $director['name'] ?? $pelicula->director ?? 'Información no disponible' }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['production_companies']) && count($pelicula['production_companies']) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Productora:</span>
                        <span class="detail-value">
                            {{ implode(', ', array_column($pelicula['production_companies'], 'name')) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($pelicula['original_title']) || isset($pelicula->titulo_original))
                    <div class="detail-item">
                        <span class="detail-label">Título original:</span>
                        <span class="detail-value">{{ $pelicula['original_title'] ?? $pelicula->titulo_original ?? $pelicula->titulo }}</span>
                    </div>
                    @endif

                    @if(isset($pelicula['release_date']) || isset($pelicula->fecha_estreno))
                    <div class="detail-item">
                        <span class="detail-label">Fecha de estreno:</span>
                        <span class="detail-value">
                            @php
                                $fecha = isset($pelicula['release_date']) ? $pelicula['release_date'] : ($pelicula->fecha_estreno ?? $pelicula->año_estreno);
                                // Formatear fecha si es posible
                                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                                    $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                }
                            @endphp
                            {{ $fecha }}
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
            </section>
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
                        <form id="review-form">
                            <input type="hidden" name="id_pelicula" value="{{ $pelicula->id }}">
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
                    <h2>Películas similares</h2>
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
                            <p>No se encontraron películas similares.</p>
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
<script src="{{ asset('movie-details.js') }}"></script>
@endpush
