@extends('layouts.app')

@section('title', 'Registrarse - CrítiFlix')

@section('content')
<main>
  <section class="register-page">
    <h2>Registrarse</h2>
    <form method="POST" action="{{ route('register') }}">
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
  </section>
</main>
@endsection
