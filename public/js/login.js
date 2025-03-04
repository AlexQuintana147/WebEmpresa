document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[x-show="isLogin"]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

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

            // Clear previous error messages
            loginForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            loginForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const formData = {
                correo: document.querySelector('#email').value,
                contrasena: document.querySelector('#password').value,
                remember: document.querySelector('#remember').checked
            };

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
                }
            } catch (error) {
                console.error('Error during login:', error);
                showNotification('Error al procesar la solicitud', 'error');
            }

            // Validate required fields
            if (!formData.correo || !formData.contrasena) {
                console.log('Validation failed: Missing required fields');
                showNotification('Por favor complete todos los campos', 'error');
                return;
            }

            try {
                console.log('Form validation passed, proceeding with login');
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                console.log('CSRF Token found:', !!csrfToken, 'Token value:', csrfToken?.getAttribute('content'));
                
                if (!csrfToken) {
                    console.error('CSRF token not found in the document');
                    showNotification('Error de seguridad: Token CSRF no encontrado', 'error');
                    return;
                }

                console.log('Preparing to send login request...');
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify(formData),
                    credentials: 'same-origin'
                });

                console.log('Login response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    headers: Object.fromEntries(response.headers.entries())
                });

                const data = await response.json();
                console.log('Login response data:', data);

                if (response.ok) {
                    console.log('Login successful, user data:', data.user);
                    console.log('Session state:', document.cookie);
                    showNotification(data.message || 'Inicio de sesión exitoso', 'success');
                    
                    if (data.user) {
                        console.log('Attempting to update user name display');
                        const userNameElement = document.querySelector('.text-white.text-sm.font-medium');
                        if (userNameElement) {
                            console.log('Found user name element, updating to:', data.user.nombre);
                            userNameElement.textContent = data.user.nombre;
                        } else {
                            console.log('User name element not found in the DOM');
                        }
                    } else {
                        console.log('No user data received from server');
                    }

                    setTimeout(() => {
                        console.log('Redirecting to dashboard...');
                        window.location.href = '/';
                    }, 1500);
                } else {
                    console.error('Login failed:', data);
                    showNotification(data.message || 'Error al iniciar sesión', 'error');
                }
            } catch (error) {
                console.error('Error during login:', error);
                showNotification('Error al procesar la solicitud', 'error');
            }
        });
    } else {
        console.error('Login form not found in the document');
    }
});