import config from './utils/config.js';
import helpers from './utils/helpers.js';
import bannerModule from './components/banner.js';
import trendingModule from './components/trending.js';
import randomizerModule from './components/randomizer.js';
import favoritesModule from './components/favorites.js';
import searchModule from './components/search.js';
import criticsModule from './components/critics.js';
import authModule from './components/auth.js';
import movieDetailModule from './components/movieDetail.js';

// Configurar variables globales con valores correctos
window.API_KEY = config.API_KEY;
window.BASE_URL = config.BASE_URL;
window.IMG_URL = config.IMG_URL + config.POSTER_SIZES.MEDIUM;

// Exponer funciones útiles globalmente para uso en HTML
window.helpers = helpers;
window.movieDetail = movieDetailModule;
window.openMovieDetail = movieDetailModule.openMovieDetail;
window.toggleFavorite = favoritesModule.toggleFavorite;
window.renderStars = helpers.renderStars;
window.showToast = helpers.showToast;

// Funciones del script original que pueden ser necesarias para mantener compatibilidad
window.generarRecomendacion = () => randomizerModule.generarRecomendacion();
window.cargarBanner = () => bannerModule.loadBannerForGenericContainer();
window.cargarTendencias = () => trendingModule.cargarTendencias();
window.renderFavorites = () => favoritesModule.renderFavorites();
window.renderCriticos = () => criticsModule.renderCriticos();

// Inicialización de módulos cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar módulos según la página
    setupGlobalNavigation();

    try {
        // Inicializar módulos principales
        authModule.init();
        searchModule.init();

        // Módulos de página de inicio
        bannerModule.init();
        trendingModule.init();
        randomizerModule.init();
        favoritesModule.init();
        criticsModule.init();

        console.log("CritFlix inicializado correctamente");
    } catch (error) {
        console.error("Error en la inicialización:", error);
    }

    // Inicializar menú móvil cuando el DOM esté cargado
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('show-mobile');
            this.classList.toggle('active');
            // Cambiar el ícono según el estado
            const icon = this.querySelector('i');
            if (icon) {
                if (this.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // Cerrar el menú al hacer click en un enlace
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 767 && mainNav.classList.contains('show-mobile')) {
                mainNav.classList.remove('show-mobile');
                if (mobileMenuToggle) {
                    mobileMenuToggle.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        });
    });

    // Manejo de dropdowns mejorado
    // Selector de idioma
    const languageDropdown = document.querySelector('.language-dropdown');
    const languageBtn = languageDropdown?.querySelector('.language-btn');
    let isHoveringLanguage = false;

    if (languageBtn && languageDropdown) {
        // Manejar clic en el botón de idioma
        languageBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Cerrar dropdown de perfil si está abierto
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            if (profileDropdown && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }

            // Alternar el dropdown de idioma
            languageDropdown.classList.toggle('active');
        });

        // Eliminar eventos de hover que pueden interferir
        /* Comentamos estos eventos para eliminar el comportamiento de hover
        languageDropdown.addEventListener('mouseenter', function() {
            isHoveringLanguage = true;
        });

        languageDropdown.addEventListener('mouseleave', function() {
            isHoveringLanguage = false;
            // Solo cerrar si no está activo por clic
            if (!languageDropdown.classList.contains('active')) {
                const content = languageDropdown.querySelector('.language-dropdown-content');
                if (content) {
                    content.style.display = 'none';
                    setTimeout(() => {
                        if (!isHoveringLanguage) {
                            content.style.display = '';
                        }
                    }, 100);
                }
            }
        });
        */
    }

    // Dropdown de perfil de usuario
    const profileDropdown = document.querySelector('.user-profile-dropdown');
    const profileBtn = profileDropdown?.querySelector('.profile-btn');
    let isHoveringProfile = false;

    if (profileBtn && profileDropdown) {
        // Manejar clic en el botón de perfil
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Cerrar dropdown de idioma si está abierto
            if (languageDropdown && languageDropdown.classList.contains('active')) {
                languageDropdown.classList.remove('active');
            }

            // Alternar el dropdown de perfil
            profileDropdown.classList.toggle('active');
        });

        // Evitar que el hover interfiera con el clic
        profileDropdown.addEventListener('mouseenter', function() {
            isHoveringProfile = true;
        });

        profileDropdown.addEventListener('mouseleave', function() {
            isHoveringProfile = false;
            // Solo cerrar si no está activo por clic
            if (!profileDropdown.classList.contains('active')) {
                const content = profileDropdown.querySelector('.profile-dropdown-content');
                if (content) {
                    content.style.display = 'none';
                    setTimeout(() => {
                        if (!isHoveringProfile) {
                            content.style.display = '';
                        }
                    }, 100);
                }
            }
        });
    }

    // Cerrar dropdowns al hacer clic en cualquier otra parte
    document.addEventListener('click', function(e) {
        // Cerrar dropdown de idioma si está abierto y se hizo clic fuera
        if (languageDropdown &&
            languageDropdown.classList.contains('active') &&
            !languageDropdown.contains(e.target)) {
            languageDropdown.classList.remove('active');
        }

        // Cerrar dropdown de perfil si está abierto y se hizo clic fuera
        if (profileDropdown &&
            (profileDropdown.classList.contains('active') ||
             (profileDropdown.querySelector('.profile-dropdown-content') &&
              getComputedStyle(profileDropdown.querySelector('.profile-dropdown-content')).display === 'block')) &&
            !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');

            // Asegurarse de que el contenido esté oculto
            const profileContent = profileDropdown.querySelector('.profile-dropdown-content');
            if (profileContent) {
                profileContent.style.display = 'none';
                setTimeout(() => {
                    profileContent.style.display = '';
                }, 100);
            }
        }

        if (mainNav && mainNav.classList.contains('show-mobile') &&
            !mainNav.contains(e.target) &&
            !mobileMenuToggle.contains(e.target)) {
            mainNav.classList.remove('show-mobile');
            if (mobileMenuToggle) {
                mobileMenuToggle.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }
    });

    // Cerrar dropdowns con la tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (languageDropdown && languageDropdown.classList.contains('active')) {
                languageDropdown.classList.remove('active');
            }

            if (profileDropdown && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }

            if (mainNav && mainNav.classList.contains('show-mobile')) {
                mainNav.classList.remove('show-mobile');
                if (mobileMenuToggle) {
                    mobileMenuToggle.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        }
    });

    // Ajustar header al hacer scroll
    const header = document.querySelector('.main-header');

    if (header) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
});

// Configuración de navegación global
function setupGlobalNavigation() {
    // Botón Back to Top
    const backToTopBtn = document.getElementById("backToTop");
    if (backToTopBtn) {
        window.addEventListener("scroll", () => {
            backToTopBtn.style.display = window.scrollY > 300 ? "block" : "none";
        });
        backToTopBtn.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }
}
