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
                            <!-- Previous Month Days -->
                            <div class="p-2 text-center text-gray-400">30</div>
                            <div class="p-2 text-center text-gray-400">31</div>

                            <!-- Current Month Days -->
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">1</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">2</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">3</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">4</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">5</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer relative">
                                6
                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                            </div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">7</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">8</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer">9</div>
                            <div class="p-2 text-center hover:bg-gray-50 rounded-lg cursor-pointer relative">
                                10
                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-red-500 rounded-full"></div>
                            </div>
                            <!-- Continue with remaining days... -->
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