// Funciones para la gestión de listas
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la página de creación de lista
    if (document.getElementById('userId')) {
        const user = JSON.parse(localStorage.getItem('user'));

        if (!user || !user.id) {
            window.location.href = '/';
            return;
        }

        document.getElementById('userId').value = user.id;
    }

    // Contador de caracteres para el nombre
    const nombreInput = document.getElementById('nombre_lista');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length > 100) {
                this.value = this.value.substring(0, 100);
            }
        });
    }

    // Contador de caracteres para la descripción
    const descripcionTextarea = document.getElementById('descripcion');
    if (descripcionTextarea) {
        descripcionTextarea.addEventListener('input', function() {
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
            }
        });
    }
});

class MovieSearch {
    constructor() {
        this.searchTimeout = null;
        this.lastQuery = '';
        this.currentSuggestionIndex = -1;
        this.config = {
            BASE_URL: 'https://api.themoviedb.org/3',
            API_KEY: document.querySelector('meta[name="tmdb-api-key"]').getAttribute('content')
        };
        this.init();
    }

    init() {
        this.setupSearchListeners();
    }

    setupSearchListeners() {
        const searchInput = document.getElementById("listMovieSearch");
        const suggestionsContainer = document.getElementById("listSuggestions");

        if (!searchInput || !suggestionsContainer) return;

        searchInput.addEventListener("input", () => {
            const query = searchInput.value.trim();
            this.currentSuggestionIndex = -1;

            if (this.searchTimeout) clearTimeout(this.searchTimeout);

            if (query.length > 2) {
                suggestionsContainer.innerHTML = '<div class="loading-suggestions">Buscando...</div>';
                suggestionsContainer.classList.add('active');

                this.searchTimeout = setTimeout(() => {
                    if (query !== this.lastQuery) {
                        this.lastQuery = query;
                        this.buscarSugerencias(query);
                    }
                }, 300);
            } else {
                suggestionsContainer.innerHTML = "";
                suggestionsContainer.classList.remove('active');
            }
        });

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
                }
            } else if (e.key === "Escape") {
                suggestionsContainer.innerHTML = "";
                suggestionsContainer.classList.remove('active');
                searchInput.blur();
            }
        });

        document.addEventListener("click", (e) => {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.innerHTML = "";
                suggestionsContainer.classList.remove('active');
            }
        });
    }

    updateSuggestionActive(suggestions) {
        const suggestionsContainer = document.getElementById("listSuggestions");

        suggestions.forEach((item, idx) => {
            if (idx === this.currentSuggestionIndex) {
                item.classList.add("active");
                if (item.offsetTop < suggestionsContainer.scrollTop ||
                    item.offsetTop + item.offsetHeight > suggestionsContainer.scrollTop + suggestionsContainer.offsetHeight) {
                    item.scrollIntoView({ block: "nearest" });
                }
            } else {
                item.classList.remove("active");
            }
        });
    }

    async buscarSugerencias(query) {
        try {
            const res = await fetch(`${this.config.BASE_URL}/search/movie?api_key=${this.config.API_KEY}&query=${encodeURIComponent(query)}&language=es`);

            if (!res.ok) {
                throw new Error(`Error de API: ${res.status}`);
            }

            const data = await res.json();
            this.renderSugerencias(data.results);
            return data.results;
        } catch (error) {
            console.error("Error buscando sugerencias:", error);
            document.getElementById("listSuggestions").innerHTML = '<div class="error-message">Error al buscar. Inténtalo de nuevo.</div>';
            return [];
        }
    }

    renderSugerencias(results) {
        const suggestionsContainer = document.getElementById("listSuggestions");
        if (!suggestionsContainer) return;

        suggestionsContainer.innerHTML = "";

        if (results.length === 0) {
            suggestionsContainer.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
            return;
        }

        results.forEach(movie => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `
                <img src="https://image.tmdb.org/t/p/w92${movie.poster_path}" alt="${movie.title}">
                <div class="suggestion-info">
                    <h4>${movie.title}</h4>
                    <p>${movie.release_date ? movie.release_date.split('-')[0] : 'N/A'}</p>
                </div>
            `;
            div.addEventListener('click', () => this.addMovieToList(movie));
            suggestionsContainer.appendChild(div);
        });
    }

    addMovieToList(movie) {
        const listaId = document.querySelector('meta[name="lista-id"]').getAttribute('content');

        fetch('/api/contenido-listas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id_lista: listaId,
                tmdb_id: movie.id,
                tipo: 'movie'
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error desconocido');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                location.reload();
            } else {
                console.error('Respuesta del servidor:', data);
                alert('Error al añadir la película a la lista. Por favor, revisa la consola para más detalles.');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            console.error('Datos enviados:', {
                id_lista: listaId,
                tmdb_id: movie.id,
                tipo: 'movie'
            });
            alert(`Error al añadir la película a la lista: ${error.message}`);
        });
    }
}

// Inicializar el buscador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new MovieSearch();

    // Eliminar película de la lista
    document.querySelectorAll('.remove-movie').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar esta película de la lista?')) {
                fetch(`/api/contenido-listas/${this.dataset.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.message) {
                        location.reload();
                    } else {
                        alert('Error al eliminar la película de la lista');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar la película de la lista');
                });
            }
        });
    });
});
