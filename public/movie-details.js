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
            alert(isActive ? activeMessage : inactiveMessage);
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
                prompt("Comparte este enlace:", window.location.href);
            }
        });
    }
});
