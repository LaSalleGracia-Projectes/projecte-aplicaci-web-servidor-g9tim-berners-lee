/**
 * CritFlix Admin - Gestión de Usuarios
 * Este script se encarga de las funcionalidades específicas de la sección de usuarios
 */

document.addEventListener('DOMContentLoaded', function() {
    initUsersManagement();
});

/**
 * Inicializa todas las funcionalidades de la gestión de usuarios
 */
function initUsersManagement() {
    console.log('Inicializando gestión de usuarios...');

    // Inicializar filtros
    initUserFilters();

    // Inicializar búsqueda
    initUserSearch();

    // Inicializar selección masiva
    initBulkSelection();

    // Inicializar acciones de usuarios
    initUserActions();

    // Inicializar modal de edición
    initEditUserModal();

    // Inicializar modal de creación
    initAddUserModal();
}

/**
 * Inicializa los filtros de usuarios
 */
function initUserFilters() {
    // Filtro por botones (rol, estado)
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
            applyUserFilters();
        });
    });

    // Filtro por fecha
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');

    if (dateFrom && dateTo) {
        [dateFrom, dateTo].forEach(input => {
            input.addEventListener('change', applyUserFilters);
        });
    }

    // Botón de restablecer filtros
    const resetBtn = document.querySelector('.btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetUserFilters);
    }
}

/**
 * Aplica los filtros seleccionados a la tabla de usuarios
 */
function applyUserFilters() {
    // Obtener valores de filtros
    const roleFilter = document.querySelector('.filter-group:nth-child(1) .filter-btn.active').dataset.filter;
    const statusFilter = document.querySelector('.filter-group:nth-child(2) .filter-btn.active').dataset.filter;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;

    // Aplicar filtros a las filas de la tabla
    const rows = document.querySelectorAll('.admin-table tbody tr');

    rows.forEach(row => {
        let shouldShow = true;

        // Filtro por rol
        if (roleFilter !== 'all' && row.dataset.role !== roleFilter) {
            shouldShow = false;
        }

        // Filtro por estado
        if (statusFilter !== 'all' && row.dataset.status !== statusFilter) {
            shouldShow = false;
        }

        // Filtro por fecha (si está implementado)
        if (dateFrom && dateTo && row.dataset.date) {
            const rowDate = new Date(row.dataset.date);
            const fromDate = new Date(dateFrom);
            const toDate = new Date(dateTo);

            if (rowDate < fromDate || rowDate > toDate) {
                shouldShow = false;
            }
        }

        // Mostrar u ocultar la fila
        row.style.display = shouldShow ? '' : 'none';
    });

    // Actualizar contador de resultados
    updateUserResults();
}

/**
 * Restablece todos los filtros a su valor por defecto
 */
function resetUserFilters() {
    // Restablecer filtros de botones
    document.querySelectorAll('.filter-group').forEach(group => {
        const buttons = group.querySelectorAll('.filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        buttons[0].classList.add('active'); // El primero siempre es "Todos"
    });

    // Restablecer fechas
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');
    if (dateFrom && dateTo) {
        dateFrom.value = '';
        dateTo.value = '';
    }

    // Restablecer búsqueda
    const searchInput = document.getElementById('search-users');
    if (searchInput) {
        searchInput.value = '';
    }

    // Mostrar todas las filas
    document.querySelectorAll('.admin-table tbody tr').forEach(row => {
        row.style.display = '';
    });

    // Actualizar contador
    updateUserResults();
}

/**
 * Actualiza el contador de resultados de usuarios mostrados
 */
function updateUserResults() {
    const tableInfo = document.querySelector('.table-info');
    if (!tableInfo) return;

    const totalRows = document.querySelectorAll('.admin-table tbody tr').length;
    const visibleRows = document.querySelectorAll('.admin-table tbody tr[style=""]').length +
                       document.querySelectorAll('.admin-table tbody tr:not([style])').length;

    tableInfo.querySelector('span').innerHTML = `Mostrando <strong>${visibleRows}</strong> de <strong>${totalRows}</strong> usuarios`;
}

/**
 * Inicializa la búsqueda en tiempo real de usuarios
 */
function initUserSearch() {
    const searchInput = document.getElementById('search-users');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        // Buscar en todas las filas
        document.querySelectorAll('.admin-table tbody tr').forEach(row => {
            const userName = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            const matches = userName.includes(searchTerm) || userEmail.includes(searchTerm);

            // Solo filtramos por búsqueda si hay un término, de lo contrario respetamos los otros filtros
            if (searchTerm) {
                row.style.display = matches ? '' : 'none';
            } else if (!row.hasAttribute('data-filtered')) {
                row.style.display = '';
            }
        });

        // Actualizar contador
        updateUserResults();
    });
}

/**
 * Inicializa la selección masiva de usuarios
 */
function initBulkSelection() {
    const selectAll = document.getElementById('select-all');
    if (!selectAll) return;

    // Evento para seleccionar/deseleccionar todos
    selectAll.addEventListener('change', function() {
        const isChecked = this.checked;

        // Seleccionar/deseleccionar todas las filas visibles
        document.querySelectorAll('.admin-table tbody tr:not([style*="none"]) .user-select').forEach(checkbox => {
            checkbox.checked = isChecked;
        });

        // Actualizar estado del botón de acciones masivas
        updateBulkActionButton();
    });

    // Evento para cada checkbox individual
    document.querySelectorAll('.user-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButton();

            // Actualizar el checkbox "select-all"
            const allCheckboxes = document.querySelectorAll('.admin-table tbody tr:not([style*="none"]) .user-select');
            const checkedCheckboxes = document.querySelectorAll('.admin-table tbody tr:not([style*="none"]) .user-select:checked');

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

    const hasSelection = document.querySelectorAll('.user-select:checked').length > 0;
    const bulkAction = document.getElementById('bulk-action');

    // Habilitar/deshabilitar según selección y acción
    applyBtn.disabled = !hasSelection || !bulkAction.value;
}

/**
 * Aplica la acción masiva seleccionada a los usuarios
 */
function applyBulkAction() {
    const bulkAction = document.getElementById('bulk-action');
    if (!bulkAction || !bulkAction.value) return;

    const selectedUsers = Array.from(document.querySelectorAll('.user-select:checked'))
        .map(checkbox => checkbox.dataset.id);

    if (selectedUsers.length === 0) return;

    // Confirmar la acción
    const actionText = bulkAction.options[bulkAction.selectedIndex].text;
    if (!confirm(`¿Estás seguro de que deseas ${actionText.toLowerCase()} a ${selectedUsers.length} usuarios?`)) {
        return;
    }

    // Simulación de acción
    console.log(`Aplicando ${bulkAction.value} a usuarios:`, selectedUsers);

    // Añadir efecto visual
    selectedUsers.forEach(userId => {
        const row = document.querySelector(`.user-select[data-id="${userId}"]`).closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';

        // Simular actualización
        setTimeout(() => {
            row.style.opacity = '1';

            // Actualizar estado si es necesario
            switch (bulkAction.value) {
                case 'delete':
                    row.remove();
                    break;
                case 'activate':
                    row.dataset.status = 'activo';
                    row.querySelector('td:nth-child(8) .status-badge').className = 'status-badge activo';
                    row.querySelector('td:nth-child(8) .status-badge').textContent = 'Activo';
                    break;
                case 'deactivate':
                    row.dataset.status = 'inactivo';
                    row.querySelector('td:nth-child(8) .status-badge').className = 'status-badge inactivo';
                    row.querySelector('td:nth-child(8) .status-badge').textContent = 'Inactivo';
                    break;
                case 'make-critic':
                    row.dataset.role = 'critico';
                    row.querySelector('td:nth-child(6) .status-badge').className = 'status-badge critico';
                    row.querySelector('td:nth-child(6) .status-badge').textContent = 'Crítico';
                    break;
                case 'make-premium':
                    row.dataset.role = 'premium';
                    row.querySelector('td:nth-child(6) .status-badge').className = 'status-badge premium';
                    row.querySelector('td:nth-child(6) .status-badge').textContent = 'Premium';
                    break;
            }
        }, 500);
    });

    // Limpiar selección
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.user-select').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Restablecer acción seleccionada
    bulkAction.value = '';
    updateBulkActionButton();

    // Actualizar contador
    setTimeout(updateUserResults, 600);

    // Notificación
    showNotification(`Se ha aplicado la acción "${actionText}" a ${selectedUsers.length} usuarios`, 'success');
}

/**
 * Inicializa las acciones individuales para cada usuario
 */
function initUserActions() {
    // Evento para editar usuario
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.id;
            openEditUserModal(userId);
        });
    });

    // Evento para eliminar usuario
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.id;
            deleteUser(userId);
        });
    });

    // Evento para hacer admin
    document.querySelectorAll('.action-btn.make-admin').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.id;
            makeAdmin(userId);
        });
    });

    // Botón para añadir nuevo usuario
    const addUserBtn = document.querySelector('.add-user-btn');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', openAddUserModal);
    }
}

/**
 * Inicializa el modal de edición de usuario
 */
function initEditUserModal() {
    // Botón de cancelar
    const cancelBtn = document.querySelector('.cancel-edit');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            closeModal(document.getElementById('edit-user-modal'));
        });
    }

    // Formulario de edición
    const editForm = document.getElementById('edit-user-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveUserChanges();
        });
    }
}

/**
 * Inicializa el modal para añadir nuevo usuario
 */
function initAddUserModal() {
    const modal = document.getElementById('add-user-modal');
    const form = document.getElementById('add-user-form');
    const cancelBtn = document.querySelector('.cancel-add');
    const addBtn = document.querySelector('.add-user-btn');

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            openModal(modal);
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            closeModal(modal);
            form.reset();
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            createUser({
                name: document.getElementById('add-name').value,
                email: document.getElementById('add-email').value,
                password: document.getElementById('add-password').value,
                rol: document.getElementById('add-role').value,
                status: document.getElementById('add-status').checked
            });
        });
    }
}

/**
 * Abre el modal de edición de usuario con los datos del usuario
 * @param {string} userId - ID del usuario a editar
 */
function openEditUserModal(userId) {
    const modal = document.getElementById('edit-user-modal');
    if (!modal) return;

    // Buscar fila del usuario
    const userRow = document.querySelector(`.admin-table tr[data-user-id="${userId}"]`);
    if (!userRow) return;

    // Cargar datos en el modal
    document.getElementById('edit-user-id').value = userId;
    document.getElementById('edit-name').value = userRow.querySelector('td:nth-child(4)').textContent;
    document.getElementById('edit-email').value = userRow.querySelector('td:nth-child(5)').textContent;

    // Rol
    const userRole = userRow.dataset.role;
    document.getElementById('edit-role').value = userRole;

    // Estado
    const isActive = userRow.dataset.status === 'activo';
    document.getElementById('edit-status').checked = isActive;

    // Abrir modal
    openModal(modal);
}

/**
 * Guarda los cambios del usuario tras la edición
 */
function saveUserChanges() {
    const userId = document.getElementById('edit-user-id').value;
    const name = document.getElementById('edit-name').value;
    const email = document.getElementById('edit-email').value;
    const role = document.getElementById('edit-role').value;
    const status = document.getElementById('edit-status').checked;
    const password = document.getElementById('edit-password') ? document.getElementById('edit-password').value : '';

    // Verificar datos
    if (!name || !email) {
        showNotification('Todos los campos son obligatorios', 'error');
        return;
    }

    // Preparar datos para enviar
    const userData = {
        name,
        email,
        rol: role,
        status
    };

    // Si se proporcionó una contraseña, incluirla
    if (password) {
        userData.password = password;
    }

    // Mostrar efecto de carga
    const saveBtn = document.querySelector('.save-user');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    // Llamada a la API
    fetch(`/admin/api/users/${userId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar fila en la tabla
            const userRow = document.querySelector(`.admin-table tr[data-user-id="${userId}"]`);
            if (userRow) {
                userRow.querySelector('td:nth-child(4)').textContent = name;
                userRow.querySelector('td:nth-child(5)').textContent = email;

                // Actualizar rol
                userRow.dataset.role = role;
                const roleBadge = userRow.querySelector('td:nth-child(6) .status-badge');
                roleBadge.className = `status-badge ${role}`;
                roleBadge.textContent = role.charAt(0).toUpperCase() + role.slice(1);

                // Actualizar estado
                userRow.dataset.status = status ? 'activo' : 'inactivo';
                const statusBadge = userRow.querySelector('td:nth-child(8) .status-badge');
                statusBadge.className = `status-badge ${status ? 'activo' : 'inactivo'}`;
                statusBadge.textContent = status ? 'Activo' : 'Pendiente';

                // Efecto visual
                userRow.style.transition = 'background-color 0.5s ease';
                userRow.style.backgroundColor = 'rgba(20, 255, 20, 0.1)';
                setTimeout(() => {
                    userRow.style.backgroundColor = '';
                }, 1000);
            }

            // Cerrar modal
            closeModal(document.getElementById('edit-user-modal'));

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
 * Elimina un usuario del sistema
 * @param {string} userId - ID del usuario a eliminar
 */
function deleteUser(userId) {
    if (!userId) {
        console.error('ID de usuario no válido');
        showNotification('Error: ID de usuario no válido', 'error');
        return;
    }

    if (confirm('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.')) {
        console.log('Iniciando petición para eliminar usuario:', userId);

        // Buscar fila del usuario
        const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
        if (!userRow) {
            console.error('No se encontró la fila del usuario');
            showNotification('Error: No se encontró la fila del usuario', 'error');
            return;
        }

        // Efecto de eliminación inicial
        userRow.style.opacity = '0.5';

        // Llamada a la API
        fetch(`/admin/api/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Respuesta recibida:', response);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos de la respuesta:', data);

            if (data.success) {
                // Animación de eliminación
                userRow.style.transition = 'opacity 0.5s ease, transform 0.5s ease, height 0.5s ease';
                userRow.style.opacity = '0';
                userRow.style.transform = 'translateX(20px)';

                setTimeout(() => {
                    userRow.style.height = '0';
                    userRow.style.overflow = 'hidden';
                    userRow.style.padding = '0';
                    userRow.style.border = '0';

                    setTimeout(() => {
                        userRow.remove();
                        // Actualizar estadísticas y contadores
                        updateStats();
                        updateUserResults();
                        showNotification('Usuario eliminado exitosamente', 'success');
                    }, 300);
                }, 300);
            } else {
                userRow.style.opacity = '1';
                showNotification(data.message || 'Error al eliminar el usuario', 'error');
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            userRow.style.opacity = '1';
            showNotification('Error al eliminar el usuario: ' + error.message, 'error');
        });
    }
}

/**
 * Crea un nuevo usuario
 * @param {Object} userData - Datos del usuario a crear
 */
function createUser(userData) {
    // Mostrar efecto de carga
    const addBtn = document.querySelector('.add-user');
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    }

    // Llamada a la API
    fetch('/admin/api/users', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Añadir nueva fila a la tabla
            addUserRow(data.user);

            // Cerrar modal y resetear formulario
            closeModal(document.getElementById('add-user-modal'));
            document.getElementById('add-user-form').reset();

            // Actualizar estadísticas
            updateStats();

            // Notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al crear el usuario', 'error');
    })
    .finally(() => {
        // Restaurar botón
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = 'Crear Usuario';
        }
    });
}

/**
 * Actualiza las estadísticas en tiempo real
 */
function updateStats() {
    fetch('/admin/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar números en las tarjetas
                document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = data.stats.total_users;
                document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = data.stats.verified_users;
                document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = data.stats.unverified_users ||
                    (data.stats.total_users - data.stats.verified_users);
                document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = data.stats.new_users_today;
            }
        })
        .catch(error => console.error('Error al actualizar estadísticas:', error));
}

/**
 * Alterna la verificación de un usuario
 * @param {string} userId - ID del usuario
 * @param {boolean} isVerified - Estado actual de verificación
 */
function toggleUserVerification(userId, isVerified) {
    const btn = document.querySelector(`.action-btn.verify[data-id="${userId}"]`);
    if (!btn) return;

    // Mostrar spinner
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Determinar la ruta y el método
    const route = isVerified ? `/admin/api/users/${userId}/unverify` : `/admin/api/users/${userId}/verify`;
    const method = 'PUT';

    // Llamada a la API
    fetch(route, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar UI
            updateUserVerificationUI(userId, !isVerified);

            // Actualizar estadísticas
            updateStats();

            // Notificación
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al cambiar el estado de verificación', 'error');
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = originalContent;
        btn.disabled = false;
    });
}

/**
 * Actualiza la interfaz de usuario después de cambiar el estado de verificación
 * @param {string} userId - ID del usuario
 * @param {boolean} isVerified - Nuevo estado de verificación
 */
function updateUserVerificationUI(userId, isVerified) {
    // Actualizar badge de estado
    const statusBadge = document.querySelector(`tr[data-user-id="${userId}"] td:nth-child(8) .status-badge`);
    if (statusBadge) {
        statusBadge.className = `status-badge ${isVerified ? 'activo' : 'inactivo'}`;
        statusBadge.textContent = isVerified ? 'Activo' : 'Pendiente';
    }

    // Actualizar botón de verificación
    const verifyBtn = document.querySelector(`.action-btn.verify[data-id="${userId}"]`);
    if (verifyBtn) {
        verifyBtn.className = `action-btn verify ${isVerified ? 'verified' : ''}`;
        verifyBtn.innerHTML = isVerified ?
            '<i class="fas fa-check"></i>' :
            '<i class="fas fa-times"></i>';
    }

    // Actualizar atributo data-status de la fila
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (userRow) {
        userRow.dataset.status = isVerified ? 'activo' : 'inactivo';
    }
}

/**
 * Muestra una notificación en la interfaz
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    // Definir iconos según el tipo
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    // Definir títulos según el tipo
    const titles = {
        success: 'Éxito',
        error: 'Error',
        warning: 'Advertencia',
        info: 'Información'
    };

    // Definir colores según el tipo
    const colors = {
        success: '#4CAF50',
        error: '#F44336',
        warning: '#FF9800',
        info: '#2196F3'
    };

    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon" style="color: ${colors[type]}">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="notification-body">
                <h4 class="notification-title">${titles[type]}</h4>
                <p class="notification-message">${message}</p>
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    // Añadir al DOM
    document.body.appendChild(notification);

    // Animación de entrada
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Eliminar después de 5 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

/**
 * Hace admin a un usuario
 * @param {string} userId - ID del usuario
 */
async function makeAdmin(userId) {
    if (!confirm('¿Estás seguro de que deseas hacer administrador a este usuario?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${userId}/make-admin`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Error al hacer administrador al usuario');
        }

        const data = await response.json();

        // Actualizar la interfaz
        const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
        if (userRow) {
            const rolCell = userRow.querySelector('.user-role');
            if (rolCell) {
                rolCell.textContent = 'admin';
            }
        }

        // Mostrar notificación de éxito
        Swal.fire({
            title: '¡Éxito!',
            text: 'El usuario ha sido promovido a administrador',
            icon: 'success',
            confirmButtonText: 'OK'
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}
