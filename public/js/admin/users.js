/**
 * CritFlix Admin - Gestión de Usuarios
 * Este archivo contiene las funciones necesarias para gestionar usuarios en el panel de administración
 */

document.addEventListener('DOMContentLoaded', function() {
    initUserManagement();
});

/**
 * Inicializa todas las funcionalidades de la gestión de usuarios
 */
function initUserManagement() {
    // Configuración básica
    setupUserActions();
    setupModals();
    setupFilterReset();
    setupSearchFilter();
    setupTableSorting();

    console.log('Gestión de usuarios inicializada');
}

// Obtener el token CSRF de la meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/**
 * Configura los modales para editar, añadir y eliminar usuarios
 */
function setupModals() {
    // Abrir modal de edición cuando se hace clic en "Editar"
    document.querySelectorAll('.edit').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            openEditUserModal(userId);
        });
    });

    // Abrir modal de añadir usuario
    const addUserBtn = document.getElementById('addUserBtn');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            const modal = document.getElementById('addUserModal');
            if (modal) modal.classList.add('active');
        });
    }

    // Cerrar modales
    document.querySelectorAll('.modal-close, .close-modal').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.modal-backdrop').forEach(modal => {
                modal.classList.remove('active');
            });
        });
    });

    // Cerrar modales al hacer clic fuera
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Guardar cambios de usuario
    const saveUserBtn = document.getElementById('saveUserBtn');
    if (saveUserBtn) {
        saveUserBtn.addEventListener('click', saveUserChanges);
    }

    // Crear nuevo usuario
    const createUserBtn = document.getElementById('createUserBtn');
    if (createUserBtn) {
        createUserBtn.addEventListener('click', createUser);
    }
}

/**
 * Configura acciones principales para usuarios: eliminar, hacer admin
 */
function setupUserActions() {
    // Acción de eliminar usuario
    document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.closest('.user-item').querySelector('.user-name').textContent.trim();

            // Almacenar el ID del usuario a eliminar
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.setAttribute('data-id', userId);

                // Personalizar mensaje con el nombre de usuario
                const deleteMessage = document.querySelector('#deleteUserModal .modal-body p:first-child');
                if (deleteMessage) {
                    deleteMessage.innerHTML = `¿Estás seguro de que deseas eliminar al usuario <strong>${userName}</strong>? Esta acción no se puede deshacer.`;
                }

                // Mostrar modal de confirmación
                document.getElementById('deleteUserModal').classList.add('active');
            }
        });
    });

    // Confirmar eliminación
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            deleteUser(userId);
        });
    }

    // Hacer admin
    document.querySelectorAll('.admin').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.closest('.user-item').querySelector('.user-name').textContent.trim();

            if (confirm(`¿Estás seguro de que deseas convertir a ${userName} en Administrador? Esta acción otorgará permisos completos sobre la plataforma.`)) {
                makeUserAdmin(userId);
            }
        });
    });
}

/**
 * Abre el modal de edición de usuario y carga sus datos
 * @param {string} userId - ID del usuario a editar
 */
function openEditUserModal(userId) {
    // Mostrar notificación de carga
    showNotification('Cargando datos del usuario...', 'info');

    // Obtener datos del usuario
    fetch(`/admin/api/users/${userId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Llenar el formulario con los datos del usuario
            const user = data.user;
            document.getElementById('userId').value = user.id;
            document.getElementById('editName').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editRole').value = user.rol;
            document.getElementById('editStatus').checked = user.status;
            document.getElementById('editPassword').value = '';

            // Mostrar el modal
            document.getElementById('editUserModal').classList.add('active');
        } else {
            showNotification('Error al cargar los datos: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al cargar los datos del usuario', 'error');
    });
}

/**
 * Guarda los cambios del usuario
 */
function saveUserChanges() {
    const userId = document.getElementById('userId').value;
    const name = document.getElementById('editName').value;
    const email = document.getElementById('editEmail').value;
    const rol = document.getElementById('editRole').value;
    const password = document.getElementById('editPassword').value;
    const status = document.getElementById('editStatus').checked;

    // Validación básica
    if (!name || !email) {
        showNotification('Por favor completa todos los campos requeridos', 'error');
        return;
    }

    // Mostrar notificación de carga
    showNotification('Guardando cambios...', 'info');

    // Preparar datos
    const userData = {
        name,
        email,
        rol,
        status
    };

    // Añadir contraseña solo si se ha proporcionado
    if (password) {
        userData.password = password;
    }

    // Enviar petición
    fetch(`/admin/api/users/${userId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            document.getElementById('editUserModal').classList.remove('active');

            // Actualizar la UI con los nuevos datos
            updateUserUI(data.user);

            // Mostrar notificación de éxito
            showNotification('Usuario actualizado correctamente', 'success');
        } else {
            showNotification('Error al actualizar: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar usuario', 'error');
    });
}

/**
 * Crea un nuevo usuario
 */
function createUser() {
    const name = document.getElementById('addName').value;
    const email = document.getElementById('addEmail').value;
    const password = document.getElementById('addPassword').value;
    const rol = document.getElementById('addRole').value;
    const status = document.getElementById('addStatus').checked;

    // Validación básica
    if (!name || !email || !password) {
        showNotification('Por favor completa todos los campos requeridos', 'error');
        return;
    }

    // Mostrar notificación de carga
    showNotification('Creando usuario...', 'info');

    // Preparar datos
    const userData = {
        name,
        email,
        password,
        rol,
        status
    };

    // Enviar petición
    fetch('/admin/api/users', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            document.getElementById('addUserModal').classList.remove('active');

            // Refrescar la página para mostrar el nuevo usuario
            window.location.reload();

            // Mostrar notificación de éxito
            showNotification('Usuario creado correctamente', 'success');
        } else {
            showNotification('Error al crear usuario: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al crear usuario', 'error');
    });
}

/**
 * Elimina un usuario
 * @param {string} userId - ID del usuario a eliminar
 */
function deleteUser(userId) {
    // Mostrar notificación de carga
    showNotification('Eliminando usuario...', 'info');

    // Buscar fila del usuario
    const userItem = document.querySelector(`.user-item[data-id="${userId}"]`);

    // Efecto de eliminación inicial
    if (userItem) {
        userItem.style.opacity = '0.5';
    }

    // Enviar petición
    fetch(`/admin/api/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            document.getElementById('deleteUserModal').classList.remove('active');

            // Animar eliminación de la fila
            if (userItem) {
                userItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                userItem.style.opacity = '0';
                userItem.style.transform = 'translateX(20px)';

                setTimeout(() => {
                    userItem.remove();

                    // Comprobar si no quedan usuarios
                    if (document.querySelectorAll('.user-item').length === 0) {
                        const usersContainer = document.getElementById('users-container');
                        usersContainer.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h3>No hay usuarios disponibles</h3>
                                <p>No se encontraron usuarios que coincidan con los filtros aplicados. Intenta con otros criterios.</p>
                            </div>
                        `;
                    }
                }, 300);
            }

            // Mostrar notificación de éxito
            showNotification('Usuario eliminado correctamente', 'success');
        } else {
            // Restaurar opacidad si hay error
            if (userItem) {
                userItem.style.opacity = '1';
            }

            showNotification('Error al eliminar: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Restaurar opacidad si hay error
        if (userItem) {
            userItem.style.opacity = '1';
        }

        showNotification('Error al eliminar usuario', 'error');
    });
}

/**
 * Hace administrador a un usuario
 * @param {string} userId - ID del usuario a convertir en administrador
 */
function makeUserAdmin(userId) {
    // Mostrar notificación de carga
    showNotification('Convirtiendo en administrador...', 'info');

    // Buscar fila del usuario
    const userItem = document.querySelector(`.user-item[data-id="${userId}"]`);

    // Enviar petición
    fetch(`/admin/api/users/${userId}/make-admin`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la UI
            if (userItem) {
                // Actualizar rol badge
                const roleBadge = userItem.querySelector('.role-badge');
                if (roleBadge) {
                    roleBadge.className = 'role-badge admin';
                    roleBadge.textContent = 'Admin';
                }

                // Actualizar atributo data-role
                userItem.setAttribute('data-role', 'admin');

                // Ocultar botón de hacer admin
                const adminButton = userItem.querySelector('.admin');
                if (adminButton) {
                    adminButton.remove();
                }
            }

            // Mostrar notificación de éxito
            showNotification('Usuario convertido en administrador correctamente', 'success');
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al convertir en administrador', 'error');
    });
}

/**
 * Actualiza la UI de un usuario después de editarlo
 * @param {Object} user - Datos actualizados del usuario
 */
function updateUserUI(user) {
    const userItem = document.querySelector(`.user-item[data-id="${user.id}"]`);

    if (userItem) {
        // Actualizar nombre
        const userName = userItem.querySelector('.user-name');
        if (userName) {
            // Mantener el badge y actualizar el nombre
            const badge = userName.querySelector('.role-badge');
            userName.textContent = '';
            userName.appendChild(document.createTextNode(user.name));
            if (badge) {
                userName.appendChild(badge);
            }
        }

        // Actualizar email
        const userEmail = userItem.querySelector('.user-email');
        if (userEmail) {
            userEmail.textContent = user.email;
        }

        // Actualizar rol
        const roleBadge = userItem.querySelector('.role-badge');
        if (roleBadge) {
            roleBadge.className = `role-badge ${user.rol}`;
            roleBadge.textContent = user.rol.charAt(0).toUpperCase() + user.rol.slice(1);
        }

        // Actualizar estado
        const statusIcon = userItem.querySelector('.fas.fa-circle');
        if (statusIcon) {
            if (user.status) {
                statusIcon.className = 'fas fa-circle text-success';
                statusIcon.parentElement.lastChild.textContent = 'Activo';
            } else {
                statusIcon.className = 'fas fa-circle text-danger';
                statusIcon.parentElement.lastChild.textContent = 'Pendiente';
            }
        }

        // Actualizar botón de hacer admin si es necesario
        if (user.rol === 'admin') {
            const adminButton = userItem.querySelector('.admin');
            if (adminButton) {
                adminButton.remove();
            }
        }

        // Actualizar atributos de datos
        userItem.setAttribute('data-role', user.rol);
        userItem.setAttribute('data-status', user.status ? 'active' : 'inactive');
    }
}

/**
 * Configura el filtro de búsqueda rápida
 */
function setupSearchFilter() {
    const searchInput = document.getElementById('quickSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');

            userItems.forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = item.querySelector('.user-email').textContent.toLowerCase();

                if (userName.includes(searchValue) || userEmail.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Configura el botón para reiniciar filtros
 */
function setupFilterReset() {
    const resetButton = document.getElementById('resetFilters');

    if (resetButton) {
        resetButton.addEventListener('click', function() {
            const form = document.getElementById('filterForm');

            // Limpiar todos los campos
            form.querySelectorAll('input, select').forEach(field => {
                if (field.type === 'text') {
                    field.value = '';
                } else if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                }
            });

            // Enviar el formulario
            form.submit();
        });
    }
}

/**
 * Configura el ordenamiento de la tabla de usuarios
 */
function setupTableSorting() {
    const sortButtons = document.querySelectorAll('.sort-btn');

    if (sortButtons.length) {
        sortButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sortBy = this.getAttribute('data-sort');
                const sortDir = this.getAttribute('data-dir') || 'asc';
                const newSortDir = sortDir === 'asc' ? 'desc' : 'asc';

                // Actualizar atributos del botón
                this.setAttribute('data-dir', newSortDir);

                // Actualizar icono
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = `fas fa-sort-${newSortDir === 'asc' ? 'up' : 'down'}`;
                }

                // Ordenar usuarios
                sortUsers(sortBy, newSortDir);
            });
        });
    }
}

/**
 * Ordena los usuarios según el criterio especificado
 * @param {string} sortBy - Campo por el que ordenar
 * @param {string} sortDir - Dirección del ordenamiento (asc/desc)
 */
function sortUsers(sortBy, sortDir) {
    const usersContainer = document.getElementById('users-container');
    const userItems = Array.from(document.querySelectorAll('.user-item'));

    if (userItems.length && usersContainer) {
        // Ordenar los elementos
        userItems.sort((a, b) => {
            let valueA, valueB;

            switch (sortBy) {
                case 'name':
                    valueA = a.querySelector('.user-name').textContent.trim();
                    valueB = b.querySelector('.user-name').textContent.trim();
                    break;
                case 'email':
                    valueA = a.querySelector('.user-email').textContent.trim();
                    valueB = b.querySelector('.user-email').textContent.trim();
                    break;
                case 'role':
                    valueA = a.getAttribute('data-role');
                    valueB = b.getAttribute('data-role');
                    break;
                case 'status':
                    valueA = a.getAttribute('data-status');
                    valueB = b.getAttribute('data-status');
                    break;
                case 'date':
                    valueA = new Date(a.querySelector('.user-meta-item:first-child').textContent.replace('Registrado: ', ''));
                    valueB = new Date(b.querySelector('.user-meta-item:first-child').textContent.replace('Registrado: ', ''));
                    break;
                default:
                    valueA = a.querySelector('.user-name').textContent.trim();
                    valueB = b.querySelector('.user-name').textContent.trim();
            }

            // Comparar valores
            if (valueA < valueB) return sortDir === 'asc' ? -1 : 1;
            if (valueA > valueB) return sortDir === 'asc' ? 1 : -1;
            return 0;
        });

        // Limpiar contenedor
        usersContainer.innerHTML = '';

        // Añadir elementos ordenados
        userItems.forEach(item => {
            usersContainer.appendChild(item);
        });
    }
}

/**
 * Muestra una notificación
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, info, warning)
 */
function showNotification(message, type = 'info') {
    // Verificar si ya existe el contenedor de notificaciones
    let notificationsContainer = document.querySelector('.notifications-container');

    // Si no existe, crear uno nuevo
    if (!notificationsContainer) {
        notificationsContainer = document.createElement('div');
        notificationsContainer.className = 'notifications-container';
        document.body.appendChild(notificationsContainer);

        // Estilos para el contenedor
        notificationsContainer.style.position = 'fixed';
        notificationsContainer.style.top = '20px';
        notificationsContainer.style.right = '20px';
        notificationsContainer.style.zIndex = '9999';
        notificationsContainer.style.display = 'flex';
        notificationsContainer.style.flexDirection = 'column';
        notificationsContainer.style.alignItems = 'flex-end';
        notificationsContainer.style.gap = '10px';
    }

    // Iconos según el tipo
    const iconClass = {
        'success': 'check-circle',
        'error': 'times-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };

    // Crear la notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-${iconClass[type] || 'info-circle'}"></i>
        </div>
        <div class="notification-content">${message}</div>
        <button class="notification-close">&times;</button>
    `;

    // Estilos de la notificación
    Object.assign(notification.style, {
        display: 'flex',
        alignItems: 'center',
        padding: '12px 15px',
        borderRadius: '5px',
        boxShadow: '0 3px 10px rgba(0, 0, 0, 0.2)',
        marginBottom: '10px',
        opacity: '0',
        transform: 'translateX(50px)',
        transition: 'opacity 0.3s ease, transform 0.3s ease',
        maxWidth: '350px',
        backgroundColor: 'var(--bg-card)',
        color: 'var(--text-light)',
        border: `1px solid ${
            type === 'success' ? 'var(--verde-neon)' :
            type === 'error' ? 'var(--rojo-neon)' :
            type === 'warning' ? 'var(--amarillo-neon)' :
            'var(--cyan-neon)'
        }`
    });

    // Estilos para el icono
    const icon = notification.querySelector('.notification-icon');
    Object.assign(icon.style, {
        marginRight: '10px',
        fontSize: '1.1rem',
        color: type === 'success' ? 'var(--verde-neon)' :
                type === 'error' ? 'var(--rojo-neon)' :
                type === 'warning' ? 'var(--amarillo-neon)' :
                'var(--cyan-neon)'
    });

    // Estilos para el botón cerrar
    const closeBtn = notification.querySelector('.notification-close');
    Object.assign(closeBtn.style, {
        background: 'none',
        border: 'none',
        color: 'var(--text-muted)',
        fontSize: '1.2rem',
        cursor: 'pointer',
        marginLeft: '10px',
        padding: '0 5px'
    });

    // Función para eliminar la notificación
    const removeNotification = () => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';

        setTimeout(() => {
            notification.remove();
        }, 300);
    };

    // Evento para el botón cerrar
    closeBtn.addEventListener('click', removeNotification);

    // Auto cerrar después de cierto tiempo
    const duration = type === 'error' ? 6000 : 4000;
    setTimeout(removeNotification, duration);

    // Agregar la notificación al contenedor
    notificationsContainer.appendChild(notification);

    // Animar la entrada
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 10);
}

// Exportar funciones útiles para uso global
window.userAdmin = {
    openEditUserModal,
    deleteUser,
    makeUserAdmin,
    showNotification
};
