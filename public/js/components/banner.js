import config from '../utils/config.js';
import helpers from '../utils/helpers.js';

/**
 * Módulo para gestionar el banner de películas destacadas
 */
const bannerModule = {
    /**
     * Inicializa el módulo del banner
     */
    init() {
        const bannerContainer = document.querySelector('.hero-banner');
        if (!bannerContainer) {
            // Si no existe el contenedor específico, intentar con el contenedor genérico
            const genericBanner = document.querySelector('.banner .slides');
            if (genericBanner) {
                this.loadBannerForGenericContainer();
                return;
            }

            // Si tampoco hay un contenedor genérico, salir
            return;
        }

        this.loadBannerMovies();
    },

    /**
     * Carga películas para el banner genérico
     */
    async loadBannerForGenericContainer() {
        try {
            helpers.showSpinner();

            const url = `${config.BASE_URL}/movie/popular?api_key=${config.API_KEY}&language=es-ES`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Error al cargar las películas destacadas');
            }

            const data = await response.json();
            this.renderGenericBanner(data.results.slice(0, 5));
        } catch (error) {
            console.error('Error cargando el banner:', error);
        } finally {
            helpers.hideSpinner();
        }
    },

    /**
     * Renderiza las películas en el banner genérico
     * @param {Array} movies - Lista de películas a mostrar
     */
    renderGenericBanner(movies) {
        const slidesContainer = document.getElementById("bannerSlides");
        if (!slidesContainer) return;

        // Mantiene los slides existentes si hay, sino los reemplaza
        if (slidesContainer.querySelector('.slide')) return;

        slidesContainer.innerHTML = "";

        movies.forEach((movie, index) => {
            const slide = document.createElement("div");
            slide.classList.add("slide");
            if (index === 0) slide.classList.add("active");

            const backdropPath = movie.backdrop_path;
            const imageUrl = backdropPath
                ? `${config.IMG_URL}${config.BACKDROP_SIZES.LARGE}${backdropPath}`
                : '/img/default-backdrop.jpg';

            slide.style.backgroundImage = `url('${imageUrl}')`;

            slide.innerHTML = `
                <div class="slide-content">
                    <h2>${movie.title}</h2>
                    <p>${movie.overview.substring(0, 150)}...</p>
                </div>
            `;

            slidesContainer.appendChild(slide);
        });

        // Configurar navegación
        const dotsContainer = document.getElementById("sliderIndicators");
        if (dotsContainer) {
            dotsContainer.innerHTML = "";
            movies.forEach((_, index) => {
                const dot = document.createElement("span");
                dot.classList.add("dot");
                if (index === 0) dot.classList.add("active");
                dot.dataset.slide = index;

                dot.addEventListener("click", () => {
                    const currentIndex = Array.from(dotsContainer.children).findIndex(dot => dot.classList.contains("active"));
                    this.goToSlide(index);
                });

                dotsContainer.appendChild(dot);
            });
        }

        // Configurar botones
        const prevButton = document.getElementById("prevSlide");
        const nextButton = document.getElementById("nextSlide");

        if (prevButton) {
            prevButton.addEventListener("click", () => {
                const dots = dotsContainer.querySelectorAll(".dot");
                const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains("active"));
                this.goToSlide(currentIndex - 1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener("click", () => {
                const dots = dotsContainer.querySelectorAll(".dot");
                const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains("active"));
                this.goToSlide(currentIndex + 1);
            });
        }

        // Auto rotación
        setInterval(() => {
            const dots = dotsContainer.querySelectorAll(".dot");
            const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains("active"));
            this.goToSlide(currentIndex + 1);
        }, 5000);
    },

    /**
     * Navega a un slide específico
     */
    goToSlide(index) {
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".dot");

        if (!slides.length) return;

        // Calcula el índice correcto (manejo circular)
        const activeIndex = (index + slides.length) % slides.length;

        // Actualiza slides
        slides.forEach((slide, i) => {
            slide.style.opacity = i === activeIndex ? "1" : "0";
        });

        // Actualiza dots
        dots.forEach((dot, i) => {
            dot.classList.toggle("active", i === activeIndex);
        });
    },

    /**
     * Carga películas destacadas para el banner
     */
    async loadBannerMovies() {
        try {
            helpers.showSpinner();

            const url = `${config.BASE_URL}/movie/popular?api_key=${config.API_KEY}&language=es-ES`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Error al cargar las películas destacadas');
            }

            const data = await response.json();
            this.renderBanner(data.results.slice(0, 3));
        } catch (error) {
            console.error('Error cargando el banner:', error);
            helpers.showToast('Error al cargar las películas destacadas', 'error');
        } finally {
            helpers.hideSpinner();
        }
    },

    /**
     * Renderiza las películas en el banner
     * @param {Array} movies - Lista de películas a mostrar
     */
    renderBanner(movies) {
        const bannerContainer = document.querySelector('.hero-banner');
        if (!bannerContainer) return;

        // Limpia el contenedor
        bannerContainer.innerHTML = '';

        // Crea el slider
        const bannerSlider = document.createElement('div');
        bannerSlider.classList.add('banner-slider');

        // Agrega cada película al slider
        movies.forEach((movie, index) => {
            const slide = document.createElement('div');
            slide.classList.add('banner-slide');
            if (index === 0) slide.classList.add('active');

            // Usar el backdrop de la película
            const backdropPath = movie.backdrop_path;
            const imageUrl = backdropPath
                ? `${config.IMG_URL}${config.BACKDROP_SIZES.LARGE}${backdropPath}`
                : '/img/default-backdrop.jpg';

            // Crea el contenido del slide
            slide.innerHTML = `
                <div class="banner-content" style="background-image: url('${imageUrl}')">
                    <div class="banner-info">
                        <h2>${movie.title}</h2>
                        <p class="banner-overview">${movie.overview.slice(0, 150)}...</p>
                        <div class="banner-rating">${helpers.renderStars(movie.vote_average)}</div>
                        <a href="/infoPelicula/${movie.id}" class="banner-btn">Ver detalles</a>
                    </div>
                </div>
            `;

            bannerSlider.appendChild(slide);
        });

        // Agrega controles de navegación
        const navButtons = document.createElement('div');
        navButtons.classList.add('banner-navigation');

        // Botones de anterior y siguiente
        navButtons.innerHTML = `
            <button class="banner-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="banner-dots"></div>
            <button class="banner-next"><i class="fas fa-chevron-right"></i></button>
        `;

        // Agrega los puntos indicadores
        const dotsContainer = navButtons.querySelector('.banner-dots');
        movies.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('banner-dot');
            if (index === 0) dot.classList.add('active');
            dot.dataset.index = index;
            dotsContainer.appendChild(dot);
        });

        // Agrega el slider y navegación al contenedor
        bannerContainer.appendChild(bannerSlider);
        bannerContainer.appendChild(navButtons);

        // Configura la navegación
        this.setupBannerNavigation();
    },

    /**
     * Configura la navegación del banner
     */
    setupBannerNavigation() {
        const bannerContainer = document.querySelector('.hero-banner');
        if (!bannerContainer) return;

        const prevBtn = bannerContainer.querySelector('.banner-prev');
        const nextBtn = bannerContainer.querySelector('.banner-next');
        const dots = bannerContainer.querySelectorAll('.banner-dot');

        // Función para ir a un slide específico
        const goToSlide = (index) => {
            const slides = bannerContainer.querySelectorAll('.banner-slide');
            if (!slides.length) return;

            // Oculta todos los slides
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Muestra el slide activo
            const activeIndex = (index + slides.length) % slides.length;
            slides[activeIndex].classList.add('active');
            dots[activeIndex].classList.add('active');
        };

        // Configurar botones
        prevBtn.addEventListener('click', () => {
            const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains('active'));
            goToSlide(currentIndex - 1);
        });

        nextBtn.addEventListener('click', () => {
            const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains('active'));
            goToSlide(currentIndex + 1);
        });

        // Configurar puntos indicadores
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });

        // Rotación automática
        let interval = setInterval(() => {
            const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains('active'));
            goToSlide(currentIndex + 1);
        }, 6000);

        // Detener rotación al interactuar
        bannerContainer.addEventListener('mouseenter', () => clearInterval(interval));

        // Reanudar rotación al salir
        bannerContainer.addEventListener('mouseleave', () => {
            clearInterval(interval);
            interval = setInterval(() => {
                const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains('active'));
                goToSlide(currentIndex + 1);
            }, 6000);
        });
    }
};

export default bannerModule;
