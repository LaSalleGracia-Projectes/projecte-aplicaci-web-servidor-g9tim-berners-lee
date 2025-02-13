document.addEventListener("DOMContentLoaded", function() {
    const genreSelect = document.getElementById("genreSelect");
    const minRating = document.getElementById("minRating");
    const ratingValue = document.getElementById("ratingValue");
    const allMoviesContainer = document.getElementById("allMoviesContainer");
    const movieCards = allMoviesContainer.querySelectorAll(".movie-card");

    function filterMovies() {
      const selectedGenre = genreSelect.value.toLowerCase();
      const minRatingValue = parseFloat(minRating.value);
      console.log("Filtro de rating mínimo:", minRatingValue);

      movieCards.forEach(card => {
        const genre = card.dataset.genre;
        const rating = parseFloat(card.dataset.rating) || 0;
        console.log("Película:", card.querySelector(".movie-info h3").textContent, "Rating:", rating);

        const matchesGenre = selectedGenre === "" || (genre && genre.includes(selectedGenre));
        const matchesRating = rating >= minRatingValue;
        console.log("Cumple rating?", matchesRating);

        if (matchesGenre && matchesRating) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }

    // Actualiza el valor mostrado del rating y filtra al mover el slider
    minRating.addEventListener("input", function() {
      ratingValue.textContent = minRating.value;
      filterMovies();
    });

    // Filtra al cambiar el género
    genreSelect.addEventListener("change", filterMovies);

    // Llamada inicial para mostrar todas las películas según los valores por defecto
    filterMovies();
  });
