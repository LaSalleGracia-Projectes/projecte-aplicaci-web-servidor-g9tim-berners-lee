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

    // Sistema de rating con estrellas
    const stars = document.querySelectorAll(".stars .fa-star");
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener("mouseover", function () {
            highlightStars(parseInt(this.getAttribute("data-rating")));
        });

        star.addEventListener("click", function () {
            selectedRating = parseInt(this.getAttribute("data-rating"));
            highlightStars(selectedRating);
        });
    });

    document.querySelector(".stars").addEventListener("mouseleave", function () {
        highlightStars(selectedRating);
    });

    function highlightStars(count) {
        stars.forEach((star, index) => {
            star.classList.toggle("fas", index < count);
            star.classList.toggle("far", index >= count);
        });
    }

    // Botón para enviar crítica
    const submitButton = document.querySelector(".btn-submit-review");
    if (submitButton) {
        submitButton.addEventListener("click", function () {
            const reviewText = document.querySelector(".add-review textarea").value.trim();

            if (!selectedRating) return alert("Por favor, selecciona una puntuación");
            if (!reviewText) return alert("Por favor, escribe tu opinión sobre la película");

            alert("Gracias por tu crítica. Se publicará después de ser revisada.");
            document.querySelector(".add-review textarea").value = "";
            selectedRating = 0;
            highlightStars(0);
        });
    }

    // Botones de acción (Favoritos, Ver más tarde, Compartir)
    const toggleButton = (selector, activeMessage, inactiveMessage) => {
        const button = document.querySelector(selector);
        if (!button) return;

        button.addEventListener("click", function () {
            this.classList.toggle("active");
            const icon = this.querySelector("i");
            const isActive = this.classList.contains("active");
            icon.classList.toggle("fas", isActive);
            icon.classList.toggle("far", !isActive);
            console.log(isActive ? activeMessage : inactiveMessage);
        });
    };

    toggleButton(".btn-favorite", "Película añadida a favoritos", "Película eliminada de favoritos");
    toggleButton(".btn-watchlist", "Película añadida a 'Ver más tarde'", "Película eliminada de 'Ver más tarde'");

    const btnShare = document.querySelector(".btn-share");
    if (btnShare) {
        btnShare.addEventListener("click", function () {
            if (navigator.share) {
                navigator.share({
                    title: document.querySelector("h1").innerText + " - CrítiFlix",
                    text: "Échale un vistazo a esta película en CrítiFlix",
                    url: window.location.href
                }).catch(error => console.log("Error compartiendo:", error));
            } else {
                console.log("Compartir enlace:", window.location.href);
            }
        });
    }

    // Sistema de comentarios
    function initComentarios() {
        const peliculaId = document.querySelector('#review-form input[name="id_pelicula"]').value;
        const userData = JSON.parse(sessionStorage.getItem('userData'));
        const reviewForm = document.getElementById('review-form');
        const reviewsList = document.querySelector('.reviews-list');

        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!userData) {
                    console.log('Error: Usuario no autenticado');
                    return;
                }

                if (!selectedRating) {
                    console.log('Error: Debes seleccionar una puntuación');
                    return;
                }

                const formData = new FormData(this);
                formData.append('user_id', userData.id);
                formData.append('rating', selectedRating);

                console.log('Enviando crítica...', {
                    id_pelicula: formData.get('id_pelicula'),
                    comentario: formData.get('comentario'),
                    es_spoiler: formData.get('es_spoiler'),
                    user_id: formData.get('user_id'),
                    rating: selectedRating
                });

                fetch('/api/comentarios', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta del servidor:', data);

                    if (data.success) {
                        console.log('Crítica guardada exitosamente');
                        this.reset();
                        selectedRating = 0;
                        highlightStars(0);
                        cargarComentarios(peliculaId, reviewsList, userData);
                    } else {
                        console.error('Error al guardar la crítica:', data.errors);
                    }
                })
                .catch(error => {
                    console.error('Error en la petición:', error);
                });
            });
        }

        // Cargar comentarios inicialmente
        cargarComentarios(peliculaId, reviewsList, userData);
    }

    function cargarComentarios(peliculaId, reviewsList, userData) {
        console.log('Cargando críticas para película:', peliculaId);

        fetch(`/api/comentarios/pelicula/${peliculaId}`)
            .then(response => response.json())
            .then(comentarios => {
                console.log('Críticas recibidas:', comentarios);
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
                                <img src="${comentario.usuario.foto_perfil || '/images/default-avatar.png'}"
                                     alt="${comentario.usuario.name}"
                                     class="avatar">
                                <div>
                                    <span class="username">${comentario.usuario.name}</span>
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
            })
            .catch(error => {
                console.error('Error al cargar críticas:', error);
            });
    }

    function eliminarComentario(id, peliculaId, reviewsList, userData) {
        console.log('Intentando eliminar crítica:', id);

        fetch(`/api/comentarios/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userData.id,
                user_rol: userData.rol
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de eliminación:', data);

            if (data.success) {
                console.log('Crítica eliminada exitosamente');
                cargarComentarios(peliculaId, reviewsList, userData);
            } else {
                console.error('Error al eliminar la crítica:', data.errors);
            }
        })
        .catch(error => {
            console.error('Error en la petición de eliminación:', error);
        });
    }

    // Inicializar el sistema de comentarios
    initComentarios();
});
