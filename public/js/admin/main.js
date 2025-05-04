/**
 * CritFlix - Panel de Administración
 * Funcionalidades principales para el panel administrativo
 */

// Objeto global para funciones de administración
window.critflixAdmin = {
    showNotification: function(title, message, type = 'success') {
        showNotification(title, message, type);
    },
    reloadStats: function() {
        // Función para recargar las estadísticas via AJAX
        ajaxRequest('/api/admin/stats', 'GET', {}, function(response) {
            if (response.success) {
                showNotification('Estadísticas', 'Datos actualizados correctamente', 'success');
                // Aquí se actualizarían los datos en el dashboard
                console.log('Estadísticas actualizadas:', response.stats);

                // Si se está en la página del dashboard, recargar
                if (window.location.pathname.includes('/admin')) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            }
        }, function(error) {
            showNotification('Error', 'No se pudieron actualizar las estadísticas', 'error');
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminPanel();
});

/**
 * Inicializa todas las funcionalidades del panel de administración
 */
function initializeAdminPanel() {
    // Configurar el comportamiento del botón de logout
    setupLogoutButton();

    // Configurar buscador global
    setupGlobalSearch();

    // Configurar menú móvil
    setupMobileMenu();

    // Inicializar tooltips y popovers si se usa Bootstrap
    setupBootstrapComponents();

    // Configurar el modo oscuro
    setupDarkMode();

    // Inicializar actualizaciones en tiempo real
    setupRealTimeUpdates();
}

/**
 * Configura el botón de logout
 */
function setupLogoutButton() {
    const logoutBtn = document.getElementById('admin-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Confirmar cierre de sesión
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                document.getElementById('logout-form').submit();
            }
        });
    }
}

/**
 * Configura la funcionalidad de búsqueda global
 */
function setupGlobalSearch() {
    const searchInput = document.querySelector('.admin-search input');
    const searchButton = document.querySelector('.admin-search button');

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                executeSearch(this.value.trim());
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            executeSearch(query);
        });
    }
}

/**
 * Ejecuta la búsqueda con el término proporcionado
 */
function executeSearch(query) {
    if (!query) return;

    showNotification('Búsqueda', `Buscando: "${query}"`, 'info');

    // Aquí se implementaría la búsqueda real
    // Por ahora solo mostraremos una notificación
    setTimeout(() => {
        showNotification('Resultados', `Se encontraron resultados para "${query}"`, 'success');
    }, 1000);
}

/**
 * Configura el menú de navegación para móviles
 */
function setupMobileMenu() {
    // Crear el botón de menú si no existe
    if (!document.querySelector('.menu-toggle')) {
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        menuToggle.setAttribute('title', 'Mostrar/ocultar menú');

        const headerTitle = document.querySelector('.admin-header-title');
        if (headerTitle) {
            headerTitle.parentNode.insertBefore(menuToggle, headerTitle);
        }
    }

    // Configurar evento de click para mostrar/ocultar sidebar
    document.querySelector('.menu-toggle')?.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        sidebar.classList.toggle('show');
    });

    // Ocultar sidebar al hacer click fuera de ella en móvil
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.admin-sidebar');
        const menuToggle = document.querySelector('.menu-toggle');

        if (window.innerWidth <= 768 &&
            sidebar &&
            menuToggle &&
            !sidebar.contains(e.target) &&
            !menuToggle.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
}

/**
 * Inicializa componentes de Bootstrap si están disponibles
 */
function setupBootstrapComponents() {
    if (typeof bootstrap !== 'undefined') {
        // Inicializar tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length) {
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }

        // Inicializar popovers
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popoverTriggerList.length) {
            [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        }
    }
}

/**
 * Configura la funcionalidad de modo oscuro
 */
function setupDarkMode() {
    // Cargar preferencia al inicio
    loadThemePreference();

    // Configurar evento para el botón de modo oscuro
    const darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', function() {
            toggleDarkMode();
        });
    }
}

/**
 * Carga las preferencias de tema del usuario
 */
function loadThemePreference() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedPreference = localStorage.getItem('critflix-dark-mode');

    if (savedPreference === 'true' || (savedPreference === null && prefersDark)) {
        document.body.classList.add('dark-mode');

        const darkModeBtn = document.getElementById('darkModeToggle');
        if (darkModeBtn) {
            darkModeBtn.innerHTML = '<i class="fas fa-sun"></i>';
        }
    }
}

/**
 * Alterna entre modo oscuro y claro
 */
function toggleDarkMode() {
    const body = document.body;
    const isDarkMode = body.classList.toggle('dark-mode');
    localStorage.setItem('critflix-dark-mode', isDarkMode ? 'true' : 'false');

    // Actualizar icono del botón
    const darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) {
        darkModeBtn.innerHTML = isDarkMode ?
            '<i class="fas fa-sun"></i>' :
            '<i class="fas fa-moon"></i>';
    }

    showNotification('Tema', isDarkMode ? 'Modo oscuro activado' : 'Modo claro activado', 'info');
}

/**
 * Configura actualizaciones en tiempo real
 */
function setupRealTimeUpdates() {
    // Ejemplo de actualización periódica
    setInterval(function() {
        const notificationBadge = document.querySelector('.notification-badge');
        if (notificationBadge) {
            // Simular actualizaciones de notificaciones
            const currentCount = parseInt(notificationBadge.textContent);
            if (Math.random() > 0.9) {
                notificationBadge.textContent = currentCount + 1;
            }
        }
    }, 60000); // Cada minuto
}

/**
 * Muestra una notificación toast
 * @param {string} title - Título de la notificación
 * @param {string} message - Mensaje de la notificación
 * @param {string} type - Tipo de notificación (success, error, info)
 */
function showNotification(title, message, type = 'success') {
    // Crear contenedor si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    toast.innerHTML = `
        <div class="toast-header">
            <strong>${title}</strong>
            <button type="button" class="btn-close">&times;</button>
        </div>
        <div class="toast-body">${message}</div>
    `;

    // Añadir al DOM
    toastContainer.appendChild(toast);

    // Mostrar y configurar autoclose
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);

    // Configurar botón de cierre
    const closeBtn = toast.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    }
}

/**
 * Realiza una petición AJAX con soporte para CSRF
 * @param {string} url - URL para la petición
 * @param {string} method - Método HTTP (GET, POST, PUT, DELETE)
 * @param {Object} data - Datos a enviar (opcional)
 * @param {Function} successCallback - Función a ejecutar cuando la petición es exitosa
 * @param {Function} errorCallback - Función a ejecutar cuando la petición falla (opcional)
 */
function ajaxRequest(url, method, data, successCallback, errorCallback) {
    // Obtener el token CSRF
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Configurar la petición
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', token);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            successCallback(JSON.parse(xhr.responseText));
        } else {
            showNotification('Error', 'Ha ocurrido un error en la petición', 'error');
            console.error('Error en la petición:', xhr.responseText);
            if (errorCallback) errorCallback(xhr);
        }
    };

    xhr.onerror = function() {
        showNotification('Error de conexión', 'No se ha podido conectar al servidor', 'error');
        if (errorCallback) errorCallback(xhr);
    };

    xhr.send(data ? JSON.stringify(data) : null);
}

/**
 * Confirma la eliminación de un elemento
 * @param {string} message - Mensaje de confirmación
 * @param {Function} callback - Función a ejecutar si se confirma
 */
function confirmDelete(message, callback) {
    if (confirm(message || '¿Estás seguro de que deseas eliminar este elemento?')) {
        callback();
    }
}

/**
 * Formatea una fecha en formato legible
 * @param {string} dateString - Fecha en formato ISO
 * @returns {string} Fecha formateada
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
