@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
<style>
    /* Estilos específicos para comentarios */
    body {
        padding-top: 0;
        background-color: #0a0a0a;
    }

    .admin-container {
        display: flex;
        min-height: 100vh;
        background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
    }

    .admin-sidebar {
        width: 250px;
        background-color: rgba(10, 10, 10, 0.95);
        border-right: 1px solid rgba(20, 255, 20, 0.1);
        padding: 1.5rem 0;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 100;
        overflow-y: auto;
    }

    .admin-main {
        flex: 1;
        margin-left: 250px;
        padding-bottom: 2rem;
    }

    .admin-header {
        position: sticky;
        top: 0;
        padding: 1rem 2rem;
        backdrop-filter: blur(10px);
        background-color: rgba(26, 26, 26, 0.9);
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        z-index: 90;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-content {
        padding: 2rem;
    }

    .admin-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .admin-modal-content {
        background-color: #1a1a1a;
        border: 1px solid rgba(20, 255, 20, 0.2);
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }

    .admin-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-modal-header h3 {
        margin: 0;
        color: var(--verde-neon);
        font-size: 1.4rem;
        text-shadow: 0 0 10px rgba(20, 255, 20, 0.4);
    }

    .modal-close {
        background: none;
        border: none;
        color: #aaa;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .modal-close:hover {
        color: var(--verde-neon);
    }

    .admin-modal-body {
        padding: 1.5rem;
    }

    .comment-detail {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detail-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-group label {
        font-weight: 600;
        color: #aaa;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-group span {
        color: #fff;
        font-size: 1.1rem;
    }

    .comment-content {
        background-color: rgba(20, 255, 20, 0.05);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 8px;
        padding: 1rem;
        color: #fff;
        line-height: 1.5;
        max-height: 200px;
        overflow-y: auto;
    }

    .admin-modal-footer {
        padding: 1.5rem;
        border-top: 1px solid rgba(20, 255, 20, 0.1);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .btn-danger {
        background-color: #ff4444;
        color: #fff;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #ff6666;
        box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
    }

    .btn-neon {
        background-color: transparent;
        border: 1px solid var(--verde-neon);
        color: var(--verde-neon);
        padding: 0.6rem 1.2rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-neon:hover {
        background-color: rgba(20, 255, 20, 0.1);
        box-shadow: 0 0 10px rgba(20, 255, 20, 0.3);
    }

    /* Estilos para tablas */
    .admin-table-card {
        background-color: rgba(26, 26, 26, 0.6);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-actions {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .admin-table-container {
        padding: 0 1.5rem;
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 800px;
    }

    .admin-table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .admin-table th {
        text-align: left;
        padding: 1rem;
        color: #aaa;
        font-weight: 500;
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        background-color: rgba(20, 255, 20, 0.05);
    }

    .admin-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(20, 255, 20, 0.05);
        vertical-align: middle;
        color: #ddd;
    }

    .admin-table tbody tr:hover {
        background-color: rgba(20, 255, 20, 0.02);
    }

    .pagination-container {
        padding: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        background-color: rgba(20, 255, 20, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--verde-neon);
    }

    .user-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        background-color: rgba(20, 255, 20, 0.1);
        color: var(--verde-neon);
        margin-left: 0.5rem;
    }

    .user-badge.admin {
        background-color: rgba(255, 68, 68, 0.1);
        color: #ff4444;
    }

    .user-badge.premium {
        background-color: rgba(255, 215, 0, 0.1);
        color: #ffd700;
    }

    .user-badge.critico {
        background-color: rgba(0, 255, 221, 0.1);
        color: #00ffdd;
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 3px;
        font-weight: 500;
        display: inline-block;
    }

    .status-badge.destacado {
        background-color: rgba(20, 255, 20, 0.1);
        color: var(--verde-neon);
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .status-badge.normal {
        background-color: rgba(255, 255, 255, 0.05);
        color: #aaa;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .actions {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #aaa;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background-color: rgba(20, 255, 20, 0.1);
        color: var(--verde-neon);
    }

    .action-btn.delete:hover {
        background-color: rgba(255, 68, 68, 0.1);
        color: #ff4444;
    }

    .action-btn.highlight.is-highlighted {
        color: var(--verde-neon);
    }

    .comment-preview {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .comment-full {
        display: none;
        background-color: rgba(20, 255, 20, 0.05);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 8px;
        margin-top: 0.5rem;
        padding: 1rem;
        white-space: pre-wrap;
        position: relative;
    }

    .comment-close {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: none;
        border: none;
        color: #aaa;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .comment-close:hover {
        color: var(--verde-neon);
    }

    /* Filtros */
    .filters-container {
        background-color: rgba(26, 26, 26, 0.6);
        border: 1px solid rgba(20, 255, 20, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .filters-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(20, 255, 20, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filters-header h3 {
        margin: 0;
        font-size: 1.2rem;
        color: var(--verde-neon);
    }

    .btn-reset-filters {
        background: none;
        border: none;
        color: #aaa;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset-filters:hover {
        color: var(--verde-neon);
    }

    .filters-body {
        padding: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 0.8rem;
        color: #aaa;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .filter-btn {
        background-color: rgba(20, 255, 20, 0.05);
        border: 1px solid rgba(20, 255, 20, 0.1);
        color: #aaa;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: rgba(20, 255, 20, 0.1);
        color: #fff;
    }

    .filter-btn.active {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
        border-color: rgba(20, 255, 20, 0.3);
    }

    .date-range {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .neon-input {
        background-color: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(20, 255, 20, 0.2);
        color: #fff;
        padding: 0.5rem;
        border-radius: 5px;
        outline: none;
        transition: all 0.2s ease;
    }

    .neon-input:focus {
        border-color: var(--verde-neon);
        box-shadow: 0 0 5px rgba(20, 255, 20, 0.3);
    }

    @media (max-width: 768px) {
        .admin-sidebar {
            width: 70px;
        }

        .admin-sidebar .admin-logo h1 {
            font-size: 1rem;
        }

        .admin-sidebar .admin-nav-item span {
            display: none;
        }

        .admin-main {
            margin-left: 70px;
        }

        .admin-content {
            padding: 1rem;
        }

        .filters-body {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-container">
    <!-- Sidebar de administración -->
    <div class="admin-sidebar">
        <div class="admin-logo text-center mb-4 p-3">
            <h1 class="text-light m-0">CritFlix</h1>
            <div class="small text-muted">Panel de Admin</div>
        </div>

        <ul class="admin-nav list-unstyled ps-3 pe-3">
            <li class="mb-2">
                <a href="{{ url('/admin') }}" class="admin-nav-item">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ url('/admin/users') }}" class="admin-nav-item">
                    <i class="fas fa-users me-2"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ url('/admin/movies') }}" class="admin-nav-item">
                    <i class="fas fa-film me-2"></i>
                    <span>Películas</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ url('/admin/reviews') }}" class="admin-nav-item">
                    <i class="fas fa-star me-2"></i>
                    <span>Reseñas</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ url('/admin/comments') }}" class="admin-nav-item active">
                    <i class="fas fa-comments me-2"></i>
                    <span>Comentarios</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ url('/profile') }}" class="admin-nav-item">
                    <i class="fas fa-user-cog me-2"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>
            <li>
                <a href="#" class="admin-nav-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Cerrar Sesión</span>
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <!-- Header -->
        <div class="admin-header">
            <h2 class="m-0 text-light">
                <i class="fas fa-comments me-2"></i> Gestión de Comentarios
            </h2>
            <div class="search-box">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control neon-input" placeholder="Buscar comentarios...">
                    <button class="btn btn-neon" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="admin-content">
            <!-- Estadísticas de comentarios -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <h5 class="stat-card-title">Total Comentarios</h5>
                            <div class="stat-card-value">{{ $stats['total'] }}</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <h5 class="stat-card-title">Comentarios Destacados</h5>
                            <div class="stat-card-value">{{ $stats['destacados'] }}</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <h5 class="stat-card-title">Contienen Spoilers</h5>
                            <div class="stat-card-value">{{ $stats['spoilers'] }}</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <h5 class="stat-card-title">Nuevos Hoy</h5>
                            <div class="stat-card-value">{{ $stats['new_today'] }}</div>
                            <div class="stat-card-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros de comentarios -->
            <div class="filters-container">
                <div class="filters-header">
                    <h3>Filtros</h3>
                    <button class="btn-reset-filters">
                        <i class="fas fa-undo-alt"></i> Resetear filtros
                    </button>
                </div>
                <div class="filters-body">
                    <div class="filter-group">
                        <label>Estado</label>
                        <div class="filter-options">
                            <button class="filter-btn active" data-filter="all">Todos</button>
                            <button class="filter-btn" data-filter="highlighted">Destacados</button>
                            <button class="filter-btn" data-filter="normal">Normales</button>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Contenido</label>
                        <div class="filter-options">
                            <button class="filter-btn active" data-filter="all">Todos</button>
                            <button class="filter-btn" data-filter="spoiler">Spoilers</button>
                            <button class="filter-btn" data-filter="no-spoiler">Sin Spoilers</button>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Fecha</label>
                        <div class="date-range">
                            <input type="date" class="neon-input" id="dateFrom">
                            <span>a</span>
                            <input type="date" class="neon-input" id="dateTo">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de comentarios -->
            <div class="admin-table-card">
                <div class="table-actions">
                    <div class="fw-bold">{{ $comments->total() }} comentarios encontrados</div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-neon" id="bulk-highlight">
                            <i class="fas fa-medal me-1"></i> Destacar seleccionados
                        </button>
                        <button class="btn btn-danger" id="bulk-delete">
                            <i class="fas fa-trash-alt me-1"></i> Eliminar seleccionados
                        </button>
                    </div>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th width="20%">Usuario</th>
                                <th width="15%">Película</th>
                                <th width="32%">Comentario</th>
                                <th width="10%">Estado</th>
                                <th width="12%">Fecha</th>
                                <th width="9%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                            <tr data-id="{{ $comment->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input comment-check" value="{{ $comment->id }}">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            {{ $comment->user_name }}
                                            @if($comment->user_rol == 'admin')
                                            <span class="user-badge admin">Admin</span>
                                            @elseif($comment->user_rol == 'premium')
                                            <span class="user-badge premium">Premium</span>
                                            @elseif($comment->user_rol == 'critico')
                                            <span class="user-badge critico">Crítico</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $comment->movie_title }}</td>
                                <td>
                                    <div class="comment-preview">
                                        {{ $comment->comentario }}
                                        @if($comment->spoiler)
                                        <span class="status-badge" style="background-color: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid rgba(255, 68, 68, 0.3);">Spoiler</span>
                                        @endif
                                    </div>
                                    <button class="show-more-btn" data-id="{{ $comment->id }}">Ver más</button>
                                    <div class="comment-full" id="comment-{{ $comment->id }}">
                                        <button class="comment-close" data-id="{{ $comment->id }}"><i class="fas fa-times"></i></button>
                                        {{ $comment->comentario }}
                                    </div>
                                </td>
                                <td>
                                    @if($comment->destacado)
                                    <span class="status-badge destacado">Destacado</span>
                                    @else
                                    <span class="status-badge normal">Normal</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn view-btn" data-id="{{ $comment->id }}" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn highlight highlight-btn {{ $comment->destacado ? 'is-highlighted' : '' }}"
                                                data-id="{{ $comment->id }}"
                                                data-highlighted="{{ $comment->destacado ? 1 : 0 }}"
                                                title="{{ $comment->destacado ? 'Quitar destacado' : 'Destacar' }}">
                                            <i class="fas fa-medal"></i>
                                        </button>
                                        <button class="action-btn delete delete-btn" data-id="{{ $comment->id }}" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($comments->total() > 0)
                <div class="pagination-container">
                    {{ $comments->links() }}
                </div>
                @else
                <div class="p-4 text-center text-muted">
                    No se encontraron comentarios con los filtros actuales.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del comentario -->
<div class="admin-modal" id="commentModal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3>Detalles del Comentario</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="admin-modal-body">
            <div class="comment-detail">
                <div class="detail-group">
                    <label>Usuario</label>
                    <span id="commentUser"></span>
                </div>
                <div class="detail-group">
                    <label>Película</label>
                    <span id="commentMovie"></span>
                </div>
                <div class="detail-group">
                    <label>Fecha</label>
                    <span id="commentDate"></span>
                </div>
                <div class="detail-group">
                    <label>Estado</label>
                    <span id="commentStatus"></span>
                </div>
                <div class="detail-group">
                    <label>Contenido</label>
                    <div class="comment-content" id="commentContent"></div>
                </div>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="btn btn-neon" id="modalHighlightBtn">
                <i class="fas fa-medal me-1"></i> <span id="highlightBtnText">Destacar</span>
            </button>
            <button class="btn btn-danger" id="modalDeleteBtn">
                <i class="fas fa-trash-alt me-1"></i> Eliminar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a elementos
        const commentModal = document.getElementById('commentModal');
        const modalClose = commentModal.querySelector('.modal-close');
        const viewButtons = document.querySelectorAll('.view-btn');
        const highlightButtons = document.querySelectorAll('.highlight-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const showMoreButtons = document.querySelectorAll('.show-more-btn');
        const commentCloseButtons = document.querySelectorAll('.comment-close');
        const selectAll = document.getElementById('selectAll');
        const bulkDelete = document.getElementById('bulk-delete');
        const bulkHighlight = document.getElementById('bulk-highlight');
        const modalHighlightBtn = document.getElementById('modalHighlightBtn');
        const modalDeleteBtn = document.getElementById('modalDeleteBtn');

        // Función para obtener todos los comentarios seleccionados
        function getSelectedComments() {
            return Array.from(document.querySelectorAll('.comment-check:checked')).map(checkbox => checkbox.value);
        }

        // Modal para ver detalles
        let currentCommentId = null;
        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                const commentId = button.dataset.id;
                currentCommentId = commentId;
                const row = button.closest('tr');
                const user = row.querySelector('.user-info').textContent.trim();
                const movie = row.cells[2].textContent.trim();
                const comment = row.querySelector('.comment-preview').textContent.trim();
                const date = row.cells[5].textContent.trim();
                const isHighlighted = row.querySelector('.highlight-btn').classList.contains('is-highlighted');

                document.getElementById('commentUser').textContent = user;
                document.getElementById('commentMovie').textContent = movie;
                document.getElementById('commentDate').textContent = date;
                document.getElementById('commentContent').textContent = comment;
                document.getElementById('commentStatus').textContent = isHighlighted ? 'Destacado' : 'Normal';
                document.getElementById('commentStatus').className = isHighlighted ? 'status-badge destacado' : 'status-badge normal';

                // Actualizar botón de destacar
                document.getElementById('highlightBtnText').textContent = isHighlighted ? 'Quitar destacado' : 'Destacar';
                modalHighlightBtn.dataset.highlighted = isHighlighted ? '1' : '0';

                // Mostrar modal
                commentModal.style.display = 'flex';
            });
        });

        // Cerrar modal
        modalClose.addEventListener('click', () => {
            commentModal.style.display = 'none';
            currentCommentId = null;
        });

        // Seleccionar todos los comentarios
        selectAll.addEventListener('change', () => {
            document.querySelectorAll('.comment-check').forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });

        // Botones para mostrar/ocultar comentario completo
        showMoreButtons.forEach(button => {
            button.addEventListener('click', () => {
                const commentId = button.dataset.id;
                const commentFull = document.getElementById(`comment-${commentId}`);
                commentFull.style.display = 'block';
            });
        });

        commentCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const commentId = button.dataset.id;
                const commentFull = document.getElementById(`comment-${commentId}`);
                commentFull.style.display = 'none';
            });
        });

        // Eliminar comentarios en masa
        bulkDelete.addEventListener('click', async () => {
            const selectedIds = getSelectedComments();
            if (selectedIds.length === 0) {
                alert('Por favor, selecciona al menos un comentario para eliminar.');
                return;
            }

            if (confirm(`¿Estás seguro de que quieres eliminar ${selectedIds.length} comentario(s)?`)) {
                await bulkDeleteComments(selectedIds);
            }
        });

        // Destacar comentarios en masa
        bulkHighlight.addEventListener('click', async () => {
            const selectedIds = getSelectedComments();
            if (selectedIds.length === 0) {
                alert('Por favor, selecciona al menos un comentario para destacar.');
                return;
            }

            await bulkHighlightComments(selectedIds, true);
        });

        // Acción de destacar desde el modal
        modalHighlightBtn.addEventListener('click', async () => {
            if (!currentCommentId) return;

            const isHighlighted = modalHighlightBtn.dataset.highlighted === '1';
            await highlightComment(currentCommentId, !isHighlighted);
            commentModal.style.display = 'none';
        });

        // Acción de eliminar desde el modal
        modalDeleteBtn.addEventListener('click', async () => {
            if (!currentCommentId) return;

            if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                await deleteComment(currentCommentId);
                commentModal.style.display = 'none';
            }
        });

        // Acción de destacar para botones individuales
        highlightButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const commentId = button.dataset.id;
                const isHighlighted = button.dataset.highlighted === '1';
                await highlightComment(commentId, !isHighlighted);
            });
        });

        // Acción de eliminar para botones individuales
        deleteButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const commentId = button.dataset.id;

                if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                    await deleteComment(commentId);
                }
            });
        });

        // Función para eliminar comentarios en masa
        async function bulkDeleteComments(ids) {
            try {
                for (const id of ids) {
                    await deleteComment(id);
                }
            } catch (error) {
                console.error('Error al eliminar comentarios:', error);
                alert('Ha ocurrido un error al eliminar los comentarios.');
            }
        }

        // Función para destacar comentarios en masa
        async function bulkHighlightComments(ids, highlight) {
            try {
                for (const id of ids) {
                    await highlightComment(id, highlight);
                }
            } catch (error) {
                console.error('Error al destacar comentarios:', error);
                alert('Ha ocurrido un error al destacar los comentarios.');
            }
        }

        // Función para destacar/quitar destacado a un comentario
        async function highlightComment(commentId, highlight) {
            try {
                const response = await fetch(`/api/admin/comments/${commentId}/${highlight ? 'highlight' : 'unhighlight'}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Actualizar la UI
                    const row = document.querySelector(`tr[data-id="${commentId}"]`);
                    const highlightBtn = row.querySelector('.highlight-btn');
                    const statusBadge = row.querySelector('.status-badge');

                    highlightBtn.dataset.highlighted = highlight ? '1' : '0';
                    highlightBtn.title = highlight ? 'Quitar destacado' : 'Destacar';

                    if (highlight) {
                        highlightBtn.classList.add('is-highlighted');
                        statusBadge.textContent = 'Destacado';
                        statusBadge.className = 'status-badge destacado';
                    } else {
                        highlightBtn.classList.remove('is-highlighted');
                        statusBadge.textContent = 'Normal';
                        statusBadge.className = 'status-badge normal';
                    }

                    // Actualizar estadísticas (esto requeriría recargar la página o una petición adicional)
                    // Por ahora simplemente mostramos un mensaje
                    alert(highlight ? 'Comentario destacado con éxito' : 'Se ha quitado el destacado del comentario');
                } else {
                    alert(data.message || 'Error al actualizar el comentario');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ha ocurrido un error al procesar la solicitud');
            }
        }

        // Función para eliminar un comentario
        async function deleteComment(commentId) {
            try {
                const response = await fetch(`/api/admin/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Eliminar la fila de la tabla
                    const row = document.querySelector(`tr[data-id="${commentId}"]`);
                    row.remove();

                    // Actualizar el contador
                    const totalElement = document.querySelector('.table-actions .fw-bold');
                    const currentTotal = parseInt(totalElement.textContent.match(/\d+/)[0]) - 1;
                    totalElement.textContent = `${currentTotal} comentarios encontrados`;

                    alert('Comentario eliminado con éxito');
                } else {
                    alert(data.message || 'Error al eliminar el comentario');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ha ocurrido un error al procesar la solicitud');
            }
        }
    });
</script>
@endpush
