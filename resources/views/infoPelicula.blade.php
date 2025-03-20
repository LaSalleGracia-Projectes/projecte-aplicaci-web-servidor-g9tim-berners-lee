@extends('layouts.app')

@section('title', $pelicula['title'] ?? $pelicula->titulo . ' - CrítiFlix')

@push('styles')
    <link rel="stylesheet" href="{{ asset('movie-details.css') }}">
@endpush

@section('content')
<main class="pelicula-detalle">
    <!-- Banner grande con título -->
    <div class="banner">
        <img src="{{ isset($pelicula['backdrop_path']) ? 'https://image.tmdb.org/t/p/original'.$pelicula['backdrop_path'] : asset('images/default-backdrop.jpg') }}"
             alt="{{ $pelicula['title'] ?? $pelicula->titulo }}">
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
                            <div class="rating-selector">
                                <span>Tu puntuación:</span>
                                <div class="stars" role="radiogroup" aria-label="Puntuación">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="far fa-star" data-rating="{{ $i }}" role="radio" aria-label="{{ $i }} estrellas" tabindex="0"></i>
                                    @endfor
                                </div>
                            </div>
                            <textarea name="review-text" placeholder="Escribe tu opinión sobre esta película..." aria-label="Tu crítica"></textarea>
                            <button type="submit" class="btn-submit-review">Publicar crítica</button>
                        </form>
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
                            // Obtener películas del mismo año o similares
                            $relatedMovies = isset($peliculasSimilares) ? $peliculasSimilares : (
                                App\Models\PeliculasSeries::where('tipo', 'pelicula')
                                ->where('año_estreno', $pelicula->año_estreno ?? (isset($pelicula['release_date']) ? substr($pelicula['release_date'], 0, 4) : null))
                                ->where('id', '!=', $pelicula->id ?? null)
                                ->limit(4)
                                ->get()
                            );

                            // Si no hay suficientes, mostrar algunas aleatorias
                            if (!isset($peliculasSimilares) && $relatedMovies->count() < 4) {
                                $moreMovies = App\Models\PeliculasSeries::where('tipo', 'pelicula')
                                            ->where('id', '!=', $pelicula->id ?? null)
                                            ->whereNotIn('id', $relatedMovies->pluck('id')->toArray())
                                            ->inRandomOrder()
                                            ->limit(4 - $relatedMovies->count())
                                            ->get();

                                $relatedMovies = $relatedMovies->concat($moreMovies);
                            }
                        @endphp

                        @if(isset($peliculasSimilares) || $relatedMovies->count() > 0)
                            @foreach($peliculasSimilares ?? $relatedMovies as $relatedMovie)
                                @php
                                    // Obtener poster de TMDB si es necesario
                                    $apiKey = env('TMDB_API_KEY');
                                    $tmdbId = isset($relatedMovie['id']) ? $relatedMovie['id'] : ($relatedMovie->api_id ?? $relatedMovie->id);
                                    $posterUrl = isset($relatedMovie['poster_path'])
                                        ? 'https://image.tmdb.org/t/p/w500'.$relatedMovie['poster_path']
                                        : asset('images/no-poster.jpg');
                                    $rating = isset($relatedMovie['vote_average']) ? $relatedMovie['vote_average'] : 0;
                                    $title = isset($relatedMovie['title']) ? $relatedMovie['title'] : $relatedMovie->titulo;

                                    if (!isset($relatedMovie['poster_path']) && !isset($peliculasSimilares)) {
                                        try {
                                            $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$apiKey}&language=es-ES");
                                            if (!$response->failed()) {
                                                $movieData = $response->json();
                                                $posterUrl = isset($movieData['poster_path'])
                                                    ? 'https://image.tmdb.org/t/p/w500'.$movieData['poster_path']
                                                    : asset('images/no-poster.jpg');
                                                $rating = $movieData['vote_average'] ?? 0;
                                            }
                                        } catch (\Exception $e) {
                                            // Usar poster predeterminado si hay error
                                        }
                                    }
                                @endphp
                                <div class="movie-card">
                                    <img src="{{ $posterUrl }}" alt="{{ $title }}">
                                    <div class="movie-info">
                                        <h3>{{ $title }}</h3>
                                        <p class="rating">⭐ {{ number_format($rating, 1) }}/10</p>
                                        <a href="{{ isset($relatedMovie['id'])
                                            ? route('pelicula.detail', $relatedMovie['id'])
                                            : route('pelicula.detail', $relatedMovie->id) }}"
                                           class="btn-details">Ver Detalles</a>
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
