<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            })
        })

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();

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

            // Inicializar calendario
            updateCalendar();
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
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
                                <!-- 8:00 AM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">8:00 AM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 9:00 AM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">9:00 AM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg bg-green-100 text-green-800 border border-green-200">
                                        <p class="font-medium">Presentación</p>
                                        <p>9:00 - 10:30</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 10:00 AM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">10:00 AM</div>
                                    <div class="p-1 text-xs rounded-lg bg-blue-100 text-blue-800 border border-blue-200">
                                        <p class="font-medium">Reunión Equipo</p>
                                        <p>10:00 - 11:30</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 11:00 AM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">11:00 AM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 12:00 PM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">12:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <p class="font-medium">Almuerzo Cliente</p>
                                        <p>12:00 - 1:30</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 1:00 PM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">1:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 2:00 PM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">2:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg bg-purple-100 text-purple-800 border border-purple-200">
                                        <p class="font-medium">Videoconferencia</p>
                                        <p>2:00 - 3:00</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 3:00 PM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">3:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg bg-red-100 text-red-800 border border-red-200">
                                        <p class="font-medium">Revisión Proyecto</p>
                                        <p>3:00 - 4:30</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 4:00 PM -->
                                <div class="grid grid-cols-8 gap-2 border-b py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">4:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                </div>

                                <!-- 5:00 PM -->
                                <div class="grid grid-cols-8 gap-2 py-2">
                                    <div class="text-center text-sm text-gray-500 w-20">5:00 PM</div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg"></div>
                                    <div class="p-1 text-xs rounded-lg bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        <p class="font-medium">Evento Social</p>
                                        <p>5:00 - 7:00</p>
                                    </div>
                                    <div class="p-1 text-xs rounded-lg"></div>
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