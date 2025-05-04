/**
 * CritFlix Admin - Gestión de Películas y Series
 * Este script se encarga de las funcionalidades específicas de la sección de películas
 */

document.addEventListener('DOMContentLoaded', function() {
    initMoviesManagement();
});

/**
 * Inicializa todas las funcionalidades de la gestión de películas
 */
function initMoviesManagement() {
    console.log('Inicializando gestión de películas...');

    // Inicializar filtros
    initMovieFilters();

    // Inicializar búsqueda
    initMovieSearch();

    // Inicializar selección masiva
    initBulkSelection();

    // Inicializar acciones de películas
    initMovieActions();

    // Inicializar modal de edición
    initEditMovieModal();

    // Inicializar modal de creación
    initAddMovieModal();

    // Inicializar previsualizadores de imagen
    initImagePreviews();
}

/**
 * Inicializa los filtros de películas
 */
function initMovieFilters() {
    // Filtro por botones (tipo, género)
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Quitar clase active de los botones del mismo grupo
            const filterGroup = this.closest('.filter-group');
            filterGroup.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });

            // Añadir clase active al botón clicado
            this.classList.add('active');

            // Aplicar filtro
            applyMovieFilters();
        });
    });

    // Filtro por año
    const yearFrom = document.getElementById('year-from');
    const yearTo = document.getElementById('year-to');

    if (yearFrom && yearTo) {
        [yearFrom, yearTo].forEach(input => {
            input.addEventListener('change', applyMovieFilters);
        });
    }

    // Botón de restablecer filtros
    const resetBtn = document.querySelector('.btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetMovieFilters);
    }
}

/**
 * Aplica los filtros seleccionados a la tabla de películas
 */
function applyMovieFilters() {
    // Obtener valores de filtros
    const typeFilter = document.querySelector('.filter-group:nth-child(1) .filter-btn.active').dataset.filter;
    const genreFilter = document.querySelector('.filter-group:nth-child(2) .filter-btn.active').dataset.filter;
    const yearFrom = document.getElementById('year-from').value;
    const yearTo = document.getElementById('year-to').value;

    // Aplicar filtros a las filas de la tabla
    const rows = document.querySelectorAll('.movies-table tbody tr');

    rows.forEach(row => {
        let shouldShow = true;

        // Filtro por tipo
        if (typeFilter !== 'all' && row.dataset.type !== typeFilter) {
            shouldShow = false;
        }

        // Filtro por género
        if (genreFilter !== 'all' && row.dataset.genre !== genreFilter) {
            shouldShow = false;
        }

        // Filtro por año
        if (yearFrom && yearTo) {
            const movieYear = parseInt(row.dataset.year);
            const fromYear = parseInt(yearFrom);
            const toYear = parseInt(yearTo);

            if (isNaN(movieYear) || movieYear < fromYear || movieYear > toYear) {
                shouldShow = false;
            }
        } else if (yearFrom) {
            const movieYear = parseInt(row.dataset.year);
            const fromYear = parseInt(yearFrom);

            if (isNaN(movieYear) || movieYear < fromYear) {
                shouldShow = false;
            }
        } else if (yearTo) {
            const movieYear = parseInt(row.dataset.year);
            const toYear = parseInt(yearTo);

            if (isNaN(movieYear) || movieYear > toYear) {
                shouldShow = false;
            }
        }

        // Mostrar u ocultar la fila
        row.style.display = shouldShow ? '' : 'none';
    });

    // Actualizar contador de resultados
    updateMovieResults();
}

/**
 * Restablece todos los filtros a su valor por defecto
 */
function resetMovieFilters() {
    // Restablecer filtros de botones
    document.querySelectorAll('.filter-group').forEach(group => {
        const buttons = group.querySelectorAll('.filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        buttons[0].classList.add('active'); // El primero siempre es "Todos"
    });

    // Restablecer años
    const yearFrom = document.getElementById('year-from');
    const yearTo = document.getElementById('year-to');
    if (yearFrom && yearTo) {
        yearFrom.value = '';
        yearTo.value = '';
    }

    // Restablecer búsqueda
    const searchInput = document.getElementById('search-movies');
    if (searchInput) {
        searchInput.value = '';
    }

    // Mostrar todas las filas
    document.querySelectorAll('.movies-table tbody tr').forEach(row => {
        row.style.display = '';
    });

    // Actualizar contador
    updateMovieResults();
}

/**
 * Actualiza el contador de resultados de películas mostradas
 */
function updateMovieResults() {
    const tableInfo = document.querySelector('.table-info');
    if (!tableInfo) return;

    const totalRows = document.querySelectorAll('.movies-table tbody tr').length;
    const visibleRows = document.querySelectorAll('.movies-table tbody tr[style=""]').length +
                       document.querySelectorAll('.movies-table tbody tr:not([style])').length;

    tableInfo.querySelector('span').innerHTML = `Mostrando <strong>${visibleRows}</strong> de <strong>${totalRows}</strong> películas`;
}

/**
 * Inicializa la búsqueda en tiempo real de películas
 */
function initMovieSearch() {
    const searchInput = document.getElementById('search-movies');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        // Buscar en todas las filas
        document.querySelectorAll('.movies-table tbody tr').forEach(row => {
            const movieTitle = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const movieGenre = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

            const matches = movieTitle.includes(searchTerm) || movieGenre.includes(searchTerm);

            // Solo filtramos por búsqueda si hay un término, de lo contrario respetamos los otros filtros
            if (searchTerm) {
                row.style.display = matches ? '' : 'none';
            } else if (!row.hasAttribute('data-filtered')) {
                row.style.display = '';
            }
        });

        // Actualizar contador
        updateMovieResults();
    });
}

/**
 * Inicializa la selección masiva de películas
 */
function initBulkSelection() {
    const selectAll = document.getElementById('select-all');
    if (!selectAll) return;

    // Evento para seleccionar/deseleccionar todos
    selectAll.addEventListener('change', function() {
        const isChecked = this.checked;

        // Seleccionar/deseleccionar todas las filas visibles
        document.querySelectorAll('.movies-table tbody tr:not([style*="none"]) .movie-select').forEach(checkbox => {
            checkbox.checked = isChecked;
        });

        // Actualizar estado del botón de acciones masivas
        updateBulkActionButton();
    });

    // Evento para cada checkbox individual
    document.querySelectorAll('.movie-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButton();

            // Actualizar el checkbox "select-all"
            const allCheckboxes = document.querySelectorAll('.movies-table tbody tr:not([style*="none"]) .movie-select');
            const checkedCheckboxes = document.querySelectorAll('.movies-table tbody tr:not([style*="none"]) .movie-select:checked');

            selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
            selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        });
    });

    // Botón de aplicar acción masiva
    const applyBtn = document.querySelector('.apply-action');
    if (applyBtn) {
        applyBtn.addEventListener('click', applyBulkAction);
    }
}

/**
 * Actualiza el estado del botón de acción masiva
 */
function updateBulkActionButton() {
    const applyBtn = document.querySelector('.apply-action');
    if (!applyBtn) return;

    const hasSelection = document.querySelectorAll('.movie-select:checked').length > 0;
    const bulkAction = document.getElementById('bulk-action');

    // Habilitar/deshabilitar según selección y acción
    applyBtn.disabled = !hasSelection || !bulkAction.value;
}

/**
 * Aplica la acción masiva seleccionada a las películas
 */
function applyBulkAction() {
    const bulkAction = document.getElementById('bulk-action');
    if (!bulkAction || !bulkAction.value) return;

    const selectedMovies = Array.from(document.querySelectorAll('.movie-select:checked'))
        .map(checkbox => checkbox.dataset.id);

    if (selectedMovies.length === 0) return;

    // Confirmar la acción
    const actionText = bulkAction.options[bulkAction.selectedIndex].text;
    if (!confirm(`¿Estás seguro de que deseas ${actionText.toLowerCase()} a ${selectedMovies.length} películas?`)) {
        return;
    }

    // Aplicar acción según el tipo
    switch (bulkAction.value) {
        case 'delete':
            deleteMovies(selectedMovies);
            break;
        case 'feature':
            toggleMoviesFeature(selectedMovies, true);
            break;
        case 'unfeature':
            toggleMoviesFeature(selectedMovies, false);
            break;
    }

    // Limpiar selección
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.movie-select').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Restablecer acción seleccionada
    bulkAction.value = '';
    updateBulkActionButton();
}

/**
 * Elimina películas masivamente
 * @param {Array} movieIds - Array de IDs de películas a eliminar
 */
function deleteMovies(movieIds) {
    let successCount = 0;
    let failedCount = 0;
    let totalToProcess = movieIds.length;
    let processed = 0;

    // Mostrar notificación de inicio
    showNotification(`Procesando eliminación de ${totalToProcess} películas...`, 'info');

    // Procesar cada película
    movieIds.forEach(movieId => {
        // Añadir efecto visual
        const row = document.querySelector(`.movie-select[data-id="${movieId}"]`).closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';

        // Llamar a la API para eliminar
        fetch(`/admin/api/movies/${movieId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            processed++;

            if (data.success) {
                successCount++;
                // Eliminar fila con animación
                row.style.height = '0';
                row.style.overflow = 'hidden';
                row.style.padding = '0';
                row.style.border = '0';

                setTimeout(() => {
                    row.remove();

                    // Actualizar contador
                    updateMovieResults();

                    // Mostrar notificación final si es la última
                    if (processed === totalToProcess) {
                        showFinalNotification(successCount, failedCount, totalToProcess);
                    }
                }, 300);
            } else {
                failedCount++;
                row.style.opacity = '1';

                // Mostrar notificación final si es la última
                if (processed === totalToProcess) {
                    showFinalNotification(successCount, failedCount, totalToProcess);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            processed++;
            failedCount++;
            row.style.opacity = '1';

            // Mostrar notificación final si es la última
            if (processed === totalToProcess) {
                showFinalNotification(successCount, failedCount, totalToProcess);
            }
        });
    });
}

/**
 * Muestra una notificación final con el resultado de la operación masiva
 */
function showFinalNotification(successCount, failedCount, totalCount) {
    if (successCount === totalCount) {
        showNotification(`Se han eliminado ${successCount} películas correctamente`, 'success');
    } else if (successCount > 0 && failedCount > 0) {
        showNotification(`Operación completada: ${successCount} películas eliminadas, ${failedCount} con errores`, 'warning');
    } else {
        showNotification(`Error al eliminar películas: todas las operaciones fallaron`, 'error');
    }
}

/**
 * Cambia el estado destacado de múltiples películas
 * @param {Array} movieIds - Array de IDs de películas
 * @param {boolean} featured - Si deben destacarse o no
 */
function toggleMoviesFeature(movieIds, featured) {
    let successCount = 0;
    let failedCount = 0;
    let totalToProcess = movieIds.length;
    let processed = 0;

    // Mostrar notificación de inicio
    const actionText = featured ? 'destacando' : 'quitando destacado de';
    showNotification(`Procesando ${actionText} ${totalToProcess} películas...`, 'info');

    // Procesar cada película
    movieIds.forEach(movieId => {
        // Añadir efecto visual
        const row = document.querySelector(`.movie-select[data-id="${movieId}"]`).closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';

        // Llamar a la API para cambiar estado
        fetch(`/admin/api/movies/${movieId}/feature`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ featured: featured })
        })
        .then(response => response.json())
        .then(data => {
            processed++;

            if (data.success) {
                successCount++;
                row.style.opacity = '1';

                // Actualizar UI
                const featureBtn = row.querySelector('.action-btn.featured');
                if (featureBtn) {
                    if (featured) {
                        featureBtn.classList.add('is-featured');
                        featureBtn.innerHTML = '<i class="fas fa-trophy"></i>';
                        featureBtn.title = 'Quitar destacado';
                    } else {
                        featureBtn.classList.remove('is-featured');
                        featureBtn.innerHTML = '<i class="fas fa-award"></i>';
                        featureBtn.title = 'Destacar';
                    }
                }

                // Mostrar notificación final si es la última
                if (processed === totalToProcess) {
                    showFeaturedFinalNotification(successCount, failedCount, totalToProcess, featured);
                }
            } else {
                failedCount++;
                row.style.opacity = '1';

                // Mostrar notificación final si es la última
                if (processed === totalToProcess) {
                    showFeaturedFinalNotification(successCount, failedCount, totalToProcess, featured);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            processed++;
            failedCount++;
            row.style.opacity = '1';

            // Mostrar notificación final si es la última
            if (processed === totalToProcess) {
                showFeaturedFinalNotification(successCount, failedCount, totalToProcess, featured);
            }
        });
    });
}

/**
 * Muestra una notificación final con el resultado de la operación de destacar
 */
function showFeaturedFinalNotification(successCount, failedCount, totalCount, featured) {
    const actionText = featured ? 'destacado' : 'quitado destacado de';

    if (successCount === totalCount) {
        showNotification(`Se ha ${actionText} ${successCount} películas correctamente`, 'success');
    } else if (successCount > 0 && failedCount > 0) {
        showNotification(`Operación completada: ${actionText} ${successCount} películas, ${failedCount} con errores`, 'warning');
    } else {
        showNotification(`Error al ${actionText} películas: todas las operaciones fallaron`, 'error');
    }
}

/**
 * Inicializa los eventos para las acciones de películas (ver, editar, eliminar)
 */
function initMovieActions() {
    // Evento para editar película
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.id;
            openEditMovieModal(movieId);
        });
    });

    // Evento para eliminar película
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.id;
            deleteMovie(movieId);
        });
    });

    // Evento para destacar/quitar destacado
    document.querySelectorAll('.action-btn.featured').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.id;
            const isFeatured = this.classList.contains('is-featured');
            toggleMovieFeature(movieId, !isFeatured);
        });
    });

    // Botón para añadir nueva película
    const addMovieBtn = document.querySelector('.add-movie-btn');
    if (addMovieBtn) {
        addMovieBtn.addEventListener('click', function() {
            openModal(document.getElementById('add-movie-modal'));
        });
    }
}

/**
 * Elimina una película
 * @param {string} movieId - ID de la película a eliminar
 */
function deleteMovie(movieId) {
    // Confirmar eliminación
    if (!confirm(`¿Estás seguro de que deseas eliminar la película #${movieId}?`)) {
        return;
    }

    // Buscar fila de la película
    const movieRow = document.querySelector(`tr[data-movie-id="${movieId}"]`);
    if (!movieRow) return;

    // Efecto de eliminación inicial
    movieRow.style.opacity = '0.5';

    // Llamada a la API
    fetch(`/admin/api/movies/${movieId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Eliminar fila con animación
            movieRow.style.transition = 'opacity 0.5s ease, transform 0.5s ease, height 0.5s ease';
            movieRow.style.opacity = '0';
            movieRow.style.transform = 'translateX(20px)';

            setTimeout(() => {
                movieRow.style.height = '0';
                movieRow.style.overflow = 'hidden';
                movieRow.style.padding = '0';
                movieRow.style.border = '0';

                setTimeout(() => {
                    movieRow.remove();
                    updateMovieResults();

                    // Notificación
                    showNotification(data.message, 'success');
                }, 300);
            }, 300);
        } else {
            // Restaurar opacidad y mostrar error
            movieRow.style.opacity = '1';
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Restaurar opacidad y mostrar error
        movieRow.style.opacity = '1';
        showNotification('Error al eliminar la película', 'error');
    });
}

/**
 * Cambia el estado destacado de una película
 * @param {string} movieId - ID de la película
 * @param {boolean} featured - Si debe destacarse o no
 */
function toggleMovieFeature(movieId, featured) {
    // Buscar botón de destacado
    const featureBtn = document.querySelector(`.action-btn.featured[data-id="${movieId}"]`);
    if (!featureBtn) return;

    // Mostrar efecto de carga
    const originalContent = featureBtn.innerHTML;
    featureBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    featureBtn.disabled = true;

    // Llamada a la API
    fetch(`/admin/api/movies/${movieId}/feature`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ featured: featured })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar UI
            if (featured) {
                featureBtn.classList.add('is-featured');
                featureBtn.innerHTML = '<i class="fas fa-trophy"></i>';
                featureBtn.title = 'Quitar destacado';
            } else {
                featureBtn.classList.remove('is-featured');
                featureBtn.innerHTML = '<i class="fas fa-award"></i>';
                featureBtn.title = 'Destacar';
            }

            // Notificación
            const actionText = featured ? 'destacada' : 'quitado el destacado de';
            showNotification(`Película ${actionText} correctamente`, 'success');
        } else {
            // Restaurar contenido original
            featureBtn.innerHTML = originalContent;

            // Notificación
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Restaurar contenido original
        featureBtn.innerHTML = originalContent;

        // Notificación
        showNotification('Error al cambiar el estado de destacado', 'error');
    })
    .finally(() => {
        featureBtn.disabled = false;
    });
}

/**
 * Inicializa el modal de edición de película
 */
function initEditMovieModal() {
    // Botón de cancelar
    const cancelBtn = document.querySelector('.cancel-edit');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            closeModal(document.getElementById('edit-movie-modal'));
        });
    }

    // Formulario de edición
    const editForm = document.getElementById('edit-movie-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveMovieChanges();
        });
    }

    // Botones de cierre de modal
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.admin-modal-container');
            closeModal(modal);
        });
    });
}

/**
 * Inicializa el modal para añadir nueva película
 */
function initAddMovieModal() {
    const modal = document.getElementById('add-movie-modal');
    const form = document.getElementById('add-movie-form');
    const cancelBtn = document.querySelector('.cancel-add');

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            closeModal(modal);
            form.reset();
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            createMovie();
        });
    }
}

/**
 * Inicializa la previsualización de imágenes para posters
 */
function initImagePreviews() {
    // Previsualización al añadir
    const addPosterInput = document.getElementById('add-poster');
    if (addPosterInput) {
        addPosterInput.addEventListener('change', function() {
            const file = this.files[0];
            const previewContainer = document.getElementById('poster-preview');

            if (file && previewContainer) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
                };

                reader.readAsDataURL(file);
            }
        });
    }

    // Previsualización al editar
    const editPosterInput = document.getElementById('edit-poster');
    if (editPosterInput) {
        editPosterInput.addEventListener('change', function() {
            const file = this.files[0];
            const previewContainer = document.getElementById('current-poster-preview');

            if (file && previewContainer) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
                };

                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Abre el modal de edición de película con los datos de la película
 * @param {string} movieId - ID de la película a editar
 */
function openEditMovieModal(movieId) {
    const modal = document.getElementById('edit-movie-modal');
    if (!modal) return;

    // Buscar la fila para efectos visuales
    const movieRow = document.querySelector(`tr[data-movie-id="${movieId}"]`);
    if (movieRow) {
        movieRow.style.transition = 'background-color 0.3s ease';
        movieRow.style.backgroundColor = 'rgba(0, 255, 0, 0.15)';
    }

    // Hacer petición para obtener los datos completos de la película
    fetch(`/api/admin/movies/${movieId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario con los datos de la película
                document.getElementById('edit-movie-id').value = data.movie.id;
                document.getElementById('edit-title').value = data.movie.titulo;
                document.getElementById('edit-type').value = data.movie.tipo;
                document.getElementById('edit-genre').value = data.movie.genero;
                document.getElementById('edit-year').value = data.movie.year;
                document.getElementById('edit-director').value = data.movie.director;
                document.getElementById('edit-synopsis').value = data.movie.sinopsis;
                document.getElementById('edit-featured').checked = data.movie.destacado;

                // Mostrar imagen actual si existe
                const previewContainer = document.getElementById('current-poster-preview');
                if (previewContainer) {
                    if (data.movie.poster_url) {
                        previewContainer.innerHTML = `<img src="${data.movie.poster_url}" alt="${data.movie.titulo}">`;
                    } else if (data.movie.poster) {
                        previewContainer.innerHTML = `<img src="/storage/${data.movie.poster}" alt="${data.movie.titulo}">`;
                    } else {
                        previewContainer.innerHTML = `<i class="fas fa-film"></i>`;
                    }
                }

                // Abrir modal
                openModal(modal);
            } else {
                showNotification(data.message || 'Error al cargar datos de la película', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar datos de la película', 'error');
        })
        .finally(() => {
            // Restaurar apariencia de la fila
            setTimeout(() => {
                movieRow.style.backgroundColor = '';
            }, 500);
        });
}

/**
 * Guarda los cambios de la película tras la edición
 */
function saveMovieChanges() {
    const movieId = document.getElementById('edit-movie-id').value;
    const formData = new FormData();

    // Añadir los campos del formulario
    formData.append('titulo', document.getElementById('edit-title').value);
    formData.append('tipo', document.getElementById('edit-type').value);
    formData.append('genero', document.getElementById('edit-genre').value);
    formData.append('year', document.getElementById('edit-year').value);
    formData.append('director', document.getElementById('edit-director').value);
    formData.append('sinopsis', document.getElementById('edit-synopsis').value);
    formData.append('destacado', document.getElementById('edit-featured').checked ? 1 : 0);

    // Añadir poster si se seleccionó un nuevo archivo
    const posterInput = document.getElementById('edit-poster');
    if (posterInput.files.length > 0) {
        formData.append('poster', posterInput.files[0]);
    }

    // Añadir token CSRF
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PUT'); // Para simular una petición PUT con FormData

    // Mostrar efecto de carga
    const saveBtn = document.querySelector('.save-movie');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    // Llamada a la API
    fetch(`/admin/api/movies/${movieId}`, {
        method: 'POST', // Usamos POST para enviar FormData, pero con _method=PUT
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar fila en la tabla
            updateMovieRow(data.movie);

            // Cerrar modal
            closeModal(document.getElementById('edit-movie-modal'));

            // Notificación
            showNotification(data.message, 'success');
        } else {
            // Mostrar errores
            let errorMessage = data.message;
            if (data.errors) {
                errorMessage += '<br>' + Object.values(data.errors).flat().join('<br>');
            }
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al guardar los cambios', 'error');
    })
    .finally(() => {
        // Restaurar botón
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = 'Guardar Cambios';
        }
    });
}

/**
 * Crea una nueva película
 */
function createMovie() {
    const formData = new FormData();

    // Añadir los campos del formulario
    formData.append('titulo', document.getElementById('add-title').value);
    formData.append('tipo', document.getElementById('add-type').value);
    formData.append('genero', document.getElementById('add-genre').value);
    formData.append('year', document.getElementById('add-year').value);
    formData.append('director', document.getElementById('add-director').value);
    formData.append('sinopsis', document.getElementById('add-synopsis').value);
    formData.append('destacado', document.getElementById('add-featured').checked ? 1 : 0);

    // Añadir poster
    const posterInput = document.getElementById('add-poster');
    if (posterInput.files.length > 0) {
        formData.append('poster', posterInput.files[0]);
    }

    // Añadir token CSRF
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Mostrar efecto de carga
    const addBtn = document.querySelector('.add-movie');
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    }

    // Llamada a la API
    fetch('/admin/api/movies', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Añadir nueva fila a la tabla
            addMovieRow(data.movie);

            // Cerrar modal y resetear formulario
            closeModal(document.getElementById('add-movie-modal'));
            document.getElementById('add-movie-form').reset();
            document.getElementById('poster-preview').innerHTML = '';

            // Notificación
            showNotification(data.message, 'success');
        } else {
            // Mostrar errores
            let errorMessage = data.message;
            if (data.errors) {
                errorMessage += '<br>' + Object.values(data.errors).flat().join('<br>');
            }
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al crear la película', 'error');
    })
    .finally(() => {
        // Restaurar botón
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = 'Crear Película';
        }
    });
}

/**
 * Actualiza una fila de película con nuevos datos
 * @param {Object} movie - Datos de la película actualizada
 */
function updateMovieRow(movie) {
    // Obtener fila de la película
    const row = document.querySelector(`tr[data-movie-id="${movie.id}"]`);
    if (!row) return;

    // Actualizar datos básicos
    row.querySelector('td:nth-child(4)').textContent = movie.titulo;
    row.querySelector('td:nth-child(5) .status-badge').textContent = movie.tipo.charAt(0).toUpperCase() + movie.tipo.slice(1);
    row.querySelector('td:nth-child(5) .status-badge').className = `status-badge ${movie.tipo}`;
    row.querySelector('td:nth-child(6)').textContent = movie.genero;
    row.querySelector('td:nth-child(7)').textContent = movie.year;

    // Actualizar poster
    const posterContainer = row.querySelector('.movie-poster');
    if (posterContainer) {
        posterContainer.innerHTML = movie.poster_url ?
            `<img src="${movie.poster_url}" alt="${movie.titulo}">` :
            (movie.poster ?
                `<img src="/storage/${movie.poster}" alt="${movie.titulo}">` :
                '<i class="fas fa-film"></i>'
            );
    }

    // Actualizar botón de destacado
    const featureBtn = row.querySelector('.action-btn.featured');
    if (featureBtn) {
        if (movie.destacado) {
            featureBtn.classList.add('is-featured');
            featureBtn.innerHTML = '<i class="fas fa-trophy"></i>';
            featureBtn.title = 'Quitar destacado';
        } else {
            featureBtn.classList.remove('is-featured');
            featureBtn.innerHTML = '<i class="fas fa-award"></i>';
            featureBtn.title = 'Destacar';
        }
    }

    // Efecto visual
    row.style.transition = 'background-color 0.5s ease';
    row.style.backgroundColor = 'rgba(0, 255, 0, 0.1)';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 1000);
}

/**
 * Añade una nueva fila de película a la tabla
 * @param {Object} movie - Datos de la nueva película
 */
function addMovieRow(movie) {
    const tbody = document.querySelector('.movies-table tbody');
    if (!tbody) return;

    // Crear nueva fila
    const tr = document.createElement('tr');
    tr.dataset.movieId = movie.id;
    tr.dataset.type = movie.tipo;
    tr.dataset.genre = movie.genero;
    tr.dataset.year = movie.year;

    // Determinar valoración promedio
    const rating = movie.rating || 0;

    // Generar estrellas HTML
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            starsHtml += '<i class="fas fa-star"></i>';
        } else if (i - 0.5 <= rating) {
            starsHtml += '<i class="fas fa-star-half-alt"></i>';
        } else {
            starsHtml += '<i class="far fa-star"></i>';
        }
    }

    // Contenido de la fila
    tr.innerHTML = `
        <td>
            <input type="checkbox" class="movie-select" data-id="${movie.id}">
        </td>
        <td>#${movie.id}</td>
        <td>
            <div class="movie-poster">
                ${movie.poster_url ?
                    `<img src="${movie.poster_url}" alt="${movie.titulo}">` :
                    (movie.poster ?
                        `<img src="/storage/${movie.poster}" alt="${movie.titulo}">` :
                        '<i class="fas fa-film"></i>'
                    )
                }
            </div>
        </td>
        <td>${movie.titulo}</td>
        <td>
            <span class="status-badge ${movie.tipo}">${movie.tipo.charAt(0).toUpperCase() + movie.tipo.slice(1)}</span>
        </td>
        <td>${movie.genero}</td>
        <td>${movie.year}</td>
        <td>
            <div class="rating">
                <span class="rating-value">${rating.toFixed(1)}</span>
                <div class="rating-stars">
                    ${starsHtml}
                </div>
            </div>
        </td>
        <td class="actions">
            <a href="/movie/${movie.id}" class="action-btn view" title="Ver detalles">
                <i class="fas fa-eye"></i>
            </a>
            <a href="#" class="action-btn edit" title="Editar" data-id="${movie.id}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="action-btn featured ${movie.destacado ? 'is-featured' : ''}"
               title="${movie.destacado ? 'Quitar destacado' : 'Destacar'}"
               data-id="${movie.id}">
                <i class="fas fa-${movie.destacado ? 'trophy' : 'award'}"></i>
            </a>
            <a href="#" class="action-btn delete" title="Eliminar" data-id="${movie.id}">
                <i class="fas fa-trash"></i>
            </a>
        </td>
    `;

    // Añadir fila al principio de la tabla
    tbody.insertBefore(tr, tbody.firstChild);

    // Inicializar eventos para los nuevos botones
    initMovieActions();

    // Actualizar contador
    updateMovieResults();

    // Efecto visual
    tr.style.animation = 'fadeIn 0.5s ease';
}

/**
 * Abre un modal
 * @param {HTMLElement} modal - El elemento modal a abrir
 */
function openModal(modal) {
    if (!modal) return;
    modal.style.display = 'flex';

    // Añadir clase para animación
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);

    // Evitar scroll en el body
    document.body.style.overflow = 'hidden';
}

/**
 * Cierra un modal
 * @param {HTMLElement} modal - El elemento modal a cerrar
 */
function closeModal(modal) {
    if (!modal) return;

    // Quitar clase para animación
    modal.classList.remove('active');

    // Ocultar después de la animación
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);

    // Restaurar scroll en el body
    document.body.style.overflow = '';
}

/**
 * Muestra una notificación
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, info, warning)
 */
function showNotification(message, type = 'info') {
    // Crear elemento de notificación si no existe
    let notification = document.querySelector('.notification');

    if (!notification) {
        notification = document.createElement('div');
        notification.className = 'notification';
        document.body.appendChild(notification);
    }

    // Establecer icono según el tipo
    let icon = '';
    switch (type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
            break;
    }

    // Establecer contenido y clase
    notification.innerHTML = `${icon} <span>${message}</span>`;
    notification.className = `notification ${type}`;

    // Mostrar con animación
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Ocultar después de 5 segundos
    setTimeout(() => {
        notification.classList.remove('show');

        // Eliminar elemento después de la animación
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}
