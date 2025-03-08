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
                        <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer ${isToday ? 'bg-blue-100' : ''}">
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
                // Calcular el inicio de la semana (lunes)
                currentWeekStart = getMonday(new Date(selectedDate));
                
                // Calcular el fin de la semana (domingo)
                currentWeekEnd = new Date(currentWeekStart);
                currentWeekEnd.setDate(currentWeekStart.getDate() + 6);
                
                // Actualizar el título de la semana
                const weeklyHeader = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 h2.text-xl.font-semibold');
                if (weeklyHeader) {
                    weeklyHeader.textContent = `Vista Semanal: ${formatDate(currentWeekStart)} - ${formatDate(currentWeekEnd)} ${currentWeekStart.getFullYear()}`;
                }
                
                // Resaltar el día actual en la vista semanal si está dentro de la semana mostrada
                const weekDays = document.querySelectorAll('.grid.grid-cols-8.gap-2.border-b.pb-2.mb-2 div:not(:first-child)');
                
                // Quitar resaltado de todos los días
                weekDays.forEach(day => {
                    day.classList.remove('bg-blue-100', 'text-blue-800', 'font-bold');
                });
                
                // Comprobar si el día actual está en la semana mostrada
                if (today >= currentWeekStart && today <= currentWeekEnd) {
                    const todayDayOfWeek = today.getDay() || 7; // 0 es domingo, lo convertimos a 7
                    if (weekDays[todayDayOfWeek - 1]) {
                        weekDays[todayDayOfWeek - 1].classList.add('bg-blue-100', 'text-blue-800', 'font-bold');
                    }
                }
            }
            
            // Configurar botones de navegación para la vista semanal
            const weeklyPrevButton = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 .fa-chevron-left').parentElement;
            const weeklyNextButton = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.mb-6.mt-8 .fa-chevron-right').parentElement;
            
            weeklyPrevButton.addEventListener('click', () => {
                selectedDate.setDate(selectedDate.getDate() - 7);
                updateWeeklyView();
            });
            
            weeklyNextButton.addEventListener('click', () => {
                selectedDate.setDate(selectedDate.getDate() + 7);
                updateWeeklyView();
            });
            
            // Inicializar calendario y vista semanal
            updateCalendar();
            updateWeeklyView();
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
                        <!-- Add Task Button -->
                        <div class="flex justify-end mb-4">
                            <button @click="modalOpen = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Agregar Tarea</span>
                            </button>
                        </div>
                        
                        <!-- Task Modal -->
                        <div x-show="modalOpen" x-cloak @click.away="modalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold">Nueva Tarea</h3>
                                    <button @click="modalOpen = false" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                        <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Título del evento">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Día</label>
                                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <option>Lunes</option>
                                            <option>Martes</option>
                                            <option>Miércoles</option>
                                            <option>Jueves</option>
                                            <option>Viernes</option>
                                            <option>Sábado</option>
                                            <option>Domingo</option>
                                        </select>
                                    </div>
                                    <div x-data="{startTime: '', endTime: '', error: false, errorMessage: ''}">  
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora de inicio</label>
                                            <input type="time" x-model="startTime" @change="if(endTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                        </div>
                                        <div class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora de fin</label>
                                            <input type="time" x-model="endTime" @change="if(startTime) { if(startTime > endTime) { error = true; errorMessage = 'La hora de fin no puede ser anterior a la hora de inicio'; } else if(startTime === endTime) { error = true; errorMessage = 'La hora de fin no puede ser igual a la hora de inicio'; } else { error = false; } }" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                        </div>
                                        <div x-show="error" class="text-red-500 text-sm mt-1" x-text="errorMessage">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                        <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 h-24" placeholder="Descripción de la tarea"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <option value="blue">Azul</option>
                                            <option value="green">Verde</option>
                                            <option value="red">Rojo</option>
                                            <option value="yellow">Amarillo</option>
                                            <option value="purple">Morado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                                        <div class="grid grid-cols-5 gap-2 mt-2 icon-grid">
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-tasks')">
                                                <i class="fas fa-tasks text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-calendar')">
                                                <i class="fas fa-calendar text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-handshake')">
                                                <i class="fas fa-handshake text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-clock')">
                                                <i class="fas fa-clock text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-users')">
                                                <i class="fas fa-users text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-file')">
                                                <i class="fas fa-file text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-chart-bar')">
                                                <i class="fas fa-chart-bar text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-envelope')">
                                                <i class="fas fa-envelope text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-phone')">
                                                <i class="fas fa-phone text-lg"></i>
                                            </div>
                                            <div class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" onclick="selectIcon('fa-star')">
                                                <i class="fas fa-star text-lg"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" id="selectedIcon" name="icon" value="fa-tasks">
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Weekly Calendar Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold">Vista Semanal: 9 - 15 Octubre 2023</h2>
                            <div class="flex space-x-2">
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
                                <!-- Time Column Headers -->
                                <div class="grid grid-cols-8 gap-2 border-b pb-2 mb-2">
                                    <div class="text-center font-medium text-gray-500 w-20">Hora</div>
                                    <div class="text-center font-medium text-gray-600">Lunes</div>
                                    <div class="text-center font-medium text-gray-600">Martes</div>
                                    <div class="text-center font-medium text-gray-600">Miércoles</div>
                                    <div class="text-center font-medium text-gray-600">Jueves</div>
                                    <div class="text-center font-medium text-gray-600">Viernes</div>
                                    <div class="text-center font-medium text-gray-600">Sábado</div>
                                    <div class="text-center font-medium text-gray-600">Domingo</div>
                                </div>

                                <!-- Time Slots -->
                                <div class="grid grid-cols-8 gap-2">
                                    <!-- 8:00 AM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">8:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>

                                    <!-- 10:00 AM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">10:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>

                                    <!-- 12:00 PM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">12:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>

                                    <!-- 2:00 PM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">14:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>

                                    <!-- 4:00 PM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">16:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>

                                    <!-- 6:00 PM -->
                                    <div class="text-center py-3 text-sm text-gray-600 bg-gray-50">18:00</div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
                                    <div class="border rounded-lg p-1 h-16 hover:bg-gray-50"></div>
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