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
        <a href="javascript:void(0);" onclick="window.history.back();" class="btn-neon">
            <i class="fas fa-arrow-left"></i> Volver
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
                                                 alt="{{ $contenido->pelicula['title'] }}"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/default-poster.jpg') }}'">
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
@endsection

@push('styles')
<style>
.search-container {
    margin-bottom: 1.5rem;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid var(--verde-neon);
    border-radius: 8px;
    padding: 0.5rem 1rem;
}

.search-icon {
    color: var(--verde-neon);
    margin-right: 0.5rem;
}

.search-box input {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--blanco);
    font-size: 1rem;
    padding: 0.5rem;
    outline: none;
}

.search-box input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.9);
    border: 1px solid var(--verde-neon);
    border-radius: 8px;
    margin-top: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.search-suggestions.active {
    display: block;
}

.suggestion-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover {
    background-color: rgba(0, 255, 135, 0.1);
}

.suggestion-item img {
    width: 50px;
    height: 75px;
    object-fit: cover;
    border-radius: 4px;
}

.suggestion-info {
    flex: 1;
}

.suggestion-title {
    color: var(--blanco);
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.suggestion-meta {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('listMovieSearch');
    const suggestionsDiv = document.getElementById('listSuggestions');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsDiv.classList.remove('active');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/tmdb/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsDiv.innerHTML = '';
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(movie => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerHTML = `
                                <img src="https://image.tmdb.org/t/p/w92${movie.poster_path}" alt="${movie.title}" onerror="this.onerror=null; this.src='{{ asset('images/default-poster.jpg') }}'">
                                <div class="suggestion-info">
                                    <div class="suggestion-title">${movie.title}</div>
                                    <div class="suggestion-meta">
                                        ${movie.release_date ? movie.release_date.split('-')[0] : 'N/A'} •
                                        ${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'} <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            `;
                            div.addEventListener('click', () => addMovieToList(movie));
                            suggestionsDiv.appendChild(div);
                        });
                        suggestionsDiv.classList.add('active');
                    } else {
                        suggestionsDiv.classList.remove('active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    suggestionsDiv.classList.remove('active');
                });
        }, 300);
    });

    function addMovieToList(movie) {
        fetch(`/api/tmdb/movie/${movie.id}`)
            .then(response => response.json())
            .then(movieDetails => {
                return fetch('/api/contenido-listas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_lista: {{ $lista->id }},
                        tmdb_id: movieDetails.id,
                        title: movieDetails.title,
                        poster_path: movieDetails.poster_path,
                        release_date: movieDetails.release_date,
                        vote_average: movieDetails.vote_average
                    })
                });
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    location.reload();
                } else {
                    alert('Error al añadir la película a la lista');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al añadir la película a la lista');
            });
    }

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.remove('active');
        }
    });

    // Eliminar película de la lista
    document.querySelectorAll('.remove-movie').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar esta película de la lista?')) {
                fetch(`/api/contenido-listas/${this.dataset.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.message) {
                        location.reload();
                    } else {
                        alert('Error al eliminar la película de la lista');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar la película de la lista');
                });
            }
        });
    });
});
</script>
@endpush


