<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a CritFlix!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1a1a1a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #e50914;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a CritFlix!</h1>
        </div>

        <div class="content">
            <h2>¡Hola {{ $name }}!</h2>
            <p>Gracias por registrarte en CritFlix. Estamos emocionados de tenerte como parte de nuestra comunidad.</p>
            <p>Con tu cuenta, podrás:</p>
            <ul>
                <li>Explorar nuestra extensa colección de películas</li>
                <li>Guardar tus películas favoritas</li>
                <li>Recibir recomendaciones personalizadas</li>
                <li>Compartir tus opiniones con otros usuarios</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="button">Comenzar a explorar</a>
            </div>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no responder.</p>
            <p>&copy; {{ date('Y') }} CritFlix. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
