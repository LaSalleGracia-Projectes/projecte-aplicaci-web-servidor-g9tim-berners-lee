@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <!-- Cabecera del perfil -->
    <div class="profile-header">
        <div class="profile-avatar">
            @if($user->foto_perfil)
            <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="Foto de perfil">
            @else
            <div class="profile-avatar-placeholder">
                <i class="fas fa-user"></i>
            </div>
            @endif
        </div>
        <div class="profile-info">
            <h1>{{ $user->name }}</h1>
            <div class="profile-meta">
                <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                <span><i class="fas fa-user-tag"></i> {{ ucfirst($user->rol) }}</span>
                <span><i class="fas fa-calendar"></i> Miembro desde {{ $user->created_at->format('d/m/Y') }}</span>
            </div>

            <div class="action-buttons">
                <a href="{{ route('profile.edit', $user->id) }}" class= "btn-neon">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
                <a href="{{ route('profile.change-password') }}" class="btn-neon">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </a>
                <button id="logout-btn" class="btn-neon btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </div>
        </div>
    </div>

    <!-- Pestañas de navegación -->
    <div class="profile-tabs">
        <button class="tab-button active" data-tab="bio">Biografía</button>
        <button class="tab-button" data-tab="reviews">Mis Críticas</button>
        <button class="tab-button" data-tab="favorites">Películas Favoritas</button>
        <button class="tab-button" data-tab="watchlist">Lista de Seguimiento</button>
        <button class="tab-button" data-tab="lists">Mis Listas</button>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="tab-content">
        <!-- Biografía -->
        <div class="tab-panel active" id="bio-panel">
            <div class="bio-container">
                <h2>Sobre mí</h2>
                @if($user->biografia)
                <p>{{ $user->biografia }}</p>
                @else
                <p class="empty-state">No has añadido una biografía todavía.</p>
                @endif
            </div>
        </div>

        <!-- Críticas -->
        <div class="tab-panel" id="reviews-panel">
            <h2>Mis Críticas</h2>
            <div class="user-reviews">
                <!-- Aquí irían las críticas del usuario -->
                <p class="empty-state">No has publicado críticas todavía.</p>
            </div>
        </div>

        <!-- Favoritos -->
        <div class="tab-panel" id="favorites-panel">
            <h2>Mis Películas Favoritas</h2>
            <div class="movie-grid">
                <!-- Aquí irían las películas favoritas -->
                <p class="empty-state">No has añadido películas a favoritos todavía.</p>
            </div>
        </div>

        <!-- Lista de seguimiento -->
        <div class="tab-panel" id="watchlist-panel">
            <h2>Mi Lista de Seguimiento</h2>
            <div class="movie-grid">
                <!-- Aquí irían las películas en la lista de seguimiento -->
                <p class="empty-state">No has añadido películas a tu lista de seguimiento todavía.</p>
            </div>
        </div>

        <!-- Listas -->
        <div class="tab-panel" id="lists-panel">
            <h2>Mis Listas</h2>
            <div class="user-lists">
                <div class="lists-header">
                    <a href="{{ route('listas.create', ['user_id' => $user->id]) }}" class="btn-neon">
                        <i class="fas fa-plus"></i> Crear Nueva Lista
                    </a>
                </div>

                @if(count($user->listas) > 0)
                    <div class="lists-grid">
                        @foreach($user->listas as $lista)
                        <div class="list-card">
                            <div class="list-card-header">
                                <h3>{{ $lista->nombre_lista }}</h3>
                                <span class="list-date">{{ $lista->fecha_creacion }}</span>
                            </div>
                            <div class="list-card-body">
                                @if(count($lista->contenidosListas) > 0)
                                    <div class="list-thumbnails">
                                        @foreach($lista->contenidosListas->take(4) as $contenido)
                                            <div class="thumbnail">
                                                <img src="https://image.tmdb.org/t/p/w500{{ $contenido->pelicula['poster_path'] }}"
                                                     alt="{{ $contenido->pelicula['title'] }}"
                                                     onerror="this.src='{{ asset('images/default-poster.jpg') }}'">
                                            </div>
                                        @endforeach
                                        @if(count($lista->contenidosListas) > 4)
                                            <div class="more-items">+{{ count($lista->contenidosListas) - 4 }}</div>
                                        @endif
                                    </div>
                                @else
                                    <p class="empty-list">Esta lista está vacía</p>
                                @endif
                            </div>
                            <div class="list-card-footer">
                                <a href="{{ route('listas.show', $lista->id) }}" class="btn-link btn-view" title="Ver lista">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('listas.edit', $lista->id) }}" class="btn-link btn-edit" title="Editar lista">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('listas.destroy', $lista->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-link btn-delete delete-list" data-id="{{ $lista->id }}" title="Eliminar lista">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="empty-state">No has creado listas todavía.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('profile.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar la eliminación de listas
    document.querySelectorAll('.delete-list').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que quieres eliminar esta lista?')) {
                this.closest('form').submit();
            }
        });
    });
});
</script>
@endsection
