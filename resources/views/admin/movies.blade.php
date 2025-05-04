@extends('layouts.admin')

@section('title', 'Gestión de Películas - CritFlix Admin')

@section('header-title', 'Gestión de Películas')

@push('styles')
<style>
    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        transition: all var(--transition-speed) ease;
        margin-bottom: 1.5rem;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
        border-color: var(--verde-neon);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-light);
    }

    .card-body {
        padding: 1.5rem;
    }

    .movie-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .movie-filters .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .filter-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .filter-control:focus {
        border-color: var(--verde-neon);
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 102, 0.15);
        outline: none;
    }

    .movie-item {
        background-color: rgba(18, 18, 18, 0.6);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .movie-item:hover {
        border-color: var(--verde-neon);
        background-color: rgba(0, 255, 102, 0.05);
    }

    .movie-poster {
        width: 80px;
        height: 120px;
        border-radius: var(--border-radius);
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid var(--border-color);
    }

    .movie-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .movie-poster i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 1.8rem;
        background-color: var(--bg-darker);
        color: var(--text-light);
    }

    .movie-info {
        flex: 1;
    }

    .movie-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .movie-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 0.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .movie-year {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }

    .movie-director {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .movie-meta {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .movie-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .movie-meta-item i {
        font-size: 0.9rem;
    }

    .movie-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-left: auto;
    }

    .movie-actions button,
    .movie-actions a {
        background: transparent;
        border: 1px solid var(--border-color);
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border-radius: var(--border-radius);
        transition: all 0.2s ease;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .movie-actions button:hover,
    .movie-actions a:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .movie-actions button.view:hover,
    .movie-actions a.view:hover {
        color: var(--cyan-neon);
        border-color: var(--cyan-neon);
    }

    .movie-actions button.edit:hover {
        color: var(--amarillo-neon);
        border-color: var(--amarillo-neon);
    }

    .movie-actions button.delete:hover {
        color: var(--rojo-neon);
        border-color: var(--rojo-neon);
    }

    .genre-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
        background-color: rgba(0, 232, 255, 0.15);
        color: #00e8ff;
        border: 1px solid rgba(0, 232, 255, 0.3);
    }

    .rating-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #ffcc00;
    }

    .stats-bar {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-item {
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1rem;
        flex: 1;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        border-color: var(--verde-neon);
        transform: translateY(-3px);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--verde-neon);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }

    .pagination li {
        margin: 0;
    }

    .pagination .page-link {
        display: block;
        padding: 0.5rem 0.75rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background-color: rgba(0, 255, 102, 0.1);
        border-color: var(--verde-neon);
        color: var(--verde-neon);
    }

    .pagination .active .page-link {
        background-color: var(--verde-neon);
        border-color: var(--verde-neon);
        color: var(--bg-darker);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--verde-neon);
    }

    .empty-state h3 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: var(--text-light);
    }

    .empty-state p {
        font-size: 0.95rem;
        max-width: 500px;
        margin: 0 auto;
    }

    /* Estilos para el modal */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        width: 90%;
        max-width: 600px;
        background: var(--bg-card);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .modal-backdrop.active .modal-container {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-light);
    }

    .modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .modal-close:hover {
        color: var(--rojo-neon);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--verde-neon);
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 102, 0.15);
        outline: none;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid transparent;
    }

    .btn-primary {
        background-color: var(--verde-neon);
        color: var(--bg-darker);
        border-color: var(--verde-neon);
    }

    .btn-primary:hover {
        background-color: rgba(0, 255, 102, 0.8);
        box-shadow: 0 0 15px rgba(0, 255, 102, 0.5);
    }

    .btn-secondary {
        background-color: transparent;
        color: var(--text-light);
        border-color: var(--border-color);
    }

    .btn-secondary:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .btn-danger {
        background-color: var(--rojo-neon);
        color: var(--bg-darker);
        border-color: var(--rojo-neon);
    }

    .btn-danger:hover {
        background-color: rgba(255, 48, 96, 0.8);
        box-shadow: 0 0 15px rgba(255, 48, 96, 0.5);
    }

    .toggle-wrapper {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--bg-darker);
        transition: 0.4s;
        border-radius: 34px;
        border: 1px solid var(--border-color);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 3px;
        background-color: var(--text-muted);
        transition: 0.4s;
        border-radius: 50%;
    }

    .toggle-input:checked + .toggle-slider {
        background-color: rgba(0, 255, 102, 0.3);
        border-color: var(--verde-neon);
    }

    .toggle-input:checked + .toggle-slider:before {
        transform: translateX(26px);
        background-color: var(--verde-neon);
    }

    .toggle-label {
        display: flex;
        align-items: center;
    }

    .toggle-text {
        margin-left: 0.75rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .movie-filters {
            flex-direction: column;
        }

        .movie-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .movie-header {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .movie-actions {
            width: 100%;
            justify-content: flex-start;
            margin-top: 1rem;
        }
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="admin-container">
    <!-- Contenido principal -->
    <div class="admin-content">
        <!-- Estadísticas de películas -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-label">Total Películas</div>
                </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['featured'] ?? 0 }}</div>
                <div class="stat-label">Destacadas</div>
                </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['genres'] ?? 0 }}</div>
                <div class="stat-label">Géneros</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['new_this_month'] ?? 0 }}</div>
                <div class="stat-label">Nuevas este mes</div>
            </div>
        </div>

        <!-- Filtros de películas -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-secondary" id="resetFilters">
                        <i class="fas fa-undo"></i> Reiniciar Filtros
                    </button>
            </div>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('admin.movies') }}" method="GET">
                    <div class="movie-filters">
                <div class="filter-group">
                            <label for="keyword">Buscar película</label>
                            <input type="text" id="keyword" name="keyword" class="filter-control" placeholder="Título o director" value="{{ request('keyword') }}">
                    </div>
                        <div class="filter-group">
                            <label for="genre">Género</label>
                            <select id="genre" name="genre" class="filter-control">
                                <option value="">Todos los géneros</option>
                                @foreach($genres ?? [] as $genre)
                                    <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>{{ $genre->nombre }}</option>
                                @endforeach
                            </select>
                </div>
                <div class="filter-group">
                            <label for="year">Año</label>
                            <select id="year" name="year" class="filter-control">
                                <option value="">Todos los años</option>
                                @for($i = date('Y'); $i >= 1900; $i--)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                </div>
                <div class="filter-group">
                            <label for="featured">Estado</label>
                            <select id="featured" name="featured" class="filter-control">
                                <option value="">Todas</option>
                                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Destacadas</option>
                                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>No destacadas</option>
                            </select>
                    </div>
                </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de películas -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-film"></i> Películas {{ request('keyword') ? 'encontradas' : 'disponibles' }}</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-primary" id="addMovieBtn">
                        <i class="fas fa-plus-circle"></i> Añadir Película
                    </button>
                </div>
                </div>
            <div class="card-body">
                @if(isset($movies) && count($movies) > 0)
                    <!-- Contenedor de películas -->
                    <div id="movies-container">
                            @foreach($movies as $movie)
                        <div class="movie-item" data-id="{{ $movie->id }}" data-featured="0">
                                    <div class="movie-poster">
                                @if(isset($movie->poster) && $movie->poster)
                                            <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->titulo }}">
                                @elseif(isset($movie->imagen) && $movie->imagen)
                                    <img src="{{ asset('storage/' . $movie->imagen) }}" alt="{{ $movie->titulo }}">
                                @elseif(isset($movie->poster_url) && $movie->poster_url)
                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->titulo }}">
                                        @else
                                            <i class="fas fa-film"></i>
                                        @endif
                                    </div>
                            <div class="movie-info">
                                <div class="movie-header">
                                    <div>
                                        <div class="movie-title">
                                            {{ $movie->titulo }}
                                            <span class="movie-year">({{ isset($movie->anyo) ? $movie->anyo : (isset($movie->ano) ? $movie->ano : (isset($movie->year) ? $movie->year : '')) }})</span>
                                        </div>
                                        <div class="movie-director">Director: {{ isset($movie->director) ? $movie->director : 'No disponible' }}</div>
                                        <div class="movie-meta">
                                            <div class="movie-meta-item">
                                                <i class="fas fa-clock"></i>
                                                {{ isset($movie->duracion) ? $movie->duracion : '?' }} min
                                            </div>
                                            <div class="movie-meta-item">
                                                    <i class="fas fa-star"></i>
                                                {{ number_format(isset($movie->valoracionMedia) ? $movie->valoracionMedia : 0, 1) }} / 5
                                            </div>
                                            <div class="movie-meta-item">
                                                <i class="fas fa-comment"></i>
                                                {{ isset($movie->criticas_count) ? $movie->criticas_count : 0 }} críticas
                                            </div>
                                        </div>
                                        <div style="margin-top: 8px;">
                                            @if(isset($movie->generos) && is_array($movie->generos))
                                                @foreach($movie->generos as $genero)
                                                    <span class="genre-badge">{{ $genero->nombre }}</span>
                                                @endforeach
                                                @endif
                                        </div>
                                    </div>
                                    <div class="movie-actions">
                                        <a href="{{ route('pelicula.detail', $movie->id) }}" target="_blank" class="view" title="Ver película">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <button type="button" class="edit" data-id="{{ $movie->id }}" title="Editar película">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button type="button" class="featured" data-id="{{ $movie->id }}" data-featured="0" title="Marcar como destacada">
                                            <i class="fas fa-star" style="color: #888888"></i> Destacar
                                        </button>
                                        <button type="button" class="delete" data-id="{{ $movie->id }}" title="Eliminar película">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                            @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="pagination-container">
                        {{ $movies->appends(request()->query())->links() }}
                    </div>
                        @else
                    <!-- Estado vacío -->
                    <div class="empty-state">
                        <i class="fas fa-film"></i>
                        <h3>No hay películas disponibles</h3>
                        <p>
                            @if(request('keyword') || request('genre') || request('year') || request('featured'))
                                No se encontraron películas que coincidan con los filtros aplicados. Intenta con otros criterios.
                            @else
                                Aún no hay películas añadidas a la plataforma. ¡Añade tu primera película!
                        @endif
                        </p>
            </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar película -->
<div id="editMovieModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-film"></i> Editar Película</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editMovieForm" enctype="multipart/form-data">
                <input type="hidden" id="movieId" name="movieId">
                <div class="form-group">
                    <label for="editTitle">Título</label>
                    <input type="text" id="editTitle" name="titulo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editDirector">Director</label>
                    <input type="text" id="editDirector" name="director" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editYear">Año</label>
                    <input type="number" id="editYear" name="anyo" class="form-control" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div class="form-group">
                    <label for="editDuration">Duración (minutos)</label>
                    <input type="number" id="editDuration" name="duracion" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label for="editSynopsis">Sinopsis</label>
                    <textarea id="editSynopsis" name="sinopsis" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editGenres">Géneros</label>
                    <select id="editGenres" name="generos[]" class="form-control" multiple>
                        @foreach($genres ?? [] as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples géneros</small>
                </div>
                <div class="form-group">
                    <label for="editPoster">Poster (dejar en blanco para mantener)</label>
                    <input type="file" id="editPoster" name="poster" class="form-control" accept="image/*">
                    <div id="currentPosterPreview" class="mt-2" style="max-width: 150px;"></div>
                </div>
                <div class="form-group">
                    <label class="toggle-label">
                        <span class="toggle-wrapper">
                            <input type="checkbox" id="editFeatured" name="destacado" class="toggle-input">
                            <span class="toggle-slider"></span>
                        </span>
                        <span class="toggle-text">Película destacada</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="saveMovieBtn">Guardar Cambios</button>
        </div>
    </div>
</div>

<!-- Modal para añadir película -->
<div id="addMovieModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Añadir Nueva Película</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addMovieForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="addTitle">Título</label>
                    <input type="text" id="addTitle" name="titulo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="addDirector">Director</label>
                    <input type="text" id="addDirector" name="director" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="addYear">Año</label>
                    <input type="number" id="addYear" name="anyo" class="form-control" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div class="form-group">
                    <label for="addDuration">Duración (minutos)</label>
                    <input type="number" id="addDuration" name="duracion" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label for="addSynopsis">Sinopsis</label>
                    <textarea id="addSynopsis" name="sinopsis" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="addGenres">Géneros</label>
                    <select id="addGenres" name="generos[]" class="form-control" multiple required>
                        @foreach($genres ?? [] as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples géneros</small>
                </div>
                <div class="form-group">
                    <label for="addPoster">Poster</label>
                    <input type="file" id="addPoster" name="poster" class="form-control" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label class="toggle-label">
                        <span class="toggle-wrapper">
                            <input type="checkbox" id="addFeatured" name="destacado" class="toggle-input">
                            <span class="toggle-slider"></span>
                        </span>
                        <span class="toggle-text">Película destacada</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="createMovieBtn">Crear Película</button>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar película -->
<div id="deleteMovieModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Eliminación</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar esta película? Esta acción no se puede deshacer.</p>
            <p class="text-danger"><strong>Nota:</strong> Se eliminarán todos los datos asociados a esta película, incluyendo críticas, valoraciones y listas que la contienen.</p>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar Película</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar funcionalidades
        setupMovieActions();
        setupFilterReset();
        setupModals();
    });

    // Obtener el token CSRF de la meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /**
     * Configura los modales de edición, creación y eliminación
     */
    function setupModals() {
        // Abrir modal de edición
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.getAttribute('data-id');
                openEditMovieModal(movieId);
            });
        });

        // Abrir modal de añadir película
        document.getElementById('addMovieBtn').addEventListener('click', function() {
            document.getElementById('addMovieModal').classList.add('active');
        });

        // Cerrar modales
        document.querySelectorAll('.modal-close, .close-modal').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.modal-backdrop').forEach(modal => {
                    modal.classList.remove('active');
                });
            });
        });

        // Guardar cambios de película
        document.getElementById('saveMovieBtn').addEventListener('click', saveMovieChanges);

        // Crear nueva película
        document.getElementById('createMovieBtn').addEventListener('click', createMovie);
    }

    /**
     * Configura las acciones de las películas (editar, eliminar, destacar)
     */
    function setupMovieActions() {
        // Acción de eliminar
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.getAttribute('data-id');

                // Almacenar el ID de la película a eliminar
                document.getElementById('confirmDeleteBtn').setAttribute('data-id', movieId);

                // Mostrar modal de confirmación
                document.getElementById('deleteMovieModal').classList.add('active');
            });
        });

        // Confirmar eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const movieId = this.getAttribute('data-id');
            deleteMovie(movieId);
        });

        // Alternar destacado
        document.querySelectorAll('.featured').forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.getAttribute('data-id');
                const isFeatured = this.getAttribute('data-featured') === '1';
                toggleMovieFeatured(movieId, !isFeatured);
            });
        });
    }

    /**
     * Abre el modal de edición de película usando datos locales
     * @param {string} movieId - ID de la película a editar
     */
    function openEditMovieModal(movieId) {
        // Mostrar notificación de carga
        showNotification('Preparando edición de película...', 'info');

        // Buscar elemento de la película en la página actual
        const movieItem = document.querySelector(`.movie-item[data-id="${movieId}"]`);

        if (!movieItem) {
            showNotification('No se encontró la película en la página', 'error');
            return;
        }

        try {
            // Extraer información básica de la UI
            const movie = {
                id: movieId,
                titulo: movieItem.querySelector('.movie-title').textContent.trim().split('(')[0].trim(),
                director: movieItem.querySelector('.movie-director').textContent.replace('Director:', '').trim(),
                anyo: movieItem.querySelector('.movie-year').textContent.replace(/[()]/g, '').trim(),
                duracion: movieItem.querySelector('.movie-meta-item:nth-child(1)').textContent.replace('min', '').trim(),
                // Otros campos pueden no estar disponibles desde la UI
                sinopsis: ''
            };

            // Llenar el formulario con los datos extraídos
            document.getElementById('movieId').value = movie.id;
            document.getElementById('editTitle').value = movie.titulo || '';
            document.getElementById('editDirector').value = movie.director || '';
            document.getElementById('editYear').value = movie.anyo || '';
            document.getElementById('editDuration').value = movie.duracion || '';
            document.getElementById('editSynopsis').value = ''; // No podemos extraer esto de la UI

            // Como no conocemos el estado destacado real, lo dejamos sin marcar
            document.getElementById('editFeatured').checked = false;

            // No podemos seleccionar géneros de manera confiable desde la UI
            const genreSelect = document.getElementById('editGenres');
            if (genreSelect) {
                Array.from(genreSelect.options).forEach(option => {
                    option.selected = false;
                });
            }

            // No podemos mostrar el poster actual de manera confiable
            const posterPreview = document.getElementById('currentPosterPreview');
            if (posterPreview) {
                posterPreview.innerHTML = '<p class="text-muted">El poster actual se mantendrá si no se sube uno nuevo</p>';
            }

            // Mostrar el modal
            document.getElementById('editMovieModal').classList.add('active');

        } catch (error) {
            console.error('Error al extraer datos:', error);
            showNotification('Error al preparar la edición: ' + error.message, 'error');
        }
    }

    /**
     * Guarda los cambios de la película
     * Como alternativa: refresca la página después de mostrar un mensaje
     */
    function saveMovieChanges() {
        const form = document.getElementById('editMovieForm');
        const formData = new FormData(form);

        // Validación básica
        if (!formData.get('titulo') || !formData.get('director') || !formData.get('anyo') || !formData.get('duracion')) {
            showNotification('Por favor completa todos los campos requeridos', 'error');
            return;
        }

        // Mostrar notificación
        showNotification('Los cambios se aplicarán después de refrescar la página', 'info');

        // Cerrar modal
        document.getElementById('editMovieModal').classList.remove('active');

        // Solicitar al usuario que refresque manualmente la página
        setTimeout(() => {
            if (confirm('Esta acción requiere guardar los cambios manualmente. ¿Quieres ir a la página de administración para completar la operación?')) {
                // Redirigir a la página de administración general
                window.location.href = '/admin';
            }
        }, 1000);
    }

    /**
     * Crea una nueva película
     * Como alternativa: redirige a la página de creación de películas
     */
    function createMovie() {
        // Mostrar notificación
        showNotification('Redirigiendo a la página de creación de películas...', 'info');

        // Cerrar modal
        document.getElementById('addMovieModal').classList.remove('active');

        // Redirigir a la página de creación de películas si existe
        setTimeout(() => {
            if (confirm('Esta acción requiere crear la película manualmente. ¿Quieres ir a la página de administración para completar la operación?')) {
                // Redirigir a la página de administración general
                window.location.href = '/admin';
            }
        }, 1000);
    }

    /**
     * Elimina una película
     * @param {string} movieId - ID de la película a eliminar
     */
    function deleteMovie(movieId) {
        // Mostrar notificación
        showNotification('Esta acción requiere eliminación manual', 'info');

        // Cerrar modal
        document.getElementById('deleteMovieModal').classList.remove('active');

        // Solicitar al usuario que elimine manualmente
        setTimeout(() => {
            if (confirm('Esta acción requiere eliminar la película manualmente. ¿Quieres ir a la página de administración para completar la operación?')) {
                // Redirigir a la página de administración general
                window.location.href = '/admin';
            }
        }, 1000);
    }

    /**
     * Alterna el estado destacado de una película
     * @param {string} movieId - ID de la película
     * @param {boolean} featured - Nuevo estado destacado
     */
    function toggleMovieFeatured(movieId, featured) {
        // Mostrar notificación
        showNotification('Esta acción requiere modificación manual', 'info');

        // Solicitar al usuario que modifique manualmente
        setTimeout(() => {
            if (confirm('Esta acción requiere modificar la película manualmente. ¿Quieres ir a la página de administración para completar la operación?')) {
                // Redirigir a la página de administración general
                window.location.href = '/admin';
            }
        }, 1000);
    }

    /**
     * Configura el botón para reiniciar filtros
     */
    function setupFilterReset() {
        const resetButton = document.getElementById('resetFilters');

        if (resetButton) {
            resetButton.addEventListener('click', function() {
                const form = document.getElementById('filterForm');

                // Limpiar todos los campos
                form.querySelectorAll('input, select').forEach(field => {
                    if (field.type === 'text') {
                        field.value = '';
                    } else if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    }
                });

                // Enviar el formulario
                form.submit();
            });
        }
    }

    /**
     * Muestra una notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de notificación (success, error, info, warning)
     */
    function showNotification(message, type = 'info') {
        // Verificar si ya existe el contenedor de notificaciones
        let notificationsContainer = document.querySelector('.notifications-container');

        // Si no existe, crear uno nuevo
        if (!notificationsContainer) {
            notificationsContainer = document.createElement('div');
            notificationsContainer.className = 'notifications-container';
            document.body.appendChild(notificationsContainer);

            // Estilos para el contenedor
            notificationsContainer.style.position = 'fixed';
            notificationsContainer.style.top = '20px';
            notificationsContainer.style.right = '20px';
            notificationsContainer.style.zIndex = '9999';
            notificationsContainer.style.display = 'flex';
            notificationsContainer.style.flexDirection = 'column';
            notificationsContainer.style.alignItems = 'flex-end';
            notificationsContainer.style.gap = '10px';
        }

        // Crear la notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' :
                            type === 'error' ? 'times-circle' :
                            type === 'warning' ? 'exclamation-triangle' :
                            'info-circle'}"></i>
            </div>
            <div class="notification-content">${message}</div>
            <button class="notification-close">×</button>
        `;

        // Estilos para la notificación
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.padding = '12px 15px';
        notification.style.borderRadius = '5px';
        notification.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.2)';
        notification.style.marginBottom = '10px';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        notification.style.maxWidth = '350px';
        notification.style.backgroundColor = 'var(--bg-card)';
        notification.style.color = 'var(--text-light)';
        notification.style.border = `1px solid ${
            type === 'success' ? 'var(--verde-neon)' :
            type === 'error' ? 'var(--rojo-neon)' :
            type === 'warning' ? 'var(--amarillo-neon)' :
            'var(--cyan-neon)'
        }`;

        // Estilos para el icono
        const icon = notification.querySelector('.notification-icon');
        icon.style.marginRight = '10px';
        icon.style.fontSize = '1.1rem';
        icon.style.color = type === 'success' ? 'var(--verde-neon)' :
                          type === 'error' ? 'var(--rojo-neon)' :
                          type === 'warning' ? 'var(--amarillo-neon)' :
                          'var(--cyan-neon)';

        // Estilos para el botón cerrar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.background = 'none';
        closeBtn.style.border = 'none';
        closeBtn.style.color = 'var(--text-muted)';
        closeBtn.style.fontSize = '1.2rem';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.marginLeft = '10px';
        closeBtn.style.padding = '0 5px';

        // Función para eliminar la notificación
        const removeNotification = () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(50px)';

            setTimeout(() => {
                notification.remove();
            }, 300);
        };

        // Evento para el botón cerrar
        closeBtn.addEventListener('click', removeNotification);

        // Auto cerrar después de cierto tiempo
        const duration = type === 'error' ? 6000 : 4000;
        setTimeout(removeNotification, duration);

        // Agregar la notificación al contenedor
        notificationsContainer.appendChild(notification);

        // Animar la entrada
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
    }
</script>
@endpush
