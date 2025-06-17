<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atención Directa - Clínica Ricardo Palma</title>
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
                            <h2 class="text-2xl font-bold text-gray-800">
                                @if(Auth::user()->rol_id == 3)
                                    Atención Directa - Pacientes Asignados
                                @else
                                    Mi Doctor Asignado
                                @endif
                            </h2>
                            <div class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</div>
                        </div>
                        
                        <!-- Información del doctor -->
                        <div class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Información del Doctor</h3>
                                    <p class="text-gray-700"><span class="font-medium">Nombre:</span> {{ $doctor->nombre_completo }}</p>
                                    <p class="text-gray-700"><span class="font-medium">Especialidad:</span> {{ $doctor->especialidad }}</p>
                                </div>
                                <div class="mt-4 md:mt-0">
                                    <p class="text-gray-700"><span class="font-medium">Teléfono:</span> 
                                        @if($doctor->telefono)
                                            <a href="tel:{{ $doctor->telefono }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                <i class="fas fa-phone-alt"></i> {{ $doctor->telefono }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">No registrado</span>
                                        @endif
                                    </p>
                                    <p class="text-gray-700"><span class="font-medium">Correo:</span> 
                                        @if($doctor->correo)
                                            <a href="mailto:{{ $doctor->correo }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                <i class="fas fa-envelope"></i> {{ $doctor->correo }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">No registrado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if(Auth::user()->rol_id == 3)
                            <!-- Lista de pacientes asignados (solo visible para doctores) -->
                            @if($pacientes->count() > 0)
                                <h3 class="text-xl font-semibold text-gray-700 mb-4">Pacientes Asignados</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                        <thead class="bg-gray-100 text-gray-700">
                                            <tr>
                                                <th class="py-3 px-4 text-left">Nombre</th>
                                                <th class="py-3 px-4 text-left">DNI</th>
                                                <th class="py-3 px-4 text-left">Teléfono</th>
                                                <th class="py-3 px-4 text-left">Correo</th>
                                                <th class="py-3 px-4 text-left">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($pacientes as $paciente)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-3 px-4 text-gray-700">{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}</td>
                                                    <td class="py-3 px-4 text-gray-700">{{ $paciente->dni }}</td>
                                                    <td class="py-3 px-4 text-gray-700">
                                                        @if($paciente->telefono)
                                                            <a href="tel:{{ $paciente->telefono }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                                <i class="fas fa-phone-alt"></i> {{ $paciente->telefono }}
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500">No registrado</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-gray-700">
                                                        @if($paciente->correo)
                                                            <a href="mailto:{{ $paciente->correo }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                                <i class="fas fa-envelope"></i> {{ $paciente->correo }}
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500">No registrado</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-gray-700 flex gap-2">
                                                        <a href="{{ route('historial.show', $paciente->id) }}" class="bg-cyan-100 hover:bg-cyan-200 text-cyan-700 font-semibold py-1 px-3 rounded shadow-sm border border-cyan-200 transition-all flex items-center gap-1" title="Ver historial">
                                                            <i class="fas fa-notes-medical"></i> Historial
                                                        </a>
                                                        <a href="{{ route('historial.create', $paciente->id) }}" class="bg-green-100 hover:bg-green-200 text-green-700 font-semibold py-1 px-3 rounded shadow-sm border border-green-200 transition-all flex items-center gap-1" title="Registrar historial">
                                                            <i class="fas fa-plus-circle"></i> Registrar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="text-cyan-500 text-5xl mb-4">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay pacientes asignados</h3>
                                    <p class="text-gray-500 mb-6">Aún no tienes pacientes asignados para atención directa.</p>
                                    <a href="{{ route('pacientes.index') }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 inline-flex items-center justify-center shadow-lg">
                                        <i class="fas fa-users mr-2"></i> Ver lista de pacientes
                                    </a>
                                </div>
                            @endif
                        @else
                            <!-- Información para pacientes -->
                            <div class="bg-indigo-50 p-6 rounded-lg mb-8 border border-indigo-100">
                                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de Contacto</h3>
                                <p class="text-gray-700 mb-4">Tu doctor te ha asignado para atención directa. Puedes contactarlo directamente para consultas médicas, seguimiento de tratamientos o cualquier duda sobre tu salud.</p>
                                
                                <div class="flex flex-col md:flex-row gap-4 mt-6">
                                    @if($doctor->telefono)
                                        <a href="tel:{{ $doctor->telefono }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 inline-flex items-center justify-center shadow-lg">
                                            <i class="fas fa-phone-alt mr-2"></i> Llamar ahora
                                        </a>
                                    @endif
                                    
                                    @if($doctor->correo)
                                        <a href="mailto:{{ $doctor->correo }}" class="bg-white text-indigo-600 border border-indigo-200 py-3 px-6 rounded-lg font-medium hover:bg-indigo-50 transition-all duration-300 inline-flex items-center justify-center shadow-sm">
                                            <i class="fas fa-envelope mr-2"></i> Enviar correo
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('citas.index') }}" class="bg-white text-green-600 border border-green-200 py-3 px-6 rounded-lg font-medium hover:bg-green-50 transition-all duration-300 inline-flex items-center justify-center shadow-sm">
                                        <i class="fas fa-calendar-check mr-2"></i> Agendar cita
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Recordatorio de citas -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recuerda</h3>
                                <ul class="space-y-3 text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Mantén actualizados tus datos de contacto para que tu doctor pueda comunicarse contigo.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Asiste puntualmente a tus citas programadas o notifica con anticipación si necesitas reprogramar.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Sigue las indicaciones médicas y el tratamiento prescrito por tu doctor.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Puedes revisar tu historial médico completo en la sección <a href="{{ route('historial.index') }}" class="text-blue-600 hover:underline">Historial</a>.</span>
                                    </li>
                                </ul>
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
            
            // Ocultar la notificación después de 5 segundos
            setTimeout(() => {
                closeNotification();
            }, 5000);
        }
        
        // Función para cerrar la notificación
        function closeNotification() {
            document.getElementById('notification_container').classList.add('hidden');
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