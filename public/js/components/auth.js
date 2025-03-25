import config from '../utils/config.js';
import helpers from '../utils/helpers.js';

const authModule = {
    init() {
        this.setupModals();
        this.checkAuthState();
        this.setupForms();
    },

    setupModals() {
        const loginLink = document.getElementById("loginLink");
        const registerLink = document.getElementById("registerLink");
        const loginModal = document.getElementById("loginModal");
        const registerModal = document.getElementById("registerModal");
        const closeLogin = document.getElementById("closeLogin");
        const closeRegister = document.getElementById("closeRegister");
        const movieDetailModal = document.getElementById("movieDetailModal");
        const profileLink = document.getElementById("profileLink");

        // Abrir modales
        if (loginLink && loginModal) {
            loginLink.addEventListener("click", (e) => {
                e.preventDefault();
                loginModal.classList.add("show");
            });
        }

        if (registerLink && registerModal) {
            registerLink.addEventListener("click", (e) => {
                e.preventDefault();
                registerModal.classList.add("show");
            });
        }

        if (profileLink) {
            profileLink.addEventListener("click", (e) => {
                e.preventDefault();
                const token = localStorage.getItem("token");
                const user = JSON.parse(localStorage.getItem("user"));
                if (token && user && user.id) {
                    window.location.href = `/profile/${user.id}`;
                }
            });
        }

        // Cerrar modales con el botón [X]
        if (closeLogin && loginModal) {
            closeLogin.addEventListener("click", () => {
                loginModal.classList.remove("show");
            });
        }

        if (closeRegister && registerModal) {
            closeRegister.addEventListener("click", () => {
                registerModal.classList.remove("show");
            });
        }

        // Cerrar modales al hacer clic fuera del contenido
        window.addEventListener("click", (e) => {
            if (loginModal && e.target === loginModal) {
                loginModal.classList.remove("show");
            }
            if (registerModal && e.target === registerModal) {
                registerModal.classList.remove("show");
            }
            if (movieDetailModal && e.target === movieDetailModal) {
                movieDetailModal.classList.remove("show");
            }
        });
    },

    checkAuthState() {
        const loginLink = document.getElementById("loginLink");
        const registerLink = document.getElementById("registerLink");
        const logoutButton = document.getElementById("logoutButton");

        // Ocultar enlaces si hay token en localStorage
        if (localStorage.getItem("token")) {
            if (loginLink) loginLink.style.display = "none";
            if (registerLink) registerLink.style.display = "none";
        }

        // Botón de logout
        if (logoutButton) {
            logoutButton.addEventListener("click", () => {
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                window.location.reload();
            });
        }
    },

    setupForms() {
        // Formulario de registro
        const registerForm = document.getElementById("registerForm");
        if (registerForm) {
            registerForm.addEventListener("submit", async (event) => {
                event.preventDefault(); // Evita la redirección por defecto

                const name = document.querySelector("#registerModal input[name='name']").value;
                const email = document.querySelector("#registerModal input[name='email']").value;
                const password = document.querySelector("#registerModal input[name='password']").value;
                const password_confirmation = document.querySelector("#registerModal input[name='password_confirmation']").value;

                try {
                    const response = await fetch("/api/register", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ name, email, password, password_confirmation }),
                    });

                    const data = await response.json();
                    console.log("Respuesta del servidor:", data);

                    if (data.errors) {
                        alert("Error en el registro: " + JSON.stringify(data.errors));
                    } else if (data.token) {
                        localStorage.setItem("token", data.token);
                        localStorage.setItem("user", JSON.stringify(data.user));
                        document.getElementById("registerModal").classList.remove("show");
                        window.location.href = "/";
                    } else {
                        alert("Error en el registro");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Error en la conexión. Inténtalo de nuevo.");
                }
            });
        }

        // Formulario de login
        const loginForm = document.getElementById("loginForm");
        if (loginForm) {
            loginForm.addEventListener("submit", async (event) => {
                event.preventDefault();

                const email = document.querySelector("#loginModal input[name='correo']").value;
                const password = document.querySelector("#loginModal input[name='contrasena']").value;

                try {
                    const response = await fetch("/api/login", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ email, password }),
                    });

                    const data = await response.json();

                    if (data.errors) {
                        alert("Error en el login: " + JSON.stringify(data.errors));
                    } else if (data.token) {
                        localStorage.setItem("token", data.token);
                        localStorage.setItem("user", JSON.stringify(data.user));
                        window.location.href = "/";
                    } else {
                        alert("Error en el login");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Error en la conexión. Inténtalo de nuevo.");
                }
            });
        }
    }
};

export default authModule;
