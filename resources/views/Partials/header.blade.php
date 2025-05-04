<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ route('home') }}">
                <span class="logo-text">CritFlix</span>
                <span class="logo-dot"></span>
            </a>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="{{ route('home') }}" class="nav-link">{{ __('messages.home') }}</a></li>
                <li class="nav-item"><a href="{{ route('criticos') }}" class="nav-link">{{ __('messages.critics') }}</a></li>
                <li class="nav-item"><a href="{{ route('peliculas') }}" class="nav-link">{{ __('messages.movies') }}</a></li>
                <li class="nav-item"><a href="{{ route('series') }}" class="nav-link">{{ __('messages.series') }}</a></li>
                <li class="nav-item"><a href="{{ route('tendencias') }}" class="nav-link">{{ __('messages.trends') }}</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search" placeholder="{{ __('messages.search') }} {{ __('messages.movies') }}, {{ __('messages.series') }}...">
                    <div id="suggestions" class="search-suggestions"></div>
                </div>
            </div>

            <div class="language-selector">
                <div class="language-dropdown js-language-dropdown">
                    <button class="language-btn" aria-expanded="false" aria-haspopup="true" id="language-menu-button">
                        <i class="fas fa-globe"></i>
                        <span class="btn-text">{{ __('messages.language') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="language-dropdown-content" role="menu" aria-labelledby="language-menu-button">
                        <a href="{{ route('language.change', ['locale' => 'es']) }}" @if(app()->getLocale() == 'es') class="active" @endif>
                            <i class="fas fa-flag"></i> {{ __('messages.spanish') }}
                        </a>
                        <a href="{{ route('language.change', ['locale' => 'ca']) }}" @if(app()->getLocale() == 'ca') class="active" @endif>
                            <i class="fas fa-flag"></i> {{ __('messages.catalan') }}
                        </a>
                        <a href="{{ route('language.change', ['locale' => 'en']) }}" @if(app()->getLocale() == 'en') class="active" @endif>
                            <i class="fas fa-flag"></i> {{ __('messages.english') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="auth-buttons">
                @guest
                    <a href="{{ route('login') }}" class="auth-btn login-btn">
                        <i class="fas fa-user"></i>
                        <span class="btn-text">{{ __('messages.login') }}</span>
                    </a>
                    <a href="{{ route('register') }}" class="auth-btn register-btn">
                        <i class="fas fa-user-plus"></i>
                        <span class="btn-text">{{ __('messages.register') }}</span>
                    </a>
                @else
                    <div class="user-actions">
                        @include('partials.notificaciones')

                        <div class="user-profile-dropdown js-profile-dropdown">
                            <button class="profile-btn" aria-expanded="false" aria-haspopup="true" id="profile-menu-button">
                                <i class="fas fa-user-circle"></i>
                                <span class="btn-text">{{ Str::limit(auth()->user()->name, 12) }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="profile-dropdown-content" role="menu" aria-labelledby="profile-menu-button">
                                <a href="{{ route('profile.show', auth()->id()) }}">
                                    <i class="fas fa-user"></i> {{ __('messages.profile') }}
                                </a>
                                <a href="{{ route('home') }}">
                                    <i class="fas fa-heart"></i> {{ __('messages.my_favorites') }}
                                </a>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>

<script>
    // Script mejorado para manejar los dropdowns en todos los dispositivos
    document.addEventListener('DOMContentLoaded', function() {
        // Función para configurar dropdowns
        function setupDropdown(dropdownClass, btnId, closeOnClickOutside = true) {
            const dropdown = document.querySelector(dropdownClass);
            const btn = document.getElementById(btnId);

            if (!dropdown || !btn) return;

            // Manejo de clics
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Cerrar otros dropdowns
                document.querySelectorAll('.active').forEach(el => {
                    if (!el.contains(dropdown)) {
                        el.classList.remove('active');
                        const otherBtn = el.querySelector('button');
                        if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle actual dropdown
                const isExpanded = btn.getAttribute('aria-expanded') === 'true';
                dropdown.classList.toggle('active');
                btn.setAttribute('aria-expanded', !isExpanded);
            });

            // Manejo de teclas
            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    btn.click();
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('active');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });

            // Cerrar al hacer clic fuera si está habilitado
            if (closeOnClickOutside) {
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Enlaces dentro del dropdown
            dropdown.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(() => {
                        dropdown.classList.remove('active');
                        btn.setAttribute('aria-expanded', 'false');
                    }, 100);
                });
            });
        }

        // Configurar dropdowns
        setupDropdown('.js-language-dropdown', 'language-menu-button');
        setupDropdown('.js-profile-dropdown', 'profile-menu-button');

        // Cierre con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.active').forEach(el => {
                    el.classList.remove('active');
                    const btn = el.querySelector('button');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                });
            }
        });
    });
</script>


