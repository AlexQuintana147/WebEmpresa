<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session('success'))
    <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if(session('error'))
    <meta name="error-message" content="{{ session('error') }}">
    @endif
    <title>Calendario Médico - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="{{ asset('js/calendario.js') }}" defer></script>
    <style>
        .time-slot {
            transition: all 0.2s ease;
        }
        .time-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .day-column {
            min-height: 600px;
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
            
            <!-- Contenido Principal -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Título de la página con decoración médica -->
                <div class="mb-8 text-center relative">
                    <h1 class="text-3xl font-bold text-gray-800">Calendario de Horarios Médicos</h1>
                    <p class="text-gray-600 mt-2">Gestione sus horarios de atención semanal</p>
                </div>
                
                <!-- Botón para agregar nuevo horario -->
                <div class="mb-6 flex justify-end">
                    <button id="btn-agregar-horario" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i> Agregar Horario
                    </button>
                </div>
                
                <!-- Calendario Semanal -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                    <!-- Cabecera de días -->
                    <div class="grid grid-cols-7 bg-blue-600 text-white font-semibold text-center py-3">
                        <div>Lunes</div>
                        <div>Martes</div>
                        <div>Miércoles</div>
                        <div>Jueves</div>
                        <div>Viernes</div>
                        <div>Sábado</div>
                        <div>Domingo</div>
                    </div>
                    
                    <!-- Contenido del calendario -->
                    <div class="grid grid-cols-7 divide-x divide-gray-200">
                        <!-- Columna para cada día -->
                        @for ($i = 1; $i <= 7; $i++)
                        <div class="day-column p-3 relative" data-day="{{ $i }}">
                            <!-- Aquí se cargarán dinámicamente los horarios -->
                            @foreach ($tareas as $tarea)
                                @if ($tarea->dia_semana == $i)
                                <div class="time-slot mb-2 p-2 rounded-lg cursor-pointer" 
                                     style="background-color: {{ $tarea->color }}" 
                                     data-id="{{ $tarea->id }}"
                                     data-titulo="{{ $tarea->titulo }}"
                                     data-descripcion="{{ $tarea->descripcion }}"
                                     data-dia="{{ $tarea->dia_semana }}"
                                     data-inicio="{{ $tarea->hora_inicio }}"
                                     data-fin="{{ $tarea->hora_fin }}"
                                     data-color="{{ $tarea->color }}"
                                     data-icono="{{ $tarea->icono }}">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-white">{{ $tarea->titulo }}</span>
                                        <i class="fas {{ $tarea->icono }} text-white"></i>
                                    </div>
                                    <div class="text-xs text-white mt-1">
                                        {{ substr($tarea->hora_inicio, 0, 5) }} - {{ substr($tarea->hora_fin, 0, 5) }}
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        @endfor
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Modal para Agregar/Editar Horario -->
    <div id="modal-horario" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="border-b px-6 py-4 flex justify-between items-center">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-800">Agregar Horario</h3>
                <button id="btn-cerrar-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-horario" class="px-6 py-4">
                <input type="hidden" id="tarea-id" name="id">
                @csrf
                
                <div class="mb-4">
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" id="titulo" name="titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="dia_semana" class="block text-sm font-medium text-gray-700 mb-1">Día de la Semana</label>
                    <select id="dia_semana" name="dia_semana" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-1">Hora de Inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-1">Hora de Fin</label>
                        <input type="time" id="hora_fin" name="hora_fin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="color" id="color" name="color" value="#4A90E2" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                        <select id="icono" name="icono" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="fa-user-md">Doctor</option>
                            <option value="fa-stethoscope">Estetoscopio</option>
                            <option value="fa-hospital">Hospital</option>
                            <option value="fa-heartbeat">Latido</option>
                            <option value="fa-pills">Medicamentos</option>
                            <option value="fa-procedures">Procedimiento</option>
                            <option value="fa-calendar-check">Cita</option>
                            <option value="fa-tasks">Tareas</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="btn-cancelar" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-guardar" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Eliminar -->
    <div id="modal-confirmar" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="border-b px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Confirmar Eliminación</h3>
            </div>
            
            <div class="px-6 py-4">
                <p class="text-gray-700">¿Está seguro que desea eliminar este horario? Esta acción no se puede deshacer.</p>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button id="btn-cancelar-eliminar" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button id="btn-confirmar-eliminar" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenedor para notificaciones -->
    <div id="notification_container" class="fixed top-4 right-4 z-50 max-w-md"></div>
</body>
</html>