@extends('layouts.app')

@section('content')
<div class="container">
    <div class="list-detail">
        <div class="list-detail-header">
            <h1>{{ $lista->nombre_lista }}</h1>
            <div class="list-meta">
                <span><i class="fas fa-user"></i> {{ $lista->usuario->name }}</span>
                <span><i class="fas fa-calendar"></i> {{ $lista->fecha_creacion }}</span>
                <span><i class="fas fa-film"></i> {{ count($lista->contenidosListas) }} películas</span>
            </div>

            @if(Auth::id() == $lista->user_id)
            <div class="list-actions">
                <a href="{{ route('listas.edit', $lista->id) }}" class="btn-neon">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form method="POST" action="{{ route('listas.destroy', $lista->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-neon btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta lista?')">
                        <i class="fas fa-trash"></i> Eliminar
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
                            <img src="{{ asset('storage/posters/' . $contenido->pelicula->poster) }}" alt="{{ $contenido->pelicula->titulo }}">
                            @if(Auth::id() == $lista->user_id)
                            <div class="movie-actions">
                                <button class="remove-movie" data-id="{{ $contenido->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        <div class="movie-info">
                            <h3>{{ $contenido->pelicula->titulo }}</h3>
                            <div class="movie-meta">
                                <span>{{ $contenido->pelicula->anio }}</span>
                                <span>{{ $contenido->pelicula->duracion }} min</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>Esta lista no contiene películas todavía.</p>
                    @if(Auth::id() == $lista->user_id)
                    <a href="{{ route('peliculas.index') }}" class="btn-neon">
                        <i class="fas fa-plus"></i> Añadir Películas
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Eliminar película de la lista
        $('.remove-movie').click(function() {
            if (confirm('¿Estás seguro de querer eliminar esta película de la lista?')) {
                const contenidoId = $(this).data('id');
                $.ajax({
                    url: '{{ route("contenido-listas.destroy", ":id") }}'.replace(':id', contenidoId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
