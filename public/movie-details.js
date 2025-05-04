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

/**
 * Sistema de comentarios legacy (mantener para compatibilidad)
 */
function initComentarios() {
    const peliculaId = document.querySelector('#review-form input[name="id_pelicula"]')?.value;
    if (!peliculaId) return; // Salir si no hay ID de película

    let userData;
    try {
        userData = JSON.parse(localStorage.getItem('user'));
    } catch (e) {
        console.error('Error al obtener datos del usuario:', e);
        userData = null;
    }

    const reviewForm = document.getElementById('review-form');
    const reviewsList = document.querySelector('.reviews-list');

    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!userData) {
                showNotification('Debes iniciar sesión para dejar una crítica', 'warning');
                return;
            }

            if (!selectedRating) {
                showNotification('Debes seleccionar una puntuación', 'warning');
                return;
            }

            const formData = new FormData(this);
            formData.append('user_id', userData.id);
            formData.append('rating', selectedRating);

            try {
                showSpinner();

                const response = await fetch('/api/comentarios', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.reset();
                    selectedRating = 0;
                    highlightStars(0);
                    await cargarComentarios(peliculaId, reviewsList, userData);
                    showNotification('¡Crítica publicada correctamente!', 'success');
                } else {
                    showNotification('Error al guardar la crítica', 'error');
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                showNotification('Error al enviar la crítica', 'error');
            } finally {
                hideSpinner();
            }
        });
    }

    // Cargar comentarios inicialmente si hay reviewsList
    if (reviewsList) {
        cargarComentarios(peliculaId, reviewsList, userData);
    }
}

// Función auxiliar para resaltar estrellas
function highlightStars(rating) {
    const stars = document.querySelectorAll('.stars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.className = 'fas fa-star';
        } else {
            star.className = 'far fa-star';
        }
    });
}

/**
 * Carga los comentarios de la película
 */
async function cargarComentarios(peliculaId, reviewsList, userData) {
    try {
        if (!reviewsList) return;

        reviewsList.innerHTML = '<div class="loading-comments">Cargando críticas...</div>';

        const response = await fetch(`/api/comentarios/pelicula/${peliculaId}`);
        const comentarios = await response.json();

        reviewsList.innerHTML = '';

        if (comentarios.length === 0) {
            reviewsList.innerHTML = '<p class="no-reviews">Aún no hay críticas para esta película. ¡Sé el primero en opinar!</p>';
            return;
        }

        comentarios.forEach(comentario => {
            const comentarioDiv = document.createElement('div');
            comentarioDiv.className = `review ${comentario.es_spoiler ? 'spoiler' : ''}`;

            const fecha = new Date(comentario.created_at);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Generar estrellas según la puntuación
            const estrellasHTML = Array(5).fill().map((_, i) =>
                `<i class="fa-star ${i < comentario.rating ? 'fas' : 'far'}"></i>`
            ).join('');

            comentarioDiv.innerHTML = `
                <div class="review-header">
                    <div class="user-info">
                        <img src="${comentario.usuario?.foto_perfil || '/images/default-avatar.png'}"
                             alt="${comentario.usuario?.name || 'Usuario'}"
                             class="avatar">
                        <div>
                            <span class="username">${comentario.usuario?.name || 'Usuario'}</span>
                            <span class="review-date">${fechaFormateada}</span>
                        </div>
                    </div>
                    <div class="review-rating">
                        ${estrellasHTML}
                    </div>
                    ${userData && (userData.id === comentario.user_id || userData.rol === 'admin') ? `
                        <button class="btn btn-sm btn-danger delete-review" data-id="${comentario.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
                ${comentario.es_spoiler ? `
                    <div class="spoiler-warning">
                        <i class="fas fa-exclamation-triangle"></i> Esta crítica contiene spoilers
                        <button class="btn btn-sm btn-warning show-spoiler">Mostrar spoiler</button>
                    </div>
                    <div class="review-content spoiler-content" style="display: none;">
                        ${comentario.comentario}
                    </div>
                ` : `
                    <div class="review-content">
                        ${comentario.comentario}
                    </div>
                `}
            `;

            reviewsList.appendChild(comentarioDiv);
        });

        // Agregar eventos para mostrar spoilers
        document.querySelectorAll('.show-spoiler').forEach(button => {
            button.addEventListener('click', function() {
                const content = this.closest('.review').querySelector('.spoiler-content');
                const warning = this.closest('.spoiler-warning');
                content.style.display = 'block';
                warning.style.display = 'none';
            });
        });

        // Agregar eventos para eliminar críticas
        document.querySelectorAll('.delete-review').forEach(button => {
            button.addEventListener('click', function() {
                const comentarioId = this.dataset.id;
                eliminarComentario(comentarioId, peliculaId, reviewsList, userData);
            });
        });
    } catch (error) {
        console.error('Error al cargar críticas:', error);
        reviewsList.innerHTML = '<p class="error-message">Error al cargar las críticas. Inténtalo de nuevo más tarde.</p>';
    }
}

/**
 * Elimina un comentario de la película
 */
async function eliminarComentario(id, peliculaId, reviewsList, userData) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta crítica?')) {
        return;
    }

    try {
        showSpinner();

        const response = await fetch(`/api/comentarios/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userData?.id,
                user_rol: userData?.rol
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Crítica eliminada correctamente', 'success');
            await cargarComentarios(peliculaId, reviewsList, userData);
        } else {
            showNotification('Error al eliminar la crítica', 'error');
        }
    } catch (error) {
        console.error('Error en la petición de eliminación:', error);
        showNotification('Error al eliminar la crítica', 'error');
    } finally {
        hideSpinner();
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

/**
 * Configura el sistema de críticas
 */
function setupReviews() {
    const reviewForm = document.getElementById('review-form');
    const reviewsList = document.querySelector('.reviews-list');

    if (!reviewForm || !reviewsList) return;

    // Carga de críticas existentes
    loadReviews();

    // Manejo del formulario de críticas
    reviewForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await submitReview(reviewForm);
    });

    // Selección de estrellas
    const stars = reviewForm.querySelectorAll('.stars i');
    stars.forEach(star => {
        star.addEventListener('click', () => setStarRating(star, stars));
        star.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                setStarRating(star, stars);
            }
        });
    });
}

/**
 * Establece la calificación de estrellas
 */
function setStarRating(clickedStar, allStars) {
    const rating = parseInt(clickedStar.dataset.rating, 10);

    allStars.forEach(star => {
        const starRating = parseInt(star.dataset.rating, 10);
        if (starRating <= rating) {
            star.className = 'fas fa-star';
        } else {
            star.className = 'far fa-star';
        }
    });

    // Establecer el valor en un campo oculto para el envío
    const ratingField = document.createElement('input');
    ratingField.type = 'hidden';
    ratingField.name = 'rating';
    ratingField.value = rating;

    // Reemplazar campo existente o añadir uno nuevo
    const existingField = document.querySelector('input[name="rating"]');
    if (existingField) {
        existingField.value = rating;
    } else {
        document.getElementById('review-form').appendChild(ratingField);
    }
}

/**
 * Carga las críticas de la película
 */
async function loadReviews() {
    // Implementación pendiente
}

/**
 * Envía una nueva crítica
 */
async function submitReview(form) {
    // Implementación pendiente
}

/**
 * Muestra una notificación al usuario
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getIconForType(type)}"></i>
            <p>${message}</p>
        </div>
        <button class="notification-close">&times;</button>
    `;

    document.body.appendChild(notification);

    // Animación de entrada
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Auto-eliminar después de 5 segundos
    const timeout = setTimeout(() => {
        removeNotification(notification);
    }, 5000);

    // Permitir cerrar manualmente
    notification.querySelector('.notification-close').addEventListener('click', () => {
        clearTimeout(timeout);
        removeNotification(notification);
    });
}

/**
 * Retorna el icono según el tipo de notificación
 */
function getIconForType(type) {
    switch (type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        case 'info':
        default: return 'fa-info-circle';
    }
}

/**
 * Elimina una notificación con animación
 */
function removeNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Muestra un indicador de carga
 */
function showSpinner() {
    let spinner = document.getElementById('global-spinner');

    if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'global-spinner';
        spinner.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(spinner);
    }

    spinner.classList.add('active');
    document.body.classList.add('loading');
}

/**
 * Oculta el indicador de carga
 */
function hideSpinner() {
    const spinner = document.getElementById('global-spinner');
    if (spinner) {
        spinner.classList.remove('active');
        document.body.classList.remove('loading');

        setTimeout(() => {
            if (spinner.parentNode && !spinner.classList.contains('active')) {
                spinner.parentNode.removeChild(spinner);
            }
        }, 300);
    }
}
