<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Diagnóstico de Idioma - CritFlix</title>
    <style>
        :root {
            --negro: #0a0a0a;
            --blanco: #ffffff;
            --gris-oscuro: #222222;
            --gris-medio: #666666;
            --verde-neon: #00ff87;
            --verde-claro: #7affbe;
            --verde-principal: #00cc6a;
            --rojo: #ff3d3d;
            --amarillo: #ffcc00;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--negro);
            color: var(--blanco);
            line-height: 1.6;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1, h2, h3, h4 {
            color: var(--verde-neon);
        }

        .box {
            background-color: var(--gris-oscuro);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid var(--gris-medio);
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: rgba(0, 255, 135, 0.1);
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

        .active {
            background-color: var(--verde-neon);
            color: var(--negro);
            font-weight: bold;
        }

        code {
            background-color: var(--negro);
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }

        .status-ok {
            color: var(--verde-neon);
        }

        .status-warning {
            color: var(--amarillo);
        }

        .status-error {
            color: var(--rojo);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico del Sistema de Traducción</h1>

        <div class="box">
            <h2>Información General</h2>
            <table>
                <tr>
                    <th>Idioma actual (App::getLocale)</th>
                    <td>{{ $locale }}</td>
                </tr>
                <tr>
                    <th>Idioma en sesión</th>
                    <td>{{ $sessionLocale }}</td>
                </tr>
                <tr>
                    <th>Idioma en cookie</th>
                    <td>{{ $cookieLocale }}</td>
                </tr>
            </table>

            <h3>Configuración de Laravel</h3>
            <table>
                @foreach($appConfig as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <div>
                <h3>Prueba de cambio de idioma</h3>
                <a href="{{ route('language.change', ['locale' => 'es']) }}" class="btn {{ $locale == 'es' ? 'active' : '' }}">Español</a>
                <a href="{{ route('language.change', ['locale' => 'ca']) }}" class="btn {{ $locale == 'ca' ? 'active' : '' }}">Català</a>
                <a href="{{ route('language.change', ['locale' => 'en']) }}" class="btn {{ $locale == 'en' ? 'active' : '' }}">English</a>
            </div>
        </div>

        <div class="box">
            <h2>Prueba de Traducciones en Vivo</h2>

            <h3>Helper __() </h3>
            <table>
                @foreach($liveTests['helper'] as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <h3>Helper trans()</h3>
            <table>
                @foreach($liveTests['trans'] as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="box">
            <h2>Archivos de Traducción</h2>

            @foreach($translations as $lang => $data)
            <h3>Idioma: {{ $lang }}</h3>
            <div>Directorio: <code>{{ $data['directory'] }}</code></div>

            @foreach($data['files'] as $file)
            <div class="box">
                <h4>{{ $file['name'] }}</h4>
                <table>
                    <tr>
                        <th>Clave</th>
                        <th>Valor</th>
                    </tr>
                    @foreach($file['examples'] as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endforeach
            @endforeach
        </div>

        <div class="box">
            <a href="{{ route('home') }}" class="btn">Volver al inicio</a>
            <a href="{{ route('test.direct') }}" class="btn">Ver Test Directo</a>
        </div>
    </div>
</body>
</html>
