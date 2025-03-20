/**
 * Configuración global de la aplicación
 * Contiene las URLs base y claves de API
 */
const config = {
    // API de TMDB
    API_KEY: 'ba232569da1aac2f9b80a35300d0b04f',
    BASE_URL: 'https://api.themoviedb.org/3',
    IMG_URL: 'https://image.tmdb.org/t/p',

    // Tamaños de póster disponibles
    POSTER_SIZES: {
        SMALL: '/w185',
        MEDIUM: '/w342',
        LARGE: '/w500',
        ORIGINAL: '/original'
    },

    // Tamaños de backdrop disponibles
    BACKDROP_SIZES: {
        SMALL: '/w300',
        MEDIUM: '/w780',
        LARGE: '/w1280',
        ORIGINAL: '/original'
    },

    // Tipos de contenido
    CONTENT_TYPES: {
        MOVIE: 'movie',
        TV: 'tv'
    },

    // Clave para almacenar favoritos en localStorage
    FAVORITES_KEY: "critiflixFavorites"
};

export default config;
