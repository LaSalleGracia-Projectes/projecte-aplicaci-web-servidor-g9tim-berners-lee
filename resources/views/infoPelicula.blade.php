@extends('layouts.app')

@section('title', $pelicula['title'] . ' - CrítiFlix')

@section('content')
<main class="pelicula-detalle">
    <!-- Banner grande -->
    <div class="banner">
        <img src="https://image.tmdb.org/t/p/original{{ $pelicula['backdrop_path'] }}" alt="{{ $pelicula['title'] }}">
    </div>

    <!-- Contenedor principal -->
    <div class="info-container">
        <!-- Imagen del póster -->
        <div class="poster">
            <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] }}" alt="{{ $pelicula['title'] }}">
        </div>

        <!-- Información de la película -->
        <div class="info">
            <h1>{{ $pelicula['title'] }}</h1>
            <p><strong>Fecha de estreno:</strong> {{ $pelicula['release_date'] }}</p>
            <p><strong>Duración:</strong> {{ $pelicula['runtime'] }} min</p>
            <p><strong>Géneros:</strong> {{ implode(', ', array_column($pelicula['genres'], 'name')) }}</p>
            <p><strong>Sinopsis:</strong> {{ $pelicula['overview'] }}</p>
            <p><strong>Calificación:</strong> ⭐ {{ number_format($pelicula['vote_average'], 1) }}</p>
        </div>
    </div>
</main>

<style>
    .pelicula-detalle {
        max-width: 900px;
        margin: auto;
    }
    .banner img {
        width: 100%;
        border-radius: 10px;
    }
    .info-container {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-top: 20px;
    }
    .poster img {
        width: 200px;
        border-radius: 10px;
    }
    .info {
        flex-grow: 1;
    }
</style>
@endsection
