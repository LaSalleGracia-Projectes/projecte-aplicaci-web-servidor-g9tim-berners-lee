import config from '../utils/config.js';

const searchModule = {
    searchTimeout: null,
    currentSuggestionIndex: -1,
    lastQuery: '',

    init() {
        this.setupSearchListeners();
    },

    setupSearchListeners() {
        const searchInput = document.getElementById("search");
        const suggestionsContainer = document.getElementById("suggestions");

        if (!searchInput || !suggestionsContainer) return;

        // Manejo de entrada en el campo de búsqueda
        searchInput.addEventListener("input", () => {
            const query = searchInput.value.trim();

            // Reiniciar índice de selección
            this.currentSuggestionIndex = -1;

            // Cancelar búsqueda anterior si está en progreso
            if (this.searchTimeout) clearTimeout(this.searchTimeout);

            if (query.length > 2) {
                // Añadir indicador de carga
                suggestionsContainer.innerHTML = '<div class="loading-suggestions">Buscando...</div>';

                // Retraso para evitar muchas peticiones
                this.searchTimeout = setTimeout(() => {
                    // Solo buscar si el query ha cambiado
                    if (query !== this.lastQuery) {
                        this.lastQuery = query;
                        this.buscarSugerencias(query);
                    }
                }, 300);
            } else {
                suggestionsContainer.innerHTML = "";
            }
        });

        // Manejo de navegación con teclado
        searchInput.addEventListener("keydown", (e) => {
            const suggestions = document.querySelectorAll(".suggestion-item");

            if (suggestions.length === 0) return;

            if (e.key === "ArrowDown") {
                e.preventDefault();
                this.currentSuggestionIndex = (this.currentSuggestionIndex + 1) % suggestions.length;
                this.updateSuggestionActive(suggestions);
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                this.currentSuggestionIndex = (this.currentSuggestionIndex - 1 + suggestions.length) % suggestions.length;
                this.updateSuggestionActive(suggestions);
            } else if (e.key === "Enter") {
                if (this.currentSuggestionIndex >= 0) {
                    e.preventDefault();
                    suggestions[this.currentSuggestionIndex].click();
                } else if (searchInput.value.trim().length > 2) {
                    // Si no hay sugerencia seleccionada pero hay texto válido
                    this.buscarSugerencias(searchInput.value.trim()).then((results) => {
                        if (results && results.length > 0) {
                            window.location.href = `/infoPelicula/${results[0].id}`;
                        }
                    });
                }
            } else if (e.key === "Escape") {
                // Cerrar sugerencias con ESC
                suggestionsContainer.innerHTML = "";
                searchInput.blur();
            }
        });

        // Limpiar al doble clic en el campo
        searchInput.addEventListener("dblclick", () => {
            searchInput.value = "";
            suggestionsContainer.innerHTML = "";
            this.lastQuery = '';
        });

        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener("click", (e) => {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.innerHTML = "";
            }
        });
    },

    updateSuggestionActive(suggestions) {
        const suggestionsContainer = document.getElementById("suggestions");

        suggestions.forEach((item, idx) => {
            if (idx === this.currentSuggestionIndex) {
                item.classList.add("active");
                // Hacer scroll si es necesario
                if (item.offsetTop < suggestionsContainer.scrollTop ||
                    item.offsetTop + item.offsetHeight > suggestionsContainer.scrollTop + suggestionsContainer.offsetHeight) {
                    item.scrollIntoView({ block: "nearest" });
                }
            } else {
                item.classList.remove("active");
            }
        });
    },

    async buscarSugerencias(query) {
        try {
            const res = await fetch(`${config.BASE_URL}/search/movie?api_key=${config.API_KEY}&query=${encodeURIComponent(query)}&language=es`);

            if (!res.ok) {
                throw new Error(`Error de API: ${res.status}`);
            }

            const data = await res.json();
            this.renderSugerencias(data.results);
            return data.results;
        } catch (error) {
            console.error("Error buscando sugerencias:", error);
            document.getElementById("suggestions").innerHTML = '<div class="error-message">Error al buscar. Inténtalo de nuevo.</div>';
            return [];
        }
    },

    renderSugerencias(results) {
        const suggestionsContainer = document.getElementById("suggestions");
        if (!suggestionsContainer) return;

        suggestionsContainer.innerHTML = "";

        if (results.length === 0) {
            suggestionsContainer.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
            return;
        }

        // Agregar estilos CSS necesarios dinámicamente si no existen
        if (!document.getElementById('search-suggestion-styles')) {
            const style = document.createElement('style');
            style.id = 'search-suggestion-styles';
            style.textContent = `
                .search-suggestions {
                    max-height: 350px;
                    overflow-y: auto;
                    background: rgba(20, 20, 20, 0.95);
                    border: 1px solid var(--verde-neon);
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                }
                .suggestion-item {
                    display: flex;
                    align-items: center;
                    padding: 10px 15px;
                    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                }
                .suggestion-item:hover, .suggestion-item.active {
                    background: rgba(0, 255, 135, 0.1);
                }
                .suggestion-poster {
                    width: 40px;
                    height: 60px;
                    margin-right: 10px;
                    overflow: hidden;
                    border-radius: 5px;
                    flex-shrink: 0;
                }
                .suggestion-poster img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .suggestion-info {
                    flex-grow: 1;
                }
                .suggestion-title {
                    font-weight: 600;
                    margin-bottom: 3px;
                }
                .suggestion-rating {
                    font-size: 0.8em;
                    color: #888;
                }
                .suggestion-rating i {
                    color: #ffc107;
                    margin-right: 3px;
                }
                .ver-todos-resultados {
                    padding: 12px 15px;
                    background: rgba(0, 255, 135, 0.15);
                    text-align: center;
                    font-weight: 600;
                    cursor: pointer;
                    border-top: 1px solid rgba(0, 255, 135, 0.3);
                }
                .ver-todos-resultados:hover {
                    background: rgba(0, 255, 135, 0.25);
                }
                .loading-suggestions, .no-results, .error-message {
                    padding: 15px;
                    text-align: center;
                    color: #888;
                    font-style: italic;
                }
                .error-message {
                    color: #ff6b6b;
                }
            `;
            document.head.appendChild(style);
        }

        results.slice(0, 5).forEach((movie) => {
            const item = document.createElement("div");
            item.classList.add("suggestion-item");

            // Crear estructura para incluir imagen y título
            const posterPath = movie.poster_path
                ? `${config.IMG_URL}${movie.poster_path}`
                : 'img/no-poster.jpg';

            const year = movie.release_date ? ` (${movie.release_date.substring(0, 4)})` : '';

            item.innerHTML = `
                <div class="suggestion-poster">
                    <img src="${posterPath}" alt="${movie.title}" onerror="this.src='img/no-poster.jpg'">
                </div>
                <div class="suggestion-info">
                    <div class="suggestion-title">${movie.title}${year}</div>
                    <div class="suggestion-rating">
                        <i class="fas fa-star"></i> ${movie.vote_average || 'N/A'}
                    </div>
                </div>
            `;

            item.addEventListener("click", () => {
                window.location.href = `/infoPelicula/${movie.id}`;
            });

            suggestionsContainer.appendChild(item);
        });

        // Añadir enlace para ver todos los resultados
        const verTodos = document.createElement("div");
        verTodos.classList.add("ver-todos-resultados");
        verTodos.innerHTML = `<i class="fas fa-search"></i> Ver todos los resultados`;
        verTodos.addEventListener("click", () => {
            const searchInput = document.getElementById("search");
            window.location.href = `/busqueda?q=${encodeURIComponent(searchInput.value.trim())}`;
        });

        suggestionsContainer.appendChild(verTodos);
    }
};

export default searchModule;
