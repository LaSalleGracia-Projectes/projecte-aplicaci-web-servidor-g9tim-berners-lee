@extends('layouts.admin')

@section('title', 'Gestión de Valoraciones - CritFlix Admin')

@section('header-title', 'Gestión de Valoraciones')

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

    .review-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .review-filters .filter-group {
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

    .review-item {
        background-color: rgba(18, 18, 18, 0.6);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .review-item:hover {
        border-color: var(--verde-neon);
        background-color: rgba(0, 255, 102, 0.05);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .review-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }

    .review-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .review-author {
        font-weight: 600;
        color: var(--text-light);
    }

    .review-date {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .review-content {
        color: var(--text-light);
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .review-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .rating-stars {
        color: var(--amarillo-neon);
        letter-spacing: 2px;
    }

    .rating-value {
        font-weight: 600;
        color: var(--text-light);
    }

    .review-actions {
        display: flex;
        gap: 0.5rem;
    }

    .review-actions button {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
        border-radius: var(--border-radius);
        transition: all 0.2s ease;
        color: var(--text-muted);
    }

    .review-actions button:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .review-actions button.approve {
        color: var(--verde-neon);
    }

    .review-actions button.delete {
        color: var(--rojo-neon);
    }

    .review-actions button i {
        margin-right: 0.25rem;
    }

    .review-movie {
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

    .like-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background-color: rgba(0, 255, 102, 0.15);
        color: var(--verde-neon);
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .dislike-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background-color: rgba(220, 53, 69, 0.15);
        color: var(--rojo-neon);
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
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
        <div class="stat-label">Total de valoraciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['likes'] ?? 0 }}</div>
        <div class="stat-label">Me gusta</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['dislikes'] ?? 0 }}</div>
        <div class="stat-label">No me gusta</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['hoy'] ?? 0 }}</div>
        <div class="stat-label">Valoraciones hoy</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Listado de valoraciones</h3>
        <button class="export-btn" id="exportReviews">
            <i class="fas fa-file-export"></i> Exportar
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reviews') }}" method="GET" id="filterForm">
            <div class="review-filters">
                <div class="filter-group">
                    <label for="user">Usuario</label>
                    <input type="text" name="user" id="user" class="filter-control" placeholder="Nombre de usuario" value="{{ request('user') }}">
                </div>
                <div class="filter-group">
                    <label for="movie">Película</label>
                    <input type="text" name="movie" id="movie" class="filter-control" placeholder="Título de película" value="{{ request('movie') }}">
                </div>
                <div class="filter-group">
                    <label for="rating">Valoración</label>
                    <select name="rating" id="rating" class="filter-control">
                        <option value="">Todas</option>
                        <option value="like" {{ request('rating') == 'like' ? 'selected' : '' }}>Me gusta</option>
                        <option value="dislike" {{ request('rating') == 'dislike' ? 'selected' : '' }}>No me gusta</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date">Fecha</label>
                    <select name="date" id="date" class="filter-control">
                        <option value="">Todas</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hoy</option>
                        <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>Última semana</option>
                        <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Último mes</option>
                    </select>
                </div>
                <button type="button" class="btn-reset-filters" id="resetFilters">
                    <i class="fas fa-undo"></i> Reiniciar
                </button>
            </div>
        </form>

        @if(count($reviews ?? []) > 0)
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Película/Serie</th>
                            <th>Valoración</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <div class="review-user">
                                    @if ($review->user_profile_photo)
                                        <img src="{{ asset('storage/' . $review->user_profile_photo) }}" alt="{{ $review->user_name }}" class="user-avatar">
                                    @else
                                        <div class="avatar-placeholder">{{ strtoupper(substr($review->user_name, 0, 1)) }}</div>
                                    @endif
                                    <span>{{ $review->user_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="review-movie">
                                    No disponible
                                </div>
                            </td>
                            <td class="review-rating">
                                @if($review->valoracion == 'like')
                                    <i class="fas fa-thumbs-up text-success"></i> Me gusta
                                @else
                                    <i class="fas fa-thumbs-down text-danger"></i> No me gusta
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="actions">
                                <button class="btn-action delete-review" data-id="{{ $review->id }}" title="Eliminar valoración">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay valoraciones disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                @if ($reviews->hasPages())
                    <nav>
                        <ul class="pagination">
                            {{-- Botón Anterior --}}
                            @if ($reviews->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $reviews->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            {{-- Enlaces de números de página --}}
                            @php
                                $window = 3; // Cuántas páginas mostrar a cada lado de la página actual
                                $lastPage = $reviews->lastPage();
                                $currentPage = $reviews->currentPage();
                                $startPage = max($currentPage - $window, 1);
                                $endPage = min($currentPage + $window, $lastPage);
                            @endphp

                            {{-- Primera página si estamos lejos --}}
                            @if ($startPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $reviews->appends(request()->except('page'))->url(1) }}">1</a>
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
                                    <a class="page-link" href="{{ $reviews->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
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
                                    <a class="page-link" href="{{ $reviews->appends(request()->except('page'))->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Botón Siguiente --}}
                            @if ($reviews->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $reviews->appends(request()->except('page'))->nextPageUrl() }}" rel="next">&raquo;</a>
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
                <i class="far fa-star"></i>
                <h4>No hay valoraciones disponibles</h4>
                <p>No se encontraron valoraciones que coincidan con los criterios de búsqueda o no hay valoraciones en el sistema.</p>
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

    // Botones para eliminar valoraciones
    document.querySelectorAll('.delete-review').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-id');
            if (confirm('¿Estás seguro de que deseas eliminar esta valoración? Esta acción no se puede deshacer.')) {
                deleteReview(reviewId);
            }
        });
    });

    // Función para eliminar una valoración
    function deleteReview(id) {
        fetch(`/api/admin/reviews/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al eliminar la valoración');
            }
            return response.json();
        })
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al eliminar la valoración.');
        });
    }

    // Exportar valoraciones
    document.getElementById('exportReviews').addEventListener('click', function() {
        // Se podría implementar exportación a CSV o Excel
        alert('Funcionalidad de exportación en desarrollo');
    });
});
</script>
@endpush
