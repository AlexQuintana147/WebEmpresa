<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrucciones</title>
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

            <!-- Instructions Content -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Welcome Section -->
                    <div class="mb-10 text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenido a tu Sistema de Gestión Financiera</h1>
                        <p class="text-xl text-gray-600">Una solución completa para administrar tus finanzas, tareas y fechas importantes</p>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <!-- Financial Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-blue-600 mb-4">
                                <i class="fas fa-chart-line text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión Financiera</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Control de ingresos y gastos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Seguimiento de ganancias
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Reportes financieros detallados
                                </li>
                            </ul>
                        </div>

                        <!-- Task Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-purple-600 mb-4">
                                <i class="fas fa-tasks text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión de Actividades</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Lista de tareas pendientes
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Organización de actividades
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Recordatorios importantes
                                </li>
                            </ul>
                        </div>

                        <!-- Calendar -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-orange-600 mb-4">
                                <i class="fas fa-calendar-alt text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Calendario Inteligente</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Fechas importantes
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Notificaciones automáticas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Vista mensual y semanal
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Getting Started Section -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-8 text-white">
                        <h2 class="text-3xl font-bold mb-4">¿Cómo Empezar?</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold">1</div>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Configura tu Perfil</h3>
                                    <p class="text-blue-100">Personaliza tu cuenta y establece tus preferencias iniciales</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold">2</div>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Agrega tus Finanzas</h3>
                                    <p class="text-blue-100">Comienza a registrar tus ingresos y gastos diarios</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold">3</div>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Planifica tus Actividades</h3>
                                    <p class="text-blue-100">Organiza tus tareas y marca fechas importantes</p>
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