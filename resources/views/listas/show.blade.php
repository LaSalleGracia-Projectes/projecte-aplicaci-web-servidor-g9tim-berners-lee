@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="list-detail-container">
    <div class="back-button">
        <a href="{{ route('profile.show', $lista->user_id) }}" class="btn-neon">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>
    </div>

    <div class="list-detail-header">
        <div class="header-content">
            <h1><i class="fas fa-list"></i> {{ $lista->nombre_lista }}</h1>
            <div class="list-meta">
                <span><i class="fas fa-user"></i> Creada por {{ $lista->usuario->name }}</span>
                <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($lista->fecha_creacion)->format('d/m/Y') }}</span>
                <span><i class="fas fa-film"></i> {{ count($lista->contenidosListas) }} películas</span>
            </div>
            @if($lista->descripcion)
            <div class="list-description">
                <p>{{ $lista->descripcion }}</p>
            </div>
            @endif
        </div>

        @if(Auth::id() == $lista->user_id)
        <div class="list-actions">
            <a href="{{ route('listas.edit', $lista->id) }}" class="btn-neon">
                <i class="fas fa-edit"></i> Editar Lista
            </a>
            <form method="POST" action="{{ route('listas.destroy', $lista->id) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-neon btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta lista?')">
                    <i class="fas fa-trash"></i> Eliminar Lista
                </button>
            </form>
        </div>
        @endif
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
                        @if(Auth::id() == $lista->user_id)
                        <div class="movie-actions">
                            <button type="button" class="remove-movie" data-id="{{ $contenido->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>Esta lista no contiene películas todavía.</p>
                @if(Auth::id() == $lista->user_id)
                <a href="{{ route('listas.edit', $lista->id) }}" class="btn-neon">
                    <i class="fas fa-plus"></i> Añadir Películas
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Eliminar película de la lista
    document.querySelectorAll('.remove-movie').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (confirm('¿Estás seguro de querer eliminar esta película de la lista?')) {
                const contenidoId = this.dataset.id;
                fetch(`/api/contenido-listas/${contenidoId}`, {
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
