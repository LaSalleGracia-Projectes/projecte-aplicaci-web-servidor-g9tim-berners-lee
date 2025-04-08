document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form"); // Selecciona el formulario

    if (form) {
        console.log("Formulario encontrado.");

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Evita el envío tradicional del formulario
            console.log("Formulario enviado (preventDefault activado).");

            let formData = new FormData(this);

            // Obtener user_id desde localStorage
            let userId = localStorage.getItem("user_id");
            console.log("user_id obtenido desde localStorage:", userId);

            if (!userId) {
                alert("Error: No se encontró el usuario en localStorage");
                console.error("No se encontró 'user_id' en localStorage.");
                return;
            }

            formData.append("user_id", userId); // Agregar user_id a los datos
            console.log("Datos del formulario antes de enviar:", Object.fromEntries(formData));

            fetch(this.action, { // Usa la acción del formulario como URL
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: formData
            })
            .then(response => {
                console.log("Respuesta recibida del servidor:", response);
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.json();
            })
            .then(data => {
                console.log("Lista creada con éxito:", data);
                alert("Lista creada exitosamente");
                window.location.reload(); // Recarga la página o redirige donde quieras
            })
            .catch(error => {
                console.error("Error al crear la lista:", error);
                alert("No se pudo crear la lista. Revisa la consola para más detalles.");
            });
        });
    } else {
        console.error("No se encontró el formulario.");
    }
});
