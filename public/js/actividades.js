// Inicializar Alpine.js store para actividades
document.addEventListener('alpine:init', () => {
    Alpine.store('actividades', {
        modalOpen: false,
        modalTitle: 'Nueva Actividad',
        modalMode: 'create',
        currentActivity: null,
        showSubactivities: false,
        currentParentId: null,
        deleteModalOpen: false,
        activityToDelete: null,
        
        init() {
            // Asegurarse de que el modal esté cerrado al inicializar
            this.modalOpen = false;
            
            // Evitar que el modal se abra durante la inicialización
            document.addEventListener('DOMContentLoaded', () => {
                this.modalOpen = false;
            });
            
            // Prevenir cualquier apertura automática del modal
            window.addEventListener('load', () => {
                this.modalOpen = false;
            });
            
            // Forzar el estado cerrado después de cualquier renderizado
            document.addEventListener('alpine:initialized', () => {
                this.modalOpen = false;
            });
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
        
        openDeleteModal(activityId) {
            this.activityToDelete = activityId;
            this.deleteModalOpen = true;
        },
        
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.activityToDelete = null;
        },
        
        deleteActivity(activityId) {
            // Si se llama directamente, abrimos el modal de confirmación
            if (!this.deleteModalOpen) {
                this.openDeleteModal(activityId);
                return;
            }
            
            // Si llegamos aquí, es porque se confirmó la eliminación desde el modal
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
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Asegurarse de que el modal esté cerrado al cargar la página
    if (Alpine.store('actividades')) {
        Alpine.store('actividades').modalOpen = false;
        Alpine.store('actividades').deleteModalOpen = false;
    }
    
    // Añadir el modal de confirmación de eliminación al DOM si no existe
    if (!document.getElementById('deleteActivityModal')) {
        const modalHTML = `
        <div x-show="$store.actividades.deleteModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay de fondo oscuro -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                     x-show="$store.actividades.deleteModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-60"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-60"
                     x-transition:leave-end="opacity-0"
                     @click="$store.actividades.closeDeleteModal()"></div>
                
                <!-- Centrar el modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <!-- Modal -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                     x-show="$store.actividades.deleteModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <!-- Contenido del modal -->
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <i class="text-red-600 fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Eliminar actividad</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar esta actividad? Esta acción no se puede deshacer y eliminará también todas las subactividades asociadas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="$store.actividades.deleteActivity($store.actividades.activityToDelete); $store.actividades.closeDeleteModal()">
                            Eliminar
                        </button>
                        <button type="button" 
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="$store.actividades.closeDeleteModal()">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Crear un contenedor para el modal y añadirlo al body
        const modalContainer = document.createElement('div');
        modalContainer.id = 'deleteActivityModal';
        modalContainer.innerHTML = modalHTML;
        document.body.appendChild(modalContainer);
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