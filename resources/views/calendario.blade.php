<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Inicialización global de Alpine.js
        window.addEventListener('DOMContentLoaded', () => {
            window.Alpine = window.Alpine || {};
            window.Alpine.store('modal', {
                open: false
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();
            let currentWeekStart = new Date();
            let currentWeekEnd = new Date();
            let selectedDate = new Date();
            
            // Cargar tareas desde el servidor
            let tareas = [];
            
            // Función para cargar las tareas
            function cargarTareas() {
                fetch('{{ route("tareas.json") }}')
                    .then(response => response.json())
                    .then(data => {
                        tareas = data;
                        renderizarTareas();
                    })
                    .catch(error => console.error('Error cargando tareas:', error));
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
            const prevButton = document.querySelector('.fa-chevron-left').parentElement;
            const nextButton = document.querySelector('.fa-chevron-right').parentElement;

            prevButton.addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                updateCalendar();
            });

            nextButton.addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                updateCalendar();
            });

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
                
                // Renderizar cada tarea
                tareas.forEach((tarea, index) => {
                    console.log(`\nProcesando tarea ${index + 1}:`, {
                        titulo: tarea.titulo,
                        dia_semana: tarea.dia_semana,
                        hora_inicio: tarea.hora_inicio
                    });
                
                    // Obtener el día de la semana (1-7)
                    const diaSemana = parseInt(tarea.dia_semana);
                    
                    console.log(`Día de la semana: ${diaSemana}`);
                    
                    if (diaSemana < 1 || diaSemana > 7) {
                        console.error(`Error: Valor de día inválido: ${diaSemana}`);
                        return;
                    }
                    
                    // Obtener la hora de inicio para determinar la celda
                    const startHour = tarea.hora_inicio.substring(0, 5);
                    
                    // Encontrar la celda correspondiente usando los atributos data-day y data-hour
                    const hourParts = startHour.split(':');
                    const hourValue = parseInt(hourParts[0]);
                    
                    // Buscar la hora más cercana disponible en la tabla (8, 10, 12, 14, 16, 18)
                    let nearestHour;
                    const availableHours = [8, 10, 12, 14, 16, 18];
                    
                    // Encontrar la hora más cercana
                    if (availableHours.includes(hourValue)) {
                        nearestHour = hourValue;
                    } else {
                        // Encontrar la hora más cercana por abajo o por arriba
                        const lowerHours = availableHours.filter(h => h <= hourValue);
                        const higherHours = availableHours.filter(h => h >= hourValue);
                        
                        if (lowerHours.length === 0) {
                            nearestHour = Math.min(...higherHours);
                        } else if (higherHours.length === 0) {
                            nearestHour = Math.max(...lowerHours);
                        } else {
                            const maxLower = Math.max(...lowerHours);
                            const minHigher = Math.min(...higherHours);
                            
                            // Elegir la hora más cercana
                            nearestHour = (hourValue - maxLower) <= (minHigher - hourValue) ? maxLower : minHigher;
                        }
                    }
                    
                    const hourOnly = `${nearestHour}:00`;
                    
                    // Buscar la celda que corresponde al día y hora más cercana
                    const targetCell = document.querySelector(`#weekly-calendar-table td[data-day="${diaSemana}"][data-hour="${hourOnly}"]`);
                    
                    if (!targetCell) {
                        console.error(`Error: No se encontró celda para día ${diaSemana} y hora ${hourOnly}`);
                        return;
                    }
                    
                    console.log(`Renderizando tarea "${tarea.titulo}" en día ${diaSemana} (${['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][diaSemana]}) hora ${startHour}`);
                    
                    // Crear el elemento de la tarea con un diseño más limpio
                    // Verificar si ya hay tareas en esta celda y ajustar la posición
                    const taskElement = document.createElement('div');
                    taskElement.className = 'p-1 rounded text-xs overflow-hidden mb-1';
                    taskElement.style.backgroundColor = tarea.color || '#3B82F6';
                    taskElement.style.color = 'white';
                    taskElement.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas ${tarea.icono || 'fa-calendar'} mr-1"></i>
                            <span class="font-medium truncate">${tarea.titulo}</span>
                        </div>
                        <div class="text-xs opacity-90">${tarea.hora_inicio.substring(0, 5)} - ${tarea.hora_fin.substring(0, 5)}</div>
                    `;
                    
                    // Añadir la tarea a la celda
                    targetCell.appendChild(taskElement);
                });
                console.log('Renderizado de tareas completado.');
            }
            
            // Inicializar calendario y vista semanal
            updateCalendar();
            updateWeeklyView();
            
            // Cargar tareas al iniciar
            cargarTareas();
            
            // Mostrar mensaje de éxito si existe
            @if(session('success'))
                alert('{{ session("success") }}');
            @endif
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100">
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
                    <!-- Page Title -->
                    <div class="mb-10 text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Calendario de Eventos</h1>
                        <p class="text-xl text-gray-600">Organiza y visualiza tus eventos y fechas importantes</p>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <!-- Calendar Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold">Octubre 2023</h2>
                            <div class="flex space-x-2">
                                <button class="p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-chevron-left text-gray-600"></i>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-chevron-right text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Days -->
                        <div class="grid grid-cols-7 gap-2 mb-4">
                            <div class="text-center font-medium text-gray-600">Dom</div>
                            <div class="text-center font-medium text-gray-600">Lun</div>
                            <div class="text-center font-medium text-gray-600">Mar</div>
                            <div class="text-center font-medium text-gray-600">Mié</div>
                            <div class="text-center font-medium text-gray-600">Jue</div>
                            <div class="text-center font-medium text-gray-600">Vie</div>
                            <div class="text-center font-medium text-gray-600">Sáb</div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2">
                            <!-- Calendar will be dynamically populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Weekly Calendar View -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6 mt-8">

                        
                        <!-- Task Modal -->
                        <div x-show="modalOpen" x-cloak @click.away="modalOpen = false" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl transform transition-all duration-300" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100">
                                <!-- Header with gradient background -->
                                <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-100">
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                        <span class="bg-gradient-to-r from-blue-500 to-purple-600 h-8 w-1 rounded-full mr-3"></span>
                                        Nueva Tarea
                                    </h3>
                                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors duration-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <form class="space-y-5" action="{{ route('tareas.store') }}" method="POST">
                                    @csrf
                                    <!-- Título field with icon -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-heading text-blue-500 mr-2"></i>
                                            Título
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="titulo" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none" 
                                                placeholder="Título del evento" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Día field with icon -->
                                    <div class="group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-calendar-day text-green-500 mr-2"></i>
                                            Día
                                        </label>
                                        <div class="relative">
                                            <select name="dia_semana" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none bg-white" 
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
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Time fields with validation -->
                                    <div x-data="{startTime: '', endTime: '', error: false, errorMessage: ''}" class="grid grid-cols-2 gap-4">
                                        <div class="group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                <i class="fas fa-hourglass-start text-orange-500 mr-2"></i>
                                                Hora de inicio
                                            </label>
                                            <div class="relative">
                                                <input type="time" name="hora_inicio" x-model="startTime" 
                                                    @change="if(endTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" 
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 outline-none" 
                                                    required>
                                            </div>
                                        </div>
                                        <div class="group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                <i class="fas fa-hourglass-end text-red-500 mr-2"></i>
                                                Hora de fin
                                            </label>
                                            <div class="relative">
                                                <input type="time" name="hora_fin" x-model="endTime" 
                                                    @change="if(startTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" 
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 outline-none" 
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
                                            Descripción
                                        </label>
                                        <div class="relative">
                                            <textarea name="descripcion" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 h-24 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none" 
                                                placeholder="Descripción de la tarea"></textarea>
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
                            <h2 class="text-xl font-semibold">Vista Semanal</h2>
                            <div class="flex items-center space-x-2">
                                <button @click="modalOpen = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 mr-4">
                                    <i class="fas fa-plus"></i>
                                    <span>Agregar Tarea</span>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-chevron-left text-gray-600"></i>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-chevron-right text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Weekly Calendar Grid -->
                        <div class="overflow-x-auto">
                            <div class="min-w-max">
                                <!-- Time Slots - New Implementation with Table -->
                                <table class="w-full border-collapse" id="weekly-calendar-table">
                                    <thead>
                                        <tr>
                                            <th class="w-20 p-2 text-center text-sm font-medium text-gray-600 bg-gray-50 border">Hora</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Lunes</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Martes</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Miércoles</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Jueves</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Viernes</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Sábado</th>
                                            <th class="p-2 text-center font-medium text-gray-600 bg-gray-50 border">Domingo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 8:00 AM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">8:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="8:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="8:00"></td>
                                        </tr>
                                        <!-- 10:00 AM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">10:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="10:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="10:00"></td>
                                        </tr>
                                        <!-- 12:00 PM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">12:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="12:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="12:00"></td>
                                        </tr>
                                        <!-- 2:00 PM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">14:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="14:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="14:00"></td>
                                        </tr>
                                        <!-- 4:00 PM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">16:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="16:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="16:00"></td>
                                        </tr>
                                        <!-- 6:00 PM -->
                                        <tr>
                                            <td class="p-2 text-center text-sm text-gray-600 bg-gray-50 border">18:00</td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="1" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="2" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="3" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="4" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="5" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="6" data-hour="18:00"></td>
                                            <td class="border p-1 h-16 align-top overflow-y-auto" data-day="7" data-hour="18:00"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Today's Events -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4">Eventos de Hoy</h2>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Reunión de Equipo</h3>
                                        <p class="text-sm text-gray-500">10:00 AM - 11:30 AM</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <i class="fas fa-video text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Videoconferencia con Cliente</h3>
                                        <p class="text-sm text-gray-500">2:00 PM - 3:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4">Próximos Eventos</h2>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i class="fas fa-calendar-alt text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Presentación de Proyecto</h3>
                                        <p class="text-sm text-gray-500">Mañana, 9:00 AM</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="p-2 bg-yellow-100 rounded-lg">
                                        <i class="fas fa-users text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">Reunión de Planificación</h3>
                                        <p class="text-sm text-gray-500">25 Oct, 2:00 PM</p>
                                    </div>
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