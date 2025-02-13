
// Variables globales para el slider
let bannerSlides = [];
let currentSlide = 0;
let sliderInterval;

// Para almacenar favoritos (usamos localStorage)
const FAVORITES_KEY = 'critiflixFavorites';
// Función para mostrar/ocultar el spinner
function showSpinner() {
  document.getElementById('loadingSpinner').classList.remove('hidden');
}
function hideSpinner() {
  document.getElementById('loadingSpinner').classList.add('hidden');
}
// BANNER / SLIDER
async function cargarBanner() {
  showSpinner();
  try {
    const res = await fetch(`${BASE_URL}/movie/upcoming?api_key=${API_KEY}&language=es`);
    const data = await res.json();
    bannerSlides = data.results.slice(0, 5);
    renderBannerSlides();
    startSlider();
  } catch (error) {
    console.error('Error cargando banner:', error);
  } finally {
    hideSpinner();
  }
}

function renderBannerSlides() {
  const slidesContainer = document.getElementById('bannerSlides');
  slidesContainer.innerHTML = '';
  bannerSlides.forEach((movie, index) => {
    const slide = document.createElement('div');
    slide.classList.add('slide');
    slide.style.backgroundImage = `url(${IMG_URL}${movie.backdrop_path})`;
    slide.innerHTML = `
      <div class="slide-content">
        <h2>${movie.title}</h2>
        <p>${movie.overview.substring(0, 150)}...</p>
      </div>
    `;
    slide.style.opacity = (index === currentSlide) ? '1' : '0';
    slidesContainer.appendChild(slide);
  });
  renderSliderIndicators();
}

function renderSliderIndicators() {
  const indicatorsContainer = document.getElementById('sliderIndicators');
  indicatorsContainer.innerHTML = '';
  bannerSlides.forEach((_, index) => {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (index === currentSlide) dot.classList.add('active');
    dot.addEventListener('click', () => {
      currentSlide = index;
      updateSlider();
      resetSliderInterval();
    });
    indicatorsContainer.appendChild(dot);
  });
}

function updateSlider() {
  const slides = document.querySelectorAll('.slide');
  slides.forEach((slide, index) => {
    slide.style.opacity = (index === currentSlide) ? '1' : '0';
  });
  const dots = document.querySelectorAll('.dot');
  dots.forEach((dot, index) => {
    dot.classList.toggle('active', index === currentSlide);
  });
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % bannerSlides.length;
  updateSlider();
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + bannerSlides.length) % bannerSlides.length;
  updateSlider();
}

function startSlider() {
  sliderInterval = setInterval(nextSlide, 5000);
}

function resetSliderInterval() {
  clearInterval(sliderInterval);
  startSlider();
}

document.getElementById('nextSlide').addEventListener('click', () => {
  nextSlide();
  resetSliderInterval();
});
document.getElementById('prevSlide').addEventListener('click', () => {
  prevSlide();
  resetSliderInterval();
});
// TENDENCIAS
async function cargarTendencias() {
  showSpinner();
  try {
    const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}&language=es`);
    const data = await res.json();
    renderTrending(data.results);
  } catch (error) {
    console.error('Error cargando tendencias:', error);
  } finally {
    hideSpinner();
  }
}

function renderTrending(movies) {
  const trendingContainer = document.getElementById('trendingContainer');
  trendingContainer.innerHTML = '';
  movies.forEach(movie => {
    const card = document.createElement('div');
    card.classList.add('trending-card');
    card.innerHTML = `
      <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}">
      <div class="card-info">
         <h3>${movie.title}</h3>
         <div class="rating">${renderStars(movie.vote_average)}</div>
         <button class="favorite-btn" onclick="toggleFavorite(event, ${movie.id})">❤</button>
      </div>
    `;
    // Al hacer clic en la tarjeta se abre el detalle
    card.addEventListener('click', () => {
      openMovieDetail(movie.id);
    });
    trendingContainer.appendChild(card);
  });
}

function renderStars(vote) {
  const stars = Math.round(vote / 2);
  let html = '';
  for (let i = 1; i <= 5; i++) {
    html += (i <= stars) ? '★' : '☆';
  }
  return html;
}
// FAVORITOS
function toggleFavorite(e, movieId) {
  // Evitar que el clic en el botón se propague al modal de detalle
  e.stopPropagation();
  let favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];
  if (favorites.includes(movieId)) {
    favorites = favorites.filter(id => id !== movieId);
    alert('Eliminado de favoritos');
  } else {
    favorites.push(movieId);
    alert('Guardado en favoritos');
  }
  localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
  renderFavorites();
}

async function renderFavorites() {
  const favoritesContainer = document.getElementById('favoritesContainer');
  favoritesContainer.innerHTML = '';
  const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];
  if (favorites.length === 0) {
    favoritesContainer.innerHTML = '<p>No tienes favoritos aún.</p>';
    return;
  }
  // Buscar detalles de cada película en favoritos
  for (const id of favorites) {
    try {
      const res = await fetch(`${BASE_URL}/movie/${id}?api_key=${API_KEY}&language=es`);
      const movie = await res.json();
      const card = document.createElement('div');
      card.classList.add('trending-card');
      card.innerHTML = `
        <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}">
        <div class="card-info">
           <h3>${movie.title}</h3>
           <div class="rating">${renderStars(movie.vote_average)}</div>
           <button class="favorite-btn" onclick="toggleFavorite(event, ${movie.id})">❤</button>
        </div>
      `;
      // Permitir abrir detalle al hacer clic
      card.addEventListener('click', () => {
        openMovieDetail(movie.id);
      });
      favoritesContainer.appendChild(card);
    } catch (error) {
      console.error('Error obteniendo favorito:', error);
    }
  }
}
// DETALLE DE PELÍCULA (MODAL)
async function openMovieDetail(movieId) {
    showSpinner();
    try {
      const res = await fetch(`${BASE_URL}/movie/${movieId}?api_key=${API_KEY}&language=es`);
      const movie = await res.json();
      const detailContainer = document.getElementById('movieDetailContent');
      detailContainer.innerHTML = `
        <h2>${movie.title}</h2>
        <img src="${IMG_URL}${movie.poster_path}" alt="${movie.title}" style="width:200px; float:left; margin-right:20px;">
        <p><strong>Fecha de lanzamiento:</strong> ${movie.release_date}</p>
        <p><strong>Rating:</strong> ${movie.vote_average} (${renderStars(movie.vote_average)})</p>
        <p>${movie.overview}</p>
        <button class="action-btn" onclick="toggleFavorite(event, ${movie.id})">Favorito</button>
        <a href="/infoPelicula/${movie.id}" class="action-btn">Ver más detalles</a>
      `;
      document.getElementById('movieDetailModal').style.display = 'block';
    } catch (error) {
      console.error('Error abriendo detalle:', error);
    } finally {
      hideSpinner();
    }
  }



document.getElementById('closeMovieDetail').addEventListener('click', () => {
  document.getElementById('movieDetailModal').style.display = 'none';
});
// BÚSQUEDA CON SUGERENCIAS MEJORADA
let searchTimeout;
let currentSuggestionIndex = -1;
document.getElementById('search').addEventListener('input', function(e) {
  const query = this.value.trim();
  currentSuggestionIndex = -1;
  if (searchTimeout) clearTimeout(searchTimeout);
  if (query.length > 2) {
    searchTimeout = setTimeout(() => {
      buscarSugerencias(query);
    }, 300);
  } else {
    document.getElementById('suggestions').innerHTML = '';
  }
});

// Navegación con flechas en sugerencias
document.getElementById('search').addEventListener('keydown', function(e) {
  const suggestions = document.querySelectorAll('.suggestion-item');
  if (suggestions.length === 0) return;
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    currentSuggestionIndex = (currentSuggestionIndex + 1) % suggestions.length;
    updateSuggestionActive(suggestions);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    currentSuggestionIndex = (currentSuggestionIndex - 1 + suggestions.length) % suggestions.length;
    updateSuggestionActive(suggestions);
  } else if (e.key === 'Enter') {
    if (currentSuggestionIndex >= 0) {
      e.preventDefault();
      suggestions[currentSuggestionIndex].click();
    }
  }
});

document.getElementById('search').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      const query = this.value.trim();
      if (query.length > 2) {
        buscarSugerencias(query).then((results) => {
          if (results.length > 0) {
            window.location.href = `/infoPelicula/${results[0].id}`;
          }
        });
      }
    }
  });


function updateSuggestionActive(suggestions) {
  suggestions.forEach((item, idx) => {
    if (idx === currentSuggestionIndex) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
}

async function buscarSugerencias(query) {
    try {
      const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}&language=es`);
      const data = await res.json();
      renderSuggestions(data.results);
      return data.results; // Devolvemos los resultados para usarlos en el Enter
    } catch (error) {
      console.error('Error buscando sugerencias:', error);
      return [];
    }
  }


function renderSuggestions(results) {
    const suggestionsDiv = document.getElementById('suggestions');
    suggestionsDiv.innerHTML = '';
    results.slice(0, 5).forEach((movie) => {
      const item = document.createElement('div');
      item.classList.add('suggestion-item');
      item.textContent = movie.title;
      item.addEventListener('click', () => {
        window.location.href = `/infoPelicula/${movie.id}`;
      });
      suggestionsDiv.appendChild(item);
    });
  }

// CINE RANDOMIZER
async function generarRecomendacion() {
  const tipo = document.getElementById('tipoContenido').value;
  let url = '';
  if (tipo === 'movie') {
    url = `${BASE_URL}/discover/movie?api_key=${API_KEY}&language=es&sort_by=popularity.desc`;
  } else {
    url = `${BASE_URL}/discover/tv?api_key=${API_KEY}&language=es&sort_by=popularity.desc`;
  }
  try {
    showSpinner();
    const res = await fetch(url);
    const data = await res.json();
    if (data.results.length) {
      const randomIndex = Math.floor(Math.random() * data.results.length);
      const content = data.results[randomIndex];
      renderRandomContent(content, tipo);
    }
  } catch (error) {
    console.error('Error generando recomendación:', error);
  } finally {
    hideSpinner();
  }
}

function renderRandomContent(content, tipo) {
  const container = document.getElementById('randomContainer');
  container.innerHTML = `
    <img src="${IMG_URL}${tipo === 'movie' ? content.poster_path : content.backdrop_path}" alt="${content.title || content.name}" width="150">
    <h3>${content.title || content.name}</h3>
    <p>${content.overview.substring(0, 100)}...</p>
    <button class="action-btn" onclick="openMovieDetail(${content.id})">Ver Detalle</button>
  `;
}
// MODALES (LOGIN / REGISTRO)
function setupModals() {
  const loginLink = document.getElementById('loginLink');
  const registerLink = document.getElementById('registerLink');
  const loginModal = document.getElementById('loginModal');
  const registerModal = document.getElementById('registerModal');
  const closeLogin = document.getElementById('closeLogin');
  const closeRegister = document.getElementById('closeRegister');

  loginLink.addEventListener('click', (e) => {
    e.preventDefault();
    loginModal.style.display = 'block';
  });

  registerLink.addEventListener('click', (e) => {
    e.preventDefault();
    registerModal.style.display = 'block';
  });

  closeLogin.addEventListener('click', () => {
    loginModal.style.display = 'none';
  });

  closeRegister.addEventListener('click', () => {
    registerModal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
      loginModal.style.display = 'none';
    }
    if (e.target === registerModal) {
      registerModal.style.display = 'none';
    }
    if (e.target === document.getElementById('movieDetailModal')) {
      document.getElementById('movieDetailModal').style.display = 'none';
    }
  });
}
// NAVEGACIÓN EN TENDENCIAS
document.getElementById('trendingPrev').addEventListener('click', () => {
  document.getElementById('trendingContainer').scrollBy({
    left: -300,
    behavior: 'smooth'
  });
});

document.getElementById('trendingNext').addEventListener('click', () => {
  document.getElementById('trendingContainer').scrollBy({
    left: 300,
    behavior: 'smooth'
  });
});
// ACCIONES EN SECCIÓN CRÍTICOS
document.getElementById('spoilerBtn').addEventListener('click', () => {
  if (confirm('Al hacer clic en los perfiles de críticos podrías ver spoilers. ¿Deseas continuar?')) {
    alert('Spoilers activados.');
  }
});

document.getElementById('hazteCritico').addEventListener('click', () => {
  alert('Formulario para hacerse crítico. (Simulado)');
});
// BOTÓN BACK TO TOP
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
  if (window.scrollY > 300) {
    backToTopBtn.style.display = 'block';
  } else {
    backToTopBtn.style.display = 'none';
  }
});
backToTopBtn.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

//FILTROS DEL RANDOMIZER
async function populateGenres() {
  try {
    const res = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}&language=es`);
    const data = await res.json();
    const generoSelect = document.getElementById('genero');
    data.genres.forEach(genre => {
      const option = document.createElement('option');
      option.value = genre.id;
      option.textContent = genre.name;
      generoSelect.appendChild(option);
    });
  } catch (error) {
    console.error('Error al cargar géneros:', error);
  }
}

function populateYears() {
  const anioSelect = document.getElementById('anio');
  const currentYear = new Date().getFullYear();
  for (let year = currentYear; year >= 1980; year--) {
    const option = document.createElement('option');
    option.value = year;
    option.textContent = year;
    anioSelect.appendChild(option);
  }
}
// INICIALIZACIÓN
window.onload = () => {
  cargarBanner();
  cargarTendencias();
  setupModals();
  populateGenres();
  populateYears();
  renderFavorites();
};
document.getElementById('generarRandom').addEventListener('click', generarRecomendacion);
