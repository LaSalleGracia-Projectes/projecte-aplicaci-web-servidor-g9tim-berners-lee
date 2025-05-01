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

    .action-btn.approve:hover {
        color: var(--verde-neon);
    }

    .action-btn.reject:hover {
        color: var(--accent-danger);
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

    /* Estrellas de valoración */
    .rating-stars i {
        color: var(--verde-neon);
        text-shadow: var(--neon-primary-glow);
    }

    .rating-value {
        font-weight: bold;
        color: var(--verde-neon);
    }

    /* Estilo para botones de exportación */
    .export-btn {
        background: transparent;
        border: 1px solid rgba(20, 255, 20, 0.3);
        color: var(--verde-neon);
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: 10px;
    }

    .export-btn:hover {
        background-color: rgba(20, 255, 20, 0.1);
        border-color: var(--verde-neon);
        box-shadow: var(--neon-primary-glow);
    }

    /* Estilos para los badges de estado */
    .status-badge.approved {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .status-badge.pending {
        background-color: rgba(255, 193, 7, 0.15);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-badge.rejected {
        background-color: rgba(220, 53, 69, 0.15);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }
</style>
@endpush

@section('title', 'Administración de Valoraciones - CritFlix')

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
            <a href="{{ route('admin.movies') }}" class="admin-nav-item">
                <i class="fas fa-film"></i> Películas
            </a>
            <a href="{{ route('admin.reviews') }}" class="admin-nav-item active">
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
                <h2>Gestión de Valoraciones</h2>
                <p>Administra las valoraciones de los usuarios</p>
            </div>
            <div class="admin-header-actions">
                <div class="admin-search">
                    <input type="text" id="search-reviews" placeholder="Buscar valoraciones...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <button class="btn-neon export-reviews-btn">
                    <i class="fas fa-file-export"></i> Exportar
                </button>
            </div>
        </header>

        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="stat-card neon-card">
                <div class="stat-icon review-icon">
                    <i class="fas fa-comment"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Valoraciones</h3>
                    <p class="stat-number">{{ $reviews->total() }}</p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon review-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>Valoración Media</h3>
                    <p class="stat-number">
                        @php
                            try {
                                // Usamos valoracion en lugar de puntuacion que ya no existe
                                $likesCount = DB::table('valoraciones')
                                    ->where('valoracion', 'like')
                                    ->count();
                                $totalCount = DB::table('valoraciones')->count();

                                // Calculamos un puntaje basado en likes/total (5 estrellas máximo)
                                $avgRating = $totalCount > 0 ? ($likesCount / $totalCount) * 5 : 4.2;
                            } catch (\Exception $e) {
                                $avgRating = 4.2; // Valor predeterminado en caso de error
                            }
                        @endphp
                        {{ number_format($avgRating, 1) }}
                    </p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon review-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>Nuevas Hoy</h3>
                    <p class="stat-number">
                        @php
                            $newReviews = DB::table('valoraciones')
                                ->whereDate('created_at', now()->toDateString())
                                ->count();
                        @endphp
                        {{ $newReviews }}
                    </p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon review-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Usuarios Críticos</h3>
                    <p class="stat-number">
                        @php
                            $criticUsers = DB::table('users')
                                ->where('rol', 'critico')
                                ->count();
                        @endphp
                        {{ $criticUsers }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtros de valoraciones -->
        <div class="filters-container neon-card">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros</h3>
                <button class="btn-reset-filters"><i class="fas fa-undo-alt"></i> Restablecer</button>
            </div>
            <div class="filters-body">
                <div class="filter-group">
                    <label>Calificación</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todas</button>
                        <button class="filter-btn" data-filter="5">5 ★</button>
                        <button class="filter-btn" data-filter="4">4 ★</button>
                        <button class="filter-btn" data-filter="3">3 ★</button>
                        <button class="filter-btn" data-filter="2">2 ★</button>
                        <button class="filter-btn" data-filter="1">1 ★</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Tipo de usuario</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todos</button>
                        <button class="filter-btn" data-filter="verified">Verificados</button>
                        <button class="filter-btn" data-filter="unverified">No verificados</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Fecha de creación</label>
                    <div class="date-range">
                        <input type="date" id="date-from" class="neon-input">
                        <span><i class="fas fa-arrow-right"></i></span>
                        <input type="date" id="date-to" class="neon-input">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de valoraciones -->
        <div class="admin-table-card neon-card">
            <div class="table-actions">
                <div class="table-info">
                    <span>Mostrando <strong>{{ $reviews->count() }}</strong> de <strong>{{ $reviews->total() }}</strong> valoraciones</span>
                </div>
                <div class="bulk-actions">
                    <select class="neon-input" id="bulk-action">
                        <option value="">Acciones en masa</option>
                        <option value="delete">Eliminar seleccionadas</option>
                        <option value="highlight">Destacar seleccionadas</option>
                        <option value="unhighlight">Quitar destacadas</option>
                    </select>
                    <button class="btn-neon apply-action" disabled>Aplicar</button>
                </div>
            </div>
            <div class="admin-table-container">
                <table class="admin-table reviews-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Película/Serie</th>
                            <th>Puntuación</th>
                            <th>Comentario</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reviews->count() > 0)
                            @foreach($reviews as $review)
                            @php
                                $userType = DB::table('users')->where('id', $review->user_id)->value('rol') ?? 'usuario';
                            @endphp
                            <tr data-review-id="{{ $review->id }}" data-rating="{{ $review->valoracion == 'like' ? 5 : 1 }}" data-user-type="{{ $userType }}">
                                <td>
                                    <input type="checkbox" class="review-select" data-id="{{ $review->id }}">
                                </td>
                                <td>#{{ $review->id }}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-name">
                                            {{ $review->user_name }}
                                            <span class="user-badge {{ $userType }}">{{ ucfirst($userType) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $review->movie_title }}</td>
                                <td>
                                    <div class="rating">
                                        <span class="rating-value">{{ $review->valoracion == 'like' ? 5 : 1 }}</span>
                                        <div class="rating-stars">
                                            @if($review->valoracion == 'like')
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="comment-preview">
                                        {{ Str::limit($review->comentario, 100) }}
                                        @if(strlen($review->comentario) > 100)
                                            <button class="show-more-btn" data-review-id="{{ $review->id }}">Ver más</button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="status-badge {{ $review->destacado ? 'destacado' : 'normal' }}">
                                        {{ $review->destacado ? 'Destacado' : 'Normal' }}
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="#" class="action-btn view" title="Ver detalles" data-id="{{ $review->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="action-btn highlight {{ $review->destacado ? 'is-highlighted' : '' }}"
                                       title="{{ $review->destacado ? 'Quitar destacado' : 'Destacar' }}"
                                       data-id="{{ $review->id }}">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="#" class="action-btn delete" title="Eliminar" data-id="{{ $review->id }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No hay valoraciones disponibles</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                {{ $reviews->links() }}
            </div>
        </div>

        <!-- Botones de exportación -->
        <div class="export-actions">
            <button class="export-btn" id="exportCSV">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </button>
            <button class="export-btn" id="exportExcel">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="export-btn" id="exportPDF">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de valoración -->
<div id="review-detail-modal" class="admin-modal-container">
    <div class="admin-modal neon-card">
        <div class="modal-header">
            <h3>Detalles de Valoración</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="review-detail-content">
                <div class="review-detail-header">
                    <div class="movie-info">
                        <h4 id="detail-movie-title"></h4>
                        <div class="detail-rating">
                            <div class="rating-stars" id="detail-stars"></div>
                            <span class="rating-value" id="detail-rating"></span>
                        </div>
                    </div>
                    <div class="user-info">
                        <p>Por <span id="detail-user-name"></span></p>
                        <p id="detail-date"></p>
                    </div>
                </div>
                <div class="review-title">
                    <h5 id="detail-review-title"></h5>
                </div>
                <div class="review-text">
                    <p id="detail-comment"></p>
                </div>
                <div class="detail-actions">
                    <button class="btn-neon highlight-review" id="detail-highlight">
                        <i class="fas fa-star"></i> <span>Destacar</span>
                    </button>
                    <button class="btn-neon delete-review">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin.js') }}"></script>
<script src="{{ asset('js/admin/reviews.js') }}"></script>
@endsection
