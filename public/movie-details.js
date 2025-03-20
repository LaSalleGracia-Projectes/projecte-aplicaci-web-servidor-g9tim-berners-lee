// movie-details.js
document.addEventListener('DOMContentLoaded', function() {
    // Gestión de tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Desactivar todos los tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));

            // Activar el tab seleccionado
            const tabId = this.getAttribute('data-tab');
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Sistema de rating con estrellas
    const stars = document.querySelectorAll('.stars .fa-star');
    let selectedRating = 0;

    stars.forEach(star => {
        // Hover effect
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(rating);
        });

        // Click para seleccionar
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            highlightStars(selectedRating);
        });
    });

    // Al salir del área de estrellas se restaura la selección
    document.querySelector('.stars').addEventListener('mouseleave', function() {
        highlightStars(selectedRating);
    });

    // Función para iluminar estrellas
    function highlightStars(count) {
        stars.forEach((star, index) => {
            if (index < count) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }

    // Botón para enviar crítica
    const submitButton = document.querySelector('.btn-submit-review');
    if (submitButton) {
        submitButton.addEventListener('click', function() {
            const reviewText = document.querySelector('.add-review textarea').value.trim();

            if (selectedRating === 0) {
                alert('Por favor, selecciona una puntuación');
                return;
            }

            if (reviewText === '') {
                alert('Por favor, escribe tu opinión sobre la película');
                return;
            }

            // Aquí iría el código para enviar la crítica al servidor
            alert('Gracias por tu crítica. Se publicará después de ser revisada.');

            // Reiniciar formulario
            document.querySelector('.add-review textarea').value = '';
            selectedRating = 0;
            highlightStars(0);
        });
    }

    // Botones de acción
    const btnFavorite = document.querySelector('.btn-favorite');
    if (btnFavorite) {
        btnFavorite.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                alert('Película añadida a favoritos');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                alert('Película eliminada de favoritos');
            }
        });
    }

    const btnWatchlist = document.querySelector('.btn-watchlist');
    if (btnWatchlist) {
        btnWatchlist.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                alert('Película añadida a "Ver más tarde"');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                alert('Película eliminada de "Ver más tarde"');
            }
        });
    }

    const btnShare = document.querySelector('.btn-share');
    if (btnShare) {
        btnShare.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.querySelector('h1').innerText + ' - CrítiFlix',
                    text: 'Échale un vistazo a esta película en CrítiFlix',
                    url: window.location.href
                })
                .catch(error => console.log('Error compartiendo:', error));
            } else {
                prompt('Comparte este enlace:', window.location.href);
            }
        });
    }
});
