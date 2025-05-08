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

    <!-- Selector de tipo de contenido -->
    <div class="content-type-selector">
        <button type="button" class="content-type-btn active" data-type="movie">
            <i class="fas fa-film"></i> {{ __('messages.movies') }}
        </button>
        <button type="button" class="content-type-btn" data-type="tv">
            <i class="fas fa-tv"></i> {{ __('messages.series') }}
        </button>
    </div>

    <form id="randomizerForm" action="{{ route('random.generate') }}" method="GET">
        <input type="hidden" name="tipoContenido" id="tipoContenido" value="movie">

        <!-- Filtros para películas -->
        <div class="randomizer-filters movie-filters">
            <div class="filter-group">
                <label for="genero"><i class="fas fa-theater-masks"></i> {{ __('messages.genre') }}</label>
                <select id="genero" name="genero">
                    <option value="">{{ __('messages.all_genres') }}</option>
                    <option value="accion">Acción</option>
                    <option value="aventura">Aventura</option>
                    <option value="animacion">Animación</option>
                    <option value="comedia">Comedia</option>
                    <option value="crimen">Crimen</option>
                    <option value="documental">Documental</option>
                    <option value="drama">Drama</option>
                    <option value="familiar">Familiar</option>
                    <option value="fantasia">Fantasía</option>
                    <option value="historia">Historia</option>
                    <option value="terror">Terror</option>
                    <option value="musica">Música</option>
                    <option value="misterio">Misterio</option>
                    <option value="romance">Romance</option>
                    <option value="ciencia ficcion">Ciencia ficción</option>
                    <option value="thriller">Thriller</option>
                    <option value="belica">Bélica</option>
                    <option value="western">Western</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="duracion"><i class="fas fa-clock"></i> {{ __('messages.duration') }}</label>
                <select id="duracion" name="duracion">
                    <option value="">{{ __('messages.any') }}</option>
                    <option value="short">{{ __('messages.short') }} (< 90 min)</option>
                    <option value="medium">{{ __('messages.medium') }} (90-120 min)</option>
                    <option value="long">{{ __('messages.long') }} (> 120 min)</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="anio"><i class="fas fa-calendar"></i> {{ __('messages.release_year') }}</label>
                <select id="anio" name="anio">
                    <option value="">{{ __('messages.all') }}</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="plataforma"><i class="fas fa-play-circle"></i> {{ __('messages.platform') }}</label>
                <select id="plataforma" name="plataforma">
                    <option value="">{{ __('messages.all_platforms') }}</option>
                    <option value="netflix">Netflix</option>
                    <option value="hbo">HBO</option>
                    <option value="disney">Disney+</option>
                    <option value="prime">Prime Video</option>
                </select>
            </div>
        </div>

        <!-- Filtros para series -->
        <div class="randomizer-filters series-filters" style="display: none;">
            <div class="filter-group">
                <label for="generoSerie"><i class="fas fa-theater-masks"></i> {{ __('messages.genre') }}</label>
                <select id="generoSerie" name="genero">
                    <option value="">{{ __('messages.all_genres') }}</option>
                    <option value="accion">Acción y Aventura</option>
                    <option value="animacion">Animación</option>
                    <option value="comedia">Comedia</option>
                    <option value="crimen">Crimen</option>
                    <option value="documental">Documental</option>
                    <option value="drama">Drama</option>
                    <option value="familiar">Familiar</option>
                    <option value="infantil">Infantil</option>
                    <option value="misterio">Misterio</option>
                    <option value="noticias">Noticias</option>
                    <option value="reality">Reality</option>
                    <option value="ciencia ficcion">Ciencia ficción y Fantasía</option>
                    <option value="soap">Soap</option>
                    <option value="talk">Talk</option>
                    <option value="guerra">Guerra y Política</option>
                    <option value="western">Western</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="temporadas"><i class="fas fa-layer-group"></i> {{ __('messages.seasons') }}</label>
                <select id="temporadas" name="temporadas">
                    <option value="">{{ __('messages.any') }}</option>
                    <option value="1">1 {{ __('messages.season') }}</option>
                    <option value="2-3">2-3 {{ __('messages.seasons') }}</option>
                    <option value="4-6">4-6 {{ __('messages.seasons') }}</option>
                    <option value="7+">7+ {{ __('messages.seasons') }}</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="anioSerie"><i class="fas fa-calendar"></i> {{ __('messages.release_year') }}</label>
                <select id="anioSerie" name="anio">
                    <option value="">{{ __('messages.all') }}</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="plataformaSerie"><i class="fas fa-play-circle"></i> {{ __('messages.platform') }}</label>
                <select id="plataformaSerie" name="plataforma">
                    <option value="">{{ __('messages.all_platforms') }}</option>
                    <option value="netflix">Netflix</option>
                    <option value="hbo">HBO</option>
                    <option value="disney">Disney+</option>
                    <option value="prime">Prime Video</option>
                </select>
            </div>
        </div>

        <button type="submit" id="generarRandom" class="action-btn">
            <i class="fas fa-dice"></i> {{ __('messages.generate') }}
        </button>
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
