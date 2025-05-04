/**
 * CritFlix Admin Dashboard - Funcionalidades específicas del Dashboard
 * Este script se encarga de las funcionalidades específicas del panel principal de administración
 */

document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
});

/**
 * Inicializa todas las funcionalidades específicas del dashboard
 */
function initDashboard() {
    console.log('Inicializando dashboard de CritFlix...');

    // Inicializar gráficas
    initDashboardCharts();

    // Inicializar eventos para acciones rápidas
    initQuickActions();

    // Actualizar estadísticas en tiempo real cada 30 segundos
    fetchRealTimeStats();
    setInterval(fetchRealTimeStats, 30000);
}

/**
 * Inicializa las gráficas específicas del dashboard
 */
function initDashboardCharts() {
    // Gráfico de barras para registros de usuarios
    initUserRegistrationChart();

    // Gráfico circular para distribución de roles
    initRoleDistributionChart();

    // Animamos las gráficas después de un pequeño retraso
    setTimeout(() => {
        animateCharts();
    }, 300);
}

/**
 * Inicializa el gráfico de registros de usuarios
 */
function initUserRegistrationChart() {
    const chartBars = document.querySelector('.chart-bars');
    if (!chartBars) return;

    // Limpiar el contenido existente
    chartBars.innerHTML = '';

    // Verificar si tenemos datos reales de la ventana global
    let userData = [];
    if (window.dashboardData && window.dashboardData.userRegistration) {
        userData = Object.entries(window.dashboardData.userRegistration);
    }

    // Si no hay datos, usar datos aleatorios como fallback
    if (userData.length === 0) {
        for (let i = 1; i <= 30; i++) {
            const height = Math.floor(Math.random() * 85) + 5; // Entre 5% y 90%
            const bar = document.createElement('div');
            bar.className = 'chart-bar';
            bar.style.height = '0%'; // Comenzamos con altura 0 para animación
            bar.dataset.height = `${height}%`;

            // Tooltip con información del día
            bar.title = `Día ${i}: ${Math.floor(height / 2)} usuarios`;

            chartBars.appendChild(bar);
        }
    } else {
        // Encontrar el valor máximo para escalar las barras
        const maxUsers = Math.max(...userData.map(item => item[1]));

        // Crear barras basadas en datos reales
        userData.forEach(([date, count], index) => {
            const height = maxUsers > 0 ? Math.max((count / maxUsers) * 90, 5) : 5; // Mínimo 5% de altura

            const bar = document.createElement('div');
            bar.className = 'chart-bar';
            bar.style.height = '0%'; // Comenzamos con altura 0 para animación
            bar.dataset.height = `${height}%`;

            // Formatear la fecha para el tooltip (yyyy-mm-dd a dd/mm)
            const formattedDate = date.slice(8, 10) + '/' + date.slice(5, 7);
            bar.title = `${formattedDate}: ${count} usuarios`;

            chartBars.appendChild(bar);
        });
    }
}

/**
 * Inicializa el gráfico circular de distribución de roles
 */
function initRoleDistributionChart() {
    const ctx = document.getElementById('role-distribution-chart').getContext('2d');
    const rolePercentages = window.rolePercentages || {
        admin: { count: 0, percentage: 0 },
        premium: { count: 0, percentage: 0 },
        critico: { count: 0, percentage: 0 },
        usuario: { count: 0, percentage: 0 }
    };

    const data = {
        labels: ['Administradores', 'Premium', 'Críticos', 'Usuarios'],
        datasets: [{
            data: [
                rolePercentages.admin.count,
                rolePercentages.premium.count,
                rolePercentages.critico.count,
                rolePercentages.usuario.count
            ],
            backgroundColor: [
                'rgba(255, 48, 96, 0.8)',     // Rojo para Administradores
                'rgba(255, 204, 0, 0.8)',     // Amarillo para Premium
                'rgba(0, 232, 255, 0.8)',     // Cyan para Críticos
                'rgba(0, 255, 102, 0.8)'      // Verde para Usuarios
            ],
            borderColor: [
                '#ff3060',     // Rojo para Administradores
                '#ffcc00',     // Amarillo para Premium
                '#00e8ff',     // Cyan para Críticos
                '#00ff66'      // Verde para Usuarios
            ],
            borderWidth: 2,
            hoverOffset: 15
        }]
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const percentage = rolePercentages[context.label.toLowerCase()]?.percentage || 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    const roleChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: options
    });

    // Añadir interactividad con la leyenda
    document.querySelectorAll('.legend-item').forEach(item => {
        item.addEventListener('click', function() {
            const role = this.dataset.role;
            const index = data.labels.findIndex(label =>
                label.toLowerCase() === role
            );

            if (index !== -1) {
                const meta = roleChart.getDatasetMeta(0);
                const currentState = meta.data[index].hidden;

                // Toggle el estado de visibilidad
                meta.data[index].hidden = !currentState;

                // Actualizar el estilo del elemento de leyenda
                this.classList.toggle('inactive');

                // Actualizar el gráfico
                roleChart.update();
            }
        });
    });

    return roleChart;
}

/**
 * Anima todas las gráficas con efectos visuales
 */
function animateCharts() {
    // Animar barras con efecto de crecimiento
    const bars = document.querySelectorAll('.chart-bar');
    bars.forEach((bar, index) => {
        setTimeout(() => {
            bar.style.transition = 'height 0.6s cubic-bezier(0.2, 0.8, 0.3, 1.2)';
            bar.style.height = bar.dataset.height;
        }, index * 40); // Retraso progresivo para efecto en cascada
    });

    // Animar gráfico circular con efecto de aparición
    const donut = document.querySelector('.chart-donut');
    if (donut) {
        donut.style.transform = 'scale(0.5) rotate(-180deg)';
        donut.style.opacity = '0';

        setTimeout(() => {
            donut.style.transition = 'transform 1.2s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.8s ease-in';
            donut.style.transform = 'scale(1) rotate(0deg)';
            donut.style.opacity = '1';
        }, 300);

        // Añadir efecto de brillo al pasar el ratón
        donut.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 0 25px var(--verde-neon), 0 0 10px var(--verde-claro)';
            this.style.transform = 'scale(1.08) rotate(5deg)';
        });

        donut.addEventListener('mouseleave', function() {
            this.style.boxShadow = '0 0 20px rgba(20, 255, 20, 0.3)';
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    }

    // Animar leyendas
    const legendItems = document.querySelectorAll('.legend-item');
    legendItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-30px)';

        setTimeout(() => {
            item.style.transition = 'opacity 0.5s ease, transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 800 + (index * 150));
    });

    // Añadir efecto de pulsación al texto central después de la animación
    setTimeout(() => {
        if (donut) {
            donut.style.animation = 'pulse 2s infinite alternate';
        }
    }, 2000);
}

// Añadir la keyframe animation para el efecto de pulsación
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% {
                box-shadow: 0 0 20px rgba(20, 255, 20, 0.3);
            }
            100% {
                box-shadow: 0 0 30px rgba(20, 255, 20, 0.6), 0 0 15px rgba(0, 255, 200, 0.4);
            }
        }
    `;
    document.head.appendChild(style);
});

/**
 * Inicializa los eventos para acciones rápidas en el dashboard
 */
function initQuickActions() {
    // Eventos para los botones de acción en las tablas
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const action = this.classList.contains('view') ? 'ver' :
                          this.classList.contains('edit') ? 'editar' : 'eliminar';

            const row = this.closest('tr');
            const id = row.dataset.userId || row.dataset.reviewId;

            const isUser = row.dataset.userId !== undefined;
            const type = isUser ? 'usuario' : 'valoración';

            // Manejar las diferentes acciones
            if (action === 'ver') {
                if (isUser) {
                    // Redireccionar al perfil del usuario
                    window.location.href = `/profile/${id}`;
                } else {
                    // Mostrar detalles de la valoración
                    showNotification(`Ver detalles de la valoración #${id}`, 'info');
                }
            } else if (action === 'editar') {
                if (isUser) {
                    // Redireccionar a la página de edición de usuario
                    window.location.href = `/admin/users?edit=${id}`;
                } else {
                    showNotification(`Editar valoración #${id}`, 'info');
                }
            } else if (action === 'eliminar') {
                // Eliminar usuario o valoración
                if (isUser) {
                    if (confirm(`¿Estás seguro de que deseas eliminar al usuario #${id}?`)) {
                        deleteUser(id);
                    }
                } else {
                    if (confirm(`¿Estás seguro de que deseas eliminar la valoración #${id}?`)) {
                        deleteReview(id);
                    }
                }
            }
        });
    });
}

/**
 * Obtiene estadísticas en tiempo real desde la API
 */
function fetchRealTimeStats() {
    fetch('/admin/api/stats', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboardStats(data.stats);
        } else {
            console.error('Error al obtener estadísticas:', data.message);
        }
    })
    .catch(error => {
        console.error('Error de conexión:', error);
    });
}

/**
 * Actualiza las estadísticas del dashboard con los nuevos valores
 * @param {Object} stats - Objeto con las estadísticas
 */
function updateDashboardStats(stats) {
    // Actualizar contadores
    const statElements = {
        'total_users': document.querySelector('.stat-users .admin-stat-value'),
        'total_movies': document.querySelector('.stat-movies .admin-stat-value'),
        'total_reviews': document.querySelector('.stat-reviews .admin-stat-value'),
        'visits_today': document.querySelector('.stat-visits .admin-stat-value')
    };

    // Actualizar cada estadística con efecto visual
    for (const [key, element] of Object.entries(statElements)) {
        if (element && stats[key] !== undefined) {
            // Solo animar si el valor es diferente
            const currentValue = parseInt(element.textContent);
            const newValue = stats[key];

            if (currentValue !== newValue) {
                // Guardar valores para animación
                element.dataset.oldValue = currentValue;
                element.dataset.newValue = newValue;

                // Iniciar animación
                animateValueChange(element);
            }
        }
    }
}

/**
 * Anima el cambio de valor con un efecto de contador
 * @param {HTMLElement} element - Elemento a animar
 */
function animateValueChange(element) {
    const oldValue = parseInt(element.dataset.oldValue);
    const newValue = parseInt(element.dataset.newValue);

    // Si los valores son iguales, no hay nada que animar
    if (oldValue === newValue) return;

    // Destacar visualmente
    element.style.transition = 'color 0.3s ease, transform 0.3s ease';
    element.style.color = newValue > oldValue ? 'var(--verde-claro)' : 'var(--rojo-neon)';
    element.style.transform = 'scale(1.1)';

    // Determinar la duración de la animación y el incremento
    const duration = 1000; // 1 segundo
    const difference = newValue - oldValue;
    const increment = difference > 0 ? 1 : -1;
    const steps = Math.abs(difference);
    const stepDuration = duration / steps;

    let currentStep = 0;
    let currentValue = oldValue;

    // Usar un intervalo para incrementar/decrementar gradualmente
    const interval = setInterval(() => {
        if (currentStep >= steps) {
            // Finalizar la animación
            clearInterval(interval);
            element.textContent = newValue;

            // Restaurar estilos
            setTimeout(() => {
                element.style.color = '';
                element.style.transform = 'scale(1)';
            }, 300);
            return;
        }

        // Actualizar el valor
        currentValue += increment;
        element.textContent = currentValue;
        currentStep++;
    }, stepDuration);
}

/**
 * Muestra una notificación en pantalla
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación: success, error, warning, info
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

    // Crear la notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' :
                            type === 'error' ? 'times-circle' :
                            type === 'warning' ? 'exclamation-triangle' :
                            'info-circle'}"></i>
        </div>
        <div class="notification-content">${message}</div>
        <button class="notification-close">×</button>
    `;

    // Estilos para la notificación
    notification.style.display = 'flex';
    notification.style.alignItems = 'center';
    notification.style.padding = '12px 15px';
    notification.style.borderRadius = '5px';
    notification.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.2)';
    notification.style.marginBottom = '10px';
    notification.style.opacity = '0';
    notification.style.transform = 'translateX(50px)';
    notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    notification.style.maxWidth = '350px';
    notification.style.backgroundColor = 'var(--fondo-oscuro)';
    notification.style.border = `1px solid ${
        type === 'success' ? 'var(--verde-neon)' :
        type === 'error' ? 'var(--rojo-neon)' :
        type === 'warning' ? 'var(--amarillo-neon)' :
        'var(--azul-neon)'
    }`;
    notification.style.color = 'var(--texto-claro)';

    // Estilos para el icono
    const icon = notification.querySelector('.notification-icon');
    icon.style.marginRight = '10px';
    icon.style.fontSize = '1.1rem';
    icon.style.color = type === 'success' ? 'var(--verde-neon)' :
                      type === 'error' ? 'var(--rojo-neon)' :
                      type === 'warning' ? 'var(--amarillo-neon)' :
                      'var(--azul-neon)';

    // Estilos para el botón cerrar
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.background = 'none';
    closeBtn.style.border = 'none';
    closeBtn.style.color = 'var(--texto-gris)';
    closeBtn.style.fontSize = '1.2rem';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.marginLeft = '10px';
    closeBtn.style.padding = '0 5px';

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

/**
 * Elimina un usuario
 * @param {string} userId - ID del usuario a eliminar
 */
function deleteUser(userId) {
    // Buscar fila del usuario
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!userRow) return;

    // Efecto de eliminación inicial
    userRow.style.opacity = '0.5';

    // Llamada a la API
    fetch(`/admin/api/users/${userId}`, {
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
                    fetchRealTimeStats(); // Actualizar las estadísticas

                    // Notificación
                    showNotification(data.message, 'success');
                }, 300);
            }, 300);
        } else {
            // Restaurar opacidad y mostrar error
            userRow.style.opacity = '1';
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Restaurar opacidad y mostrar error
        userRow.style.opacity = '1';
        showNotification('Error al eliminar el usuario', 'error');
    });
}

/**
 * Elimina una reseña
 * @param {string} reviewId - ID de la reseña a eliminar
 */
function deleteReview(reviewId) {
    // Buscar fila de la reseña
    const reviewRow = document.querySelector(`tr[data-review-id="${reviewId}"]`);
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
                    fetchRealTimeStats(); // Actualizar las estadísticas

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
