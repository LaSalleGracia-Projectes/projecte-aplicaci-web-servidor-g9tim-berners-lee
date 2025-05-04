@extends('layouts.app')

@section('title', 'Términos de Uso - CritFlix')

@section('content')
<div class="policy-container">
    <header class="policy-header">
        <div class="policy-header-content">
            <h1>Términos de Uso</h1>
            <p class="policy-meta">Última actualización: {{ date('d/m/Y') }}</p>
        </div>
    </header>

    <div class="policy-content">
        <div class="policy-section">
            <h2>1. Introducción</h2>
            <p>Bienvenido a CritFlix. Estos Términos de Uso ("Términos") rigen tu acceso y uso de nuestro sitio web, servicios, aplicaciones y herramientas (colectivamente, los "Servicios").</p>
            <p>Al acceder o utilizar nuestros Servicios, aceptas estar sujeto a estos Términos. Si no estás de acuerdo con estos Términos, no puedes acceder ni utilizar nuestros Servicios.</p>
        </div>

        <div class="policy-section">
            <h2>2. Uso de nuestros servicios</h2>

            <h3>2.1 Elegibilidad</h3>
            <p>Para utilizar nuestros Servicios, debes tener al menos 16 años. Si eres menor de 18 años, debes tener el consentimiento de un padre o tutor legal para utilizar nuestros Servicios.</p>

            <h3>2.2 Registro y cuenta</h3>
            <p>Cuando creas una cuenta en CritFlix, debes proporcionar información precisa y completa. Eres responsable de mantener la confidencialidad de tu contraseña y de todas las actividades que ocurran bajo tu cuenta. Nos reservamos el derecho de cerrar tu cuenta en cualquier momento y por cualquier motivo.</p>

            <h3>2.3 Conducta del usuario</h3>
            <p>Al utilizar nuestros Servicios, aceptas no:</p>
            <ul>
                <li>Violar cualquier ley aplicable o regulación</li>
                <li>Infringir los derechos de propiedad intelectual u otros derechos de terceros</li>
                <li>Publicar contenido falso, engañoso, difamatorio, obsceno, pornográfico o que incite al odio</li>
                <li>Acosar, intimidar o amenazar a otros usuarios</li>
                <li>Utilizar nuestros Servicios para enviar spam o mensajes no solicitados</li>
                <li>Interferir con el funcionamiento normal de nuestros Servicios</li>
                <li>Intentar acceder a áreas o características a las que no tienes autorización</li>
                <li>Utilizar robots, rastreadores o herramientas similares en nuestros Servicios</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Contenido del usuario</h2>

            <h3>3.1 Propiedad del contenido</h3>
            <p>Conservas todos los derechos sobre el contenido que publicas en CritFlix, incluidas tus críticas, comentarios, valoraciones y listas. Sin embargo, al publicar contenido, nos otorgas una licencia mundial, no exclusiva, libre de regalías, transferible y sublicenciable para usar, reproducir, modificar, adaptar, publicar, traducir, crear obras derivadas, distribuir y mostrar dicho contenido en cualquier medio o forma.</p>

            <h3>3.2 Responsabilidad por el contenido</h3>
            <p>Eres completamente responsable de todo el contenido que publicas en nuestros Servicios. No respaldamos ni garantizamos la veracidad, exactitud o fiabilidad de ningún contenido publicado por usuarios.</p>

            <h3>3.3 Moderación del contenido</h3>
            <p>Nos reservamos el derecho, pero no la obligación, de supervisar, eliminar o editar cualquier contenido que consideremos objetable o que viola estos Términos, a nuestra entera discreción y sin previo aviso.</p>
        </div>

        <div class="policy-section">
            <h2>4. Propiedad intelectual</h2>

            <h3>4.1 Nuestro contenido</h3>
            <p>El contenido proporcionado por CritFlix, incluyendo pero no limitado a texto, gráficos, logotipos, iconos, imágenes, clips de audio, descargas digitales y compilaciones de datos, es propiedad de CritFlix o de sus proveedores de contenido y está protegido por leyes de derechos de autor, marcas registradas y otras leyes.</p>

            <h3>4.2 Licencia limitada</h3>
            <p>Te otorgamos una licencia limitada, no exclusiva, no transferible y revocable para acceder y utilizar nuestros Servicios para tu uso personal y no comercial.</p>

            <h3>4.3 Restricciones</h3>
            <p>No puedes:</p>
            <ul>
                <li>Utilizar nuestros Servicios o contenido para fines comerciales sin nuestro consentimiento previo por escrito</li>
                <li>Copiar, reproducir, distribuir, modificar o crear obras derivadas de nuestro contenido</li>
                <li>Eliminar avisos de derechos de autor, marcas registradas u otros avisos de propiedad</li>
                <li>Utilizar técnicas de ingeniería inversa, descompilar o desensamblar cualquier parte de nuestros Servicios</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Enlaces a sitios de terceros</h2>
            <p>Nuestros Servicios pueden contener enlaces a sitios web o servicios de terceros que no son propiedad ni están controlados por CritFlix. No tenemos control sobre, y no asumimos responsabilidad por, el contenido, las políticas de privacidad o las prácticas de sitios de terceros. Al utilizar nuestros Servicios, reconoces y aceptas que no somos responsables de ningún daño o pérdida causada o presuntamente causada por o en conexión con el uso de cualquier contenido, bien o servicio disponible a través de cualquier sitio de terceros.</p>
        </div>

        <div class="policy-section">
            <h2>6. Descargo de responsabilidad</h2>
            <p>NUESTROS SERVICIOS SE PROPORCIONAN "TAL CUAL" Y "SEGÚN DISPONIBILIDAD", SIN GARANTÍAS DE NINGÚN TIPO, YA SEAN EXPRESAS O IMPLÍCITAS. EN LA MÁXIMA MEDIDA PERMITIDA POR LA LEY APLICABLE, CRITFLIX Y SUS AFILIADOS, DIRECTORES, EMPLEADOS, AGENTES, PROVEEDORES O LICENCIANTES RENUNCIAN A TODAS LAS GARANTÍAS, EXPRESAS O IMPLÍCITAS, INCLUYENDO, SIN LIMITACIÓN, GARANTÍAS IMPLÍCITAS DE COMERCIABILIDAD, IDONEIDAD PARA UN FIN PARTICULAR, NO INFRACCIÓN Y FUNCIONAMIENTO SIN ERRORES.</p>
        </div>

        <div class="policy-section">
            <h2>7. Limitación de responsabilidad</h2>
            <p>EN LA MÁXIMA MEDIDA PERMITIDA POR LA LEY APLICABLE, EN NINGÚN CASO CRITFLIX, SUS AFILIADOS, DIRECTORES, EMPLEADOS, AGENTES, PROVEEDORES O LICENCIANTES SERÁN RESPONSABLES POR CUALQUIER DAÑO INDIRECTO, PUNITIVO, INCIDENTAL, ESPECIAL, CONSECUENTE O EJEMPLAR, INCLUYENDO, SIN LIMITACIÓN, DAÑOS POR PÉRDIDA DE BENEFICIOS, FONDO DE COMERCIO, USO, DATOS U OTRAS PÉRDIDAS INTANGIBLES, QUE SURJAN DE O ESTÉN RELACIONADOS CON EL USO O LA INCAPACIDAD DE USAR LOS SERVICIOS.</p>
        </div>

        <div class="policy-section">
            <h2>8. Indemnización</h2>
            <p>Aceptas indemnizar, defender y eximir de responsabilidad a CritFlix, sus afiliados, directores, empleados, agentes, proveedores o licenciantes de y contra todas las reclamaciones, responsabilidades, daños, pérdidas y gastos, incluyendo, sin limitación, honorarios legales y contables razonables, que surjan de o estén relacionados con tu acceso o uso de nuestros Servicios, tu violación de estos Términos o tu violación de cualquier derecho de terceros.</p>
        </div>

        <div class="policy-section">
            <h2>9. Modificaciones</h2>
            <p>Nos reservamos el derecho de modificar estos Términos en cualquier momento. Si realizamos cambios, te notificaremos publicando los Términos actualizados en esta página y actualizando la fecha de "última actualización". Te recomendamos revisar estos Términos periódicamente para estar informado de cualquier cambio. El uso continuado de nuestros Servicios después de la publicación de los Términos modificados constituye tu aceptación de los cambios.</p>
        </div>

        <div class="policy-section">
            <h2>10. Terminación</h2>
            <p>Podemos terminar o suspender tu acceso a nuestros Servicios inmediatamente, sin previo aviso o responsabilidad, por cualquier razón, incluyendo, sin limitación, si violas estos Términos. Tras la terminación, tu derecho a utilizar los Servicios cesará inmediatamente.</p>
        </div>

        <div class="policy-section">
            <h2>11. Ley aplicable</h2>
            <p>Estos Términos se regirán e interpretarán de acuerdo con las leyes de España, sin tener en cuenta sus disposiciones sobre conflictos de leyes.</p>
        </div>

        <div class="policy-section">
            <h2>12. Resolución de disputas</h2>
            <p>Cualquier disputa, controversia o reclamación que surja de o esté relacionada con estos Términos o el incumplimiento, terminación o validez de los mismos, será resuelta mediante negociación de buena fe. Si la disputa no puede ser resuelta mediante negociación, será sometida a los tribunales competentes de Barcelona, España.</p>
        </div>

        <div class="policy-section">
            <h2>13. Disposiciones generales</h2>

            <h3>13.1 Acuerdo completo</h3>
            <p>Estos Términos constituyen el acuerdo completo entre tú y CritFlix con respecto a nuestros Servicios y sustituyen a todos los acuerdos anteriores o contemporáneos, escritos u orales, con respecto a los Servicios.</p>

            <h3>13.2 Divisibilidad</h3>
            <p>Si alguna disposición de estos Términos se considera inválida o inaplicable, dicha disposición se interpretará de manera consistente con la ley aplicable para reflejar, lo más fielmente posible, las intenciones originales de las partes, y las disposiciones restantes seguirán teniendo pleno vigor y efecto.</p>

            <h3>13.3 Renuncia</h3>
            <p>Ninguna renuncia por parte de CritFlix a cualquier término o condición establecida en estos Términos se considerará una renuncia adicional o continua a dicho término o condición o una renuncia a cualquier otro término o condición, y cualquier falla de CritFlix para hacer valer un derecho o disposición bajo estos Términos no constituirá una renuncia a dicho derecho o disposición.</p>
        </div>

        <div class="policy-section">
            <h2>14. Contacto</h2>
            <p>Si tienes alguna pregunta sobre estos Términos, contáctanos en:</p>
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
