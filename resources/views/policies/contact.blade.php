@extends('layouts.app')

@section('title', 'Contacto - CritFlix')

@section('content')
<div class="contact-container">
    <header class="contact-header">
        <div class="contact-header-content">
            <h1>Contacta con Nosotros</h1>
            <p class="contact-meta">Estamos aquí para ayudarte</p>
        </div>
    </header>

    <div class="contact-content">
        <div class="contact-grid">
            <div class="contact-info-section">
                <h2>Información de Contacto</h2>
                <p>Tienes preguntas, comentarios o sugerencias? Estamos encantados de escucharte. Ponte en contacto con nuestro equipo utilizando cualquiera de los siguientes métodos:</p>

                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-detail">
                            <h3>Email</h3>
                            <p><a href="mailto:critflixteam@gmail.com">critflixteam@gmail.com</a></p>
                            <p class="detail-meta">Respuesta en 24-48 horas laborables</p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-detail">
                            <h3>Dirección</h3>
                            <p>CritFlix S.L.</p>
                            <p>Calle de la Cinematografía 123</p>
                            <p>08001 Barcelona, España</p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-detail">
                            <h3>Teléfono</h3>
                            <p><a href="tel:+34900123456">+34 900 123 456</a></p>
                            <p class="detail-meta">Lunes a Viernes: 9:00 - 18:00</p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="contact-detail">
                            <h3>Redes Sociales</h3>
                            <div class="social-links">
                                <a href="#" class="social-link facebook" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link twitter" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link instagram" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link youtube" title="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-section">
                <h2>Envíanos un Mensaje</h2>
                <p>Rellena el siguiente formulario y nos pondremos en contacto contigo lo antes posible.</p>

                <form id="contactForm" class="contact-form" method="POST" action="#">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" placeholder="Tu nombre" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="subject">Asunto</label>
                        <select id="subject" name="subject" class="form-control" required>
                            <option value="">Selecciona un asunto</option>
                            <option value="support">Soporte técnico</option>
                            <option value="account">Problemas con mi cuenta</option>
                            <option value="feedback">Sugerencias</option>
                            <option value="business">Colaboraciones</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Mensaje</label>
                        <textarea id="message" name="message" rows="5" placeholder="¿En qué podemos ayudarte?" required class="form-control"></textarea>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" id="privacy" name="privacy" required class="form-check-input">
                        <label for="privacy" class="form-check-label">He leído y acepto la <a href="{{ route('policies.privacy') }}">Política de Privacidad</a></label>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="faq-section">
            <h2>Preguntas Frecuentes</h2>

            <div class="faq-grid">
                <div class="faq-item">
                    <h3>¿Cómo puedo recuperar mi contraseña?</h3>
                    <p>Puedes recuperar tu contraseña haciendo clic en "¿Olvidaste tu contraseña?" en la página de inicio de sesión y siguiendo las instrucciones que se enviarán a tu correo electrónico.</p>
                </div>

                <div class="faq-item">
                    <h3>¿Cómo puedo crear una lista de películas?</h3>
                    <p>Una vez inicies sesión, ve a tu perfil y haz clic en "Mis Listas". Allí podrás crear nuevas listas y añadir películas a tus listas existentes.</p>
                </div>

                <div class="faq-item">
                    <h3>¿Cómo puedo convertirme en crítico en CritFlix?</h3>
                    <p>Para convertirte en crítico, debes publicar regularmente críticas de calidad. Después de alcanzar cierto número de críticas bien valoradas, nuestro equipo revisará tu perfil y podrá otorgarte el estatus de crítico.</p>
                </div>

                <div class="faq-item">
                    <h3>¿Puedo cambiar mi nombre de usuario?</h3>
                    <p>Actualmente no es posible cambiar tu nombre de usuario una vez creada la cuenta. Sin embargo, puedes editar tu nombre para mostrar en tu perfil desde los ajustes de tu cuenta.</p>
                </div>

                <div class="faq-item">
                    <h3>¿CritFlix tiene aplicación móvil?</h3>
                    <p>Actualmente no tenemos una aplicación móvil, pero nuestra web está optimizada para dispositivos móviles, por lo que puedes disfrutar de CritFlix desde cualquier dispositivo con un navegador web.</p>
                </div>

                <div class="faq-item">
                    <h3>¿Cómo puedo reportar contenido inapropiado?</h3>
                    <p>Si encuentras contenido que consideras inapropiado, puedes reportarlo haciendo clic en el botón "Reportar" que aparece junto a cada crítica o comentario. Nuestro equipo revisará el contenido y tomará las medidas necesarias.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        color: var(--text-light);
        font-family: 'Poppins', Arial, sans-serif;
    }

    .contact-header {
        background: linear-gradient(135deg, var(--verde-neon) 0%, var(--verde-principal) 100%);
        color: var(--negro);
        border-radius: 8px;
        margin: 30px 0;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 255, 60, 0.2);
        text-align: center;
    }

    .contact-header-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .contact-header h1 {
        font-size: 2.5rem;
        margin: 0;
        font-weight: 700;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .contact-meta {
        margin-top: 10px;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .contact-content {
        background: var(--bg-card);
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        margin-bottom: 50px;
        border: 1px solid var(--border-color);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 60px;
    }

    .contact-info-section, .contact-form-section {
        padding: 20px;
    }

    .contact-info-section h2, .contact-form-section h2, .faq-section h2 {
        color: var(--verde-neon);
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .contact-methods {
        margin-top: 30px;
    }

    .contact-method {
        display: flex;
        margin-bottom: 30px;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--verde-neon) 0%, var(--verde-principal) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .contact-icon i {
        color: var(--negro);
        font-size: 1.5rem;
    }

    .contact-detail h3 {
        font-size: 1.2rem;
        margin: 0 0 8px 0;
        color: var(--text-light);
    }

    .contact-detail p {
        margin: 0 0 5px 0;
        color: var(--text-light);
    }

    .detail-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .social-links {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.3s ease;
    }

    .social-link.facebook {
        background-color: #3b5998;
    }

    .social-link.twitter {
        background-color: #1da1f2;
    }

    .social-link.instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }

    .social-link.youtube {
        background-color: #ff0000;
    }

    .social-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    /* Form styles */
    .contact-form {
        background-color: var(--bg-hover);
        padding: 25px;
        border-radius: 8px;
        border: 1px solid var(--border-light);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-dark);
        color: var(--text-light);
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--verde-neon);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 255, 60, 0.1);
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-light);
    }

    .form-check {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-right: 10px;
    }

    .form-check-label {
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--verde-neon) 0%, var(--verde-principal) 100%);
        color: var(--negro);
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--verde-principal) 0%, var(--verde-claro) 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 255, 60, 0.2);
    }

    /* FAQ section */
    .faq-section {
        margin-top: 40px;
        border-top: 1px solid var(--border-color);
        padding-top: 40px;
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin-top: 30px;
    }

    .faq-item {
        background-color: var(--bg-hover);
        border-radius: 8px;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        border: 1px solid var(--border-light);
    }

    .faq-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        border-color: var(--verde-neon);
    }

    .faq-item h3 {
        color: var(--verde-neon);
        font-size: 1.2rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .faq-item p {
        color: var(--text-light);
        line-height: 1.6;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }

        .faq-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .contact-header {
            padding: 30px 20px;
            margin: 20px 0;
        }

        .contact-header h1 {
            font-size: 2rem;
        }

        .contact-content {
            padding: 25px 20px;
        }

        .contact-method {
            flex-direction: column;
        }

        .contact-icon {
            margin-bottom: 15px;
            margin-right: 0;
        }
    }

    /* Accessibility */
    .form-control:focus, .btn:focus {
        outline: 3px solid rgba(0, 255, 60, 0.3);
    }

    /* Links */
    a {
        color: var(--verde-neon);
        text-decoration: none;
        transition: color 0.2s;
    }

    a:hover {
        color: var(--verde-principal);
        text-decoration: underline;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Aquí iría la lógica para enviar el formulario por AJAX
                // Por ahora solo mostramos una alerta de éxito

                alert('Gracias por tu mensaje. Te contactaremos pronto.');
                contactForm.reset();
            });
        }
    });
</script>
@endpush
