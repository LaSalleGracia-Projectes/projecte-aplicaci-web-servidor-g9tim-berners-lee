<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Test Directo - CritFlix</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            background: #111;
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #222;
            border-radius: 8px;
        }
        h1 {
            color: #00ff87;
        }
        .test-box {
            margin: 10px 0;
            padding: 10px;
            background: rgba(0, 255, 135, 0.1);
            border-radius: 4px;
        }
        a {
            display: inline-block;
            margin: 5px;
            padding: 8px 16px;
            background: transparent;
            color: #00ff87;
            border: 1px solid #00ff87;
            border-radius: 4px;
            text-decoration: none;
        }
        a:hover {
            background: #00ff87;
            color: #111;
        }
        .active {
            background: #00ff87;
            color: #111;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Directo de Idioma</h1>

        <p>Idioma actual: <strong>{{ app()->getLocale() }}</strong></p>
        <p>Sesión locale: <strong>{{ session('locale', 'no hay') }}</strong></p>
        <p>Cookie locale: <strong>{{ isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'no hay' }}</strong></p>

        <div>
            <a href="{{ route('language.change', ['locale' => 'es']) }}" class="{{ app()->getLocale() == 'es' ? 'active' : '' }}">Español</a>
            <a href="{{ route('language.change', ['locale' => 'ca']) }}" class="{{ app()->getLocale() == 'ca' ? 'active' : '' }}">Català</a>
            <a href="{{ route('language.change', ['locale' => 'en']) }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a>
        </div>

        <div class="test-box">
            <h3>Textos traducidos (messages.php)</h3>
            <p><strong>Home:</strong> {{ __('messages.home') }}</p>
            <p><strong>Movies:</strong> {{ __('messages.movies') }}</p>
            <p><strong>Series:</strong> {{ __('messages.series') }}</p>
            <p><strong>Login:</strong> {{ __('messages.login') }}</p>
            <p><strong>Register:</strong> {{ __('messages.register') }}</p>
        </div>

        <div class="test-box">
            <h3>Textos traducidos (directos)</h3>
            <p><strong>Translated via trans() helper:</strong> {{ trans('messages.home') }}</p>
        </div>

        <br>
        <a href="{{ route('home') }}">Volver al inicio</a>
    </div>
</body>
</html>
