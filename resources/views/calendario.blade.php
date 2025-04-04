<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session('success'))
    <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if(session('error'))
    <meta name="error-message" content="{{ session('error') }}">
    @endif
    <title>Calendario Médico - Clínica Ricardo Palma</title>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Animaciones mejoradas */
        @keyframes pulse-medical {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        @keyframes float-medical {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes glow-medical {
            0%, 100% { box-shadow: 0 0 5px rgba(8, 145, 178, 0.2); }
            50% { box-shadow: 0 0 15px rgba(8, 145, 178, 0.4); }
        }
        
        /* Clases de animación */
        .animate-pulse-medical {
            animation: pulse-medical 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        .animate-float-medical {
            animation: float-medical 4s ease-in-out infinite;
        }
        
        .animate-glow-medical {
            animation: glow-medical 3s ease-in-out infinite;
        }
        
        /* Gradientes y colores */
        .medical-gradient {
            background-image: linear-gradient(135deg, #0891b2, #0e7490);
        }
        
        .medical-gradient-light {
            background-image: linear-gradient(135deg, #e0f2fe, #bae6fd);
        }
        
        /* Tarjetas y elementos UI */
        .medical-card {
            transition: all 0.3s ease;
            border-left: 4px solid #0891b2;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .medical-card:hover {
            box-shadow: 0 10px 15px -3px rgba(8, 145, 178, 0.2), 0 4px 6px -2px rgba(8, 145, 178, 0.1);
            transform: translateY(-2px);
        }
        
        /* Estilos para el calendario */
        .calendar-cell {
            transition: all 0.2s ease;
        }
        
        .calendar-cell:hover {
            background-color: #f0f9ff;
        }
        
        .calendar-header {
            background: linear-gradient(to right, #0891b2, #0e7490);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        
        /* Tipos de citas */
        .appointment-urgent {
            border-left: 4px solid #ef4444;
        }
        
        .appointment-regular {
            border-left: 4px solid #3b82f6;
        }
        
        .appointment-checkup {
            border-left: 4px solid #10b981;
        }
        
        /* Tooltip personalizado */
        .medical-tooltip {
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e7eb;
            border-left: 4px solid #0891b2;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Inicialización global de Alpine.js
        // Definir tareas como variable global para que esté disponible en todos los componentes Alpine
        window.tareas = window.tareas || [];
        
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initialized');
            Alpine.store('modal', {
                open: false,
                editOpen: false,
                deleteOpen: false
            });
            
            // Store para manejar la edición de tareas
            Alpine.store('editTask', {
                selectedTask: null,
                isEditing: false,
                
                // Método para seleccionar una tarea para editar
                selectTask(task) {
                    this.selectedTask = JSON.parse(JSON.stringify(task)); // Clonar la tarea para evitar modificar la original
                    this.isEditing = true;
                },
                
                // Método para cancelar la edición
                cancelEdit() {
                    this.selectedTask = null;
                    this.isEditing = false;
                },
                
                // Método para guardar los cambios
                saveChanges() {
                    // Verificar que selectedTask no sea null antes de continuar
                    if (!this.selectedTask) {
                        console.error('Error: No hay tarea seleccionada para guardar');
                        return;
                    }
                    
                    try {
                        // Crear un formulario para enviar los datos
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/tareas/${this.selectedTask.id}`;
                        form.style.display = 'none';
                        
                        // Método PUT para actualizar
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        form.appendChild(methodInput);
                        
                        // Token CSRF
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        form.appendChild(csrfInput);
                        
                        // Campos a actualizar
                        const fields = ['titulo', 'descripcion', 'color', 'hora_inicio', 'hora_fin', 'dia_semana'];
                        fields.forEach(field => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = field;
                            // Verificar que el campo existe en selectedTask antes de acceder a él
                            input.value = (this.selectedTask && this.selectedTask[field] !== undefined) 
                                ? this.selectedTask[field] || '' 
                                : ''; // Añadir valor por defecto vacío si es null o undefined
                            form.appendChild(input);
                        });
                        
                        // Añadir el formulario al documento y enviarlo
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error al guardar los cambios:', error);
                    }
                }
            });
            // Store para manejar la eliminación de tareas
            Alpine.store('deleteTask', {
                selectedTask: null,
                
                // Método para seleccionar una tarea para eliminar
                selectTask(task) {
                    this.selectedTask = JSON.parse(JSON.stringify(task)); // Clonar la tarea para evitar modificar la original
                },
                
                // Método para cancelar la eliminación
                cancelDelete() {
                    this.selectedTask = null;
                },
                
                // Método para eliminar la tarea
                deleteTask() {
                    // Verificar que selectedTask no sea null antes de continuar
                    if (!this.selectedTask) {
                        console.error('Error: No hay tarea seleccionada para eliminar');
                        return;
                    }
                    
                    try {
                        // Crear un formulario para enviar los datos
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/tareas/${this.selectedTask.id}`;
                        form.style.display = 'none';
                        
                        // Método DELETE para eliminar
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        // Token CSRF
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        form.appendChild(csrfInput);
                        
                        // Añadir el formulario al documento y enviarlo
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error al eliminar la tarea:', error);
                    }
                }
            });
            
            console.log('Modal store initialized:', Alpine.store('modal'));
        });

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();
            let currentWeekStart = new Date();
            let currentWeekEnd = new Date();
            let selectedDate = new Date();
            
            // Definir cargarTareas en el ámbito global para que esté disponible en toda la página
            window.cargarTareas = function() {
                fetch('{{ route("tareas.json") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Asignar a la variable global window.tareas
                        window.tareas = data || [];
                        renderizarTareas();
                    })
                    .catch(error => console.error('Error cargando tareas:', error));
            };
            
            // Función para cargar las tareas (mantener para compatibilidad)
            function cargarTareas() {
                window.cargarTareas();
            }

            function updateCalendar() {
                const firstDay = new Date(currentYear, currentMonth, 1);
                const lastDay = new Date(currentYear, currentMonth + 1, 0);
                const prevMonthLastDay = new Date(currentYear, currentMonth, 0);
                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                // Actualizar título del mes
                document.querySelector('h2.text-xl.font-semibold').textContent = `${monthNames[currentMonth]} ${currentYear}`;

                const calendarGrid = document.querySelector('.grid.grid-cols-7.gap-2:not(.mb-4)');
                calendarGrid.innerHTML = '';

                // Días del mes anterior
                for (let i = firstDay.getDay() - 1; i >= 0; i--) {
                    const day = prevMonthLastDay.getDate() - i;
                    calendarGrid.innerHTML += `<div class="p-2 text-center text-gray-400">${day}</div>`;
                }

                // Días del mes actual
                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const isToday = day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
                    calendarGrid.innerHTML += `
                        <div class="relative p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer ${isToday ? 'bg-blue-100' : ''}">
                            ${day}
                            ${isToday ? '<div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>' : ''}
                        </div>`;
                }

                // Días del próximo mes
                const remainingDays = 42 - (firstDay.getDay() + lastDay.getDate());
                for (let day = 1; day <= remainingDays; day++) {
                    calendarGrid.innerHTML += `<div class="p-2 text-center text-gray-400">${day}</div>`;
                }
            }

            // Configurar botones de navegación
            //const prevButton = document.querySelector('.fa-chevron-left').parentElement;
            //const nextButton = document.querySelector('.fa-chevron-right').parentElement;


            // Función para obtener el primer día de la semana (lunes)
            function getMonday(d) {
                const day = d.getDay();
                const diff = d.getDate() - day + (day === 0 ? -6 : 1); // ajuste cuando es domingo
                return new Date(d.setDate(diff));
            }

            // Función para formatear fecha como DD Mes
            function formatDate(date) {
                const day = date.getDate();
                const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                return day + ' ' + monthNames[date.getMonth()];
            }

            // Función para actualizar la vista semanal
            function updateWeeklyView() {
                // Actualizar el título de la semana
                const weeklyHeader = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 h2.text-xl.font-semibold');
                if (weeklyHeader) {
                    weeklyHeader.textContent = 'Vista Semanal';
                }
                
                // Resaltar el día actual en la vista semanal
                const weekDays = document.querySelectorAll('.grid.grid-cols-8.gap-2.border-b.pb-2.mb-2 div:not(:first-child)');
                
                // Quitar resaltado de todos los días
                weekDays.forEach(day => {
                    day.classList.remove('bg-blue-100', 'text-blue-800', 'font-bold');
                });
                
                // Resaltar el día actual
                const todayDayOfWeek = today.getDay() || 7; // 0 es domingo, lo convertimos a 7
                if (weekDays[todayDayOfWeek - 1]) {
                    weekDays[todayDayOfWeek - 1].classList.add('bg-blue-100', 'text-blue-800', 'font-bold');
                }
            }
            
            // Eliminar los event listeners de navegación semanal
            const weeklyPrevButton = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 .fa-chevron-left')?.parentElement;
            const weeklyNextButton = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 .fa-chevron-right')?.parentElement;
            
            if (weeklyPrevButton) weeklyPrevButton.style.display = 'none';
            if (weeklyNextButton) weeklyNextButton.style.display = 'none';
            
            // Función para renderizar las tareas en la vista semanal
            function renderizarTareas() {
                console.log('Iniciando renderizado de tareas...');
                // Limpiar todas las celdas primero
                const cells = document.querySelectorAll('#weekly-calendar-table td[data-day]');
                console.log(`Número total de celdas encontradas: ${cells.length}`);
                cells.forEach((cell) => {
                    cell.innerHTML = '';
                });
                
                // Crear un elemento para el tooltip si no existe
                let tooltip = document.getElementById('task-tooltip');
                if (!tooltip) {
                    tooltip = document.createElement('div');
                    tooltip.id = 'task-tooltip';
                    tooltip.className = 'fixed hidden z-50 p-4 bg-white text-gray-800 text-sm rounded-lg shadow-xl max-w-xs border-l-4 border border-gray-200 transition-all duration-300 ease-in-out';
                    tooltip.style.minWidth = '220px';
                    tooltip.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                    document.body.appendChild(tooltip);
                }
                
                // Renderizar cada tarea
                tareas.forEach((tarea, index) => {
                    console.log(`\nProcesando tarea ${index + 1}:`, {
                        titulo: tarea.titulo,
                        dia_semana: tarea.dia_semana,
                        hora_inicio: tarea.hora_inicio,
                        hora_fin: tarea.hora_fin
                    });
                
                    // Obtener el día de la semana (1-7)
                    const diaSemana = parseInt(tarea.dia_semana);
                    
                    console.log(`Día de la semana: ${diaSemana}`);
                    
                    if (diaSemana < 1 || diaSemana > 7) {
                        console.error(`Error: Valor de día inválido: ${diaSemana}`);
                        return;
                    }
                    
                    // Obtener la hora de inicio y fin para determinar las celdas
                    const startHour = tarea.hora_inicio.substring(0, 5);
                    const endHour = tarea.hora_fin.substring(0, 5);
                    
                    // Convertir horas a valores numéricos para cálculos
                    const startHourParts = startHour.split(':');
                    const endHourParts = endHour.split(':');
                    const startHourValue = parseInt(startHourParts[0]);
                    const endHourValue = parseInt(endHourParts[0]);
                    
                    // Horas disponibles en la tabla
                    const availableHours = [8, 10, 12, 14, 16, 18];
                    
                    // Encontrar la hora de inicio más cercana en la tabla
                    let startNearestHour;
                    if (availableHours.includes(startHourValue)) {
                        startNearestHour = startHourValue;
                    } else {
                        // Encontrar la hora más cercana por abajo
                        const lowerHours = availableHours.filter(h => h <= startHourValue);
                        const higherHours = availableHours.filter(h => h >= startHourValue);
                        
                        if (lowerHours.length === 0) {
                            startNearestHour = Math.min(...higherHours);
                        } else if (higherHours.length === 0) {
                            startNearestHour = Math.max(...lowerHours);
                        } else {
                            const maxLower = Math.max(...lowerHours);
                            const minHigher = Math.min(...higherHours);
                            
                            // Elegir la hora más cercana
                            startNearestHour = (startHourValue - maxLower) <= (minHigher - startHourValue) ? maxLower : minHigher;
                        }
                    }
                    
                    // Encontrar la hora de fin más cercana en la tabla (pero debe ser >= que la hora de inicio)
                    let endNearestHour;
                    // Filtrar horas disponibles que sean mayores o iguales a la hora de inicio
                    const validEndHours = availableHours.filter(h => h >= startNearestHour);
                    
                    if (availableHours.includes(endHourValue) && endHourValue >= startNearestHour) {
                        endNearestHour = endHourValue;
                    } else {
                        // Si la hora de fin es menor que la hora de inicio en la tabla, usar la siguiente hora disponible
                        if (endHourValue <= startNearestHour) {
                            const nextHours = availableHours.filter(h => h > startNearestHour);
                            endNearestHour = nextHours.length > 0 ? Math.min(...nextHours) : availableHours[availableHours.length - 1];
                        } else {
                            // Encontrar la hora más cercana por arriba para el fin
                            const lowerHours = availableHours.filter(h => h <= endHourValue && h >= startNearestHour);
                            const higherHours = availableHours.filter(h => h >= endHourValue);
                            
                            if (lowerHours.length === 0) {
                                // Si no hay horas menores, usar la primera hora mayor
                                endNearestHour = higherHours.length > 0 ? Math.min(...higherHours) : availableHours[availableHours.length - 1];
                            } else {
                                // Usar la hora más cercana por abajo que sea >= a la hora de inicio
                                endNearestHour = Math.max(...lowerHours);
                                
                                // Si la hora de fin es significativamente mayor que la última hora disponible en lowerHours,
                                // extender hasta la siguiente hora disponible
                                if (endHourValue > endNearestHour + 1 && higherHours.length > 0) {
                                    endNearestHour = Math.min(...higherHours);
                                }
                            }
                        }
                    }
                    
                    // Si después de todos los cálculos, la hora de fin es igual a la de inicio, intentar extender a la siguiente hora disponible
                    if (endNearestHour === startNearestHour) {
                        const nextHours = availableHours.filter(h => h > startNearestHour);
                        if (nextHours.length > 0) {
                            endNearestHour = Math.min(...nextHours);
                        }
                    }
                    
                    console.log(`Hora de inicio ajustada: ${startNearestHour}:00, Hora de fin ajustada: ${endNearestHour}:00`);
                    
                    // Crear un contenedor principal para la tarea que se extenderá verticalmente
                    const taskContainer = document.createElement('div');
                    taskContainer.className = 'task-container absolute inset-x-0 rounded-md overflow-hidden z-10 medical-card animate-float-medical';
                    taskContainer.style.backgroundColor = tarea.color || '#3B82F6';
                    taskContainer.style.color = 'white';
                    taskContainer.style.top = '0';
                    taskContainer.style.bottom = '0';
                    taskContainer.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
                    taskContainer.style.transition = 'all 0.3s ease';
                    
                    // Contenido de la tarea
                    taskContainer.innerHTML = `
                        <div class="p-2 h-full flex flex-col justify-between">
                            <div class="flex items-center mb-1">
                                <i class="fas ${tarea.icono || 'fa-calendar'} mr-2 text-white"></i>
                                <span class="font-semibold truncate text-white">${tarea.titulo}</span>
                            </div>
                            <div class="text-xs bg-white/20 rounded px-2 py-1 inline-flex items-center self-start">
                                <i class="far fa-clock mr-1"></i>
                                ${tarea.hora_inicio.substring(0, 5)} - ${tarea.hora_fin.substring(0, 5)}
                            </div>
                        </div>
                    `;
                    
                    // Añadir efecto hover
                    taskContainer.addEventListener('mouseenter', () => {
                        taskContainer.style.transform = 'translateY(-3px)';
                        taskContainer.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
                    });
                    
                    taskContainer.addEventListener('mouseleave', () => {
                        taskContainer.style.transform = '';
                        taskContainer.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
                    });
                    
                    // Buscar la celda de inicio
                    const startCell = document.querySelector(`#weekly-calendar-table td[data-day="${diaSemana}"][data-hour="${startNearestHour}:00"]`);
                    
                    if (!startCell) {
                        console.error(`Error: No se encontró celda para día ${diaSemana} y hora ${startNearestHour}:00`);
                        return;
                    }
                    
                    // Determinar las celdas que abarcará la tarea
                    let currentHour = startNearestHour;
                    const cellsToSpan = [];
                    
                    while (currentHour <= endNearestHour) {
                        const cell = document.querySelector(`#weekly-calendar-table td[data-day="${diaSemana}"][data-hour="${currentHour}:00"]`);
                        if (cell) {
                            cellsToSpan.push(cell);
                        }
                        
                        // Avanzar a la siguiente hora disponible
                        const nextHours = availableHours.filter(h => h > currentHour);
                        if (nextHours.length > 0) {
                            currentHour = Math.min(...nextHours);
                        } else {
                            break; // Salir si no hay más horas disponibles
                        }
                    }
                    
                    console.log(`La tarea abarcará ${cellsToSpan.length} celdas`);
                    
                    if (cellsToSpan.length === 0) {
                        console.error('Error: No se encontraron celdas para abarcar');
                        return;
                    }
                    
                    // Si solo hay una celda, simplemente añadir la tarea a esa celda
                    if (cellsToSpan.length === 1) {
                        const cell = cellsToSpan[0];
                        cell.style.position = 'relative';
                        cell.appendChild(taskContainer);
                    } else {
                        // Si hay múltiples celdas, crear un contenedor que abarque todas las celdas
                        const firstCell = cellsToSpan[0];
                        
                        // Definir variables importantes primero
                        const rowSpan = cellsToSpan.length;
                        const cellHeight = 64; // Altura de cada celda en píxeles (h-16 = 4rem = 64px)
                        
                        // Limpiar todas las celdas excepto la primera
                        for (let i = 1; i < cellsToSpan.length; i++) {
                            cellsToSpan[i].innerHTML = '';
                            cellsToSpan[i].style.border = 'none';
                            cellsToSpan[i].style.padding = '0';
                            // Asegurarse de que las celdas no tengan altura propia
                            cellsToSpan[i].style.height = '0';
                        }
                        
                        // Posicionar el contenedor de la tarea
                        firstCell.style.position = 'relative';
                        // Asegurar que la primera celda tenga suficiente altura para contener toda la tarea
                        firstCell.style.height = `${rowSpan * cellHeight}px`;
                        firstCell.style.verticalAlign = 'top';
                        
                        // Configurar el contenedor para que abarque todas las celdas
                        taskContainer.style.height = `${rowSpan * cellHeight}px`;
                        taskContainer.style.position = 'absolute';
                        taskContainer.style.top = '0';
                        taskContainer.style.left = '0';
                        taskContainer.style.right = '0';
                        taskContainer.style.bottom = '0';
                        taskContainer.style.zIndex = '10';
                        
                        // Añadir el contenedor a la primera celda
                        firstCell.appendChild(taskContainer);
                        
                        // Extender la primera celda para que abarque todas las filas
                        firstCell.setAttribute('rowspan', rowSpan);
                        
                        // Ocultar las celdas que están siendo abarcadas
                        for (let i = 1; i < cellsToSpan.length; i++) {
                            cellsToSpan[i].style.display = 'none';
                        }
                    }
                    
                    // Añadir eventos para mostrar/ocultar tooltip con la descripción
                    taskContainer.addEventListener('mouseenter', (e) => {
                        // Preparar contenido del tooltip
                        const descripcion = tarea.descripcion || 'Sin descripción';
                        
                        // Aplicar clases médicas al tooltip
                        tooltip.className = 'fixed z-50 p-4 bg-white text-gray-800 text-sm rounded-lg shadow-xl max-w-xs border-l-4 medical-tooltip animate-glow-medical';
                        tooltip.style.minWidth = '250px';
                        
                        // Usar el color de la tarea para el borde izquierdo del tooltip
                        tooltip.style.borderLeftColor = tarea.color || '#0891b2';
                        
                        // Determinar el tipo de cita para mostrar un icono adecuado
                        let tipoIcono = 'fa-stethoscope';
                        let tipoTexto = 'Consulta Regular';
                        
                        if (tarea.titulo.toLowerCase().includes('urgencia') || tarea.titulo.toLowerCase().includes('emergencia')) {
                            tipoIcono = 'fa-heartbeat';
                            tipoTexto = 'Consulta Urgente';
                        } else if (tarea.titulo.toLowerCase().includes('control') || tarea.titulo.toLowerCase().includes('revisión')) {
                            tipoIcono = 'fa-clipboard-check';
                            tipoTexto = 'Control Médico';
                        }
                        
                        tooltip.innerHTML = `
                            <div class="font-bold text-base mb-2 pb-2 border-b border-gray-200 flex items-center">
                                <i class="fas ${tarea.icono || 'fa-calendar'} mr-2 text-lg" style="color: ${tarea.color || '#0891b2'}"></i>
                                <span>${tarea.titulo}</span>
                            </div>
                            <div class="text-gray-700 leading-relaxed mb-3">${descripcion}</div>
                            <div class="bg-cyan-50 rounded-md p-2 mb-3 flex items-center text-cyan-800">
                                <i class="fas ${tipoIcono} mr-2"></i>
                                <span>${tipoTexto}</span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-200 text-sm text-gray-600 flex justify-between">
                                <span class="flex items-center"><i class="far fa-clock mr-1 text-cyan-600"></i>${tarea.hora_inicio.substring(0, 5)} - ${tarea.hora_fin.substring(0, 5)}</span>
                                <span class="flex items-center"><i class="far fa-calendar-alt mr-1 text-cyan-600"></i>${['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][parseInt(tarea.dia_semana)]}</span>
                            </div>
                        `;
                        
                        // Posicionar el tooltip cerca del elemento (no del cursor)
                        const rect = taskContainer.getBoundingClientRect();
                        const tooltipHeight = 200; // Altura estimada actualizada
                        
                        // Posicionar arriba o abajo dependiendo del espacio disponible
                        const spaceBelow = window.innerHeight - rect.bottom;
                        if (spaceBelow < tooltipHeight + 10) {
                            // Posicionar arriba
                            tooltip.style.left = `${rect.left}px`;
                            tooltip.style.top = `${rect.top - tooltipHeight - 10}px`;
                        } else {
                            // Posicionar abajo
                            tooltip.style.left = `${rect.left}px`;
                            tooltip.style.top = `${rect.bottom + 10}px`;
                        }
                        
                        // Mostrar el tooltip con animación
                        tooltip.classList.remove('hidden');
                        tooltip.style.opacity = '0';
                        tooltip.style.transform = 'translateY(10px)';
                        
                        // Forzar un reflow para que la animación funcione
                        void tooltip.offsetWidth;
                        
                        tooltip.style.opacity = '1';
                        tooltip.style.transform = 'translateY(0)';
                    });
                    
                    taskContainer.addEventListener('mouseleave', () => {
                        // Ocultar el tooltip con animación
                        tooltip.style.opacity = '0';
                        tooltip.style.transform = 'translateY(10px)';
                        
                        setTimeout(() => {
                            if (!tooltip.matches(':hover')) {
                                tooltip.classList.add('hidden');
                            }
                        }, 300);
                    });
                    
                    // Nota: Ya no necesitamos añadir la tarea a la celda aquí, ya que se hace arriba
                    // dependiendo de si es una sola celda o múltiples celdas
                });
                console.log('Renderizado de tareas completado.');
            }
            
            // Inicializar calendario y vista semanal
            updateCalendar();
            updateWeeklyView();
            
            // Cargar tareas al iniciar
            cargarTareas();
            
            // Los mensajes de sesión ahora se manejan a través de meta tags y el sistema de notificaciones mejorado
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="{{ asset('js/calendario.js') }}"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex" x-data="{ modalOpen: false }">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            <!-- Main Content Area -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Title con decoración médica -->
                    <div class="mb-10 text-center relative">
                        <!-- Elementos decorativos médicos -->
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-16 h-16 text-cyan-500 opacity-10 animate-float-medical">
                            <i class="fas fa-stethoscope text-6xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-cyan-900 mb-4 flex items-center justify-center">
                            <span class="medical-gradient text-white p-2 rounded-lg shadow-md mr-4 inline-flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-white mr-2"></i>
                            </span>
                            Agenda Médica
                        </h1>
                        <p class="text-xl text-cyan-700">Gestiona tus citas médicas y horarios de consulta</p>
                        <!-- Indicadores médicos -->
                        <div class="flex justify-center mt-4 space-x-6">
                            <div class="flex items-center text-sm text-cyan-700">
                                <i class="fas fa-user-md text-cyan-600 mr-2"></i>
                                <span>Consultas</span>
                            </div>
                            <div class="flex items-center text-sm text-cyan-700">
                                <i class="fas fa-procedures text-cyan-600 mr-2"></i>
                                <span>Tratamientos</span>
                            </div>
                            <div class="flex items-center text-sm text-cyan-700">
                                <i class="fas fa-notes-medical text-cyan-600 mr-2"></i>
                                <span>Seguimientos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Grid con estilo médico -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-t-4 border-cyan-600">
                        <!-- Calendar Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-cyan-800 flex items-center">
                                <i class="fas fa-calendar-alt text-cyan-600 mr-2"></i>
                                Vista Mensual de Disponibilidad
                            </h2>
                        </div>

                        <!-- Calendar Days con estilo médico -->
                        <div class="grid grid-cols-7 gap-2 mb-4 bg-gradient-to-r from-cyan-50 to-teal-50 p-2 rounded-lg">
                            <div class="text-center font-medium text-cyan-800 py-2">Dom</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Lun</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Mar</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Mié</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Jue</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Vie</div>
                            <div class="text-center font-medium text-cyan-800 py-2">Sáb</div>
                        </div>

                        <!-- Calendar Grid con estilo médico -->
                        <div class="grid grid-cols-7 gap-2">
                            <!-- Calendar will be dynamically populated by JavaScript -->
                        </div>
                        
                        <!-- Leyenda de tipos de disponibilidad -->
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-medium text-cyan-800 mb-3">Tipos de Disponibilidad:</h3>
                            <div class="flex flex-wrap gap-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-cyan-500 mr-2"></div>
                                    <span class="text-xs text-gray-600">Consulta General</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-teal-500 mr-2"></div>
                                    <span class="text-xs text-gray-600">Consulta Especializada</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                    <span class="text-xs text-gray-600">Procedimientos</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                                    <span class="text-xs text-gray-600">Seguimiento</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                    <span class="text-xs text-gray-600">Emergencias</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista Semanal de Consultas Médicas -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6 mt-8 border-t-4 border-teal-600">
                        <!-- Encabezado de la vista semanal -->
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-teal-800 flex items-center">
                                <i class="fas fa-clock text-teal-600 mr-2"></i>
                                Horarios de Disponibilidad Médica
                            </h2>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-teal-700">Configuración Semanal</span>
                            </div>
                        </div>
                        
                        <!-- Modal de Cita Médica -->
                        <div x-show="modalOpen" x-cloak @click.away="modalOpen = false" class="fixed inset-0 bg-cyan-900 bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl transform transition-all duration-300" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100">
                                <!-- Header con estilo médico -->
                                <div class="flex justify-between items-center mb-6 pb-3 border-b border-cyan-100">
                                    <h3 class="text-xl font-bold text-cyan-800 flex items-center">
                                        <span class="bg-gradient-to-r from-cyan-500 to-teal-600 h-8 w-1 rounded-full mr-3"></span>
                                        Nuevo Horario de Disponibilidad
                                    </h3>
                                    <button @click="modalOpen = false" class="text-gray-400 hover:text-cyan-600 bg-gray-100 hover:bg-cyan-50 rounded-full p-2 transition-colors duration-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <form class="space-y-5" action="{{ route('tareas.store') }}" method="POST">
                                    @csrf
                                    <!-- Tipo de Consulta field with icon -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-cyan-700 mb-2 flex items-center">
                                            <i class="fas fa-stethoscope text-cyan-500 mr-2"></i>
                                            Tipo de Consulta
                                        </label>
                                        <div class="relative">
                                            <select name="titulo" 
                                                class="w-full border border-cyan-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 outline-none appearance-none bg-white" 
                                                required>
                                                <option value="Consulta General">Consulta General</option>
                                                <option value="Consulta Especializada">Consulta Especializada</option>
                                                <option value="Seguimiento">Seguimiento</option>
                                                <option value="Procedimientos">Procedimientos</option>
                                                <option value="Emergencias">Emergencias</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-cyan-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Día de disponibilidad field with icon -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-cyan-700 mb-2 flex items-center">
                                            <i class="fas fa-calendar-day text-teal-500 mr-2"></i>
                                            Día de Disponibilidad
                                        </label>
                                        <div class="relative">
                                            <select name="dia_semana" 
                                                class="w-full border border-cyan-200 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 outline-none bg-white" 
                                                required>
                                                <option value="1">Lunes</option>
                                                <option value="2">Martes</option>
                                                <option value="3">Miércoles</option>
                                                <option value="4">Jueves</option>
                                                <option value="5">Viernes</option>
                                                <option value="6">Sábado</option>
                                                <option value="7">Domingo</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-cyan-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Horario de consulta con validación -->
                                    <div x-data="{startTime: '', endTime: '', error: false, errorMessage: ''}" class="grid grid-cols-2 gap-4">
                                        <div class="group">
                                            <label class="block text-sm font-medium text-cyan-700 mb-2 flex items-center">
                                                <i class="fas fa-clock text-cyan-500 mr-2"></i>
                                                Hora de inicio
                                            </label>
                                            <div class="relative">
                                                <input type="time" name="hora_inicio" x-model="startTime" 
                                                    @change="if(endTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" 
                                                    class="w-full border border-cyan-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 outline-none" 
                                                    required>
                                            </div>
                                        </div>
                                        <div class="group">
                                            <label class="block text-sm font-medium text-cyan-700 mb-2 flex items-center">
                                                <i class="fas fa-clock text-teal-500 mr-2"></i>
                                                Hora de fin
                                            </label>
                                            <div class="relative">
                                                <input type="time" name="hora_fin" x-model="endTime" 
                                                    @change="if(startTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" 
                                                    class="w-full border border-cyan-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 outline-none" 
                                                    required>
                                            </div>
                                        </div>
                                        <div x-show="error" class="col-span-2 text-red-500 text-sm mt-1 bg-red-50 p-2 rounded-lg flex items-center" x-transition>
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            <span x-text="errorMessage"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Descripción field with icon -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-align-left text-purple-500 mr-2"></i>
                                            Detalles del Horario
                                        </label>
                                        <div class="relative">
                                            <textarea name="descripcion" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 h-24 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none" 
                                                placeholder="Detalles adicionales sobre este horario de disponibilidad (tipo de pacientes, requisitos especiales, etc.)"></textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- Color field with visual color indicators -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-palette text-yellow-500 mr-2"></i>
                                            Color
                                        </label>
                                        <div class="relative">
                                            <select name="color" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 outline-none bg-white" 
                                                x-data="{color: '#4A90E2'}" 
                                                x-model="color" 
                                                x-bind:style="'background-image: linear-gradient(to right, ' + color + '10, white 80%);'">
                                                <option value="#4A90E2">Azul</option>
                                                <option value="#2ECC71">Verde</option>
                                                <option value="#E74C3C">Rojo</option>
                                                <option value="#F1C40F">Amarillo</option>
                                                <option value="#9B59B6">Morado</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Icon selection with improved grid -->
                                    <div class="group" x-data="{selectedIconClass: 'fa-tasks'}">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-icons text-blue-500 mr-2"></i>
                                            Icono
                                        </label>
                                        <div class="grid grid-cols-5 gap-3 mt-2 icon-grid">
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-tasks'}" 
                                                 @click="selectedIconClass = 'fa-tasks'; document.getElementById('selectedIcon').value = 'fa-tasks'">
                                                <i class="fas fa-tasks text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-calendar'}" 
                                                 @click="selectedIconClass = 'fa-calendar'; document.getElementById('selectedIcon').value = 'fa-calendar'">
                                                <i class="fas fa-calendar text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-handshake'}" 
                                                 @click="selectedIconClass = 'fa-handshake'; document.getElementById('selectedIcon').value = 'fa-handshake'">
                                                <i class="fas fa-handshake text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-clock'}" 
                                                 @click="selectedIconClass = 'fa-clock'; document.getElementById('selectedIcon').value = 'fa-clock'">
                                                <i class="fas fa-clock text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-users'}" 
                                                 @click="selectedIconClass = 'fa-users'; document.getElementById('selectedIcon').value = 'fa-users'">
                                                <i class="fas fa-users text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-file'}" 
                                                 @click="selectedIconClass = 'fa-file'; document.getElementById('selectedIcon').value = 'fa-file'">
                                                <i class="fas fa-file text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-chart-bar'}" 
                                                 @click="selectedIconClass = 'fa-chart-bar'; document.getElementById('selectedIcon').value = 'fa-chart-bar'">
                                                <i class="fas fa-chart-bar text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-envelope'}" 
                                                 @click="selectedIconClass = 'fa-envelope'; document.getElementById('selectedIcon').value = 'fa-envelope'">
                                                <i class="fas fa-envelope text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-phone'}" 
                                                 @click="selectedIconClass = 'fa-phone'; document.getElementById('selectedIcon').value = 'fa-phone'">
                                                <i class="fas fa-phone text-xl text-gray-700"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md" 
                                                 :class="{'bg-blue-100 border-blue-500 ring-2 ring-blue-300': selectedIconClass === 'fa-star'}" 
                                                 @click="selectedIconClass = 'fa-star'; document.getElementById('selectedIcon').value = 'fa-star'">
                                                <i class="fas fa-star text-xl text-gray-700"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" id="selectedIcon" name="icono" value="fa-tasks">
                                    </div>
                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center">
                                            <i class="fas fa-times mr-2"></i>
                                            Cancelar
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center">
                                            <i class="fas fa-save mr-2"></i>
                                            Guardar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Weekly Calendar Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <button @click="
                                    const isAuthenticated = document.querySelector('meta[name=\'auth-check\']').content === '1';
                                    if (!isAuthenticated) {
                                        alert('Primero debe iniciar sesión para agregar una tarea semanal');
                                    } else {
                                        modalOpen = true;
                                    }
                                " class="medical-gradient text-white px-4 py-2 rounded-lg flex items-center shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 mr-2">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    <span>Agregar Cita</span>
                                </button>
                                <button @click="
                                    const isAuthenticated = document.querySelector('meta[name=\'auth-check\']').content === '1';
                                    if (!isAuthenticated) {
                                        alert('Primero debe iniciar sesión para editar una tarea');
                                    } else {
                                        $store.modal.editOpen = true;
                                    }
                                " class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg flex items-center shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 mr-2">
                                    <i class="fas fa-edit mr-2"></i>
                                    <span>Editar Cita</span>
                                </button>
                                <button @click="
                                    const isAuthenticated = document.querySelector('meta[name=\'auth-check\']').content === '1';
                                    if (!isAuthenticated) {
                                        alert('Primero debe iniciar sesión para eliminar una tarea');
                                    } else {
                                        $store.modal.deleteOpen = true;
                                    }
                                " class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg flex items-center shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 mr-2">
                                    <i class="fas fa-trash-alt mr-2"></i>
                                    <span>Eliminar Cita</span>
                                </button>
                                <div class="ml-2 flex items-center bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                    <button class="p-2 hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-chevron-left text-cyan-700"></i>
                                    </button>
                                    <div class="h-6 border-r border-gray-200"></div>
                                    <button class="p-2 hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-chevron-right text-cyan-700"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Calendar Grid -->                        
                        <div class="overflow-hidden bg-white rounded-lg shadow-md p-4">
                            <div class="min-w-max">
                                <!-- Time Slots - New Implementation with Table -->                                
                                <table class="w-full border-collapse" id="weekly-calendar-table">
                                    <thead>
                                        <tr class="calendar-header">
                                            <th class="w-20 p-3 text-center text-sm font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700 rounded-tl-lg">Hora</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Lunes</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Martes</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Miércoles</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Jueves</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Viernes</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700">Sábado</th>
                                            <th class="p-3 text-center font-semibold text-white bg-cyan-600 border-b-2 border-cyan-700 rounded-tr-lg">Domingo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <!-- 8:00 AM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-b border-r border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>8:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="8:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="7" data-hour="8:00"></td>
                                        </tr>
                                        <!-- 10:00 AM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-b border-r border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>10:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="10:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="7" data-hour="10:00"></td>
                                        </tr>
                                        <!-- 12:00 PM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-b border-r border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>12:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="12:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="7" data-hour="12:00"></td>
                                        </tr>
                                        <!-- 2:00 PM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-b border-r border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>14:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="14:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="7" data-hour="14:00"></td>
                                        </tr>
                                        <!-- 4:00 PM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-b border-r border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>16:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="16:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="7" data-hour="16:00"></td>
                                        </tr>
                                        <!-- 6:00 PM -->
                                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                                            <td class="p-3 text-center text-sm font-medium text-cyan-800 bg-cyan-50 border-r border-gray-200 flex items-center justify-center rounded-bl-lg">
                                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                                <span>18:00</span>
                                            </td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="1" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="2" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="3" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="4" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="5" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell" data-day="6" data-hour="18:00"></td>
                                            <td class="border border-gray-200 p-2 h-16 align-top overflow-y-auto calendar-cell rounded-br-lg" data-day="7" data-hour="18:00"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
           
        </div>
    </div>
</body>
</html>
<script>
    function selectIcon(iconName) {
        // Update the hidden input value
        document.getElementById('selectedIcon').value = iconName;
        
        // Remove highlight from all icons
        document.querySelectorAll('.icon-grid div').forEach(div => {
            div.classList.remove('bg-blue-50', 'border-blue-500');
        });
        
        // Highlight the selected icon
        event.currentTarget.classList.add('bg-blue-50', 'border-blue-500');
    }
</script>
<!-- Edit Task Modal -->
<div x-data="{ tareas: [] }" x-init="$watch('$store.modal.editOpen', value => { if(value) { tareas = window.tareas } })" x-show="$store.modal.editOpen" x-cloak @click.away="$store.modal.editOpen = false" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-xl p-0 w-full max-w-md shadow-2xl transform transition-all duration-300 overflow-hidden" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100">
        <!-- Header with medical gradient background -->
        <div class="medical-gradient text-white p-4 flex justify-between items-center">
            <h3 class="text-xl font-bold flex items-center">
                <i class="fas fa-stethoscope mr-3 animate-pulse-medical"></i>
                <span x-text="$store.editTask.isEditing ? 'Editar Cita Médica' : 'Citas Programadas'"></span>
            </h3>
            <button @click="$store.modal.editOpen = false; $store.editTask.cancelEdit()" class="text-white hover:text-cyan-100 bg-cyan-700 hover:bg-cyan-800 rounded-full p-2 transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Edit Task Form -->
        <div x-show="$store.editTask.isEditing && $store.editTask.selectedTask" class="p-6">
            <form @submit.prevent="$store.editTask.saveChanges()">
                <!-- Título -->
                <div class="mb-4">
                    <label for="titulo" class="block text-sm font-medium text-cyan-800 mb-1 flex items-center">
                        <i class="fas fa-file-medical mr-2 text-cyan-600"></i>
                        Título de la Cita
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-pen-fancy text-cyan-500"></i>
                        </div>
                        <input type="text" id="titulo" x-model="$store.editTask.selectedTask.titulo" 
                               class="w-full pl-10 pr-3 py-2 border border-cyan-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-cyan-50" required>
                    </div>
                </div>
                
                <!-- Descripción -->
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-cyan-800 mb-1 flex items-center">
                        <i class="fas fa-notes-medical mr-2 text-cyan-600"></i>
                        Descripción
                    </label>
                    <div class="relative">
                        <textarea id="descripcion" x-model="$store.editTask.selectedTask.descripcion" 
                                  class="w-full px-3 py-2 border border-cyan-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-cyan-50 h-24"></textarea>
                    </div>
                </div>
                
                <!-- Color -->
                <div class="mb-4">
                    <label for="color" class="block text-sm font-medium text-cyan-800 mb-1 flex items-center">
                        <i class="fas fa-palette mr-2 text-cyan-600"></i>
                        Color de la Cita
                    </label>
                    <input type="color" id="color" x-model="$store.editTask.selectedTask.color" 
                           class="w-full h-10 border border-cyan-200 rounded-md cursor-pointer">
                </div>
                
                <!-- Horario - Flex container para hora inicio y fin -->
                <div class="flex space-x-4 mb-4">
                    <!-- Hora Inicio -->
                    <div class="flex-1">
                        <label for="hora_inicio" class="block text-sm font-medium text-cyan-800 mb-1 flex items-center">
                            <i class="fas fa-hourglass-start mr-2 text-cyan-600"></i>
                            Hora de inicio
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-clock text-cyan-500"></i>
                            </div>
                            <input type="time" id="hora_inicio" x-model="$store.editTask.selectedTask.hora_inicio" 
                                   class="w-full pl-10 pr-3 py-2 border border-cyan-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-cyan-50" required>
                        </div>
                    </div>
                    
                    <!-- Hora Fin -->
                    <div class="flex-1">
                        <label for="hora_fin" class="block text-sm font-medium text-cyan-800 mb-1 flex items-center">
                            <i class="fas fa-hourglass-end mr-2 text-cyan-600"></i>
                            Hora de fin
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-clock text-cyan-500"></i>
                            </div>
                            <input type="time" id="hora_fin" x-model="$store.editTask.selectedTask.hora_fin" 
                                   class="w-full pl-10 pr-3 py-2 border border-cyan-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-cyan-50" required>
                        </div>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="$store.editTask.cancelEdit()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors flex items-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 medical-gradient text-white rounded-md hover:opacity-90 transition-colors flex items-center shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Task List Grouped by Day -->
        <div x-show="!$store.editTask.isEditing" class="max-h-96 overflow-y-auto p-6">
            <template x-if="tareas.length === 0">
                <div class="text-center py-8 bg-cyan-50 rounded-lg">
                    <i class="fas fa-calendar-times text-cyan-300 text-5xl mb-4 animate-pulse-medical"></i>
                    <p class="text-cyan-700">No hay citas médicas programadas</p>
                </div>
            </template>
            
            <!-- Group tasks by day of the week -->
            <template x-for="dayNumber in [1, 2, 3, 4, 5, 6, 7]" :key="dayNumber">
                <div>
                    <!-- Day header -->
                    <template x-if="tareas.filter(t => parseInt(t.dia_semana) === dayNumber).length > 0">
                        <div class="flex items-center py-2 px-1 mb-2 border-b border-cyan-200 text-cyan-800">
                            <i class="fas fa-calendar-day mr-2 text-cyan-600"></i>
                            <h3 class="font-medium" x-text="getDayName(dayNumber)"></h3>
                        </div>
                    </template>
                    
                    <!-- Tasks for this day -->
                    <template x-for="(tarea, index) in tareas.filter(t => parseInt(t.dia_semana) === dayNumber)" :key="index">
                        <div class="mb-4 p-4 rounded-lg transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md transform hover:-translate-y-1 medical-card" 
                             :style="{ backgroundColor: tarea.color || '#0891b2', color: 'white', borderLeft: '4px solid ' + (tarea.color ? tarea.color.replace('91', '70') : '#0e7490') }"
                             @click="$store.editTask.selectTask(tarea)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium flex items-center">
                                        <i class="fas fa-stethoscope mr-2"></i>
                                        <span x-text="tarea.titulo"></span>
                                    </h4>
                                    <p class="text-sm opacity-90 mt-1" x-text="tarea.descripcion || 'Sin descripción'"></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-3 text-sm bg-white/20 rounded px-2 py-1 inline-block">
                                <div class="flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    <span x-text="tarea.hora_inicio + ' - ' + tarea.hora_fin"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Delete Task Modal -->
<div x-data="{ tareas: [] }" x-init="$watch('$store.modal.deleteOpen', value => { if(value) { tareas = window.tareas } })" x-show="$store.modal.deleteOpen" x-cloak @click.away="$store.modal.deleteOpen = false" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-xl p-0 w-full max-w-md shadow-2xl transform transition-all duration-300 overflow-hidden" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100">
        <!-- Header with medical gradient background -->
        <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white p-4 flex justify-between items-center">
            <h3 class="text-xl font-bold flex items-center">
                <i class="fas fa-trash-alt mr-3 animate-pulse-medical"></i>
                <span>Eliminar Cita Médica</span>
            </h3>
            <button @click="$store.modal.deleteOpen = false; $store.deleteTask.cancelDelete()" class="text-white hover:text-red-100 bg-red-700 hover:bg-red-800 rounded-full p-2 transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Delete Task Confirmation -->
        <div x-show="$store.deleteTask.selectedTask" class="p-6">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-md shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl"></i>
                    <p class="text-red-700 font-medium">¿Estás seguro de que deseas eliminar esta cita médica?</p>
                </div>
                <div class="mt-4 p-3 bg-white rounded-md border border-red-100 shadow-sm">
                    <div class="flex items-center text-red-800">
                        <i class="fas fa-calendar-day mr-2"></i>
                        <span class="font-semibold" x-text="$store.deleteTask.selectedTask.titulo"></span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600" x-text="$store.deleteTask.selectedTask.descripcion || 'Sin descripción'"></div>
                    <div class="mt-2 flex items-center text-sm text-gray-600">
                        <i class="far fa-clock mr-1 text-red-400"></i>
                        <span x-text="$store.deleteTask.selectedTask.hora_inicio + ' - ' + $store.deleteTask.selectedTask.hora_fin"></span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button @click="$store.deleteTask.cancelDelete(); $store.modal.deleteOpen = false" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancelar
                </button>
                <button @click="$store.deleteTask.deleteTask()" 
                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-md hover:opacity-90 transition-colors flex items-center shadow-md">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Eliminar
                </button>
            </div>
        </div>
        
        <!-- Task List Grouped by Day -->
        <div x-show="!$store.deleteTask.selectedTask" class="max-h-96 overflow-y-auto p-6">
            <template x-if="tareas.length === 0">
                <div class="text-center py-8 bg-red-50 rounded-lg">
                    <i class="fas fa-calendar-times text-red-300 text-5xl mb-4 animate-pulse-medical"></i>
                    <p class="text-red-700">No hay citas médicas para eliminar</p>
                </div>
            </template>
            
            <!-- Group tasks by day of the week -->
            <template x-for="dayNumber in [1, 2, 3, 4, 5, 6, 7]" :key="dayNumber">
                <div>
                    <!-- Day header -->
                    <template x-if="tareas.filter(t => parseInt(t.dia_semana) === dayNumber).length > 0">
                        <div class="flex items-center py-2 px-1 mb-2 border-b border-red-200 text-red-800">
                            <i class="fas fa-calendar-day mr-2 text-red-600"></i>
                            <h3 class="font-medium" x-text="getDayName(dayNumber)"></h3>
                        </div>
                    </template>
                    
                    <!-- Tasks for this day -->
                    <template x-for="(tarea, index) in tareas.filter(t => parseInt(t.dia_semana) === dayNumber)" :key="index">
                        <div class="mb-4 p-4 rounded-lg transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md transform hover:-translate-y-1 medical-card" 
                             :style="{ backgroundColor: tarea.color || '#EF4444', color: 'white', borderLeft: '4px solid ' + (tarea.color ? tarea.color.replace('44', '22') : '#B91C1C') }"
                             @click="$store.deleteTask.selectTask(tarea)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium flex items-center">
                                        <i class="fas fa-user-md mr-2"></i>
                                        <span x-text="tarea.titulo"></span>
                                    </h4>
                                    <p class="text-sm opacity-90 mt-1" x-text="tarea.descripcion || 'Sin descripción'"></p>
                                </div>
                                <div>
                                    <i class="fas fa-trash-alt hover:text-red-200 bg-red-700 p-2 rounded-full"></i>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-3 text-sm bg-white/20 rounded px-2 py-1 inline-block">
                                <div class="flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    <span x-text="tarea.hora_inicio + ' - ' + tarea.hora_fin"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
// Make getDayName available to Alpine.js templates by adding it to the window object
window.addEventListener('DOMContentLoaded', () => {
    // Make getDayName available to Alpine templates
    window.getDayName = function(dayNumber) {
        const dayNames = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        return dayNames[dayNumber] || '';
    };
    
    // Cargar las tareas al iniciar la página
    // Verificar que la función existe antes de llamarla
    if (typeof window.cargarTareas === 'function') {
        window.cargarTareas();
    } else {
        console.error('La función cargarTareas no está definida');
    }
    
    // No need for manual event listener since we're using Alpine.js directives
    console.log('Alpine modal store initialized and ready:', Alpine.store('modal'));
    
    // Ensure the modal is visible by checking if Alpine.js is properly initialized
    console.log('Alpine available in DOMContentLoaded:', window.Alpine);
    console.log('Current modal state:', Alpine.store('modal'));
});
</script>