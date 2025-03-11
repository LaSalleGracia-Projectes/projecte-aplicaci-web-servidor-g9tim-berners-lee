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
            </div>
        </div>
    </div>
</header>
<!-- Modal de Login -->
<div id="loginModal" class="modal">
  <div class="modal-content">
    <span id="closeLogin" class="close">&times;</span>
    <div class="modal-header">
      <h2>Iniciar Sesión</h2>
    </div>
    <div class="modal-body">
      <form id="loginForm" action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="correo" placeholder="Correo o Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <div class="recordarme">
          <input type="checkbox" id="recordar" name="recordar">
          <label for="recordar">Recordarme</label>
        </div>
        <button type="submit">Iniciar Sesión</button>
      </form>
    </div>
    <div class="modal-footer">
      <a href="#">¿Olvidaste tu contraseña?</a>
    </div>
  </div>
</div>

<!-- Modal de Registro -->
<div id="registerModal" class="modal">
  <div class="modal-content">
    <span id="closeRegister" class="close">&times;</span>
    <div class="modal-header">
      <h2>Registrarse</h2>
    </div>
    <div class="modal-body">
      <form id="registerForm" action="{{ route('register') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
        <button type="submit">Registrarse</button>
      </form>
    </div>
    <div class="modal-footer">
      <span>¿Ya tienes una cuenta? <a href="#" id="loginFromRegister">Inicia Sesión</a></span>
    </div>
  </div>
</div>

