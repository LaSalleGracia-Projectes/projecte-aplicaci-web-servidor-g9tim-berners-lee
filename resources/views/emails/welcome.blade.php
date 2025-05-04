<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a CritFlix</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .logo-container {
            background-color: #151515;
            text-align: center;
            padding: 20px 0;
        }
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #00ff66;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(0, 255, 102, 0.5);
        }
        .logo span {
            color: #fff;
        }
        .header {
            background: linear-gradient(135deg, #151515 0%, #252525 100%);
            color: #00ff66;
            padding: 30px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border-bottom: 3px solid #00ff66;
        }
        .greeting {
            background-color: #00ff66;
            color: #151515;
            margin: 0;
            padding: 15px 30px;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
            color: #444;
            background: #fff;
        }
        .feature-list {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px 20px 20px 40px;
            margin: 20px 0;
        }
        .feature-list li {
            margin-bottom: 12px;
            position: relative;
        }
        .feature-list li:before {
            content: "✓";
            color: #00ff66;
            font-weight: bold;
            position: absolute;
            left: -25px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(to right, #00ff66, #00cc52);
            color: #151515;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 255, 102, 0.3);
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 255, 102, 0.4);
        }
        .footer {
            background-color: #151515;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #aaa;
        }
        .social-icons {
            margin: 15px 0;
        }
        .social-icons a {
            display: inline-block;
            margin: 0 8px;
            color: #00ff66;
            font-size: 20px;
            text-decoration: none;
        }
        .divider {
            height: 3px;
            background: linear-gradient(to right, transparent, #00ff66, transparent);
            margin: 20px 0;
        }
        .signature {
            font-style: italic;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">Crit<span>Flix</span></div>
        </div>

        <div class="header">
            ¡Bienvenido a la comunidad cinematográfica!
        </div>

        <h2 class="greeting">Hola {{ $name }},</h2>

        <div class="content">
            <p>¡Gracias por registrarte en <strong>CritFlix</strong>! Estamos emocionados de tenerte como parte de nuestra comunidad de cine y series.</p>

            <p>Con tu nueva cuenta ahora puedes disfrutar de todas nuestras funciones exclusivas:</p>

            <ul class="feature-list">
                <li><strong>Descubre</strong> nuevas películas y series basadas en tus gustos personales</li>
                <li><strong>Valora y comparte</strong> tus opiniones sobre tus contenidos favoritos</li>
                <li><strong>Crea listas personalizadas</strong> para organizar lo que quieres ver</li>
                <li><strong>Conecta</strong> con otros aficionados al cine y las series</li>
            </ul>

            <p>Tu cuenta ya está <strong>completamente activa</strong> y puedes comenzar a explorar todo lo que CritFlix tiene para ofrecer.</p>

            <div class="button-container">
                <a href="http://127.0.0.1:8000" class="button">Comenzar ahora</a>
            </div>

            <div class="divider"></div>

            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos respondiendo a este correo.</p>

            <p class="signature">¡Disfruta de la experiencia CritFlix!<br>
            <strong>El equipo de CritFlix</strong></p>
        </div>

        <div class="footer">
            <div class="social-icons">
                <!-- Iconos de redes sociales en texto -->
                <a href="#" style="color: #00ff66; text-decoration: none; margin: 0 10px;">Twitter</a>
                <a href="#" style="color: #00ff66; text-decoration: none; margin: 0 10px;">Facebook</a>
                <a href="#" style="color: #00ff66; text-decoration: none; margin: 0 10px;">Instagram</a>
            </div>
            <p>© 2025 CritFlix. Todos los derechos reservados.</p>
            <p>Este correo fue enviado a {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>
