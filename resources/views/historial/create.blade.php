<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrar Historial Médico - Clínica Ricardo Palma</title>
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
                            <h2 class="text-2xl font-bold text-gray-800">Registrar Historial Médico</h2>
                            <div class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</div>
                        </div>

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

                        <!-- Formulario de registro de historial -->
                        <form id="historialForm" class="space-y-6">
                            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                            @if($cita)
                            <input type="hidden" name="cita_id" value="{{ $cita->id }}">
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha de consulta</label>
                                    <input type="date" id="fecha" name="fecha" value="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cyan-500 focus:border-cyan-500">
                                </div>

                                <div>
                                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo de consulta</label>
                                    <input type="text" id="motivo" name="motivo" value="{{ $cita ? $cita->motivo_consulta : '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cyan-500 focus:border-cyan-500" placeholder="Ej: Control rutinario, Dolor abdominal, etc." required>
                                </div>
                            </div>

                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción del malestar</label>
                                <textarea id="descripcion" name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cyan-500 focus:border-cyan-500" placeholder="Descripción detallada del malestar o síntomas del paciente">{{ $cita ? $cita->descripcion_malestar : '' }}</textarea>
                            </div>

                            <div>
                                <label for="diagnostico" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                                <textarea id="diagnostico" name="diagnostico" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cyan-500 focus:border-cyan-500" placeholder="Diagnóstico médico" required></textarea>
                            </div>

                            <div>
                                <label for="tratamiento" class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                                <textarea id="tratamiento" name="tratamiento" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cyan-500 focus:border-cyan-500" placeholder="Tratamiento recomendado"></textarea>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('pacientes.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-all flex items-center gap-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" id="submitBtn" class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-md hover:from-cyan-600 hover:to-blue-600 transition-all flex items-center gap-2">
                                    <i class="fas fa-save"></i> Guardar Historial
                                </button>
                            </div>
                        </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('historialForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('{{ route("historial.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });

                    const data = await response.json();

                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("historial.show", $paciente->id) }}';
                        }, 1500);
                    } else {
                        showNotification('Error al guardar el historial: ' + (data.message || 'Verifique los datos ingresados'), 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Historial';
                    }
                } catch (error) {
                    showNotification('Error al procesar la solicitud: ' + error.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Historial';
                }
            });
        });

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
    </script>
</body>
</html>