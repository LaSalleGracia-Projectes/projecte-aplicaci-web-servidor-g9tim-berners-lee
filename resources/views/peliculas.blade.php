@extends('layouts.app')

@section('title', 'Películas - CrítiFlix')

@section('content')
<main>
  <section class="peliculas">
    <h2>Películas</h2>
    <div class="peliculas-container" id="peliculasContainer">
      @foreach($peliculas as $pelicula)
        <div class="pelicula">
          <h3>{{ $pelicula->titulo }}</h3>
          <p>{{ $pelicula->sinopsis }}</p>
          <p><small>Año: {{ $pelicula->año_estreno }}</small></p>
          <p><small>Duración: {{ $pelicula->duracion }} min</small></p>
        </div>
      @endforeach
    </div>
  </section>
</main>
@endsection
