// Inicializar Alpine.js store para actividades
document.addEventListener('alpine:init', () => {
    Alpine.store('actividades', {
        modalOpen: false,
        modalTitle: 'Nueva Actividad',
        modalMode: 'create',
        currentActivity: null,
        showSubactivities: false,
        currentParentId: null,
        
        init() {
            // Asegurarse de que el modal esté cerrado al inicializar
            this.modalOpen = false;
        },
        
        openModal(mode, activity = null) {
            this.modalMode = mode;
            this.modalTitle = mode === 'create' ? 'Nueva Actividad' : 'Editar Actividad';
            this.currentActivity = activity;
            
            if (mode === 'create') {
                // Limpiar formulario
                document.getElementById('actividadForm').reset();
                document.getElementById('actividad_id').value = '';
                document.getElementById('actividad_padre_id').value = this.currentParentId || '';
                
                // Establecer valores predeterminados
                document.getElementById('color').value = '#4A90E2';
                document.getElementById('icono').value = 'fa-tasks';
                document.getElementById('nivel').value = this.currentParentId ? 'secundaria' : 'principal';
                document.getElementById('estado').value = 'pendiente';
                document.getElementById('prioridad').value = '1';
                
                // Establecer fecha límite predeterminada (hoy + 1 día)
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('fecha_limite').value = tomorrow.toISOString().split('T')[0];
                document.getElementById('hora_limite').value = '12:00';
            } else if (mode === 'edit' && activity) {
                // Rellenar formulario con datos de la actividad
                document.getElementById('actividad_id').value = activity.id;
                document.getElementById('titulo').value = activity.titulo;
                document.getElementById('descripcion').value = activity.descripcion || '';
                document.getElementById('nivel').value = activity.nivel;
                document.getElementById('estado').value = activity.estado;
                document.getElementById('fecha_limite').value = activity.fecha_limite;
                document.getElementById('hora_limite').value = activity.hora_limite;
                document.getElementById('color').value = activity.color;
                document.getElementById('icono').value = activity.icono;
                document.getElementById('prioridad').value = activity.prioridad;
                document.getElementById('actividad_padre_id').value = activity.actividad_padre_id || '';
            }
            
            this.modalOpen = true;
        },
        
        closeModal() {
            this.modalOpen = false;
            this.currentActivity = null;
        },
        
        toggleSubactivities(parentId) {
            const subactivitiesContainer = document.getElementById(`subactivities-${parentId}`);
            if (subactivitiesContainer) {
                const isHidden = subactivitiesContainer.classList.contains('hidden');
                if (isHidden) {
                    subactivitiesContainer.classList.remove('hidden');
                    document.getElementById(`toggle-icon-${parentId}`).classList.replace('fa-chevron-down', 'fa-chevron-up');
                } else {
                    subactivitiesContainer.classList.add('hidden');
                    document.getElementById(`toggle-icon-${parentId}`).classList.replace('fa-chevron-up', 'fa-chevron-down');
                }
            }
        },
        
        addSubactivity(parentId) {
            this.currentParentId = parentId;
            this.openModal('create');
        },
        
        changeStatus(activityId, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/actividades/${activityId}/cambiar-estado`;
            form.style.display = 'none';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PUT';
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = csrfToken;
            
            const statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'estado';
            statusField.value = newStatus;
            
            form.appendChild(methodField);
            form.appendChild(csrfField);
            form.appendChild(statusField);
            
            document.body.appendChild(form);
            form.submit();
        },
        
        deleteActivity(activityId) {
            if (confirm('¿Estás seguro de que deseas eliminar esta actividad? Esta acción no se puede deshacer.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/actividades/${activityId}`;
                form.style.display = 'none';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = csrfToken;
                
                form.appendChild(methodField);
                form.appendChild(csrfField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Manejar envío del formulario de actividad
    const actividadForm = document.getElementById('actividadForm');
    if (actividadForm) {
        actividadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const activityId = formData.get('actividad_id');
            
            // Determinar URL y método según si es creación o edición
            let url = '/actividades';
            let method = 'POST';
            
            if (activityId) {
                url = `/actividades/${activityId}`;
                method = 'PUT';
            }
            
            // Enviar solicitud AJAX
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar modal y recargar página para mostrar cambios
                    Alpine.store('actividades').closeModal();
                    window.location.reload();
                } else {
                    // Mostrar errores
                    console.error('Error:', data.message);
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = document.getElementById(key);
                            if (input) {
                                input.classList.add('border-red-500');
                                const errorElement = document.createElement('p');
                                errorElement.classList.add('text-red-500', 'text-xs', 'mt-1');
                                errorElement.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorElement);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    // Inicializar selectores de iconos y colores si existen
    initializeIconSelector();
    initializeColorPicker();
});

// Función para inicializar selector de iconos
function initializeIconSelector() {
    const iconSelector = document.getElementById('iconSelector');
    const iconInput = document.getElementById('icono');
    
    if (iconSelector && iconInput) {
        // Lista de iconos disponibles
        const icons = [
            'fa-tasks', 'fa-calendar', 'fa-clock', 'fa-star', 'fa-check-circle',
            'fa-list', 'fa-clipboard', 'fa-file', 'fa-folder', 'fa-bookmark',
            'fa-bell', 'fa-flag', 'fa-tag', 'fa-home', 'fa-building',
            'fa-user', 'fa-users', 'fa-briefcase', 'fa-chart-line', 'fa-cog'
        ];
        
        // Generar botones de iconos
        icons.forEach(icon => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'p-2 rounded-lg hover:bg-gray-100 transition-colors';
            button.innerHTML = `<i class="fas ${icon} text-xl"></i>`;
            button.addEventListener('click', function() {
                iconInput.value = icon;
                // Actualizar icono seleccionado
                document.getElementById('selectedIcon').innerHTML = `<i class="fas ${icon} text-xl"></i>`;
            });
            iconSelector.appendChild(button);
        });
    }
}

// Función para inicializar selector de colores
function initializeColorPicker() {
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');
    
    if (colorPicker && colorInput) {
        // Lista de colores predefinidos
        const colors = [
            '#4A90E2', '#50E3C2', '#B8E986', '#F8E71C', '#F5A623',
            '#E74C3C', '#8E44AD', '#3498DB', '#1ABC9C', '#2ECC71',
            '#F1C40F', '#E67E22', '#E74C3C', '#ECF0F1', '#95A5A6',
            '#34495E', '#2C3E50', '#7F8C8D', '#BDC3C7', '#000000'
        ];
        
        // Generar botones de colores
        colors.forEach(color => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-8 h-8 rounded-full border border-gray-300 m-1';
            button.style.backgroundColor = color;
            button.addEventListener('click', function() {
                colorInput.value = color;
                // Actualizar color seleccionado
                document.getElementById('selectedColor').style.backgroundColor = color;
            });
            colorPicker.appendChild(button);
        });
    }
}