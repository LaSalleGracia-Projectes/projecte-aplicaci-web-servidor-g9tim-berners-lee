@extends('layouts.app')

@section('title', 'Películas - CrítiFlix')

@push('styles')
  <link rel="stylesheet" href="{{ asset('/movies.css') }}">
@endpush

@section('content')
<main>
  <!-- SECCIÓN: PELÍCULAS DESTACADAS (RANDOM) -->
  <section class="random-movies">
    <h2>Películas Destacadas</h2>
    <div class="movies-container" id="randomMoviesContainer">
    @if(!empty($randomMovies) && $randomMovies->count() > 0)
        @foreach($randomMovies as $movie)
          <div class="movie-card" data-title="{{ strtolower($movie->title) }}">
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
            <div class="movie-info">
              <h3>{{ $movie->title }}</h3>
              <p class="rating">Puntuación: {{ $movie->tmdb_rating }}/10</p>
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
      <label for="genreSelect">Género:</label>
      <select id="genreSelect">
        <option value="">Todos</option>
        @foreach($genresList as $genre)
          <option value="{{ strtolower($genre['name']) }}">{{ $genre['name'] }}</option>
        @endforeach
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
        <div class="movie-card"
             data-title="{{ strtolower($movie->title) }}"
             data-genre="{{ strtolower($movie->genre ?? '') }}"
             data-rating="{{ $movie->tmdb_rating }}">
          <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
          <div class="movie-info">
            <h3>{{ $movie->title }}</h3>
            <p class="rating">Puntuación: {{ $movie->tmdb_rating }}/10</p>
            <a href="{{ route('pelicula.detail', $movie->id) }}" class="btn-details">Ver Detalles</a>
          </div>
        </div>
      @endforeach
    </div>
  </section>
</main>
@endsection

@push('scripts')
  <script src="{{ asset('movies.js') }}"></script>
@endpush
