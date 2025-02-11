@extends('layouts.app')

@section('title', 'Críticos - CrítiFlix')

@section('content')
<main>
  <section class="criticos">
    <h2>Críticos Destacados</h2>
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
</main>
@endsection
