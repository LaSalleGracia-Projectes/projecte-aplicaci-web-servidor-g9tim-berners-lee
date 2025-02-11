@extends('layouts.app')

@section('title', 'Iniciar Sesión - CrítiFlix')

@section('content')
<main>
  <section class="login-page">
    <h2>Iniciar Sesión</h2>
    <form method="POST" action="{{ route('login') }}">
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
  </section>
</main>
@endsection
