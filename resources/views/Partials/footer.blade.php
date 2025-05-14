<footer class="main-footer">
    <div class="footer-grid">
        <!-- Sección de información -->
        <div class="footer-section">
            <h4>{{ __('messages.about_critflix') }}</h4>
            <p>{{ __('messages.footer_description') }}</p>
        </div>

        <!-- Sección de navegación -->
        <div class="footer-section">
            <h4>{{ __('messages.navigation') }}</h4>
            <nav class="footer-nav">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> {{ __('messages.home') }}</a>
                <a href="{{ route('peliculas') }}"><i class="fas fa-film"></i> {{ __('messages.movies') }}</a>
                <a href="{{ route('series') }}"><i class="fas fa-tv"></i> {{ __('messages.series') }}</a>
                <a href="{{ route('criticos') }}"><i class="fas fa-users"></i> {{ __('messages.critics') }}</a>
                <a href="{{ route('tendencias') }}"><i class="fas fa-chart-line"></i> {{ __('messages.trends') }}</a>
            </nav>
        </div>

        <!-- Sección de enlaces -->
        <div class="footer-section">
            <h4>{{ __('messages.useful_links') }}</h4>
            <nav class="footer-nav">
                <a href="{{ route('policies.privacy') }}"><i class="fas fa-shield-alt"></i> {{ __('messages.privacy') }}</a>
                <a href="{{ route('policies.terms') }}"><i class="fas fa-file-contract"></i> {{ __('messages.terms') }}</a>
                <a href="{{ route('policies.contact') }}"><i class="fas fa-envelope"></i> {{ __('messages.contact') }}</a>
            </nav>
        </div>

        <!-- Sección de contacto -->
        <div class="footer-section">
            <h4>{{ __('messages.contact') }}</h4>
            <nav class="footer-nav">
                <a href="mailto:info@critflix.com"><i class="fas fa-envelope"></i> info@critflix.com</a>
                <a href="tel:+123456789"><i class="fas fa-phone"></i> +123 456 789</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> {{ __('messages.location') }}</a>
            </nav>
        </div>
    </div>

    <!-- Footer inferior -->
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} CritFlix - {{ __('messages.copyright') }}</p>
    </div>
</footer>
