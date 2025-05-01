@extends('layouts.admin')

@section('title', 'Gestión de Usuarios - CritFlix Admin')

@section('header-title', 'Gestión de Usuarios')

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

    .user-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .user-filters .filter-group {
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

    .user-item {
        background-color: rgba(18, 18, 18, 0.6);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .user-item:hover {
        border-color: var(--verde-neon);
        background-color: rgba(0, 255, 102, 0.05);
    }

    .user-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid var(--border-color);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-avatar i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 1.8rem;
        background-color: var(--bg-darker);
        color: var(--text-light);
    }

    .user-info {
        flex: 1;
    }

    .user-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 0.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-email {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .user-meta {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .user-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .user-meta-item i {
        font-size: 0.9rem;
    }

    .user-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-left: auto;
    }

    .user-actions button,
    .user-actions a {
        background: transparent;
        border: 1px solid var(--border-color);
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border-radius: var(--border-radius);
        transition: all 0.2s ease;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .user-actions button:hover,
    .user-actions a:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .user-actions button.view:hover,
    .user-actions a.view:hover {
        color: var(--cyan-neon);
        border-color: var(--cyan-neon);
    }

    .user-actions button.edit:hover {
        color: var(--amarillo-neon);
        border-color: var(--amarillo-neon);
    }

    .user-actions button.admin:hover {
        color: var(--rojo-neon);
        border-color: var(--rojo-neon);
    }

    .user-actions button.delete:hover {
        color: var(--rojo-neon);
        border-color: var(--rojo-neon);
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .role-badge.admin {
        background-color: rgba(255, 48, 96, 0.15);
        color: #ff3060;
        border: 1px solid rgba(255, 48, 96, 0.3);
    }

    .role-badge.premium {
        background-color: rgba(255, 204, 0, 0.15);
        color: #ffcc00;
        border: 1px solid rgba(255, 204, 0, 0.3);
    }

    .role-badge.critico {
        background-color: rgba(0, 232, 255, 0.15);
        color: #00e8ff;
        border: 1px solid rgba(0, 232, 255, 0.3);
    }

    .role-badge.usuario {
        background-color: rgba(0, 255, 102, 0.15);
        color: #00ff66;
        border: 1px solid rgba(0, 255, 102, 0.3);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-badge.active {
        background-color: rgba(0, 255, 102, 0.15);
        color: #00ff66;
        border: 1px solid rgba(0, 255, 102, 0.3);
    }

    .status-badge.inactive {
        background-color: rgba(255, 48, 96, 0.15);
        color: #ff3060;
        border: 1px solid rgba(255, 48, 96, 0.3);
    }

    .stats-bar {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-item {
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1rem;
        flex: 1;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        border-color: var(--verde-neon);
        transform: translateY(-3px);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--verde-neon);
        margin-bottom: 0.25rem;
    }

    .stat-label {
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
    }

    .pagination li {
        margin: 0;
    }

    .pagination .page-link {
        display: block;
        padding: 0.5rem 0.75rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background-color: rgba(0, 255, 102, 0.1);
        border-color: var(--verde-neon);
        color: var(--verde-neon);
    }

    .pagination .active .page-link {
        background-color: var(--verde-neon);
        border-color: var(--verde-neon);
        color: var(--bg-darker);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--verde-neon);
    }

    .empty-state h3 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: var(--text-light);
    }

    .empty-state p {
        font-size: 0.95rem;
        max-width: 500px;
        margin: 0 auto;
    }

    /* Estilos para el modal */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        width: 90%;
        max-width: 600px;
        background: var(--bg-card);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .modal-backdrop.active .modal-container {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-light);
    }

    .modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .modal-close:hover {
        color: var(--rojo-neon);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: var(--bg-darker);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-light);
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--verde-neon);
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 102, 0.15);
        outline: none;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid transparent;
    }

    .btn-primary {
        background-color: var(--verde-neon);
        color: var(--bg-darker);
        border-color: var(--verde-neon);
    }

    .btn-primary:hover {
        background-color: rgba(0, 255, 102, 0.8);
        box-shadow: 0 0 15px rgba(0, 255, 102, 0.5);
    }

    .btn-secondary {
        background-color: transparent;
        color: var(--text-light);
        border-color: var(--border-color);
    }

    .btn-secondary:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .btn-danger {
        background-color: var(--rojo-neon);
        color: var(--bg-darker);
        border-color: var(--rojo-neon);
    }

    .btn-danger:hover {
        background-color: rgba(255, 48, 96, 0.8);
        box-shadow: 0 0 15px rgba(255, 48, 96, 0.5);
    }

    .toggle-wrapper {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--bg-darker);
        transition: 0.4s;
        border-radius: 34px;
        border: 1px solid var(--border-color);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 3px;
        background-color: var(--text-muted);
        transition: 0.4s;
        border-radius: 50%;
    }

    .toggle-input:checked + .toggle-slider {
        background-color: rgba(0, 255, 102, 0.3);
        border-color: var(--verde-neon);
    }

    .toggle-input:checked + .toggle-slider:before {
        transform: translateX(26px);
        background-color: var(--verde-neon);
    }

    .toggle-label {
        display: flex;
        align-items: center;
    }

    .toggle-text {
        margin-left: 0.75rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .user-filters {
            flex-direction: column;
        }

        .user-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-header {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .user-actions {
            width: 100%;
            justify-content: flex-start;
            margin-top: 1rem;
        }
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="admin-container">
    <!-- Contenido principal -->
    <div class="admin-content">
        <!-- Estadísticas de usuarios -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Usuarios</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['verified'] }}</div>
                <div class="stat-label">Activos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['unverified'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['new_today'] }}</div>
                <div class="stat-label">Nuevos Hoy</div>
            </div>
        </div>

        <!-- Filtros de usuarios -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-secondary" id="resetFilters">
                        <i class="fas fa-undo"></i> Reiniciar Filtros
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('admin.users') }}" method="GET">
                    <div class="user-filters">
                        <div class="filter-group">
                            <label for="keyword">Buscar usuario</label>
                            <input type="text" id="keyword" name="keyword" class="filter-control" placeholder="Nombre o email" value="{{ request('keyword') }}">
                        </div>
                        <div class="filter-group">
                            <label for="role">Rol</label>
                            <select id="role" name="role" class="filter-control">
                                <option value="">Todos los roles</option>
                                <option value="usuario" {{ request('role') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                                <option value="critico" {{ request('role') == 'critico' ? 'selected' : '' }}>Crítico</option>
                                <option value="premium" {{ request('role') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status">Estado</label>
                            <select id="status" name="status" class="filter-control">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pendientes</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="date">Fecha de registro</label>
                            <select id="date" name="date" class="filter-control">
                                <option value="">Cualquier fecha</option>
                                <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hoy</option>
                                <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>Últimos 7 días</option>
                                <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Último mes</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de usuarios -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Usuarios {{ request('keyword') ? 'encontrados' : 'registrados' }}</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-primary" id="addUserBtn">
                        <i class="fas fa-user-plus"></i> Añadir Usuario
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(isset($users) && count($users) > 0)
                    <!-- Contenedor de usuarios -->
                    <div id="users-container">
                        @foreach($users as $user)
                        <div class="user-item" data-id="{{ $user->id }}" data-role="{{ $user->rol }}" data-status="{{ $user->email_verified_at ? 'active' : 'inactive' }}">
                            <div class="user-avatar">
                                @if($user->foto_perfil)
                                    <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="{{ $user->name }}">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="user-info">
                                <div class="user-header">
                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                            <span class="role-badge {{ $user->rol }}">{{ ucfirst($user->rol) }}</span>
                                        </div>
                                        <div class="user-email">{{ $user->email }}</div>
                                        <div class="user-meta">
                                            <div class="user-meta-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                Registrado: {{ $user->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="user-meta-item">
                                                <i class="fas fa-clock"></i>
                                                Último acceso: {{ $user->last_login_at ?? 'Nunca' }}
                                            </div>
                                            <div class="user-meta-item">
                                                <i class="fas fa-circle {{ $user->email_verified_at ? 'text-success' : 'text-danger' }}"></i>
                                                {{ $user->email_verified_at ? 'Activo' : 'Pendiente' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="{{ route('profile.show', $user->id) }}" class="view" title="Ver perfil">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <button type="button" class="edit" data-id="{{ $user->id }}" title="Editar usuario">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        @if($user->rol !== 'admin')
                                        <button type="button" class="admin" data-id="{{ $user->id }}" title="Hacer administrador">
                                            <i class="fas fa-user-shield"></i> Hacer Admin
                                        </button>
                                        @endif
                                        <button type="button" class="delete" data-id="{{ $user->id }}" title="Eliminar usuario">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="pagination-container">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Estado vacío -->
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No hay usuarios disponibles</h3>
                        <p>
                            @if(request('keyword') || request('role') || request('status') || request('date'))
                                No se encontraron usuarios que coincidan con los filtros aplicados. Intenta con otros criterios.
                            @else
                                Aún no hay usuarios registrados en la plataforma.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div id="editUserModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Editar Usuario</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="userId" name="userId">
                <div class="form-group">
                    <label for="editName">Nombre</label>
                    <input type="text" id="editName" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editRole">Rol</label>
                    <select id="editRole" name="rol" class="form-control">
                        <option value="usuario">Usuario</option>
                        <option value="critico">Crítico</option>
                        <option value="premium">Premium</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editPassword">Contraseña (dejar en blanco para mantener)</label>
                    <input type="password" id="editPassword" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="toggle-label">
                        <span class="toggle-wrapper">
                            <input type="checkbox" id="editStatus" name="status" class="toggle-input">
                            <span class="toggle-slider"></span>
                        </span>
                        <span class="toggle-text">Usuario activo</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="saveUserBtn">Guardar Cambios</button>
        </div>
    </div>
</div>

<!-- Modal para añadir usuario -->
<div id="addUserModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Añadir Nuevo Usuario</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addUserForm">
                <div class="form-group">
                    <label for="addName">Nombre</label>
                    <input type="text" id="addName" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="addEmail">Email</label>
                    <input type="email" id="addEmail" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="addPassword">Contraseña</label>
                    <input type="password" id="addPassword" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="addRole">Rol</label>
                    <select id="addRole" name="rol" class="form-control">
                        <option value="usuario">Usuario</option>
                        <option value="critico">Crítico</option>
                        <option value="premium">Premium</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="toggle-label">
                        <span class="toggle-wrapper">
                            <input type="checkbox" id="addStatus" name="status" class="toggle-input" checked>
                            <span class="toggle-slider"></span>
                        </span>
                        <span class="toggle-text">Usuario activo</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="createUserBtn">Crear Usuario</button>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar usuario -->
<div id="deleteUserModal" class="modal-backdrop">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Eliminación</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
            <p class="text-danger"><strong>Nota:</strong> Se eliminarán todos los datos asociados a este usuario, incluyendo comentarios, valoraciones y listas.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar Usuario</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar funcionalidades
        setupUserActions();
        setupFilterReset();
        setupModals();
    });

    // Obtener el token CSRF de la meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /**
     * Configura los modales de edición, creación y eliminación
     */
    function setupModals() {
        // Abrir modal de edición
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                openEditUserModal(userId);
            });
        });

        // Abrir modal de añadir usuario
        document.getElementById('addUserBtn').addEventListener('click', function() {
            document.getElementById('addUserModal').classList.add('active');
        });

        // Cerrar modales
        document.querySelectorAll('.modal-close, .close-modal').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.modal-backdrop').forEach(modal => {
                    modal.classList.remove('active');
                });
            });
        });

        // Guardar cambios de usuario
        document.getElementById('saveUserBtn').addEventListener('click', saveUserChanges);

        // Crear nuevo usuario
        document.getElementById('createUserBtn').addEventListener('click', createUser);
    }

    /**
     * Configura las acciones de los usuarios (editar, eliminar, hacer admin)
     */
    function setupUserActions() {
        // Acción de eliminar
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');

                // Almacenar el ID del usuario a eliminar
                document.getElementById('confirmDeleteBtn').setAttribute('data-id', userId);

                // Mostrar modal de confirmación
                document.getElementById('deleteUserModal').classList.add('active');
            });
        });

        // Confirmar eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            deleteUser(userId);
        });

        // Hacer admin
        document.querySelectorAll('.admin').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');

                if (confirm('¿Estás seguro de que deseas convertir a este usuario en Administrador? Esta acción otorgará permisos completos sobre la plataforma.')) {
                    makeUserAdmin(userId);
                }
            });
        });
    }

    /**
     * Abre el modal de edición de usuario y carga sus datos
     * @param {string} userId - ID del usuario a editar
     */
    function openEditUserModal(userId) {
        // Mostrar notificación de carga
        showNotification('Cargando datos del usuario...', 'info');

        // Obtener datos del usuario
        fetch(`/api/admin/users/${userId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario con los datos del usuario
                const user = data.user;
                document.getElementById('userId').value = user.id;
                document.getElementById('editName').value = user.name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editRole').value = user.rol;
                document.getElementById('editStatus').checked = user.status;

                // Mostrar el modal
                document.getElementById('editUserModal').classList.add('active');
            } else {
                showNotification('Error al cargar los datos del usuario: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los datos del usuario', 'error');
        });
    }

    /**
     * Guarda los cambios del usuario
     */
    function saveUserChanges() {
        const userId = document.getElementById('userId').value;
        const name = document.getElementById('editName').value;
        const email = document.getElementById('editEmail').value;
        const rol = document.getElementById('editRole').value;
        const password = document.getElementById('editPassword').value;
        const status = document.getElementById('editStatus').checked;

        // Validación básica
        if (!name || !email) {
            showNotification('Por favor completa todos los campos requeridos', 'error');
            return;
        }

        // Mostrar notificación de carga
        showNotification('Guardando cambios...', 'info');

        // Preparar datos
        const userData = {
            name,
            email,
            rol,
            status
        };

        // Añadir contraseña solo si se ha proporcionado
        if (password) {
            userData.password = password;
        }

        // Enviar petición
        fetch(`/api/admin/users/${userId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(userData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                document.getElementById('editUserModal').classList.remove('active');

                // Actualizar la UI con los nuevos datos
                updateUserUI(data.user);

                // Mostrar notificación de éxito
                showNotification('Usuario actualizado correctamente', 'success');
            } else {
                showNotification('Error al actualizar usuario: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al actualizar usuario', 'error');
        });
    }

    /**
     * Crea un nuevo usuario
     */
    function createUser() {
        const name = document.getElementById('addName').value;
        const email = document.getElementById('addEmail').value;
        const password = document.getElementById('addPassword').value;
        const rol = document.getElementById('addRole').value;
        const status = document.getElementById('addStatus').checked;

        // Validación básica
        if (!name || !email || !password) {
            showNotification('Por favor completa todos los campos requeridos', 'error');
            return;
        }

        // Mostrar notificación de carga
        showNotification('Creando usuario...', 'info');

        // Preparar datos
        const userData = {
            name,
            email,
            password,
            rol,
            status
        };

        // Enviar petición
        fetch('/api/admin/users', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(userData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                document.getElementById('addUserModal').classList.remove('active');

                // Refrescar la página para mostrar el nuevo usuario
                window.location.reload();

                // Mostrar notificación de éxito
                showNotification('Usuario creado correctamente', 'success');
            } else {
                showNotification('Error al crear usuario: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al crear usuario', 'error');
        });
    }

    /**
     * Elimina un usuario
     * @param {string} userId - ID del usuario a eliminar
     */
    function deleteUser(userId) {
        // Mostrar notificación de carga
        showNotification('Eliminando usuario...', 'info');

        // Buscar fila del usuario
        const userItem = document.querySelector(`.user-item[data-id="${userId}"]`);

        // Efecto de eliminación inicial
        if (userItem) {
            userItem.style.opacity = '0.5';
        }

        // Enviar petición
        fetch(`/api/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                document.getElementById('deleteUserModal').classList.remove('active');

                // Animar eliminación de la fila
                if (userItem) {
                    userItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    userItem.style.opacity = '0';
                    userItem.style.transform = 'translateX(20px)';

                    setTimeout(() => {
                        userItem.remove();

                        // Comprobar si no quedan usuarios
                        if (document.querySelectorAll('.user-item').length === 0) {
                            const usersContainer = document.getElementById('users-container');
                            usersContainer.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h3>No hay usuarios disponibles</h3>
                                    <p>No se encontraron usuarios que coincidan con los filtros aplicados. Intenta con otros criterios.</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                // Mostrar notificación de éxito
                showNotification('Usuario eliminado correctamente', 'success');
            } else {
                // Restaurar opacidad si hay error
                if (userItem) {
                    userItem.style.opacity = '1';
                }

                showNotification('Error al eliminar usuario: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Restaurar opacidad si hay error
            if (userItem) {
                userItem.style.opacity = '1';
            }

            showNotification('Error al eliminar usuario', 'error');
        });
    }

    /**
     * Hace administrador a un usuario
     * @param {string} userId - ID del usuario a convertir en administrador
     */
    function makeUserAdmin(userId) {
        // Mostrar notificación de carga
        showNotification('Convirtiendo en administrador...', 'info');

        // Buscar fila del usuario
        const userItem = document.querySelector(`.user-item[data-id="${userId}"]`);

        // Enviar petición
        fetch(`/api/admin/users/${userId}/make-admin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la UI
                if (userItem) {
                    // Actualizar rol badge
                    const roleBadge = userItem.querySelector('.role-badge');
                    if (roleBadge) {
                        roleBadge.className = 'role-badge admin';
                        roleBadge.textContent = 'Admin';
                    }

                    // Actualizar atributo data-role
                    userItem.setAttribute('data-role', 'admin');

                    // Ocultar botón de hacer admin
                    const adminButton = userItem.querySelector('.admin');
                    if (adminButton) {
                        adminButton.remove();
                    }
                }

                // Mostrar notificación de éxito
                showNotification('Usuario convertido en administrador correctamente', 'success');
            } else {
                showNotification('Error al convertir usuario en administrador: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al convertir usuario en administrador', 'error');
        });
    }

    /**
     * Actualiza la UI de un usuario después de editarlo
     * @param {Object} user - Datos actualizados del usuario
     */
    function updateUserUI(user) {
        const userItem = document.querySelector(`.user-item[data-id="${user.id}"]`);

        if (userItem) {
            // Actualizar nombre
            const userName = userItem.querySelector('.user-name');
            if (userName) {
                // Mantener el badge y actualizar el nombre
                const badge = userName.querySelector('.role-badge');
                userName.textContent = '';
                userName.appendChild(document.createTextNode(user.name));
                if (badge) {
                    userName.appendChild(badge);
                }
            }

            // Actualizar email
            const userEmail = userItem.querySelector('.user-email');
            if (userEmail) {
                userEmail.textContent = user.email;
            }

            // Actualizar rol
            const roleBadge = userItem.querySelector('.role-badge');
            if (roleBadge) {
                roleBadge.className = `role-badge ${user.rol}`;
                roleBadge.textContent = user.rol.charAt(0).toUpperCase() + user.rol.slice(1);
            }

            // Actualizar estado
            const statusIcon = userItem.querySelector('.fas.fa-circle');
            if (statusIcon) {
                if (user.status) {
                    statusIcon.className = 'fas fa-circle text-success';
                    statusIcon.parentElement.lastChild.textContent = 'Activo';
                } else {
                    statusIcon.className = 'fas fa-circle text-danger';
                    statusIcon.parentElement.lastChild.textContent = 'Pendiente';
                }
            }

            // Actualizar botón de hacer admin si es necesario
            if (user.rol === 'admin') {
                const adminButton = userItem.querySelector('.admin');
                if (adminButton) {
                    adminButton.remove();
                }
            }

            // Actualizar atributos de datos
            userItem.setAttribute('data-role', user.rol);
            userItem.setAttribute('data-status', user.status ? 'active' : 'inactive');
        }
    }

    /**
     * Configura el botón para reiniciar filtros
     */
    function setupFilterReset() {
        const resetButton = document.getElementById('resetFilters');

        if (resetButton) {
            resetButton.addEventListener('click', function() {
                const form = document.getElementById('filterForm');

                // Limpiar todos los campos
                form.querySelectorAll('input, select').forEach(field => {
                    if (field.type === 'text') {
                        field.value = '';
                    } else if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    }
                });

                // Enviar el formulario
                form.submit();
            });
        }
    }

    /**
     * Muestra una notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de notificación (success, error, info, warning)
     */
    function showNotification(message, type = 'info') {
        // Verificar si ya existe el contenedor de notificaciones
        let notificationsContainer = document.querySelector('.notifications-container');

        // Si no existe, crear uno nuevo
        if (!notificationsContainer) {
            notificationsContainer = document.createElement('div');
            notificationsContainer.className = 'notifications-container';
            document.body.appendChild(notificationsContainer);

            // Estilos para el contenedor
            notificationsContainer.style.position = 'fixed';
            notificationsContainer.style.top = '20px';
            notificationsContainer.style.right = '20px';
            notificationsContainer.style.zIndex = '9999';
            notificationsContainer.style.display = 'flex';
            notificationsContainer.style.flexDirection = 'column';
            notificationsContainer.style.alignItems = 'flex-end';
            notificationsContainer.style.gap = '10px';
        }

        // Crear la notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' :
                            type === 'error' ? 'times-circle' :
                            type === 'warning' ? 'exclamation-triangle' :
                            'info-circle'}"></i>
            </div>
            <div class="notification-content">${message}</div>
            <button class="notification-close">×</button>
        `;

        // Estilos para la notificación
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.padding = '12px 15px';
        notification.style.borderRadius = '5px';
        notification.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.2)';
        notification.style.marginBottom = '10px';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        notification.style.maxWidth = '350px';
        notification.style.backgroundColor = 'var(--bg-card)';
        notification.style.color = 'var(--text-light)';
        notification.style.border = `1px solid ${
            type === 'success' ? 'var(--verde-neon)' :
            type === 'error' ? 'var(--rojo-neon)' :
            type === 'warning' ? 'var(--amarillo-neon)' :
            'var(--cyan-neon)'
        }`;

        // Estilos para el icono
        const icon = notification.querySelector('.notification-icon');
        icon.style.marginRight = '10px';
        icon.style.fontSize = '1.1rem';
        icon.style.color = type === 'success' ? 'var(--verde-neon)' :
                          type === 'error' ? 'var(--rojo-neon)' :
                          type === 'warning' ? 'var(--amarillo-neon)' :
                          'var(--cyan-neon)';

        // Estilos para el botón cerrar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.background = 'none';
        closeBtn.style.border = 'none';
        closeBtn.style.color = 'var(--text-muted)';
        closeBtn.style.fontSize = '1.2rem';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.marginLeft = '10px';
        closeBtn.style.padding = '0 5px';

        // Función para eliminar la notificación
        const removeNotification = () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(50px)';

            setTimeout(() => {
                notification.remove();
            }, 300);
        };

        // Evento para el botón cerrar
        closeBtn.addEventListener('click', removeNotification);

        // Auto cerrar después de cierto tiempo
        const duration = type === 'error' ? 6000 : 4000;
        setTimeout(removeNotification, duration);

        // Agregar la notificación al contenedor
        notificationsContainer.appendChild(notification);

        // Animar la entrada
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
    }
</script>
@endpush
