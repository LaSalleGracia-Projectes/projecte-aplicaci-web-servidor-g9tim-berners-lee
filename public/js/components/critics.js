import config from '../utils/config.js';
import helpers from '../utils/helpers.js';

const criticsModule = {
    init() {
        this.renderCriticos();
        this.setupSpoilerBtn();
        this.setupHazteCriticoBtn();
    },

    renderCriticos() {
        const criticosContainer = document.querySelector('.criticos-container');
        if (!criticosContainer) return;

        const criticos = [
            {
                nombre: "Ana García",
                imagen: "https://randomuser.me/api/portraits/women/1.jpg",
                rol: "Crítico Experto",
                reviews: 156,
                seguidores: 2.3,
                especialidad: "Cine de Autor",
                bio: "Especialista en cine de autor y documentales. Más de 10 años analizando el séptimo arte.",
                peliculasFavoritas: ["El Padrino", "Cinema Paradiso", "8½"],
                ultimasReviews: [
                    { pelicula: "Oppenheimer", rating: 4.5, texto: "Una obra maestra del cine moderno..." },
                    { pelicula: "Barbie", rating: 4.0, texto: "Una sorprendente sátira social..." }
                ]
            },
            {
                nombre: "Carlos Ruiz",
                imagen: "https://randomuser.me/api/portraits/men/2.jpg",
                rol: "Crítico Verificado",
                reviews: 98,
                seguidores: 1.8,
                especialidad: "Ciencia Ficción",
                bio: "Amante del cine de acción y ciencia ficción. Especializado en efectos visuales.",
                peliculasFavoritas: ["Blade Runner", "Matrix", "Inception"],
                ultimasReviews: [
                    { pelicula: "Dune", rating: 4.8, texto: "Una experiencia visual extraordinaria..." }
                ]
            },
            {
                nombre: "Laura Martínez",
                imagen: "https://randomuser.me/api/portraits/women/3.jpg",
                rol: "Crítico Destacado",
                reviews: 203,
                seguidores: 3.1,
                especialidad: "Cine Independiente",
                bio: "Experta en cine independiente y festivales internacionales.",
                peliculasFavoritas: ["Lost in Translation", "Moonlight", "Lady Bird"],
                ultimasReviews: [
                    { pelicula: "Past Lives", rating: 4.7, texto: "Una joya del cine independiente..." }
                ]
            }
        ];

        criticosContainer.innerHTML = criticos.map(critico => `
            <div class="critico" data-reviews='${JSON.stringify(critico.ultimasReviews)}'>
                <div class="critico-imagen">
                    <img src="${critico.imagen}" alt="${critico.nombre}">
                </div>
                <div class="critico-info">
                    <h3 class="critico-nombre">${critico.nombre}</h3>
                    <span class="badge">${critico.rol}</span>
                    <div class="critico-stats">
                        <div class="stat">
                            <i class="fas fa-star"></i>
                            <span>${critico.reviews} reviews</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            <span>${critico.seguidores}k</span>
                        </div>
                    </div>
                    <div class="critico-especialidad">
                        <i class="fas fa-film"></i>
                        <span>${critico.especialidad}</span>
                    </div>
                    <p class="critico-bio">${critico.bio}</p>
                    <div class="critico-favoritas">
                        <small>Películas favoritas:</small>
                        <div class="favoritas-tags">
                            ${critico.peliculasFavoritas.map(peli => `<span class="favorita-tag">${peli}</span>`).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Navegación en críticos
        const prevBtn = document.getElementById('criticosPrev');
        const nextBtn = document.getElementById('criticosNext');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                criticosContainer.scrollBy({ left: -320, behavior: 'smooth' });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                criticosContainer.scrollBy({ left: 320, behavior: 'smooth' });
            });
        }

        // Interactividad en las tarjetas de críticos
        document.querySelectorAll('.critico').forEach(critico => {
            critico.addEventListener('click', () => {
                const spoilerEnabled = document.getElementById('spoilerEnabled')?.checked;
                if (spoilerEnabled) {
                    this.showCriticoDetail(critico);
                } else {
                    config.showToast('Activa los spoilers para ver las reseñas detalladas', 'info');
                }
            });
        });
    },

    showCriticoDetail(criticoElement) {
        const reviews = JSON.parse(criticoElement.dataset.reviews);
        const nombre = criticoElement.querySelector('.critico-nombre').textContent;

        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Reseñas de ${nombre}</h2>
                <div class="reviews-list">
                    ${reviews.map(review => `
                        <div class="review-item">
                            <h4>${review.pelicula}</h4>
                            <div class="review-rating">
                                ${helpers.renderStars(review.rating * 2)}
                            </div>
                            <p>${review.texto}</p>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'block';

        modal.querySelector('.close').addEventListener('click', () => {
            modal.style.display = 'none';
            setTimeout(() => modal.remove(), 300);
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                setTimeout(() => modal.remove(), 300);
            }
        });
    },

    setupSpoilerBtn() {
        const spoilerBtn = document.getElementById("spoilerBtn");
        if (spoilerBtn) {
            spoilerBtn.addEventListener("click", () => {
                if (confirm("Al hacer clic en los perfiles de críticos podrías ver spoilers. ¿Deseas continuar?")) {
                    // Crear checkbox oculto para control de estado
                    if (!document.getElementById('spoilerEnabled')) {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.id = 'spoilerEnabled';
                        checkbox.style.display = 'none';
                        document.body.appendChild(checkbox);
                    }
                    document.getElementById('spoilerEnabled').checked = true;
                    alert("Spoilers activados.");
                }
            });
        }
    },

    setupHazteCriticoBtn() {
        const hazteCriticoBtn = document.getElementById("hazteCritico");
        if (hazteCriticoBtn) {
            hazteCriticoBtn.addEventListener("click", () => {
                helpers.showToast("Próximamente: Formulario para hacerse crítico", "info");
            });
        }
    }
};

export default criticsModule;
