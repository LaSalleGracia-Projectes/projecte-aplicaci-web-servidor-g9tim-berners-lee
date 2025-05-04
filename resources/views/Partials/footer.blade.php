<footer class="main-footer">
    <div class="footer-grid">
        <!-- Sección de información -->
        <div class="footer-section info-section">
            <div class="footer-logo">
                <i class="fas fa-film"></i>
                <h3>CritFlix</h3>
            </div>
            <p>Tu plataforma de confianza para descubrir, calificar y compartir películas. Únete a nuestra comunidad cinéfila.</p>
            <div class="footer-stats">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span>+10K Usuarios</span>
                </div>
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <span>+50K Reseñas</span>
                </div>
            </div>
        </div>

        <!-- Sección de navegación -->
        <div class="footer-section nav-section">
            <h4>Navegación</h4>
            <nav class="footer-nav">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> Inicio</a>
                <a href="{{ route('peliculas') }}"><i class="fas fa-film"></i> Películas</a>
                <a href="{{ route('series') }}"><i class="fas fa-tv"></i> Series</a>
                <a href="#"><i class="fas fa-star"></i> Top Valoradas</a>
                <a href="#"><i class="fas fa-clock"></i> Próximamente</a>
            </nav>
        </div>

        <!-- Sección de categorías -->
        <div class="footer-section categories-section">
            <h4>Categorías</h4>
            <div class="footer-categories">
                <a href="#"><i class="fas fa-theater-masks"></i> Drama</a>
                <a href="#"><i class="fas fa-ghost"></i> Terror</a>
                <a href="#"><i class="fas fa-laugh"></i> Comedia</a>
                <a href="#"><i class="fas fa-rocket"></i> Ciencia Ficción</a>
                <a href="#"><i class="fas fa-heart"></i> Romance</a>
            </div>
        </div>

        <!-- Sección de contacto y redes sociales -->
        <div class="footer-section contact-section">
            <h4>Síguenos</h4>
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

    <!-- Footer inferior -->
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p>&copy; {{ date('Y') }} CritFlix - Todos los derechos reservados</p>
            <div class="footer-links">
                <a href="{{ route('policies.privacy') }}">Política de Privacidad</a>
                <a href="{{ route('policies.terms') }}">Términos de Uso</a>
                <a href="{{ route('policies.contact') }}">Contacto</a>
            </div>
        </div>
    </div>
</footer>
