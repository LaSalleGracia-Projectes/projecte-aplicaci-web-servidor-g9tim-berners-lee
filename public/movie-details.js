// Variable global para selección de calificación
let selectedRating = 0;

document.addEventListener("DOMContentLoaded", function() {
    console.log("Inicializando página de detalles de película...");
    // Inicializar todos los componentes de la página
    initializeMovieDetails();
});

/**
 * Muestra una notificación al usuario
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    console.log(`Mostrando notificación: ${message} (tipo: ${type})`);

    // Verificar si existe el contenedor de notificaciones, sino crearlo
    let notificationContainer = document.getElementById('notifications-container');

    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notifications-container';
        notificationContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 90vw;';
        document.body.appendChild(notificationContainer);
    }

    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        margin-bottom: 10px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        animation: fadeIn 0.3s ease-out;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 280px;
        max-width: 400px;
        position: relative;
        overflow: hidden;
    `;

    // Configurar color según tipo
    switch (type) {
        case 'success':
            notification.style.backgroundColor = '#14ff14';
            notification.style.color = '#000';
            break;
        case 'error':
            notification.style.backgroundColor = '#ff4d4d';
            break;
        case 'warning':
            notification.style.backgroundColor = '#ffbb33';
            notification.style.color = '#000';
            break;
        default: // info
            notification.style.backgroundColor = '#33b5e5';
            break;
    }

    // Contenido de la notificación
    const content = document.createElement('div');
    content.style.flex = '1';
    content.textContent = message;

    // Botón de cierre
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '&times;';
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: inherit;
        font-size: 20px;
        margin-left: 10px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    `;
    closeBtn.addEventListener('mouseover', () => closeBtn.style.opacity = '1');
    closeBtn.addEventListener('mouseout', () => closeBtn.style.opacity = '0.7');
    closeBtn.addEventListener('click', () => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    });

    // Añadir elementos a la notificación
    notification.appendChild(content);
    notification.appendChild(closeBtn);

    // Añadir notificación al contenedor
    notificationContainer.appendChild(notification);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(20px)';
            notification.style.transition = 'opacity 0.3s, transform 0.3s';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

/**
 * Inicializa todos los componentes de la página de detalles de película
 */
function initializeMovieDetails() {
    const movieId = document.querySelector('meta[name="movie-id"]')?.content;
    if (!movieId) {
        console.error('No se encontró el ID de la película');
        return;
    }

    // Cargar estado de favoritos
    loadFavoriteStatus(movieId);

    // Inicializar sistema de comentarios
    initComentarios();

    // Inicializar otros elementos
    initializeTrailerButtons();
    setupSimilarMovies();
    initializeTabs();

    // Actualizar notificaciones al cargar la página
    actualizarNotificaciones();
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

/**
 * Inicializa el sistema de comentarios
 */
function initComentarios() {
    // Cargar comentarios al iniciar
    cargarComentarios(false);

    // Configurar el formulario de comentarios
    const comentarioForm = document.getElementById('comentarioForm');

    if (!comentarioForm) {
        console.error('Error: No se encontró el formulario de comentarios');
        return;
    }

    comentarioForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Obtener token CSRF y user ID de manera segura
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const userIdMeta = document.querySelector('meta[name="user-id"]');

        if (!csrfTokenMeta || !userIdMeta) {
            showNotification('Error: Falta información de autenticación. Por favor, inicia sesión de nuevo.', 'error');
            console.error('Error: No se encontraron los meta tags necesarios (csrf-token o user-id)');
            return;
        }

        const csrfToken = csrfTokenMeta.content;
        const userId = userIdMeta.content;

        if (!csrfToken || !userId) {
            showNotification('Debes iniciar sesión para publicar comentarios', 'error');
            return;
        }

        const idPeliculaInput = document.querySelector('input[name="id_pelicula"]');
        const comentarioTextarea = document.querySelector('textarea[name="comentario"]');
        const esSpoilerCheckbox = document.getElementById('es_spoiler');

        if (!idPeliculaInput || !comentarioTextarea || !esSpoilerCheckbox) {
            showNotification('Error: Formulario incompleto', 'error');
            console.error('Error: No se encontraron todos los campos del formulario');
            return;
        }

        const tmdbId = idPeliculaInput.value;
        const comentario = comentarioTextarea.value;
        const esSpoiler = esSpoilerCheckbox.checked;

        if (!tmdbId) {
            showNotification('Error: ID de película no válido', 'error');
            return;
        }

        if (!comentario.trim()) {
            showNotification('El comentario no puede estar vacío', 'error');
            return;
        }

        // Mostrar indicador de carga
        showNotification('Enviando comentario...', 'info');

        // Construir URL absoluta para eviar problemas de rutas
        const baseUrl = window.location.origin;
        const postComentarioUrl = `${baseUrl}/api/comentarios`;
        console.log("Enviando comentario a:", postComentarioUrl);

        fetch(postComentarioUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                user_id: userId,
                tmdb_id: tmdbId,
                tipo: 'pelicula',
                comentario: comentario,
                es_spoiler: esSpoiler
            })
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422 || response.status === 500) {
                    // Si hay un error en el backend pero el comentario se guardó
                    console.log('El comentario parece haberse enviado a pesar del error');
                    comentarioTextarea.value = '';
                    esSpoilerCheckbox.checked = false;
                    selectedRating = 0;

                    // Actualizar la interfaz con el nuevo comentario sin recargar la página
                    const userName = document.querySelector('meta[name="user-name"]')?.content || 'Usuario';
                    const userAvatar = document.querySelector('meta[name="user-avatar"]')?.content || '';

                    // Intentar cargar los comentarios para obtener el recién creado
                    cargarComentarios();

                    showNotification('El comentario se ha guardado pero hubo un error. La página se actualizará en 5 segundos.', 'warning');

                    // Programar una recarga de la página en 5 segundos como salvaguarda
                    setTimeout(() => {
                        location.reload();
                    }, 5000);

                    return Promise.reject('Error de validación pero el comentario puede haberse guardado');
                }
                return Promise.reject('Error al enviar el comentario');
            }
            return response.json();
        })
        .then(data => {
            comentarioTextarea.value = '';
            esSpoilerCheckbox.checked = false;
            selectedRating = 0;
            showNotification('Comentario publicado correctamente', 'success');
            cargarComentarios();
        })
        .catch(error => {
            console.error('Error:', error);
            if (error === 'Error de validación pero el comentario puede haberse guardado') {
                // Ya manejamos este caso arriba
                return;
            }
            showNotification('Error al enviar el comentario. Inténtalo más tarde.', 'error');
        });
    });
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
    }
}

/**
 * Carga el estado de favoritos del usuario
 */
async function loadFavoriteStatus(movieId) {
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

/**
 * Configura el botón de depuración para ayudar a identificar problemas con los comentarios
 */
function setupDebugButton() {
    const debugBtn = document.getElementById('debug-comentarios');
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            console.log('=== DEPURACIÓN DE COMENTARIOS ===');

            // Información general
            console.log('URL actual:', window.location.href);
            console.log('Origin:', window.location.origin);

            // Verificar tokens
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const userId = document.querySelector('meta[name="user-id"]')?.content;
            console.log('CSRF Token:', csrfToken ? 'Presente' : 'No encontrado');
            console.log('User ID:', userId || 'No encontrado');

            // Verificar ID de película
            const idPeliculaInput = document.querySelector('input[name="id_pelicula"]');
            console.log('Input ID película:', idPeliculaInput ? 'Presente' : 'No encontrado');
            if (idPeliculaInput) {
                console.log('Valor ID película:', idPeliculaInput.value);
            }

            // Verificar contenedor de comentarios
            const reviewsContainer = document.querySelector('.reviews-list');
            console.log('Contenedor de comentarios:', reviewsContainer ? 'Presente' : 'No encontrado');

            // Intentar cargar comentarios de nuevo
            console.log('Intentando cargar comentarios de nuevo...');
            cargarComentarios();

            showNotification('Información de depuración en la consola', 'info');
        });
    }
}

function cargarComentarios(scrollToComments = false) {
    console.log("Iniciando carga de comentarios...");

    // Verificar primero si hay un elemento de id_pelicula en el formulario
    const idPeliculaInput = document.querySelector('input[name="id_pelicula"]');

    if (!idPeliculaInput) {
        console.error('Error: No se encontró el input con el ID de la película');
        const container = document.querySelector('.reviews-list');
        if (container) {
            container.innerHTML = '<p class="no-reviews">Error al cargar comentarios: falta información de la película</p>';
        }
        return;
    }

    const tmdbId = idPeliculaInput.value;
    console.log("ID de película encontrado:", tmdbId);

    if (!tmdbId) {
        console.error('Error: ID de película vacío');
        return;
    }

    // Mostrar estado de carga
    const container = document.querySelector('.reviews-list');
    if (container) {
        container.innerHTML = `
            <div class="loading-comentarios">
                <div class="spinner"></div>
                <p>Cargando comentarios...</p>
            </div>
        `;
    }

    // Construir URL absoluta con la ruta correcta para obtener comentarios
    const baseUrl = window.location.origin;
    const comentariosUrl = `${baseUrl}/api/comentarios/tmdb/${tmdbId}/pelicula`;
    console.log("URL de comentarios:", comentariosUrl);

    fetch(comentariosUrl)
        .then(response => {
            console.log("Respuesta del servidor:", response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`Error al cargar los comentarios. Estado: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos del servidor:", data);

            // Asegurarnos de que trabajamos con un array de comentarios
            // La respuesta puede ser un array directamente o un objeto con una propiedad que contiene el array
            let comentarios = Array.isArray(data) ? data :
                             (data.comentarios ? data.comentarios :
                             (data.data ? data.data : []));

            console.log("Comentarios procesados:", comentarios);

            const container = document.querySelector('.reviews-list');
            if (!container) {
                console.error('Error: No se encontró el contenedor para las críticas');
                return;
            }

            container.innerHTML = '';

            if (!Array.isArray(comentarios) || comentarios.length === 0) {
                console.log("No hay comentarios disponibles");
                container.innerHTML = '<p class="no-reviews">No hay críticas aún. ¡Sé el primero en escribir una!</p>';
                return;
            }

            comentarios.forEach(comentario => {
                // Verificar que el comentario tiene una estructura válida
                if (!comentario || !comentario.id) {
                    console.warn('Comentario inválido omitido:', comentario);
                    return;
                }

                // Asegurarse de que todos los campos necesarios están presentes
                const usuario = comentario.usuario || {};
                const esSpoiler = comentario.es_spoiler || false;
                const likes = comentario.likes_count || 0;
                const dislikes = comentario.dislikes_count || 0;

                // Estado del like del usuario actual
                const userLikeStatus = comentario.user_like_status || 'none';

                // Determinar las clases e iconos para los botones de like/dislike
                const likeActiveClass = userLikeStatus === 'like' ? 'active' : '';
                const dislikeActiveClass = userLikeStatus === 'dislike' ? 'active' : '';
                const likeIcon = userLikeStatus === 'like' ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
                const dislikeIcon = userLikeStatus === 'dislike' ? 'fas fa-thumbs-down' : 'far fa-thumbs-down';

                // Crear elemento de comentario
                const reviewEl = document.createElement('div');
                reviewEl.className = `review ${esSpoiler ? 'spoiler' : ''}`;

                // Contenido del comentario
                reviewEl.innerHTML = `
                    <div class="review-header">
                        <div class="user-info">
                            <img src="${usuario.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(usuario.name)}&background=random&color=fff&size=40&rounded=true&bold=true&format=png`}"
                                 alt="${usuario.name}"
                                 class="avatar">
                            <div>
                                <span class="username">${usuario.name}</span>
                                <span class="review-date">${new Date(comentario.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                    ${esSpoiler ?
                        `<div class="spoiler-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Esta crítica contiene spoilers
                            <button class="show-spoiler">Mostrar spoiler</button>
                        </div>` : ''
                    }
                    <div class="review-content ${esSpoiler ? 'spoiler-content' : ''}">${comentario.comentario}</div>
                    <div class="review-actions">
                        <button class="btn-like ${likeActiveClass}" data-id="${comentario.id}">
                            <i class="${likeIcon}"></i>
                            <span class="likes-count">${likes}</span>
                        </button>
                        <button class="btn-dislike ${dislikeActiveClass}" data-id="${comentario.id}">
                            <i class="${dislikeIcon}"></i>
                            <span class="dislikes-count">${dislikes}</span>
                        </button>
                        <button class="btn-reply" data-id="${comentario.id}">
                            <i class="far fa-comment"></i> Responder
                        </button>
                    </div>
                `;

                // Crear contenedor específico para las respuestas de este comentario
                const respuestasContainer = document.createElement('div');
                respuestasContainer.className = 'respuestas-container';
                respuestasContainer.id = `respuestas-${comentario.id}`;

                // Agregar comentario y su contenedor de respuestas al documento
                container.appendChild(reviewEl);
                reviewEl.appendChild(respuestasContainer);

                // Cargar las respuestas para este comentario
                console.log("Cargando respuestas para comentario:", comentario.id);
                cargarRespuestas(comentario.id);
            });

            // Agregar event listeners para los botones de like/dislike
            document.querySelectorAll('.btn-like, .btn-dislike').forEach(button => {
                button.addEventListener('click', function() {
                    const tipo = this.classList.contains('btn-like') ? 'like' : 'dislike';
                    handleLikeDislike(this, tipo);
                });
            });

            // Agregar event listeners para mostrar spoilers
            document.querySelectorAll('.show-spoiler').forEach(button => {
                button.addEventListener('click', function() {
                    const reviewEl = this.closest('.review');
                    const warningEl = reviewEl.querySelector('.spoiler-warning');
                    const contentEl = reviewEl.querySelector('.spoiler-content, .contenido-spoiler');

                    if (warningEl && contentEl) {
                        warningEl.style.display = 'none';
                        contentEl.style.display = 'block';
                    }
                });
            });

            // Inicializar sistema de respuestas a comentarios
            procesarRespuestas();

            // Configurar botones de spoiler
            setupSpoilerButtons();

            // Si se solicitó desplazamiento explícitamente, realizarlo
            if (scrollToComments && container) {
                container.scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Error al cargar comentarios:', error);
            if (container) {
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar los comentarios. Por favor, intenta de nuevo más tarde.</p>
                    </div>
                `;
            }
        });
}

// Función para cargar respuestas de un comentario
function cargarRespuestas(comentarioId) {
    if (!comentarioId) {
        console.error('Error: ID de comentario no válido para cargar respuestas');
        return;
    }

    console.log("Iniciando carga de respuestas para comentario ID:", comentarioId);

    // Obtener el contenedor de respuestas
    const container = document.getElementById(`respuestas-${comentarioId}`);
    if (!container) {
        console.error(`Error: No se encontró el contenedor para las respuestas del comentario ${comentarioId}`);
        return;
    }

    // Mostrar indicador de carga
    container.innerHTML = `
        <div class="loading-state" style="text-align: center; padding: 10px;">
            <i class="fas fa-spinner fa-spin" style="color: var(--verde-neon);"></i>
            <p>Cargando respuestas...</p>
        </div>
    `;

    // Construir URL absoluta para evitar problemas de rutas relativas
    const baseUrl = window.location.origin;
    const respuestasUrl = `${baseUrl}/api/respuestas_comentarios/comentario/${comentarioId}`;
    console.log("URL respuestas:", respuestasUrl);

    fetch(respuestasUrl)
        .then(response => {
            console.log("Respuesta del servidor para respuestas:", response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`Error al cargar las respuestas: ${response.status}`);
            }
            return response.json();
        })
        .then(respuestas => {
            console.log("Respuestas recibidas para comentario", comentarioId, ":", respuestas);

            // Limpiar el contenedor
            container.innerHTML = '';

            // Asegurarnos de que trabajamos con un array de respuestas
            const respuestasArray = Array.isArray(respuestas) ? respuestas : [];

            if (respuestasArray.length === 0) {
                console.log(`No hay respuestas para el comentario ${comentarioId}`);
                container.innerHTML = `
                    <div class="no-respuestas" style="padding: 10px; color: var(--texto-muted); font-style: italic;">
                        No hay respuestas a este comentario todavía.
                    </div>
                `;
                return;
            }

            // Renderizar cada respuesta
            respuestasArray.forEach(respuesta => {
                // Verificar que la respuesta tenga la información mínima necesaria
                if (!respuesta || !respuesta.id || !respuesta.respuesta) {
                    console.warn('Advertencia: respuesta inválida omitida', respuesta);
                    return;
                }

                // Obtener usuario de manera segura
                const usuario = respuesta.usuario || {};
                const esSpoiler = respuesta.es_spoiler || false;
                const fecha = new Date(respuesta.created_at);
                const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Determinar si hay que mostrar warning de spoiler
                const spoilerWarning = esSpoiler ? `
                    <div class="spoiler-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Esta respuesta contiene spoilers.</span>
                        <button class="btn-show-spoiler">Mostrar de todos modos</button>
                    </div>
                ` : '';

                const respuestaHtml = `
                    <div class="respuesta ${esSpoiler ? 'spoiler' : ''}" data-respuesta-id="${respuesta.id}">
                        <div class="respuesta-header">
                            <div class="user-info">
                                <img src="${usuario.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(usuario.name || 'Usuario')}&background=random&color=fff&size=40&rounded=true&bold=true&format=png`}"
                                     alt="${usuario.name || 'Usuario'}"
                                     class="avatar">
                                <span class="username">${usuario.name || 'Usuario'}</span>
                            </div>
                            <span class="respuesta-date">${fechaFormateada}</span>
                        </div>
                        ${spoilerWarning}
                        <div class="respuesta-content ${esSpoiler ? 'spoiler-content' : ''}">${respuesta.respuesta.replace(/\n/g, '<br>')}</div>
                    </div>
                `;
                container.innerHTML += respuestaHtml;
            });

            // Configurar los botones de spoiler en las respuestas recién cargadas
            setupSpoilerButtons();
        })
        .catch(error => {
            console.error('Error al cargar respuestas para comentario', comentarioId, ':', error);
            container.innerHTML = `
                <div class="error-message" style="padding: 10px; color: #ff6b6b;">
                    <i class="fas fa-exclamation-circle"></i>
                    Error al cargar las respuestas: ${error.message}
                </div>
            `;
        });
}

// Función para manejar los likes y dislikes
function handleLikeDislike(button, tipo) {
    // Verificar si el usuario está autenticado
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const userId = document.querySelector('meta[name="user-id"]')?.content;

    if (!csrfToken || !userId) {
        showNotification('Debes iniciar sesión para valorar comentarios', 'error');
        return;
    }

    const comentarioId = button.dataset.id;

    // Mostrar indicador de carga
    button.disabled = true;
    const iconElement = button.querySelector('i');
    const originalIcon = iconElement.className;
    iconElement.className = 'fas fa-spinner fa-spin';

    // Construir URL absoluta
    const baseUrl = window.location.origin;
    const likesUrl = `${baseUrl}/api/likes_comentarios`;
    console.log("Enviando like/dislike a:", likesUrl);

    fetch(likesUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            user_id: userId,
            id_comentario: comentarioId,
            tipo: tipo
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al procesar la valoración');
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta de like/dislike:', data);

        // Habilitar el botón de nuevo
        button.disabled = false;

        // Actualizar contadores en la interfaz
        if (data.comentario) {
            const review = button.closest('.review');

            const likesCountEl = review.querySelector('.btn-like .likes-count');
            if (likesCountEl) {
                likesCountEl.textContent = data.comentario.likes_count || 0;
            }

            const dislikesCountEl = review.querySelector('.btn-dislike .dislikes-count');
            if (dislikesCountEl) {
                dislikesCountEl.textContent = data.comentario.dislikes_count || 0;
            }

            // Actualizar clases activas de los botones
            const likeBtn = review.querySelector('.btn-like');
            const dislikeBtn = review.querySelector('.btn-dislike');
            const likeBtnIcon = likeBtn.querySelector('i');
            const dislikeBtnIcon = dislikeBtn.querySelector('i');

            likeBtn.classList.remove('active');
            dislikeBtn.classList.remove('active');
            likeBtnIcon.className = 'far fa-thumbs-up';
            dislikeBtnIcon.className = 'far fa-thumbs-down';

            // Actualizar el botón correspondiente según la respuesta
            if (data.message.includes('creado') || data.message.includes('actualizado')) {
                if (tipo === 'like') {
                    likeBtn.classList.add('active');
                    likeBtnIcon.className = 'fas fa-thumbs-up';
                } else {
                    dislikeBtn.classList.add('active');
                    dislikeBtnIcon.className = 'fas fa-thumbs-down';
                }

                // Actualizar notificaciones en tiempo real después de un like/dislike
                actualizarNotificaciones();
            }

            // Mostrar notificación
            const accionTexto = data.message.includes('creado') ? 'añadido' :
                                (data.message.includes('actualizado') ? 'actualizado' : 'eliminado');
            showNotification(`Se ha ${accionTexto} tu ${tipo}`, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restaurar icono original y habilitar el botón
        button.disabled = false;
        iconElement.className = originalIcon;
        showNotification('Error al procesar tu valoración', 'error');
    });
}

// Función para actualizar las notificaciones en tiempo real
function actualizarNotificaciones() {
    console.log("Actualizando notificaciones...");

    // Verificar si existe el elemento de notificaciones
    const notificacionesList = document.getElementById('notificaciones-list');
    const notificacionesCounter = document.getElementById('notificaciones-counter');
    const notificacionesContent = document.getElementById('notificaciones-content');

    // Si no existe el panel de notificaciones, salir de la función
    if (!notificacionesCounter || !notificacionesList) {
        console.log("No se encontraron los elementos de notificaciones en el DOM");
        return;
    }

    // Obtener ID del usuario
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId) {
        console.log("No se encontró el ID del usuario");
        return;
    }

    // Construir URL absoluta
    const baseUrl = window.location.origin;
    const notificacionesUrl = `${baseUrl}/api/notificaciones/user/${userId}`;
    console.log("Obteniendo notificaciones de:", notificacionesUrl);

    fetch(notificacionesUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la respuesta: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Notificaciones obtenidas:", data);

            // Contar notificaciones no leídas
            const noLeidas = data.filter(n => !n.leido).length;
            console.log(`Notificaciones no leídas: ${noLeidas}`);

            // Actualizar contador
            if (noLeidas > 0) {
                notificacionesCounter.textContent = noLeidas > 9 ? '9+' : noLeidas;
                notificacionesCounter.style.display = 'block';
            } else {
                notificacionesCounter.style.display = 'none';
            }

            // Actualizar la lista de notificaciones si está visible
            if (notificacionesList && notificacionesContent.classList.contains('show')) {
                console.log("Actualizando panel de notificaciones visible");

                // Renderizar las notificaciones (esta función debe estar definida en el blade)
                if (typeof renderizarNotificaciones === 'function') {
                    console.log("Usando función renderizarNotificaciones del blade");
                    renderizarNotificaciones(data);
                } else {
                    console.log("Usando implementación alternativa para renderizar notificaciones");
                    // Implementación alternativa si la función no está disponible
                    let html = '';

                    if (!data || data.length === 0) {
                        html = `
                            <div class="sin-notificaciones">
                                <i class="fas fa-bell-slash"></i>
                                <p>No tienes notificaciones</p>
                            </div>
                        `;
                    } else {
                        data.forEach(notificacion => {
                            const fecha = new Date(notificacion.created_at);
                            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            });

                            // Analizar el mensaje para determinar mejor el tipo
                            const mensaje = notificacion.mensaje || '';
                            let tipoReal = notificacion.tipo || 'nuevo_comentario';
                            let icono = 'fa-bell';

                            // Determinar icono basado en el tipo o el contenido del mensaje
                            if (tipoReal === 'nuevo_like' || mensaje.includes('like') || mensaje.includes('ha dado like')) {
                                icono = 'fa-thumbs-up';
                                tipoReal = 'nuevo_like';
                            } else if (mensaje.includes('respuesta') || mensaje.includes('ha respondido')) {
                                // Si el mensaje contiene palabras relativas a respuestas, usar el icono de respuesta
                                icono = 'fa-reply';
                            } else if (tipoReal === 'nuevo_comentario' || mensaje.includes('comentario') || mensaje.includes('ha comentado')) {
                                icono = 'fa-comment';
                            }

                            html += `
                                <div class="notificacion-item ${notificacion.leido ? '' : 'no-leida'}" data-id="${notificacion.id}" data-tipo="${tipoReal}">
                                    <div class="notificacion-icono">
                                        <i class="fas ${icono}"></i>
                                    </div>
                                    <div class="notificacion-contenido">
                                        <p>${notificacion.mensaje}</p>
                                        <span class="notificacion-fecha">${fechaFormateada}</span>
                                    </div>
                                    <button class="marcar-leida-btn" title="Marcar como leída">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            `;
                        });
                    }

                    notificacionesList.innerHTML = html;

                    // Añadir evento de marcar como leída
                    document.querySelectorAll('.marcar-leida-btn').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const notificacionEl = this.closest('.notificacion-item');
                            const id = notificacionEl.dataset.id;

                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                            // Construir URL absoluta para marcar notificación como leída
                            const markReadUrl = `${baseUrl}/api/notificaciones/read/${id}`;
                            console.log("Marcando notificación como leída:", markReadUrl);

                            fetch(markReadUrl, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => response.json())
                            .then(() => {
                                notificacionEl.classList.remove('no-leida');
                                actualizarNotificaciones();
                            })
                            .catch(error => console.error('Error al marcar notificación:', error));
                        });
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error al actualizar notificaciones:', error);
        });
}

// Función para manejar las respuestas a comentarios
function procesarRespuestas() {
    console.log("Inicializando sistema de respuestas a comentarios...");

    // Añadir event listeners para los botones de respuesta
    document.querySelectorAll('.btn-reply').forEach(button => {
        button.addEventListener('click', function() {
            const comentarioId = this.getAttribute('data-id');
            console.log(`Botón de respuesta clickeado para comentario ID: ${comentarioId}`);

            // Buscar o crear contenedor para respuestas
            let respuestasContainer = document.getElementById(`respuestas-${comentarioId}`);
            if (!respuestasContainer) {
                console.log(`Creando contenedor de respuestas para comentario ID: ${comentarioId}`);
                const review = this.closest('.review');

                // Si todavía no existe un contenedor de respuestas, crearlo
                respuestasContainer = document.createElement('div');
                respuestasContainer.id = `respuestas-${comentarioId}`;
                respuestasContainer.className = 'respuestas-container';

                // Buscar el mejor lugar para insertar el contenedor de respuestas
                const reviewActions = review.querySelector('.review-actions');
                if (reviewActions) {
                    reviewActions.insertAdjacentElement('afterend', respuestasContainer);
                } else {
                    review.appendChild(respuestasContainer);
                }
            }

            // Buscar o crear el formulario de respuesta
            const respuestaForm = document.getElementById(`respuesta-form-${comentarioId}`);
            if (!respuestaForm) {
                console.log(`Creando formulario de respuesta para comentario ID: ${comentarioId}`);

                // Crear el formulario de respuesta
                const formHTML = `
                    <div class="respuesta-form" id="respuesta-form-${comentarioId}" style="display: none;">
                        <textarea placeholder="Escribe tu respuesta..." class="respuesta-text"></textarea>
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="es_spoiler_respuesta_${comentarioId}">
                            <label class="form-check-label" for="es_spoiler_respuesta_${comentarioId}">Esta respuesta contiene spoilers</label>
                        </div>
                        <button class="btn-submit-respuesta" data-id="${comentarioId}">Enviar respuesta</button>
                    </div>
                `;

                // Insertar el formulario después del contenedor de respuestas
                respuestasContainer.insertAdjacentHTML('afterend', formHTML);

                // Ahora que hemos creado el formulario, lo seleccionamos
                const nuevoRespuestaForm = document.getElementById(`respuesta-form-${comentarioId}`);

                // Configurar evento para el botón de envío
                const submitButton = nuevoRespuestaForm.querySelector('.btn-submit-respuesta');
                submitButton.addEventListener('click', function() {
                    enviarRespuesta(comentarioId);
                });

                // Mostrar el formulario
                nuevoRespuestaForm.style.display = 'block';

                // Cambiar el texto del botón de respuesta
                this.innerHTML = '<i class="fas fa-times"></i> Cancelar';
            } else {
                // Si el formulario ya existe, alternar su visibilidad
                const displayActual = respuestaForm.style.display;
                respuestaForm.style.display = displayActual === 'none' ? 'block' : 'none';

                // Cambiar el texto del botón según corresponda
                if (respuestaForm.style.display === 'block') {
                    this.innerHTML = '<i class="fas fa-times"></i> Cancelar';
                } else {
                    this.innerHTML = '<i class="far fa-comment"></i> Responder';
                }
            }

            // Cargar las respuestas existentes
            cargarRespuestas(comentarioId);
        });
    });
}

// Función para enviar una respuesta a un comentario
function enviarRespuesta(comentarioId) {
    console.log(`Enviando respuesta para comentario ID: ${comentarioId}`);

    const respuestaForm = document.getElementById(`respuesta-form-${comentarioId}`);
    if (!respuestaForm) {
        console.error(`No se encontró el formulario para el comentario #${comentarioId}`);
        return;
    }

    const textareaEl = respuestaForm.querySelector('.respuesta-text');
    const esSpoilerEl = document.getElementById(`es_spoiler_respuesta_${comentarioId}`);

    if (!textareaEl || !esSpoilerEl) {
        console.error('No se encontraron los elementos del formulario');
        showNotification('Error: Formulario de respuesta incompleto', 'error');
        return;
    }

    const respuesta = textareaEl.value.trim();
    const esSpoiler = esSpoilerEl.checked;

    if (!respuesta) {
        showNotification('La respuesta no puede estar vacía', 'error');
        return;
    }

    // Verificar si el usuario está autenticado
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const userName = document.querySelector('meta[name="user-name"]')?.content || 'Usuario';
    const userAvatar = document.querySelector('meta[name="user-avatar"]')?.content || '';

    if (!csrfToken || !userId) {
        showNotification('Debes iniciar sesión para responder comentarios', 'error');
        return;
    }

    // Mostrar indicador de carga
    const submitButton = respuestaForm.querySelector('.btn-submit-respuesta');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    // Construir URL absoluta
    const baseUrl = window.location.origin;
    const respuestasUrl = `${baseUrl}/api/respuestas_comentarios`;
    console.log("URL para enviar respuesta:", respuestasUrl);

    // Enviar respuesta
    fetch(respuestasUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            comentario_id: comentarioId,
            user_id: userId,
            respuesta: respuesta,
            es_spoiler: esSpoiler
        })
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 422 || response.status === 500) {
                // Si hay un error en el backend pero la respuesta se guardó
                console.log('La respuesta parece haberse enviado a pesar del error');

                // Limpiar el formulario y ocultarlo
                textareaEl.value = '';
                esSpoilerEl.checked = false;
                respuestaForm.style.display = 'none';

                // Restaurar el botón de responder
                const replyButton = document.querySelector(`.btn-reply[data-id="${comentarioId}"]`);
                if (replyButton) {
                    replyButton.innerHTML = '<i class="far fa-comment"></i> Responder';
                }

                // Intentar cargar las respuestas para obtener la recién creada
                setTimeout(() => {
                    cargarRespuestas(comentarioId);
                }, 500);

                showNotification('La respuesta se ha guardado correctamente.', 'success');

                return Promise.reject('Error de validación pero la respuesta puede haberse guardado');
            }
            throw new Error(`Error al enviar la respuesta: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Respuesta creada:", data);

        // Limpiar el formulario
        textareaEl.value = '';
        esSpoilerEl.checked = false;
        respuestaForm.style.display = 'none';

        // Restaurar el botón de responder
        const replyButton = document.querySelector(`.btn-reply[data-id="${comentarioId}"]`);
        if (replyButton) {
            replyButton.innerHTML = '<i class="far fa-comment"></i> Responder';
        }

        // Añadir la nueva respuesta directamente a la interfaz sin recargar la página
        const respuestasContainer = document.getElementById(`respuestas-${comentarioId}`);
        if (respuestasContainer) {
            // Eliminar mensaje de "no hay respuestas" si existe
            const noRespuestasMsg = respuestasContainer.querySelector('.no-respuestas');
            if (noRespuestasMsg) {
                noRespuestasMsg.remove();
            }

            // Crear elemento para la nueva respuesta
            const fechaActual = new Date();
            const fechaFormateada = fechaActual.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Determinar si hay que mostrar warning de spoiler
            const spoilerWarning = esSpoiler ? `
                <div class="spoiler-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Esta respuesta contiene spoilers.</span>
                    <button class="btn-show-spoiler">Mostrar de todos modos</button>
                </div>
            ` : '';

            // Obtener o generar avatar del usuario
            const avatarUrl = userAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=random&color=fff&size=40&rounded=true&bold=true&format=png`;

            // Crear elemento de respuesta
            const nuevaRespuestaEl = document.createElement('div');
            nuevaRespuestaEl.className = `respuesta ${esSpoiler ? 'spoiler' : ''}`;
            nuevaRespuestaEl.dataset.respuestaId = data.id || '';
            nuevaRespuestaEl.innerHTML = `
                <div class="respuesta-header">
                    <div class="user-info">
                        <img src="${avatarUrl}" alt="${userName}" class="avatar">
                        <span class="username">${userName}</span>
                    </div>
                    <span class="respuesta-date">${fechaFormateada}</span>
                </div>
                ${spoilerWarning}
                <div class="respuesta-content ${esSpoiler ? 'spoiler-content' : ''}">${respuesta.replace(/\n/g, '<br>')}</div>
            `;

            // Añadir la nueva respuesta al principio del contenedor
            respuestasContainer.insertBefore(nuevaRespuestaEl, respuestasContainer.firstChild);

            // Configurar botones de spoiler
            setupSpoilerButtons();
        } else {
            console.error(`No se encontró contenedor de respuestas para comentario ID: ${comentarioId}`);
        }

        // Mostrar notificación
        showNotification('Respuesta enviada correctamente', 'success');

        // También actualizar notificaciones
        actualizarNotificaciones();
    })
    .catch(error => {
        console.error('Error al enviar respuesta:', error);
        if (error === 'Error de validación pero la respuesta puede haberse guardado') {
            // Ya manejamos este caso arriba
            return;
        }
        showNotification('Error al enviar la respuesta. Por favor, inténtalo de nuevo.', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// Función para manejar los botones que muestran spoilers
function setupSpoilerButtons() {
    console.log("Configurando botones de spoiler...");

    // Manejar botones para mostrar spoilers en comentarios
    document.querySelectorAll('.show-spoiler, .btn-show-spoiler').forEach(button => {
        button.addEventListener('click', function() {
            const reviewEl = this.closest('.review');
            if (!reviewEl) {
                console.error('No se encontró el elemento de revisión para el botón de spoiler');
                return;
            }

            const warningEl = reviewEl.querySelector('.spoiler-warning');
            const contentEl = reviewEl.querySelector('.spoiler-content, .contenido-spoiler');

            if (!warningEl || !contentEl) {
                console.error('No se encontraron los elementos de advertencia o contenido de spoiler');
                return;
            }

            console.log('Mostrando contenido con spoiler');
            warningEl.style.display = 'none';
            contentEl.style.display = 'block';

            // Remover la clase 'spoiler' para evitar problemas con CSS
            reviewEl.classList.remove('spoiler');
        });
    });

    // Manejar botones para mostrar spoilers en respuestas
    document.querySelectorAll('.respuesta .btn-show-spoiler').forEach(button => {
        button.addEventListener('click', function() {
            const respuestaEl = this.closest('.respuesta');
            if (!respuestaEl) {
                console.error('No se encontró el elemento de respuesta para el botón de spoiler');
                return;
            }

            const warningEl = respuestaEl.querySelector('.spoiler-warning');
            const contentEl = respuestaEl.querySelector('.respuesta-content');

            if (!warningEl || !contentEl) {
                console.error('No se encontraron los elementos de advertencia o contenido en la respuesta');
                return;
            }

            console.log('Mostrando contenido de respuesta con spoiler');
            warningEl.style.display = 'none';
            contentEl.style.display = 'block';

            // Remover la clase 'spoiler' para evitar problemas con CSS
            respuestaEl.classList.remove('spoiler');
        });
    });
}

// ========================================================
// FUNCIONES PARA TRAILERS Y PELÍCULAS SIMILARES
// ========================================================

/**
 * Inicializa los botones de trailer en la página
 */
function initializeTrailerButtons() {
    console.log("Inicializando botones de trailer...");

    // Configurar botón principal de trailer si existe
    const mainTrailerBtn = document.querySelector('.btn-trailer');
    if (mainTrailerBtn) {
        mainTrailerBtn.addEventListener('click', function() {
            const movieId = this.dataset.id || document.querySelector('meta[name="movie-id"]')?.content;
            if (movieId) {
                loadAndShowTrailer(movieId);
            } else {
                console.error('No se pudo obtener el ID de la película para el trailer');
            }
        });
    }

    // Configurar botones de trailer en la sección de videos
    document.querySelectorAll('.video-item .btn-play').forEach(button => {
        button.addEventListener('click', function() {
            const videoKey = this.dataset.key;
            if (videoKey) {
                showVideoModal(videoKey);
            }
        });
    });

    // Buscar y configurar botones de trailer en películas similares
    document.querySelectorAll('.related-movies-container .btn-trailer').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const movieId = this.dataset.id;
            if (movieId) {
                loadAndShowTrailer(movieId);
            }
        });
    });
}

/**
 * Muestra un video en el modal
 * @param {string} videoKey - Clave de YouTube para el video
 */
function showVideoModal(videoKey) {
    const trailerModal = document.getElementById('trailerModalStatic');
    const trailerContainer = document.getElementById('trailerContainerStatic');

    if (!trailerModal || !trailerContainer) {
        console.error('No se encontraron los elementos del modal');
        return;
    }

    // Mostrar el modal
    trailerModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Cargar el video
    trailerContainer.innerHTML = `
        <iframe
            width="100%"
            height="100%"
            src="https://www.youtube.com/embed/${videoKey}?autoplay=1&rel=0"
            title="YouTube video player"
            frameborder="0"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    `;
}
