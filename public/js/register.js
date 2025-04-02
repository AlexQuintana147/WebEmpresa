document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('#registerForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isSubmitting = false;
    
    const errorMessages = {
        nombre: 'El nombre solo puede contener letras y espacios',
        correo: 'Por favor ingrese un correo válido',
        contrasena: 'La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un carácter especial'
    };

    // Función para mostrar notificaciones
    function showNotification(message, type = 'error') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        notification.style.transform = 'translateX(100%)';

        document.body.appendChild(notification);

        // Animación de entrada
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });

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

    // Función para limpiar todos los errores
    function clearErrors() {
        registerForm.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        registerForm.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }

    if (registerForm && csrfToken) {
        console.log('Register form found and initialized');
        
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
            console.log('Register form submission started');

            if (isSubmitting) return;
            isSubmitting = true;

            // Clear previous error messages
            clearErrors();

            const formData = {
                nombre: document.querySelector('#reg-name').value,
                correo: document.querySelector('#reg-email').value,
                contrasena: document.querySelector('#reg-password').value,
                password_confirmation: document.querySelector('#reg-password-confirm').value
            };
            console.log('Form data collected:', formData);

            // Validar campos obligatorios
            if (!formData.nombre || !formData.correo || !formData.contrasena || !formData.password_confirmation) {
                console.log('Validation failed: Missing required fields');
                showNotification('Por favor complete todos los campos', 'error');
                isSubmitting = false;
                return;
            }

            // Validar correo antes de enviar
            if (!validateEmail(formData.correo)) {
                showFieldError(emailInput, 'Por favor ingrese un correo electrónico válido');
                isSubmitting = false;
                return;
            }

            // Validar que las contraseñas coincidan
            if (formData.contrasena !== formData.password_confirmation) {
                showFieldError(document.querySelector('#reg-password-confirm'), 'Las contraseñas no coinciden');
                isSubmitting = false;
                return;
            }

            try {
                console.log('Sending registration request...');
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData),
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    console.log('Registration successful');
                    showNotification(data.message || 'Registro exitoso', 'success');
                    
                    // Cerrar el modal de registro
                    if (typeof Alpine !== 'undefined') {
                        Alpine.store('modal').open = false;
                    }
                    
                    // Redireccionar después de un breve retraso
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    console.error('Registration failed:', data);
                    isSubmitting = false;
                    
                    if (data.errors) {
                        // Mostrar errores de validación en los campos correspondientes
                        Object.keys(data.errors).forEach(field => {
                            let inputId;
                            switch(field) {
                                case 'nombre':
                                    inputId = '#reg-name';
                                    break;
                                case 'correo':
                                    inputId = '#reg-email';
                                    break;
                                case 'contrasena':
                                    inputId = '#reg-password';
                                    break;
                                case 'password_confirmation':
                                    inputId = '#reg-password-confirm';
                                    break;
                                default:
                                    inputId = null;
                            }
                            
                            if (inputId) {
                                const input = document.querySelector(inputId);
                                if (input) {
                                    showFieldError(input, data.errors[field][0]);
                                }
                            }
                        });
                    } else {
                        showNotification(data.message || 'Error en el registro', 'error');
                    }
                }
            } catch (error) {
                console.error('Error during registration:', error);
                showNotification('Error en el registro: ' + error.message, 'error');
                isSubmitting = false;
            }
        });
    } else {
        console.error('Register form or CSRF token not found in the document');
    }
});