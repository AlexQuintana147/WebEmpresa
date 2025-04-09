/**
 * Calendario.js - Gestión de horarios médicos
 * Este archivo maneja la lógica del calendario de horarios para los doctores
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Alpine.js si no está ya inicializado
    if (typeof Alpine === 'undefined') {
        console.error('Alpine.js no está cargado. Asegúrate de incluir la biblioteca.');
        return;
    }

    // Inicializar notificaciones
    Alpine.store('notification', {
        show: false,
        message: '',
        type: 'success', // success, error, warning, info
        timeout: null,

        notify(message, type = 'success', duration = 3000) {
            this.message = message;
            this.type = type;
            this.show = true;

            // Limpiar cualquier timeout existente
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            // Configurar un nuevo timeout para ocultar la notificación
            this.timeout = setTimeout(() => {
                this.show = false;
            }, duration);
        }
    });

    // Funciones de utilidad para el calendario
    window.calendarUtils = {
        // Convertir hora en formato HH:MM a minutos desde medianoche
        timeToMinutes(timeString) {
            if (!timeString) return 0;
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        },

        // Calcular duración en minutos entre dos horas
        getDuration(startTime, endTime) {
            return this.timeToMinutes(endTime) - this.timeToMinutes(startTime);
        },

        // Calcular altura en píxeles basada en la duración
        getHeightFromDuration(duration) {
            // Cada hora representa 60px de altura
            return (duration / 60) * 60;
        },

        // Formatear hora para mostrar
        formatTime(timeString) {
            if (!timeString) return '';
            const [hours, minutes] = timeString.split(':');
            return `${hours}:${minutes}`;
        },

        // Obtener nombre del día a partir del número
        getDayName(dayNumber) {
            const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            return days[dayNumber - 1] || '';
        }
    };

    // Manejar respuestas de la API
    window.handleApiResponse = function(response, successCallback, errorCallback) {
        if (response.success) {
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
            if (response.message) {
                Alpine.store('notification').notify(response.message, 'success');
            }
        } else {
            if (typeof errorCallback === 'function') {
                errorCallback(response);
            }
            if (response.message) {
                Alpine.store('notification').notify(response.message, 'error');
            } else {
                Alpine.store('notification').notify('Ha ocurrido un error inesperado', 'error');
            }
        }
    };

    // Validación de formularios
    window.formValidation = {
        validateTimeRange(startTime, endTime) {
            if (!startTime || !endTime) return false;
            
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            
            return start < end;
        },
        
        validateRequired(value) {
            return value !== null && value !== undefined && value.toString().trim() !== '';
        }
    };
});