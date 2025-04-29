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
