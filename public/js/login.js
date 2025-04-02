document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('#loginForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isSubmitting = false;

    // Usar la función showNotification definida en el header
    function displayError(message) {
        const errorDiv = document.getElementById('login-error');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        } else {
            // Si no existe el div de error, usar la notificación
            showNotification(message, 'error');
        }
    }

    function clearErrors() {
        const errorDiv = document.getElementById('login-error');
        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.classList.add('hidden');
        }
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }

    if (loginForm && csrfToken) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmitting) return;
            isSubmitting = true;

            // Clear previous error messages
            clearErrors();

            const formData = {
                correo: document.querySelector('#email').value,
                contrasena: document.querySelector('#password').value,
                remember: document.querySelector('#remember').checked
            };

            // Validate required fields
            if (!formData.correo || !formData.contrasena) {
                console.log('Validation failed: Missing required fields');
                displayError('Por favor complete todos los campos');
                isSubmitting = false;
                return;
            }

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData),
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok) {
                    // Mostrar mensaje de éxito
                    showNotification(data.message || 'Inicio de sesión exitoso', 'success');
                    
                    // Actualizar nombre de usuario si está disponible
                    if (data.user) {
                        const userNameElement = document.querySelector('.text-white.text-base.font-medium');
                        if (userNameElement) {
                            userNameElement.textContent = data.user.nombre;
                        }
                    }

                    // Cerrar el modal de login
                    if (typeof Alpine !== 'undefined') {
                        Alpine.store('modal').open = false;
                    }

                    // Redireccionar después de un breve retraso
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1500);
                } else {
                    // Mostrar mensaje de error
                    displayError(data.message || 'Las credenciales proporcionadas son incorrectas');
                    isSubmitting = false;
                }
            } catch (error) {
                console.error('Error during login:', error);
                displayError('Error al procesar la solicitud. Por favor, inténtelo de nuevo más tarde.');
                isSubmitting = false;
            }
        });
    }
});