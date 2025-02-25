@extends('layouts.app')

@section('title', 'Iniciar Sesión - CrítiFlix')

@section('content')
<main>
  <section class="login-page">
    <h2>Iniciar Sesión</h2>
    <form id="loginForm">
      @csrf
      <input type="email" id="email" placeholder="Correo o Usuario" required>
      <input type="password" id="password" placeholder="Contraseña" required>
      <label><input type="checkbox" id="remember"> Recuérdame</label>
      <button type="submit" class="action-btn">Login</button>
      <p><a href="#">¿Has olvidado tu contraseña?</a></p>
    </form>
    <div class="social-login">
      <button class="social-btn google">Login con Google</button>
    </div>
  </section>
</main>

@endsection
