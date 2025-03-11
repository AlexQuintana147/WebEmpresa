document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar notificaciones mejoradas
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        const container = document.createElement('div');
        const iconContainer = document.createElement('div');
        const textContainer = document.createElement('div');
        const messageElement = document.createElement('p');
        const icon = document.createElement('i');
        
        // Configurar el icono según el tipo de notificación
        let bgColor = 'bg-blue-500';
        let borderColor = 'border-blue-600';
        let iconClass = 'fa-info-circle';
        
        if (type === 'success') {
            bgColor = 'bg-green-500';
            borderColor = 'border-green-600';
            iconClass = 'fa-check-circle';
        } else if (type === 'error') {
            bgColor = 'bg-blue-500';
            borderColor = 'border-blue-600';
            iconClass = 'fa-exclamation-circle';
        } else if (type === 'warning') {
            bgColor = 'bg-yellow-500';
            borderColor = 'border-yellow-600';
            iconClass = 'fa-exclamation-triangle';
        }
        
        // Configurar el contenedor principal
        notification.className = `fixed top-4 right-4 z-50 transform transition-all duration-300 ease-in-out`;
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        // Configurar el contenedor interno
        container.className = `flex items-center ${bgColor} text-white p-4 rounded-lg shadow-lg border-l-4 ${borderColor} min-w-[300px] max-w-md`;
        
        // Configurar el icono
        iconContainer.className = 'flex-shrink-0 mr-3';
        icon.className = `fas ${iconClass} text-xl`;
        iconContainer.appendChild(icon);
        
        // Configurar el texto
        textContainer.className = 'flex-grow';
        messageElement.className = 'text-sm font-medium';
        messageElement.textContent = message;
        textContainer.appendChild(messageElement);
        
        // Ensamblar la notificación
        container.appendChild(iconContainer);
        container.appendChild(textContainer);
        notification.appendChild(container);
        
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
        }, 4000);
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