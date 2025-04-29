@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin.css') }}">
@endpus

@section('title', 'Mi Perfil de Administrador - CritFlix')

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
            <a href="{{ route('admin.reviews') }}" class="admin-nav-item">
                <i class="fas fa-star"></i> <span>Valoraciones</span>
            </a>
            <a href="{{ route('admin.comments') }}" class="admin-nav-item">
                <i class="fas fa-comments"></i> <span>Comentarios</span>
            </a>
            <div class="admin-nav-divider"></div>
            <a href="{{ route('admin.profile') }}" class="admin-nav-item active">
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
                <h2>Mi Perfil</h2>
                <p>Gestiona tu información de administrador</p>
            </div>
            <div class="admin-header-actions">
                <div class="admin-search">
                    <input type="text" placeholder="Buscar...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="admin-user-menu">
                    <span>Admin</span>
                    <div class="admin-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-profile-container">
            <!-- Tarjeta de perfil -->
            <div class="admin-profile-card neon-card">
                <div class="profile-header">
                    <div class="profile-cover"></div>
                    <div class="profile-avatar-large">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="profile-info">
                        <h2>{{ $admin->name }}</h2>
                        <p class="profile-role">{{ ucfirst($admin->rol) }} - Administrador</p>
                        <p class="profile-email">{{ $admin->email }}</p>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">65</span>
                        <span class="stat-label">Acciones</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">132</span>
                        <span class="stat-label">Modificaciones</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">87</span>
                        <span class="stat-label">Días activo</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn-neon edit-profile">
                        <i class="fas fa-edit"></i> Editar perfil
                    </button>
                    <button class="btn-neon change-password">
                        <i class="fas fa-key"></i> Cambiar contraseña
                    </button>
                </div>
            </div>

            <!-- Información personal -->
            <div class="admin-profile-details neon-card">
                <h3>Información personal</h3>
                <form id="admin-profile-form" class="admin-form">
                    <div class="form-group">
                        <label for="admin-name">Nombre completo</label>
                        <input type="text" id="admin-name" value="{{ $admin->name }}" class="neon-input">
                    </div>
                    <div class="form-group">
                        <label for="admin-email">Correo electrónico</label>
                        <input type="email" id="admin-email" value="{{ $admin->email }}" class="neon-input">
                    </div>
                    <div class="form-group">
                        <label for="admin-role">Rol de administrador</label>
                        <select id="admin-role" class="neon-input">
                            <option value="moderador" {{ $admin->rol === 'moderador' ? 'selected' : '' }}>Moderador</option>
                            <option value="superadmin" {{ $admin->rol === 'superadmin' ? 'selected' : '' }}>Super Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin-bio">Biografía</label>
                        <textarea id="admin-bio" class="neon-input" rows="4">{{ $admin->biografia ?? 'Administrador del sistema CritFlix, encargado de gestionar usuarios, películas y valoraciones.' }}</textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-neon save-profile">Guardar cambios</button>
                    </div>
                </form>
            </div>

            <!-- Actividad reciente -->
            <div class="admin-recent-activity neon-card">
                <h3>Actividad reciente</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has añadido un nuevo usuario: <strong>María Rodríguez</strong></p>
                            <span class="activity-time">Hace 2 horas</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has modificado la información de la película: <strong>Oppenheimer</strong></p>
                            <span class="activity-time">Ayer, 14:30</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has eliminado una valoración inapropiada del usuario: <strong>Carlos85</strong></p>
                            <span class="activity-time">Hace 2 días</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-film"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has añadido una nueva película: <strong>Dune 2</strong></p>
                            <span class="activity-time">Hace 3 días</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has cambiado el rol del usuario <strong>JuanAP</strong> a <strong>Crítico</strong></p>
                            <span class="activity-time">Hace 1 semana</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de administración -->
            <div class="admin-profile-stats neon-card">
                <h3>Estadísticas de administración</h3>
                <div class="admin-profile-stats-grid">
                    <div class="stats-metric">
                        <div class="metric-value">85%</div>
                        <div class="metric-title">Eficiencia de gestión</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 85%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <div class="metric-value">92%</div>
                        <div class="metric-title">Tasa de resolución</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 92%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <div class="metric-value">78%</div>
                        <div class="metric-title">Participación</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 78%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <div class="metric-value">95%</div>
                        <div class="metric-title">Satisfacción</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 95%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin.js') }}"></script>
@endsection
