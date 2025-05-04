<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Idioma - CritFlix</title>
    <style>
        :root {
            --verde-neon: #39ff14;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 20px;
        }

        h1, h2, h3 {
            color: var(--verde-neon);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #222;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn {
            background-color: transparent;
            color: var(--verde-neon);
            border: 1px solid var(--verde-neon);
            padding: 8px 16px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn.active {
            background-color: var(--verde-neon);
            color: #000;
        }

        pre {
            background-color: #333;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
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
                <button id="btn-es" onclick="changeLanguage('es')" class="btn {{ app()->getLocale() == 'es' ? 'active' : '' }}">Español</button>
                <button id="btn-ca" onclick="changeLanguage('ca')" class="btn {{ app()->getLocale() == 'ca' ? 'active' : '' }}">Català</button>
                <button id="btn-en" onclick="changeLanguage('en')" class="btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</button>
            </div>

            <div id="status" style="margin-top: 10px;"></div>
        </div>

        <div class="card">
            <h3>Información de depuración</h3>
            <ul>
                <li>Idioma de la aplicación: <strong>{{ app()->getLocale() }}</strong></li>
                <li>Idioma en sesión: <strong>{{ session('locale', 'no hay') }}</strong></li>
                <li>Idioma en cookie: <strong>{{ isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'no hay' }}</strong></li>
            </ul>

            <h3>Cookies actuales:</h3>
            <pre id="cookie-debug">{{ print_r($_COOKIE, true) }}</pre>
        </div>
    </div>

    <script>
        function changeLanguage(locale) {
            const status = document.getElementById('status');
            status.textContent = `Cambiando idioma a ${locale}...`;

            // Establece la cookie directamente
            document.cookie = `locale=${locale}; max-age=31536000; path=/; SameSite=Lax`;

            // Actualiza el debug de cookies
            document.getElementById('cookie-debug').textContent = document.cookie;

            // Hace la petición al servidor
            fetch(`/change-language/${locale}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    status.textContent = 'Idioma cambiado correctamente. Recargando...';

                    // Recarga la página con un parámetro para evitar caché
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?t=' + Date.now();
                    }, 1000);
                } else {
                    throw new Error('Error al cambiar el idioma');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                status.textContent = 'Error al cambiar el idioma. Intentando método alternativo...';

                // Método alternativo
                fetch(`/language/${locale}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.ok) {
                        status.textContent = 'Idioma cambiado correctamente (método alternativo). Recargando...';

                        // Recarga la página con un parámetro para evitar caché
                        setTimeout(() => {
                            window.location.href = window.location.pathname + '?t=' + Date.now();
                        }, 1000);
                    } else {
                        throw new Error('Error al cambiar el idioma (método alternativo)');
                    }
                })
                .catch(err => {
                    console.error('Error (método alternativo):', err);
                    status.textContent = 'Error al cambiar el idioma. Por favor, recarga la página manualmente.';
                });
            });
        }

        // Muestra el estado de las cookies al cargar la página
        window.addEventListener('load', function() {
            document.getElementById('cookie-debug').textContent = document.cookie;
        });
    </script>
</body>
</html>
