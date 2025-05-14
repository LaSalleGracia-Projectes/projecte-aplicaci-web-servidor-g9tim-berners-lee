<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Test de Idioma - CritFlix</title>
    <style>
        :root {
            --negro: #0a0a0a;
            --blanco: #ffffff;
            --gris-oscuro: #222222;
            --gris-medio: #666666;
            --verde-neon: #00ff87;
            --verde-claro: #7affbe;
            --verde-principal: #00cc6a;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--negro);
            color: var(--blanco);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }

        h1, h2, h3 {
            color: var(--verde-neon);
        }

        .card {
            background-color: var(--gris-oscuro);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn {
            display: inline-block;
            background-color: var(--gris-oscuro);
            color: var(--verde-neon);
            border: 1px solid var(--verde-neon);
            padding: 8px 16px;
            margin-right: 10px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover, .btn:focus {
            background-color: var(--verde-neon);
            color: var(--negro);
        }

        .btn.active {
            background-color: var(--verde-neon);
            color: var(--negro);
            font-weight: bold;
        }

        pre {
            background-color: var(--negro);
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .test-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            background-color: rgba(0, 255, 135, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test de Idioma - CritFlix</h1>

        <div class="card">
            <h2>Idioma actual: {{ app()->getLocale() }}</h2>
            <p>Esta es una página de prueba para verificar el funcionamiento del sistema de idiomas.</p>

            <div style="margin: 20px 0;">
                <a href="{{ route('language.change', ['locale' => 'es']) }}" class="btn {{ app()->getLocale() == 'es' ? 'active' : '' }}">Español</a>
                <a href="{{ route('language.change', ['locale' => 'ca']) }}" class="btn {{ app()->getLocale() == 'ca' ? 'active' : '' }}">Català</a>
                <a href="{{ route('language.change', ['locale' => 'en']) }}" class="btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a>
            </div>
        </div>

        <div class="card">
            <h3>Textos traducidos</h3>
            <div class="test-item">
                <strong>Home/Inicio:</strong> {{ __('messages.home') }}
            </div>
            <div class="test-item">
                <strong>Movies/Películas:</strong> {{ __('messages.movies') }}
            </div>
            <div class="test-item">
                <strong>Series/Series:</strong> {{ __('messages.series') }}
            </div>
            <div class="test-item">
                <strong>Critics/Críticos:</strong> {{ __('messages.critics') }}
            </div>
            <div class="test-item">
                <strong>Trends/Tendencias:</strong> {{ __('messages.trends') }}
            </div>
            <div class="test-item">
                <strong>Language/Idioma:</strong> {{ __('messages.language') }}
            </div>
            <div class="test-item">
                <strong>Login/Iniciar sesión:</strong> {{ __('messages.login') }}
            </div>
            <div class="test-item">
                <strong>Register/Registrarse:</strong> {{ __('messages.register') }}
            </div>
        </div>

        <div class="card">
            <h3>Información de depuración</h3>
            <ul>
                <li>Idioma de la aplicación: <strong>{{ app()->getLocale() }}</strong></li>
                <li>Idioma en sesión: <strong>{{ session('locale', 'no hay') }}</strong></li>
                <li>Idioma en cookie: <strong>{{ isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'no hay' }}</strong></li>
                <li>URL Actual: <strong>{{ url()->current() }}</strong></li>
            </ul>
        </div>

        <div class="card">
            <a href="{{ route('home') }}" class="btn">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
