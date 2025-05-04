@extends('layouts.app')

@section('title', __('messages.home') . ' - CritFlix')

@section('content')
<main>
<!-- BANNER/SLIDER -->
<section class="banner">
  <div class="slider">
    <div class="slides" id="bannerSlides">
      @foreach($banners as $banner)
        <div class="slide {{ $loop->first ? 'active' : '' }}">
          <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->titulo }}">
          <div class="slide-content">
            <h2>{{ $banner->titulo }}</h2>
          </div>
        </div>
      @endforeach
    </div>
    <button class="prev" id="prevSlide"><i class="fas fa-chevron-left"></i></button>
    <button class="next" id="nextSlide"><i class="fas fa-chevron-right"></i></button>
    <div class="indicators" id="sliderIndicators">
      @foreach($banners as $index => $banner)
        <span class="dot" data-slide="{{ $index }}"></span>
      @endforeach
    </div>
  </div>
</section>


  <!-- TENDENCIAS -->
  <section class="trending">
    <h2>{{ __('messages.trends') }}</h2>
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
      <label for="platformFilter">{{ __('messages.filter_by_platform') }}:</label>
      <select id="platformFilter">
        <option value="">{{ __('messages.all_platforms') }}</option>
        <option value="netflix">Netflix</option>
        <option value="prime">Prime Video</option>
        <option value="cine">Cine</option>
      </select>
    </div>
  </section>

  <!-- CRÍTICOS DESTACADOS -->
  <section class="criticos">
    <h2>{{ __('messages.featured_critics') }}</h2>
    <button id="spoilerBtn" class="spoiler-btn">{{ __('messages.spoiler_warning') }}</button>
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
    <button id="hazteCritico" class="action-btn">{{ __('messages.become_critic') }}</button>
  </section>

  <!-- MI LISTA (FAVORITOS) -->
  <section class="mi-lista">
    <h2>{{ __('messages.my_favorites') }}</h2>
    <div class="lista-content" id="miListaContent">
      <p>{{ __('messages.add_favorites') }}.</p>
      <div id="favoritesContainer">
        @if(isset($favoritos) && count($favoritos) > 0)
          @foreach($favoritos as $favorito)
            <div class="favorito">
              <p>{{ $favorito->titulo }}</p>
            </div>
          @endforeach
        @else
          <p>{{ __('messages.no_favorites') }}.</p>
        @endif
      </div>
    </div>
  </section>

  <!-- CINE RANDOMIZER -->
  <section class="cine-randomizer">
    <h2>{{ __('messages.randomizer') }}</h2>
    <form action="{{ route('random.generate') }}" method="GET">
      <div class="randomizer-filters">
        <label for="tipoContenido">{{ __('messages.content_type') }}:</label>
        <select id="tipoContenido" name="tipoContenido">
          <option value="movie">{{ __('messages.movies') }}</option>
          <option value="tv">{{ __('messages.series') }}</option>
        </select>
        <label for="genero">{{ __('messages.genre') }}:</label>
        <select id="genero" name="genero">
          <option value="">{{ __('messages.all_genres') }}</option>
        </select>
        <label for="duracion">{{ __('messages.duration') }}:</label>
        <select id="duracion" name="duracion">
          <option value="">{{ __('messages.any') }}</option>
          <option value="short">{{ __('messages.short') }}</option>
          <option value="long">{{ __('messages.long') }}</option>
        </select>
        <label for="anio">{{ __('messages.release_year') }}:</label>
        <select id="anio" name="anio">
          <option value="">{{ __('messages.all') }}</option>
        </select>
        <label for="plataforma">{{ __('messages.platform') }}:</label>
        <select id="plataforma" name="plataforma">
          <option value="">{{ __('messages.all_platforms') }}</option>
          <option value="netflix">Netflix</option>
          <option value="hbo">HBO</option>
          <option value="disney">Disney+</option>
        </select>
      </div>
      <button type="submit" id="generarRandom" class="action-btn">🎲 {{ __('messages.generate') }}</button>
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
