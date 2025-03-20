// ===== DEFINICIONES BÁSICAS =====
// URL base de la API, clave de acceso e imagen
const BASE_URL = "https://api.themoviedb.org/3";
const API_KEY = "ba232569da1aac2f9b80a35300d0b04f"; // Reemplaza con tu API key real
const IMG_URL = "https://image.tmdb.org/t/p/w500";

// ===== GESTIÓN DEL SPINNER =====
// Si no tienes un spinner predefinido en el HTML, lo creamos dinámicamente.
function showSpinner() {
  let spinner = document.getElementById("spinner");
  if (!spinner) {
    spinner = document.createElement("div");
    spinner.id = "spinner";
    spinner.style.position = "fixed";
    spinner.style.top = "50%";
    spinner.style.left = "50%";
    spinner.style.transform = "translate(-50%, -50%)";
    spinner.style.padding = "1rem 2rem";
    spinner.style.backgroundColor = "rgba(0,0,0,0.8)";
    spinner.style.color = "#fff";
    spinner.style.zIndex = "10000";
    spinner.style.borderRadius = "8px";
    spinner.innerText = "Cargando...";
    document.body.appendChild(spinner);
  }
  spinner.style.display = "block";
}

function hideSpinner() {
  const spinner = document.getElementById("spinner");
  if (spinner) spinner.style.display = "none";
}

// ===== CARGAR PELÍCULAS DESTACADAS ALEATORIAS =====
async function cargarRandomMovies() {
  showSpinner();
  try {
    // Obtenemos películas populares de la API
    const res = await fetch(`${BASE_URL}/movie/popular?api_key=${API_KEY}&language=es`);
    const data = await res.json();
    let movies = data.results;

    // Mezclar la lista de películas (algoritmo de Fisher-Yates)
    for (let i = movies.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [movies[i], movies[j]] = [movies[j], movies[i]];
    }

    // Seleccionamos las 6 primeras películas aleatorias
    const randomMovies = movies.slice(0, 6);
    renderRandomMovies(randomMovies);
  } catch (error) {
    console.error("Error cargando películas aleatorias:", error);
  } finally {
    hideSpinner();
  }
}

function renderRandomMovies(movies) {
  const container = document.getElementById("randomMoviesContainer");
  if (!container) return;
  container.innerHTML = "";
  movies.forEach(movie => {
    // Construir la URL del poster y extraer el año de estreno
    const posterUrl = movie.poster_path ? `${IMG_URL}${movie.poster_path}` : 'images/no-poster.jpg';
    const year = movie.release_date ? movie.release_date.split("-")[0] : "N/A";
    const card = document.createElement("div");
    card.classList.add("movie-card");
    card.setAttribute("data-title", movie.title.toLowerCase());
    card.setAttribute("data-year", year);
    card.setAttribute("data-rating", movie.vote_average || 0);
    card.innerHTML = `
      <img src="${posterUrl}" alt="${movie.title}">
      <div class="movie-info">
          <h3>${movie.title}</h3>
          <p class="rating">Puntuación: ${movie.vote_average}/10</p>
          <a href="/pelicula/${movie.id}" class="btn-details">Ver Detalles</a>
      </div>
    `;
    container.appendChild(card);
  });
}

// ===== FILTRADO DE "TODAS LAS PELÍCULAS" =====
function filterMovies() {
  const searchInput = document.getElementById('searchInput');
  const yearSelect = document.getElementById('yearSelect');
  const minRatingInput = document.getElementById('minRating');
  const movieCards = document.querySelectorAll('#allMoviesContainer .movie-card');

  const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";
  const selectedYear = yearSelect ? yearSelect.value : "";
  const minRating = minRatingInput ? parseFloat(minRatingInput.value) : 0;

  movieCards.forEach(card => {
    const title = card.getAttribute('data-title');
    const year = card.getAttribute('data-year');
    const rating = parseFloat(card.getAttribute('data-rating') || 0);

    const matchesSearch = title.includes(searchTerm);
    const matchesYear = selectedYear === '' || year === selectedYear;
    const matchesRating = rating >= minRating;

    card.style.display = (matchesSearch && matchesYear && matchesRating) ? 'block' : 'none';
  });
}

// Función debounce para optimizar la búsqueda
function debounce(func, delay) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

// ===== EVENTOS AL CARGAR EL DOM =====
document.addEventListener("DOMContentLoaded", () => {
  // Cargar las películas destacadas aleatorias
  cargarRandomMovies();

  // Configurar eventos de filtrado si existen los elementos
  if (document.getElementById('allMoviesContainer')) {
    const searchInput = document.getElementById('searchInput');
    const yearSelect = document.getElementById('yearSelect');
    const minRatingInput = document.getElementById('minRating');
    const ratingValue = document.getElementById('ratingValue');

    if (minRatingInput && ratingValue) {
      minRatingInput.addEventListener('input', function() {
        ratingValue.textContent = this.value;
        filterMovies();
      });
    }

    if (yearSelect) {
      yearSelect.addEventListener('change', filterMovies);
    }

    if (searchInput) {
      searchInput.addEventListener('input', debounce(filterMovies, 300));
    }
  }
});
