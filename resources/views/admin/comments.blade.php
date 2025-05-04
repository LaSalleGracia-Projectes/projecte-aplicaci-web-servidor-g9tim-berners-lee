@extends('layouts.admin')

@section('title', 'Gestión de Comentarios - CritFlix Admin')

@section('header-title', 'Gestión de Comentarios')

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

    .comment-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .comment-filters .filter-group {
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

    .btn-reset-filters {
        background-color: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 1.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset-filters:hover {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: var(--verde-neon);
        color: var(--verde-neon);
    }

    .comment-item {
        background-color: rgba(18, 18, 18, 0.6);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .comment-item:hover {
        border-color: var(--verde-neon);
        background-color: rgba(0, 255, 102, 0.05);
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .comment-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }

    .comment-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comment-author {
        font-weight: 600;
        color: var(--text-light);
    }

    .comment-date {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .comment-content {
        color: var(--text-light);
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .comment-actions {
        display: flex;
        gap: 0.5rem;
    }

    .comment-actions button {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
        border-radius: var(--border-radius);
        transition: all 0.2s ease;
        color: var(--text-muted);
    }

    .comment-actions button:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .comment-actions button.highlight {
        color: var(--amarillo-neon);
    }

    .comment-actions button.delete {
        color: var(--rojo-neon);
    }

    .comment-actions button i {
        margin-right: 0.25rem;
    }

    .comment-item.highlighted {
        border: 1px solid var(--amarillo-neon);
        background-color: rgba(255, 215, 0, 0.05);
    }

    .highlighted-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: rgba(255, 215, 0, 0.15);
        color: var(--amarillo-neon);
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.75rem;
    }

    .comment-movie {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .movie-poster {
        width: 40px;
        height: 60px;
        border-radius: 4px;
        overflow: hidden;
    }

    .movie-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .movie-title {
        font-weight: 600;
        color: var(--text-light);
    }

    .movie-type {
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
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination .page-item {
        margin: 0;
    }

    .pagination .page-link,
    .pagination .page-item span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0.5rem 0.75rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 1rem;
        font-weight: 500;
    }

    .pagination .page-link:hover {
        background-color: rgba(0, 255, 102, 0.1);
        border-color: var(--verde-neon);
        color: var(--verde-neon);
        box-shadow: 0 0 10px rgba(0, 255, 102, 0.2);
    }

    .pagination .active .page-link,
    .pagination .page-item.active span,
    .pagination .page-item.active a {
        background-color: var(--verde-neon);
        border-color: var(--verde-neon);
        color: #000;
        font-weight: 600;
        box-shadow: 0 0 10px rgba(0, 255, 102, 0.4);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Asegurarnos que los iconos de flechas se vean bien */
    .pagination .page-link:first-child,
    .pagination .page-link:last-child {
        font-weight: bold;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: var(--verde-neon);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--verde-neon);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .export-btn {
        background-color: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .export-btn:hover {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: var(--verde-neon);
        color: var(--verde-neon);
    }

    .spoiler-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: rgba(220, 53, 69, 0.15);
        color: var(--rojo-neon);
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.75rem;
    }

    .interactions {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .interaction {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .interaction i {
        font-size: 0.9rem;
    }

    .interaction.likes {
        color: var(--verde-neon);
    }

    .interaction.dislikes {
        color: var(--rojo-neon);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: var(--text-light);
    }

    .empty-state p {
        max-width: 500px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="stat-label">Total de comentarios</div>
        </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['destacados'] ?? 0 }}</div>
        <div class="stat-label">Comentarios destacados</div>
        </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['spoilers'] ?? 0 }}</div>
        <div class="stat-label">Comentarios con spoilers</div>
        </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['hoy'] ?? 0 }}</div>
        <div class="stat-label">Comentarios hoy</div>
    </div>
    </div>

    <div class="card">
        <div class="card-header">
        <h3>Listado de comentarios</h3>
        <button class="export-btn" id="exportComments">
            <i class="fas fa-file-export"></i> Exportar
                </button>
        </div>
        <div class="card-body">
        <form action="{{ route('admin.comments') }}" method="GET" id="filterForm">
                <div class="comment-filters">
                    <div class="filter-group">
                    <label for="keyword">Buscar por contenido</label>
                    <input type="text" name="keyword" id="keyword" class="filter-control" placeholder="Buscar..." value="{{ request('keyword') }}">
                </div>
                <div class="filter-group">
                    <label for="user">Usuario</label>
                    <input type="text" name="user" id="user" class="filter-control" placeholder="Nombre de usuario" value="{{ request('user') }}">
                    </div>
                    <div class="filter-group">
                    <label for="content_type">Tipo de contenido</label>
                    <select name="content_type" id="content_type" class="filter-control">
                        <option value="">Todos</option>
                        <option value="pelicula" {{ request('content_type') == 'pelicula' ? 'selected' : '' }}>Películas</option>
                        <option value="serie" {{ request('content_type') == 'serie' ? 'selected' : '' }}>Series</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status">Estado</label>
                    <select name="status" id="status" class="filter-control">
                        <option value="">Todos</option>
                        <option value="destacado" {{ request('status') == 'destacado' ? 'selected' : '' }}>Destacados</option>
                        <option value="spoiler" {{ request('status') == 'spoiler' ? 'selected' : '' }}>Con spoilers</option>
                        </select>
                </div>
                <button type="button" class="btn-reset-filters" id="resetFilters">
                    <i class="fas fa-undo"></i> Reiniciar
                </button>
            </div>
        </form>

        @if(count($comments ?? []) > 0)
                    @foreach($comments as $comment)
            <div class="comment-item {{ $comment->destacado ? 'highlighted' : '' }}">
                        <div class="comment-header">
                            <div class="comment-meta">
                                <div class="comment-avatar">
                            @if($comment->usuario && $comment->usuario->profile_photo)
                                <img src="{{ asset('storage/' . $comment->usuario->profile_photo) }}" alt="{{ $comment->usuario->name }}">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                                </div>
                                <div>
                                    <div class="comment-author">
                                {{ $comment->usuario ? $comment->usuario->name : 'Usuario desconocido' }}
                                @if($comment->destacado)
                                    <span class="highlighted-badge"><i class="fas fa-star"></i> Destacado</span>
                                @endif
                                @if($comment->es_spoiler)
                                    <span class="spoiler-badge"><i class="fas fa-exclamation-triangle"></i> Spoiler</span>
                                        @endif
                                    </div>
                            <div class="comment-date">
                                <i class="far fa-clock"></i> {{ $comment->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                            </div>
                            <div class="comment-actions">
                        @if(!$comment->destacado)
                        <button class="highlight highlight-btn" data-id="{{ $comment->id }}">
                                    <i class="fas fa-star"></i> Destacar
                                </button>
                                @else
                        <button class="highlight unhighlight-btn" data-id="{{ $comment->id }}">
                            <i class="far fa-star"></i> Quitar destacado
                                </button>
                                @endif
                        <button class="delete delete-btn" data-id="{{ $comment->id }}">
                            <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <div class="comment-content">
                    {{ $comment->comentario }}
                </div>
                <div class="interactions">
                    <div class="interaction likes">
                        <i class="fas fa-thumbs-up"></i> {{ $comment->likes_count ?? 0 }}
                    </div>
                    <div class="interaction dislikes">
                        <i class="fas fa-thumbs-down"></i> {{ $comment->dislikes_count ?? 0 }}
                    </div>
                        </div>
                        <div class="comment-movie">
                            <div class="movie-poster">
                        @php
                            // Intentar buscar la película/serie de forma segura
                            try {
                                // La tabla comentarios usa tmdb_id pero la tabla peliculas_series usa api_id
                                $contenido = DB::table('peliculas_series')
                                    ->where('api_id', $comment->tmdb_id)
                                    ->where('tipo', $comment->tipo)
                                    ->first();

                                // Si no se encuentra, buscar directamente en la API de TMDB (simulado para no hacer la llamada real)
                                if (!$contenido) {
                                    // Usar un placeholder mientras tanto
                                    $titulo = 'Contenido #' . $comment->tmdb_id;
                                    $poster = null;
                                } else {
                                    $titulo = $contenido->titulo;
                                    $poster = isset($contenido->poster) ? $contenido->poster : null;
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error al buscar contenido para comentario: ' . $e->getMessage());
                                $titulo = 'Contenido no disponible';
                                $poster = null;
                            }
                        @endphp

                        @if($poster)
                            <img src="https://image.tmdb.org/t/p/w92{{ $poster }}" alt="{{ $titulo }}">
                        @else
                            <i class="fas fa-film"></i>
                        @endif
                            </div>
                            <div>
                        <div class="movie-title">{{ $titulo }}</div>
                        <div class="movie-type">{{ $comment->tipo == 'pelicula' ? 'Película' : 'Serie' }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                <div class="pagination-container">
                @if ($comments->hasPages())
                    <nav>
                        <ul class="pagination">
                            {{-- Botón Anterior --}}
                            @if ($comments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $comments->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            {{-- Enlaces de números de página --}}
                            @php
                                $window = 3; // Cuántas páginas mostrar a cada lado de la página actual
                                $lastPage = $comments->lastPage();
                                $currentPage = $comments->currentPage();
                                $startPage = max($currentPage - $window, 1);
                                $endPage = min($currentPage + $window, $lastPage);
                            @endphp

                            {{-- Primera página si estamos lejos --}}
                            @if ($startPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $comments->appends(request()->except('page'))->url(1) }}">1</a>
                                </li>
                                @if ($startPage > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Páginas numeradas alrededor de la actual --}}
                            @for ($i = $startPage; $i <= $endPage; $i++)
                                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $comments->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Última página si estamos lejos --}}
                            @if ($endPage < $lastPage)
                                @if ($endPage < $lastPage - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $comments->appends(request()->except('page'))->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Botón Siguiente --}}
                            @if ($comments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $comments->appends(request()->except('page'))->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
                </div>
            @else
                <div class="empty-state">
                <i class="far fa-comments"></i>
                <h4>No hay comentarios disponibles</h4>
                <p>No se encontraron comentarios que coincidan con los criterios de búsqueda o no hay comentarios en el sistema.</p>
                </div>
            @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Control de formulario de filtros
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('input, select');

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Botón de reinicio de filtros
    const resetButton = document.getElementById('resetFilters');
    resetButton.addEventListener('click', function() {
        filterInputs.forEach(input => {
            input.value = '';
        });
        filterForm.submit();
    });

    // Botones para destacar comentarios
    document.querySelectorAll('.highlight-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-id');
            highlightComment(commentId);
        });
    });

    // Botones para quitar destacado
    document.querySelectorAll('.unhighlight-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-id');
            unhighlightComment(commentId);
            });
        });

    // Botones para eliminar comentarios
    document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-id');
            if (confirm('¿Estás seguro de que deseas eliminar este comentario? Esta acción no se puede deshacer.')) {
                deleteComment(commentId);
            }
                });
            });

    // Función para destacar un comentario
    function highlightComment(id) {
        fetch(`/api/admin/comments/${id}/highlight`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al destacar el comentario');
            }
            return response.json();
        })
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al destacar el comentario.');
        });
    }

    // Función para quitar destacado
    function unhighlightComment(id) {
        fetch(`/api/admin/comments/${id}/unhighlight`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al quitar destacado del comentario');
            }
            return response.json();
        })
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al quitar el destacado del comentario.');
        });
    }

    // Función para eliminar un comentario
    function deleteComment(id) {
        fetch(`/api/admin/comments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al eliminar el comentario');
            }
            return response.json();
        })
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al eliminar el comentario.');
        });
    }

    // Exportar comentarios
    document.getElementById('exportComments').addEventListener('click', function() {
        // Se podría implementar exportación a CSV o Excel
        alert('Funcionalidad de exportación en desarrollo');
    });
});
</script>
@endpush
