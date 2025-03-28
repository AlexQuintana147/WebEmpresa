<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Actividades</title>
    <style>[x-cloak] { display: none !important; }</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Inicializar los stores directamente para evitar problemas de sincronización
        document.addEventListener('alpine:init', () => {
            // Inicializar el store modal globalmente
            Alpine.store('modal', {
                open: false,
                type: null, // 'create', 'edit', 'delete'
                item: null
            });
            
            console.log('Modal store inicializado:', Alpine.store('modal'));
        });
        
        document.addEventListener('alpine:init', () => {

            Alpine.data('actividades', () => ({
                pendientes: [],
                enProgreso: [],
                completadas: [],
                actividadSeleccionada: null,
                iconos: ['fa-tasks', 'fa-calendar', 'fa-handshake', 'fa-clock', 'fa-users', 'fa-file', 'fa-chart-bar', 'fa-envelope', 'fa-phone', 'fa-star'],
                colores: [
                    {nombre: 'Azul', valor: '#4A90E2'},
                    {nombre: 'Verde', valor: '#2ECC71'},
                    {nombre: 'Rojo', valor: '#E74C3C'},
                    {nombre: 'Amarillo', valor: '#F1C40F'},
                    {nombre: 'Morado', valor: '#9B59B6'},
                    {nombre: 'Naranja', valor: '#E67E22'},
                    {nombre: 'Turquesa', valor: '#1ABC9C'}
                ],
                nuevaActividad: {
                    titulo: '',
                    descripcion: '',
                    nivel: 'principal',
                    estado: 'pendiente',
                    fecha_limite: '',
                    hora_limite: '',
                    color: '#4A90E2',
                    icono: 'fa-tasks',
                    prioridad: 1,
                    actividad_padre_id: null
                },
                
                init() {
                    this.cargarActividades();
                    // Asegurarse de que el store modal esté disponible en el contexto de Alpine
                    if (!Alpine.store('modal')) {
                        Alpine.store('modal', {
                            open: false,
                            type: null,
                            item: null
                        });
                    }
                    console.log('Modal store verificado en init de actividades:', Alpine.store('modal'));
                },
                
                cargarActividades() {
                    @if(Auth::check() && isset($actividades))
                        const actividades = @json($actividades);
                        this.pendientes = actividades.filter(a => a.estado === 'pendiente');
                        this.enProgreso = actividades.filter(a => a.estado === 'en_progreso');
                        this.completadas = actividades.filter(a => a.estado === 'completada');
                    @endif
                },
                
                abrirModalCrear() {
                    this.resetearNuevaActividad();
                    Alpine.store('modal').type = 'create';
                    Alpine.store('modal').open = true;
                    console.log('Modal crear abierto:', Alpine.store('modal'));
                },
                
                abrirModalEditar(actividad) {
                    this.actividadSeleccionada = {...actividad};
                    Alpine.store('modal').type = 'edit';
                    Alpine.store('modal').item = actividad;
                    Alpine.store('modal').open = true;
                    this.modalOpen = true; // Añadir esta línea para abrir el modal
                    console.log('Modal editar abierto:', Alpine.store('modal'));
                },
                
                abrirModalEliminar(actividad) {
                    this.actividadSeleccionada = actividad;
                    Alpine.store('modal').type = 'delete';
                    Alpine.store('modal').item = actividad;
                    Alpine.store('modal').open = true;
                    console.log('Modal eliminar abierto:', Alpine.store('modal'));
                },
                
                resetearNuevaActividad() {
                    this.nuevaActividad = {
                        titulo: '',
                        descripcion: '',
                        nivel: 'principal',
                        estado: 'pendiente',
                        fecha_limite: '',
                        hora_limite: '',
                        color: '#4A90E2',
                        icono: 'fa-tasks',
                        prioridad: 1,
                        actividad_padre_id: null
                    };
                },
                
                cambiarEstado(actividad, nuevoEstado) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/actividades/${actividad.id}/cambiar-estado`;
                    form.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';
                    
                    const estadoInput = document.createElement('input');
                    estadoInput.type = 'hidden';
                    estadoInput.name = 'estado';
                    estadoInput.value = nuevoEstado;
                    
                    form.appendChild(csrfToken);
                    form.appendChild(method);
                    form.appendChild(estadoInput);
                    document.body.appendChild(form);
                    form.submit();
                },
                
                obtenerClasePrioridad(prioridad) {
                    switch(parseInt(prioridad)) {
                        case 3: return 'bg-red-100 text-red-600';
                        case 2: return 'bg-yellow-100 text-yellow-600';
                        case 1: default: return 'bg-blue-100 text-blue-600';
                    }
                },
                
                obtenerTextoPrioridad(prioridad) {
                    switch(parseInt(prioridad)) {
                        case 3: return 'Alta';
                        case 2: return 'Media';
                        case 1: default: return 'Baja';
                    }
                }
            }));
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex" x-data="{ modalOpen: false }">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Main Content Area -->
            <main class="p-6" x-data="actividades">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Title and Add Button -->
                    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="text-center md:text-left mb-4 md:mb-0">
                            <h1 class="text-4xl font-bold text-gray-800 mb-2">Lista de Actividades</h1>
                            <p class="text-xl text-gray-600">Gestiona y organiza tus tareas de manera eficiente</p>
                        </div>
                        @auth
                        <button @click="abrirModalCrear(); modalOpen = true; $store.modal.type = 'create';" class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>
                            <span>Nueva Actividad</span>
                        </button>
                        @endauth
                    </div>

                    <!-- Task Management Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Pending Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-400">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold flex items-center">
                                    <div class="p-2 bg-yellow-100 rounded-full mr-3">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                    Pendientes
                                </h2>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium" x-text="pendientes.length + ' tareas'"></span>
                            </div>
                            <div class="space-y-4">
                                <template x-if="pendientes.length === 0">
                                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                                        <p class="text-gray-500">No hay actividades pendientes</p>
                                    </div>
                                </template>
                                
                                <template x-for="actividad in pendientes" :key="actividad.id">
                                    <div class="p-4 bg-gray-50 rounded-lg hover:shadow-md transition-all duration-200 border-l-4" :style="`border-color: ${actividad.color}`">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="font-medium flex items-center">
                                                <i class="fas mr-2" :class="actividad.icono"></i>
                                                <span x-text="actividad.titulo"></span>
                                            </h3>
                                            <span class="px-2 py-1 rounded-full text-xs" :class="obtenerClasePrioridad(actividad.prioridad)" x-text="obtenerTextoPrioridad(actividad.prioridad)"></span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3" x-text="actividad.descripcion"></p>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span x-show="actividad.fecha_limite">
                                                <i class="far fa-calendar mr-1"></i> 
                                                <span x-text="actividad.fecha_limite"></span>
                                                <span x-show="actividad.hora_limite">
                                                    <i class="far fa-clock ml-2 mr-1"></i>
                                                    <span x-text="actividad.hora_limite"></span>
                                                </span>
                                            </span>
                                            <div class="flex space-x-2">
                                                <button @click="cambiarEstado(actividad, 'en_progreso')" class="p-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition-colors" title="Mover a En Progreso">
                                                    <i class="fas fa-spinner"></i>
                                                </button>
                                                <button @click="abrirModalEditar(actividad)" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="abrirModalEliminar(actividad)" class="p-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- In Progress Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-400">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-full mr-3">
                                        <i class="fas fa-spinner text-blue-600"></i>
                                    </div>
                                    En Progreso
                                </h2>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium" x-text="enProgreso.length + ' tareas'"></span>
                            </div>
                            <div class="space-y-4">
                                <template x-if="enProgreso.length === 0">
                                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                                        <p class="text-gray-500">No hay actividades en progreso</p>
                                    </div>
                                </template>
                                
                                <template x-for="actividad in enProgreso" :key="actividad.id">
                                    <div class="p-4 bg-gray-50 rounded-lg hover:shadow-md transition-all duration-200 border-l-4" :style="`border-color: ${actividad.color}`">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="font-medium flex items-center">
                                                <i class="fas mr-2" :class="actividad.icono"></i>
                                                <span x-text="actividad.titulo"></span>
                                            </h3>
                                            <span class="px-2 py-1 rounded-full text-xs" :class="obtenerClasePrioridad(actividad.prioridad)" x-text="obtenerTextoPrioridad(actividad.prioridad)"></span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3" x-text="actividad.descripcion"></p>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span x-show="actividad.fecha_limite">
                                                <i class="far fa-calendar mr-1"></i> 
                                                <span x-text="actividad.fecha_limite"></span>
                                                <span x-show="actividad.hora_limite">
                                                    <i class="far fa-clock ml-2 mr-1"></i>
                                                    <span x-text="actividad.hora_limite"></span>
                                                </span>
                                            </span>
                                            <div class="flex space-x-2">
                                                <button @click="cambiarEstado(actividad, 'completada')" class="p-1 bg-green-100 text-green-600 rounded hover:bg-green-200 transition-colors" title="Mover a Completadas">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button @click="cambiarEstado(actividad, 'pendiente')" class="p-1 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200 transition-colors" title="Mover a Pendientes">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                                <button @click="abrirModalEditar(actividad)" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="abrirModalEliminar(actividad)" class="p-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Completed Tasks -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-400">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold flex items-center">
                                    <div class="p-2 bg-green-100 rounded-full mr-3">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    Completadas
                                </h2>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium" x-text="completadas.length + ' tareas'"></span>
                            </div>
                            <div class="space-y-4">
                                <template x-if="completadas.length === 0">
                                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                                        <p class="text-gray-500">No hay actividades completadas</p>
                                    </div>
                                </template>
                                
                                <template x-for="actividad in completadas" :key="actividad.id">
                                    <div class="p-4 bg-gray-50 rounded-lg hover:shadow-md transition-all duration-200 border-l-4 opacity-75" :style="`border-color: ${actividad.color}`">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="font-medium flex items-center line-through">
                                                <i class="fas mr-2" :class="actividad.icono"></i>
                                                <span x-text="actividad.titulo"></span>
                                            </h3>
                                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Completada</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3" x-text="actividad.descripcion"></p>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span x-show="actividad.fecha_limite">
                                                <i class="far fa-calendar mr-1"></i> 
                                                <span x-text="actividad.fecha_limite"></span>
                                            </span>
                                            <div class="flex space-x-2">
                                                <button @click="cambiarEstado(actividad, 'pendiente')" class="p-1 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200 transition-colors" title="Mover a Pendientes">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                                <button @click="abrirModalEditar(actividad)" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="abrirModalEliminar(actividad)" class="p-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Create/Edit Modal -->
                <div x-show="modalOpen" x-cloak @click.away="modalOpen = false" 
                     class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
                    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl transform transition-all duration-300" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100">
                        <!-- Header with gradient background -->
                        <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                <span class="bg-gradient-to-r from-blue-500 to-purple-600 h-8 w-1 rounded-full mr-3"></span>
                                <span x-text="$store.modal.type === 'create' ? 'Nueva Actividad' : 'Editar Actividad'"></span>
                            </h3>
                            <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors duration-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <form x-bind:action="$store.modal.type === 'create' ? '{{ route("actividades.store") }}' : '/actividades/' + $store.modal.item?.id" method="POST" class="space-y-5">
                            @csrf
                            <template x-if="$store.modal.type === 'edit'">
                                @method('PUT')
                            </template>
                            
                            <!-- Título field with icon -->
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-heading text-blue-500 mr-2"></i>
                                    Título
                                </label>
                                <div class="relative">
                                    <input type="text" name="titulo" 
                                           x-bind:value="$store.modal.type === 'create' ? nuevaActividad.titulo : $store.modal.item?.titulo"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none" 
                                           placeholder="Título de la actividad" required>
                                </div>
                            </div>
                            
                            <!-- Descripción field with icon -->
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-align-left text-purple-500 mr-2"></i>
                                    Descripción
                                </label>
                                <div class="relative">
                                    <textarea name="descripcion" 
                                              x-bind:value="$store.modal.type === 'create' ? nuevaActividad.descripcion : $store.modal.item?.descripcion"
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 h-24 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none" 
                                              placeholder="Descripción de la actividad"></textarea>
                                </div>
                            </div>
                            
                            <!-- Nivel y Estado fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-layer-group text-green-500 mr-2"></i>
                                        Nivel
                                    </label>
                                    <div class="relative">
                                        <select name="nivel" 
                                                x-bind:value="$store.modal.type === 'create' ? nuevaActividad.nivel : $store.modal.item?.nivel"
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none bg-white">
                                            <option value="principal">Principal</option>
                                            <option value="secundaria">Secundaria</option>
                                            <option value="terciaria">Terciaria</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-tasks-alt text-orange-500 mr-2"></i>
                                        Estado
                                    </label>
                                    <div class="relative">
                                        <select name="estado" 
                                                x-bind:value="$store.modal.type === 'create' ? nuevaActividad.estado : $store.modal.item?.estado"
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 outline-none bg-white">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completada">Completada</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fecha y Hora fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar-day text-red-500 mr-2"></i>
                                        Fecha Límite
                                    </label>
                                    <div class="relative">
                                        <input type="date" name="fecha_limite" 
                                               x-bind:value="$store.modal.type === 'create' ? nuevaActividad.fecha_limite : $store.modal.item?.fecha_limite"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 outline-none">
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-clock text-indigo-500 mr-2"></i>
                                        Hora Límite
                                    </label>
                                    <div class="relative">
                                        <input type="time" name="hora_limite" 
                                               x-bind:value="$store.modal.type === 'create' ? nuevaActividad.hora_limite : $store.modal.item?.hora_limite"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Prioridad field -->
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-flag text-pink-500 mr-2"></i>
                                    Prioridad
                                </label>
                                <div class="relative">
                                    <select name="prioridad" 
                                            x-bind:value="$store.modal.type === 'create' ? nuevaActividad.prioridad : $store.modal.item?.prioridad"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 appearance-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 outline-none bg-white">
                                        <option value="1">Baja</option>
                                        <option value="2">Media</option>
                                        <option value="3">Alta</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Color field with visual color indicators -->
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-palette text-yellow-500 mr-2"></i>
                                    Color
                                </label>
                                <div class="grid grid-cols-7 gap-2">
                                    <template x-for="color in colores" :key="color.valor">
                                        <button type="button" 
                                                @click="$store.modal.type === 'create' ? nuevaActividad.color = color.valor : $store.modal.item.color = color.valor"
                                                :class="{
                                                    'ring-2 ring-offset-2 ring-gray-800': 
                                                        ($store.modal.type === 'create' && nuevaActividad.color === color.valor) || 
                                                        ($store.modal.type === 'edit' && $store.modal.item?.color === color.valor)
                                                }"
                                                class="w-full h-10 rounded-full focus:outline-none transition-all duration-200"
                                                :style="`background-color: ${color.valor}`">
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="color" 
                                       x-bind:value="$store.modal.type === 'create' ? nuevaActividad.color : $store.modal.item?.color">
                            </div>
                            
                            <!-- Icono field with visual icon indicators -->
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-icons text-blue-500 mr-2"></i>
                                    Icono
                                </label>
                                <div class="grid grid-cols-5 gap-2">
                                    <template x-for="icono in iconos" :key="icono">
                                        <button type="button" 
                                                @click="$store.modal.type === 'create' ? nuevaActividad.icono = icono : $store.modal.item.icono = icono"
                                                :class="{
                                                    'bg-blue-100 ring-2 ring-blue-500': 
                                                        ($store.modal.type === 'create' && nuevaActividad.icono === icono) || 
                                                        ($store.modal.type === 'edit' && $store.modal.item?.icono === icono)
                                                }"
                                                class="p-3 rounded-lg focus:outline-none transition-all duration-200 bg-gray-100 hover:bg-gray-200">
                                            <i class="fas text-lg" :class="icono"></i>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="icono" 
                                       x-bind:value="$store.modal.type === 'create' ? nuevaActividad.icono : $store.modal.item?.icono">
                            </div>
                            
                            <!-- Submit buttons -->
                            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                                <button type="button" @click="modalOpen = false" 
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-colors duration-200">
                                    <span x-text="$store.modal.type === 'create' ? 'Crear Actividad' : 'Guardar Cambios'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Delete Confirmation Modal -->
                <div x-show="$store.modal.open && $store.modal.type === 'delete'" 
                     class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
                    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl transform transition-all duration-300" 
                         @click.away="$store.modal.open = false"
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100">
                        <!-- Header with gradient background -->
                        <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                <span class="bg-gradient-to-r from-red-500 to-pink-600 h-8 w-1 rounded-full mr-3"></span>
                                <span>Eliminar Actividad</span>
                            </h3>
                            <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors duration-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-gray-600 mb-4">¿Estás seguro de que deseas eliminar esta actividad? Esta acción no se puede deshacer.</p>
                            <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-yellow-400">
                                <h4 class="font-medium text-gray-800 mb-1" x-text="Alpine.store('modal').item?.titulo"></h4>
                                <p class="text-gray-600 text-sm" x-text="Alpine.store('modal').item?.descripcion"></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button @click="Alpine.store('modal').open = false" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                Cancelar
                            </button>
                            <form x-bind:action="'/actividades/' + Alpine.store('modal').item?.id" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-colors duration-200">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>