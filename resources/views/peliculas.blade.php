@extends('layouts.app')

@section('title', 'Películas - CrítiFlix')

@push('styles')
    <link rel="stylesheet" href="{{ asset('movies.css') }}">
@endpush

@section('content')
<main>
    <!-- SECCIÓN: PELÍCULAS DESTACADAS (RANDOM) -->
    <section class="random-movies">
        <h2>Películas Destacadas</h2>
        <div class="movies-container" id="randomMoviesContainer">
            @if(!empty($randomMovies) && $randomMovies->count() > 0)
                @foreach($randomMovies as $movie)
                    <div class="movie-card" data-title="{{ strtolower($movie->titulo) }}">
                        @php
                            // Obtener poster de TMDB
                            $apiKey = env('TMDB_API_KEY');
                            $tmdbId = $movie->api_id ?? $movie->id;
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
                        <img src="{{ $posterUrl }}" alt="{{ $movie->titulo }}">
                        <div class="movie-info">
                            <h3>{{ $movie->titulo }}</h3>
                            <p class="rating">Puntuación: {{ $rating }}/10</p>
                            <a href="{{ route('pelicula.detail', $movie->id) }}" class="btn-details">Ver Detalles</a>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No hay películas destacadas disponibles.</p>
            @endif
        </div>
    </section>

    <!-- SECCIÓN: FILTRADOR DE PELÍCULAS AVANZADO -->
    <section class="filter-bar">
        <div class="filter-container">
            <label for="searchInput">Buscar:</label>
            <input type="text" id="searchInput" placeholder="Buscar película...">

            <label for="yearSelect">Año:</label>
            <select id="yearSelect">
                <option value="">Todos</option>
                @php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= 1900; $year--) {
                        echo "<option value=\"{$year}\">{$year}</option>";
                    }
                @endphp
            </select>

            <label for="minRating">Rating mínimo:</label>
            <input type="range" id="minRating" min="0" max="10" step="0.1" value="0">
            <span id="ratingValue">0</span>
        </div>
    </section>

    <!-- SECCIÓN: TODAS LAS PELÍCULAS -->
    <section class="all-movies">
        <h2>Todas las Películas</h2>
        <div class="movies-container" id="allMoviesContainer">
            @foreach($movies as $movie)
                @php
                    // Obtener poster y rating de TMDB
                    $apiKey = env('TMDB_API_KEY');
                    $tmdbId = $movie->api_id ?? $movie->id;
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
                <div class="movie-card"
                     data-title="{{ strtolower($movie->titulo) }}"
                     data-year="{{ $movie->año_estreno }}"
                     data-rating="{{ $rating }}">
                    <img src="{{ $posterUrl }}" alt="{{ $movie->titulo }}">
                    <div class="movie-info">
                        <h3>{{ $movie->titulo }}</h3>
                        <p class="year">{{ $movie->año_estreno }}</p>
                        <p class="rating">Puntuación: {{ $rating }}/10</p>
                        <a href="{{ route('pelicula.detail', $movie->id) }}" class="btn-details">Ver Detalles</a>
                    </div>
                </div>
            @endforeach
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
<script type="module" src="{{ asset('movies.js') }}"></script>
