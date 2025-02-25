@extends('layouts.app')

@section('title', 'Tendencias - CrítiFlix')

@section('content')
<main>
  <section class="tendencias">
    <h2>Tendencias</h2>
    <div class="trending-container" id="trendingContainer">
      @foreach($tendencias as $item)
        <div class="movie">
          <h3>{{ $item->titulo }}</h3>
          <p>{{ $item->sinopsis }}</p>
        </div>
      @endforeach
    </div>
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
</main>
@endsection
