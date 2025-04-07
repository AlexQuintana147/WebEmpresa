document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const btnAgregarHorario = document.getElementById('btn-agregar-horario');
    const modalHorario = document.getElementById('modal-horario');
    const modalTitle = document.getElementById('modal-title');
    const btnCerrarModal = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar');
    const formHorario = document.getElementById('form-horario');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const btnCancelarEliminar = document.getElementById('btn-cancelar-eliminar');
    const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');
    
    // Variables para almacenar el estado
    let modoEdicion = false;
    let tareaIdEliminar = null;
    
    // Verificar si hay mensajes de notificación
    const successMsg = document.querySelector('meta[name="success-message"]');
    const errorMsg = document.querySelector('meta[name="error-message"]');
    
    if (successMsg) {
        mostrarNotificacion(successMsg.getAttribute('content'), 'success');
    } else if (errorMsg) {
        mostrarNotificacion(errorMsg.getAttribute('content'), 'error');
    }
    
    // Event Listeners
    btnAgregarHorario.addEventListener('click', abrirModalAgregar);
    btnCerrarModal.addEventListener('click', cerrarModal);
    btnCancelar.addEventListener('click', cerrarModal);
    formHorario.addEventListener('submit', guardarHorario);
    btnCancelarEliminar.addEventListener('click', cerrarModalConfirmar);
    btnConfirmarEliminar.addEventListener('click', eliminarHorario);
    
    // Agregar event listeners a los horarios existentes
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', function(e) {
            e.stopPropagation();
            abrirModalEditar(this);
        });
    });
    
    // Funciones
    function abrirModalAgregar() {
        modoEdicion = false;
        modalTitle.textContent = 'Agregar Horario';
        formHorario.reset();
        document.getElementById('tarea-id').value = '';
        modalHorario.classList.remove('hidden');
    }
    
    function abrirModalEditar(elemento) {
        modoEdicion = true;
        modalTitle.textContent = 'Editar Horario';
        
        // Obtener datos del horario
        const id = elemento.dataset.id;
        const titulo = elemento.dataset.titulo;
        const descripcion = elemento.dataset.descripcion;
        const dia = elemento.dataset.dia;
        const inicio = elemento.dataset.inicio;
        const fin = elemento.dataset.fin;
        const color = elemento.dataset.color;
        const icono = elemento.dataset.icono;
        
        // Llenar el formulario
        document.getElementById('tarea-id').value = id;
        document.getElementById('titulo').value = titulo;
        document.getElementById('descripcion').value = descripcion;
        document.getElementById('dia_semana').value = dia;
        document.getElementById('hora_inicio').value = inicio;
        document.getElementById('hora_fin').value = fin;
        document.getElementById('color').value = color;
        document.getElementById('icono').value = icono;
        
        // Mostrar botón de eliminar
        const btnEliminar = document.getElementById('btn-eliminar');
        if (!btnEliminar) {
            const btnGuardar = document.getElementById('btn-guardar');
            btnGuardar.insertAdjacentHTML('beforebegin', `
                <button type="button" id="btn-eliminar" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            `);
            
            document.getElementById('btn-eliminar').addEventListener('click', function() {
                tareaIdEliminar = id;
                cerrarModal();
                abrirModalConfirmar();
            });
        }
        
        modalHorario.classList.remove('hidden');
    }
    
    function cerrarModal() {
        modalHorario.classList.add('hidden');
        const btnEliminar = document.getElementById('btn-eliminar');
        if (btnEliminar) {
            btnEliminar.remove();
        }
    }
    
    function abrirModalConfirmar() {
        modalConfirmar.classList.remove('hidden');
    }
    
    function cerrarModalConfirmar() {
        modalConfirmar.classList.add('hidden');
    }
    
    function guardarHorario(e) {
        e.preventDefault();
        
        const formData = new FormData(formHorario);
        const tareaId = document.getElementById('tarea-id').value;
        
        let url, method;
        if (modoEdicion && tareaId) {
            url = `/tareas/${tareaId}`;
            method = 'POST';
            // Agregar método PUT para Laravel
            formData.append('_method', 'PUT');
        } else {
            url = '/tareas';
            method = 'POST';
        }
        
        // Asegurarse de que el token CSRF esté incluido en el FormData
        if (!formData.has('_token')) {
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        }
        
        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
                // No incluir Content-Type cuando se usa FormData
                // El navegador lo configurará automáticamente
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion(data.message || 'Horario guardado correctamente', 'success');
                cerrarModal();
                // Recargar la página para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarNotificacion(data.message || 'Error al guardar el horario', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Ocurrió un error al procesar la solicitud', 'error');
        });
    }
    
    function eliminarHorario() {
        if (!tareaIdEliminar) return;
        
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        fetch(`/tareas/${tareaIdEliminar}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
                // No incluir Content-Type cuando se usa FormData
                // El token CSRF ya está incluido en el formData con @csrf en el formulario
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion(data.message || 'Horario eliminado correctamente', 'success');
                cerrarModalConfirmar();
                // Recargar la página para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarNotificacion(data.message || 'Error al eliminar el horario', 'error');
                cerrarModalConfirmar();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Ocurrió un error al procesar la solicitud', 'error');
            cerrarModalConfirmar();
        });
    }
    
    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo) {
        // Crear el contenedor de notificación si no existe
        let notificationContainer = document.getElementById('notification_container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification_container';
            notificationContainer.className = 'fixed top-4 right-4 z-50 max-w-md';
            document.body.appendChild(notificationContainer);
        }
        
        // Crear la notificación
        const notification = document.createElement('div');
        notification.className = `rounded-lg shadow-lg p-4 mb-4 flex items-center justify-between ${
            tipo === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 
            'bg-red-100 border-l-4 border-red-500 text-red-700'
        }`;
        
        // Icono según el tipo
        const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        // Contenido de la notificación
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icon} mr-3 text-xl"></i>
                <p>${mensaje}</p>
            </div>
            <button class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Agregar la notificación al contenedor
        notificationContainer.appendChild(notification);
        
        // Eliminar la notificación después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
});