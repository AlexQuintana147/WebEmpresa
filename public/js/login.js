document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[x-show="isLogin"]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isSubmitting = false;

    function showNotification(message, type = 'error') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        notification.style.transform = 'translateX(100%)';

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });

        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    if (loginForm && csrfToken) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmitting) return;
            isSubmitting = true;

            // Clear previous error messages
            loginForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            loginForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const formData = {
                correo: document.querySelector('#email').value,
                contrasena: document.querySelector('#password').value,
                remember: document.querySelector('#remember').checked
            };

            // Validate required fields
            if (!formData.correo || !formData.contrasena) {
                console.log('Validation failed: Missing required fields');
                showNotification('Por favor complete todos los campos', 'error');
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
                    showNotification(data.message || 'Inicio de sesión exitoso', 'success');
                    
                    if (data.user) {
                        const userNameElement = document.querySelector('.text-white.text-sm.font-medium');
                        if (userNameElement) {
                            userNameElement.textContent = data.user.nombre;
                        }
                    }

                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Error al iniciar sesión', 'error');
                    isSubmitting = false;
                }
            } catch (error) {
                console.error('Error during login:', error);
                showNotification('Error al procesar la solicitud', 'error');
                isSubmitting = false;
            }
        });
    }
});