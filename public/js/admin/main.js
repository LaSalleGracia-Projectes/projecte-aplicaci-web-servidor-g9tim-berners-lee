// Configuración global para AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Toggle del sidebar
$(document).ready(function() {
    $("#sidebarToggleTop").on('click', function(e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
    });

    // Cerrar cualquier menú abierto cuando la ventana es pequeña
    if (window.innerWidth < 768) {
        $(".sidebar").addClass("toggled");
    }

    // Prevenir que el wrapper se desplace cuando el sidebar está minimizado
    $(window).resize(function() {
        if (window.innerWidth < 768) {
            $(".sidebar").addClass("toggled");
        }
    });

    // Prevenir que el dropdown del usuario se cierre al hacer click dentro
    $(".dropdown-menu").on('click', function(e) {
        e.stopPropagation();
    });

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    // Crear el elemento de notificación
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);

    // Agregar la notificación al contenedor
    $("#notifications").append(notification);

    // Remover la notificación después de 5 segundos
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}

// Función para confirmar acciones
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Función para formatear fechas
function formatDate(date) {
    const d = new Date(date);
    return d.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Función para validar formularios
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    let isValid = true;
    const errors = {};

    for (const field in rules) {
        const value = form[field].value;
        const fieldRules = rules[field];

        if (fieldRules.required && !value) {
            errors[field] = 'Este campo es requerido';
            isValid = false;
        }

        if (fieldRules.email && !/\S+@\S+\.\S+/.test(value)) {
            errors[field] = 'Email inválido';
            isValid = false;
        }

        if (fieldRules.minLength && value.length < fieldRules.minLength) {
            errors[field] = `Mínimo ${fieldRules.minLength} caracteres`;
            isValid = false;
        }
    }

    // Mostrar errores si existen
    for (const field in errors) {
        const errorElement = document.getElementById(`${field}-error`);
        if (errorElement) {
            errorElement.textContent = errors[field];
        }
    }

    return isValid;
}

// Función para manejar errores de AJAX
function handleAjaxError(xhr, status, error) {
    console.error('Error en la solicitud AJAX:', error);
    let errorMessage = 'Ha ocurrido un error. Por favor, inténtalo de nuevo.';

    if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
    }

    showNotification(errorMessage, 'danger');
}

// Exportar funciones útiles
window.adminHelpers = {
    showNotification,
    confirmAction,
    formatDate,
    validateForm,
    handleAjaxError
};
