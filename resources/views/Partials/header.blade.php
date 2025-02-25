<header>
  <div class="logo">
    <a href="{{ route('home') }}">CrítiFlix</a>
  </div>
  <nav>
    <ul>
      <li><a href="{{ route('home') }}">Inicio</a></li>
      <li><a href="{{ route('criticos') }}">Críticos</a></li>
      <li><a href="{{ route('peliculas') }}">Películas</a></li>
      <li><a href="{{ route('series') }}">Series</a></li>
      <li><a href="{{ route('tendencias') }}">Tendencias</a></li>
      <li class="search">
        <input type="text" id="search" placeholder="Buscar...">
        <div id="suggestions"></div>
      </li>
        <li><a href="#" id="loginLink">Iniciar Sesión</a></li>
        <li><a href="#" id="registerLink">Registrarse</a></li>
        <li><button id="logoutButton">Cerrar Sesión</button></li>

    </ul>
  </nav>
</header>
