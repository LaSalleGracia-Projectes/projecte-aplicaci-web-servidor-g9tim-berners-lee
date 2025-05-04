@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Películas Populares</h1>

    @if(count($peliculas) > 0)
        <div class="movie-grid">
            @foreach($peliculas as $pelicula)
                <div class="movie-card">
                    <div class="card-image">
                        <img src="https://image.tmdb.org/t/p/w500{{ $pelicula->poster_path }}"
                             alt="{{ $pelicula->title }}">
                    </div>
                    <div class="card-info">
                        <h3>{{ $pelicula['title'] }}</h3>
                        <div class="card-meta">
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($pelicula['vote_average'], 1) }}</span>
                            </div>
                            <span>{{ \Carbon\Carbon::parse($pelicula['release_date'])->format('Y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <p>No se encontraron películas disponibles.</p>
        </div>
    @endif
</div>
@endsection
