<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta en CritFlix</title>
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
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a CritFlix!</h1>
        </div>

        <div class="content">
            <h2>Verifica tu cuenta</h2>
            <p>Hola {{ $user->name }},</p>
            <p>Gracias por registrarte en CritFlix. Para comenzar a usar nuestra plataforma, por favor verifica tu dirección de correo electrónico haciendo clic en el botón de abajo:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verificar mi cuenta</a>
            </div>

            <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
            <p style="word-break: break-all;">{{ $verificationUrl }}</p>

            <p>Este enlace expirará en 60 minutos.</p>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no responder.</p>
            <p>&copy; {{ date('Y') }} CritFlix. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
