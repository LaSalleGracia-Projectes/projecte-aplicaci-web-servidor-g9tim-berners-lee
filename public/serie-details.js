// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
let API_KEY = "";

// Obtener API key desde el elemento meta
document.addEventListener('DOMContentLoaded', function() {
    const metaApiKey = document.querySelector('meta[name="tmdb-api-key"]');
    if (metaApiKey) {
        API_KEY = metaApiKey.getAttribute('content');
    }

    // Configurar eventos
    setupTabs();
    setupRatingSelector();
    setupReviewForm();
    setupModalEvents();
    loadSerieComments();
});

// ========================================================
// FUNCIONES PARA TABS DE NAVEGACIÓN
// ========================================================
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Desactivar todos los botones y paneles
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));

            // Activar el botón clickeado
            button.classList.add('active');

            // Activar el panel correspondiente
            const tabName = button.getAttribute('data-tab');
            const panel = document.getElementById(tabName);
            if (panel) {
                panel.classList.add('active');
            }
        });
    });
}

// ========================================================
// GESTIÓN DE VALORACIONES
// ========================================================
function setupRatingSelector() {
    const stars = document.querySelectorAll('.stars i');
    let selectedRating = 0;

    stars.forEach(star => {
        // Evento al pasar el mouse
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateStarsDisplay(rating);
        });

        // Evento al quitar el mouse
        star.addEventListener('mouseout', function() {
            updateStarsDisplay(selectedRating);
        });

        // Evento al hacer clic
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            updateStarsDisplay(selectedRating);
            document.querySelector('input[name="rating"]') ? document.querySelector('input[name="rating"]').value = selectedRating : null;
        });

        // Accesibilidad con teclado
        star.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Función para actualizar la visualización de estrellas
    function updateStarsDisplay(rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
    }
}

// ========================================================
// MANEJO DE FORMULARIO DE CRÍTICAS
// ========================================================
function setupReviewForm() {
    const reviewForm = document.getElementById('review-form');
    if (!reviewForm) return;

    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Verificar si hay un usuario conectado
        if (!checkUserAuthentication()) {
            showLoginPrompt();
            return;
        }

        // Obtener los datos del formulario
        const formData = new FormData(reviewForm);

        // Verificar que todos los campos requeridos están presentes
        if (!validateReviewForm(formData)) {
            showNotification('Por favor, selecciona una puntuación y escribe un comentario.', 'error');
            return;
        }

        // Enviar los datos al servidor
        sendReview(formData);
    });
}

function checkUserAuthentication() {
    // Verificar si existe un token de autenticación
    const authToken = document.querySelector('meta[name="auth-token"]');
    return !!authToken;
}

function showLoginPrompt() {
    // Mostrar un modal o mensaje para que el usuario inicie sesión
    const loginPrompt = document.createElement('div');
    loginPrompt.className = 'login-prompt';
    loginPrompt.innerHTML = `
        <div class="login-message">
            <i class="fas fa-lock"></i>
            <h3>Necesitas iniciar sesión</h3>
            <p>Para dejar una crítica, por favor inicia sesión o regístrate en CritFlix.</p>
            <div class="login-actions">
                <a href="/login" class="btn-login">Iniciar sesión</a>
                <a href="/register" class="btn-register">Registrarme</a>
            </div>
        </div>
    `;

    document.body.appendChild(loginPrompt);

    // Cerrar al hacer clic fuera
    loginPrompt.addEventListener('click', function(e) {
        if (e.target === loginPrompt) {
            loginPrompt.remove();
        }
    });
}

function validateReviewForm(formData) {
    const rating = document.querySelector('.stars i.fas');
    const comentario = formData.get('comentario');
    return !!rating && comentario.trim() !== '';
}

function sendReview(formData) {
    // Añadir el token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    formData.append('_token', csrfToken);

    // Mostrar estado de carga
    const reviewForm = document.getElementById('review-form');
    const submitBtn = reviewForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    // Realizar petición AJAX para guardar el comentario
    fetch('/api/series/comentarios', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al enviar la crítica');
        }
        return response.json();
    })
    .then(data => {
        showNotification('Tu crítica ha sido publicada correctamente.', 'success');
        resetReviewForm();
        loadSerieComments(); // Recargar comentarios
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ha ocurrido un error al enviar tu crítica. Por favor, inténtalo de nuevo.', 'error');
    })
    .finally(() => {
        // Restaurar el estado del botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Publicar crítica';
    });
}

function resetReviewForm() {
    const reviewForm = document.getElementById('review-form');
    reviewForm.reset();

    // Resetear estrellas
    document.querySelectorAll('.stars i').forEach(star => {
        star.className = 'far fa-star';
    });
}

// ========================================================
// CARGA DE COMENTARIOS
// ========================================================
function loadSerieComments() {
    const reviewsList = document.querySelector('.reviews-list');
    if (!reviewsList) return;

    // Obtener ID de la serie de la URL
    const serieId = extractSerieIdFromURL();
    if (!serieId) return;

    // Mostrar estado de carga
    reviewsList.innerHTML = `
        <div class="loading-comentarios">
            <div class="spinner"></div>
            <p>Cargando comentarios...</p>
        </div>
    `;

    // Realizar petición al servidor
    fetch(`/api/series/${serieId}/comentarios`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar comentarios');
        }
        return response.json();
    })
    .then(data => {
        renderComments(data.comentarios, reviewsList);
    })
    .catch(error => {
        console.error('Error:', error);
        reviewsList.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>No se pudieron cargar los comentarios.</p>
            </div>
        `;
    });
}

function extractSerieIdFromURL() {
    // Extraer ID de la serie de la URL actual
    const urlParts = window.location.pathname.split('/');
    const idIndex = urlParts.indexOf('serie') + 1;
    return urlParts[idIndex] || null;
}

function renderComments(comentarios, container) {
    if (!comentarios || comentarios.length === 0) {
        container.innerHTML = `
            <div class="no-comments">
                <i class="far fa-comment-alt"></i>
                <p>Aún no hay críticas para esta serie. ¡Sé el primero en dejar tu opinión!</p>
            </div>
        `;
        return;
    }

    // Ordenar comentarios por fecha (más recientes primero)
    comentarios.sort((a, b) => new Date(b.fecha_creacion) - new Date(a.fecha_creacion));

    // Limpiar contenedor y añadir comentarios
    container.innerHTML = '';
    comentarios.forEach(comentario => {
        const el = createCommentElement(comentario);
        container.appendChild(el);
    });

    // Configurar eventos para mostrar spoilers
    setupSpoilerToggles();
}

function createCommentElement(comentario) {
    const fecha = new Date(comentario.fecha_creacion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Crear elemento de comentario
    const comentarioEl = document.createElement('div');
    comentarioEl.className = `comentario ${comentario.es_spoiler ? 'spoiler' : ''}`;

    // Generar HTML interno
    comentarioEl.innerHTML = `
        <div class="comentario-header">
            <div class="usuario-info">
                <img src="${comentario.usuario.avatar || 'img/perfil-default.jpg'}" class="avatar" alt="${comentario.usuario.nombre}">
                <div>
                    <strong>${comentario.usuario.nombre}</strong>
                    <span class="fecha">${fechaFormateada}</span>
                </div>
            </div>
            <div class="comentario-rating">
                ${generateStarRating(comentario.puntuacion)}
            </div>
        </div>
        ${comentario.es_spoiler ? `
            <div class="spoiler-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Este comentario contiene spoilers.</span>
                <button class="btn-mostrar-spoiler">Mostrar spoiler</button>
            </div>
            <div class="contenido-spoiler">
                <p>${comentario.contenido}</p>
            </div>
        ` : `
            <div class="contenido-comentario">
                <p>${comentario.contenido}</p>
            </div>
        `}
    `;

    return comentarioEl;
}

function setupSpoilerToggles() {
    document.querySelectorAll('.btn-mostrar-spoiler').forEach(btn => {
        btn.addEventListener('click', function() {
            const spoilerContent = this.closest('.comentario').querySelector('.contenido-spoiler');
            spoilerContent.style.display = 'block';
            this.parentElement.style.display = 'none';
        });
    });
}

function generateStarRating(rating) {
    let starsHTML = '';
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;

    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            starsHTML += '<i class="fas fa-star"></i>';
        } else if (i === fullStars + 1 && halfStar) {
            starsHTML += '<i class="fas fa-star-half-alt"></i>';
        } else {
            starsHTML += '<i class="far fa-star"></i>';
        }
    }

    return starsHTML;
}

// ========================================================
// MANEJO DE MODALES Y NOTIFICACIONES
// ========================================================
function setupModalEvents() {
    // Configurar botones de trailer
    document.querySelectorAll('.btn-trailer').forEach(btn => {
        btn.addEventListener('click', function() {
            const serieId = this.getAttribute('data-id');
            if (serieId) {
                loadSerieTrailer(serieId);
            }
        });
    });

    // Configurar botones de favoritos
    document.querySelectorAll('.btn-favorite').forEach(btn => {
        btn.addEventListener('click', function() {
            const serieId = this.getAttribute('data-id');
            if (serieId) {
                toggleFavorite(serieId, this);
            }
        });
    });

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModals();
        }
    });
}

function loadSerieTrailer(serieId) {
    // Mostrar cargador
    showLoading('Cargando trailer...');

    fetch(`${BASE_URL}/tv/${serieId}/videos?api_key=${API_KEY}&language=es-ES`)
    .then(response => response.json())
    .then(data => {
        hideLoading();

        // Buscar el trailer
        const videos = data.results || [];
        const trailer = findTrailer(videos);

        if (trailer) {
            showTrailerModal(trailer.key);
        } else {
            showNotification('No se encontró ningún trailer para esta serie', 'info');
        }
    })
    .catch(error => {
        console.error('Error cargando trailer:', error);
        hideLoading();
        showNotification('Error al cargar el trailer', 'error');
    });
}

function findTrailer(videos) {
    // Buscar primero un trailer en español
    const spanishTrailer = videos.find(v =>
        v.site === 'YouTube' &&
        v.type === 'Trailer' &&
        (v.name.toLowerCase().includes('español') || v.name.toLowerCase().includes('spanish'))
    );

    if (spanishTrailer) return spanishTrailer;

    // Si no hay trailer en español, buscar cualquier trailer
    const anyTrailer = videos.find(v => v.site === 'YouTube' && v.type === 'Trailer');
    if (anyTrailer) return anyTrailer;

    // Como última opción, buscar un teaser
    return videos.find(v => v.site === 'YouTube' && v.type === 'Teaser');
}

function showTrailerModal(videoKey) {
    // Crear o mostrar modal de trailer
    let trailerModal = document.getElementById('trailer-modal');
    if (!trailerModal) {
        trailerModal = document.createElement('div');
        trailerModal.id = 'trailer-modal';
        trailerModal.className = 'trailer-modal';
        trailerModal.innerHTML = `
            <div class="trailer-content">
                <button class="close-trailer">&times;</button>
                <div class="trailer-container" id="trailer-container"></div>
            </div>
        `;
        document.body.appendChild(trailerModal);

        // Configurar eventos de cierre
        const closeBtn = trailerModal.querySelector('.close-trailer');
        closeBtn.addEventListener('click', () => {
            closeTrailerModal();
        });

        trailerModal.addEventListener('click', (e) => {
            if (e.target === trailerModal) {
                closeTrailerModal();
            }
        });
    }

    // Mostrar modal
    trailerModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Agregar iframe con el video
    const trailerContainer = document.getElementById('trailer-container');
    trailerContainer.innerHTML = `
        <iframe
            width="100%"
            height="100%"
            src="https://www.youtube.com/embed/${videoKey}?autoplay=1"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    `;
}

function closeTrailerModal() {
    const trailerModal = document.getElementById('trailer-modal');
    if (trailerModal) {
        trailerModal.style.display = 'none';
        const trailerContainer = document.getElementById('trailer-container');
        trailerContainer.innerHTML = ''; // Detener el video
        document.body.style.overflow = '';
    }
}

function closeModals() {
    closeTrailerModal();

    // Cerrar otros modales si existen
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });

    document.body.style.overflow = '';
}

function toggleFavorite(serieId, button) {
    // Verificar autenticación
    if (!checkUserAuthentication()) {
        showLoginPrompt();
        return;
    }

    // Obtener estado actual
    const isFavorite = button.classList.contains('active');

    // Preparar datos
    const formData = new FormData();
    formData.append('serie_id', serieId);
    formData.append('action', isFavorite ? 'remove' : 'add');

    // Obtener token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mostrar estado de carga
    button.disabled = true;
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Realizar petición
    fetch('/api/favorites/series', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al procesar favorito');
        return response.json();
    })
    .then(data => {
        // Actualizar UI
        if (isFavorite) {
            button.classList.remove('active');
            button.innerHTML = `<i class="far fa-heart"></i> <span>Favorito</span>`;
            showNotification('Serie eliminada de favoritos', 'info');
        } else {
            button.classList.add('active');
            button.innerHTML = `<i class="fas fa-heart"></i> <span>Favorito</span>`;
            showNotification('Serie añadida a favoritos', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalHTML; // Restaurar HTML original
        showNotification('Error al actualizar favoritos', 'error');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// ========================================================
// NOTIFICACIONES Y UTILIDADES
// ========================================================
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    // Determinar icono según tipo
    let icon;
    switch (type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
    }

    notification.innerHTML = `${icon} <span>${message}</span>`;

    // Crear contenedor si no existe
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }

    // Añadir notificación
    container.appendChild(notification);

    // Mostrar con animación
    setTimeout(() => notification.classList.add('show'), 10);

    // Eliminar después de 4 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

function showLoading(message = 'Cargando...') {
    let loading = document.querySelector('.global-loading');
    if (!loading) {
        loading = document.createElement('div');
        loading.className = 'global-loading';
        loading.innerHTML = `
            <div class="loading-content">
                <div class="spinner"></div>
                <p>${message}</p>
            </div>
        `;
        document.body.appendChild(loading);
    }
    loading.style.display = 'flex';
}

function hideLoading() {
    const loading = document.querySelector('.global-loading');
    if (loading) {
        loading.style.display = 'none';
    }
}
