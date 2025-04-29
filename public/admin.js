/**
 * JavaScript para el panel de administración de CritFlix
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes del panel admin
    initAdminPanel();
});

/**
 * Inicializa todas las funcionalidades del panel de administración
 */
function initAdminPanel() {
    // Gestión de navegación y pestañas
    initNavigation();

    // Inicializar componentes interactivos
    initDropdowns();
    initModals();
    initTooltips();

    // Inicializar formularios
    initForms();

    // Inicializar eventos de botones
    initButtons();

    // Añadir efectos de hover para tarjetas neón
    initNeonEffects();

    // Si estamos en la página del dashboard, inicializar las gráficas
    if (document.querySelector('.admin-charts')) {
        initCharts();
        // Animación retrasada para que se vea el efecto de carga
        setTimeout(() => {
            animateBarChart();
            animateDonutChart();
        }, 300);
    }

    // Si estamos en la página de perfil, inicializar eventos específicos
    if (document.querySelector('.admin-profile-container')) {
        initProfilePage();
    }

    console.log('Panel de administración inicializado correctamente');
}

/**
 * Gestiona la navegación del panel lateral
 */
function initNavigation() {
    // Gestionar navegación activa
    const navItems = document.querySelectorAll('.admin-nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.id === 'admin-logout') {
                e.preventDefault();
                confirmLogout();
                return;
            }

            // Si no es el enlace de logout, marcar como activo
            if (!this.classList.contains('active')) {
                navItems.forEach(navItem => navItem.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Gestionar toggle del menú en móviles
    const menuToggle = document.createElement('button');
    menuToggle.className = 'admin-menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';

    // Añadir toggle de menú en móviles
    const header = document.querySelector('.admin-header');
    if (header) {
        header.prepend(menuToggle);

        menuToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.classList.toggle('show-mobile');

            // Cambiar el icono del botón
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('show-mobile')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

/**
 * Inicializa los efectos de neón para las tarjetas y elementos interactivos
 */
function initNeonEffects() {
    // Añadir eventos de hover para tarjetas neón
    document.querySelectorAll('.neon-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.borderColor = 'var(--verde-neon)';
            card.style.boxShadow = 'var(--neon-primary-glow)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.borderColor = 'rgba(20, 255, 20, 0.3)';
            card.style.boxShadow = 'none';
        });
    });

    // Efecto para botones de acción
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.textShadow = 'var(--neon-primary-glow)';
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.textShadow = 'none';
        });
    });

    // Efecto para botones neón
    document.querySelectorAll('.btn-neon').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.boxShadow = 'var(--neon-primary-glow)';
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.boxShadow = 'none';
        });
    });

    // Efecto para elementos del sidebar
    document.querySelectorAll('.admin-nav-item').forEach(item => {
        if (!item.classList.contains('active')) {
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = 'rgba(20, 255, 20, 0.05)';
                item.style.color = 'var(--verde-neon)';
            });

            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = '';
                item.style.color = '';
            });
        }
    });
}

/**
 * Inicializa campos de formulario y validación
 */
function initForms() {
    // Formulario de perfil
    const profileForm = document.getElementById('admin-profile-form');

    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Recopilar datos del formulario
            const name = document.getElementById('admin-name').value;
            const email = document.getElementById('admin-email').value;
            const role = document.getElementById('admin-role').value;
            const bio = document.getElementById('admin-bio').value;

            // Aquí iría la lógica para enviar los datos al servidor
            console.log('Datos actualizados:', { name, email, role, bio });

            // Mostrar notificación de éxito
            showNotification('Perfil actualizado correctamente', 'success');
        });
    }

    // Aplicar estilo a los inputs al tener foco
    const inputs = document.querySelectorAll('.neon-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
}

/**
 * Inicializa botones y sus eventos
 */
function initButtons() {
    // Botón de logout
    const logoutBtn = document.getElementById('admin-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            confirmLogout();
        });
    }

    // Botones de acciones en tablas
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const action = this.classList.contains('view') ? 'ver' :
                          this.classList.contains('edit') ? 'editar' : 'eliminar';

            const itemId = this.closest('tr').querySelector('td:first-child').textContent;

            if (action === 'eliminar') {
                confirmDelete(itemId);
            } else {
                console.log(`Acción: ${action} el elemento ${itemId}`);
            }
        });
    });

    // Botón de editar perfil
    const editProfileBtn = document.querySelector('.edit-profile');
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            const detailsSection = document.querySelector('.admin-profile-details');
            detailsSection.scrollIntoView({ behavior: 'smooth' });

            // Focus en el primer input
            setTimeout(() => {
                document.getElementById('admin-name').focus();
            }, 500);
        });
    }

    // Botón de cambiar contraseña
    const changePasswordBtn = document.querySelector('.change-password');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            showPasswordModal();
        });
    }
}

/**
 * Inicializa dropdowns y menús desplegables
 */
function initDropdowns() {
    // Implementar si se necesitan dropdowns
}

/**
 * Inicializa ventanas modales
 */
function initModals() {
    // Crear un contenedor para modales si no existe
    if (!document.getElementById('modal-container')) {
        const modalContainer = document.createElement('div');
        modalContainer.id = 'modal-container';
        document.body.appendChild(modalContainer);
    }
}

/**
 * Inicializa tooltips para botones e íconos
 */
function initTooltips() {
    // Implementar tooltips simples
    const elements = document.querySelectorAll('[title]');
    elements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            this.removeAttribute('title');

            const tooltip = document.createElement('div');
            tooltip.className = 'admin-tooltip';
            tooltip.textContent = title;

            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = `${rect.bottom + 10}px`;
            tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;

            this.setAttribute('data-tooltip-text', title);
            this.tooltipElement = tooltip;
        });

        el.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                document.body.removeChild(this.tooltipElement);
                this.setAttribute('title', this.getAttribute('data-tooltip-text'));
                this.removeAttribute('data-tooltip-text');
                delete this.tooltipElement;
            }
        });
    });
}

/**
 * Inicializa las gráficas del dashboard
 */
function initCharts() {
    console.log('Inicializando gráficas');

    // Preparar el gráfico de barras (si existe)
    const chartBars = document.querySelector('.chart-bars');
    if (chartBars) {
        // Limpiar las barras existentes
        chartBars.innerHTML = '';

        // Generar datos aleatorios para los últimos 30 días
        for (let i = 1; i <= 30; i++) {
            const height = Math.floor(Math.random() * 90) + 10; // Altura entre 10% y 100%
            const bar = document.createElement('div');
            bar.className = 'chart-bar';
            bar.style.height = '0%'; // Inicialmente en 0 para animar
            bar.dataset.height = `${height}%`; // Guardar la altura objetivo para la animación
            chartBars.appendChild(bar);
        }
    }

    // Preparar el gráfico de donut (si existe)
    const chartDonut = document.querySelector('.chart-donut');
    if (chartDonut) {
        // Ya se configura por CSS, pero podríamos actualizar los datos dinámicamente
        // Por ejemplo, obteniendo los porcentajes reales de usuarios por rol
    }
}

/**
 * Anima el gráfico de barras con un efecto de crecimiento
 */
function animateBarChart() {
    const bars = document.querySelectorAll('.chart-bar');

    bars.forEach((bar, index) => {
        // Añadir delay incremental para efecto cascada
        setTimeout(() => {
            bar.style.height = bar.dataset.height;
        }, index * 30);
    });
}

/**
 * Anima el gráfico de dona con un efecto de rotación
 */
function animateDonutChart() {
    const donut = document.querySelector('.chart-donut');

    if (donut) {
        donut.style.transform = 'rotate(-90deg)';
        donut.style.opacity = '0';

        setTimeout(() => {
            donut.style.transition = 'transform 1s ease-out, opacity 0.5s ease-in';
            donut.style.transform = 'rotate(0deg)';
            donut.style.opacity = '1';
        }, 100);
    }
}

/**
 * Inicializa la página de perfil de administrador
 */
function initProfilePage() {
    // Añadir funcionalidad para cambiar contraseña
    const changePasswordBtn = document.querySelector('.change-password');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            showPasswordModal();
        });
    }

    // Animación para las métricas de perfil
    const metrics = document.querySelectorAll('.stats-metric');
    if (metrics.length) {
        metrics.forEach((metric, index) => {
            const progress = metric.querySelector('.metric-progress');
            const value = metric.querySelector('.metric-value').textContent;
            const percentage = parseInt(value);

            // Animar la barra de progreso
            progress.style.width = '0%';
            setTimeout(() => {
                progress.style.transition = 'width 1s ease-in-out';
                progress.style.width = percentage + '%';
            }, 300 * index);
        });
    }
}

/**
 * Muestra una ventana de confirmación para cerrar sesión
 */
function confirmLogout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        console.log('Cerrando sesión...');
        // Aquí iría el código para cerrar sesión
        window.location.href = '/';
    }
}

/**
 * Muestra una ventana de confirmación para eliminar un elemento
 */
function confirmDelete(itemId) {
    if (confirm(`¿Estás seguro de que deseas eliminar el elemento ${itemId}?`)) {
        console.log(`Eliminando el elemento ${itemId}...`);
        // Aquí iría el código para eliminar
        showNotification(`Elemento ${itemId} eliminado correctamente`, 'success');
    }
}

/**
 * Muestra una ventana modal para cambiar la contraseña
 */
function showPasswordModal() {
    // Crear modal
    const modalHTML = `
        <div class="admin-modal">
            <div class="admin-modal-content neon-card">
                <div class="admin-modal-header">
                    <h3>Cambiar Contraseña</h3>
                    <button class="admin-modal-close">&times;</button>
                </div>
                <div class="admin-modal-body">
                    <form id="change-password-form" class="admin-form">
                        <div class="form-group">
                            <label for="current-password">Contraseña Actual</label>
                            <input type="password" id="current-password" class="neon-input" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">Nueva Contraseña</label>
                            <input type="password" id="new-password" class="neon-input" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirmar Contraseña</label>
                            <input type="password" id="confirm-password" class="neon-input" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-neon cancel-btn">Cancelar</button>
                            <button type="submit" class="btn-neon save-btn">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = modalHTML;

    // Mostrar modal
    setTimeout(() => {
        modalContainer.classList.add('show');
        document.body.classList.add('modal-open');
    }, 10);

    // Cerrar modal
    const closeBtn = modalContainer.querySelector('.admin-modal-close');
    const cancelBtn = modalContainer.querySelector('.cancel-btn');

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Fuera del modal
    modalContainer.addEventListener('click', function(e) {
        if (e.target === modalContainer) {
            closeModal();
        }
    });

    // Form submit
    const form = document.getElementById('change-password-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (newPassword !== confirmPassword) {
            showNotification('Las contraseñas no coinciden', 'error');
            return;
        }

        // Aquí iría la lógica para cambiar la contraseña
        console.log('Cambiando contraseña...');

        closeModal();
        showNotification('Contraseña actualizada correctamente', 'success');
    });

    function closeModal() {
        modalContainer.classList.remove('show');
        document.body.classList.remove('modal-open');
        setTimeout(() => {
            modalContainer.innerHTML = '';
        }, 300);
    }
}

/**
 * Muestra una notificación al usuario
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (info, success, warning, error)
 */
function showNotification(message, type = 'info') {
    // Eliminar notificaciones existentes
    const existingNotification = document.querySelector('.admin-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Crear la notificación
    const notification = document.createElement('div');
    notification.className = `admin-notification ${type}`;

    // Determinar el icono según el tipo
    let icon = 'info-circle';
    switch (type) {
        case 'success':
            icon = 'check-circle';
            break;
        case 'warning':
            icon = 'exclamation-triangle';
            break;
        case 'error':
            icon = 'times-circle';
            break;
    }

    // Crear el contenido
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-${icon}"></i>
        </div>
        <div class="notification-content">
            ${message}
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Añadir al DOM
    document.body.appendChild(notification);

    // Mostrar con animación
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Configurar el cierre
    const closeBtn = notification.querySelector('.notification-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeNotification);
    }

    // Auto-cierre después de 5 segundos
    setTimeout(() => {
        closeNotification();
    }, 5000);

    /**
     * Cierra la notificación con animación
     */
    function closeNotification() {
        notification.classList.remove('show');

        // Eliminar del DOM después de la animación
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

// Añadir estilos CSS para las notificaciones, tooltips y modales que dependen de JS
const styleElement = document.createElement('style');
styleElement.textContent = `
    /* Modal */
    #modal-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    #modal-container.show {
        opacity: 1;
        visibility: visible;
    }

    .admin-modal {
        width: 100%;
        max-width: 500px;
        margin: 0 20px;
    }

    .admin-modal-content {
        background-color: var(--card-bg);
        border-radius: 10px;
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        box-shadow: var(--neon-primary-glow);
    }

    #modal-container.show .admin-modal-content {
        transform: translateY(0);
    }

    .admin-modal-header {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .admin-modal-header h3 {
        margin: 0;
        color: var(--text-color);
    }

    .admin-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .admin-modal-close:hover {
        color: var(--danger-color);
    }

    .admin-modal-body {
        padding: 20px;
    }

    /* Notificaciones */
    #notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .admin-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        padding: 15px;
        background-color: var(--card-bg);
        border-radius: 5px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
        transform: translateX(100%);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .admin-notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .admin-notification.hide {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notification-content i {
        font-size: 18px;
    }

    .admin-notification.info {
        border-left: 4px solid var(--info-color);
    }

    .admin-notification.info i {
        color: var(--info-color);
    }

    .admin-notification.success {
        border-left: 4px solid var(--success-color);
    }

    .admin-notification.success i {
        color: var(--success-color);
    }

    .admin-notification.error {
        border-left: 4px solid var(--danger-color);
    }

    .admin-notification.error i {
        color: var(--danger-color);
    }

    .admin-notification.warning {
        border-left: 4px solid var(--warning-color);
    }

    .admin-notification.warning i {
        color: var(--warning-color);
    }

    .notification-close {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .notification-close:hover {
        color: var(--text-color);
    }

    /* Tooltips */
    .admin-tooltip {
        position: absolute;
        background-color: var(--dark-color);
        color: var(--text-color);
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Estilos adicionales para móviles */
    @media (max-width: 576px) {
        .admin-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 5px;
            color: var(--text-color);
            font-size: 18px;
            cursor: pointer;
        }

        .admin-sidebar.show-mobile {
            transform: translateX(0);
            width: var(--sidebar-width);
        }

        #notification-container {
            left: 20px;
            right: 20px;
        }

        .admin-notification {
            min-width: auto;
            width: 100%;
        }
    }
`;

document.head.appendChild(styleElement);

