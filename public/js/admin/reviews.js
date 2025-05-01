/**
 * CritFlix Admin Panel - Gestión de Valoraciones
 * Controla las funcionalidades específicas de la vista de valoraciones
 */

document.addEventListener('DOMContentLoaded', function() {
    initReviewsPage();
});

/**
 * Inicializa todas las funcionalidades de la página de valoraciones
 */
function initReviewsPage() {
    // Eventos para las acciones de valoraciones
    initReviewActions();

    // Inicializar modal de detalles de valoración
    initReviewModal();

    // Inicializar exportación de datos
    initExportFeature();

    // Inicializar la selección masiva de valoraciones
    initBulkSelection();
}

/**
 * Inicializa los eventos para las acciones de valoraciones (ver, destacar, eliminar)
 */
function initReviewActions() {
    // Botones de ver detalle
    document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const reviewId = btn.getAttribute('data-id');
            openReviewDetails(reviewId);
        });
    });

    // Botones de destacar/quitar destacado
    document.querySelectorAll('.action-btn.highlight').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const reviewId = btn.getAttribute('data-id');
            toggleHighlight(reviewId, btn);
        });
    });

    // Botones de eliminar valoración
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const reviewId = btn.getAttribute('data-id');
            confirmDeleteReview(reviewId);
        });
    });

    // Botones "Ver más" en los comentarios
    document.querySelectorAll('.show-more-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            openReviewDetails(reviewId);
        });
    });
}

/**
 * Abre el modal de detalles de una valoración
 * @param {string} reviewId - ID de la valoración
 */
function openReviewDetails(reviewId) {
    const modal = document.getElementById('review-detail-modal');
    if (!modal) return;

    // Buscar fila de la valoración
    const reviewRow = document.querySelector(`tr[data-review-id="${reviewId}"]`);
    if (!reviewRow) return;

    // Mostrar efecto de carga en la fila
    reviewRow.style.transition = 'background-color 0.3s ease';
    reviewRow.style.backgroundColor = 'rgba(0, 255, 255, 0.05)';

    // Solicitar datos detallados de la valoración a la API
    fetch(`/admin/api/reviews/${reviewId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Llenar el modal con los datos
            document.getElementById('detail-movie-title').textContent = data.review.movie_title;
            document.getElementById('detail-user-name').textContent = data.review.user_name;
            document.getElementById('detail-date').textContent = new Date(data.review.created_at).toLocaleString();
            document.getElementById('detail-review-title').textContent = data.review.valoracion || 'Sin título';
            document.getElementById('detail-comment').textContent = data.review.comentario || 'Sin comentario';
            document.getElementById('detail-rating').textContent = data.review.puntuacion;

            // Generar estrellas
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= data.review.puntuacion) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            document.getElementById('detail-stars').innerHTML = starsHtml;

            // Configurar botón de destacar
            const highlightBtn = document.getElementById('detail-highlight');
            if (highlightBtn) {
                if (data.review.destacado) {
                    highlightBtn.querySelector('span').textContent = 'Quitar destacado';
                    highlightBtn.classList.add('highlighted');
                } else {
                    highlightBtn.querySelector('span').textContent = 'Destacar';
                    highlightBtn.classList.remove('highlighted');
                }

                // Evento para destacar/quitar desde el modal
                highlightBtn.onclick = function() {
                    toggleReviewHighlight(reviewId, !data.review.destacado);
                    closeModal(modal);
                };
            }

            // Configurar botón de eliminar
            const deleteBtn = modal.querySelector('.delete-review');
            if (deleteBtn) {
                deleteBtn.onclick = function() {
                    if (confirm('¿Estás seguro de que deseas eliminar esta valoración?')) {
                        deleteReview(reviewId);
                        closeModal(modal);
                    }
                };
            }

            // Abrir modal
            openModal(modal);
        } else {
            showNotification(data.message || 'Error al cargar detalles', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al cargar detalles de la valoración', 'error');
    })
    .finally(() => {
        // Restaurar apariencia de la fila
        setTimeout(() => {
            reviewRow.style.backgroundColor = '';
        }, 500);
    });
}

/**
 * Inicializa eventos para el modal de detalles
 */
function initReviewModal() {
    // Botón para destacar/quitar destacado
    const toggleBtn = document.querySelector('.toggle-highlight');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const reviewId = toggleBtn.getAttribute('data-id');
            toggleHighlight(reviewId, toggleBtn);
        });
    }

    // Botón para eliminar
    const deleteBtn = document.querySelector('.delete-review');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            const reviewId = deleteBtn.getAttribute('data-id');
            closeModal('review-modal');
            confirmDeleteReview(reviewId);
        });
    }
}

/**
 * Alterna el estado de destacado de una valoración
 * @param {string} reviewId - ID de la valoración
 * @param {HTMLElement} btn - Botón que se ha pulsado
 */
function toggleHighlight(reviewId, btn) {
    // Determinar estado actual
    const isCurrentlyHighlighted = btn.querySelector('i').classList.contains('fa-star');
    const newState = !isCurrentlyHighlighted;

    // Mensaje de carga
    showNotification('Actualizando estado...', 'info');

    // Simular llamada AJAX (reemplazar con AJAX real)
    setTimeout(() => {
        console.log(`${newState ? 'Destacando' : 'Quitando destacado'} valoración ${reviewId}`);

        // Actualizar UI
        if (btn.classList.contains('toggle-highlight')) {
            // Es el botón del modal
            btn.querySelector('i').className = newState ? 'fas fa-star' : 'fas fa-star-half-alt';
            document.getElementById('highlight-text').textContent = newState ? 'Quitar destacado' : 'Destacar';

            // Actualizar también el botón en la tabla
            const tableBtn = document.querySelector(`.action-btn.highlight[data-id="${reviewId}"]`);
            if (tableBtn) {
                tableBtn.querySelector('i').className = newState ? 'fas fa-star' : 'fas fa-star-half-alt';
                tableBtn.setAttribute('title', newState ? 'Quitar destacado' : 'Destacar');
            }
        } else {
            // Es el botón de la tabla
            btn.querySelector('i').className = newState ? 'fas fa-star' : 'fas fa-star-half-alt';
            btn.setAttribute('title', newState ? 'Quitar destacado' : 'Destacar');

            // Actualizar también el botón del modal si está abierto
            const modalBtn = document.querySelector('.toggle-highlight');
            if (modalBtn && modalBtn.getAttribute('data-id') === reviewId) {
                modalBtn.querySelector('i').className = newState ? 'fas fa-star' : 'fas fa-star-half-alt';
                document.getElementById('highlight-text').textContent = newState ? 'Quitar destacado' : 'Destacar';
            }
        }

        // Mostrar notificación de éxito
        showNotification(
            newState ? 'Valoración destacada correctamente' : 'Valoración quitada de destacados',
            'success'
        );
    }, 500);
}

/**
 * Confirma eliminación de una valoración
 * @param {string} reviewId - ID de la valoración a eliminar
 */
function confirmDeleteReview(reviewId) {
    if (confirm(`¿Estás seguro de que deseas eliminar la valoración #${reviewId}? Esta acción no se puede deshacer.`)) {
        // Simular eliminación (reemplazar con AJAX real)
        console.log(`Eliminando valoración ${reviewId}...`);

        // Mensaje de carga
        showNotification('Eliminando valoración...', 'info');

        // Simular éxito
        setTimeout(() => {
            // Eliminar del DOM
            const reviewRow = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            if (reviewRow) {
                reviewRow.remove();
            }

            // Actualizar contadores de estadísticas
            updateStatsCounters(-1);

            // Mostrar notificación de éxito
            showNotification('Valoración eliminada correctamente', 'success');
        }, 500);
    }
}

/**
 * Actualiza los contadores de estadísticas
 * @param {number} delta - Valor a añadir (positivo) o quitar (negativo)
 */
function updateStatsCounters(delta) {
    // Actualizar contador total de valoraciones
    const totalReviews = document.querySelector('.stats-card:nth-child(1) h3');
    if (totalReviews) {
        const currentValue = parseInt(totalReviews.textContent);
        totalReviews.textContent = currentValue + delta;
    }

    // Actualizar contador de usuarios activos (si es necesario)
    const activeReviewers = document.querySelector('.stats-card:nth-child(3) h3');
    if (activeReviewers && delta < 0) {
        const currentValue = parseInt(activeReviewers.textContent);
        // Solo disminuir si es mayor que 0
        if (currentValue > 0) {
            activeReviewers.textContent = currentValue + delta;
        }
    }
}

/**
 * Inicializa la funcionalidad de exportación de datos
 */
function initExportFeature() {
    const exportBtn = document.querySelector('.start-export');

    if (exportBtn) {
        exportBtn.addEventListener('click', startExport);
    }

    // Cambiar la visibilidad de fecha personalizada
    const exportPeriod = document.getElementById('export-period');
    const customDateRange = document.querySelector('.custom-date-range');

    if (exportPeriod && customDateRange) {
        exportPeriod.addEventListener('change', () => {
            customDateRange.classList.toggle('hidden', exportPeriod.value !== 'custom');
        });
    }
}

/**
 * Inicia el proceso de exportación de datos
 */
function startExport() {
    // Obtener opciones seleccionadas
    const format = document.getElementById('export-format').value;
    const includeUser = document.getElementById('export-user-data').checked;
    const includeMovie = document.getElementById('export-movie-data').checked;
    const includeComments = document.getElementById('export-comments').checked;
    const period = document.getElementById('export-period').value;

    // Fechas personalizadas si es aplicable
    let dateFrom = null;
    let dateTo = null;

    if (period === 'custom') {
        dateFrom = document.getElementById('export-date-from').value;
        dateTo = document.getElementById('export-date-to').value;

        if (!dateFrom || !dateTo) {
            showNotification('Por favor, selecciona un rango de fechas válido', 'error');
            return;
        }
    }

    // Mostrar notificación de inicio
    showNotification(`Exportando datos en formato ${format.toUpperCase()}...`, 'info');

    // Simular exportación (reemplazar con AJAX real)
    console.log('Opciones de exportación:', {
        format,
        includeUser,
        includeMovie,
        includeComments,
        period,
        dateFrom,
        dateTo
    });

    // Simular éxito después de un tiempo
    setTimeout(() => {
        // Cerrar modal
        closeModal('export-modal');

        // En una implementación real, se generaría un archivo para descargar
        // Por ahora, solo mostrar una notificación de éxito
        showNotification(`Datos exportados correctamente en formato ${format.toUpperCase()}`, 'success');
    }, 1500);
}

/**
 * Filtra las valoraciones según los criterios seleccionados
 */
function filterReviews() {
    const tableRows = document.querySelectorAll('tr[data-review-id]');

    // No hay elementos para filtrar
    if (!tableRows.length) return;

    // Obtener valores de filtros
    const searchQuery = document.getElementById('search-reviews')?.value.toLowerCase() || '';
    const ratingFilter = document.querySelector('.rating-filter .stars i.active');
    const minRating = ratingFilter ? parseInt(ratingFilter.getAttribute('data-value')) : 0;

    // Filtro de tipo seleccionado
    const typeFilter = document.querySelector('.filter-btn[data-filter].active')?.getAttribute('data-filter') || 'all';

    // Filtro de fechas
    const dateFrom = document.getElementById('date-from')?.value;
    const dateTo = document.getElementById('date-to')?.value;

    // Convertir las fechas a timestamps para comparación
    const fromTimestamp = dateFrom ? new Date(dateFrom).getTime() : 0;
    const toTimestamp = dateTo ? new Date(dateTo).getTime() + 86400000 : Infinity; // +1 día

    // Filtrar filas
    tableRows.forEach(row => {
        const userName = row.querySelector('.user-info span').textContent.toLowerCase();
        const movieTitle = row.querySelector('.movie-info span').textContent.toLowerCase();
        const commentEl = row.querySelector('.comment-preview, .no-comment');
        const hasComment = !row.querySelector('.no-comment');
        const comment = commentEl ? commentEl.textContent.toLowerCase() : '';

        // Rating
        const stars = row.querySelectorAll('.rating i.fas').length;

        // Fecha
        const dateText = row.querySelector('td:nth-child(6)').textContent;
        const dateParts = dateText.split(' ')[0].split('/'); // Formato: dd/mm/yyyy
        const reviewDate = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`).getTime();

        // Aplicar filtros
        const matchesSearch = userName.includes(searchQuery) ||
                            movieTitle.includes(searchQuery) ||
                            comment.includes(searchQuery);
        const matchesRating = stars >= minRating;
        const matchesType = typeFilter === 'all' ||
                        (typeFilter === 'with-comment' && hasComment) ||
                        (typeFilter === 'no-comment' && !hasComment);
        const matchesDate = reviewDate >= fromTimestamp && reviewDate <= toTimestamp;

        // Mostrar u ocultar según filtros
        if (matchesSearch && matchesRating && matchesType && matchesDate) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Datos de ejemplo para una valoración (simulación)
 * @param {string} id - ID de la valoración
 * @returns {Object} Datos de la valoración
 */
function getReviewMockData(id) {
    // En una implementación real, estos datos vendrían de una petición AJAX
    const mockReviews = {
        '1': {
            rating: 5,
            comment: 'Una obra maestra del cine moderno. Christopher Nolan demuestra una vez más su dominio de la narrativa compleja y el cine de gran escala. La cinematografía, la banda sonora y las actuaciones son excepcionales. Una película que te hace pensar durante días después de verla.',
            date: '15/05/2023 14:30',
            highlighted: true,
            user: {
                name: 'Maria García',
                avatar: 'https://randomuser.me/api/portraits/women/44.jpg'
            },
            movie: {
                title: 'Inception',
                year: 2010,
                duration: 148,
                poster: 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg'
            }
        },
        '2': {
            rating: 4,
            comment: 'Buena película, aunque en algunos momentos la trama es confusa. Los efectos especiales son espectaculares.',
            date: '03/06/2023 10:15',
            highlighted: false,
            user: {
                name: 'David Rodríguez',
                avatar: 'https://randomuser.me/api/portraits/men/22.jpg'
            },
            movie: {
                title: 'Interstellar',
                year: 2014,
                duration: 169,
                poster: 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg'
            }
        },
        '3': {
            rating: 3,
            comment: '',
            date: '10/07/2023 18:45',
            highlighted: false,
            user: {
                name: 'Lucía Martínez',
                avatar: 'https://randomuser.me/api/portraits/women/63.jpg'
            },
            movie: {
                title: 'The Matrix',
                year: 1999,
                duration: 136,
                poster: 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg'
            }
        }
    };

    return mockReviews[id] || {
        rating: 4,
        comment: 'Comentario de ejemplo para esta valoración.',
        date: '01/01/2023 12:00',
        highlighted: false,
        user: {
            name: 'Usuario Ejemplo',
            avatar: 'https://randomuser.me/api/portraits/lego/1.jpg'
        },
        movie: {
            title: 'Película Ejemplo',
            year: 2023,
            duration: 120,
            poster: 'https://via.placeholder.com/300x450?text=Película'
        }
    };
}

/**
 * Elimina una reseña
 * @param {string} reviewId - ID de la reseña a eliminar
 */
function deleteReview(reviewId) {
    // Confirmar eliminación
    if (!confirm(`¿Estás seguro de que deseas eliminar la reseña #${reviewId}?`)) {
        return;
    }

    // Buscar fila de la reseña
    const reviewRow = document.querySelector(`.admin-table tr[data-review-id="${reviewId}"]`);
    if (!reviewRow) return;

    // Efecto de eliminación inicial
    reviewRow.style.opacity = '0.5';

    // Llamada a la API
    fetch(`/admin/api/reviews/${reviewId}`, {
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
            reviewRow.style.transition = 'opacity 0.5s ease, transform 0.5s ease, height 0.5s ease';
            reviewRow.style.opacity = '0';
            reviewRow.style.transform = 'translateX(20px)';

            setTimeout(() => {
                reviewRow.style.height = '0';
                reviewRow.style.overflow = 'hidden';
                reviewRow.style.padding = '0';
                reviewRow.style.border = '0';

                setTimeout(() => {
                    reviewRow.remove();
                    updateReviewResults();

                    // Notificación
                    showNotification(data.message, 'success');
                }, 300);
            }, 300);
        } else {
            // Restaurar opacidad y mostrar error
            reviewRow.style.opacity = '1';
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Restaurar opacidad y mostrar error
        reviewRow.style.opacity = '1';
        showNotification('Error al eliminar la reseña', 'error');
    });
}

/**
 * Inicializa la selección masiva de valoraciones
 */
function initBulkSelection() {
    const selectAll = document.getElementById('select-all');
    if (!selectAll) return;

    // Evento para seleccionar/deseleccionar todos
    selectAll.addEventListener('change', function() {
        const isChecked = this.checked;

        // Seleccionar/deseleccionar todas las filas visibles
        document.querySelectorAll('.reviews-table tbody tr:not([style*="none"]) .review-select').forEach(checkbox => {
            checkbox.checked = isChecked;
        });

        // Actualizar estado del botón de acciones masivas
        updateBulkActionButton();
    });

    // Evento para cada checkbox individual
    document.querySelectorAll('.review-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButton();

            // Actualizar el checkbox "select-all"
            const allCheckboxes = document.querySelectorAll('.reviews-table tbody tr:not([style*="none"]) .review-select');
            const checkedCheckboxes = document.querySelectorAll('.reviews-table tbody tr:not([style*="none"]) .review-select:checked');

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

    const hasSelection = document.querySelectorAll('.review-select:checked').length > 0;
    const bulkAction = document.getElementById('bulk-action');

    // Habilitar/deshabilitar según selección y acción
    applyBtn.disabled = !hasSelection || !bulkAction.value;
}

/**
 * Aplica la acción masiva seleccionada a las valoraciones
 */
function applyBulkAction() {
    const bulkAction = document.getElementById('bulk-action');
    if (!bulkAction || !bulkAction.value) return;

    const selectedReviews = Array.from(document.querySelectorAll('.review-select:checked'))
        .map(checkbox => checkbox.dataset.id);

    if (selectedReviews.length === 0) return;

    // Confirmar la acción
    const actionText = bulkAction.options[bulkAction.selectedIndex].text;
    if (!confirm(`¿Estás seguro de que deseas ${actionText.toLowerCase()} ${selectedReviews.length} valoraciones?`)) {
        return;
    }

    // Aplicar acción según el tipo
    switch (bulkAction.value) {
        case 'delete':
            deleteReviews(selectedReviews);
            break;
        case 'highlight':
            toggleReviewsHighlight(selectedReviews, true);
            break;
        case 'unhighlight':
            toggleReviewsHighlight(selectedReviews, false);
            break;
    }

    // Limpiar selección
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.review-select').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Restablecer acción seleccionada
    bulkAction.value = '';
    updateBulkActionButton();
}

/**
 * Elimina valoraciones masivamente
 * @param {Array} reviewIds - Array de IDs de valoraciones a eliminar
 */
function deleteReviews(reviewIds) {
    let successCount = 0;
    let failedCount = 0;
    let totalToProcess = reviewIds.length;
    let processed = 0;

    // Mostrar notificación de inicio
    showNotification(`Procesando eliminación de ${totalToProcess} valoraciones...`, 'info');

    // Procesar cada valoración
    reviewIds.forEach(reviewId => {
        // Añadir efecto visual
        const row = document.querySelector(`.review-select[data-id="${reviewId}"]`).closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';

        // Llamar a la API para eliminar
        fetch(`/admin/api/reviews/${reviewId}`, {
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
                    updateReviewResults();

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
        showNotification(`Se han eliminado ${successCount} valoraciones correctamente`, 'success');
    } else if (successCount > 0 && failedCount > 0) {
        showNotification(`Operación completada: ${successCount} valoraciones eliminadas, ${failedCount} con errores`, 'warning');
    } else {
        showNotification(`Error al eliminar valoraciones: todas las operaciones fallaron`, 'error');
    }
}

/**
 * Cambia el estado destacado de múltiples valoraciones
 * @param {Array} reviewIds - Array de IDs de valoraciones
 * @param {boolean} highlight - Si deben destacarse o no
 */
function toggleReviewsHighlight(reviewIds, highlight) {
    let successCount = 0;
    let failedCount = 0;
    let totalToProcess = reviewIds.length;
    let processed = 0;

    // Mostrar notificación de inicio
    const actionText = highlight ? 'destacando' : 'quitando destacado de';
    showNotification(`Procesando ${actionText} ${totalToProcess} valoraciones...`, 'info');

    // Procesar cada valoración
    reviewIds.forEach(reviewId => {
        // Añadir efecto visual
        const row = document.querySelector(`.review-select[data-id="${reviewId}"]`).closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';

        // Llamar a la API para cambiar estado
        fetch(`/admin/api/reviews/${reviewId}/highlight`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ destacado: highlight })
        })
        .then(response => response.json())
        .then(data => {
            processed++;

            if (data.success) {
                successCount++;
                row.style.opacity = '1';

                // Actualizar UI
                updateReviewHighlightUI(reviewId, highlight);

                // Mostrar notificación final si es la última
                if (processed === totalToProcess) {
                    showHighlightFinalNotification(successCount, failedCount, totalToProcess, highlight);
                }
            } else {
                failedCount++;
                row.style.opacity = '1';

                // Mostrar notificación final si es la última
                if (processed === totalToProcess) {
                    showHighlightFinalNotification(successCount, failedCount, totalToProcess, highlight);
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
                showHighlightFinalNotification(successCount, failedCount, totalToProcess, highlight);
            }
        });
    });
}

/**
 * Muestra una notificación final con el resultado de la operación de destacar
 */
function showHighlightFinalNotification(successCount, failedCount, totalCount, highlight) {
    const actionText = highlight ? 'destacado' : 'quitado destacado de';

    if (successCount === totalCount) {
        showNotification(`Se ha ${actionText} ${successCount} valoraciones correctamente`, 'success');
    } else if (successCount > 0 && failedCount > 0) {
        showNotification(`Operación completada: ${actionText} ${successCount} valoraciones, ${failedCount} con errores`, 'warning');
    } else {
        showNotification(`Error al ${actionText} valoraciones: todas las operaciones fallaron`, 'error');
    }
}

/**
 * Actualiza la interfaz de usuario para reflejar el estado destacado de una valoración
 * @param {string} reviewId - ID de la valoración
 * @param {boolean} highlighted - Si la valoración está destacada o no
 */
function updateReviewHighlightUI(reviewId, highlighted) {
    // Actualizar botón
    const highlightBtn = document.querySelector(`.action-btn.highlight[data-id="${reviewId}"]`);
    if (highlightBtn) {
        if (highlighted) {
            highlightBtn.classList.add('is-highlighted');
            highlightBtn.title = 'Quitar destacado';
        } else {
            highlightBtn.classList.remove('is-highlighted');
            highlightBtn.title = 'Destacar';
        }
        highlightBtn.innerHTML = '<i class="fas fa-star"></i>';
    }

    // Actualizar badge de estado
    const statusBadge = document.querySelector(`tr[data-review-id="${reviewId}"] td:nth-child(8) .status-badge`);
    if (statusBadge) {
        statusBadge.className = `status-badge ${highlighted ? 'destacado' : 'normal'}`;
        statusBadge.textContent = highlighted ? 'Destacado' : 'Normal';
    }

    // Efecto visual en la fila
    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
    if (row) {
        row.style.transition = 'background-color 0.5s ease';
        row.style.backgroundColor = 'rgba(0, 255, 0, 0.1)';
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 1000);
    }
}
