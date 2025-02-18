@extends('layouts.app')

@section('title', 'Registrarse - CrítiFlix')

@section('content')
<main>
  <section class="register-page">
    <h2>Registrarse</h2>
    <form id="registerForm">
      @csrf
      <input type="text" id="name" placeholder="Nombre" required>
      <input type="email" id="email" placeholder="Correo" required>
      <input type="password" id="password" placeholder="Contraseña" required>
      <input type="password" id="password_confirmation" placeholder="Repetir Contraseña" required>
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
