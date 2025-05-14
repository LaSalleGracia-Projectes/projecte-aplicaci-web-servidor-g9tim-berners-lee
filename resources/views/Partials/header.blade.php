<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ route('home') }}">
                <span class="logo-text">CritFlix</span>
                <span class="logo-dot"></span>
            </a>
        </div>

        <button id="mobile-menu-toggle" class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>

        <nav id="main-navigation" class="main-nav">
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
                <div class="language-dropdown">
                    <button type="button" class="language-btn">
                        <i class="fas fa-globe"></i>
                        <span class="btn-text">{{ __('messages.language') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="language-dropdown-content">
                        <a href="{{ route('language.change', ['locale' => 'es']) }}" class="language-link {{ app()->getLocale() == 'es' ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> {{ __('messages.spanish') }}
                        </a>
                        <a href="{{ route('language.change', ['locale' => 'ca']) }}" class="language-link {{ app()->getLocale() == 'ca' ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> {{ __('messages.catalan') }}
                        </a>
                        <a href="{{ route('language.change', ['locale' => 'en']) }}" class="language-link {{ app()->getLocale() == 'en' ? 'active' : '' }}">
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

                        <div class="user-profile-dropdown">
                            <button type="button" class="profile-btn">
                                <i class="fas fa-user-circle"></i>
                                <span class="btn-text">{{ Str::limit(auth()->user()->name, 12) }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="profile-dropdown-content">
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


