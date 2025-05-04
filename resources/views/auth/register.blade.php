@extends('layouts.app')

@section('title', 'Registrarse - CritFlix')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h2>Crear Cuenta</h2>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="social-login">
            <a href="{{ route('auth.google') }}" class="google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google Logo">
                Registrarse con Google
            </a>
        </div>

        <div class="separator">
            <span>o</span>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Registrarse</button>
            </div>

            <div class="auth-links">
                <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar Sesión</a></p>
            </div>
        </form>
    </div>
</div>

<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 20px;
}

.auth-card {
    background-color: #111;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 255, 0, 0.2);
    padding: 30px;
    width: 100%;
    max-width: 400px;
}

.auth-card h2 {
    color: #00ff00;
    text-align: center;
    margin-bottom: 20px;
}

.auth-form .form-group {
    margin-bottom: 20px;
}

.auth-form label {
    display: block;
    margin-bottom: 5px;
    color: #ccc;
}

.auth-form .form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #333;
    border-radius: 4px;
    background-color: #222;
    color: #fff;
}

.auth-form .remember-me {
    display: flex;
    align-items: center;
}

.auth-form .remember-me input {
    margin-right: 10px;
}

.auth-form .submit-btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 4px;
    background-color: #00ff00;
    color: #000;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.auth-form .submit-btn:hover {
    background-color: #00cc00;
}

.auth-links {
    text-align: center;
    margin-top: 20px;
}

.auth-links a {
    color: #00ff00;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}

.error-message {
    color: #ff4d4d;
    font-size: 0.9em;
    margin-top: 5px;
    display: block;
}

.alert {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background-color: rgba(255, 0, 0, 0.1);
    border: 1px solid rgba(255, 0, 0, 0.3);
    color: #ff4d4d;
}

/* Estilos para autenticación con Google */
.social-login {
    margin-bottom: 25px;
    width: 100%;
}

.google-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 10px 0;
    background-color: #fff;
    color: #757575;
    border-radius: 4px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    text-decoration: none;
    transition: all 0.3s ease;
}

.google-btn:hover {
    background-color: #f5f5f5;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.google-btn img {
    width: 18px;
    height: 18px;
    margin-right: 10px;
}

.separator {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 20px 0;
    color: #aaa;
}

.separator::before,
.separator::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #333;
}

.separator span {
    padding: 0 10px;
}
</style>
@endsection
