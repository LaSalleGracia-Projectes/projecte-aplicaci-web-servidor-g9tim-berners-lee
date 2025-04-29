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

    /* Estilos específicos para esta sección */
    .filter-btn.active {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
        box-shadow: var(--neon-primary-glow);
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

    .action-btn.verify {
        color: var(--accent-danger);
    }

    .action-btn.verify.verified {
        color: var(--accent-success);
    }

    .action-btn.verify:hover {
        color: var(--accent-success);
    }

    .action-btn.verify.verified:hover {
        color: var(--accent-danger);
    }

    /* Estilos para los badges de roles y estados */
    .status-badge.usuario,
    .status-badge.critico,
    .status-badge.premium,
    .status-badge.activo,
    .status-badge.inactivo,
    .status-badge.pendiente {
        border: 1px solid rgba(20, 255, 20, 0.3);
    }

    .status-badge.usuario {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
    }

    .status-badge.critico {
        background-color: rgba(255, 187, 0, 0.15);
        color: var(--accent-warning);
    }

    .status-badge.premium {
        background-color: rgba(0, 255, 221, 0.15);
        color: var(--verde-claro);
    }

    .status-badge.admin {
        background-color: rgba(255, 0, 0, 0.15);
        color: var(--accent-danger);
        border: 1px solid var(--accent-danger);
    }

    .status-badge.activo {
        background-color: rgba(20, 255, 20, 0.15);
        color: var(--verde-neon);
    }

    .status-badge.inactivo,
    .status-badge.pendiente {
        background-color: rgba(255, 51, 102, 0.15);
        color: var(--accent-danger);
    }

    /* Estilos para notificaciones */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 400px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 16px;
        margin-bottom: 16px;
        transform: translateX(120%);
        transition: transform 0.3s ease-in-out;
        z-index: 9999;
        border-left: 4px solid;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-content {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .notification-icon {
        font-size: 24px;
        flex-shrink: 0;
    }

    .notification-body {
        flex-grow: 1;
    }

    .notification-title {
        margin: 0 0 4px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .notification-message {
        margin: 0;
        font-size: 14px;
        color: #666;
        line-height: 1.4;
    }

    .notification-close {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: #999;
        font-size: 16px;
        transition: color 0.2s;
    }

    .notification-close:hover {
        color: #666;
    }

    /* Colores específicos para cada tipo de notificación */
    .notification.success {
        border-left-color: #4CAF50;
    }

    .notification.error {
        border-left-color: #F44336;
    }

    .notification.warning {
        border-left-color: #FF9800;
    }

    .notification.info {
        border-left-color: #2196F3;
    }
</style>
@endpush

@section('title', 'Administración de Usuarios - CritFlix')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

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
            <a href="{{ route('admin.users') }}" class="admin-nav-item active">
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
                <h2>Gestión de Usuarios</h2>
                <p>Administra los usuarios de la plataforma</p>
            </div>
            <div class="admin-header-actions">
                <div class="admin-search">
                    <input type="text" id="search-users" placeholder="Buscar usuarios...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <button class="btn-neon">
                    <i class="fas fa-user-plus"></i> Añadir Usuario
                </button>
            </div>
        </header>

        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="stat-card neon-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Usuarios</h3>
                    <p class="stat-number">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Usuarios Activos</h3>
                    <p class="stat-number">{{ $stats['verified'] }}</p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Pendientes</h3>
                    <p class="stat-number">{{ $stats['unverified'] }}</p>
                </div>
            </div>
            <div class="stat-card neon-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <h3>Nuevos Hoy</h3>
                    <p class="stat-number">{{ $stats['new_today'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filtros de usuarios -->
        <div class="filters-container neon-card">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros</h3>
                <button class="btn-reset-filters"><i class="fas fa-undo-alt"></i> Restablecer</button>
            </div>
            <div class="filters-body">
                <div class="filter-group">
                    <label>Rol de usuario</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todos</button>
                        <button class="filter-btn" data-filter="usuario">Usuarios</button>
                        <button class="filter-btn" data-filter="critico">Críticos</button>
                        <button class="filter-btn" data-filter="premium">Premium</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Estado de cuenta</label>
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">Todos</button>
                        <button class="filter-btn" data-filter="activo">Activos</button>
                        <button class="filter-btn" data-filter="inactivo">Inactivos</button>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Fecha de registro</label>
                    <div class="date-range">
                        <input type="date" id="date-from" class="neon-input" placeholder="Desde">
                        <span><i class="fas fa-arrow-right"></i></span>
                        <input type="date" id="date-to" class="neon-input" placeholder="Hasta">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="admin-table-card neon-card">
            <div class="table-actions">
                <div class="table-info">
                    <span>Mostrando <strong>{{ $users->count() }}</strong> de <strong>{{ $users->total() }}</strong> usuarios</span>
                </div>
                <div class="bulk-actions">
                    <select class="neon-input" id="bulk-action">
                        <option value="">Acciones en masa</option>
                        <option value="delete">Eliminar seleccionados</option>
                        <option value="activate">Activar seleccionados</option>
                        <option value="deactivate">Desactivar seleccionados</option>
                        <option value="make-critic">Convertir a Crítico</option>
                        <option value="make-premium">Convertir a Premium</option>
                    </select>
                    <button class="btn-neon apply-action" disabled>Aplicar</button>
                </div>
            </div>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha de registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($users->count() > 0)
                            @foreach($users as $user)
                            <tr data-user-id="{{ $user->id }}" data-role="{{ $user->rol }}" data-status="{{ $user->email_verified_at ? 'activo' : 'inactivo' }}">
                                <td>
                                    <input type="checkbox" class="user-select" data-id="{{ $user->id }}">
                                </td>
                                <td>#{{ $user->id }}</td>
                                <td>
                                    <div class="user-avatar">
                                        @if($user->foto_perfil)
                                            <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="{{ $user->name }}">
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="status-badge {{ $user->rol }}">{{ ucfirst($user->rol) }}</span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="status-badge {{ $user->email_verified_at ? 'activo' : 'inactivo' }}">
                                        {{ $user->email_verified_at ? 'Activo' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="{{ route('profile.show', $user->id) }}" class="action-btn view" data-id="{{ $user->id }}" title="Ver perfil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="action-btn edit" data-id="{{ $user->id }}" title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($user->rol !== 'admin')
                                    <button class="action-btn make-admin" data-id="{{ $user->id }}" title="Hacer administrador">
                                        <i class="fas fa-user-shield"></i>
                                    </button>
                                    @endif
                                    <button class="action-btn delete" data-id="{{ $user->id }}" title="Eliminar usuario">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No hay usuarios disponibles</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div id="edit-user-modal" class="admin-modal-container">
    <div class="admin-modal neon-card">
        <div class="modal-header">
            <h3>Editar Usuario</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label for="edit-name">Nombre</label>
                    <input type="text" id="edit-name" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="edit-role">Rol</label>
                    <select id="edit-role" class="neon-input">
                        <option value="usuario">Usuario</option>
                        <option value="critico">Crítico</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="edit-status">
                        <label for="edit-status">
                            <span class="slider"></span>
                            <span class="labels" data-on="Activo" data-off="Inactivo"></span>
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-neon cancel-edit">Cancelar</button>
                    <button type="submit" class="btn-neon save-user">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para añadir usuario -->
<div id="add-user-modal" class="admin-modal-container">
    <div class="admin-modal neon-card">
        <div class="modal-header">
            <h3>Añadir Nuevo Usuario</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="add-user-form">
                <div class="form-group">
                    <label for="add-name">Nombre</label>
                    <input type="text" id="add-name" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="add-email">Email</label>
                    <input type="email" id="add-email" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="add-password">Contraseña</label>
                    <input type="password" id="add-password" class="neon-input" required>
                </div>
                <div class="form-group">
                    <label for="add-role">Rol</label>
                    <select id="add-role" class="neon-input">
                        <option value="usuario">Usuario</option>
                        <option value="critico">Crítico</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="add-status" checked>
                        <label for="add-status">
                            <span class="slider"></span>
                            <span class="labels" data-on="Activo" data-off="Inactivo"></span>
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-neon cancel-add">Cancelar</button>
                    <button type="submit" class="btn-neon add-user">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/main.js') }}"></script>
<script src="{{ asset('js/admin/users.js') }}"></script>
@endsection
