<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inversiones</title>
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
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Lista de Actividades</h1>
                        <p class="text-xl text-gray-600">Gestiona y organiza tus tareas de manera eficiente</p>
                    </div>

                    <!-- Task Management Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Pending Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold">Pendientes</h2>
                                <div class="p-2 bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <!-- Task Item -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium">Reunión con Clientes</h3>
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs">Alta</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Presentación del nuevo proyecto</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span><i class="far fa-calendar mr-1"></i> 25 Oct 2023</span>
                                        <span><i class="far fa-clock mr-1"></i> 10:00 AM</span>
                                    </div>
                                </div>

                                <!-- Task Item -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium">Revisión de Presupuesto</h3>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs">Media</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Análisis mensual de gastos</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span><i class="far fa-calendar mr-1"></i> 26 Oct 2023</span>
                                        <span><i class="far fa-clock mr-1"></i> 2:00 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold">En Progreso</h2>
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <i class="fas fa-spinner text-blue-600"></i>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <!-- Task Item -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium">Desarrollo de Informe</h3>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs">Media</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Informe trimestral de ventas</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span><i class="far fa-calendar mr-1"></i> En curso</span>
                                        <span><i class="fas fa-percentage mr-1"></i> 60%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold">Completadas</h2>
                                <div class="p-2 bg-green-100 rounded-full">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <!-- Task Item -->
                                <div class="p-4 bg-gray-50 rounded-lg opacity-75">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium line-through">Actualización de Sistema</h3>
                                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Completada</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Mantenimiento programado</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span><i class="far fa-calendar mr-1"></i> 22 Oct 2023</span>
                                        <span><i class="far fa-check-circle mr-1"></i> Finalizada</span>
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