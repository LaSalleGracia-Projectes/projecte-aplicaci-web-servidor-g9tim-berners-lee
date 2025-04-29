import config from '../utils/config.js';
import helpers from '../utils/helpers.js';

const authModule = {
    init() {
        this.checkAuthState();
        this.setupForms();
    },

    checkAuthState() {
        // Ya no necesitamos esta funcionalidad ya que Laravel maneja la autenticación
        // y los enlaces se muestran/ocultan con las directivas @guest y @auth
    },

    setupForms() {
        // Formulario de registro
        const registerForm = document.getElementById("registerForm");
        if (registerForm) {
            registerForm.addEventListener("submit", async (event) => {
                event.preventDefault();

                const formData = new FormData(registerForm);
                const data = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    password_confirmation: formData.get('password_confirmation')
                };

                try {
                    const response = await fetch("/register", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data),
                    });

                    const responseData = await response.json();

                    if (responseData.errors) {
                        // Mostrar errores en el formulario
                        Object.keys(responseData.errors).forEach(key => {
                            const errorElement = document.querySelector(`#${key}-error`);
                            if (errorElement) {
                                errorElement.textContent = responseData.errors[key][0];
                            }
                        });
                    } else {
                        // Redirigir al home después del registro exitoso
                        window.location.href = "/";
                    }
                } catch (error) {
                    console.error("Error:", error);
                    helpers.showToast("Error en la conexión. Inténtalo de nuevo.", "error");
                }
            });
        }

        // Formulario de login
        const loginForm = document.getElementById("loginForm");
        if (loginForm) {
            loginForm.addEventListener("submit", async (event) => {
                event.preventDefault();

                const formData = new FormData(loginForm);
                const data = {
                    email: formData.get('email'),
                    password: formData.get('password'),
                    remember: formData.get('remember') === 'on'
                };

                try {
                    const response = await fetch("/login", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data),
                    });

                    const responseData = await response.json();

                    if (responseData.errors) {
                        // Mostrar errores en el formulario
                        Object.keys(responseData.errors).forEach(key => {
                            const errorElement = document.querySelector(`#${key}-error`);
                            if (errorElement) {
                                errorElement.textContent = responseData.errors[key][0];
                            }
                        });
                    } else {
                        // Redirigir al home después del login exitoso
                        window.location.href = "/";
                    }
                } catch (error) {
                    console.error("Error:", error);
                    helpers.showToast("Error en la conexión. Inténtalo de nuevo.", "error");
                }
            });
        }
    }
};

export default authModule;
