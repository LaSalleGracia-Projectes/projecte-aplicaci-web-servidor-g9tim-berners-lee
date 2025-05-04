<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - CritFlix')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fuentes y librerías externas -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos base -->
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/main.css') }}" rel="stylesheet">

    <!-- Estilos adicionales -->
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h1>CritFlix <span>Admin</span></h1>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> <span>Usuarios</span>
                </a>
                <a href="{{ route('admin.movies') }}" class="admin-nav-item {{ request()->routeIs('admin.movies') ? 'active' : '' }}">
                    <i class="fas fa-film"></i> <span>Películas</span>
                </a>
                <a href="{{ route('admin.reviews') }}" class="admin-nav-item {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> <span>Valoraciones</span>
                </a>
                <a href="{{ route('admin.comments') }}" class="admin-nav-item {{ request()->routeIs('admin.comments') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> <span>Comentarios</span>
                </a>
                <div class="admin-nav-divider"></div>
                <a href="{{ route('admin.profile') }}" class="admin-nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> <span>Mi Perfil</span>
                </a>
                <a href="#" class="admin-nav-item" id="admin-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
                </a>
            </nav>
        </aside>

        <!-- Contenido Principal -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <!-- Botón de menú para móvil -->
                <button type="button" class="menu-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="admin-header-title">
                    <h2>@yield('header-title', 'Panel de Administración')</h2>
                </div>
                <div class="admin-header-actions">
                    <div class="admin-search">
                        <input type="text" placeholder="Buscar..." class="form-control">
                        <button type="button"><i class="fas fa-search"></i></button>
                    </div>
                    <button type="button" class="dark-mode-toggle" id="darkModeToggle" title="Cambiar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="admin-notifications">
                        <button type="button">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>
                    <div class="admin-user">
                        <span>{{ Auth::user()->name }}</span>
                        <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ Auth::user()->name }}" class="admin-avatar">
                    </div>
                </div>
            </header>

            <!-- Contenido -->
            <div class="admin-content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Contenedor de notificaciones toast -->
    <div class="toast-container"></div>

    <!-- Formulario de logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
