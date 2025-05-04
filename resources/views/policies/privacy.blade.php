@extends('layouts.app')

@section('title', 'Política de Privacidad - CritFlix')

@section('content')
<div class="policy-container">
    <header class="policy-header">
        <div class="policy-header-content">
            <h1>Política de Privacidad</h1>
            <p class="policy-meta">Última actualización: {{ date('d/m/Y') }}</p>
        </div>
    </header>

    <div class="policy-content">
        <div class="policy-section">
            <h2>1. Introducción</h2>
            <p>En CritFlix, nos comprometemos a proteger tu privacidad y a ser transparentes sobre cómo recopilamos, utilizamos y compartimos tu información. Esta Política de Privacidad describe nuestras prácticas de recopilación, uso y divulgación de información cuando utilizas nuestro servicio de críticas de películas y series.</p>
            <p>Al utilizar CritFlix, aceptas las prácticas descritas en esta política. Si no estás de acuerdo con nuestra política, por favor no utilices nuestros servicios.</p>
        </div>

        <div class="policy-section">
            <h2>2. Información que recopilamos</h2>

            <h3>2.1 Información que nos proporcionas</h3>
            <p>Recopilamos la información que nos proporcionas directamente cuando:</p>
            <ul>
                <li>Creas o modificas tu cuenta (nombre, correo electrónico, contraseña)</li>
                <li>Completas tu perfil (foto, biografía, preferencias)</li>
                <li>Publicas críticas, comentarios o valoraciones</li>
                <li>Creas listas de películas o series</li>
                <li>Te comunicas con nuestro equipo de soporte</li>
            </ul>

            <h3>2.2 Información recopilada automáticamente</h3>
            <p>Cuando interactúas con nuestros servicios, recopilamos automáticamente cierta información, incluyendo:</p>
            <ul>
                <li>Información del dispositivo (tipo de dispositivo, sistema operativo, navegador)</li>
                <li>Registros de uso (páginas visitadas, tiempo de visita, funciones utilizadas)</li>
                <li>Dirección IP y ubicación aproximada</li>
                <li>Información de cookies y tecnologías similares</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Cómo utilizamos tu información</h2>
            <p>Utilizamos la información recopilada para:</p>
            <ul>
                <li>Proporcionar, mantener y mejorar nuestros servicios</li>
                <li>Personalizar tu experiencia en CritFlix</li>
                <li>Procesar y completar transacciones</li>
                <li>Enviar comunicaciones relacionadas con el servicio</li>
                <li>Responder a tus comentarios y preguntas</li>
                <li>Entender cómo los usuarios utilizan nuestros servicios</li>
                <li>Detectar, prevenir y abordar problemas técnicos o de seguridad</li>
                <li>Cumplir con nuestras obligaciones legales</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Compartición de información</h2>
            <p>Podemos compartir tu información en las siguientes circunstancias:</p>

            <h3>4.1 Con tu consentimiento</h3>
            <p>Compartiremos tu información personal cuando nos hayas dado tu consentimiento para hacerlo, como cuando eliges hacer público tu perfil o tus críticas.</p>

            <h3>4.2 Con proveedores de servicios</h3>
            <p>Trabajamos con terceros que nos ayudan a proporcionar y mejorar nuestros servicios (como servidores en la nube, procesamiento de pagos, análisis de datos).</p>

            <h3>4.3 Por razones legales</h3>
            <p>Podemos compartir información si creemos de buena fe que la divulgación es necesaria para:</p>
            <ul>
                <li>Cumplir con una obligación legal</li>
                <li>Proteger los derechos, la propiedad o la seguridad de CritFlix, nuestros usuarios o el público</li>
                <li>Investigar y prevenir actividades fraudulentas o ilegales</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Tus derechos y opciones</h2>
            <p>Dependiendo de tu ubicación, puedes tener ciertos derechos relacionados con tu información personal:</p>
            <ul>
                <li>Acceder a la información que tenemos sobre ti</li>
                <li>Corregir información inexacta o incompleta</li>
                <li>Eliminar tu información personal</li>
                <li>Oponerte al procesamiento de tu información</li>
                <li>Retirar tu consentimiento en cualquier momento</li>
                <li>Presentar una queja ante una autoridad de protección de datos</li>
            </ul>
            <p>Para ejercer estos derechos, ponte en contacto con nosotros a través de <a href="mailto:critflixteam@gmail.com">critflixteam@gmail.com</a>.</p>
        </div>

        <div class="policy-section">
            <h2>6. Seguridad de datos</h2>
            <p>Implementamos medidas de seguridad técnicas y organizativas diseñadas para proteger tu información personal contra el acceso, la divulgación, el uso y la modificación no autorizados. Sin embargo, ningún sistema es completamente seguro, y no podemos garantizar la seguridad absoluta de tu información.</p>
        </div>

        <div class="policy-section">
            <h2>7. Retención de datos</h2>
            <p>Conservamos tu información durante el tiempo necesario para proporcionar los servicios que has solicitado, o según lo requiera la ley. Cuando ya no necesitamos usar tu información y no sea necesario conservarla para cumplir con nuestras obligaciones legales, la eliminaremos de nuestros sistemas o la anonimizaremos.</p>
        </div>

        <div class="policy-section">
            <h2>8. Menores</h2>
            <p>Nuestros servicios no están dirigidos a personas menores de 16 años, y no recopilamos a sabiendas información personal de niños menores de 16 años. Si eres padre o tutor y crees que tu hijo nos ha proporcionado información personal, contáctanos para que podamos tomar las medidas necesarias.</p>
        </div>

        <div class="policy-section">
            <h2>9. Cambios en esta política</h2>
            <p>Podemos actualizar esta Política de Privacidad de vez en cuando. La versión más reciente estará siempre disponible en nuestro sitio web con la fecha de "última actualización". Te recomendamos revisar periódicamente esta política para estar informado sobre cómo protegemos tu información.</p>
        </div>

        <div class="policy-section">
            <h2>10. Contacto</h2>
            <p>Si tienes preguntas o inquietudes sobre esta Política de Privacidad o nuestras prácticas de datos, contáctanos en:</p>
            <div class="contact-info">
                <p><strong>Email:</strong> <a href="mailto:critflixteam@gmail.com">critflixteam@gmail.com</a></p>
                <p><strong>Dirección:</strong> CritFlix S.L., Calle de la Cinematografía 123, 08001 Barcelona, España</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .policy-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        color: var(--text-light);
        font-family: 'Poppins', Arial, sans-serif;
    }

    .policy-header {
        background: linear-gradient(135deg, var(--verde-neon) 0%, var(--verde-principal) 100%);
        color: var(--negro);
        border-radius: 8px;
        margin: 30px 0;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 255, 60, 0.2);
    }

    .policy-header-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .policy-header h1 {
        font-size: 2.5rem;
        margin: 0;
        font-weight: 700;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .policy-meta {
        margin-top: 10px;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .policy-content {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        margin-bottom: 50px;
        border: 1px solid var(--border-color);
    }

    .policy-section {
        margin-bottom: 40px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 20px;
    }

    .policy-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .policy-section h2 {
        color: var(--verde-neon);
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .policy-section h3 {
        font-size: 1.3rem;
        margin: 30px 0 15px;
        color: var(--text-light);
        font-weight: 600;
    }

    .policy-section p, .policy-section li {
        line-height: 1.7;
        margin-bottom: 15px;
        color: var(--text-light);
    }

    .policy-section ul {
        padding-left: 20px;
        margin-bottom: 20px;
    }

    .policy-section ul li {
        margin-bottom: 10px;
    }

    .contact-info {
        background-color: var(--bg-hover);
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
        border: 1px solid var(--border-light);
    }

    .contact-info p {
        margin-bottom: 5px;
    }

    .policy-section a {
        color: var(--verde-neon);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .policy-section a:hover {
        color: var(--verde-principal);
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .policy-header {
            padding: 30px 20px;
            margin: 20px 0;
        }

        .policy-header h1 {
            font-size: 2rem;
        }

        .policy-content {
            padding: 25px 20px;
        }

        .policy-section h2 {
            font-size: 1.5rem;
        }

        .policy-section h3 {
            font-size: 1.2rem;
        }
    }
</style>
@endpush
