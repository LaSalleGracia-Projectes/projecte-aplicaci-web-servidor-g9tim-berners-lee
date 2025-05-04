// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
let API_KEY = "";
let selectedRating = 0;

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
    cargarComentarios();
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
// CONFIGURAR FORMULARIO DE COMENTARIOS
// ========================================================
function setupReviewForm() {
    const comentarioForm = document.getElementById('comentarioForm');

    if (!comentarioForm) {
        console.error('Error: No se encontró el formulario de comentarios');
        return;
    }

    comentarioForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Verificar los campos requeridos
        if (selectedRating === 0) {
            showNotification('Por favor, selecciona una puntuación para tu crítica.', 'warning');
            return;
        }

        const comentarioText = comentarioForm.querySelector('textarea[name="comentario"]').value.trim();
        if (!comentarioText) {
            showNotification('Por favor, escribe tu opinión sobre esta serie.', 'warning');
            return;
        }

        // Obtener token CSRF y user ID de manera segura
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const userIdMeta = document.querySelector('meta[name="user-id"]');

        if (!csrfTokenMeta || !userIdMeta) {
            showNotification('Error: Falta información de autenticación. Por favor, inicia sesión de nuevo.', 'error');
            console.error('Error: No se encontraron los meta tags necesarios (csrf-token o user-id)');
            return;
        }

        // Preparar los datos del formulario
        const formData = new FormData(comentarioForm);
        formData.append('puntuacion', selectedRating);
        formData.append('tipo', 'serie');

        // Cambiar el estado del botón
        const submitButton = comentarioForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerText;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        // Enviar el comentario mediante AJAX
        fetch('/api/comentarios', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al enviar el comentario');
            }
            return response.json();
        })
        .then(data => {
            // Resetear el formulario
            comentarioForm.reset();
            selectedRating = 0;
            updateStarsDisplay(0);

            // Mostrar notificación de éxito
            showNotification('Tu crítica ha sido publicada correctamente', 'success');

            // Recargar los comentarios
            cargarComentarios();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ha ocurrido un error al publicar tu crítica. Por favor, inténtalo de nuevo.', 'error');
        })
        .finally(() => {
            // Restaurar el botón
            submitButton.disabled = false;
            submitButton.innerText = originalButtonText;
        });

        // Función interna para actualizar estrellas
        function updateStarsDisplay(rating) {
            const stars = document.querySelectorAll('.stars i');
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.className = 'fas fa-star';
                } else {
                    star.className = 'far fa-star';
                }
            });
        }
    });
}

// ========================================================
// CARGAR COMENTARIOS
// ========================================================
function cargarComentarios() {
    const reviewsList = document.querySelector('.reviews-list');

    if (!reviewsList) {
        console.error('No se encontró el contenedor de comentarios');
        return;
    }

    // Mostrar estado de carga
    reviewsList.innerHTML = `
        <div class="loading-state" style="text-align: center; padding: 30px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--verde-neon);"></i>
            <p style="margin-top: 15px; color: var(--gris-texto);">Cargando comentarios...</p>
        </div>
    `;

    // Obtener el ID de la serie desde el formulario
    const serieIdInput = document.querySelector('input[name="id_serie"]');

    if (!serieIdInput || !serieIdInput.value) {
        console.error('No se pudo obtener el ID de la serie');
        reviewsList.innerHTML = `
            <div class="error-message" style="text-align: center; padding: 30px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--rojo);"></i>
                <p style="margin-top: 15px; color: var(--blanco);">No se pudo obtener el ID de la serie</p>
            </div>
        `;
        return;
    }

    const serieId = serieIdInput.value;

    // Cargar comentarios mediante AJAX usando la ruta correcta
    const baseUrl = window.location.origin;
    const comentariosUrl = `${baseUrl}/api/comentarios/tmdb/${serieId}/serie`;
    console.log("URL de comentarios:", comentariosUrl);

    fetch(comentariosUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los comentarios');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                // Renderizar comentarios
                let comentariosHTML = '';
                data.forEach(comentario => {
                    comentariosHTML += createComentarioHTML(comentario);
                });
                reviewsList.innerHTML = comentariosHTML;

                // Configurar botones de respuesta
                setupRespuestaButtons();
            } else {
                // No hay comentarios
                reviewsList.innerHTML = `
                    <div class="empty-content">
                        <i class="far fa-comment-dots"></i>
                        <p>Aún no hay críticas para esta serie</p>
                        <span>¡Sé el primero en compartir tu opinión!</span>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            reviewsList.innerHTML = `
                <div class="error-message" style="text-align: center; padding: 30px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--rojo);"></i>
                    <p style="margin-top: 15px; color: var(--blanco);">Error al cargar los comentarios</p>
                </div>
            `;
        });
}

// Generar HTML para un comentario
function createComentarioHTML(comentario) {
    // Convertir fecha a formato legible
    const fecha = new Date(comentario.created_at || comentario.fecha_creacion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    // Generar estrellas basadas en la puntuación (si existe)
    const puntuacion = comentario.puntuacion || comentario.valoracion || 0;
    const estrellas = generateStarRating(puntuacion);

    // Verificar si el comentario contiene spoilers
    const esSpoiler = comentario.es_spoiler || comentario.contiene_spoiler || false;
    const spoilerClass = esSpoiler ? 'spoiler' : '';
    const spoilerWarning = esSpoiler ? `
        <div class="spoiler-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Esta crítica contiene spoilers. </span>
            <button class="btn-show-spoiler">Mostrar de todos modos</button>
        </div>
    ` : '';

    // Obtener el contenido del comentario
    const contenidoComentario = comentario.comentario || comentario.texto || '';

    // Obtener información del usuario
    const usuario = comentario.usuario || {};
    const nombreUsuario = usuario.name || usuario.username || 'Usuario';
    const avatarUrl = usuario.avatar_url || usuario.avatar || '';

    // Obtener conteo de likes y dislikes
    const likesCount = comentario.likes_count || 0;
    const dislikesCount = comentario.dislikes_count || 0;

    // Estado del usuario actual con respecto al comentario
    const userLikeStatus = comentario.user_like_status || null;
    const likeActiveClass = userLikeStatus === 'like' ? 'active' : '';
    const dislikeActiveClass = userLikeStatus === 'dislike' ? 'active' : '';

    // Iconos según el estado
    const likeIcon = userLikeStatus === 'like' ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
    const dislikeIcon = userLikeStatus === 'dislike' ? 'fas fa-thumbs-down' : 'far fa-thumbs-down';

    // Generar HTML del comentario
    let comentarioHTML = `
        <div class="review ${spoilerClass}" data-comentario-id="${comentario.id}">
            <div class="review-header">
                <div class="user-info">
                    ${avatarUrl ?
                        `<img src="${avatarUrl}" alt="Avatar" class="avatar">` :
                        `<div class="avatar-icon"><i class="fas fa-user"></i></div>`
                    }
                    <div>
                        <span class="username">${nombreUsuario}</span>
                        <div class="review-rating">${estrellas}</div>
                    </div>
                </div>
                <span class="review-date">${fechaFormateada}</span>
            </div>
            ${spoilerWarning}
            <div class="review-content ${esSpoiler ? 'contenido-spoiler' : ''}">
                ${contenidoComentario.replace(/\n/g, '<br>')}
            </div>
            <div class="review-actions">
                <button class="btn-like ${likeActiveClass}" data-comentario-id="${comentario.id}">
                    <i class="${likeIcon}"></i>
                    <span class="likes-count">${likesCount}</span>
                </button>
                <button class="btn-dislike ${dislikeActiveClass}" data-comentario-id="${comentario.id}">
                    <i class="${dislikeIcon}"></i>
                    <span class="dislikes-count">${dislikesCount}</span>
                </button>
                <button class="btn-reply" data-comentario-id="${comentario.id}">
                    <i class="fas fa-reply"></i> Responder
                </button>
            </div>
    `;

    // Añadir respuestas si existen
    if (comentario.respuestas && comentario.respuestas.length > 0) {
        comentarioHTML += `<div class="respuestas-container">`;
        comentario.respuestas.forEach(respuesta => {
            const fechaRespuesta = new Date(respuesta.fecha_creacion);
            const fechaRespuestaFormateada = fechaRespuesta.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            comentarioHTML += `
                <div class="respuesta" data-respuesta-id="${respuesta.id}">
                    <div class="respuesta-header">
                        <div class="user-info">
                            <img src="${respuesta.usuario.avatar_url || '/img/perfil-default.jpg'}" alt="Avatar" class="avatar">
                            <span class="username">${respuesta.usuario.name || 'Usuario'}</span>
                        </div>
                        <span class="respuesta-date">${fechaRespuestaFormateada}</span>
                    </div>
                    <div class="respuesta-content">
                        ${respuesta.respuesta.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;
        });
        comentarioHTML += `</div>`;
    }

    // Cerrar div del comentario
    comentarioHTML += `</div>`;

    return comentarioHTML;
}

// Configurar botones de respuesta
function setupRespuestaButtons() {
    const replyButtons = document.querySelectorAll('.btn-reply');
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const userIdMeta = document.querySelector('meta[name="user-id"]');

    if (!csrfTokenMeta || !userIdMeta) {
        console.error('No se encontraron los metadatos necesarios para la autenticación');
        return;
    }

    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const comentarioId = this.getAttribute('data-comentario-id');

            // Verificar si ya existe el contenedor, si no, crearlo
            let respuestaFormContainer = document.querySelector(`#respuesta-form-container-${comentarioId}`);
            let respuestasContainer = document.querySelector(`#respuestas-${comentarioId}`);

            if (!respuestasContainer) {
                // Crear contenedor para respuestas si no existe
                const review = this.closest('.review');
                respuestasContainer = document.createElement('div');
                respuestasContainer.id = `respuestas-${comentarioId}`;
                respuestasContainer.className = 'respuestas-container';

                // Insertar después de los botones de acción
                const actionsDiv = review.querySelector('.review-actions');
                if (actionsDiv) {
                    actionsDiv.insertAdjacentElement('afterend', respuestasContainer);
                } else {
                    review.appendChild(respuestasContainer);
                }
            }

            if (!respuestaFormContainer) {
                // Crear contenedor para el formulario de respuesta si no existe
                respuestaFormContainer = document.createElement('div');
                respuestaFormContainer.id = `respuesta-form-container-${comentarioId}`;
                respuestaFormContainer.className = 'respuesta-form-container';
                respuestaFormContainer.style.display = 'none';

                // Insertar después del contenedor de respuestas
                respuestasContainer.insertAdjacentElement('afterend', respuestaFormContainer);
            }

            // Mostrar/ocultar formulario
            if (respuestaFormContainer.style.display === 'none' || !respuestaFormContainer.style.display) {
                respuestaFormContainer.style.display = 'block';
                this.innerHTML = '<i class="fas fa-times"></i> Cancelar respuesta';
            } else {
                respuestaFormContainer.style.display = 'none';
                this.innerHTML = '<i class="fas fa-reply"></i> Responder';
                return;
            }

            // Si ya hay un formulario, no crear otro
            if (respuestaFormContainer.querySelector('form')) {
                return;
            }

            // Crear formulario de respuesta dinámicamente
            const form = document.createElement('form');
            form.classList.add('respuesta-form');
            form.innerHTML = `
                <textarea name="respuesta" class="respuesta-textarea" placeholder="Escribe tu respuesta..."></textarea>
                <div class="form-check">
                    <input type="checkbox" id="es_spoiler_${comentarioId}" name="es_spoiler" class="form-check-input">
                    <label for="es_spoiler_${comentarioId}" class="form-check-label">Esta respuesta contiene spoilers</label>
                </div>
                <button type="submit" class="btn-submit">Enviar respuesta</button>
            `;

            respuestaFormContainer.appendChild(form);

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const respuestaText = this.querySelector('textarea[name="respuesta"]').value;
                const esSpoiler = this.querySelector('input[name="es_spoiler"]').checked;
                const submitButton = this.querySelector('button[type="submit"]');

                if (!respuestaText.trim()) {
                    showNotification('La respuesta no puede estar vacía', 'error');
                    return;
                }

                // Bloquear botón mientras se envía
                const originalText = submitButton.innerText;
                submitButton.disabled = true;
                submitButton.innerText = 'Enviando...';

                // Construir URL absoluta para evitar problemas de rutas relativas
                const baseUrl = window.location.origin;
                const respuestasUrl = `${baseUrl}/api/respuestas_comentarios`;
                console.log("URL para enviar respuesta:", respuestasUrl);

                // Enviar respuesta mediante AJAX
                fetch(respuestasUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        comentario_id: comentarioId,
                        user_id: userIdMeta.getAttribute('content'),
                        respuesta: respuestaText,
                        es_spoiler: esSpoiler
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al enviar la respuesta');
                    }
                    return response.json();
                })
                .then(data => {
                    showNotification('Tu respuesta ha sido publicada correctamente', 'success');
                    // Recargar comentarios para mostrar la nueva respuesta
                    cargarComentarios();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al publicar tu respuesta', 'error');
                })
                .finally(() => {
                    // Restaurar botón
                    submitButton.disabled = false;
                    submitButton.innerText = originalText;
                });
            });
        });
    });
}

// Función para manejar los likes y dislikes
function handleLikeDislike(button, tipo) {
    // Verificar si el usuario está autenticado
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta || !userIdMeta.getAttribute('content')) {
        showNotification('Debes iniciar sesión para valorar comentarios', 'warning');
        return;
    }

    const comentarioId = button.getAttribute('data-comentario-id');
    const userId = userIdMeta.getAttribute('content');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mostrar indicador de carga
    button.disabled = true;
    const iconElement = button.querySelector('i');
    const originalIcon = iconElement.className;
    iconElement.className = 'fas fa-spinner fa-spin';

    // Construir URL absoluta
    const baseUrl = window.location.origin;
    const likesUrl = `${baseUrl}/api/likes_comentarios`;
    console.log("Enviando like/dislike a:", likesUrl);

    // Enviar solicitud a la API usando JSON en lugar de FormData
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
            }

            // Mostrar notificación
            const accionTexto = data.message.includes('creado') ? 'añadido' :
                               (data.message.includes('actualizado') ? 'actualizado' : 'eliminado');
            showNotification(`Se ha ${accionTexto} tu ${tipo}`, 'success');

            // Actualizar notificaciones en tiempo real si se ha creado o actualizado un like
            if (data.message.includes('creado') || data.message.includes('actualizado')) {
                actualizarNotificaciones();
            }
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
    // Verificar si existe el elemento de notificaciones
    const notificacionesList = document.getElementById('notificaciones-list');
    const notificacionesCounter = document.getElementById('notificaciones-counter');
    const notificacionesContent = document.getElementById('notificaciones-content');

    // Si no existe el panel de notificaciones, salir de la función
    if (!notificacionesCounter || !notificacionesList) {
        return;
    }

    // Obtener ID del usuario
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId) {
        return;
    }

    // Construir URL absoluta
    const baseUrl = window.location.origin;
    const notificacionesUrl = `${baseUrl}/api/notificaciones/user/${userId}`;
    console.log("Obteniendo notificaciones de:", notificacionesUrl);

    fetch(notificacionesUrl)
        .then(response => response.json())
        .then(data => {
            // Obtener elementos de notificaciones

            // Contar notificaciones no leídas
            const noLeidas = data.filter(n => !n.leido).length;

            // Actualizar contador
            if (noLeidas > 0) {
                notificacionesCounter.textContent = noLeidas > 9 ? '9+' : noLeidas;
                notificacionesCounter.style.display = 'block';
            } else {
                notificacionesCounter.style.display = 'none';
            }

            // Actualizar la lista de notificaciones si está visible
            if (notificacionesList && notificacionesContent.classList.contains('show')) {
                // Renderizar las notificaciones (esta función debe estar definida en el blade)
                if (typeof renderizarNotificaciones === 'function') {
                    renderizarNotificaciones(data);
                } else {
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
        .catch(error => console.error('Error al actualizar notificaciones:', error));
}

// Generar estrellas HTML
function generateStarRating(rating) {
    let starsHTML = '';
    const totalStars = 5;

    for (let i = 1; i <= totalStars; i++) {
        if (i <= rating) {
            starsHTML += '<i class="fas fa-star"></i>';
        } else {
            starsHTML += '<i class="far fa-star"></i>';
        }
    }

    return starsHTML;
}

// ========================================================
// FUNCIONES PARA MODALES DE TRAILERS Y DETALLES
// ========================================================
function setupModalEvents() {
    // Configurar botones de trailer en series similares
    document.querySelectorAll('.btn-trailer').forEach(button => {
        button.addEventListener('click', function() {
            const serieId = this.getAttribute('data-id');
            if (serieId) {
                loadSerieTrailer(serieId);
            }
        });
    });

    // Configurar botones de favoritos en series similares
    document.querySelectorAll('.btn-favorite').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const serieId = this.getAttribute('data-id');
            if (serieId) {
                toggleFavorite(serieId, this);
            }
        });
    });

    // Configurar cierre de modales con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModals();
        }
    });

    // Cerrar modal de trailer al hacer clic fuera
    const trailerModal = document.getElementById('trailerModalStatic');
    if (trailerModal) {
        trailerModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTrailerModal();
            }
        });

        // Configurar botón de cierre del trailer
        const closeBtn = document.getElementById('closeTrailerBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeTrailerModal);
        }
    }
}

// ========================================================
// SISTEMA DE NOTIFICACIONES
// ========================================================
function showNotification(message, type = 'info') {
    // Verificar si existe el contenedor de notificaciones, sino crearlo
    let notificationContainer = document.getElementById('notifications-container');

    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notifications-container';
        document.body.appendChild(notificationContainer);
    }

    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.animation = 'fadeIn 0.3s ease-out';

    // Contenido de la notificación
    notification.innerHTML = `
        <div>${message}</div>
        <button class="notification-close">&times;</button>
    `;

    // Configurar botón de cierre
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.classList.add('hide');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });

    // Añadir notificación al contenedor
    notificationContainer.appendChild(notification);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.classList.add('hide');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}

// ========================================================
// FUNCIONES AUXILIARES
// ========================================================
function extractSerieIdFromURL() {
    // Intentar obtener el ID de la URL primero (formato: /series/{id})
    const urlParts = window.location.pathname.split('/');
    return urlParts[urlParts.length - 1];
}

function loadSerieTrailer(serieId) {
    // Mostrar modal de trailer
    const trailerModal = document.getElementById('trailerModalStatic');
    const trailerContainer = document.getElementById('trailerContainerStatic');

    if (!trailerModal || !trailerContainer) {
        console.error('Error: No se encontraron elementos del modal de trailer');
        return;
    }

    // Mostrar estado de carga en el contenedor
    trailerContainer.innerHTML = `
        <div class="trailer-loading">
            <div class="spinner"></div>
            <p>Cargando trailer...</p>
        </div>
    `;

    // Mostrar el modal
    trailerModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Cargar información del trailer desde la API
    fetch(`${BASE_URL}/tv/${serieId}/videos?api_key=${API_KEY}&language=es-ES`)
        .then(response => response.json())
        .then(data => {
            // Buscar el trailer entre los videos
            const videos = data.results || [];
            const trailer = findTrailer(videos);

            if (trailer) {
                // Mostrar trailer
                trailerContainer.innerHTML = `
                    <iframe
                        width="100%"
                        height="100%"
                        src="https://www.youtube.com/embed/${trailer.key}?autoplay=1"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                `;
            } else {
                // Mostrar mensaje de que no hay trailer
                trailerContainer.innerHTML = `
                    <div class="no-trailer">
                        <i class="fas fa-video-slash"></i>
                        <h3>No se encontró trailer</h3>
                        <p>Lo sentimos, no hay trailer disponible para esta serie.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error cargando trailer:', error);
            trailerContainer.innerHTML = `
                <div class="no-trailer">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error al cargar el trailer</h3>
                    <p>Ocurrió un problema al intentar cargar el trailer. Por favor, inténtalo de nuevo más tarde.</p>
                </div>
            `;
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

function closeTrailerModal() {
    const trailerModal = document.getElementById('trailerModalStatic');
    const trailerContainer = document.getElementById('trailerContainerStatic');

    if (trailerModal) {
        trailerModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (trailerContainer) {
        trailerContainer.innerHTML = ''; // Limpiar el contenedor para detener la reproducción
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
