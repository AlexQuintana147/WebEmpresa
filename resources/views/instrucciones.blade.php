<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía del Sistema - Clínica Ricardo Palma</title>
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
    <style>
        @keyframes pulse-medical {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .animate-pulse-medical {
            animation: pulse-medical 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .medical-gradient {
            background-image: linear-gradient(to right, #0891b2, #0284c7);
        }
        .medical-card-hover:hover {
            box-shadow: 0 0 25px rgba(8, 145, 178, 0.2);
        }
    </style>
</head>
<body class="bg-blue-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />

            <!-- Instructions Content -->
            <main class="p-6 bg-gradient-to-br from-blue-50 via-blue-100/30 to-cyan-100/30">
                <div class="max-w-7xl mx-auto">
                    <!-- Welcome Section -->
                    <div class="mb-10 text-center bg-white rounded-2xl shadow-lg p-8 border border-cyan-100">
                        <div class="inline-block p-3 rounded-full bg-cyan-100 mb-4 animate-pulse-medical">
                            <i class="fas fa-hospital text-4xl text-cyan-600"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenido al Sistema de la Clínica Ricardo Palma</h1>
                        <p class="text-xl text-gray-600">Una solución integral para la gestión médica, administrativa y de pacientes</p>
                        <div class="mt-6 inline-block px-6 py-3 bg-gradient-to-r from-cyan-600 to-teal-600 text-white rounded-full shadow-md">
                            <span class="flex items-center"><i class="fas fa-stethoscope mr-2"></i> Excelencia en atención médica</span>
                        </div>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <!-- Financial Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105 border-t-4 border-cyan-500 medical-card-hover">
                            <div class="text-cyan-600 mb-4 bg-cyan-100 inline-block p-3 rounded-full">
                                <i class="fas fa-file-invoice-dollar text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión Financiera</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-cyan-500 mr-2"></i>
                                    Control de ingresos y gastos médicos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-cyan-500 mr-2"></i>
                                    Categorización de transacciones clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-cyan-500 mr-2"></i>
                                    Reportes financieros del centro médico
                                </li>
                            </ul>
                        </div>

                        <!-- Task Management -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105 border-t-4 border-teal-500 medical-card-hover">
                            <div class="text-teal-600 mb-4 bg-teal-100 inline-block p-3 rounded-full">
                                <i class="fas fa-clipboard-list text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión de Actividades</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-teal-500 mr-2"></i>
                                    Seguimiento de procedimientos médicos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-teal-500 mr-2"></i>
                                    Organización de actividades clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-teal-500 mr-2"></i>
                                    Priorización de tareas médicas
                                </li>
                            </ul>
                        </div>

                        <!-- Calendar -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105 border-t-4 border-blue-500 medical-card-hover">
                            <div class="text-blue-600 mb-4 bg-blue-100 inline-block p-3 rounded-full">
                                <i class="fas fa-calendar-plus text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Calendario Médico</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Programación de citas médicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Recordatorios de consultas y tratamientos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Organización semanal de horarios
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Additional Features -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <!-- Notes System -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105 border-t-4 border-emerald-500 medical-card-hover">
                            <div class="text-emerald-600 mb-4 bg-emerald-100 inline-block p-3 rounded-full">
                                <i class="fas fa-notes-medical text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Historias Clínicas</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                    Registro digital de historias clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                    Organización por especialidades médicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                    Acceso rápido a información del paciente
                                </li>
                            </ul>
                        </div>

                        <!-- AI Assistant -->
                        <div class="bg-white rounded-xl shadow-lg p-6 transform transition-all duration-300 hover:scale-105 border-t-4 border-indigo-500 medical-card-hover">
                            <div class="text-indigo-600 mb-4 bg-indigo-100 inline-block p-3 rounded-full">
                                <i class="fas fa-user-md text-4xl"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Asistente Médico Virtual</h2>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                                    Consultas médicas preliminares asistidas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                                    Ayuda en la toma de decisiones clínicas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                                    Respuestas a preguntas frecuentes de pacientes
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>