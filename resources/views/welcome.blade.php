<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CritFlix - Bienvenido</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

            <style>
            :root {
                --verde-neon: #39ff14;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background-color: #121212;
                color: #fff;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            h1 {
                color: var(--verde-neon);
                text-align: center;
                margin-bottom: 30px;
            }

            .card {
                background: rgba(0, 0, 0, 0.4);
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 30px;
            }

            .btn {
                display: inline-block;
                margin: 5px;
                padding: 8px 15px;
                background: transparent;
                border: 1px solid var(--verde-neon);
                color: var(--verde-neon);
                cursor: pointer;
                border-radius: 4px;
                text-decoration: none;
                font-size: 16px;
            }

            .btn-active {
                background: var(--verde-neon);
                color: #000;
            }

            a {
                color: var(--verde-neon);
                text-decoration: none;
            }

            ul {
                list-style: none;
                padding: 0;
            }

            li {
                margin-bottom: 10px;
            }

            .success-message {
                background: rgba(57, 255, 20, 0.2);
                border: 1px solid var(--verde-neon);
                color: var(--verde-neon);
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 15px;
            }
            </style>
    </head>
    <body>
        <div class="container" style="margin-top: 100px;">
            <h1>{{ __('messages.Bienvenido a CritFlix') }}</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                        </div>
                        @endif

            <div class="card" style="text-align: center;">
                <h2>{{ __('messages.Idioma') }}: <strong>{{ App::getLocale() }}</strong></h2>

                <div style="margin-top: 20px;">
                    <!-- Usando enlaces directos en lugar de JavaScript -->
                    <a href="{{ route('language.change', 'es') }}" class="btn {{ App::getLocale() == 'es' ? 'btn-active' : '' }}">Español</a>
                    <a href="{{ route('language.change', 'ca') }}" class="btn {{ App::getLocale() == 'ca' ? 'btn-active' : '' }}">Català</a>
                    <a href="{{ route('language.change', 'en') }}" class="btn {{ App::getLocale() == 'en' ? 'btn-active' : '' }}">English</a>
                                        </div>

                <div style="margin-top: 10px; font-size: 0.8em; color: #888;">Versión: 4.0 - Enlaces directos sin JavaScript</div>

                <!-- Información de diagnóstico -->
                <div style="margin-top: 15px; text-align: left; background: #111; padding: 10px; border-radius: 5px;">
                    <p><strong>Idioma actual:</strong> {{ App::getLocale() }}</p>
                    <p><strong>Idioma en sesión:</strong> {{ session('locale', 'no establecido') }}</p>
                    <p><strong>Idioma en cookie:</strong> {{ isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'no establecido' }}</p>
                                        </div>
                                    </div>

            <div class="card">
                <h3 style="color: var(--verde-neon);">{{ __('messages.Navegación') }}</h3>
                <ul>
                    <li><strong>{{ __('messages.Inicio') }}</strong></li>
                    <li><strong>{{ __('messages.Películas') }}</strong></li>
                    <li><strong>{{ __('messages.Series') }}</strong></li>
                    <li><strong>{{ __('messages.Críticos') }}</strong></li>
                    <li><strong>{{ __('messages.Tendencias') }}</strong></li>
                </ul>
                                </div>

            <div class="card">
                <h3 style="color: var(--verde-neon);">{{ __('messages.Categorías') }}</h3>
                <ul>
                    <li><strong>{{ __('messages.Drama') }}</strong></li>
                    <li><strong>{{ __('messages.Terror') }}</strong></li>
                    <li><strong>{{ __('messages.Comedia') }}</strong></li>
                    <li><strong>{{ __('messages.Ciencia Ficción') }}</strong></li>
                    <li><strong>{{ __('messages.Romance') }}</strong></li>
                </ul>
                                </div>

            <div class="card">
                <a href="{{ route('test.language') }}" class="btn">Ir a página de prueba de idioma</a>
            </div>
        </div>
    </body>
</html>
