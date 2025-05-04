// Variable global para selección de calificación
let selectedRating = 0;

document.addEventListener("DOMContentLoaded", function() {
    // Inicializar todos los componentes de la página
    initializeMovieDetails();
});

/**
 * Inicializa todos los componentes de la página de detalles de película
 */
function initializeMovieDetails() {
    // Sistema de tabs
    setupTabs();

    // Configurar películas similares
    setupSimilarMovies();

    // Sistema de comentarios
    setupReviews();

    // Cargar estado de favoritos
    loadFavoriteStatus();

    // Configurar botones principales de acción
    setupMainActionButtons();

    // Inicializar el sistema de comentarios legacy
    initComentarios();

    // Configurar botón volver arriba
    setupBackToTop();
}

/**
 * Configura los botones principales de acción (favorito, watchlist, compartir)
 */
function setupMainActionButtons() {
    const btnFavorite = document.querySelector('.action-buttons .btn-favorite');
    const btnWatchlist = document.querySelector('.action-buttons .btn-watchlist');
    const btnShare = document.querySelector('.action-buttons .btn-share');

    // Detectar el ID de la película desde la URL o del formulario
    const peliculaId = document.querySelector('#review-form input[name="id_pelicula"]')?.value
                     || window.location.pathname.split('/').pop();

    if (btnFavorite) {
        btnFavorite.addEventListener('click', function() {
            toggleFavorite(this, peliculaId);
        });
    }

    if (btnWatchlist) {
        btnWatchlist.addEventListener('click', function() {
            toggleWatchlist(this, peliculaId);
        });
    }

    if (btnShare) {
        btnShare.addEventListener('click', function() {
            shareMovie(peliculaId);
        });
    }
}

/**
 * Configura el botón volver arriba
 */
function setupBackToTop() {
    const backBtn = document.querySelector('.back-button .btn');

    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            // Prevenir comportamiento predeterminado solo si se quiere añadir animación
            // e.preventDefault();
            // window.scrollTo({ top: 0, behavior: 'smooth' });
            // setTimeout(() => { window.location = this.getAttribute('href'); }, 500);
        });
    }
}

/**
 * Añade películas a la watchlist
 */
function toggleWatchlist(button, movieId) {
    // Simulación de funcionalidad
    button.classList.toggle('active');

    if (button.classList.contains('active')) {
        button.querySelector('i').className = 'fas fa-bookmark';
        showNotification('Película añadida a "Ver más tarde"', 'success');
    } else {
        button.querySelector('i').className = 'far fa-bookmark';
        showNotification('Película eliminada de "Ver más tarde"', 'info');
    }
}

/**
 * Comparte la película en redes sociales
 */
function shareMovie(movieId) {
    // Obtener información de la película
    const title = document.querySelector('.titulo-overlay h1').textContent;
    const url = window.location.href;

    // Para implementación futura con Web Share API
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).then(() => {
            showNotification('¡Compartido con éxito!', 'success');
        }).catch(err => {
            console.error('Error al compartir:', err);
            showNotification('Error al compartir', 'error');
        });
    } else {
        // Fallback para navegadores que no soportan Web Share API
        try {
            // Copiar enlace al portapapeles
            navigator.clipboard.writeText(url).then(() => {
                showNotification('Enlace copiado al portapapeles', 'success');
            });
        } catch (err) {
            console.error('Error al copiar:', err);
            showNotification('No se pudo copiar el enlace', 'error');
        }
    }
}

    // Función para cargar comentarios/críticas
    function cargarComentarios() {
        const tmdbId = document.querySelector('input[name="id_pelicula"]').value;
        fetch(`/api/comentarios/tmdb/${tmdbId}/pelicula`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los comentarios');
                }
                return response.json();
            })
            .then(comentarios => {
                const container = document.querySelector('.reviews-list');
                container.innerHTML = '';

                if (comentarios.length === 0) {
                    container.innerHTML = '<p class="no-reviews">No hay críticas aún. ¡Sé el primero en escribir una!</p>';
                    return;
                }

                comentarios.forEach(comentario => {
                    const comentarioHtml = `
                        <div class="review ${comentario.es_spoiler ? 'spoiler' : ''}">
                            <div class="review-header">
                                <div class="user-info">
                                    <img src="${comentario.usuario.foto_perfil ? '/storage/' + comentario.usuario.foto_perfil : '/images/default-avatar.png'}"
                                         alt="${comentario.usuario.name}"
                                         class="avatar">
                                    <div>
                                        <span class="username">${comentario.usuario.name}</span>
                                        <span class="review-date">${new Date(comentario.created_at).toLocaleDateString()}</span>
                                    </div>
                                </div>
                            </div>
                            ${comentario.es_spoiler ?
                                `<div class="spoiler-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Esta crítica contiene spoilers
                                    <button class="show-spoiler">Mostrar spoiler</button>
                                </div>` : ''
                            }
                            <div class="review-content ${comentario.es_spoiler ? 'spoiler-content' : ''}">${comentario.comentario}</div>
                            <div class="review-actions">
                                <button class="btn-like" data-id="${comentario.id}">
                                    <i class="far fa-thumbs-up"></i>
                                    <span class="likes-count">${comentario.likes_count}</span>
                                </button>
                                <button class="btn-dislike" data-id="${comentario.id}">
                                    <i class="far fa-thumbs-down"></i>
                                    <span class="dislikes-count">${comentario.dislikes_count}</span>
                                </button>
                                <button class="btn-reply" data-id="${comentario.id}">
                                    <i class="far fa-comment"></i> Responder
                                </button>
                            </div>
                            <div class="respuestas-container" id="respuestas-${comentario.id}">
                                <!-- Las respuestas se cargarán aquí -->
                            </div>
                            <div class="respuesta-form" id="respuesta-form-${comentario.id}" style="display: none;">
                                <textarea placeholder="Escribe tu respuesta..." class="respuesta-text"></textarea>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="es_spoiler_respuesta_${comentario.id}">
                                    <label class="form-check-label" for="es_spoiler_respuesta_${comentario.id}">Esta respuesta contiene spoilers</label>
                                </div>
                                <button class="btn-submit-respuesta" data-id="${comentario.id}">Enviar respuesta</button>
                            </div>
                        </div>
                    `;
                    container.innerHTML += comentarioHtml;
                });

                // Agregar event listeners para los botones de like/dislike
                document.querySelectorAll('.btn-like, .btn-dislike').forEach(button => {
                    button.addEventListener('click', function() {
                        const comentarioId = this.dataset.id;
                        const tipo = this.classList.contains('btn-like') ? 'like' : 'dislike';

                        fetch('/api/likes_comentarios', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                user_id: document.querySelector('meta[name="user-id"]').content,
                                id_comentario: comentarioId,
                                tipo: tipo
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            cargarComentarios();
                        })
                        .catch(error => console.error('Error:', error));
                    });
                });

                // Agregar event listeners para mostrar spoilers
                document.querySelectorAll('.show-spoiler').forEach(button => {
                    button.addEventListener('click', function() {
                        const reviewEl = this.closest('.review');
                        const warningEl = reviewEl.querySelector('.spoiler-warning');
                        const contentEl = reviewEl.querySelector('.spoiler-content');

                        warningEl.style.display = 'none';
                        contentEl.style.display = 'block';
                    });
                });

                // Agregar event listeners para los botones de respuesta
                document.querySelectorAll('.btn-reply').forEach(button => {
                    button.addEventListener('click', function() {
                        const comentarioId = this.dataset.id;
                        const formRespuesta = document.getElementById(`respuesta-form-${comentarioId}`);
                        formRespuesta.style.display = formRespuesta.style.display === 'none' ? 'block' : 'none';
                    });
                });

                // Agregar event listeners para enviar respuestas
                document.querySelectorAll('.btn-submit-respuesta').forEach(button => {
                    button.addEventListener('click', function() {
                        const comentarioId = this.dataset.id;
                        const formRespuesta = document.getElementById(`respuesta-form-${comentarioId}`);
                        const respuestaText = formRespuesta.querySelector('.respuesta-text').value;
                        const esSpoiler = formRespuesta.querySelector(`#es_spoiler_respuesta_${comentarioId}`).checked;

                        if (!respuestaText.trim()) {
                            return;
                        }

                        // Obtener el token CSRF
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');

                        fetch('/api/respuestas-comentarios', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                comentario_id: comentarioId,
                                user_id: userId,
                                respuesta: respuestaText,
                                es_spoiler: esSpoiler
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al enviar la respuesta');
                            }
                            // Limpiar y ocultar el formulario de respuesta
                            formRespuesta.querySelector('.respuesta-text').value = '';
                            formRespuesta.querySelector(`#es_spoiler_respuesta_${comentarioId}`).checked = false;
                            formRespuesta.style.display = 'none';
                            // Recargar solo las respuestas de ese comentario
                            cargarRespuestas(comentarioId);
                        })
                        .catch(error => {
                            // También actualiza solo las respuestas aunque haya error
                            formRespuesta.querySelector('.respuesta-text').value = '';
                            formRespuesta.querySelector(`#es_spoiler_respuesta_${comentarioId}`).checked = false;
                            formRespuesta.style.display = 'none';
                            cargarRespuestas(comentarioId);
                        });
                    });
                });

                // Cargar respuestas para cada comentario
                comentarios.forEach(comentario => {
                    cargarRespuestas(comentario.id);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                const container = document.querySelector('.reviews-list');
                container.innerHTML = '<p class="no-reviews">Error al cargar las críticas. Por favor, intenta de nuevo más tarde.</p>';
            });
    }
}

/**
 * Configura los tabs de navegación en la página
 */
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const targetTab = e.target.dataset.tab;

            // Desactivar todos los tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));

            // Activar el tab seleccionado
            e.target.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

/**
 * Configura la interacción con las películas similares
 */
function setupSimilarMovies() {
    const similarContainer = document.querySelector('.related-movies-container');

    if (!similarContainer) return;

    // Delegación de eventos para botones de películas similares
    similarContainer.addEventListener('click', async (e) => {
        e.preventDefault();

        const button = e.target.closest('.action-btn');
        if (!button) return;

        const movieId = button.dataset.id;

        // Manejo de diferentes acciones según el botón
        if (button.classList.contains('btn-trailer')) {
            e.stopPropagation();
            await loadAndShowTrailer(movieId);
        } else if (button.classList.contains('btn-favorite')) {
            e.stopPropagation();
            toggleFavorite(button, movieId);
        } else if (button.classList.contains('btn-details')) {
            // La navegación se manejará a través del enlace <a>
            return true;
        }
    });
}

/**
 * Carga y muestra el trailer de una película - usando modal estático
 */
async function loadAndShowTrailer(movieId) {
    try {
        // Usar el modal estático existente en el HTML
        const trailerModal = document.getElementById('trailerModalStatic');
        const trailerContainer = document.getElementById('trailerContainerStatic');

        if (!trailerModal || !trailerContainer) {
            console.error('No se encontraron los elementos del modal');
            return;
        }

        // Mostrar el modal con indicador de carga
        trailerModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Mostrar spinner de carga
        trailerContainer.innerHTML = `
            <div class="trailer-loading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; background-color: #000;">
                <div class="spinner" style="width: 40px; height: 40px; border: 3px solid rgba(255, 255, 255, 0.2); border-top: 3px solid #14ff14; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
                <span style="font-size: 0.9rem; margin-top: 10px; color: rgba(255, 255, 255, 0.8);">Cargando trailer...</span>
            </div>
        `;

        // Configurar botón de cierre
        const closeBtn = document.getElementById('closeTrailerBtn');
        if (closeBtn) {
            // Eliminar manejadores existentes
            const newCloseBtn = closeBtn.cloneNode(true);
            closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);

            // Añadir nuevo manejador
            newCloseBtn.addEventListener('click', () => {
                // Detener el video primero
                trailerContainer.innerHTML = '';
                trailerModal.style.display = 'none';
                document.body.style.overflow = '';
            });
        }

        // Cerrar al hacer clic en el fondo
        trailerModal.addEventListener('click', (e) => {
            if (e.target === trailerModal) {
                trailerContainer.innerHTML = '';
                trailerModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Manejar tecla Escape
        const handleEsc = (e) => {
            if (e.key === 'Escape') {
                trailerContainer.innerHTML = '';
                trailerModal.style.display = 'none';
                document.body.style.overflow = '';
                document.removeEventListener('keydown', handleEsc);
            }
        };
        document.addEventListener('keydown', handleEsc);

        // Cargar datos del trailer
        const apiKey = document.querySelector('meta[name="tmdb-api-key"]')?.content || '';
        const response = await fetch(`https://api.themoviedb.org/3/movie/${movieId}/videos?api_key=${apiKey}&language=es-ES`);
        const data = await response.json();

        // Verificar si el contenedor sigue existiendo
        if (!document.body.contains(trailerContainer)) {
            return; // Modal ya cerrado
        }

        // Buscar trailer
        const trailers = data.results.filter(video =>
            video.site === 'YouTube' &&
            ['Trailer', 'Teaser'].includes(video.type)
        );

        if (trailers.length === 0) {
            trailerContainer.innerHTML = `
                <div class="no-trailer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #121212; color: white; text-align: center; padding: 20px;">
                    <i class="fas fa-video-slash" style="font-size: 3rem; color: #14ff14; margin-bottom: 20px; opacity: 0.7;"></i>
                    <p style="max-width: 80%; line-height: 1.5;">No se encontraron trailers disponibles para esta película.</p>
                </div>
            `;
            return;
        }

        // Crear iframe para el trailer
        const youtubeKey = trailers[0].key;
        trailerContainer.innerHTML = `
            <iframe
                width="100%"
                height="100%"
                src="https://www.youtube.com/embed/${youtubeKey}?autoplay=1&rel=0"
                title="YouTube video player"
                frameborder="0"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        `;

    } catch (error) {
        console.error('Error al cargar el trailer:', error);

        // Mostrar mensaje de error si el modal sigue abierto
        const trailerContainer = document.getElementById('trailerContainerStatic');
        if (trailerContainer && document.body.contains(trailerContainer)) {
            trailerContainer.innerHTML = `
                <div class="no-trailer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #121212; color: white; text-align: center; padding: 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff4d4d; margin-bottom: 20px; opacity: 0.7;"></i>
                    <p style="max-width: 80%; line-height: 1.5;">Error al cargar el trailer. Por favor, inténtalo de nuevo más tarde.</p>
                </div>
            `;
        }
    }
}

/**
 * Alterna el estado de favorito de una película
 */
async function toggleFavorite(button, movieId) {
    try {
        // Verificar si el usuario está autenticado
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            showNotification('Debes iniciar sesión para guardar favoritos.', 'warning');
            return;
        }

        // Mostrar estado de carga
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Determinar la acción (añadir o quitar)
        const isActive = button.classList.contains('active');
        const action = isActive ? 'remove' : 'add';

        // Enviar solicitud al servidor
        const response = await fetch('/api/favorites', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                movie_id: movieId,
                action: action
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Actualizar la interfaz
            if (action === 'add') {
                button.classList.add('active');
                button.querySelector('i').className = 'fas fa-heart';
                showNotification('Película añadida a favoritos', 'success');
            } else {
                button.classList.remove('active');
                button.querySelector('i').className = 'far fa-heart';
                showNotification('Película eliminada de favoritos', 'info');
            }
        } else {
            throw new Error(data.error || 'Error al procesar la solicitud');
        }
    } catch (error) {
        console.error('Error al gestionar favorito:', error);
        showNotification('Error al actualizar favoritos. Por favor, inténtalo de nuevo.', 'error');
    } finally {
        // Restaurar el botón
        if (button.disabled) {
            button.disabled = false;
            button.innerHTML = `<i class="${button.classList.contains('active') ? 'fas' : 'far'} fa-heart"></i>`;
        }

    // Manejar envío del formulario de crítica
    const comentarioForm = document.getElementById('comentarioForm');
    if (comentarioForm) {
        comentarioForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                user_id: document.querySelector('meta[name="user-id"]').content,
                tmdb_id: document.querySelector('input[name="id_pelicula"]').value,
                tipo: 'pelicula',
                comentario: formData.get('comentario'),
                es_spoiler: formData.get('es_spoiler') === 'on'
            };

            fetch('/api/comentarios', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al publicar la crítica');
                }
                return response.json();
            })
            .then(data => {
                this.reset();
                cargarComentarios();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

    }
}


/**
 * Carga el estado de favoritos del usuario
 */
async function loadFavoriteStatus() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) return; // Usuario no autenticado

        const response = await fetch('/api/user/favorites', {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (!response.ok) return;

        const data = await response.json();

        if (data.status === 'success') {
            // Convertir los favoritos a un array de IDs
            const favoriteIds = data.favorites.map(fav => fav.pelicula_id.toString());

            // Actualizar todos los botones de favoritos
            document.querySelectorAll('.btn-favorite').forEach(btn => {
                const movieId = btn.dataset.id.toString();
                if (favoriteIds.includes(movieId)) {
                    btn.classList.add('active');
                    btn.querySelector('i').className = 'fas fa-heart';
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar favoritos:', error);
    }
}


    // Cargar comentarios al iniciar
    cargarComentarios();

    // Función para cargar respuestas de un comentario
    function cargarRespuestas(comentarioId) {
        fetch(`/api/respuestas_comentarios/comentario/${comentarioId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar las respuestas');
                }
                return response.json();
            })
            .then(respuestas => {
                const container = document.getElementById(`respuestas-${comentarioId}`);
                container.innerHTML = '';

                if (respuestas.length === 0) {
                    return;
                }

                respuestas.forEach(respuesta => {
                    const respuestaHtml = `
                        <div class="respuesta ${respuesta.es_spoiler ? 'spoiler' : ''}">
                            <div class="respuesta-header">
                                <div class="user-info">
                                    <img src="${respuesta.usuario.foto_perfil ? '/storage/' + respuesta.usuario.foto_perfil : '/images/default-avatar.png'}"
                                         alt="${respuesta.usuario.name}"
                                         class="avatar">
                                    <div>
                                        <span class="username">${respuesta.usuario.name}</span>
                                        <span class="respuesta-date">${new Date(respuesta.created_at).toLocaleDateString()}</span>
                                    </div>
                                </div>
                            </div>
                            ${respuesta.es_spoiler ?
                                `<div class="spoiler-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Esta respuesta contiene spoilers
                                    <button class="show-spoiler">Mostrar spoiler</button>
                                </div>` : ''
                            }
                            <div class="respuesta-content ${respuesta.es_spoiler ? 'spoiler-content' : ''}">${respuesta.respuesta}</div>
                        </div>
                    `;
                    container.innerHTML += respuestaHtml;
                });

                // Agregar event listeners para mostrar spoilers en respuestas
                container.querySelectorAll('.show-spoiler').forEach(button => {
                    button.addEventListener('click', function() {
                        const respuestaEl = this.closest('.respuesta');
                        const warningEl = respuestaEl.querySelector('.spoiler-warning');
                        const contentEl = respuestaEl.querySelector('.spoiler-content');

                        warningEl.style.display = 'none';
                        contentEl.style.display = 'block';
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
});
