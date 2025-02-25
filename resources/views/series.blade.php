@extends('layouts.app')

@section('title', 'Series - CrítiFlix')

@section('content')
<main>
  <section class="series">
    <h2>Series</h2>
    <div class="series-container" id="seriesContainer">
      @foreach($series as $serie)
        <div class="serie">
          <h3>{{ $serie->titulo }}</h3>
          <p>{{ $serie->sinopsis }}</p>
          <p><small>Año: {{ $serie->año_estreno }}</small></p>
          <p><small>Duración: {{ $serie->duracion }} min</small></p>
        </div>
      @endforeach
    </div>
  </section>
</main>
@endsection
