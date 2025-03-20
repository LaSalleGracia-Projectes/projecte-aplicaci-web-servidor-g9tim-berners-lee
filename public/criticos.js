document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    const criticSearch = document.getElementById('criticSearch');
    const genreFilter = document.getElementById('genreFilter');
    const sortBy = document.getElementById('sortBy');
    const criticsGrid = document.getElementById('criticsGrid');
    const trendingReviews = document.getElementById('trendingReviews');
    const reviewsPrev = document.getElementById('reviewsPrev');
    const reviewsNext = document.getElementById('reviewsNext');
    const followButtons = document.querySelectorAll('.follow-btn');

    // Animación al cargar la página
    animatePageLoad();

    // Inicializar todos los eventos
    initSearchAndFilters();
    initSlider();
    initFollowButtons();
    initVerifiedBadges();

    /**
     * Funciones de animación
     */
    function animatePageLoad() {
        // Efecto de aparición gradual para los elementos principales
        gsap.from('.hero-section', { duration: 1, opacity: 0, y: -50, ease: 'power3.out' });
        gsap.from('.filter-section', { duration: 0.8, opacity: 0, y: 30, ease: 'power3.out', delay: 0.3 });
        gsap.from('.critics-grid .critic-card', {
            duration: 0.6,
            opacity: 0,
            y: 30,
            stagger: 0.1,
            ease: 'power3.out',
            delay: 0.5
        });
        gsap.from('.trending-reviews-slider .review-card', {
            duration: 0.6,
            opacity: 0,
            x: 30,
            stagger: 0.1,
            ease: 'power3.out',
            delay: 0.7
        });
    }

    /**
     * Configurar búsqueda y filtros
     */
    function initSearchAndFilters() {
        // Búsqueda en tiempo real
        if (criticSearch) {
            criticSearch.addEventListener('input', filterCritics);
        }

        // Filtros de género y ordenación
        if (genreFilter) {
            genreFilter.addEventListener('change', filterCritics);
        }

        if (sortBy) {
            sortBy.addEventListener('change', sortCritics);
        }
    }

    /**
     * Filtrar críticos según los criterios seleccionados
     */
    function filterCritics() {
        const searchTerm = criticSearch.value.toLowerCase();
        const selectedGenre = genreFilter.value.toLowerCase();

        // Obtener todas las tarjetas de críticos
        const criticCards = document.querySelectorAll('.critic-card');

        criticCards.forEach(card => {
            const criticName = card.querySelector('h3').textContent.toLowerCase();
            const criticBio = card.querySelector('.critic-bio').textContent.toLowerCase();
            const criticGenres = card.querySelectorAll('.specialty-tag');

            // Verificar si el nombre o bio coincide con la búsqueda
            const matchesSearch = searchTerm === '' ||
                                  criticName.includes(searchTerm) ||
                                  criticBio.includes(searchTerm);

            // Verificar si el género coincide con el filtro
            let matchesGenre = selectedGenre === '';

            if (!matchesGenre) {
                criticGenres.forEach(genre => {
                    if (genre.textContent.toLowerCase().includes(selectedGenre)) {
                        matchesGenre = true;
                    }
                });
            }

            // Mostrar u ocultar la tarjeta según los criterios
            if (matchesSearch && matchesGenre) {
                card.style.display = 'block';
                gsap.to(card, { duration: 0.3, opacity: 1, scale: 1, ease: 'power2.out' });
            } else {
                gsap.to(card, {
                    duration: 0.3,
                    opacity: 0,
                    scale: 0.95,
                    ease: 'power2.out',
                    onComplete: () => { card.style.display = 'none'; }
                });
            }
        });
    }

    /**
     * Ordenar críticos según el criterio seleccionado
     */
    function sortCritics() {
        const sortCriteria = sortBy.value;
        const criticCards = Array.from(criticsGrid.querySelectorAll('.critic-card'));

        // Ordenar las tarjetas según el criterio seleccionado
        criticCards.sort((a, b) => {
            if (sortCriteria === 'popular') {
                const followersA = parseInt(a.querySelector('.followers').textContent.match(/\d+/)[0]);
                const followersB = parseInt(b.querySelector('.followers').textContent.match(/\d+/)[0]);
                return followersB - followersA; // Ordenar por seguidores (descendente)
            }
            else if (sortCriteria === 'rating') {
                const ratingA = parseFloat(a.querySelector('.rating').textContent.match(/\d+(\.\d+)?/)[0]);
                const ratingB = parseFloat(b.querySelector('.rating').textContent.match(/\d+(\.\d+)?/)[0]);
                return ratingB - ratingA; // Ordenar por calificación (descendente)
            }
            else if (sortCriteria === 'recent') {
                // Aquí podríamos ordenar por fecha de registro si estuviera disponible
                // Por ahora, simulamos con un orden aleatorio
                return Math.random() - 0.5;
            }
        });

        // Eliminar todas las tarjetas del grid
        criticCards.forEach(card => {
            criticsGrid.removeChild(card);
        });

        // Añadir las tarjetas ordenadas de nuevo al grid
        criticCards.forEach((card, index) => {
            criticsGrid.appendChild(card);
            // Animar la reaparición
            gsap.from(card, {
                duration: 0.3,
                opacity: 0,
                y: 20,
                delay: index * 0.05,
                ease: 'power2.out'
            });
        });
    }

    /**
     * Inicializar slider de reseñas
     */
    function initSlider() {
        if (!trendingReviews || !reviewsPrev || !reviewsNext) return;

        const slideWidth = 370; // Ancho de una tarjeta de reseña + margin

        reviewsNext.addEventListener('click', () => {
            trendingReviews.scrollBy({ left: slideWidth * 2, behavior: 'smooth' });
        });

        reviewsPrev.addEventListener('click', () => {
            trendingReviews.scrollBy({ left: -slideWidth * 2, behavior: 'smooth' });
        });
    }

    /**
     * Funcionalidad para botones de seguir
     */
    function initFollowButtons() {
        followButtons.forEach(button => {
            button.addEventListener('click', function() {
                const criticId = this.getAttribute('data-id');
                const isFollowing = this.classList.contains('following');

                if (!isFollowing) {
                    // Enviar petición AJAX para seguir al crítico
                    followCritic(criticId, this);
                } else {
                    // Enviar petición AJAX para dejar de seguir al crítico
                    unfollowCritic(criticId, this);
                }
            });
        });
    }

    /**
     * Enviar petición para seguir a un crítico
     */
    function followCritic(criticId, button) {
        // Simulación de una petición AJAX
        // En un caso real, esto sería una llamada fetch o axios al backend

        // Mostrar efecto de carga
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        setTimeout(() => {
            // Cambiar el estado del botón a "Siguiendo"
            button.innerHTML = '<i class="fas fa-user-check"></i> Siguiendo';
            button.classList.add('following');
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');
            button.disabled = false;

            // Incrementar el contador de seguidores visualmente
            const followersElement = button.closest('.critic-card').querySelector('.followers');
            if (followersElement) {
                const currentFollowers = parseInt(followersElement.textContent.match(/\d+/)[0]);
                followersElement.innerHTML = `<i class="fas fa-users"></i> ${currentFollowers + 1}`;
            }

            // Mostrar notificación
            showNotification('Has comenzado a seguir a este crítico', 'success');

            // En un caso real, enviaríamos una petición al servidor como esta:
            /*
            fetch('/api/critics/follow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ criticId })
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar interfaz según la respuesta
            })
            .catch(error => {
                console.error('Error:', error);
                // Revertir cambios en la interfaz
            });
            */
        }, 800); // Simular retardo de red
    }

    /**
     * Enviar petición para dejar de seguir a un crítico
     */
    function unfollowCritic(criticId, button) {
        // Mostrar efecto de carga
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        setTimeout(() => {
            // Cambiar el estado del botón a "Seguir"
            button.innerHTML = '<i class="fas fa-user-plus"></i> Seguir';
            button.classList.remove('following');
            button.classList.add('btn-secondary');
            button.classList.remove('btn-primary');
            button.disabled = false;

            // Decrementar el contador de seguidores visualmente
            const followersElement = button.closest('.critic-card').querySelector('.followers');
            if (followersElement) {
                const currentFollowers = parseInt(followersElement.textContent.match(/\d+/)[0]);
                followersElement.innerHTML = `<i class="fas fa-users"></i> ${currentFollowers - 1}`;
            }

            // Mostrar notificación
            showNotification('Has dejado de seguir a este crítico', 'info');

            // En un caso real, enviaríamos una petición al servidor
        }, 800); // Simular retardo de red
    }

    /**
     * Inicializar efectos para las insignias de verificación
     */
    function initVerifiedBadges() {
        const verifiedBadges = document.querySelectorAll('.verified-badge');

        verifiedBadges.forEach(badge => {
            // Añadir animación de pulso a las insignias verificadas
            gsap.to(badge, {
                scale: 1.2,
                duration: 1,
                repeat: -1,
                yoyo: true,
                ease: 'power1.inOut'
            });
        });
    }

    /**
     * Mostrar notificación temporal
     */
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        // Añadir al DOM
        document.body.appendChild(notification);

        // Mostrar con animación
        gsap.fromTo(
            notification,
            { y: -100, opacity: 0 },
            { y: 20, opacity: 1, duration: 0.5, ease: 'power3.out' }
        );

        // Ocultar después de 3 segundos
        setTimeout(() => {
            gsap.to(notification, {
                y: -100,
                opacity: 0,
                duration: 0.5,
                ease: 'power3.in',
                onComplete: () => notification.remove()
            });
        }, 3000);
    }

    /**
     * Animaciones para la sección "Hazte Crítico"
     */
    function initBecomeCriticSection() {
        const becomeCriticSection = document.querySelector('.become-critic-section');
        if (!becomeCriticSection) return;

        // Detección de visibilidad para animaciones
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Animar elementos cuando la sección es visible
                    gsap.from('.benefit-item', {
                        y: 50,
                        opacity: 0,
                        duration: 0.7,
                        stagger: 0.15,
                        ease: 'power3.out'
                    });

                    // Animación del botón
                    gsap.from('.become-critic-btn', {
                        scale: 0.8,
                        opacity: 0,
                        duration: 0.5,
                        delay: 0.8,
                        ease: 'back.out(1.7)'
                    });

                    // Desactivar observer después de la animación
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        // Iniciar observación
        observer.observe(becomeCriticSection);
    }

    // Inicializar animaciones para sección "Hazte Crítico"
    initBecomeCriticSection();

    /**
     * Efectos de hover para las tarjetas de críticos
     */
    const criticCards = document.querySelectorAll('.critic-card');
    criticCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            const image = card.querySelector('.critic-image img');
            gsap.to(image, { scale: 1.05, duration: 0.3 });

            const specialtyTags = card.querySelectorAll('.specialty-tag');
            gsap.to(specialtyTags, {
                scale: 1.05,
                stagger: 0.05,
                duration: 0.2
            });
        });

        card.addEventListener('mouseleave', () => {
            const image = card.querySelector('.critic-image img');
            gsap.to(image, { scale: 1, duration: 0.3 });

            const specialtyTags = card.querySelectorAll('.specialty-tag');
            gsap.to(specialtyTags, { scale: 1, duration: 0.2 });
        });
    });

    /**
     * Efectos de hover para las tarjetas de reseñas
     */
    const reviewCards = document.querySelectorAll('.review-card');
    reviewCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -5,
                boxShadow: '0 10px 20px rgba(0,0,0,0.3), 0 0 15px rgba(20, 255, 20, 0.5)',
                duration: 0.3
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                boxShadow: '0 5px 15px rgba(0,0,0,0.1), 0 0 5px rgba(20, 255, 20, 0.2)',
                duration: 0.3
            });
        });
    });

    /**
     * Inicializar integraciones con API de películas externas (TMDB)
     */
    function initMovieApiIntegration() {
        // Solo inicializar si las variables están definidas
        if (typeof API_KEY === 'undefined' || typeof BASE_URL === 'undefined') return;

        // Aquí podríamos cargar información adicional de películas o posters
        console.log('API de películas inicializada');

        // Ejemplo de función para obtener detalles de una película
        function getMovieDetails(movieId) {
            fetch(`${BASE_URL}/movie/${movieId}?api_key=${API_KEY}&language=es-ES`)
                .then(response => response.json())
                .then(data => {
                    // Procesar datos de película
                    console.log('Detalles de película:', data);
                })
                .catch(error => {
                    console.error('Error al obtener detalles de película:', error);
                });
        }

        // Ejemplo de uso para desarrollo
        // getMovieDetails(550); // ID de Fight Club
    }

    // Inicializar integración con API externa si está disponible
    initMovieApiIntegration();
});
