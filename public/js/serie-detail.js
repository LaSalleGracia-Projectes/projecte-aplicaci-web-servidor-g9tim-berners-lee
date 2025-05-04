// ========================================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// ========================================================
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f"; // Reemplazar con tu API key real
const IMG_URL = "https://image.tmdb.org/t/p/w500";
const BACKDROP_URL = "https://image.tmdb.org/t/p/original";

// ========================================================
// FUNCIONES DE AYUDA
// ========================================================
function showSpinner() {
    const spinner = document.querySelector(".spinner-overlay") || createSpinner();
    spinner.classList.add("active");
    document.body.classList.add("loading");
}

function hideSpinner() {
    const spinner = document.querySelector(".spinner-overlay");
    if (spinner) {
        spinner.classList.remove("active");
        document.body.classList.remove("loading");
    }
}

function createSpinner() {
    const spinner = document.createElement("div");
    spinner.className = "spinner-overlay";
    spinner.innerHTML = `
        <div class="spinner">
            <div class="spinner-inner"></div>
        </div>
    `;
    document.body.appendChild(spinner);
    return spinner;
}

// ========================================================
// GESTOR DE TRAILER
// ========================================================
async function loadTrailer() {
    showSpinner();
    try {
        const response = await fetch(`${BASE_URL}/tv/${SERIE_ID}/videos?api_key=${API_KEY}&language=es-ES`);
        const data = await response.json();
        const trailers = data.results.filter(video => video.type === "Trailer");

        if (trailers.length > 0) {
            const trailer = trailers[0];
            showTrailerModal(trailer.key);
        } else {
            showNotification("No hay trailer disponible", "error");
        }
    } catch (error) {
        console.error("Error cargando trailer:", error);
        showNotification("Error al cargar el trailer", "error");
    } finally {
        hideSpinner();
    }
}

function showTrailerModal(videoKey) {
    // Crear el modal sin el iframe primero
    const modal = document.createElement("div");
    modal.className = "trailer-modal";
    modal.innerHTML = `
        <div class="trailer-modal-content">
            <button class="close-modal">&times;</button>
            <div id="serieDetailTrailerContainer"></div>
        </div>
    `;

    // Añadir el modal al DOM
    document.body.appendChild(modal);
    document.body.style.overflow = "hidden";

    // Configurar el cierre del modal
    const closeBtn = modal.querySelector(".close-modal");
    const closeModal = () => {
        modal.remove();
        document.body.style.overflow = "";
    };

    closeBtn.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Cargar el iframe después de que el modal esté en el DOM
    requestAnimationFrame(() => {
        const trailerContainer = document.getElementById("serieDetailTrailerContainer");
        trailerContainer.innerHTML = `
            <iframe
                width="100%"
                height="100%"
                src="https://www.youtube.com/embed/${videoKey}?autoplay=1"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        `;
    });
}

// ========================================================
// GESTOR DE FAVORITOS
// ========================================================
function toggleFavorite() {
    const btn = document.querySelector(".btn-favorite");
    if (!btn) return;

    const isFavorite = btn.classList.contains("active");
    const icon = btn.querySelector("i");

    if (isFavorite) {
        btn.classList.remove("active");
        icon.classList.remove("fas");
        icon.classList.add("far");
        showNotification("Serie eliminada de favoritos", "success");
    } else {
        btn.classList.add("active");
        icon.classList.remove("far");
        icon.classList.add("fas");
        showNotification("Serie añadida a favoritos", "success");
    }
}

// ========================================================
// GESTOR DE COMPARTIR
// ========================================================
function shareSerie() {
    const url = window.location.href;
    const title = document.querySelector("h1").textContent;

    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(console.error);
    } else {
        // Fallback para navegadores que no soportan la API Web Share
        const tempInput = document.createElement("input");
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        showNotification("URL copiada al portapapeles", "success");
    }
}

// ========================================================
// NOTIFICACIONES
// ========================================================
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === "success" ? "fa-check-circle" : "fa-info-circle"}"></i>
            <span>${message}</span>
        </div>
        <button class="close-notification">&times;</button>
    `;

    document.body.appendChild(notification);

    // Animar entrada
    gsap.fromTo(notification,
        { y: -50, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" }
    );

    // Auto-cerrar después de 3 segundos
    setTimeout(() => {
        gsap.to(notification, {
            y: -50,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => notification.remove()
        });
    }, 3000);

    // Cerrar manualmente
    const closeBtn = notification.querySelector(".close-notification");
    closeBtn.addEventListener("click", () => {
        gsap.to(notification, {
            y: -50,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => notification.remove()
        });
    });
}

// ========================================================
// INICIALIZACIÓN
// ========================================================
document.addEventListener("DOMContentLoaded", () => {
    // Configurar botones
    const watchBtn = document.querySelector(".btn-watch");
    const favoriteBtn = document.querySelector(".btn-favorite");
    const shareBtn = document.querySelector(".btn-share");

    if (watchBtn) {
        watchBtn.addEventListener("click", loadTrailer);
    }

    if (favoriteBtn) {
        favoriteBtn.addEventListener("click", toggleFavorite);
    }

    if (shareBtn) {
        shareBtn.addEventListener("click", shareSerie);
    }

    // Animar elementos al cargar
    gsap.from(".serie-header-content", {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: "power2.out"
    });

    gsap.from(".serie-content", {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.3,
        ease: "power2.out"
    });

    gsap.from(".temporadas-section", {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.6,
        ease: "power2.out"
    });

    gsap.from(".similares-section", {
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.9,
        ease: "power2.out"
    });
});
