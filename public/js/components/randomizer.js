import config from '../utils/config.js';
import helpers from '../utils/helpers.js';
import favorites from './favorites.js';

const randomizerModule = {
    init() {
        console.log('Inicializando módulo de randomizer');
        this.setupForm();
        this.setupContentTypeSelector();
        this.populateYears();
    },

    setupContentTypeSelector() {
        const buttons = document.querySelectorAll('.content-type-btn');
        const movieFilters = document.querySelector('.movie-filters');
        const seriesFilters = document.querySelector('.series-filters');
        const tipoContenidoInput = document.getElementById('tipoContenido');

        if (!buttons.length || !tipoContenidoInput) {
            console.error('No se encontraron elementos necesarios para el selector de tipos');
            return;
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                tipoContenidoInput.value = button.dataset.type;

                console.log(`Tipo de contenido seleccionado: ${button.dataset.type}`);

                if (button.dataset.type === 'movie') {
                    if (movieFilters) movieFilters.style.display = 'grid';
                    if (seriesFilters) seriesFilters.style.display = 'none';
                } else {
                    if (movieFilters) movieFilters.style.display = 'none';
                    if (seriesFilters) seriesFilters.style.display = 'grid';
                }
            });
        });
    },

    setupForm() {
        const form = document.getElementById('randomizerForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.generarRecomendacion();
            });
            console.log('Formulario de randomizer configurado correctamente');
        } else {
            console.error('No se encontró el formulario del randomizer');
        }
    },

    async generarRecomendacion(intentos = 0) {
        const form = document.getElementById('randomizerForm');
        if (!form) {
            console.error('No se encontró el formulario para generar recomendación');
            return;
        }

        const formData = new FormData(form);
        const params = new URLSearchParams();
        const container = document.getElementById("randomContainer");

        // Guardar el valor de la plataforma seleccionada para filtrado posterior
        const plataformaSeleccionada = formData.get('plataforma');
        console.log('Plataforma seleccionada:', plataformaSeleccionada);

        if (!container) {
            console.error('No se encontró el contenedor para mostrar la recomendación');
            return;
        }

        // Mostrar indicador de carga
        container.innerHTML = `<div class="loading"><i class="fas fa-spinner fa-spin"></i> ${window.translations ? window.translations.generating_recommendation : 'Generando recomendación...'}</div>`;

        // Limitar los reintentos a un máximo de 3 para evitar bucles infinitos
        if (intentos > 3) {
            console.warn('Se alcanzó el límite de intentos para encontrar contenido en la plataforma seleccionada');
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${window.translations ? window.translations.no_results_in_platform : `No se encontró contenido disponible en ${plataformaSeleccionada}`}</p>
                    <button class="movie-btn primary" onclick="randomizerModule.generarRecomendacionSinFiltro()">
                        <i class="fas fa-dice"></i> ${window.translations ? window.translations.try_without_platform : 'Probar sin filtro de plataforma'}
                    </button>
                </div>
            `;
            return;
        }

        try {
            // Solo añadir los parámetros que tienen valor y que no sean 'plataforma'
            // El parámetro 'plataforma' lo manejaremos en el cliente, no en el servidor
            for (let [key, value] of formData.entries()) {
                if (value && value !== '' && key !== 'plataforma') {
                    params.append(key, value);
                }
            }

            // Añadir un parámetro random para evitar resultados en caché
            params.append('_random', Math.random());

            console.log('Enviando parámetros al randomizer:', Object.fromEntries(params.entries()));

            const response = await fetch(`/random/generate?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let data;
            try {
                const responseText = await response.text();
                console.log('Respuesta del servidor (randomizer):', responseText);

                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Error al parsear la respuesta del randomizer:', e);
                throw new Error('Error al procesar la respuesta del servidor');
            }

            if (!response.ok) {
                throw new Error(data.message || 'Error al generar recomendación');
            }

            if (data.data) {
                console.log('Recomendación recibida correctamente');

                // Si hay una plataforma seleccionada, verificar que la película está disponible en esa plataforma
                if (plataformaSeleccionada && plataformaSeleccionada !== '') {
                    // Verificar si la película tiene información de proveedores
                    const movie = data.data;
                    const plataformaEncontrada = this.verificarPlataforma(movie, plataformaSeleccionada);

                    if (!plataformaEncontrada) {
                        console.log(`La película no está disponible en ${plataformaSeleccionada}, intentando otra (intento ${intentos + 1})`);
                        // Si no está disponible en la plataforma seleccionada, intentar con otra película
                        return this.generarRecomendacion(intentos + 1);
                    }
                }

                // Si pasó la verificación de plataforma o no hay plataforma seleccionada, renderizar
                this.renderRandomMovieFromAPI(data.data);
            } else {
                console.warn('No se encontraron resultados con los criterios especificados');
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>${window.translations ? window.translations.no_results_found : 'No se encontraron resultados con los criterios especificados'}</p>
                        <button class="movie-btn primary" onclick="randomizerModule.generarRecomendacionAleatoria()">
                            <i class="fas fa-dice"></i> ${window.translations ? window.translations.try_random_recommendation : 'Prueba con recomendación aleatoria'}
                        </button>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error en la generación de recomendación:', error);
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${window.translations ? window.translations.error_generating : 'Error al generar la recomendación:'} ${error.message}</p>
                    <button class="movie-btn primary" onclick="randomizerModule.generarRecomendacionAleatoria()">
                        <i class="fas fa-dice"></i> ${window.translations ? window.translations.try_other_filters : 'Intentar con otros filtros'}
                    </button>
                </div>
            `;
        }
    },

    // Método para generar una recomendación sin filtro de plataforma
    generarRecomendacionSinFiltro() {
        // Guardar el valor actual de la plataforma
        const plataformaSelect = document.getElementById('plataforma');
        const plataformaSerieSelect = document.getElementById('plataformaSerie');

        // Almacenar valores originales para restaurarlos después
        const valorOriginalPelicula = plataformaSelect ? plataformaSelect.value : '';
        const valorOriginalSerie = plataformaSerieSelect ? plataformaSerieSelect.value : '';

        // Establecer temporalmente el valor a vacío (todas las plataformas)
        if (plataformaSelect) plataformaSelect.value = '';
        if (plataformaSerieSelect) plataformaSerieSelect.value = '';

        // Generar recomendación
        this.generarRecomendacion();

        // Restaurar los valores originales
        setTimeout(() => {
            if (plataformaSelect) plataformaSelect.value = valorOriginalPelicula;
            if (plataformaSerieSelect) plataformaSerieSelect.value = valorOriginalSerie;
        }, 500);
    },

    // Método para verificar si una película/serie está disponible en la plataforma seleccionada
    verificarPlataforma(movie, plataforma) {
        // Mapeo de nombres de plataformas a IDs de proveedores de TMDb
        // Estos IDs pueden variar según la región, por lo que incluimos varios
        const plataformasMapping = {
            'netflix': [8, 337, 175, 209], // Netflix en diferentes regiones
            'prime': [9, 119, 10, 119], // Amazon Prime Video en diferentes regiones
            'disney': [337, 390, 2, 25, 142, 143], // Disney+ en diferentes regiones
            'hbo': [118, 384, 3, 27, 29, 31, 33, 35, 78, 569], // HBO, Max, etc. en diferentes regiones
        };

        console.log('Verificando plataforma:', plataforma, 'para la película:', movie.title || movie.name);

        // Para depuración, mostrar un resumen de todos los proveedores
        if (movie.providers) {
            console.log('Resumen de proveedores disponibles:');
            ['flatrate', 'rent', 'buy'].forEach(categoria => {
                if (movie.providers[categoria]) {
                    console.log(`- ${categoria}:`, movie.providers[categoria].map(p =>
                        `${p.provider_name} (ID: ${p.provider_id})`).join(', '));
                }
            });
        }

        // Si no hay plataforma seleccionada o está vacía, devolvemos true para mostrar todas
        if (!plataforma || plataforma === '') {
            console.log('No hay plataforma seleccionada, mostrando película');
            return true;
        }

        // Si la película tiene información de proveedores
        if (movie.providers && (
            movie.providers.flatrate ||
            movie.providers.rent ||
            movie.providers.buy
        )) {
            // Obtener los IDs de proveedores para la plataforma seleccionada
            const idsPlataforma = plataformasMapping[plataforma] || [];
            console.log(`IDs para plataforma ${plataforma}:`, idsPlataforma);

            // Buscar en todas las categorías de disponibilidad (suscripción, alquiler, compra)
            const categorias = ['flatrate', 'rent', 'buy'];

            for (const categoria of categorias) {
                if (movie.providers[categoria] && movie.providers[categoria].length > 0) {
                    for (const provider of movie.providers[categoria]) {
                        // Para mayor flexibilidad, también verificamos por nombre
                        const nombreCoincide = this.coincideNombrePlataforma(provider.provider_name, plataforma);

                        if (idsPlataforma.includes(provider.provider_id) || nombreCoincide) {
                            console.log(`Película disponible en ${plataforma} (${categoria}) - Proveedor: ${provider.provider_name} (ID: ${provider.provider_id})`);
                            return true;
                        }
                    }
                }
            }

            // No se encontró la plataforma entre los proveedores disponibles
            console.log(`Película NO disponible en ${plataforma} - No se encontró coincidencia`);
            return false;
        }

        // Si no hay información de proveedores, asumimos disponible para no filtrar películas
        // por falta de datos (es mejor mostrar algo que no mostrar nada)
        console.log('No hay información de proveedores disponible, asumiendo disponible');
        return true;
    },

    // Función auxiliar para verificar si el nombre del proveedor coincide con la plataforma seleccionada
    coincideNombrePlataforma(nombreProveedor, plataforma) {
        if (!nombreProveedor) return false;

        const nombre = nombreProveedor.toLowerCase();

        switch (plataforma) {
            case 'netflix':
                return nombre.includes('netflix');
            case 'prime':
                return nombre.includes('prime') || nombre.includes('amazon');
            case 'disney':
                return nombre.includes('disney') || nombre.includes('disney+');
            case 'hbo':
                return nombre.includes('hbo') || nombre.includes('max') || nombre.includes('warner');
            default:
                return false;
        }
    },

    // Función para generar una recomendación completamente aleatoria
    async generarRecomendacionAleatoria(intentos = 0) {
        const container = document.getElementById("randomContainer");
        if (!container) {
            console.error('No se encontró el contenedor para mostrar la recomendación aleatoria');
            return;
        }

        const tipoContenidoInput = document.getElementById('tipoContenido');
        const tipoContenido = tipoContenidoInput ? tipoContenidoInput.value : 'movie';

        // Obtenemos la plataforma seleccionada según el tipo de contenido
        let plataformaSeleccionada = '';
        if (tipoContenido === 'movie') {
            const plataformaSelect = document.getElementById('plataforma');
            plataformaSeleccionada = plataformaSelect ? plataformaSelect.value : '';
        } else {
            const plataformaSerieSelect = document.getElementById('plataformaSerie');
            plataformaSeleccionada = plataformaSerieSelect ? plataformaSerieSelect.value : '';
        }

        console.log('Generando recomendación aleatoria para:', tipoContenido, 'en plataforma:', plataformaSeleccionada);

        // Limitar los reintentos a un máximo de 3 para evitar bucles infinitos
        if (intentos > 3) {
            console.warn('Se alcanzó el límite de intentos para encontrar contenido aleatorio en la plataforma seleccionada');
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${window.translations ? window.translations.no_results_in_platform : `No se encontró contenido disponible en ${plataformaSeleccionada}`}</p>
                    <button class="movie-btn primary" onclick="randomizerModule.generarRecomendacionSinFiltro()">
                        <i class="fas fa-dice"></i> ${window.translations ? window.translations.try_without_platform : 'Probar sin filtro de plataforma'}
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> ' + (window.translations ? window.translations.generating_recommendation : 'Generando recomendación aleatoria...') + '</div>';

        try {
            console.log(`Generando recomendación aleatoria para: ${tipoContenido} (intento ${intentos + 1})`);

            // Añadir un parámetro random para evitar resultados en caché
            const randomParam = Math.random();

            const response = await fetch(`/random/generate?tipoContenido=${tipoContenido}&_random=${randomParam}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let data;
            try {
                const responseText = await response.text();
                console.log('Respuesta del servidor (recomendación aleatoria):', responseText);

                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Error al parsear la respuesta de recomendación aleatoria:', e);
                throw new Error('Error al procesar la respuesta del servidor');
            }

            if (!response.ok) {
                throw new Error(data.message || 'Error al generar recomendación aleatoria');
            }

            if (data.data) {
                console.log('Recomendación aleatoria recibida correctamente');

                // Si hay una plataforma seleccionada, verificar que la película está disponible en esa plataforma
                if (plataformaSeleccionada && plataformaSeleccionada !== '') {
                    // Verificar si la película tiene información de proveedores
                    const movie = data.data;
                    const plataformaEncontrada = this.verificarPlataforma(movie, plataformaSeleccionada);

                    if (!plataformaEncontrada) {
                        console.log(`La película no está disponible en ${plataformaSeleccionada}, intentando otra aleatorio (intento ${intentos + 1})`);
                        // Si no está disponible en la plataforma seleccionada, intentar nuevamente
                        return this.generarRecomendacionAleatoria(intentos + 1);
                    }
                }

                this.renderRandomMovieFromAPI(data.data);
            } else {
                console.warn('No se encontraron resultados para la recomendación aleatoria');
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>No se encontraron resultados. Intente con otros criterios.</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error en la generación de recomendación aleatoria:', error);
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al generar la recomendación aleatoria: ${error.message}</p>
                </div>
            `;
        }
    },

    // Método para renderizar películas/series desde una API externa (TMDb)
    renderRandomMovieFromAPI(movie) {
        const container = document.getElementById("randomContainer");
        if (!container) {
            console.error('No se encontró el contenedor para renderizar la película/serie');
            return;
        }

        console.log('Datos recibidos de API para renderizar:', movie);

        // Determinar valores básicos según el tipo (película o serie)
        const isMovie = movie.media_type === 'movie';
        const title = isMovie ? movie.title : movie.name;
        const originalTitle = isMovie ? (movie.original_title !== movie.title ? movie.original_title : '') :
                                      (movie.original_name !== movie.name ? movie.original_name : '');
        const overview = movie.overview || 'Sin descripción disponible';
        const year = isMovie
            ? (movie.release_date ? movie.release_date.substring(0, 4) : 'Año desconocido')
            : (movie.first_air_date ? movie.first_air_date.substring(0, 4) : 'Año desconocido');

        // Duración: para películas es runtime, para series usamos episode_run_time
        let runtime = '';
        if (isMovie) {
            runtime = movie.runtime ? `${movie.runtime} min` : '';
        } else {
            // Para series podríamos mostrar duración promedio de episodios
            runtime = movie.episode_run_time && movie.episode_run_time.length > 0
                ? `${movie.episode_run_time[0]} min por episodio`
                : '';
        }

        // Información específica para series
        const seasonInfo = !isMovie && movie.number_of_seasons
            ? `${movie.number_of_seasons} temporada${movie.number_of_seasons !== 1 ? 's' : ''}`
            : '';

        // URL del póster con tamaño apropiado
        const posterUrl = movie.poster_path
            ? `${movie.base_image_url || 'https://image.tmdb.org/t/p/w500'}${movie.poster_path}`
            : '/img/no-poster.jpg';

        // URL del backdrop (imagen de fondo) si está disponible
        const backdropUrl = movie.backdrop_path
            ? `https://image.tmdb.org/t/p/w1280${movie.backdrop_path}`
            : null;

        // Información de calificación
        const rating = movie.vote_average ? (movie.vote_average / 2).toFixed(1) : '?';
        const voteCount = movie.vote_count ? `${movie.vote_count} votos` : '';

        // Obtener información de géneros
        let genresText = '';
        if (movie.genres && movie.genres.length > 0) {
            genresText = movie.genres.map(g => g.name).join(', ');
        }

        // Obtener información de producción
        const productionCountries = movie.production_countries && movie.production_countries.length > 0
            ? movie.production_countries.map(country => country.name).join(', ')
            : '';

        // Idioma original
        const originalLanguage = movie.original_language
            ? this.getLanguageName(movie.original_language)
            : '';

        // Reparto principal (si está disponible)
        let castInfo = '';
        if (movie.credits && movie.credits.cast && movie.credits.cast.length > 0) {
            const mainCast = movie.credits.cast.slice(0, 6);
            castInfo = `<div class="movie-cast">
                <h4><i class="fas fa-users"></i> ${window.translations ? window.translations.main_cast : 'Reparto principal'}</h4>
                <div class="cast-list">
                    ${mainCast.map(actor => `
                        <div class="cast-member">
                            <div class="cast-photo" style="background-image: url('${actor.profile_path
                                ? `https://image.tmdb.org/t/p/w185${actor.profile_path}`
                                : '/img/no-avatar.jpg'}')"></div>
                            <div class="cast-info">
                                <span class="cast-name" title="${actor.name}">${actor.name}</span>
                                <span class="cast-character" title="${actor.character || ''}">${actor.character || ''}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>`;
        }

        // Trailer (si está disponible)
        let trailerKey = '';
        if (movie.videos && movie.videos.results && movie.videos.results.length > 0) {
            // Buscar primero trailers oficiales
            const trailer = movie.videos.results.find(video =>
                video.type === 'Trailer' && video.site === 'YouTube' &&
                (video.name.toLowerCase().includes('oficial') || video.name.toLowerCase().includes('official'))
            ) || movie.videos.results.find(video =>
                video.type === 'Trailer' && video.site === 'YouTube'
            );

            if (trailer) {
                trailerKey = trailer.key;
            }
        }

        // Compañías de producción
        let productionCompanies = '';
        if (movie.production_companies && movie.production_companies.length > 0) {
            productionCompanies = `
                <div class="movie-companies">
                    <h4><i class="fas fa-building"></i> ${window.translations ? window.translations.production : 'Producción'}</h4>
                    <div class="companies-list">
                        ${movie.production_companies.slice(0, 3).map(company => `
                            <div class="company">
                                ${company.logo_path
                                    ? `<img src="https://image.tmdb.org/t/p/w92${company.logo_path}" alt="${company.name}">`
                                    : `<span class="company-name-only">${company.name}</span>`
                                }
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // Plataformas de streaming (si están disponibles)
        let streamingPlatforms = '';
        if (movie.providers) {
            console.log('Información de plataformas:', movie.providers);

            // Plataformas de flatrate (suscripción)
            let flatrateProviders = '';
            if (movie.providers.flatrate && movie.providers.flatrate.length > 0) {
                flatrateProviders = `
                    <div class="providers-section">
                        <h5><i class="fas fa-tv"></i> ${window.translations ? window.translations.available_on_subscription : 'Disponible en suscripción:'}</h5>
                        <div class="providers-list">
                            ${movie.providers.flatrate.map(provider => `
                                <div class="provider-item" title="${provider.provider_name}">
                                    <img src="https://image.tmdb.org/t/p/original${provider.logo_path}" alt="${provider.provider_name}">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Plataformas de alquiler
            let rentProviders = '';
            if (movie.providers.rent && movie.providers.rent.length > 0) {
                rentProviders = `
                    <div class="providers-section">
                        <h5><i class="fas fa-ticket-alt"></i> ${window.translations ? window.translations.available_for_rent : 'Disponible para alquilar:'}</h5>
                        <div class="providers-list">
                            ${movie.providers.rent.map(provider => `
                                <div class="provider-item" title="${provider.provider_name}">
                                    <img src="https://image.tmdb.org/t/p/original${provider.logo_path}" alt="${provider.provider_name}">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Plataformas de compra
            let buyProviders = '';
            if (movie.providers.buy && movie.providers.buy.length > 0) {
                buyProviders = `
                    <div class="providers-section">
                        <h5><i class="fas fa-shopping-cart"></i> ${window.translations ? window.translations.available_for_purchase : 'Disponible para comprar:'}</h5>
                        <div class="providers-list">
                            ${movie.providers.buy.map(provider => `
                                <div class="provider-item" title="${provider.provider_name}">
                                    <img src="https://image.tmdb.org/t/p/original${provider.logo_path}" alt="${provider.provider_name}">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Si hay algún proveedor disponible, mostrar la sección
            if (flatrateProviders || rentProviders || buyProviders) {
                streamingPlatforms = `
                    <div class="movie-streaming">
                        <h4><i class="fas fa-play-circle"></i> ${window.translations ? window.translations.where_to_watch : 'Dónde ver'}</h4>
                        ${flatrateProviders}
                        ${rentProviders}
                        ${buyProviders}
                        ${movie.providers.link ?
                            `<a href="${movie.providers.link}" target="_blank" class="providers-link">
                                <i class="fas fa-external-link-alt"></i> ${window.translations ? window.translations.see_all_options : 'Ver todas las opciones'}
                            </a>` : ''
                        }
                    </div>
                `;
            }
        }

        console.log(`Renderizando ${isMovie ? 'película' : 'serie'}: ${title} (${year})`);

        // Mostrar una animación para suavizar la transición
        container.style.opacity = 0;

        // Construir el HTML con estilo mejorado
        setTimeout(() => {
            container.innerHTML = `
                <div class="random-movie" ${backdropUrl ? `style="background: linear-gradient(rgba(15, 15, 20, 0.85), rgba(15, 15, 20, 0.95)), url('${backdropUrl}') no-repeat center/cover;"` : ''}>
                    <img src="${posterUrl}" alt="${title}" onerror="this.src='/img/no-poster.jpg'">
                    <div class="random-movie-info">
                        <h3>${title} ${originalTitle ? `<span class="original-title">(${originalTitle})</span>` : ''}</h3>

                        <div class="movie-meta">
                            <span><i class="fas fa-star"></i> ${rating}/5</span>
                            ${voteCount ? `<span><i class="fas fa-users"></i> ${voteCount}</span>` : ''}
                            <span><i class="fas fa-calendar"></i> ${year}</span>
                            ${runtime ? `<span><i class="fas fa-clock"></i> ${runtime}</span>` : ''}
                            ${seasonInfo ? `<span><i class="fas fa-layer-group"></i> ${seasonInfo}</span>` : ''}
                            <span><i class="fas fa-film"></i> ${isMovie ? 'Película' : 'Serie'}</span>
                            ${originalLanguage ? `<span><i class="fas fa-language"></i> ${originalLanguage}</span>` : ''}
                        </div>

                        ${genresText ? `<div class="movie-genres"><i class="fas fa-theater-masks"></i> ${genresText}</div>` : ''}
                        ${productionCountries ? `<div class="movie-countries"><i class="fas fa-globe-americas"></i> ${productionCountries}</div>` : ''}

                        <p class="movie-description">${overview}</p>

                        ${streamingPlatforms}

                        ${castInfo}

                        ${productionCompanies}

                        <div class="movie-actions">
                            ${trailerKey ?
                                `<button class="movie-btn primary" onclick="event.stopPropagation(); window.open('https://www.youtube.com/watch?v=${trailerKey}', '_blank')">
                                    <i class="fab fa-youtube"></i> <span>${window.translations ? window.translations.watch_trailer : 'Ver Trailer'}</span>
                                </button>` :
                                `<button class="movie-btn primary" onclick="event.stopPropagation(); window.open('https://www.youtube.com/results?search_query=${encodeURIComponent(title + ' ' + year + ' trailer')}', '_blank')">
                                    <i class="fab fa-youtube"></i> <span>${window.translations ? window.translations.search_trailer : 'Buscar Trailer'}</span>
                                </button>`
                            }
                            <button class="movie-btn secondary" onclick="event.stopPropagation(); randomizerModule.generarRecomendacion()">
                                <i class="fas fa-dice"></i> <span>${window.translations ? window.translations.new_recommendation : 'Nueva Recomendación'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Mostrar con animación
            setTimeout(() => {
                container.style.opacity = 1;
                container.style.transition = 'opacity 0.5s ease';
            }, 100);
        }, 300);
    },

    // Función auxiliar para obtener el nombre del idioma
    getLanguageName(languageCode) {
        // Si tenemos traducciones disponibles desde Laravel, usarlas primero
        if (window.translations && window.translations[`lang_${languageCode}`]) {
            return window.translations[`lang_${languageCode}`];
        }

        // Fallback a las traducciones locales si no hay traducciones disponibles
        const languages = {
            'en': 'Inglés',
            'es': 'Español',
            'fr': 'Francés',
            'de': 'Alemán',
            'it': 'Italiano',
            'ja': 'Japonés',
            'ko': 'Coreano',
            'ru': 'Ruso',
            'zh': 'Chino',
            'hi': 'Hindi',
            'pt': 'Portugués'
        };

        return languages[languageCode] || languageCode;
    },

    // Mantener este método para compatibilidad, ahora redirige al nuevo método
    renderRandomMovie(movie) {
        this.renderRandomMovieFromAPI(movie);
    },

    populateGenres() {
        const generoSelect = document.getElementById("genero");
        if (!generoSelect) return;

        fetch(`${config.BASE_URL}/genre/movie/list?api_key=${config.API_KEY}&language=es`)
            .then(res => res.json())
            .then(data => {
                data.genres.forEach((genre) => {
                    const option = document.createElement("option");
                    option.value = genre.id;
                    option.textContent = genre.name;
                    generoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar géneros:", error));
    },

    populateYears() {
        const yearSelects = document.querySelectorAll('#anio, #anioSerie');
        if (!yearSelects.length) {
            console.warn('No se encontraron selectores de año para poblar');
            return;
        }

        const currentYear = new Date().getFullYear();
        console.log(`Poblando años desde ${currentYear} hasta 1980`);

        yearSelects.forEach(select => {
            for (let year = currentYear; year >= 1980; year--) {
                const option = document.createElement("option");
                option.value = year;
                option.textContent = year;
                select.appendChild(option);
            }
        });
    }
};

// Exportar funciones globales para los manejadores de eventos en línea
window.generarRecomendacion = (e) => randomizerModule.generarRecomendacion(e);
window.toggleFavorite = (e, movieId) => favorites.toggleFavorite(e, movieId);
window.generarRecomendacionAleatoria = () => randomizerModule.generarRecomendacionAleatoria();
window.generarRecomendacionSinFiltro = () => randomizerModule.generarRecomendacionSinFiltro();

// Exponer el módulo al objeto window para que sea accesible desde HTML
window.randomizerModule = randomizerModule;

export default randomizerModule;
