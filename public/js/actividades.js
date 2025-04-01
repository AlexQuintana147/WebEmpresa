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
            
            // Forzar que el modal permanezca cerrado después de la inicialización
            setTimeout(() => {
                this.modalOpen = false;
            }, 100);
        },
        
        openModal(mode, activity = null) {
            // Limpiar errores previos si existen
            document.querySelectorAll('.text-red-500').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
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
            
            // Abrir el modal después de configurarlo
            this.modalOpen = true;
            
            // Usar setTimeout para asegurar que el DOM se ha actualizado
            setTimeout(() => {
                // Actualizar visualización del color e icono seleccionados
                const selectedColor = document.getElementById('selectedColor');
                const selectedIcon = document.getElementById('selectedIcon');
                const colorInput = document.getElementById('color');
                const iconInput = document.getElementById('icono');
                
                if (selectedColor && colorInput) {
                    selectedColor.style.backgroundColor = colorInput.value;
                    
                    // Disparar evento input para actualizar la vista previa
                    const colorEvent = new Event('input');
                    colorInput.dispatchEvent(colorEvent);
                }
                
                if (selectedIcon && iconInput) {
                    selectedIcon.innerHTML = `<i class="fas ${iconInput.value} text-xl"></i>`;
                    
                    // Disparar evento input para actualizar la vista previa
                    const iconEvent = new Event('input');
                    iconInput.dispatchEvent(iconEvent);
                }
                
                // Inicializar selectores de iconos y colores
                initializeIconSelector();
                initializeColorPicker();
                
                // Disparar evento input en el título y descripción para actualizar la vista previa
                const tituloInput = document.getElementById('titulo');
                const descripcionInput = document.getElementById('descripcion');
                
                if (tituloInput) {
                    const tituloEvent = new Event('input');
                    tituloInput.dispatchEvent(tituloEvent);
                }
                
                if (descripcionInput) {
                    descripcionInput.addEventListener('input', function(e) {
                        // Actualizar la vista previa de la descripción
                        const previewDescElement = document.querySelector('[x-text="previewDesc"]');
                        if (previewDescElement && window.Alpine) {
                            window.Alpine.evaluate(previewDescElement, 'previewDesc = "' + e.target.value.replace(/"/g, '\\"') + '"');
                        }
                    });
                    
                    // Disparar evento inicial
                    const descripcionEvent = new Event('input');
                    descripcionInput.dispatchEvent(descripcionEvent);
                }
                
                // Disparar eventos change en los selectores para actualizar la vista previa
                ['nivel', 'estado', 'prioridad'].forEach(id => {
                    const select = document.getElementById(id);
                    if (select) {
                        const event = new Event('change');
                        select.dispatchEvent(event);
                    }
                });
            }, 100);
        },
        
        closeModal() {
            this.modalOpen = false;
            this.currentActivity = null;
            
            // Limpiar errores al cerrar el modal
            document.querySelectorAll('.text-red-500').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
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
    // Asegurarse de que el modal esté cerrado al cargar la página
    if (Alpine.store('actividades')) {
        Alpine.store('actividades').modalOpen = false;
    }
    
    // Manejar envío del formulario de actividad
    const actividadForm = document.getElementById('actividadForm');
    if (actividadForm) {
        actividadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Limpiar errores previos
            document.querySelectorAll('.text-red-500').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
            const formData = new FormData(this);
            const activityId = formData.get('actividad_id');
            
            // Determinar URL y método según si es creación o edición
            let url = '/actividades';
            let method = 'POST';
            
            if (activityId) {
                url = `/actividades/${activityId}`;
                method = 'PUT';
                
                // Para edición, asegurarse de que el método sea PUT
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                formData.append('_method', 'PUT');
            }
            
            // Enviar solicitud AJAX
            fetch(url, {
                method: method === 'PUT' ? 'POST' : method, // Para PUT usamos POST con _method=PUT
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
                        // Limpiar errores previos antes de mostrar nuevos
                        document.querySelectorAll('.text-red-500').forEach(el => el.remove());
                        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
                        
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
    const selectedIcon = document.getElementById('selectedIcon');
    const iconInputContainer = iconInput ? iconInput.parentElement : null;
    
    if (iconSelector && iconInput) {
        // Ocultar el campo de entrada de texto
        if (iconInputContainer) {
            iconInput.style.display = 'none';
        }
        
        // Lista de iconos disponibles (ampliada)
        const icons = [
            'fa-tasks', 'fa-calendar', 'fa-clock', 'fa-star', 'fa-check-circle',
            'fa-list', 'fa-clipboard', 'fa-file', 'fa-folder', 'fa-bookmark',
            'fa-bell', 'fa-flag', 'fa-tag', 'fa-home', 'fa-building',
            'fa-user', 'fa-users', 'fa-briefcase', 'fa-chart-line', 'fa-cog',
            'fa-calendar-check', 'fa-calendar-alt', 'fa-check', 'fa-check-square',
            'fa-clipboard-check', 'fa-clipboard-list', 'fa-edit', 'fa-exclamation-circle',
            'fa-exclamation-triangle', 'fa-lightbulb', 'fa-link', 'fa-map-marker-alt',
            'fa-paperclip', 'fa-pencil-alt', 'fa-project-diagram', 'fa-sticky-note',
            'fa-thumbtack', 'fa-trophy', 'fa-wrench', 'fa-bullseye'
        ];
        
        // Limpiar el selector antes de añadir nuevos iconos
        iconSelector.innerHTML = '';
        
        // Generar botones de iconos
        icons.forEach(icon => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'p-1.5 rounded hover:bg-amber-100 transition-colors flex items-center justify-center';
            button.innerHTML = `<i class="fas ${icon} text-lg"></i>`;
            button.title = icon.replace('fa-', '');
            
            // Añadir clase activa si es el icono seleccionado
            if (iconInput.value === icon) {
                button.classList.add('bg-amber-100', 'ring-1', 'ring-amber-500');
            }
            
            button.addEventListener('click', function() {
                // Quitar clase activa de todos los botones
                iconSelector.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('bg-amber-100', 'ring-1', 'ring-amber-500');
                });
                
                // Añadir clase activa al botón seleccionado
                this.classList.add('bg-amber-100', 'ring-1', 'ring-amber-500');
                
                // Actualizar valor del input
                iconInput.value = icon;
                
                // Actualizar icono seleccionado
                selectedIcon.innerHTML = `<i class="fas ${icon} text-xl"></i>`;
                selectedIcon.classList.add('scale-110', 'ring-1', 'ring-amber-500');
                setTimeout(() => {
                    selectedIcon.classList.remove('scale-110');
                }, 300);
                
                // Actualizar vista previa
                const event = new Event('input');
                iconInput.dispatchEvent(event);
            });
            
            iconSelector.appendChild(button);
        });
    }
}

// Función para inicializar selector de colores
function initializeColorPicker() {
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');
    const selectedColor = document.getElementById('selectedColor');
    const colorInputContainer = colorInput ? colorInput.parentElement : null;
    
    if (colorPicker && colorInput) {
        // Ocultar el campo de entrada de texto
        if (colorInputContainer) {
            colorInput.style.display = 'none';
        }
        
        // Lista de colores predefinidos (con nombres)
        const colors = [
            { hex: '#4A90E2', name: 'Azul' },
            { hex: '#50E3C2', name: 'Turquesa' },
            { hex: '#B8E986', name: 'Verde claro' },
            { hex: '#F8E71C', name: 'Amarillo' },
            { hex: '#F5A623', name: 'Naranja' },
            { hex: '#E74C3C', name: 'Rojo' },
            { hex: '#8E44AD', name: 'Púrpura' },
            { hex: '#3498DB', name: 'Azul claro' },
            { hex: '#1ABC9C', name: 'Verde agua' },
            { hex: '#2ECC71', name: 'Verde' },
            { hex: '#F1C40F', name: 'Amarillo oro' },
            { hex: '#E67E22', name: 'Naranja oscuro' },
            { hex: '#9B59B6', name: 'Violeta' },
            { hex: '#34495E', name: 'Azul marino' },
            { hex: '#16A085', name: 'Verde jade' },
            { hex: '#27AE60', name: 'Verde esmeralda' },
            { hex: '#D35400', name: 'Naranja quemado' },
            { hex: '#C0392B', name: 'Rojo oscuro' },
            { hex: '#7D3C98', name: 'Morado' },
            { hex: '#2980B9', name: 'Azul acero' }
        ];
        
        // Mejorar el aspecto del color seleccionado
        selectedColor.classList.add('ring-1', 'ring-gray-300');
        
        // Limpiar el selector antes de añadir nuevos colores
        colorPicker.innerHTML = '';
        
        // Generar botones de colores
        colors.forEach(color => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-full h-7 rounded transition-all duration-200 flex items-center justify-center';
            button.style.backgroundColor = color.hex;
            button.title = `${color.name} (${color.hex})`;
            
            // Añadir borde destacado si es el color seleccionado
            if (colorInput.value.toLowerCase() === color.hex.toLowerCase()) {
                button.classList.add('ring-1', 'ring-gray-700', 'shadow-sm');
            } else {
                button.classList.add('border-gray-200', 'hover:shadow-sm', 'hover:scale-105');
            }
            
            button.addEventListener('click', function() {
                // Quitar clase activa de todos los botones
                colorPicker.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('ring-1', 'ring-gray-700', 'shadow-sm');
                    btn.classList.add('border-gray-200', 'hover:shadow-sm', 'hover:scale-105');
                });
                
                // Añadir clase activa al botón seleccionado
                this.classList.remove('border-gray-200', 'hover:shadow-sm', 'hover:scale-105');
                this.classList.add('ring-1', 'ring-gray-700', 'shadow-sm');
                
                // Actualizar valor del input
                colorInput.value = color.hex;
                
                // Actualizar color seleccionado con efecto visual
                selectedColor.style.backgroundColor = color.hex;
                selectedColor.classList.add('scale-110', 'ring-1');
                setTimeout(() => {
                    selectedColor.classList.remove('scale-110');
                }, 300);
                
                // Actualizar vista previa
                const event = new Event('input');
                colorInput.dispatchEvent(event);
            });
            
            colorPicker.appendChild(button);
        });
    }
}