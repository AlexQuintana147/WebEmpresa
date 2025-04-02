<!DOCTYPE html>
<html lang="es">
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
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenido al Sistema de la Clínica Ricardo Palma</h1>
                        <p class="text-xl text-gray-600">Una solución integral para la gestión médica, administrativa y de pacientes</p>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <!-- Financial Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-emerald-600 mb-4">
                                <i class="fas fa-wallet text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión Financiera</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Control de ingresos y gastos médicos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Categorización de transacciones clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Reportes financieros del centro médico
                                </li>
                            </ul>
                        </div>

                        <!-- Task Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-amber-600 mb-4">
                                <i class="fas fa-tasks text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión de Actividades</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Seguimiento de procedimientos médicos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Organización de actividades clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Priorización de tareas médicas
                                </li>
                            </ul>
                        </div>

                        <!-- Calendar -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-fuchsia-600 mb-4">
                                <i class="fas fa-calendar-alt text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Calendario Médico</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Programación de citas médicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Recordatorios de consultas y tratamientos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Organización semanal de horarios
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Additional Features -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <!-- Notes System -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-teal-600 mb-4">
                                <i class="fas fa-sticky-note text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Sistema de Notas Médicas</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Registro de historias clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Organización por categorías médicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Acceso rápido a información importante
                                </li>
                            </ul>
                        </div>

                        <!-- AI Assistant -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105">
                            <div class="text-indigo-600 mb-4">
                                <i class="fas fa-brain text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Asistente IA</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Consultas médicas asistidas por IA
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Ayuda en la toma de decisiones clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Respuestas a preguntas frecuentes
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
                                    <h3 class="font-semibold mb-2">Configura tu Perfil Médico</h3>
                                    <p class="text-blue-100">Personaliza tu cuenta con tus datos profesionales</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold">2</div>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Registra Transacciones</h3>
                                    <p class="text-blue-100">Comienza a registrar ingresos y gastos del centro médico</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold">3</div>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Programa Actividades</h3>
                                    <p class="text-blue-100">Organiza tus consultas y procedimientos médicos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- For Patients Section -->
                    <div class="mt-12 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-xl shadow-lg p-8 text-white">
                        <h2 class="text-3xl font-bold mb-4">Acceso para Pacientes</h2>
                        <p class="text-lg mb-6">El sistema también ofrece funcionalidades específicas para nuestros pacientes:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 hover:bg-white/30 transition-all duration-300">
                                <div class="text-white mb-3">
                                    <i class="fas fa-file-waveform text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Historial Médico</h3>
                                <p class="text-teal-50">Acceso completo a tu historial clínico y resultados de exámenes</p>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 hover:bg-white/30 transition-all duration-300">
                                <div class="text-white mb-3">
                                    <i class="fas fa-hospital-user text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Atención Médica</h3>
                                <p class="text-teal-50">Solicitud de citas y consultas con especialistas</p>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 hover:bg-white/30 transition-all duration-300">
                                <div class="text-white mb-3">
                                    <i class="fas fa-comment-medical text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Atención Directa</h3>
                                <p class="text-teal-50">Comunicación directa con tu médico tratante</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>