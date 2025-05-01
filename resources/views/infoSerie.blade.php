@extends('layouts.app')

@section('title', $serie['name'] ?? $serie->titulo . ' - CrítiFlix')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/series-details.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<main class="serie-detalle">
    <!-- Banner grande con título -->
    <div class="banner">
        <img src="{{ $serie->backdrop_url ?? 'https://image.tmdb.org/t/p/original'.$serie['backdrop_path'] ?? asset('images/no-backdrop.jpg') }}"
             alt="{{ $serie['name'] ?? $serie->titulo }}"
             onerror="this.src='{{ asset('images/no-backdrop.jpg') }}'">
        <div class="titulo-overlay">
            <h1>{{ $serie['name'] ?? $serie->titulo }}</h1>
            @if(!empty($serie['tagline'] ?? ''))
                <p class="tagline">{{ $serie['tagline'] }}</p>
            @endif
            <div class="banner-meta">
                @if(isset($serie['vote_average']) || isset($serie->tmdb_rating))
                    <span class="rating">
                        <i class="fas fa-star"></i>
                        {{ isset($serie['vote_average']) ? number_format($serie['vote_average'], 1) : ($serie->tmdb_rating ?? 'N/A') }}/10
                    </span>
                @endif
                @if(isset($serie['first_air_date']) || isset($serie->año_estreno))
                    <span class="year">
                        {{ isset($serie['first_air_date']) ? substr($serie['first_air_date'], 0, 4) : $serie->año_estreno }}
                    </span>
                @endif
                @if(isset($serie['number_of_seasons']))
                    <span class="seasons">
                        {{ $serie['number_of_seasons'] }} temporada{{ $serie['number_of_seasons'] != 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="serie-content-container">
        <!-- Columna izquierda: poster y servicios -->
        <div class="columna-izquierda">
            <!-- Poster de la serie -->
            <div class="poster">
                <img src="{{ isset($serie['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w500'.$serie['poster_path']
                    : ($serie->poster_url ?? asset('images/no-poster.jpg')) }}"
                     alt="{{ $serie['name'] ?? $serie->titulo }}"
                     onerror="this.src='{{ asset('images/no-poster.jpg') }}'">
                @if($serie['status'] === 'Returning Series')
                    <div class="badge-new">En emisión</div>
                @endif
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons">
                <button class="btn-favorite" title="Añadir a favoritos" data-serie-id="{{ $serie->id ?? $serie['id'] }}">
                    <i class="far fa-heart"></i> <span>Favorito</span>
                </button>
                <button class="btn-watchlist" title="Añadir a lista de visualización">
                    <i class="far fa-bookmark"></i> <span>Ver más tarde</span>
                </button>
                <button class="btn-trailer" title="Ver trailer" data-toggle="modal" data-target="#trailerModal">
                    <i class="fas fa-play"></i> <span>Ver trailer</span>
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
                            <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}"
                                 alt="{{ $plataforma['provider_name'] }}"
                                 loading="lazy">
                            <span>{{ $plataforma['provider_name'] }}</span>
                        </div>
                        @endforeach
                    @else
                        <p>Información no disponible</p>
                    @endif
                </div>
            </div>

            <!-- Calificación de usuarios -->
            @auth
            <div class="user-rating">
                <h3>Tu calificación</h3>
                <div class="rating-stars">
                    @for($i = 1; $i <= 10; $i++)
                        <i class="far fa-star" data-rating="{{ $i }}"></i>
                    @endfor
                </div>
                <span class="rating-value"></span>
            </div>
            @endauth
        </div>

        <!-- Columna central: información detallada -->
        <div class="columna-central">
            <!-- Metadatos -->
            <div class="serie-meta">
                @if(isset($serie['vote_average']) || isset($serie->tmdb_rating))
                <span class="rating" title="Puntuación">
                    <i class="fas fa-star"></i>
                    {{ isset($serie['vote_average']) ? number_format($serie['vote_average'], 1) : ($serie->tmdb_rating ?? 'N/A') }}/10
                </span>
                @endif

                @if(isset($serie['first_air_date']) || isset($serie->año_estreno))
                <span class="year" title="Año de estreno">
                    <i class="far fa-calendar-alt"></i>
                    {{ isset($serie['first_air_date']) ? substr($serie['first_air_date'], 0, 4) : $serie->año_estreno }}
                </span>
                @endif

                @if(isset($serie['episode_run_time']) && !empty($serie['episode_run_time']))
                <span class="duration" title="Duración por episodio">
                    <i class="far fa-clock"></i>
                    {{ $serie['episode_run_time'][0] ?? 'N/A' }} min
                </span>
                @endif

                @if(isset($serie['genres']) && is_array($serie['genres']))
                <span class="genres" title="Géneros">
                    <i class="fas fa-film"></i>
                    {{ implode(', ', array_column($serie['genres'], 'name')) }}
                </span>
                @endif

                @if(isset($serie['number_of_episodes']))
                <span class="episodes" title="Número total de episodios">
                    <i class="fas fa-tv"></i>
                    {{ $serie['number_of_episodes'] }} episodios
                </span>
                @endif
            </div>

            <!-- Sinopsis -->
            <div class="sinopsis">
                <h2>Sinopsis</h2>
                <p>{{ $serie['overview'] ?? $serie->sinopsis ?? 'No hay sinopsis disponible.' }}</p>
            </div>

            <!-- Temporadas -->
            <div class="temporadas">
                <h2>Temporadas</h2>
                <div class="temporadas-grid">
                    @if(isset($serie['seasons']))
                        @foreach($serie['seasons'] as $temporada)
                            <div class="temporada-card" data-season="{{ $temporada['season_number'] }}">
                                <img src="{{ isset($temporada['poster_path'])
                                    ? 'https://image.tmdb.org/t/p/w300'.$temporada['poster_path']
                                    : asset('images/no-poster.jpg') }}"
                                     alt="Temporada {{ $temporada['season_number'] }}"
                                     loading="lazy">
                                <div class="temporada-info">
                                    <h3>{{ $temporada['name'] }}</h3>
                                    <p class="episode-count">{{ $temporada['episode_count'] }} episodios</p>
                                    @if($temporada['air_date'])
                                        <p class="air-date">Estreno: {{ \Carbon\Carbon::parse($temporada['air_date'])->format('d/m/Y') }}</p>
                                    @endif
                                    @if(!empty($temporada['overview']))
                                        <p class="overview">{{ Str::limit($temporada['overview'], 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No hay información de temporadas disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Producción y detalles técnicos -->
            <div class="produccion">
                <h2>Detalles de producción</h2>
                <div class="produccion-grid">
                    @if(isset($creador['name']) || isset($serie->creador))
                    <div class="detail-item">
                        <span class="detail-label">Creador:</span>
                        <span class="detail-value">{{ $creador['name'] ?? $serie->creador ?? 'Información no disponible' }}</span>
                    </div>
                    @endif

                    @if(isset($serie['production_companies']) && count($serie['production_companies']) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Productora:</span>
                        <span class="detail-value">
                            {{ implode(', ', array_column($serie['production_companies'], 'name')) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie['original_name']) || isset($serie->titulo_original))
                    <div class="detail-item">
                        <span class="detail-label">Título original:</span>
                        <span class="detail-value">{{ $serie['original_name'] ?? $serie->titulo_original ?? $serie->titulo }}</span>
                    </div>
                    @endif

                    @if(isset($serie['first_air_date']) || isset($serie->fecha_estreno))
                    <div class="detail-item">
                        <span class="detail-label">Primera emisión:</span>
                        <span class="detail-value">
                            @php
                                $fecha = isset($serie['first_air_date']) ? $serie['first_air_date'] : ($serie->fecha_estreno ?? $serie->año_estreno);
                                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                                    $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                }
                            @endphp
                            {{ $fecha }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie['status']))
                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">
                            @php
                                $estados = [
                                    'Returning Series' => 'En emisión',
                                    'Ended' => 'Finalizada',
                                    'Canceled' => 'Cancelada',
                                    'Pilot' => 'Piloto'
                                ];
                            @endphp
                            {{ $estados[$serie['status']] ?? $serie['status'] }}
                        </span>
                    </div>
                    @endif

                    @if(isset($serie['networks']) && count($serie['networks']) > 0)
                    <div class="detail-item">
                        <span class="detail-label">Cadena:</span>
                        <span class="detail-value">
                            {{ implode(', ', array_column($serie['networks'], 'name')) }}
                        </span>
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
                    @else
                        <p>No hay información de elenco disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Series similares -->
            <div class="series-similares">
                <h2>Series similares</h2>
                <div class="similares-container">
                    @if(isset($seriesSimilares) && count($seriesSimilares) > 0)
                        @foreach($seriesSimilares as $similar)
                            <div class="serie-card">
                                <img src="{{ isset($similar['poster_path'])
                                    ? 'https://image.tmdb.org/t/p/w300'.$similar['poster_path']
                                    : asset('images/no-poster.jpg') }}"
                                    alt="{{ $similar['name'] }}"
                                    onerror="this.src='{{ asset('images/no-poster.jpg') }}'">
                                <div class="serie-info">
                                    <h3>{{ $similar['name'] }}</h3>
                                    <p class="rating">⭐ {{ number_format($similar['vote_average'], 1) }}/10</p>
                                    <a href="{{ route('serie.detail', $similar['id']) }}" class="btn-details">Ver Detalles</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No se encontraron series similares.</p>
                    @endif
                </div>
            </div>

            <!-- Videos/Trailers -->
            @if(isset($serie['videos']) && count($serie['videos']['results'] ?? []) > 0)
            <section id="videos" class="tab-panel">
                <h2>Trailers y videos</h2>
                <div class="videos-container">
                    @foreach($serie['videos']['results'] as $video)
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

    <!-- Modal para trailer -->
    <div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trailer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="video-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificaciones -->
    <div class="notifications-container"></div>

    <div class="back-button">
        <a href="{{ route('series') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Volver a series
        </a>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
    const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
    const SERIE_ID = "{{ $serie->api_id ?? $serie->id }}";
    const IS_AUTHENTICATED = {{ auth()->check() ? 'true' : 'false' }};
    const USER_ID = "{{ auth()->id() ?? '' }}";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script type="module" src="{{ asset('js/serie-detail.js') }}"></script>
@endpush
