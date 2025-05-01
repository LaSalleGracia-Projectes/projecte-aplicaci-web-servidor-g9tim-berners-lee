@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Series Populares</h1>

    @if(count($series) > 0)
        <div class="movie-grid">
            @foreach($series as $serie)
                <div class="movie-card">
                    <div class="card-image">
                        <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster_path }}"
                             alt="{{ $serie->title }}">
                    </div>
                    <div class="card-info">
                        <h3>{{ $serie['name'] }}</h3>
                        <div class="card-meta">
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($serie['vote_average'], 1) }}</span>
                            </div>
                            <span>{{ \Carbon\Carbon::parse($serie['first_air_date'])->format('Y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <p>No se encontraron series disponibles.</p>
        </div>
    @endif
</div>
@endsection
