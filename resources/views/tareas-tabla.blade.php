<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tabla de Tareas - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Estilos médicos personalizados */
        .medical-gradient {
            background: linear-gradient(135deg, #e6f7ff 0%, #cce7f8 100%);
            position: relative;
            overflow: hidden;
        }
        .medical-card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .medical-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #0891b2);
        }
        .medical-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
    </style>
    <script src="{{ asset('js/calendario.js') }}" defer></script>
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="tablaTareas">
                <!-- Título y botón de volver -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Tabla de Tareas</h1>
                    <a 
                        href="/tareas"
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                    >
                        <i class="fas fa-calendar"></i>
                        <span>Volver al Calendario</span>
                    </a>
                </div>

                <!-- Mensaje de estado -->
                <div 
                    x-show="message" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    :class="{
                        'bg-green-100 border-green-400 text-green-700': message?.type === 'success',
                        'bg-red-100 border-red-400 text-red-700': message?.type === 'error'
                    }"
                    class="border-l-4 p-4 mb-6"
                    @click="message = null"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i :class="{
                                'fas fa-check-circle': message?.type === 'success',
                                'fas fa-exclamation-circle': message?.type === 'error'
                            }"></i>
                        </div>
                        <div class="ml-3">
                            <p x-text="message?.text"></p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Tareas -->
                <div class="medical-card bg-white overflow-hidden">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Listado de Tareas</h2>
                        
                        <!-- Información de depuración -->
                        <div x-show="loading" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <p>Cargando tareas...</p>
                        </div>
                        
                        <div x-show="!loading && tareas.length === 0" class="mb-4 p-3 bg-yellow-50 text-yellow-700 rounded">
                            <p>No se encontraron tareas. Si acaba de crear tareas, verifique la consola del navegador para más información.</p>
                        </div>
                        
                        <!-- Datos crudos para depuración -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-md font-medium text-gray-700 mb-2">Datos crudos recibidos:</h3>
                            <pre x-text="JSON.stringify(tareasRaw, null, 2)" class="text-xs overflow-auto max-h-40 bg-gray-100 p-2 rounded"></pre>
                        </div>
                        
                        <!-- Tabla -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Día</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Inicio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Fin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="tarea in tareas" :key="tarea.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="tarea.id"></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full" :style="`background-color: ${tarea.color || '#4A90E2'}`">
                                                        <i :class="`text-white fas ${tarea.icono || 'fa-user-doctor'}`"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900" x-text="tarea.titulo"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="getDayName(tarea.dia_semana)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="tarea.hora_inicio"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="tarea.hora_fin"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" x-text="tarea.descripcion || 'Sin descripción'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <script type="text/javascript">
                document.addEventListener('alpine:init', () => {
                    Alpine.data('tablaTareas', () => ({
                        tareas: [],
                        tareasRaw: null, // Para mostrar los datos crudos recibidos
                        loading: false,
                        message: null,
                        days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                        
                        init() {
                            this.fetchTareas();
                        },
                        
                        fetchTareas() {
                            this.loading = true;
                            console.log('Obteniendo tareas para la tabla...');
                            fetch('/tareas-json')
                                .then(response => {
                                    if (!response.ok) {
                                        if (response.status === 401) {
                                            this.message = {
                                                type: 'error',
                                                text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
                                            };
                                            setTimeout(() => {
                                                window.location.href = '/';
                                            }, 2000);
                                            throw new Error('Sesión expirada');
                                        }
                                        throw new Error(`Error de red: ${response.status}`);
                                    }
                                    
                                    const contentType = response.headers.get('content-type');
                                    if (!contentType || !contentType.includes('application/json')) {
                                        return response.text().then(text => {
                                            console.error('Respuesta no JSON recibida:', text.substring(0, 150) + '...');
                                            throw new Error('La respuesta no es JSON válido.');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Datos recibidos para la tabla:', data);
                                    // Guardar los datos crudos para depuración
                                    this.tareasRaw = data;
                                    
                                    // Asegurarse de que data sea un array
                                    this.tareas = Array.isArray(data) ? data : [];
                                    
                                    if (this.tareas.length > 0) {
                                        this.message = {
                                            type: 'success',
                                            text: `Se han cargado ${this.tareas.length} tareas correctamente.`
                                        };
                                    } else {
                                        this.message = {
                                            type: 'warning',
                                            text: 'No se encontraron tareas en el sistema.'
                                        };
                                    }
                                    
                                    this.loading = false;
                                })
                                .catch(error => {
                                    console.error('Error al obtener tareas para la tabla:', error);
                                    this.message = {
                                        type: 'error',
                                        text: 'Ha ocurrido un error al cargar las tareas: ' + error.message
                                    };
                                    this.loading = false;
                                });
                        },
                        
                        getDayName(dayNumber) {
                            // Convertir a número y restar 1 para el índice del array
                            const index = parseInt(dayNumber) - 1;
                            return this.days[index] || 'Desconocido';
                        }
                    }));
                });
                </script>
            </main>
        </div>
    </div>
</body>
</html>