<!-- Modal de Login -->
<div id="loginModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeLogin">&times;</span>
    <h2>Iniciar Sesión</h2>
    <form id="loginForm" method="POST" action="{{ route('login') }}">
      @csrf
      <input type="text" name="correo" placeholder="Correo o Usuario" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <label><input type="checkbox" name="remember"> Recuérdame</label>
      <button type="submit" class="action-btn">Login</button>
      <p><a href="#">¿Has olvidado tu contraseña?</a></p>
    </form>
    <div class="social-login">
      <button class="social-btn google">Login con Google</button>
    </div>
  </div>
</div>

<!-- Modal de Registro -->
<div id="registerModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeRegister">&times;</span>
    <h2>Registrarse</h2>
    <form id="registerForm" method="POST" action="{{ route('register') }}">
      @csrf
      <input type="email" name="correo" placeholder="Correo" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <input type="password" name="contrasena_confirmation" placeholder="Repetir Contraseña" required>
      <button type="submit" class="action-btn">Registrarse</button>
    </form>
    <div class="social-login">
      <button class="social-btn google">Registrarse con Google</button>
      <button class="social-btn facebook">Registrarse con Facebook</button>
      <button class="social-btn twitter">Registrarse con Twitter</button>
    </div>
  </div>
</div>

<!-- Modal de Detalle de Película -->
<div id="movieDetailModal" class="modal">
  <div class="modal-content detail-modal">
    <span class="close" id="closeMovieDetail">&times;</span>
    <div id="movieDetailContent">
      {{-- Aquí se mostrará el detalle de la película/serie seleccionada --}}
    </div>
    <button id="moreDetailsBtn" class="action-btn hidden">Ver más detalles</button>
  </div>
</div>


<!-- Loading Spinner -->
<div id="loadingSpinner" class="spinner hidden">
  <div class="loader"></div>
</div>
