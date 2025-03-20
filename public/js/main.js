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

/**
 * Función para verificar si la página puede usar el sistema antiguo
 * como respaldo si el nuevo sistema falla
 */
async function cargarScriptOriginal() {
    try {
        // Obtener el script original desde el servidor
        const scriptUrl = '/script.js.original';  // Suponiendo que renombramos el script original
        const response = await fetch(scriptUrl);

        if (!response.ok) {
            console.log('El script original no está disponible, utilizando solo el modular.');
            return false;
        }

        // Crear un elemento script e insertarlo en la página
        const script = document.createElement('script');
        script.textContent = await response.text();
        document.body.appendChild(script);

        console.log('Script original cargado como respaldo.');
        return true;
    } catch (error) {
        console.error('Error al cargar el script original:', error);
        return false;
    }
}

// Inicialización de módulos cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', async () => {
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
        console.log("Intentando cargar script original como respaldo...");

        // Si hay error, intentar cargar el script original como respaldo
        const scriptCargado = await cargarScriptOriginal();
        if (scriptCargado) {
            // Si se cargó el script original, intentar inicializar funciones clave
            try {
                if (typeof window.cargarBanner === 'function') window.cargarBanner();
                if (typeof window.cargarTendencias === 'function') window.cargarTendencias();
                if (typeof window.renderFavorites === 'function') window.renderFavorites();
                if (typeof window.renderCriticos === 'function') window.renderCriticos();
            } catch (err) {
                console.error("Error al inicializar con script original:", err);
            }
        }
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
