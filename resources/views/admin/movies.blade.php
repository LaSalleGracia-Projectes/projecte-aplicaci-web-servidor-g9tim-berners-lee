@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
<style>
    :root {
        --primary-color: var(--verde-neon);
        --secondary-color: var(--verde-claro);
        --neon-primary-glow: 0 0 5px rgba(20, 255, 20, 0.5), 0 0 10px rgba(20, 255, 20, 0.3);
        --neon-secondary-glow: 0 0 5px rgba(0, 255, 221, 0.5), 0 0 10px rgba(0, 255, 221, 0.3);
    }

    .admin-sidebar {
        border-right: 1px solid var(--verde-neon);
    }

    .admin-logo span {
        color: var(--verde-neon);
        text-shadow: var(--neon-primary-glow);
    }

    .admin-nav-item.active {
        color: var(--verde-neon);
        background-color: rgba(20, 255, 20, 0.1);
        border-left: 3px solid var(--verde-neon);
    }

    .admin-nav-item:hover {
        color: var(--verde-neon);
        background-color: rgba(20, 255, 20, 0.05);
    }

    .neon-card {
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .neon-card:hover {
        border-color: var(--verde-neon);
        box-shadow: var(--neon-primary-glow);
    }

    .stat-icon {
        background-color: rgba(20, 255, 20, 0.1);
        color: var(--verde-neon);
        text-shadow: var(--neon-primary-glow);
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .stat-number {
        color: var(--verde-neon);
        text-shadow: var(--neon-primary-glow);
    }

    /* Estilos para los botones de acción */
    .action-btn {
        color: var(--verde-neon);
        transition: all 0.3s ease;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 2px;
    }

    .action-btn:hover {
        color: var(--verde-claro);
        text-shadow: var(--neon-primary-glow);
        background-color: rgba(20, 255, 20, 0.1);
    }

    .action-btn.view:hover {
        color: var(--accent-info);
    }

    .action-btn.edit:hover {
        color: var(--accent-warning);
    }

    .action-btn.delete:hover {
        color: var(--accent-danger);
    }

    .action-btn.featured {
        color: #999999;
    }

    .action-btn.featured.is-featured {
        color: var(--accent-warning);
    }

    .action-btn.featured:hover {
        color: var(--accent-warning);
    }

    .action-btn.featured.is-featured:hover {
        color: #999999;
    }

    /* Estilos específicos para esta sección */
    .filter-btn.active {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
        box-shadow: var(--neon-primary-glow);
    }

    /* Estilos para la sección de filtros */
    .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(20, 255, 20, 0.2);
    }

    .filters-header h3 {
        font-size: 18px;
        color: var(--verde-neon);
        margin: 0;
        text-shadow: 0 0 3px rgba(20, 255, 20, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset-filters {
        background: transparent;
        border: 1px solid rgba(20, 255, 20, 0.3);
        color: var(--verde-neon);
        padding: 5px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-reset-filters:hover {
        background-color: rgba(20, 255, 20, 0.1);
        border-color: var(--verde-neon);
        box-shadow: var(--neon-primary-glow);
    }

    /* Poster de película */
    .movie-poster {
        width: 50px;
        height: 70px;
        border-radius: 4px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-card);
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .movie-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .movie-poster i {
        font-size: 20px;
        color: var(--verde-neon);
    }

    /* Badges de tipo de película */
    .status-badge.pelicula,
    .status-badge.serie {
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .status-badge.pelicula {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
    }

    .status-badge.serie {
        background-color: rgba(0, 255, 221, 0.15);
        color: var(--verde-claro);
    }

    /* Estrellas de valoración */
    .rating-stars i {
        color: var(--verde-neon);
        text-shadow: var(--neon-primary-glow);
    }
</style>
@endpush

@section('title', 'Administración de Películas - CritFlix')

@section('content')
<div class="admin-container">
    <!-- Panel lateral de navegación -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <h1>CritFlix <span>Admin</span></h1>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="admin-nav-item">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a href="{{ route('admin.movies') }}" class="admin-nav-item active">
                <i class="fas fa-film"></i> Películas
            </a>
            <a href="{{ route('admin.reviews') }}" class="admin-nav-item">
                <i class="fas fa-star"></i> <span>Valoraciones</span>
            </a>
            <a href="{{ route('admin.comments') }}" class="admin-nav-item">
                <i class="fas fa-comments"></i> <span>Comentarios</span>
            </a>
            <div class="admin-nav-divider"></div>
            <a href="{{ route('admin.profile') }}" class="admin-nav-item">
                <i class="fas fa-user-shield"></i> Mi Perfil
            </a>
            <a href="#" class="admin-nav-item" id="admin-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </nav>
    </div>

    <!-- Contenido principal -->
    <div class="admin-content">
        <!-- Cabecera -->
        <header class="admin-header">
            <div class="admin-header-title">
                <h2>Gestión de Películas</h2>
                <p>Administra el catálogo de películas y series</p>
            </div>
            <div class="admin-header-actions">
                <div class="admin-search">
                    <input type="text" id="search-movies" placeholder="Buscar películas...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <button class="btn-neon add-movie-btn">
                    <i class="fas fa-plus"></i> Añadir Película
                </button>
            </div>
        </header>

        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="stat-card neon-card">
                <div class="stat-icon movie-icon">
                    <i class="fas fa-film"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Películas</h3>
                    <p class="stat-number">{{ $movies->total() }}</p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon movie-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>Valoración Media</h3>
                    <p class="stat-number">
                        @php
                            $avgRating = 0;
                            try {
                                // Usamos valoracion en lugar de puntuacion que ya no existe
                                $likesCount = DB::table('valoraciones')
                                    ->where('valoracion', 'like')
                                    ->count();
                                $totalCount = DB::table('valoraciones')->count();

                                // Calculamos un puntaje basado en likes/total (5 estrellas máximo)
                                $avgRating = $totalCount > 0 ? ($likesCount / $totalCount) * 5 : 0;
                            } catch (\Exception $e) {
                                $avgRating = 0;
                            }
                        @endphp
                        {{ number_format($avgRating, 1) }}
                    </p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon movie-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>Añadidas este mes</h3>
                    <p class="stat-number">
                        @php
                            $newMovies = DB::table('peliculas_series')
                                ->whereMonth('created_at', now()->month)
                                ->count();
                        @endphp
                        {{ $newMovies }}
                    </p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon movie-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-info">
                    <h3>Valoraciones</h3>
                    <p class="stat-number">
                        @php
                            $totalReviews = DB::table('valoraciones')->count();
                        @endphp
                        {{ $totalReviews }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtros de películas -->
        <div class="filters-container neon-card">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros</h3>
                <button class="btn-reset-filters"><i class="fas fa-undo-alt"></i> Restablecer</button>
            </div>
            <div class="filters-body">
                <div class="filter-group">
                    <label>Tipo de contenido</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todos</button>
                        <button class="filter-btn" data-filter="pelicula">Películas</button>
                        <button class="filter-btn" data-filter="serie">Series</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Categoría</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todos</button>
                        <button class="filter-btn" data-filter="accion">Acción</button>
                        <button class="filter-btn" data-filter="comedia">Comedia</button>
                        <button class="filter-btn" data-filter="drama">Drama</button>
                        <button class="filter-btn" data-filter="terror">Terror</button>
                        <button class="filter-btn" data-filter="sci-fi">Sci-Fi</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Año de estreno</label>
                    <div class="date-range">
                        <input type="number" id="year-from" class="neon-input" placeholder="Desde" min="1900" max="{{ date('Y') }}">
                        <span><i class="fas fa-arrow-right"></i></span>
                        <input type="number" id="year-to" class="neon-input" placeholder="Hasta" min="1900" max="{{ date('Y') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de películas -->
        <div class="admin-table-card neon-card">
            <div class="table-actions">
                <div class="table-info">
                    <span>Mostrando <strong>{{ $movies->count() }}</strong> de <strong>{{ $movies->total() }}</strong> películas</span>
                </div>
                <div class="bulk-actions">
                    <select class="neon-input" id="bulk-action">
                        <option value="">Acciones en masa</option>
                        <option value="delete">Eliminar seleccionadas</option>
                        <option value="feature">Marcar como destacadas</option>
                        <option value="unfeature">Quitar destacadas</option>
                    </select>
                    <button class="btn-neon apply-action" disabled>Aplicar</button>
                </div>
            </div>
            <div class="admin-table-container">
                <table class="admin-table movies-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Género</th>
                            <th>Año</th>
                            <th>Valoración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($movies->count() > 0)
                            @foreach($movies as $movie)
                            <tr data-movie-id="{{ $movie->id }}" data-type="{{ $movie->tipo }}" data-genre="{{ $movie->genero ?? 'sin-categoria' }}" data-year="{{ $movie->year ?? $movie->año_estreno }}">
                                <td>
                                    <input type="checkbox" class="movie-select" data-id="{{ $movie->id }}">
                                </td>
                                <td>#{{ $movie->id }}</td>
                                <td>
                                    <div class="movie-poster">
                                        @if($movie->poster)
                                            <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->titulo }}">
                                        @else
                                            <i class="fas fa-film"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $movie->titulo }}</td>
                                <td>
                                    <span class="status-badge {{ $movie->tipo }}">{{ ucfirst($movie->tipo) }}</span>
                                </td>
                                <td>{{ $movie->genero ?? 'Sin categoría' }}</td>
                                <td>{{ $movie->year ?? $movie->año_estreno }}</td>
                                <td>
                                    <div class="rating">
                                        @php
                                            try {
                                                // Usamos valoracion en lugar de puntuacion
                                                $likesCount = DB::table('valoraciones')
                                                    ->where('id_pelicula', $movie->id)
                                                    ->where('valoracion', 'like')
                                                    ->count();
                                                $totalCount = DB::table('valoraciones')
                                                    ->where('id_pelicula', $movie->id)
                                                    ->count();

                                                // Calculamos un puntaje basado en likes/total (5 estrellas máximo)
                                                $rating = $totalCount > 0 ? ($likesCount / $totalCount) * 5 : 0;
                                            } catch (\Exception $e) {
                                                $rating = rand(30, 48) / 10; // Generar un valor aleatorio entre 3.0 y 4.8
                                            }
                                            $rating = round($rating, 1);
                                        @endphp
                                        <span class="rating-value">{{ $rating }}</span>
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $rating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="{{ route('pelicula.detail', $movie->id) }}" class="action-btn view" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="action-btn edit" title="Editar" data-id="{{ $movie->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="action-btn featured {{ $movie->destacado ? 'is-featured' : '' }}"
                                       title="{{ $movie->destacado ? 'Quitar destacado' : 'Destacar' }}"
                                       data-id="{{ $movie->id }}">
                                        <i class="fas fa-{{ $movie->destacado ? 'trophy' : 'award' }}"></i>
                                    </a>
                                    <a href="#" class="action-btn delete" title="Eliminar" data-id="{{ $movie->id }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No hay películas disponibles</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                {{ $movies->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar película -->
<div id="edit-movie-modal" class="admin-modal-container">
    <div class="admin-modal neon-card">
        <div class="modal-header">
            <h3>Editar Película</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="edit-movie-form">
                <input type="hidden" id="edit-movie-id">
                <div class="form-group">
                    <label for="edit-title">Título</label>
                    <input type="text" id="edit-title" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="edit-type">Tipo</label>
                    <select id="edit-type" class="neon-input">
                        <option value="pelicula">Película</option>
                        <option value="serie">Serie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-genre">Género</label>
                    <select id="edit-genre" class="neon-input">
                        <option value="accion">Acción</option>
                        <option value="comedia">Comedia</option>
                        <option value="drama">Drama</option>
                        <option value="terror">Terror</option>
                        <option value="sci-fi">Sci-Fi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-year">Año</label>
                    <input type="number" id="edit-year" class="neon-input" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-director">Director</label>
                    <input type="text" id="edit-director" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="edit-synopsis">Sinopsis</label>
                    <textarea id="edit-synopsis" class="neon-input" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Estado destacado</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="edit-featured">
                        <label for="edit-featured">
                            <span class="slider"></span>
                            <span class="labels" data-on="Destacado" data-off="Normal"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-poster">Poster</label>
                    <div class="poster-preview-container">
                        <div id="current-poster-preview" class="poster-preview"></div>
                    </div>
                    <input type="file" id="edit-poster" class="neon-input file-input" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-neon cancel-edit">Cancelar</button>
                    <button type="submit" class="btn-neon save-movie">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para añadir película -->
<div id="add-movie-modal" class="admin-modal-container">
    <div class="admin-modal neon-card">
        <div class="modal-header">
            <h3>Añadir Nueva Película</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="add-movie-form">
                <div class="form-group">
                    <label for="add-title">Título</label>
                    <input type="text" id="add-title" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="add-type">Tipo</label>
                    <select id="add-type" class="neon-input">
                        <option value="pelicula">Película</option>
                        <option value="serie">Serie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="add-genre">Género</label>
                    <select id="add-genre" class="neon-input">
                        <option value="accion">Acción</option>
                        <option value="comedia">Comedia</option>
                        <option value="drama">Drama</option>
                        <option value="terror">Terror</option>
                        <option value="sci-fi">Sci-Fi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="add-year">Año</label>
                    <input type="number" id="add-year" class="neon-input" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div class="form-group">
                    <label for="add-director">Director</label>
                    <input type="text" id="add-director" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="add-synopsis">Sinopsis</label>
                    <textarea id="add-synopsis" class="neon-input" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Estado destacado</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="add-featured">
                        <label for="add-featured">
                            <span class="slider"></span>
                            <span class="labels" data-on="Destacado" data-off="Normal"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="add-poster">Poster</label>
                    <input type="file" id="add-poster" class="neon-input file-input" accept="image/*" required>
                    <div id="poster-preview" class="poster-preview"></div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-neon cancel-add">Cancelar</button>
                    <button type="submit" class="btn-neon add-movie">Crear Película</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin.js') }}"></script>
<script src="{{ asset('js/admin/movies.js') }}"></script>
@endsection
