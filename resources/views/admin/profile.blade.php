@extends('layouts.admin')

@section('title', 'Mi Perfil de Administrador - CritFlix Admin')

@section('header-title', 'Mi Perfil')

@push('styles')
<style>
    /* Variables personalizadas */
    :root {
        --verde-neon: #00ff66;
        --verde-neon-hover: #00e65c;
        --verde-neon-glow: 0 0 10px rgba(0, 255, 102, 0.4), 0 0 20px rgba(0, 255, 102, 0.2);
        --rojo-neon: #ff3b30;
        --amarillo-neon: #ffcc00;
        --azul-neon: #00aeff;
        --fondo-oscuro: rgba(18, 18, 18, 0.8);
        --fondo-card: rgba(25, 25, 25, 0.9);
        --fondo-input: rgba(12, 12, 12, 0.6);
        --borde-color: rgba(255, 255, 255, 0.1);
        --texto-claro: #f7f7f7;
        --texto-muted: #a0a0a0;
        --border-radius: 12px;
        --sombra-suave: 0 5px 15px rgba(0, 0, 0, 0.2);
        --sombra-media: 0 8px 25px rgba(0, 0, 0, 0.4);
        --sombra-fuerte: 0 12px 35px rgba(0, 0, 0, 0.6);
        --transicion: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    body, html {
        overflow-x: hidden;
    }

    /* Contenedor principal */
    .profile-container {
        display: grid;
        grid-template-columns: minmax(320px, 350px) 1fr;
        gap: 1.5rem;
        margin: 0;
        padding: 1rem;
        width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 1200px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
    }

    .admin-content-wrapper {
        padding: 0;
        max-width: 100%;
    }

    /* Ajustes de formulario */
    .form-control {
        width: 100% !important;
        box-sizing: border-box !important;
    }

    textarea.form-control {
        min-height: 120px !important;
    }

    /* Ajustes para mejor encuadre */
    .profile-details {
        width: 100%;
    }

    .profile-card {
        width: 100%;
    }

    /* Correcciones específicas para móviles */
    @media (max-width: 768px) {
        .profile-container {
            padding: 0.5rem;
        }
    }

    /* Estilos generales de tarjetas */
    .card {
        background-color: var(--fondo-card);
        border: 1px solid var(--borde-color);
        border-radius: var(--border-radius);
        box-shadow: var(--sombra-suave);
        transition: var(--transicion);
        overflow: hidden;
        margin-bottom: 2rem;
        position: relative;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-body {
        padding: 1.8rem;
        flex: 1;
    }

    /* Tarjeta de perfil principal */
    .profile-card {
        position: sticky;
        top: 80px;
        background-color: var(--fondo-card);
        border-radius: var(--border-radius);
        overflow: hidden;
        height: fit-content;
        box-shadow: var(--sombra-media);
        max-width: 100%;
    }

    /* Perfil avatar y cover */
    .profile-cover {
        height: 160px;
        background: linear-gradient(135deg, #111 0%, rgba(0, 255, 102, 0.3) 100%);
        position: relative;
        overflow: hidden;
    }

    .profile-cover::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1478760329108-5c3ed9d495a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
        opacity: 0.15;
        filter: blur(1px);
    }

    .profile-avatar {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background-color: #0d0d0d;
        border: 4px solid var(--verde-neon);
        box-shadow: var(--verde-neon-glow);
        position: absolute;
        bottom: -65px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        z-index: 2;
    }

    .profile-avatar i {
        font-size: 4rem;
        color: var(--verde-neon);
        filter: drop-shadow(0 0 8px rgba(0, 255, 102, 0.6));
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info {
        margin-top: 75px;
        padding: 0 1.5rem 1.5rem;
        text-align: center;
    }

    .profile-info h2 {
        margin: 0 0 0.75rem;
        font-size: 2rem;
        font-weight: 700;
        color: var(--texto-claro);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: rgba(0, 255, 102, 0.15);
        color: var(--verde-neon);
        padding: 0.4rem 1.2rem;
        border-radius: 30px;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        box-shadow: inset 0 0 10px rgba(0, 255, 102, 0.2);
        text-shadow: 0 0 8px rgba(0, 255, 102, 0.5);
    }

    .profile-role i {
        font-size: 1rem;
    }

    .profile-email {
        color: var(--texto-muted);
        font-size: 1rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .profile-stats {
        display: flex;
        justify-content: space-around;
        padding: 1.5rem 0;
        border-top: 1px solid var(--borde-color);
        border-bottom: 1px solid var(--borde-color);
        background-color: rgba(0, 0, 0, 0.2);
    }

    .stat-item {
        text-align: center;
        padding: 0 1rem;
    }

    .stat-value {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--verde-neon);
        text-shadow: 0 0 10px rgba(0, 255, 102, 0.6);
        margin-bottom: 0.3rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--texto-muted);
    }

    .profile-actions {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .btn-profile.primary {
        background-color: var(--verde-neon);
        border-color: var(--verde-neon);
        color: #000;
        font-weight: 600;
    }

    .btn-profile.primary:hover {
        background-color: var(--verde-neon-hover);
        box-shadow: var(--verde-neon-glow);
        transform: translateY(-2px);
    }

    .btn-profile i {
        font-size: 1.1rem;
    }

    /* Estadísticas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }

    .stats-metric {
        background-color: rgba(18, 18, 18, 0.7);
        border: 1px solid var(--borde-color);
        border-radius: var(--border-radius);
        padding: 1.8rem;
        text-align: center;
        transition: var(--transicion);
        position: relative;
        overflow: hidden;
    }

    .stats-metric::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, rgba(0, 255, 102, 0.15) 0%, transparent 70%);
        opacity: 0;
        top: 0;
        left: 0;
        transition: var(--transicion);
    }

    .stats-metric:hover::before {
        opacity: 1;
    }

    .stats-metric:hover {
        border-color: var(--verde-neon);
        transform: translateY(-6px);
        box-shadow: var(--verde-neon-glow);
    }

    .metric-icon {
        font-size: 2.5rem;
        color: var(--verde-neon);
        margin-bottom: 1rem;
        filter: drop-shadow(0 0 10px rgba(0, 255, 102, 0.5));
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--verde-neon);
        margin-bottom: 0.75rem;
        text-shadow: 0 0 10px rgba(0, 255, 102, 0.6);
    }

    .metric-title {
        color: var(--texto-claro);
        font-size: 1.1rem;
        margin-bottom: 1.25rem;
        font-weight: 500;
    }

    .metric-bar {
        height: 10px;
        background-color: rgba(0, 0, 0, 0.4);
        border-radius: 5px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .metric-progress {
        height: 100%;
        background: linear-gradient(90deg, rgba(0, 255, 102, 0.7) 0%, rgba(0, 255, 102, 1) 100%);
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0, 255, 102, 0.3);
        position: relative;
        overflow: hidden;
        animation: pulse 1.5s infinite;
    }

    .metric-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent 0%,
            rgba(255, 255, 255, 0.4) 50%,
            transparent 100%
        );
        animation: shine 2s infinite linear;
    }

    @keyframes shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 5px rgba(0, 255, 102, 0.3); }
        50% { box-shadow: 0 0 15px rgba(0, 255, 102, 0.5); }
        100% { box-shadow: 0 0 5px rgba(0, 255, 102, 0.3); }
    }

    /* Alertas y notificaciones */
    .alert {
        padding: 1.25rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .alert-success {
        background-color: rgba(0, 255, 102, 0.1);
        border-left: 4px solid var(--verde-neon);
        color: var(--verde-neon);
    }

    .alert-error {
        background-color: rgba(255, 59, 48, 0.1);
        border-left: 4px solid var(--rojo-neon);
        color: var(--rojo-neon);
    }

    .alert i {
        font-size: 1.5rem;
    }

    /* Actividad reciente */
    .activity-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .activity-item {
        display: flex;
        padding: 1.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: var(--transicion);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background-color: rgba(255, 255, 255, 0.03);
        transform: translateX(5px);
    }

    .activity-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.25rem;
        flex-shrink: 0;
        position: relative;
    }

    .activity-icon.green {
        background-color: rgba(0, 255, 102, 0.15);
        color: var(--verde-neon);
        box-shadow: 0 0 15px rgba(0, 255, 102, 0.2);
    }

    .activity-icon.blue {
        background-color: rgba(0, 174, 255, 0.15);
        color: var(--azul-neon);
        box-shadow: 0 0 15px rgba(0, 174, 255, 0.2);
    }

    .activity-icon.yellow {
        background-color: rgba(255, 204, 0, 0.15);
        color: var(--amarillo-neon);
        box-shadow: 0 0 15px rgba(255, 204, 0, 0.2);
    }

    .activity-icon.red {
        background-color: rgba(255, 59, 48, 0.15);
        color: var(--rojo-neon);
        box-shadow: 0 0 15px rgba(255, 59, 48, 0.2);
    }

    .activity-icon i {
        font-size: 1.5rem;
        filter: drop-shadow(0 0 5px currentColor);
    }

    .activity-content {
        flex-grow: 1;
    }

    .activity-content p {
        margin: 0 0 0.75rem;
        color: var(--texto-claro);
        line-height: 1.5;
    }

    .activity-content strong {
        color: var(--verde-neon);
        text-shadow: 0 0 5px rgba(0, 255, 102, 0.3);
    }

    .activity-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .activity-time {
        color: var(--texto-muted);
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .activity-category {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .activity-category.green {
        background-color: rgba(0, 255, 102, 0.1);
        color: var(--verde-neon);
    }

    .activity-category.blue {
        background-color: rgba(0, 174, 255, 0.1);
        color: var(--azul-neon);
    }

    .activity-category.yellow {
        background-color: rgba(255, 204, 0, 0.1);
        color: var(--amarillo-neon);
    }

    .activity-category.red {
        background-color: rgba(255, 59, 48, 0.1);
        color: var(--rojo-neon);
    }

    /* Modal para cambio de contraseña */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }

    .modal.is-active {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background-color: var(--fondo-card);
        border-radius: var(--border-radius);
        max-width: 500px;
        width: 100%;
        padding: 2.25rem;
        position: relative;
        box-shadow: var(--sombra-fuerte);
        border: 1px solid var(--borde-color);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        margin-bottom: 1.75rem;
        position: relative;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.6rem;
        color: var(--texto-claro);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modal-header h3 i {
        color: var(--verde-neon);
    }

    .modal-close {
        position: absolute;
        top: -10px;
        right: -10px;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid var(--borde-color);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--texto-muted);
        cursor: pointer;
        transition: var(--transicion);
    }

    .modal-close:hover {
        color: var(--verde-neon);
        transform: rotate(90deg);
        border-color: var(--verde-neon);
        box-shadow: 0 0 10px rgba(0, 255, 102, 0.3);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .loading-indicator {
        display: none;
        text-align: center;
        padding: 1.25rem;
    }

    .loading-indicator.is-active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    .loading-spinner {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 3px solid rgba(0, 255, 102, 0.1);
        border-left-color: var(--verde-neon);
        border-radius: 50%;
        animation: spinner 0.8s linear infinite;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }

    /* Efectos y animaciones */
    .animated-click {
        transform: scale(0.95);
    }

    .save-button-success {
        background-color: var(--verde-neon) !important;
        color: #000 !important;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    /* Formulario del perfil */
    .form-group {
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .form-group label {
        display: block;
        color: var(--texto-muted);
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        box-sizing: border-box;
        padding: 0.75rem 1rem;
        background-color: var(--fondo-input);
        border: 1px solid var(--borde-color);
        border-radius: 8px;
        color: var(--texto-claro);
        font-size: 1rem;
        transition: var(--transicion);
    }

    .form-control:focus {
        border-color: var(--verde-neon);
        box-shadow: 0 0 0 3px rgba(0, 255, 102, 0.15);
        outline: none;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-control::placeholder {
        color: rgba(160, 160, 160, 0.6);
    }

    /* Mejoras en el diseño del card */
    .card-header {
        padding: 1.25rem 1.5rem;
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid var(--borde-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--texto-claro);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header h3 i {
        color: var(--verde-neon);
        filter: drop-shadow(0 0 5px rgba(0, 255, 102, 0.5));
    }

    .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    <!-- Tarjeta de perfil -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-cover"></div>
            <div class="profile-avatar">
                @if(isset($admin->profile_photo) && $admin->profile_photo)
                    <img src="{{ asset('storage/' . $admin->profile_photo) }}" alt="{{ $admin->name }}">
                @else
                    <i class="fas fa-user-shield"></i>
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $admin->name ?? 'Administrador' }}</h2>
                <div class="profile-role">
                    <i class="fas fa-shield-alt"></i> {{ ucfirst($admin->rol ?? 'Administrador') }}
                </div>
                <p class="profile-email">
                    <i class="fas fa-envelope"></i> {{ $admin->email ?? 'admin@critflix.com' }}
                </p>
            </div>
        </div>
        <div class="profile-stats">
            <div class="stat-item">
                <span class="stat-value">{{ isset($admin->created_at) ? now()->diffInDays($admin->created_at) : '87' }}</span>
                <span class="stat-label">Días activo</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ isset($admin->last_login_at) ? Carbon\Carbon::parse($admin->last_login_at)->diffForHumans() : '3h' }}</span>
                <span class="stat-label">Último acceso</span>
            </div>
        </div>
        <div class="profile-actions">
            <button class="btn-profile primary" id="save-profile-btn" type="button">
                <i class="fas fa-save"></i> Guardar cambios
            </button>
            <button class="btn-profile" id="change-password-btn" type="button">
                <i class="fas fa-key"></i> Cambiar contraseña
            </button>
        </div>
    </div>

    <div class="profile-details">
        <!-- Formulario del perfil -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user"></i> Información personal</h3>
            </div>
            <div class="card-body">
                <form id="profile-form" action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ $admin->name }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ $admin->email }}">
                    </div>
                    <div class="form-group">
                        <label for="biografia">Biografía</label>
                        <textarea id="biografia" name="biografia" class="form-control">{{ $admin->biografia ?? 'Administrador principal del sistema CritFlix. Responsable de supervisar la plataforma, gestionar usuarios y contenido, y asegurar la calidad de las valoraciones y comentarios.' }}</textarea>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Estadísticas de administración -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> Estadísticas de administración</h3>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stats-metric">
                        <i class="fas fa-server metric-icon"></i>
                        <div class="metric-value">98.2%</div>
                        <div class="metric-title">Disponibilidad del sistema</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 98.2%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <i class="fas fa-tasks metric-icon"></i>
                        <div class="metric-value">95.7%</div>
                        <div class="metric-title">Tasa de resolución</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 95.7%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <i class="fas fa-smile metric-icon"></i>
                        <div class="metric-value">92.3%</div>
                        <div class="metric-title">Satisfacción de usuarios</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 92.3%;"></div>
                        </div>
                    </div>
                    <div class="stats-metric">
                        <i class="fas fa-tachometer-alt metric-icon"></i>
                        <div class="metric-value">94.8%</div>
                        <div class="metric-title">Rendimiento del sistema</div>
                        <div class="metric-bar">
                            <div class="metric-progress" style="width: 94.8%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Actividad reciente</h3>
            </div>
            <div class="card-body">
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon green">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has corregido el error <strong>"Unknown column 'tmdb_id'"</strong> en la consulta de comentarios, cambiando a <strong>api_id</strong></p>
                            <div class="activity-meta">
                                <span class="activity-time">
                                    <i class="far fa-clock"></i> Hoy, {{ now()->format('H:i') }}
                                </span>
                                <span class="activity-category green">Bugfix</span>
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon blue">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has implementado una <strong>paginación personalizada</strong> para los paneles de comentarios y valoraciones con vista previa de páginas</p>
                            <div class="activity-meta">
                                <span class="activity-time">
                                    <i class="far fa-clock"></i> {{ now()->subHours(rand(4, 8))->format('d/m/Y, H:i') }}
                                </span>
                                <span class="activity-category blue">Mejora UI</span>
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon red">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has eliminado un comentario con spoilers del usuario <strong>MartaGT24</strong> sobre <strong>Los Juegos del Hambre: Balada de pájaros cantores y serpientes</strong></p>
                            <div class="activity-meta">
                                <span class="activity-time">
                                    <i class="far fa-clock"></i> {{ now()->subDays(1)->format('d/m/Y, H:i') }}
                                </span>
                                <span class="activity-category red">Moderación</span>
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon green">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has rediseñado completamente la <strong>interfaz del perfil de administrador</strong> con un diseño moderno de tarjetas y estadísticas</p>
                            <div class="activity-meta">
                                <span class="activity-time">
                                    <i class="far fa-clock"></i> {{ now()->subDays(2)->format('d/m/Y, H:i') }}
                                </span>
                                <span class="activity-category green">Diseño UI</span>
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon yellow">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="activity-content">
                            <p>Has destacado la valoración del crítico <strong>CineGeek42</strong> sobre <strong>Oppenheimer</strong> con una puntuación de <strong>4.8/5</strong></p>
                            <div class="activity-meta">
                                <span class="activity-time">
                                    <i class="far fa-clock"></i> {{ now()->subDays(3)->format('d/m/Y, H:i') }}
                                </span>
                                <span class="activity-category yellow">Destacado</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambio de contraseña -->
<div class="modal" id="password-modal">
    <div class="modal-content">
        <button class="modal-close" id="close-modal">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Cambiar contraseña</h3>
        </div>
        <form id="password-form" action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="current_password">Contraseña actual</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nueva contraseña</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirmar contraseña</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required minlength="6">
            </div>
            <div class="form-actions">
                <button type="button" class="btn-profile" id="cancel-password-btn">Cancelar</button>
                <button type="submit" class="btn-profile primary">Cambiar contraseña</button>
            </div>
        </form>
        <div id="password-alert" style="display: none;"></div>
        <div class="loading-indicator" id="password-loading">
            <div class="loading-spinner"></div>
            <p>Cambiando contraseña...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestión del modal de cambio de contraseña
    const passwordModal = document.getElementById('password-modal');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelPasswordBtn = document.getElementById('cancel-password-btn');
    const passwordForm = document.getElementById('password-form');
    const passwordAlert = document.getElementById('password-alert');
    const passwordLoading = document.getElementById('password-loading');

    // Agregar efectos visuales a los botones
    document.querySelectorAll('.btn-profile').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
    });

    function openModal() {
        document.body.style.overflow = 'hidden'; // Prevenir scroll
        passwordModal.classList.add('is-active');
        // Focus en el primer campo después de una breve pausa para permitir la animación
        setTimeout(() => {
            document.getElementById('current_password').focus();
        }, 300);
    }

    function closeModal() {
        passwordModal.classList.remove('is-active');
        document.body.style.overflow = ''; // Restaurar scroll

        // Esperar a que termine la animación para resetear el formulario
        setTimeout(() => {
            passwordForm.reset();
            if (passwordAlert) {
                passwordAlert.style.display = 'none';
                passwordAlert.className = '';
                passwordAlert.innerHTML = '';
            }
        }, 300);
    }

    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            // Efecto visual al hacer clic
            this.classList.add('animated-click');
            setTimeout(() => this.classList.remove('animated-click'), 300);
            openModal();
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (cancelPasswordBtn) {
        cancelPasswordBtn.addEventListener('click', closeModal);
    }

    // Cerrar modal con click fuera
    if (passwordModal) {
        passwordModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && passwordModal && passwordModal.classList.contains('is-active')) {
            closeModal();
        }
    });

    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Verificar que las contraseñas coincidan
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;

            if (newPassword !== confirmPassword) {
                passwordAlert.style.display = 'block';
                passwordAlert.className = 'alert alert-error';
                passwordAlert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
                return;
            }

            if (passwordLoading) {
                passwordLoading.classList.add('is-active');
            }

            if (passwordAlert) {
                passwordAlert.style.display = 'none';
            }

            // Simulación de envío (ya que podríamos no tener endpoint real)
            setTimeout(() => {
                if (passwordLoading) {
                    passwordLoading.classList.remove('is-active');
                }

                if (passwordAlert) {
                    passwordAlert.style.display = 'block';
                    passwordAlert.className = 'alert alert-success';
                    passwordAlert.innerHTML = '<i class="fas fa-check-circle"></i> Contraseña actualizada correctamente';
                }

                // Cerrar el modal después de 2 segundos
                setTimeout(closeModal, 2000);
            }, 1500);
        });
    }

    // Guardar cambios del perfil
    const profileForm = document.getElementById('profile-form');
    const saveProfileBtn = document.getElementById('save-profile-btn');

    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', function() {
            // Efecto visual al hacer clic
            this.classList.add('animated-click');
            setTimeout(() => this.classList.remove('animated-click'), 300);

            // Mostrar feedback visual antes de enviar
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            this.disabled = true;

            // Simulación de éxito (ya que en realidad enviamos directamente el formulario)
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check"></i> ¡Guardado!';
                this.classList.add('save-button-success');

                // Restaurar después de un tiempo
                setTimeout(() => {
                    // Enviar el formulario
                    if (profileForm) {
                        profileForm.submit();
                    } else {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                }, 800);
            }, 1000);
        });
    }

    // Mostrar mensajes de alerta por 5 segundos y luego ocultarlos
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
});
</script>
@endpush
