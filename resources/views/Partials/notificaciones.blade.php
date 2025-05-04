@auth
    <div id="notificaciones-dropdown" class="notificaciones-dropdown">
        <button id="notificaciones-btn" class="notificaciones-btn">
            <i class="far fa-bell"></i>
            <span id="notificaciones-counter" class="notificaciones-counter" style="display: none;">0</span>
        </button>
        <div id="notificaciones-content" class="notificaciones-content">
            <div class="notificaciones-header">
                <h3>Notificaciones</h3>
                <button id="marcar-todas-leidas" class="marcar-todas-btn">Marcar todas como leídas</button>
            </div>
            <div id="notificaciones-list" class="notificaciones-list">
                <div class="cargando-notificaciones">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando notificaciones...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener elementos del DOM
            const notificacionesBtn = document.getElementById('notificaciones-btn');
            const notificacionesContent = document.getElementById('notificaciones-content');
            const notificacionesList = document.getElementById('notificaciones-list');
            const notificacionesCounter = document.getElementById('notificaciones-counter');
            const marcarTodasBtn = document.getElementById('marcar-todas-leidas');

            // Variables
            const userId = {{ auth()->id() }};
            let notificacionesCargadas = false;

            // Abrir/cerrar dropdown de notificaciones
            notificacionesBtn.addEventListener('click', function() {
                notificacionesContent.classList.toggle('show');

                // Cargar notificaciones si no se han cargado aún
                if (!notificacionesCargadas) {
                    cargarNotificaciones();
                }
            });

            // Cerrar dropdown al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#notificaciones-dropdown') &&
                    notificacionesContent.classList.contains('show')) {
                    notificacionesContent.classList.remove('show');
                }
            });

            // Cargar notificaciones
            function cargarNotificaciones() {
                fetch(`/api/notificaciones/user/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        notificacionesCargadas = true;
                        renderizarNotificaciones(data);
                    })
                    .catch(error => {
                        console.error('Error al cargar notificaciones:', error);
                        notificacionesList.innerHTML = `
                            <div class="sin-notificaciones">
                                <p>Error al cargar notificaciones</p>
                            </div>
                        `;
                    });
            }

            // Renderizar notificaciones
            function renderizarNotificaciones(notificaciones) {
                if (!notificaciones || notificaciones.length === 0) {
                    notificacionesList.innerHTML = `
                        <div class="sin-notificaciones">
                            <i class="fas fa-bell-slash"></i>
                            <p>No tienes notificaciones</p>
                        </div>
                    `;
                    notificacionesCounter.style.display = 'none';
                    return;
                }

                // Contar notificaciones no leídas
                const noLeidas = notificaciones.filter(n => !n.leido).length;
                if (noLeidas > 0) {
                    notificacionesCounter.textContent = noLeidas > 9 ? '9+' : noLeidas;
                    notificacionesCounter.style.display = 'block';
                } else {
                    notificacionesCounter.style.display = 'none';
                }

                // Generar HTML de notificaciones
                let html = '';

                notificaciones.forEach(notificacion => {
                    const fecha = new Date(notificacion.created_at);
                    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });

                    // Analizar el mensaje para determinar mejor el tipo cuando sea necesario
                    const mensaje = notificacion.mensaje || '';
                    let tipoReal = notificacion.tipo || 'nuevo_comentario';
                    let icono = 'fa-bell';

                    // Determinar icono basado en el tipo o el contenido del mensaje
                    if (tipoReal === 'nuevo_like' || mensaje.includes('like') || mensaje.includes('ha dado like')) {
                        icono = 'fa-thumbs-up';
                        tipoReal = 'nuevo_like';
                    } else if (mensaje.includes('respuesta') || mensaje.includes('ha respondido')) {
                        // Si el mensaje contiene palabras relativas a respuestas, usar el icono de respuesta
                        // aunque el tipo en la base de datos sea 'nuevo_comentario'
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

                notificacionesList.innerHTML = html;

                // Añadir evento de marcar como leída
                document.querySelectorAll('.marcar-leida-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const notificacionEl = this.closest('.notificacion-item');
                        const id = notificacionEl.dataset.id;

                        marcarComoLeida(id, notificacionEl);
                    });
                });

                // Añadir evento de clic en notificación (marcar como leída y redirigir si hay URL)
                document.querySelectorAll('.notificacion-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const notificacionEl = this;

                        // Si la notificación no está leída, marcarla como leída
                        if (this.classList.contains('no-leida')) {
                            marcarComoLeida(id, notificacionEl);
                        }

                        // Aquí se puede agregar la lógica de redirección si hay una URL asociada
                        // Por ahora, simplemente cerramos el dropdown
                        notificacionesContent.classList.remove('show');
                    });
                });
            }

            // Marcar una notificación como leída
            function marcarComoLeida(id, element) {
                fetch(`/api/notificaciones/read/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Actualizar la apariencia visual
                    element.classList.remove('no-leida');

                    // Actualizar contador de notificaciones
                    cargarNotificaciones();
                })
                .catch(error => {
                    console.error('Error al marcar notificación como leída:', error);
                });
            }

            // Marcar todas las notificaciones como leídas
            marcarTodasBtn.addEventListener('click', function() {
                fetch(`/api/notificaciones/read_all/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Actualizar todas las notificaciones en UI
                    document.querySelectorAll('.notificacion-item').forEach(item => {
                        item.classList.remove('no-leida');
                    });

                    // Ocultar contador
                    notificacionesCounter.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error al marcar todas como leídas:', error);
                });
            });

            // Comprobar notificaciones periódicamente
            function comprobarNotificaciones() {
                fetch(`/api/notificaciones/user/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Contar notificaciones no leídas
                        const noLeidas = data.filter(n => !n.leido).length;

                        if (noLeidas > 0) {
                            notificacionesCounter.textContent = noLeidas > 9 ? '9+' : noLeidas;
                            notificacionesCounter.style.display = 'block';
                        } else {
                            notificacionesCounter.style.display = 'none';
                        }

                        // Si el dropdown está abierto, actualizar la lista
                        if (notificacionesContent.classList.contains('show')) {
                            renderizarNotificaciones(data);
                        }
                    })
                    .catch(error => console.error('Error al comprobar notificaciones:', error));
            }

            // Comprobar notificaciones cada 30 segundos
            setInterval(comprobarNotificaciones, 30000);

            // Comprobar notificaciones al cargar la página
            comprobarNotificaciones();
        });
    </script>
@endauth
