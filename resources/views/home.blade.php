@extends('layouts.app')

@section('title', 'Inicio - CrítiFlix')

@section('content')
<main>
<!-- BANNER/SLIDER -->
<section class="banner">
  <div class="slider">
    <div class="slides" id="bannerSlides">
      @foreach($banners as $banner)
        <div class="slide">
          <img src="{{ $banner->image }}" alt="{{ $banner->titulo }}">
        </div>
      @endforeach
    </div>
    <button class="prev" id="prevSlide">&#10094;</button>
    <button class="next" id="nextSlide">&#10095;</button>
    <div class="indicators" id="sliderIndicators">
      @foreach($banners as $index => $banner)
        <span class="dot" data-slide="{{ $index }}"></span>
      @endforeach
    </div>
  </div>
</section>


  <!-- TENDENCIAS -->
  <section class="trending">
    <h2>Tendencias</h2>
    <div class="trending-container" id="trendingContainer">
      @foreach($trendingMovies as $movie)
        <div class="movie">
          <img src="{{ $movie->poster ?? 'https://via.placeholder.com/150' }}" alt="{{ $movie->titulo }}">
          <p>{{ $movie->titulo }}</p>
        </div>
      @endforeach
    </div>
    <button class="nav-btn" id="trendingPrev">&#10094;</button>
    <button class="nav-btn" id="trendingNext">&#10095;</button>
    <div class="filter-platform">
      <label for="platformFilter">Filtrar por Plataforma:</label>
      <select id="platformFilter">
        <option value="">Todas</option>
        <option value="netflix">Netflix</option>
        <option value="prime">Prime Video</option>
        <option value="cine">Cine</option>
      </select>
    </div>
  </section>

  <!-- CRÍTICOS DESTACADOS -->
  <section class="criticos">
    <h2>Críticos Destacados</h2>
    <button id="spoilerBtn" class="spoiler-btn">Aviso Spoilers</button>
    <div class="criticos-container" id="criticosContainer">
      @foreach($criticos as $critico)
        <div class="critico">
          <img src="{{ $critico->foto_perfil ?? 'https://via.placeholder.com/100' }}" alt="{{ $critico->nombre_usuario }}">
          <p>
            {{ $critico->nombre_usuario }}
            @if($critico->rol === 'critico')
              <span class="badge">Top Crítico</span>
            @endif
          </p>
        </div>
      @endforeach
    </div>
    <button id="hazteCritico" class="action-btn">Hazte Crítico</button>
  </section>

  <!-- MI LISTA (FAVORITOS) -->
  <section class="mi-lista">
    <h2>Mis Favoritos</h2>
    <div class="lista-content" id="miListaContent">
      <p>Agrega tus películas favoritas para verlas luego.</p>
      <div id="favoritesContainer">
        @if(isset($favoritos) && count($favoritos) > 0)
          @foreach($favoritos as $favorito)
            <div class="favorito">
              <p>{{ $favorito->titulo }}</p>
            </div>
          @endforeach
        @else
          <p>No tienes favoritos aún.</p>
        @endif
      </div>
    </div>
  </section>

  <!-- CINE RANDOMIZER -->
  <section class="cine-randomizer">
    <h2>Cine Randomizer</h2>
    <form action="{{ route('random.generate') }}" method="GET">
      <div class="randomizer-filters">
        <label for="tipoContenido">Tipo de Contenido:</label>
        <select id="tipoContenido" name="tipoContenido">
          <option value="movie">Película</option>
          <option value="tv">Serie</option>
        </select>
        <label for="genero">Género:</label>
        <select id="genero" name="genero">
          <option value="">Todos</option>
        </select>
        <label for="duracion">Duración:</label>
        <select id="duracion" name="duracion">
          <option value="">Cualquiera</option>
          <option value="short">Corta (&lt;90 min)</option>
          <option value="long">Larga (&gt;90 min)</option>
        </select>
        <label for="anio">Año de Lanzamiento:</label>
        <select id="anio" name="anio">
          <option value="">Todos</option>
        </select>
        <label for="plataforma">Plataforma:</label>
        <select id="plataforma" name="plataforma">
          <option value="">Todas</option>
          <option value="netflix">Netflix</option>
          <option value="hbo">HBO</option>
          <option value="disney">Disney+</option>
        </select>
      </div>
      <button type="submit" id="generarRandom" class="action-btn">🎲 Generar</button>
    </form>
    <div class="random-container" id="randomContainer">
      @if(isset($randomMovie))
        <div class="random-movie">
          <h3>{{ $randomMovie->titulo }}</h3>
          <p>{{ $randomMovie->sinopsis }}</p>
        </div>
      @endif
    </div>
  </section>
</main>
@endsection
