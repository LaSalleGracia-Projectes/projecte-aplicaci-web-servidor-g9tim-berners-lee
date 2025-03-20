/**
 * Profile.js - JavaScript para la funcionalidad de la página de perfil
 * CineNeon - Sistema de gestión de películas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicialización de pestañas
    initTabs();

    // Inicializar la previsualización de la foto de perfil
    initPhotoPreview();

    // Inicializar las alertas
    initAlerts();
});

/**
 * Inicializa la funcionalidad de pestañas
 */
function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');

    if (tabButtons.length === 0) return;

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Obtener el ID del panel correspondiente
            const tabId = this.getAttribute('data-tab');
            const tabPanel = document.getElementById(`${tabId}-panel`);

            // Quitar la clase activa de todos los botones y paneles
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });

            // Agregar la clase activa al botón y panel seleccionados
            this.classList.add('active');
            if (tabPanel) {
                tabPanel.classList.add('active');
            }

            // Guardar la pestaña activa en localStorage
            localStorage.setItem('activeProfileTab', tabId);
        });
    });

    // Restaurar la última pestaña activa
    const lastActiveTab = localStorage.getItem('activeProfileTab');
    if (lastActiveTab) {
        const tabToActivate = document.querySelector(`.tab-button[data-tab="${lastActiveTab}"]`);
        if (tabToActivate) {
            tabToActivate.click();
        }
    }
}

/**
 * Inicializa la previsualización de la foto de perfil
 */
function initPhotoPreview() {
    const fileInput = document.getElementById('foto_perfil');
    const fileNameDisplay = document.querySelector('.selected-file-name');

    if (!fileInput) return;

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Mostrar el nombre del archivo seleccionado
            fileNameDisplay.textContent = this.files[0].name;

            // Previsualizar la imagen
            const reader = new FileReader();
            reader.onload = function(e) {
                const currentPhoto = document.querySelector('.current-photo');

                // Eliminar el contenido actual
                currentPhoto.innerHTML = '';

                // Crear una nueva imagen con la previsualización
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Vista previa';

                // Añadir la imagen a la previsualización
                currentPhoto.appendChild(img);
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            fileNameDisplay.textContent = 'Ningún archivo seleccionado';
        }
    });
}

/**
 * Inicializa las alertas para que se oculten automáticamente
 */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');

    if (alerts.length === 0) return;

    alerts.forEach(alert => {
        // Añadir un botón de cierre si no existe
        if (!alert.querySelector('.alert-close')) {
            const closeButton = document.createElement('span');
            closeButton.classList.add('alert-close');
            closeButton.innerHTML = '&times;';
            closeButton.style.float = 'right';
            closeButton.style.cursor = 'pointer';
            closeButton.style.fontSize = '1.2em';

            closeButton.addEventListener('click', function() {
                alert.style.display = 'none';
            });

            alert.insertBefore(closeButton, alert.firstChild);
        }

        // Ocultar automáticamente después de 5 segundos
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 1s ease';

            setTimeout(() => {
                alert.style.display = 'none';
            }, 1000);
        }, 5000);
    });
}

/**
 * Validación del formulario de perfil
 */
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.querySelector('.profile-form');

    if (!profileForm) return;

    profileForm.addEventListener('submit', function(e) {
        let hasError = false;

        // Validar el nombre
        const nameInput = document.getElementById('name');
        if (nameInput && nameInput.value.trim() === '') {
            showFieldError(nameInput, 'El nombre es obligatorio');
            hasError = true;
        } else if (nameInput && nameInput.value.length > 50) {
            showFieldError(nameInput, 'El nombre no puede exceder los 50 caracteres');
            hasError = true;
        }

        // Validar el email
        const emailInput = document.getElementById('email');
        if (emailInput && emailInput.value.trim() === '') {
            showFieldError(emailInput, 'El correo electrónico es obligatorio');
            hasError = true;
        } else if (emailInput && !isValidEmail(emailInput.value)) {
            showFieldError(emailInput, 'Por favor, introduce un correo electrónico válido');
            hasError = true;
        }

        // Validar la biografía
        const bioInput = document.getElementById('biografia');
        if (bioInput && bioInput.value.length > 500) {
            showFieldError(bioInput, 'La biografía no puede exceder los 500 caracteres');
            hasError = true;
        }

        // Validar el archivo de imagen
        const photoInput = document.getElementById('foto_perfil');
        if (photoInput && photoInput.files.length > 0) {
            const file = photoInput.files[0];
            const fileSize = file.size / 1024 / 1024; // en MB
            const fileType = file.type;

            if (!['image/jpeg', 'image/png', 'image/gif', 'image/jpg'].includes(fileType)) {
                showFieldError(photoInput, 'Solo se permiten imágenes en formato JPEG, PNG o GIF');
                hasError = true;
            }

            if (fileSize > 2) {
                showFieldError(photoInput, 'La imagen no puede superar los 2MB');
                hasError = true;
            }
        }

        // Si hay errores, prevenir el envío del formulario
        if (hasError) {
            e.preventDefault();
        }
    });

    // Validación de contraseña
    const passwordForm = document.querySelector('form[action*="password"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            let hasError = false;

            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');

            if (currentPassword && currentPassword.value.trim() === '') {
                showFieldError(currentPassword, 'La contraseña actual es obligatoria');
                hasError = true;
            }

            if (newPassword && newPassword.value.trim() === '') {
                showFieldError(newPassword, 'La nueva contraseña es obligatoria');
                hasError = true;
            } else if (newPassword && newPassword.value.length < 8) {
                showFieldError(newPassword, 'La contraseña debe tener al menos 8 caracteres');
                hasError = true;
            }

            if (confirmPassword && confirmPassword.value !== newPassword.value) {
                showFieldError(confirmPassword, 'Las contraseñas no coinciden');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    }
});

/**
 * Muestra un mensaje de error para un campo específico
 */
function showFieldError(field, message) {
    // Buscar si ya existe un mensaje de error
    let errorSpan = field.parentNode.querySelector('.form-error');

    // Si no existe, crear uno nuevo
    if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.classList.add('form-error');
        field.parentNode.appendChild(errorSpan);
    }

    // Actualizar el mensaje de error
    errorSpan.textContent = message;

    // Resaltar el campo con error
    field.style.borderColor = 'var(--rojo-suave)';
    field.style.boxShadow = '0 0 5px var(--rojo-suave)';

    // Restaurar el estilo al enfocarse en el campo
    field.addEventListener('focus', function() {
        this.style.borderColor = 'var(--verde-neon)';
        this.style.boxShadow = '0 0 5px var(--verde-neon)';
        errorSpan.textContent = '';
    });
}

/**
 * Valida si el email tiene un formato correcto
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Añade efectos visuales a elementos de la página
 */
document.addEventListener('DOMContentLoaded', function() {
    // Efecto de hover para elementos interactivos
    const interactiveElements = document.querySelectorAll('.btn-neon, .tab-button');

    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Efecto de popup para avisos importantes
    const successMessage = getQueryParam('success');
    if (successMessage) {
        showPopupMessage(decodeURIComponent(successMessage), 'success');
    }

    const errorMessage = getQueryParam('error');
    if (errorMessage) {
        showPopupMessage(decodeURIComponent(errorMessage), 'error');
    }
});

/**
 * Obtiene el valor de un parámetro de la URL
 */
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

/**
 * Muestra un mensaje emergente temporal
 */
function showPopupMessage(message, type) {
    const popup = document.createElement('div');
    popup.classList.add('popup-message');
    popup.classList.add(`popup-${type}`);

    popup.innerHTML = `
        <div class="popup-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(popup);

    // Estilo dinámico del popup
    popup.style.position = 'fixed';
    popup.style.top = '100px';
    popup.style.right = '-300px';
    popup.style.backgroundColor = type === 'success' ? 'rgba(0, 255, 60, 0.9)' : 'rgba(229, 57, 53, 0.9)';
    popup.style.color = 'white';
    popup.style.padding = '15px 20px';
    popup.style.borderRadius = '8px';
    popup.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.3)';
    popup.style.zIndex = '9999';
    popup.style.transition = 'right 0.5s ease';

    // Animar entrada
    setTimeout(() => {
        popup.style.right = '20px';
    }, 100);

    // Animar salida
    setTimeout(() => {
        popup.style.right = '-300px';
        setTimeout(() => {
            document.body.removeChild(popup);
        }, 500);
    }, 5000);
}
