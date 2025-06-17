<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Historial Médico - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Estilos médicos personalizados */
        .medical-gradient {
            background: linear-gradient(135deg, #e6f7ff 0%, #cce7f8 100%);
        }
        .medical-card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 150, 199, 0.1);
            border: 1px solid rgba(0, 150, 199, 0.1);
        }
        .medical-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }
        .timeline-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #0ea5e9;
            position: absolute;
            left: -10px;
            top: 0;
        }
        .timeline-line {
            position: absolute;
            left: 0;
            top: 20px;
            bottom: 0;
            width: 1px;
            background-color: #0ea5e9;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Contenido principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <x-header />
            <!-- Contenido principal -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="container mx-auto">
                    <!-- Tarjeta principal -->
                    <div class="medical-card bg-white p-8 mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Historial Médico</h2>
                            <div class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</div>
                        </div>

                        @if(!$paciente)
                            <div class="text-center py-12">
                                <div class="text-cyan-500 text-5xl mb-4">
                                    <i class="fas fa-file-medical-alt"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontró información de paciente</h3>
                                <p class="text-gray-500 mb-6">No hay un paciente asociado a tu cuenta de usuario.</p>
                                <a href="{{ route('atencionmedica.index') }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 inline-flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user-plus mr-2"></i> Registrarse como paciente
                                </a>
                            </div>
                        @elseif($historiales && $historiales->count() > 0)
                            <!-- Información del paciente -->
                            <div class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-100">
                                <div class="flex flex-col md:flex-row md:items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Información del Paciente</h3>
                                        <p class="text-gray-700"><span class="font-medium">Nombre:</span> {{ $paciente->nombre_completo }}</p>
                                        <p class="text-gray-700"><span class="font-medium">DNI:</span> {{ $paciente->dni }}</p>
                                        <p class="text-gray-700"><span class="font-medium">Correo:</span> {{ $paciente->correo ?: 'No registrado' }}</p>
                                    </div>
                                    <div class="mt-4 md:mt-0">
                                        <p class="text-gray-700"><span class="font-medium">Teléfono:</span> {{ $paciente->telefono ?: 'No registrado' }}</p>
                                        <p class="text-gray-700"><span class="font-medium">Doctor asignado:</span> {{ $paciente->doctor ? $paciente->doctor->nombre . ' ' . $paciente->doctor->apellido_paterno : 'No asignado' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Línea de tiempo de historiales -->
                            <div class="relative pl-8 space-y-8">
                                @foreach($historiales as $historial)
                                <div class="relative">
                                    <div class="timeline-dot"></div>
                                    @if(!$loop->last)
                                    <div class="timeline-line"></div>
                                    @endif
                                    <div class="medical-card bg-white p-6 mb-4 ml-4">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-blue-800">{{ $historial->motivo }}</h3>
                                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($historial->fecha)->format('d/m/Y') }}</p>
                                            </div>
                                            @if($historial->cita)
                                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Cita #{{ $historial->cita_id }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="space-y-4">
                                            @if($historial->descripcion)
                                            <div>
                                                <h4 class="text-md font-medium text-gray-700 mb-1">Descripción</h4>
                                                <p class="text-gray-600">{{ $historial->descripcion }}</p>
                                            </div>
                                            @endif
                                            
                                            @if($historial->diagnostico)
                                            <div>
                                                <h4 class="text-md font-medium text-gray-700 mb-1">Diagnóstico</h4>
                                                <p class="text-gray-600">{{ $historial->diagnostico }}</p>
                                            </div>
                                            @endif
                                            
                                            @if($historial->tratamiento)
                                            <div>
                                                <h4 class="text-md font-medium text-gray-700 mb-1">Tratamiento</h4>
                                                <p class="text-gray-600">{{ $historial->tratamiento }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="text-cyan-500 text-5xl mb-4">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay historiales médicos</h3>
                                <p class="text-gray-500 mb-6">Aún no tienes registros en tu historial médico.</p>
                                <a href="{{ route('citas.index') }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 inline-flex items-center justify-center shadow-lg">
                                    <i class="fas fa-calendar-plus mr-2"></i> Agendar una cita
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Notificaciones -->
    <div id="notification_container" class="fixed bottom-0 right-0 m-6 w-full max-w-sm overflow-hidden rounded-lg shadow-lg hidden">
        <div id="notification" class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i id="notification_icon"></i>
                </div>
                <div class="ml-3">
                    <p id="notification_text" class="text-sm font-medium"></p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="closeNotification()" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para mostrar notificaciones
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification_container');
            const notification = document.getElementById('notification');
            const text = document.getElementById('notification_text');
            const icon = document.getElementById('notification_icon');
            
            text.textContent = message;
            
            // Configurar el tipo de notificación
            if (type === 'success') {
                notification.className = 'p-4 bg-green-50 border-l-4 border-green-400';
                text.className = 'text-sm font-medium text-green-800';
                icon.className = 'fas fa-check-circle text-green-400';
            } else if (type === 'error') {
                notification.className = 'p-4 bg-red-50 border-l-4 border-red-400';
                text.className = 'text-sm font-medium text-red-800';
                icon.className = 'fas fa-exclamation-circle text-red-400';
            } else if (type === 'warning') {
                notification.className = 'p-4 bg-yellow-50 border-l-4 border-yellow-400';
                text.className = 'text-sm font-medium text-yellow-800';
                icon.className = 'fas fa-exclamation-triangle text-yellow-400';
            } else if (type === 'info') {
                notification.className = 'p-4 bg-blue-50 border-l-4 border-blue-400';
                text.className = 'text-sm font-medium text-blue-800';
                icon.className = 'fas fa-info-circle text-blue-400';
            }
            
            container.classList.remove('hidden');
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                closeNotification();
            }, 5000);
        }
        
        // Función para cerrar la notificación
        function closeNotification() {
            const container = document.getElementById('notification_container');
            container.classList.add('hidden');
        }
        
        // Mostrar notificación si hay mensaje de sesión
        @if(session('success'))
            showNotification("{{ session('success') }}", 'success');
        @endif
        
        @if(session('error'))
            showNotification("{{ session('error') }}", 'error');
        @endif
        
        @if(session('warning'))
            showNotification("{{ session('warning') }}", 'warning');
        @endif
        
        @if(session('info'))
            showNotification("{{ session('info') }}", 'info');
        @endif
    </script>
</body>
</html>