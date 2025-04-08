document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const modalHorario = document.getElementById('modal-horario');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const formHorario = document.getElementById('form-horario');
    const btnCerrarModal = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar');
    const btnCancelarEliminar = document.getElementById('btn-cancelar-eliminar');
    const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');
    const modalTitle = document.getElementById('modal-title');
    const notificationContainer = document.getElementById('notification_container');
    
    // Variables globales
    let tareaIdToDelete = null;
    let isEditing = false;
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type} p-4 mb-4 rounded-lg shadow-md`;
        notification.style.backgroundColor = type === 'success' ? '#10B981' : '#EF4444';
        notification.style.color = 'white';
        notification.innerHTML = message;
        
        notificationContainer.appendChild(notification);
        
        // Eliminar la notificación después de 3 segundos
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                notificationContainer.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Función para abrir el modal de agregar horario
    function openAddModal(dayNumber) {
        isEditing = false;
        modalTitle.textContent = 'Agregar Horario';
        formHorario.reset();
        document.getElementById('tarea-id').value = '';
        document.getElementById('dia_semana').value = dayNumber;
        modalHorario.classList.remove('hidden');
    }
    
    // Función para abrir el modal de editar horario
    function openEditModal(tareaId) {
        isEditing = true;
        modalTitle.textContent = 'Editar Horario';
        
        // Obtener los datos de la tarea desde el elemento HTML
        const tareaElement = document.querySelector(`.time-slot[data-id="${tareaId}"]`);
        if (!tareaElement) return;
        
        document.getElementById('tarea-id').value = tareaId;
        document.getElementById('titulo').value = tareaElement.getAttribute('data-titulo');
        document.getElementById('descripcion').value = tareaElement.getAttribute('data-descripcion');
        document.getElementById('dia_semana').value = tareaElement.getAttribute('data-dia');
        document.getElementById('hora_inicio').value = tareaElement.getAttribute('data-inicio');
        document.getElementById('hora_fin').value = tareaElement.getAttribute('data-fin');
        document.getElementById('color').value = tareaElement.getAttribute('data-color');
        document.getElementById('icono').value = tareaElement.getAttribute('data-icono');
        
        modalHorario.classList.remove('hidden');
    }
    
    // Función para abrir el modal de confirmación de eliminación
    function openDeleteConfirmModal(tareaId) {
        tareaIdToDelete = tareaId;
        modalConfirmar.classList.remove('hidden');
    }
    
    // Función para cerrar los modales
    function closeModals() {
        modalHorario.classList.add('hidden');
        modalConfirmar.classList.add('hidden');
    }
    
    // Event Listeners para abrir modales
    document.querySelectorAll('.day-column').forEach(column => {
        column.addEventListener('dblclick', function(e) {
            // Solo abrir el modal si se hace doble clic directamente en la columna, no en una tarea
            if (e.target === this || e.target.classList.contains('day-column')) {
                const dayNumber = this.getAttribute('data-day');
                openAddModal(dayNumber);
            }
        });
    });
    
    // Event Listeners para las tareas existentes
    document.querySelectorAll('.time-slot').forEach(slot => {
        // Abrir modal de edición al hacer clic en una tarea
        slot.addEventListener('click', function() {
            const tareaId = this.getAttribute('data-id');
            openEditModal(tareaId);
        });
        
        // Agregar opción para eliminar (podría ser con clic derecho o un botón específico)
        slot.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            const tareaId = this.getAttribute('data-id');
            openDeleteConfirmModal(tareaId);
        });
    });
    
    // Event Listeners para cerrar modales
    btnCerrarModal.addEventListener('click', closeModals);
    btnCancelar.addEventListener('click', closeModals);
    btnCancelarEliminar.addEventListener('click', closeModals);
    
    // Event Listener para el formulario de guardar/editar tarea
    formHorario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tareaId = document.getElementById('tarea-id').value;
        const formData = new FormData(formHorario);
        
        // URL y método según si estamos creando o editando
        let url = '/tareas';
        let method = 'POST';
        
        if (isEditing && tareaId) {
            url = `/tareas/${tareaId}`;
            formData.append('_method', 'PUT'); // Laravel method spoofing
        }
        
        // Enviar solicitud AJAX
        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModals();
                // Recargar la página para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.error || 'Ha ocurrido un error', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ha ocurrido un error en la solicitud', 'error');
        });
    });
    
    // Event Listener para eliminar tarea
    btnConfirmarEliminar.addEventListener('click', function() {
        if (!tareaIdToDelete) return;
        
        fetch(`/tareas/${tareaIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModals();
                // Recargar la página para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.error || 'Ha ocurrido un error', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ha ocurrido un error en la solicitud', 'error');
        });
    });
    
    // Estilos CSS para las notificaciones
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            transition: opacity 0.3s ease-in-out;
        }
        .fade-out {
            opacity: 0;
        }
    `;
    document.head.appendChild(style);
});