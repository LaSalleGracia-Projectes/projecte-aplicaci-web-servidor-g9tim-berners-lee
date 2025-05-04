@extends('layouts.app')

@section('title', $serie->name ?? $serie->titulo ?? 'Detalle de serie')

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

        /* Estilos específicos para series */
        .temporadas-section {
            margin-top: 2rem;
        }

        .temporadas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 1rem;
        }

        .temporada-card {
            background: #222;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .temporada-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .temporada-poster {
            position: relative;
            height: 0;
            padding-top: 150%; /* Relación de aspecto del poster */
        }

        .temporada-poster img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .temporada-info {
            padding: 15px;
            background: var(--negro-medio);
        }

        .temporada-info h3 {
            margin: 0 0 10px;
            color: var(--blanco);
            font-size: 1.1rem;
        }

        .temporada-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--texto-muted);
        }

        .temporada-overview {
            color: var(--texto-claro);
            font-size: 0.9rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .episodios-count {
            display: inline-block;
            background: rgba(20, 255, 20, 0.1);
            color: var(--verde-neon);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 10px;
        }

        .network-logos {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .network-logo {
            background: #fff;
            padding: 8px;
            border-radius: 8px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .network-logo img {
            max-height: 30px;
            max-width: 80px;
        }

        .serie-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 10px;
        }

        .status-returning {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .status-ended {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .status-pending {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
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
    <img src="{{ $serie->backdrop_url ?? '' }}" alt="{{ $serie->name ?? $serie->titulo ?? '' }}">
        <div class="titulo-overlay">
            <h1>{{ $serie->name ?? $serie->titulo ?? '' }}</h1>
            @if(!empty($serie->tagline ?? ''))
                <p class="tagline">{{ $serie->tagline }}</p>
            @endif
        </div>
    </div>

    <div class="movie-content-container">
        <!-- Columna izquierda: poster y servicios -->
        <div class="columna-izquierda">
            <!-- Poster de la serie -->
            <div class="poster">
                <img src="{{ isset($serie->poster_path)
                    ? 'https://image.tmdb.org/t/p/w500'.$serie->poster_path
                    : ($serie->poster_url ?? asset('images/no-poster.jpg')) }}"
                     alt="{{ $serie->name ?? $serie->titulo ?? '' }}">
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

            <!-- Redes en donde se emite -->
            @if(isset($serie->networks) && is_array($serie->networks) && count($serie->networks) > 0)
            <div class="networks">
                <h3>Se emite en:</h3>
                <div class="network-logos">
                    @foreach($serie->networks as $network)
                        <div class="network-logo">
                            <img src="https://image.tmdb.org/t/p/w200{{ $network['logo_path'] }}"
                                 alt="{{ $network['name'] }}"
                                 title="{{ $network['name'] }}">
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>


        <!-- Columna central: información detallada -->
        <div class="columna-central">
            <!-- Metadatos -->
            <div class="movie-meta">
                @if(isset($serie->vote_average) || isset($serie->tmdb_rating))
                <span class="rating" title="Puntuación">
                    <i class="fas fa-star"></i>
                    {{ $serie->vote_average ? number_format($serie->vote_average, 1) : ($serie->tmdb_rating ?? 'N/A') }}/10
                </span>
                @endif

                @if(isset($serie->first_air_date) || isset($serie->año_estreno))
                <span class="year" title="Año de estreno">
                    <i class="far fa-calendar-alt"></i>
                    {{ isset($serie->first_air_date) ? substr($serie->first_air_date, 0, 4) : $serie->año_estreno }}
                </span>
                @endif

                @if(isset($serie->status))
                <span class="serie-status {{ strtolower($serie->status) == 'returning series' ? 'status-returning' : (strtolower($serie->status) == 'ended' ? 'status-ended' : 'status-pending') }}" title="Estado de la serie">
                    <i class="fas fa-broadcast-tower"></i>
                    {{ $serie->status == 'Returning Series' ? 'En emisión' : ($serie->status == 'Ended' ? 'Finalizada' : $serie->status) }}
                </span>
                @endif

                @if(isset($serie->genres) && is_array($serie->genres))
                <span class="genres" title="Géneros">
                    <i class="fas fa-film"></i>
                    {{ implode(', ', array_column($serie->genres, 'name')) }}
                </span>
                @endif
            </div>

            <!-- Sinopsis -->
            <div class="sinopsis">
                <h2>Sinopsis</h2>
                <p>{{ $serie->overview ?? $serie->sinopsis ?? 'No hay sinopsis disponible.' }}</p>
            </div>

            <!-- Producción y detalles técnicos -->
            <div class="produccion">
                <div class="produccion-grid">
                    @if(isset($serie->created_by) && is_array($serie->created_by) && count($serie->created_by) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Creador(es):</span>
                        <span class="detail-value">
                            {{ implode(', ', array_column($serie->created_by, 'name')) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->production_companies) && is_array($serie->production_companies) && count($serie->production_companies) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Productora:</span>
                        <span class="detail-value">
                            {{ implode(', ', array_column($serie->production_companies, 'name')) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->number_of_seasons))
                    <div class="detail-item">
                        <span class="detail-label">Temporadas:</span>
                        <span class="detail-value">{{ $serie->number_of_seasons }}</span>
                    </div>
                    @endif

                    @if(isset($serie->number_of_episodes))
                    <div class="detail-item">
                        <span class="detail-label">Episodios:</span>
                        <span class="detail-value">{{ $serie->number_of_episodes }}</span>
                    </div>
                    @endif

                    @if(isset($serie->episode_run_time) && is_array($serie->episode_run_time) && !empty($serie->episode_run_time))
                    <div class="detail-item">
                        <span class="detail-label">Duración episodios:</span>
                        <span class="detail-value">{{ implode('-', $serie->episode_run_time) }} min</span>
                    </div>
                    @endif

                    @if(isset($serie->original_name) || isset($serie->titulo_original))
                    <div class="detail-item">
                        <span class="detail-label">Título original:</span>
                        <span class="detail-value">{{ $serie->original_name ?? $serie->titulo_original ?? $serie->titulo ?? '' }}</span>
                    </div>
                    @endif

                    @if(isset($serie->first_air_date) || isset($serie->fecha_estreno))
                    <div class="detail-item">
                        <span class="detail-label">Fecha de estreno:</span>
                        <span class="detail-value">
                            @php
                                $fecha = $serie->first_air_date ?? $serie->fecha_estreno ?? $serie->año_estreno ?? '';
                                // Formatear fecha si es posible
                                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                                    $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                }
                            @endphp
                            {{ $fecha }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->last_air_date))
                    <div class="detail-item">
                        <span class="detail-label">Último episodio:</span>
                        <span class="detail-value">
                            @php
                                $fecha = $serie->last_air_date;
                                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                                    $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                }
                            @endphp
                            {{ $fecha }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->id) || isset($serie->api_id))
                    <div class="detail-item">
                        <span class="detail-label">ID de TMDB:</span>
                        <span class="detail-value">{{ $serie->id ?? $serie->api_id ?? 'No disponible' }}</span>
                    </div>
                    @endif

                    @if(isset($serie->status))
                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">
                            {{ $serie->status == 'Returning Series' ? 'En emisión' :
                              ($serie->status == 'Ended' ? 'Finalizada' :
                              ($serie->status == 'Canceled' ? 'Cancelada' : $serie->status)) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->origin_country) && is_array($serie->origin_country) && !empty($serie->origin_country))
                    <div class="detail-item">
                        <span class="detail-label">País de origen:</span>
                        <span class="detail-value">
                            {{ implode(', ', $serie->origin_country) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie->in_production))
                    <div class="detail-item">
                        <span class="detail-label">En producción:</span>
                        <span class="detail-value">{{ $serie->in_production ? 'Sí' : 'No' }}</span>
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
                                    alt="{{ $actor['name'] ?? '' }}"
                                    onerror="this.src='{{ asset('img/perfil-default.jpg') }}'">
                                <div class="actor-info">
                                    <p class="actor-nombre">{{ $actor['name'] ?? '' }}</p>
                                    <p class="actor-personaje">{{ $actor['character'] ?? '' }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @elseif(isset($serie->elenco))
                        <p>{{ $serie->elenco }}</p>
                    @else
                        <p>No hay información de elenco disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Temporadas -->
            @if(isset($temporadas) && is_array($temporadas) && count($temporadas) > 0)
            <div class="temporadas-section">
                <h2>Temporadas</h2>
                <div class="temporadas-grid">
                    @foreach($temporadas as $temporada)
                    <div class="temporada-card">
                        <div class="temporada-poster">
                            <img src="{{ isset($temporada['poster_path']) && $temporada['poster_path']
                                ? 'https://image.tmdb.org/t/p/w300' . $temporada['poster_path']
                                : (isset($serie->poster_path)
                                    ? 'https://image.tmdb.org/t/p/w300' . $serie->poster_path
                                    : asset('img/no-poster.jpg')) }}"
                                 alt="Temporada {{ $temporada['season_number'] ?? '' }}">
                        </div>
                        <div class="temporada-info">
                            <h3>{{ $temporada['name'] ?? '' }}</h3>
                            <div class="temporada-meta">
                                <span>{{ isset($temporada['air_date']) ? substr($temporada['air_date'], 0, 4) : 'N/A' }}</span>
                                <span>{{ $temporada['episode_count'] ?? 0 }} episodios</span>
                            </div>
                            @if(isset($temporada['overview']) && $temporada['overview'])
                            <p class="temporada-overview">{{ $temporada['overview'] }}</p>
                            @else
                            <p class="temporada-overview">No hay descripción disponible para esta temporada.</p>
                            @endif
                            <span class="episodios-count">{{ $temporada['episode_count'] ?? 0 }} episodios</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tabs de navegación -->
            <div class="movie-tabs">
                <button class="tab-button active" data-tab="reviews">Críticas</button>
                <button class="tab-button" data-tab="similar">Series similares</button>
                @if(isset($serie->videos) && isset($serie->videos['results']) && count($serie->videos['results']) > 0)
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
                            <input type="hidden" name="id_serie" value="{{ $serie->id ?? '' }}">
                            <div class="rating-selector">
                                <span>Tu puntuación:</span>
                                <div class="stars" role="radiogroup" aria-label="Puntuación">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="far fa-star" data-rating="{{ $i }}" role="radio" aria-label="{{ $i }} estrellas" tabindex="0"></i>
                                    @endfor
                                </div>
                            </div>
                            <textarea name="comentario" placeholder="Escribe tu opinión sobre esta serie..." aria-label="Tu crítica" required></textarea>
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

                <!-- Series similares -->
                <section id="similar" class="tab-panel">
                    <h2>Series similares</h2>
                    <div class="related-movies-container">
                        @if(isset($seriesSimilares) && is_array($seriesSimilares) && count($seriesSimilares) > 0)
                            @foreach($seriesSimilares as $relatedSerie)
                                @php
                                    // Obtener datos de la serie similar
                                    $posterUrl = isset($relatedSerie['poster_path'])
                                        ? 'https://image.tmdb.org/t/p/w500'.$relatedSerie['poster_path']
                                        : asset('images/no-poster.jpg');
                                    $rating = isset($relatedSerie['vote_average']) ? $relatedSerie['vote_average'] : 0;
                                    $title = isset($relatedSerie['name']) ? $relatedSerie['name'] : '';
                                @endphp
                                <div class="movie-card">
                                    <div class="movie-poster">
                                        <img src="{{ $posterUrl }}" alt="{{ $title }}" loading="lazy">
                                        <div class="movie-overlay">
                                            <div class="movie-badges">
                                                @if(isset($relatedSerie['first_air_date']) && substr($relatedSerie['first_air_date'], 0, 4) >= date('Y') - 1)
                                                    <span class="badge new-badge">Nueva</span>
                                                @endif
                                                @if($rating >= 8)
                                                    <span class="badge top-badge">
                                                        <i class="fas fa-trophy"></i> {{ number_format($rating, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="movie-actions">
                                                <button class="action-btn btn-trailer" data-id="{{ $relatedSerie['id'] ?? '' }}" aria-label="Ver trailer">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <button class="action-btn btn-favorite" data-id="{{ $relatedSerie['id'] ?? '' }}" aria-label="Añadir a favoritos">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                                <a href="{{ route('serie.detail', $relatedSerie['id'] ?? '') }}" class="action-btn btn-details" aria-label="Ver detalles">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="movie-info">
                                        <h3>{{ $title }}</h3>
                                        <div class="movie-meta">
                                            <span class="year">{{ isset($relatedSerie['first_air_date']) ? substr($relatedSerie['first_air_date'], 0, 4) : 'N/A' }}</span>
                                            <span class="rating">{{ number_format($rating, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No se encontraron series similares.</p>
                        @endif
                    </div>
                </section>

                <!-- Videos/Trailers -->
                @if(isset($serie->videos) && isset($serie->videos['results']) && count($serie->videos['results']) > 0)
                <section id="videos" class="tab-panel">
                    <h2>Trailers y videos</h2>
                    <div class="videos-container">
                        @foreach($serie->videos['results'] as $video)
                            @if(isset($video['site']) && $video['site'] == 'YouTube' && isset($video['type']) && in_array($video['type'], ['Trailer', 'Teaser']))
                            <div class="video-item">
                                <h3>{{ $video['name'] ?? '' }}</h3>
                                <div class="video-wrapper">
                                    <iframe
                                        width="100%"
                                        height="315"
                                        src="https://www.youtube.com/embed/{{ $video['key'] ?? '' }}"
                                        title="{{ $video['name'] ?? '' }}"
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
        <a href="{{ route('series') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Volver a series
        </a>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('serie-details.js') }}"></script>
@endpush
