<footer class="main-footer">
    <div class="footer-grid">
        <!-- Sección de información -->
        <div class="footer-section info-section">
            <div class="footer-logo">
                <i class="fas fa-film"></i>
                <h3>CritFlix</h3>
            </div>
            <p>{{ __('messages.footer_description', ['default' => 'Tu plataforma de confianza para descubrir, calificar y compartir películas. Únete a nuestra comunidad cinéfila.']) }}</p>
            <div class="footer-stats">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span>+10K {{ __('messages.users', ['default' => 'Usuarios']) }}</span>
                </div>
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <span>+50K {{ __('messages.reviews', ['default' => 'Reseñas']) }}</span>
                </div>
            </div>
        </div>

        <!-- Sección de navegación -->
        <div class="footer-section nav-section">
            <h4>{{ __('messages.navigation', ['default' => 'Navegación']) }}</h4>
            <nav class="footer-nav">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> {{ __('messages.home') }}</a>
                <a href="{{ route('peliculas') }}"><i class="fas fa-film"></i> {{ __('messages.movies') }}</a>
                <a href="{{ route('series') }}"><i class="fas fa-tv"></i> {{ __('messages.series') }}</a>
                <a href="#"><i class="fas fa-star"></i> {{ __('messages.top_rated', ['default' => 'Top Valoradas']) }}</a>
                <a href="#"><i class="fas fa-clock"></i> {{ __('messages.coming_soon', ['default' => 'Próximamente']) }}</a>
            </nav>
        </div>

        <!-- Sección de categorías -->
        <div class="footer-section categories-section">
            <h4>{{ __('messages.categories', ['default' => 'Categorías']) }}</h4>
            <div class="footer-categories">
                <a href="#"><i class="fas fa-theater-masks"></i> {{ __('messages.drama', ['default' => 'Drama']) }}</a>
                <a href="#"><i class="fas fa-ghost"></i> {{ __('messages.horror', ['default' => 'Terror']) }}</a>
                <a href="#"><i class="fas fa-laugh"></i> {{ __('messages.comedy', ['default' => 'Comedia']) }}</a>
                <a href="#"><i class="fas fa-rocket"></i> {{ __('messages.sci_fi', ['default' => 'Ciencia Ficción']) }}</a>
                <a href="#"><i class="fas fa-heart"></i> {{ __('messages.romance', ['default' => 'Romance']) }}</a>
            </div>
        </div>

        <!-- Sección de contacto y redes sociales -->
        <div class="footer-section contact-section">
            <h4>{{ __('messages.follow_us', ['default' => 'Síguenos']) }}</h4>
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
            <p>&copy; {{ date('Y') }} CritFlix - {{ __('messages.copyright') }}</p>
            <div class="footer-links">
                <a href="{{ route('policies.privacy') }}">{{ __('messages.privacy') }}</a>
                <a href="{{ route('policies.terms') }}">{{ __('messages.terms') }}</a>
                <a href="{{ route('policies.contact') }}">{{ __('messages.contact') }}</a>
            </div>
        </div>
    </div>
</footer>
