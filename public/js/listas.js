// Funciones para la gestión de listas
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la página de creación de lista
    if (document.getElementById('userId')) {
        const user = JSON.parse(localStorage.getItem('user'));

        if (!user || !user.id) {
            window.location.href = '/';
            return;
        }

        document.getElementById('userId').value = user.id;
    }

    // Contador de caracteres para el nombre
    const nombreInput = document.getElementById('nombre_lista');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length > 100) {
                this.value = this.value.substring(0, 100);
            }
        });
    }

    // Contador de caracteres para la descripción
    const descripcionTextarea = document.getElementById('descripcion');
    if (descripcionTextarea) {
        descripcionTextarea.addEventListener('input', function() {
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
            }
        });
    }
});
