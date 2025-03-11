document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar notificaciones mejoradas
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        
        // Determinar el color de fondo según el tipo de notificación
        let bgColor = 'bg-blue-500'; // Color azul por defecto para info/warning
        if (type === 'success') {
            bgColor = 'bg-green-500';
        } else if (type === 'error') {
            bgColor = 'bg-blue-500'; // Cambiado de rojo a azul según requerimiento
        }
        
        // Aplicar estilos a la notificación
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out ${bgColor} text-white`;
        notification.textContent = message;
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        // Añadir al DOM
        document.body.appendChild(notification);
        
        // Forzar un reflow para que la animación funcione
        void notification.offsetWidth;
        
        // Animar entrada
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
        
        // Configurar temporizador para ocultar la notificación
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            
            // Eliminar del DOM después de la animación
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    };
    
    // Verificar si hay mensajes de éxito en la sesión y mostrarlos
    const successMessage = document.querySelector('meta[name="success-message"]')?.getAttribute('content');
    if (successMessage) {
        showNotification(successMessage, 'success');
    }
    
    // Verificar si hay mensajes de error en la sesión y mostrarlos
    const errorMessage = document.querySelector('meta[name="error-message"]')?.getAttribute('content');
    if (errorMessage) {
        showNotification(errorMessage, 'error');
    }
    
    // Interceptar mensajes de alerta nativos para mostrarlos con nuestro sistema de notificaciones
    const originalAlert = window.alert;
    window.alert = function(message) {
        showNotification(message, 'info');
    };
});