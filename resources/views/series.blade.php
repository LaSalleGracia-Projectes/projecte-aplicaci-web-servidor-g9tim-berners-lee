@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/series.css') }}">
@endpush

@section('content')
<div class="series-container">
    <!-- Sección de series destacadas -->
    <section class="featured-section">
        <div class="featured-slider">
            @foreach($randomSeries as $serie)
                @php
                    $apiKey = env('TMDB_API_KEY');
                    $tmdbId = $serie->api_id ?? $serie->id;
                    $posterUrl = asset('images/no-poster.jpg');
                    $backdropUrl = null;
                    $rating = 0;

                    try {
                        $response = Http::get("https://api.themoviedb.org/3/tv/{$tmdbId}?api_key={$apiKey}&language=es-ES");
                        if ($response->successful()) {
                            $serieData = $response->json();
                            $posterUrl = !empty($serieData['poster_path'])
                                ? 'https://image.tmdb.org/t/p/w500' . $serieData['poster_path']
                                : asset('images/no-poster.jpg');
                            $backdropUrl = !empty($serieData['backdrop_path'])
                                ? 'https://image.tmdb.org/t/p/w1280' . $serieData['backdrop_path']
                                : null;
                            $rating = $serieData['vote_average'] ?? 0;
                        }
                    } catch (\Exception $e) {
                        // Mantener valores predeterminados en caso de error
                    }
                @endphp
                <div class="featured-item" style="background-image: url('{{ $backdropUrl }}')">
                    <div class="featured-content">
                        <h2>{{ $serie->titulo }}</h2>
                        <div class="featured-meta">
                            <span class="rating"><i class="fas fa-star"></i> {{ number_format($rating, 1) }}/10</span>
                            <span class="year">{{ $serie->año_estreno }}</span>
                        </div>
                        <p class="featured-description">{{ Str::limit($serie->sinopsis, 150) }}</p>
                        <a href="{{ route('serie.detail', $serie->id) }}" class="btn-watch">Ver Detalles</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="slider-controls">
            <button class="nav-btn prev"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- Sección de explorar series -->
    <section class="series-section">
        <div class="section-header">
            <h2>Explorar series</h2>
            <div class="view-toggle">
                <button id="gridView" class="toggle-btn active"><i class="fas fa-th"></i></button>
                <button id="listView" class="toggle-btn"><i class="fas fa-list"></i></button>
            </div>
        </div>

        <div class="series-container grid-view" id="seriesContainer">
            @foreach($series as $serie)
                @php
                    $apiKey = env('TMDB_API_KEY');
                    $tmdbId = $serie->api_id ?? $serie->id;
                    $posterUrl = asset('images/no-poster.jpg');
                    $backdropUrl = null;
                    $rating = 0;
                    $genres = [];

                    try {
                        $response = Http::get("https://api.themoviedb.org/3/tv/{$tmdbId}?api_key={$apiKey}&language=es-ES&append_to_response=credits");
                        if ($response->successful()) {
                            $serieData = $response->json();
                            $posterUrl = !empty($serieData['poster_path'])
                                ? 'https://image.tmdb.org/t/p/w500' . $serieData['poster_path']
                                : asset('images/no-poster.jpg');
                            $backdropUrl = !empty($serieData['backdrop_path'])
                                ? 'https://image.tmdb.org/t/p/w1280' . $serieData['backdrop_path']
                                : null;
                            $rating = $serieData['vote_average'] ?? 0;
                            $genreNames = array_map(function($genre) {
                                return $genre['name'];
                            }, $serieData['genres'] ?? []);
                            $genres = implode(', ', array_slice($genreNames, 0, 3));
                        }
                    } catch (\Exception $e) {
                        // Mantener valores predeterminados en caso de error
                    }

                    // Convertir rating a escala de 5 estrellas
                    $starRating = round($rating / 2, 1);
                @endphp

                <div class="serie-card"
                     data-id="{{ $serie->id }}"
                     data-title="{{ strtolower($serie->titulo) }}"
                     data-year="{{ $serie->año_estreno }}"
                     data-rating="{{ $rating }}"
                     data-genres="{{ $genres }}">
                    <div class="serie-poster">
                        <img src="{{ $posterUrl }}" alt="{{ $serie->titulo }}" loading="lazy">
                        <div class="serie-badges">
                            @if($serie->año_estreno >= date('Y') - 1)
                                <span class="badge new-badge">Nueva</span>
                            @endif
                            @if($rating >= 8)
                                <span class="badge top-badge">Top</span>
                            @endif
                        </div>
                        <div class="serie-actions">
                            <button class="action-btn btn-trailer" data-id="{{ $tmdbId }}">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="action-btn btn-favorite" data-id="{{ $serie->id }}">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn btn-details" data-id="{{ $serie->id }}">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="serie-info">
                        <h3>{{ $serie->titulo }}</h3>
                        <div class="serie-meta">
                            <span class="year">{{ $serie->año_estreno }}</span>
                            <span class="rating">
                                <i class="fas fa-star"></i>
                                {{ number_format($rating, 1) }}
                            </span>
                        </div>
                        <p class="genres">{{ $genres }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const API_KEY = "{{ config('tmdb.api_key') }}";
    const BASE_URL = "{{ config('tmdb.base_url') }}";
    const IMG_URL = "{{ config('tmdb.img_url') }}";
    const BACKDROP_URL = "https://image.tmdb.org/t/p/original";
    const SERIE_ENDPOINT = "{{ route('serie.detail', '') }}";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
<script type="module" src="{{ asset('js/series.js') }}"></script>
@endpush
