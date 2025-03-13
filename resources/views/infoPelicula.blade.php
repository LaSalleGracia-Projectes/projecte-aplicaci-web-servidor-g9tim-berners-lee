@extends('layouts.app')

@section('title', $pelicula['title'] . ' - CrítiFlix')

@section('content')
<main class="pelicula-detalle">
    <!-- Banner grande -->
    <div class="banner">
        <img src="https://image.tmdb.org/t/p/original{{ $pelicula['backdrop_path'] }}" alt="{{ $pelicula['title'] }}">
        <div class="titulo-overlay">
            <h1>{{ $pelicula['title'] }}</h1>
            <p class="tagline">{{ $pelicula['tagline'] }}</p>
        </div>
    </div>

    <div class="info-container">
                    <div class="info">
                <div class="datos-basicos">
                    <p><strong>Fecha de estreno:</strong> {{ $pelicula['release_date'] }}</p>
                    <p><strong>Duración:</strong> {{ $pelicula['runtime'] }} min</p>
                    <p><strong>Géneros:</strong> {{ implode(', ', array_column($pelicula['genres'], 'name')) }}</p>
                    <p><strong>Calificación:</strong> <span class="estrellas">⭐ {{ number_format($pelicula['vote_average'], 1) }}</span></p>
                </div>

                <div class="sinopsis">
                    <h3>Sinopsis</h3>
                    <p>{{ $pelicula['overview'] }}</p>
                </div>

                <div class="produccion">
                    <p><strong>Director:</strong> {{ $director['name'] ?? 'Información no disponible' }}</p>
                    <p><strong>Productora:</strong> {{ implode(', ', array_column($pelicula['production_companies'], 'name')) }}</p>
                </div>

                <br>

                <div class="elenco">
                    <h3>Reparto principal</h3>
                    <div class="actores">
                        @foreach($elenco as $actor)
                        @if($loop->index < 10)
                            <div class="actor">
                                <img src="https://image.tmdb.org/t/p/w138_and_h175_face{{ $actor['profile_path'] }}"
                                    alt="{{ $actor['name'] }}"
                                    onerror="this.src='/img/perfil-default.jpg'">
                                <div class="actor-info">
                                    <p class="actor-nombre">{{ $actor['name'] }}</p>
                                    <p class="actor-personaje">{{ $actor['character'] }}</p>
                                </div>
                            </div>
                            @endif
                            @endforeach
                    </div>
                </div>
            </div>
        <div class="columna-izquierda">
            <div class="poster">
                <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] }}" alt="{{ $pelicula['title'] }}">
            </div>

            <div class="donde-ver">
                <h3>Disponible en:</h3>
                <div class="plataformas">
                    @forelse($watchProviders['flatrate'] ?? [] as $plataforma)
                    <div class="plataforma">
                        <img src="https://image.tmdb.org/t/p/w200{{ $plataforma['logo_path'] }}" alt="{{ $plataforma['provider_name'] }}">
                        <span>{{ $plataforma['provider_name'] }}</span>
                    </div>
                    @empty
                    <p>Información no disponible</p>
                    @endforelse
                </div>
            </div>
        </div>
</main>

<style>

:root {
    /* Colores principales */
    --verde-neon: #14ff14;
    --verde-principal: #00ff3c;
    --verde-claro: #00ffdd;
    --blanco: #FFFFFF;
    --negro: #000000;
    --negro-suave: #121212;
    --azul-oscuro: #001233;
    /* Colores de estados */
    --verde-pastel: #66BB6A;
    --verde-oscuro: #1B5E20;
    --rojo-suave: #E53935;
}
    .pelicula-detalle {
        max-width: 1000px;
        margin: auto;
        padding: 0 15px;
    }

    .banner {
        position: relative;

    }

    .banner img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        filter: brightness(0.7);
        margin-top: 10px;
    }

    .titulo-overlay {
        left: 30px;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    .titulo-overlay h1 {
        margin-bottom: 5px;
        font-size: 4rem;
        margin-top: 30px;
    }

    .titulo-overlay .tagline {
        font-style: italic;
    }

    .info-container {
    display: flex;
    flex-direction: row-reverse;
    align-items: flex-start;
    gap: 15px;
}
    .columna-izquierda {
        width: 200px;
        flex-shrink: 0;
    }

    .poster img {
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }

    .donde-ver {
        margin-top: 20px;
        padding: 15px;
        background-color:var(--verde-neon);
        border-radius: 10px;
        color:var(--negro)
    }

    .plataformas {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .plataforma {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .plataforma img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .info {
        flex-grow: 1;
    }

    .datos-basicos {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .estrellas {
        color: #FFC107;
        font-weight: bold;
    }

    .sinopsis {
        margin-bottom: 25px;
    }

    .elenco {
        margin-bottom: 25px;
    }

    .actores {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 2fr));
        gap: 15px;
        margin-top: 15px;
    }

    .actor {
        display: flex;
        flex-direction: column;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        transition: 0.3s;
    }

    .actor:hover {
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.5);
        scale: 1.05;
        cursor: pointer;
    }

    .actor img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .actor-info {
        padding: 10px;
        background-color:var(--verde-neon);
    }

    .actor-nombre {
        font-weight: bold;
        margin-bottom: 3px;
        color:var(--negro);
    }

    .actor-personaje {
        font-size: 0.9rem;
        color:var(--negro);
    }

    .produccion {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .info-container {
            flex-direction: column;
        }

        .columna-izquierda {
            width: 100%;
        }

        .poster img {
            max-width: 250px;
            margin: 0 auto;
            display: block;
        }

        .datos-basicos {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
