<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ route('home') }}">
                <span class="logo-text">CritFlix</span>
                <span class="logo-dot"></span>
            </a>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="{{ route('home') }}" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="{{ route('criticos') }}" class="nav-link">Críticos</a></li>
                <li class="nav-item"><a href="{{ route('peliculas') }}" class="nav-link">Películas</a></li>
                <li class="nav-item"><a href="{{ route('series') }}" class="nav-link">Series</a></li>
                <li class="nav-item"><a href="{{ route('tendencias') }}" class="nav-link">Tendencias</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search" placeholder="Buscar películas, series...">
                    <div id="suggestions" class="search-suggestions"></div>
                </div>
            </div>

            <div class="auth-buttons">
                <button id="loginLink" class="auth-btn login-btn">
                    <i class="fas fa-user"></i>
                    <span>Iniciar Sesión</span>
                </button>
                <button id="registerLink" class="auth-btn register-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Registrarse</span>
                </button>
                <button id="logoutButton" class="auth-btn logout-btn hidden">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </div>
            <div class="profile-buttons">
                <button id="profileButton" class="auth-btn profile-btn hidden">
                    <i class="fas fa-user"></i>
                    <span>Perfil</span>
                </button>
            </div>
        </div>
    </div>
</header>
