document.addEventListener("DOMContentLoaded", function () {
    // Gestión de tabs
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabPanels = document.querySelectorAll(".tab-panel");

    tabButtons.forEach(button => {
        button.addEventListener("click", function () {
            tabButtons.forEach(btn => btn.classList.remove("active"));
            tabPanels.forEach(panel => panel.classList.remove("active"));

            this.classList.add("active");
            document.getElementById(this.getAttribute("data-tab")).classList.add("active");
        });
    });

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
