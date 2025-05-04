@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="create-list-container">
    <div class="create-list-header">
        <div class="header-content">
            <h1><i class="fas fa-edit"></i> Editar Lista</h1>
            <p class="subtitle">Modifica los detalles de tu lista personalizada</p>
        </div>
        <a href="{{ route('profile.show', $lista->user_id) }}" class="btn-neon">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>
    </div>

    <div class="create-list-form-container">
        <form action="{{ route('listas.update', $lista->id) }}" method="POST" class="create-list-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-group">
                    <label for="nombre_lista">
                        <i class="fas fa-heading"></i>
                        Nombre de la Lista
                    </label>
                    <div class="input-wrapper">
                        <input type="text"
                               id="nombre_lista"
                               name="nombre_lista"
                               class="form-control @error('nombre_lista') is-invalid @enderror"
                               value="{{ old('nombre_lista', $lista->nombre_lista) }}"
                               placeholder="Ej: Películas de Ciencia Ficción"
                               required>
                        @error('nombre_lista')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <div class="input-wrapper">
                        <textarea id="descripcion"
                                name="descripcion"
                                class="form-control @error('descripcion') is-invalid @enderror"
                                rows="4"
                                placeholder="Describe tu lista (opcional)">{{ old('descripcion', $lista->descripcion) }}</textarea>
                        @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div class="description-tips">
                            <p>Sugerencias:</p>
                            <ul>
                                <li>Describe el tema o género de las películas</li>
                                <li>Menciona qué hace única a esta lista</li>
                                <li>Añade hashtags relevantes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-film"></i>
                        Películas en la Lista
                    </label>
                    <div class="input-wrapper">
                        <div class="search-container">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="listMovieSearch" placeholder="Buscar películas para añadir...">
                                <div id="listSuggestions" class="search-suggestions"></div>
                            </div>
                        </div>

                        <div class="list-content">
                            @if(count($lista->contenidosListas) > 0)
                                <div class="movie-grid">
                                    @foreach($lista->contenidosListas as $contenido)
                                    <div class="movie-card">
                                        <div class="movie-poster">
                                            <img src="https://image.tmdb.org/t/p/w500{{ $contenido->pelicula['poster_path'] }}"
                                                 alt="{{ $contenido->pelicula['title'] }}">
                                            <div class="movie-actions">
                                                <button type="button" class="remove-movie" data-id="{{ $contenido->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="movie-info">
                                            <h3>{{ $contenido->pelicula['title'] }}</h3>
                                            <div class="movie-meta">
                                                <span>{{ $contenido->pelicula['release_date'] ? \Carbon\Carbon::parse($contenido->pelicula['release_date'])->format('Y') : 'N/A' }}</span>
                                                <span>{{ $contenido->pelicula['vote_average'] ?? 'N/A' }} <i class="fas fa-star"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>Esta lista no contiene películas todavía.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" onclick="window.history.back();" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-neon btn-create">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="tmdb-api-key" content="{{ env('TMDB_API_KEY') }}">
<meta name="lista-id" content="{{ $lista->id }}">
<script>
    const defaultPosterImage = '{{ asset('images/default-poster.jpg') }}';
</script>
<script src="{{ asset('js/listas.js') }}"></script>
@endpush
@endsection


