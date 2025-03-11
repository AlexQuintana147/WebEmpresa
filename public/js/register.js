document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form[x-show="!isLogin"]');
    const errorMessages = {
        nombre: 'El nombre solo puede contener letras y espacios',
        correo: 'Por favor ingrese un correo válido',
        contrasena: 'La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un carácter especial'
    };

    // Función para mostrar notificaciones
    function showNotification(message, type = 'error') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out ${
            type === 'success' ? 'bg-green-500' : 'bg-blue-500'
        } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animación de entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Animación de salida y eliminación
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Función para validar formato de correo electrónico
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Función para mostrar error en un campo específico
    function showFieldError(inputElement, message) {
        // Eliminar mensajes de error previos
        const parent = inputElement.parentNode;
        parent.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Agregar clase de error y mensaje
        inputElement.classList.add('is-invalid');
        inputElement.classList.remove('is-valid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback text-red-500 text-sm mt-1';
        feedback.textContent = message;
        parent.appendChild(feedback);
    }

    // Función para mostrar estado válido en un campo
    function showFieldValid(inputElement) {
        // Eliminar mensajes de error previos
        const parent = inputElement.parentNode;
        parent.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Agregar clase de válido
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
        inputElement.style.borderColor = '#10b981'; // Color verde para indicar validez
    }

    if (registerForm) {
        // Validación en tiempo real para el campo de correo
        const emailInput = document.querySelector('#reg-email');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                
                if (email === '') {
                    // Si el campo está vacío, mostrar error
                    this.style.borderColor = '#ef4444'; // Rojo
                    showFieldError(this, 'El correo electrónico es obligatorio');
                } else if (!validateEmail(email)) {
                    // Si el formato no es válido, mostrar error
                    this.style.borderColor = '#ef4444'; // Rojo
                    showFieldError(this, 'Por favor ingrese un correo electrónico válido');
                } else {
                    // Si es válido, mostrar indicador de éxito
                    showFieldValid(this);
                }
            });

            // También validar al perder el foco
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email === '') {
                    showFieldError(this, 'El correo electrónico es obligatorio');
                } else if (!validateEmail(email)) {
                    showFieldError(this, 'Por favor ingrese un correo electrónico válido');
                }
            });
        }

        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submission started');

            // Clear previous error messages
            registerForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            registerForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const formData = {
                nombre: document.querySelector('#reg-name').value,
                correo: document.querySelector('#reg-email').value,
                contrasena: document.querySelector('#reg-password').value,
                password_confirmation: document.querySelector('#reg-password-confirmation').value
            };
            console.log('Form data collected:', formData);

            // Validar correo antes de enviar
            const emailInput = document.querySelector('#reg-email');
            if (!formData.correo || !validateEmail(formData.correo)) {
                showFieldError(emailInput, 'Por favor ingrese un correo electrónico válido');
                return; // Detener el envío si el correo no es válido
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                console.log('CSRF Token element:', csrfToken);
                console.log('CSRF Token value:', csrfToken ? csrfToken.getAttribute('content') : 'Not found');

                if (!csrfToken) {
                    console.error('CSRF token not found in the document');
                    showNotification('Error de seguridad: Token CSRF no encontrado', 'error');
                    return;
                }

                console.log('Sending registration request...');
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify(formData),
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    console.log('Registration successful');
                    showNotification('Registro exitoso', 'success');
                    setTimeout(() => {
                        window.location.href = window.location.href;
                    }, 1500);
                } else {
                    console.error('Registration failed:', data);
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.querySelector(`#reg-${field}`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = data.errors[field][0];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                    } else {
                        showNotification(data.message || 'Error en el registro', 'error');
                    }
                }
            } catch (error) {
                console.error('Error during registration:', error);
                showNotification('Error en el registro: ' + error.message, 'error');
            }
        });
    } else {
        console.error('Register form not found in the document');
    }
});