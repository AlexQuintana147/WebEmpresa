<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Pacientes</title>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Estilos médicos personalizados */
        .medical-gradient {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        }
        .medical-card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .medical-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-blue-50">
    <!-- Componente de Notificación -->
    <div x-data x-cloak
         x-show="$store.notification.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
         :class="{
            'bg-green-50': $store.notification.type === 'success',
            'bg-red-50': $store.notification.type === 'error',
            'bg-amber-50': $store.notification.type === 'warning'
         }">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas" :class="{
                        'fa-check-circle text-green-400': $store.notification.type === 'success',
                        'fa-exclamation-circle text-red-400': $store.notification.type === 'error',
                        'fa-exclamation-triangle text-amber-400': $store.notification.type === 'warning'
                    }"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium" :class="{
                        'text-green-800': $store.notification.type === 'success',
                        'text-red-800': $store.notification.type === 'error',
                        'text-amber-800': $store.notification.type === 'warning'
                    }" x-text="$store.notification.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="$store.notification.show = false" class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Cerrar</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="min-h-screen flex" x-data="{ modalOpen: false }">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Main Content Area -->
            <div class="container mx-auto px-4 py-8" x-data>
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-blue-800 flex items-center">
                        <i class="fas fa-user-md mr-3"></i>
                        <span>Gestión de Pacientes</span>
                    </h1>
                    <button 
                        @click="$store.actividades.openModal('create')"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg 
                               hover:from-blue-600 hover:to-blue-700
                               focus:ring-4 focus:ring-blue-300/50
                               shadow-md hover:shadow-xl
                               transform hover:-translate-y-0.5
                               transition-all duration-300 ease-out
                               active:scale-95">
                        <span class="flex items-center space-x-2">
                            <i class="fas fa-user-plus"></i>
                            <span>Nuevo Paciente</span>
                        </span>
                    </button>
                </div>

                <!-- Contenedor principal de columnas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna: Pacientes Pendientes de Diagnóstico -->
                    <div class="bg-indigo-50 rounded-xl p-4 shadow-md border border-indigo-100">
                        <h2 class="text-xl font-bold text-indigo-800 mb-4 flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i> Pendientes de Diagnóstico
                        </h2>
                        <div class="space-y-4">
                            @if(Auth::check())
                                @forelse($actividadesPendientes as $actividad)
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 medical-card" style="border-color: {{ $actividad->color }}">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color: {{ $actividad->color }}">
                                                <i class="fas {{ $actividad->icono }} text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-semibold text-blue-800">{{ $actividad->titulo }}</h3>
                                                <span class="text-xs text-gray-500">ID: #{{ $actividad->id }}</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button 
                                                @click="$store.actividades.openModal('edit', {{ json_encode($actividad) }})"
                                                class="text-gray-500 hover:text-amber-500 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                @click="$store.actividades.deleteActivity({{ $actividad->id }})"
                                                class="text-gray-500 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-gray-600">{{ $actividad->descripcion }}</p>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                                              style="background-color: {{ $actividad->color }}20; color: {{ $actividad->color }}">
                                            {{ ucfirst($actividad->nivel) }}
                                        </span>
                                        
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            {{ $actividad->estado == 'pendiente' ? 'bg-indigo-100 text-indigo-800' : 
                                               ($actividad->estado == 'en_progreso' ? 'bg-teal-100 text-teal-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $actividad->estado == 'pendiente' ? 'Pendiente de diagnóstico' : 
                                               ($actividad->estado == 'en_progreso' ? 'En tratamiento' : 'Tratamiento completado') }}
                                        </span>
                                        
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                               ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                            Urgencia: {{ $actividad->prioridad == 1 ? 'Rutina' : ($actividad->prioridad == 2 ? 'Preferente' : 'Urgente') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex items-center space-x-1 text-gray-500">
                                            <i class="far fa-calendar-alt"></i>
                                            <span class="text-sm">{{ \Carbon\Carbon::parse($actividad->fecha_limite)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-gray-500">
                                            <i class="far fa-clock"></i>
                                            <span class="text-sm">{{ $actividad->hora_limite }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-2">
                                            <button 
                                                @click="$store.actividades.changeStatus({{ $actividad->id }}, 'pendiente')"
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors"
                                                {{ $actividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                <i class="fas fa-clipboard-list mr-1"></i> Diagnóstico
                                            </button>
                                            <button 
                                                @click="$store.actividades.changeStatus({{ $actividad->id }}, 'en_progreso')"
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-teal-100 text-teal-800 hover:bg-teal-200 transition-colors"
                                                {{ $actividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                <i class="fas fa-procedures mr-1"></i> Tratamiento
                                            </button>
                                            <button 
                                                @click="$store.actividades.changeStatus({{ $actividad->id }}, 'completada')"
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition-colors"
                                                {{ $actividad->estado == 'completada' ? 'disabled' : '' }}>
                                                <i class="fas fa-check-circle mr-1"></i> Tratamiento Completado
                                            </button>
                                        </div>
                                        <button 
                                            @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                            class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                            <i class="fas fa-prescription mr-1"></i> Añadir Tratamiento
                                        </button>
                                    </div>
                                    
                                    <!-- Subactividades -->
                                    @if($actividad->actividadesHijas && $actividad->actividadesHijas->count() > 0)
                                        <div class="mt-4">
                                            <button 
                                                @click="$store.actividades.toggleSubactivities({{ $actividad->id }})"
                                                class="flex items-center space-x-2 text-sm text-gray-600 hover:text-amber-500 transition-colors">
                                                <span>{{ $actividad->actividadesHijas->count() }} Subactividades</span>
                                                <i id="toggle-icon-{{ $actividad->id }}" class="fas fa-chevron-down"></i>
                                            </button>
                                            
                                            <div id="subactivities-{{ $actividad->id }}" class="mt-3 pl-4 border-l-2 border-gray-200 hidden">
                                                @foreach($actividad->actividadesHijas as $subactividad)
                                                    <div class="mb-3 p-3 bg-gray-50 rounded-lg border-l-4" style="border-color: {{ $subactividad->color }}">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <div class="flex items-center space-x-2">
                                                                <div class="flex items-center justify-center w-6 h-6 rounded" style="background-color: {{ $subactividad->color }}">
                                                                    <i class="fas {{ $subactividad->icono }} text-white text-xs"></i>
                                                                </div>
                                                                <h4 class="font-medium text-gray-800">{{ $subactividad->titulo }}</h4>
                                                            </div>
                                                            <div class="flex space-x-1">
                                                                <button 
                                                                    @click="$store.actividades.openModal('edit', {{ json_encode($subactividad) }})"
                                                                    class="text-gray-500 hover:text-amber-500 transition-colors text-xs">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button 
                                                                    @click="$store.actividades.deleteActivity({{ $subactividad->id }})"
                                                                    class="text-gray-500 hover:text-red-500 transition-colors text-xs">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex flex-wrap gap-1 mb-2">
                                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                {{ $subactividad->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                                                   ($subactividad->estado == 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                                {{ $subactividad->estado == 'pendiente' ? 'Pendiente' : 
                                                                   ($subactividad->estado == 'en_progreso' ? 'En tratamiento' : 'Tratamiento completado') }}
                                                            </span>
                                                            
                                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                {{ $subactividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                                   ($subactividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                P{{ $subactividad->prioridad }}
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                                            <div class="flex items-center space-x-1">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($subactividad->fecha_limite)->format('d/m/Y') }}</span>
                                                            </div>
                                                            <div class="flex space-x-1">
                                                                <button 
                                                                    @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'pendiente')"
                                                                    class="hover:text-yellow-600 transition-colors"
                                                                    {{ $subactividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                                    <i class="fas fa-circle text-yellow-500"></i>
                                                                </button>
                                                                <button 
                                                                    @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'en_progreso')"
                                                                    class="hover:text-blue-600 transition-colors"
                                                                    {{ $subactividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                                    <i class="fas fa-circle text-blue-500"></i>
                                                                </button>
                                                                <button 
                                                                    @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'completada')"
                                                                    class="hover:text-green-600 transition-colors"
                                                                    {{ $subactividad->estado == 'completada' ? 'disabled' : '' }}>
                                                                    <i class="fas fa-circle text-green-500"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                                @empty
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 text-center medical-card">
                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-indigo-500 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-blue-800">No hay pacientes pendientes de diagnóstico</h3>
                                        </div>
                                    </div>
                                @endforelse
                                
                                <!-- Paginación de Actividades Pendientes -->
                                @if($actividadesPendientes->hasPages())
                                    <div class="flex justify-center items-center space-x-2 mt-4 pt-2 border-t border-gray-200">
                                        <!-- Botón Anterior -->
                                        <a href="{{ $actividadesPendientes->previousPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ $actividadesPendientes->onFirstPage() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </a>
                                        
                                        <!-- Números de Página -->
                                        @for($i = 1; $i <= $actividadesPendientes->lastPage(); $i++)
                                            <a href="{{ $actividadesPendientes->url($i) }}" 
                                               class="px-3 py-1 rounded-md {{ $i == $actividadesPendientes->currentPage() ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                                                {{ $i }}
                                            </a>
                                        @endfor
                                        
                                        <!-- Botón Siguiente -->
                                        <a href="{{ $actividadesPendientes->nextPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ !$actividadesPendientes->hasMorePages() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Columna: Pacientes En Tratamiento -->
                        <div class="bg-teal-50 rounded-xl p-4 shadow-md border border-teal-100">
                            <h2 class="text-xl font-bold text-teal-800 mb-4 flex items-center">
                                <i class="fas fa-procedures mr-2"></i> En Tratamiento
                            </h2>
                            <div class="space-y-4">
                                @forelse($actividadesEnProgreso as $actividad)
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4" style="border-color: {{ $actividad->color }}">
                                        <div class="p-5">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center justify-center w-10 h-10 rounded-lg" style="background-color: {{ $actividad->color }}">
                                                        <i class="fas {{ $actividad->icono }} text-white text-lg"></i>
                                                    </div>
                                                    <h3 class="text-xl font-semibold text-gray-800">{{ $actividad->titulo }}</h3>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button 
                                                        @click="$store.actividades.openModal('edit', {{ json_encode($actividad) }})"
                                                        class="text-gray-500 hover:text-amber-500 transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.deleteActivity({{ $actividad->id }})"
                                                        class="text-gray-500 hover:text-red-500 transition-colors">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <p class="text-gray-600">{{ $actividad->descripcion }}</p>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full"
                                                      style="background-color: {{ $actividad->color }}20; color: {{ $actividad->color }}">
                                                    {{ ucfirst($actividad->nivel) }}
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    En Tratamiento
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                       ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                    Urgencia: {{ $actividad->prioridad == 1 ? 'Rutina' : ($actividad->prioridad == 2 ? 'Preferente' : 'Urgente') }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mb-4">
                                                <div class="flex items-center space-x-1 text-gray-500">
                                                    <i class="far fa-calendar-alt"></i>
                                                    <span class="text-sm">{{ \Carbon\Carbon::parse($actividad->fecha_limite)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1 text-gray-500">
                                                    <i class="far fa-clock"></i>
                                                    <span class="text-sm">{{ $actividad->hora_limite }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center">
                                                <div class="flex space-x-2">
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'pendiente')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors"
                                                        {{ $actividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                        <i class="fas fa-clipboard-list mr-1"></i> Diagnóstico
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'en_progreso')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-teal-100 text-teal-800 hover:bg-teal-200 transition-colors"
                                                        {{ $actividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                        <i class="fas fa-procedures mr-1"></i> Tratamiento
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'completada')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition-colors"
                                                        {{ $actividad->estado == 'completada' ? 'disabled' : '' }}>
                                                        <i class="fas fa-check-circle mr-1"></i> Tratamiento Completado
                                                    </button>
                                                </div>
                                                <button 
                                                    @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                                    class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                    <i class="fas fa-prescription mr-1"></i> Añadir Tratamiento
                                                </button>
                                            </div>
                                            
                                            <!-- Subactividades -->
                                            @if($actividad->actividadesHijas && $actividad->actividadesHijas->count() > 0)
                                                <div class="mt-4">
                                                    <button 
                                                        @click="$store.actividades.toggleSubactivities({{ $actividad->id }})"
                                                        class="flex items-center space-x-2 text-sm text-gray-600 hover:text-amber-500 transition-colors">
                                                        <span>{{ $actividad->actividadesHijas->count() }} Subactividades</span>
                                                        <i id="toggle-icon-{{ $actividad->id }}" class="fas fa-chevron-down"></i>
                                                    </button>
                                                    
                                                    <div id="subactivities-{{ $actividad->id }}" class="mt-3 pl-4 border-l-2 border-gray-200 hidden">
                                                        @foreach($actividad->actividadesHijas as $subactividad)
                                                            <div class="mb-3 p-3 bg-gray-50 rounded-lg border-l-4" style="border-color: {{ $subactividad->color }}">
                                                                <div class="flex justify-between items-start mb-2">
                                                                    <div class="flex items-center space-x-2">
                                                                        <div class="flex items-center justify-center w-6 h-6 rounded" style="background-color: {{ $subactividad->color }}">
                                                                            <i class="fas {{ $subactividad->icono }} text-white text-xs"></i>
                                                                        </div>
                                                                        <h4 class="font-medium text-gray-800">{{ $subactividad->titulo }}</h4>
                                                                    </div>
                                                                    <div class="flex space-x-1">
                                                                        <button 
                                                                            @click="$store.actividades.openModal('edit', {{ json_encode($subactividad) }})"
                                                                            class="text-gray-500 hover:text-amber-500 transition-colors text-xs">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.deleteActivity({{ $subactividad->id }})"
                                                                            class="text-gray-500 hover:text-red-500 transition-colors text-xs">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="flex flex-wrap gap-1 mb-2">
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                        {{ $subactividad->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                                                           ($subactividad->estado == 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                                        {{ $subactividad->estado == 'pendiente' ? 'Pendiente' : 
                                                                           ($subactividad->estado == 'en_progreso' ? 'En tratamiento' : 'Tratamiento completado') }}
                                                                    </span>
                                                                    
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                        {{ $subactividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                                           ($subactividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                        P{{ $subactividad->prioridad }}
                                                                    </span>
                                                                </div>
                                                                
                                                                <div class="flex justify-between items-center text-xs text-gray-500">
                                                                    <div class="flex items-center space-x-1">
                                                                        <i class="far fa-calendar-alt"></i>
                                                                        <span>{{ \Carbon\Carbon::parse($subactividad->fecha_limite)->format('d/m/Y') }}</span>
                                                                    </div>
                                                                    <div class="flex space-x-1">
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'pendiente')"
                                                                            class="hover:text-yellow-600 transition-colors"
                                                                            {{ $subactividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-yellow-500"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'en_progreso')"
                                                                            class="hover:text-blue-600 transition-colors"
                                                                            {{ $subactividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-blue-500"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'completada')"
                                                                            class="hover:text-green-600 transition-colors"
                                                                            {{ $subactividad->estado == 'completada' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-green-500"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 text-center medical-card">
                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <div class="w-16 h-16 rounded-full bg-teal-100 flex items-center justify-center">
                                                <i class="fas fa-procedures text-teal-500 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-teal-800">No hay pacientes en tratamiento</h3>
                                        </div>
                                    </div>
                                @endforelse
                                
                                <!-- Paginación de Actividades En Progreso -->
                                @if($actividadesEnProgreso->hasPages())
                                    <div class="flex justify-center items-center space-x-2 mt-4 pt-2 border-t border-gray-200">
                                        <!-- Botón Anterior -->
                                        <a href="{{ $actividadesEnProgreso->previousPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ $actividadesEnProgreso->onFirstPage() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </a>
                                        
                                        <!-- Números de Página -->
                                        @for($i = 1; $i <= $actividadesEnProgreso->lastPage(); $i++)
                                            <a href="{{ $actividadesEnProgreso->url($i) }}" 
                                               class="px-3 py-1 rounded-md {{ $i == $actividadesEnProgreso->currentPage() ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                {{ $i }}
                                            </a>
                                        @endfor
                                        
                                        <!-- Botón Siguiente -->
                                        <a href="{{ $actividadesEnProgreso->nextPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ !$actividadesEnProgreso->hasMorePages() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Columna: Pacientes con Tratamiento Completado -->
                        <div class="bg-green-50 rounded-xl p-4 shadow-md border border-green-100">
                            <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                                <i class="fas fa-notes-medical mr-2"></i> Tratamiento Completado
                            </h2>
                            <div class="space-y-4">
                                @forelse($actividadesCompletadas as $actividad)
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4" style="border-color: {{ $actividad->color }}">
                                        <div class="p-5">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center justify-center w-10 h-10 rounded-lg" style="background-color: {{ $actividad->color }}">
                                                        <i class="fas {{ $actividad->icono }} text-white text-lg"></i>
                                                    </div>
                                                    <h3 class="text-xl font-semibold text-gray-800">{{ $actividad->titulo }}</h3>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button 
                                                        @click="$store.actividades.openModal('edit', {{ json_encode($actividad) }})"
                                                        class="text-gray-500 hover:text-amber-500 transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.deleteActivity({{ $actividad->id }})"
                                                        class="text-gray-500 hover:text-red-500 transition-colors">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <p class="text-gray-600">{{ $actividad->descripcion }}</p>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full"
                                                      style="background-color: {{ $actividad->color }}20; color: {{ $actividad->color }}">
                                                    {{ ucfirst($actividad->nivel) }}
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Tratamiento Completado
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                       ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                    Urgencia: {{ $actividad->prioridad == 1 ? 'Rutina' : ($actividad->prioridad == 2 ? 'Preferente' : 'Urgente') }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mb-4">
                                                <div class="flex items-center space-x-1 text-gray-500">
                                                    <i class="far fa-calendar-alt"></i>
                                                    <span class="text-sm">{{ \Carbon\Carbon::parse($actividad->fecha_limite)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1 text-gray-500">
                                                    <i class="far fa-clock"></i>
                                                    <span class="text-sm">{{ $actividad->hora_limite }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center">
                                                <div class="flex space-x-2">
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'pendiente')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors"
                                                        {{ $actividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                        <i class="fas fa-clipboard-list mr-1"></i> Diagnóstico
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'en_progreso')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-teal-100 text-teal-800 hover:bg-teal-200 transition-colors"
                                                        {{ $actividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                        <i class="fas fa-procedures mr-1"></i> Tratamiento
                                                    </button>
                                                </div>
                                                <button 
                                                    @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                                    class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                    <i class="fas fa-prescription mr-1"></i> Añadir Tratamiento
                                                </button>
                                            </div>
                                            
                                            <!-- Subactividades -->
                                            @if($actividad->actividadesHijas && $actividad->actividadesHijas->count() > 0)
                                                <div class="mt-4">
                                                    <button 
                                                        @click="$store.actividades.toggleSubactivities({{ $actividad->id }})"
                                                        class="flex items-center space-x-2 text-sm text-gray-600 hover:text-amber-500 transition-colors">
                                                        <span>{{ $actividad->actividadesHijas->count() }} Subactividades</span>
                                                        <i id="toggle-icon-{{ $actividad->id }}" class="fas fa-chevron-down"></i>
                                                    </button>
                                                    
                                                    <div id="subactivities-{{ $actividad->id }}" class="mt-3 pl-4 border-l-2 border-gray-200 hidden">
                                                        @foreach($actividad->actividadesHijas as $subactividad)
                                                            <div class="mb-3 p-3 bg-gray-50 rounded-lg border-l-4" style="border-color: {{ $subactividad->color }}">
                                                                <div class="flex justify-between items-start mb-2">
                                                                    <div class="flex items-center space-x-2">
                                                                        <div class="flex items-center justify-center w-6 h-6 rounded" style="background-color: {{ $subactividad->color }}">
                                                                            <i class="fas {{ $subactividad->icono }} text-white text-xs"></i>
                                                                        </div>
                                                                        <h4 class="font-medium text-gray-800">{{ $subactividad->titulo }}</h4>
                                                                    </div>
                                                                    <div class="flex space-x-1">
                                                                        <button 
                                                                            @click="$store.actividades.openModal('edit', {{ json_encode($subactividad) }})"
                                                                            class="text-gray-500 hover:text-amber-500 transition-colors text-xs">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.deleteActivity({{ $subactividad->id }})"
                                                                            class="text-gray-500 hover:text-red-500 transition-colors text-xs">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="flex flex-wrap gap-1 mb-2">
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                        {{ $subactividad->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                                                           ($subactividad->estado == 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                                        {{ $subactividad->estado == 'pendiente' ? 'Pendiente' : 
                                                                           ($subactividad->estado == 'en_progreso' ? 'En tratamiento' : 'Tratamiento completado') }}
                                                                    </span>
                                                                    
                                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                                        {{ $subactividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                                           ($subactividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                        P{{ $subactividad->prioridad }}
                                                                    </span>
                                                                </div>
                                                                
                                                                <div class="flex justify-between items-center text-xs text-gray-500">
                                                                    <div class="flex items-center space-x-1">
                                                                        <i class="far fa-calendar-alt"></i>
                                                                        <span>{{ \Carbon\Carbon::parse($subactividad->fecha_limite)->format('d/m/Y') }}</span>
                                                                    </div>
                                                                    <div class="flex space-x-1">
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'pendiente')"
                                                                            class="hover:text-yellow-600 transition-colors"
                                                                            {{ $subactividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-yellow-500"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'en_progreso')"
                                                                            class="hover:text-blue-600 transition-colors"
                                                                            {{ $subactividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-blue-500"></i>
                                                                        </button>
                                                                        <button 
                                                                            @click="$store.actividades.changeStatus({{ $subactividad->id }}, 'completada')"
                                                                            class="hover:text-green-600 transition-colors"
                                                                            {{ $subactividad->estado == 'completada' ? 'disabled' : '' }}>
                                                                            <i class="fas fa-circle text-green-500"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">No hay tratamientos completados</h3>
                                        </div>
                                    </div>
                                @endforelse
                                
                                <!-- Paginación de Actividades Completadas -->
                                @if($actividadesCompletadas->hasPages())
                                    <div class="flex justify-center items-center space-x-2 mt-4 pt-2 border-t border-gray-200">
                                        <!-- Botón Anterior -->
                                        <a href="{{ $actividadesCompletadas->previousPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ $actividadesCompletadas->onFirstPage() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </a>
                                        
                                        <!-- Números de Página -->
                                        @for($i = 1; $i <= $actividadesCompletadas->lastPage(); $i++)
                                            <a href="{{ $actividadesCompletadas->url($i) }}" 
                                               class="px-3 py-1 rounded-md {{ $i == $actividadesCompletadas->currentPage() ? 'bg-green-500 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                                {{ $i }}
                                            </a>
                                        @endfor
                                        
                                        <!-- Botón Siguiente -->
                                        <a href="{{ $actividadesCompletadas->nextPageUrl() }}" 
                                           class="px-3 py-1 rounded-md {{ !$actividadesCompletadas->hasMorePages() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mensaje cuando no hay actividades en ninguna columna -->
                        @if(isset($actividadesPendientes) && $actividadesPendientes->isEmpty() && isset($actividadesEnProgreso) && $actividadesEnProgreso->isEmpty() && isset($actividadesCompletadas) && $actividadesCompletadas->isEmpty())
                            <div class="col-span-full">
                                <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center">
                                            <i class="fas fa-tasks text-amber-500 text-3xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-800">No hay actividades</h3>
                                        <p class="text-gray-600">Crea tu primera actividad</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Contenido para usuarios no autenticados -->
                        <div class="col-span-full">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
                                <div class="flex flex-col items-center justify-center space-y-4 text-center">
                                    <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center">
                                        <i class="fas fa-user-lock text-amber-500 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Acceso Restringido</h3>
                                    <p class="text-gray-600 max-w-md mx-auto">Para gestionar tus actividades, necesitas iniciar sesión o crear una cuenta.</p>
                                    <button 
                                        @click="$store.modal.open = true"
                                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg 
                                               hover:from-blue-600 hover:to-blue-700
                                               focus:ring-4 focus:ring-blue-300/50
                                               shadow-md hover:shadow-xl
                                               transform hover:-translate-y-0.5
                                               transition-all duration-300 ease-out
                                               active:scale-95">
                                        <span class="flex items-center space-x-2">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Iniciar Sesión</span>
                                        </span>
                                    </button>
                                </div>
                                
                                <!-- Ejemplo de actividades para usuarios no autenticados -->
                                <div class="mt-10">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Ejemplo de Actividades</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-blue-500">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500">
                                                    <i class="fas fa-briefcase text-white"></i>
                                                </div>
                                                <h5 class="font-medium text-gray-800">Reunión de Equipo</h5>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Discutir avances del proyecto y asignar nuevas tareas.</p>
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Alta Prioridad</span>
                                                <span>Mañana, 10:00</span>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-green-500">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-green-500">
                                                    <i class="fas fa-file-invoice text-white"></i>
                                                </div>
                                                <h5 class="font-medium text-gray-800">Preparar Informe</h5>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Completar el informe mensual de ventas para presentación.</p>
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Media Prioridad</span>
                                                <span>Viernes, 15:00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Crear/Editar Actividad -->    
    <div 
        x-data
        x-cloak
        x-show="$store.actividades.modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="$store.actividades.closeModal()"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        
        <div 
            class="relative p-0 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white overflow-hidden"
            x-cloak
            x-show="$store.actividades.modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <!-- Modal Header con gradiente médico -->            
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white relative medical-gradient">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold flex items-center" x-text="$store.actividades.modalTitle"></h3>
                    <button 
                        @click="$store.actividades.closeModal()"
                        class="text-white/80 hover:text-white transition-colors p-1.5 hover:bg-white/20 rounded-full">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Indicador de modo (crear/editar) -->                
                <div class="mt-2 text-sm text-white/80 flex items-center">
                    <i class="fas" :class="$store.actividades.modalMode === 'create' ? 'fa-user-plus' : 'fa-user-edit'"></i>
                    <span class="ml-2" x-text="$store.actividades.modalMode === 'create' ? 'Registrando nuevo paciente' : 'Actualizando información del paciente'"></span>
                </div>
            </div>
            
            <!-- Contenido del formulario -->            
            <form id="actividadForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="actividad_id" name="actividad_id">
                <input type="hidden" id="actividad_padre_id" name="actividad_padre_id">
                
                <!-- Vista previa del paciente -->                
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 mb-6 flex items-start space-x-4 medical-card" x-data="{previewTitle: '', previewDesc: '', previewColor: '#4A90E2', previewIcon: 'fa-user-md'}" x-init="
                    $watch('$store.actividades.modalOpen', value => {
                        if (value) {
                            setTimeout(() => {
                                previewTitle = document.getElementById('titulo').value || 'Nuevo Paciente';
                                previewDesc = document.getElementById('descripcion').value || '';
                                previewColor = document.getElementById('color').value || '#4A90E2';
                                previewIcon = document.getElementById('icono').value || 'fa-user-md';
                            }, 100);
                        }
                    });
                    $watch('previewTitle', value => { if (!value) previewTitle = 'Nuevo Paciente'; });
                ">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full" :style="`background-color: ${previewColor}`">
                        <i class="fas text-white text-xl" :class="previewIcon"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-800" x-text="previewTitle || 'Nuevo Paciente'"></h3>
                        <p class="text-sm text-blue-600 mt-1" x-text="previewDesc" x-show="previewDesc"></p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full" 
                                  :style="`background-color: ${previewColor}20; color: ${previewColor}`">
                                <span x-text="document.getElementById('nivel')?.value === 'principal' ? 'Nuevo' : 
                                              (document.getElementById('nivel')?.value === 'secundaria' ? 'Seguimiento' : 'Crónico')"></span>
                            </span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="document.getElementById('estado')?.value === 'pendiente' ? 'bg-indigo-100 text-indigo-800' : 
                                         (document.getElementById('estado')?.value === 'en_progreso' ? 'bg-teal-100 text-teal-800' : 'bg-green-100 text-green-800')">
                                <span x-text="document.getElementById('estado')?.value === 'pendiente' ? 'Pendiente de diagnóstico' : 
                                              (document.getElementById('estado')?.value === 'en_progreso' ? 'En tratamiento' : 'Tratamiento completado')"></span>
                            </span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="document.getElementById('prioridad')?.value == 1 ? 'bg-gray-100 text-gray-800' : 
                                         (document.getElementById('prioridad')?.value == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')">
                                <span x-text="'Urgencia: ' + (document.getElementById('prioridad')?.value == 1 ? 'Rutina' : 
                                                            (document.getElementById('prioridad')?.value == 2 ? 'Preferente' : 'Urgente'))"></span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Campos del formulario -->                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Datos del Paciente (agrupados) -->                    
                    <div class="col-span-3 space-y-3">
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-blue-700 mb-1">Nombre del Paciente</label>
                            <input 
                                type="text" 
                                id="titulo" 
                                name="titulo" 
                                class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                                required
                                @input="previewTitle = $event.target.value"
                                placeholder="Ingrese el nombre completo del paciente">
                        </div>
                        
                        <!-- Síntomas/Motivo de consulta -->                    
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-blue-700 mb-1">Síntomas / Motivo de Consulta</label>
                            <textarea 
                                id="descripcion" 
                                name="descripcion" 
                                rows="2" 
                                class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="Describa los síntomas o motivo de consulta del paciente"></textarea>
                        </div>
                    </div>
                    
                    <!-- Primera columna: Información Clínica -->                    
                    <div class="space-y-4">
                        <!-- Tipo de Paciente -->                    
                        <div>
                            <label for="nivel" class="block text-sm font-medium text-blue-700 mb-1">Tipo de Paciente</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-blue-400"></i>
                                </div>
                                <select 
                                    id="nivel" 
                                    name="nivel" 
                                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="principal">Nuevo</option>
                                    <option value="secundaria">Seguimiento</option>
                                    <option value="terciaria">Crónico</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado Clínico -->                    
                        <div>
                            <label for="estado" class="block text-sm font-medium text-blue-700 mb-1">Estado Clínico</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-heartbeat text-blue-400"></i>
                                </div>
                                <select 
                                    id="estado" 
                                    name="estado" 
                                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="pendiente">Pendiente de diagnóstico</option>
                                    <option value="en_progreso">En tratamiento</option>
                                    <option value="completada">Tratamiento completado</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Nivel de Urgencia -->                    
                        <div>
                            <label for="prioridad" class="block text-sm font-medium text-blue-700 mb-1">Nivel de Urgencia</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-ambulance text-blue-400"></i>
                                </div>
                                <select 
                                    id="prioridad" 
                                    name="prioridad" 
                                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="1">Rutina</option>
                                    <option value="2">Preferente</option>
                                    <option value="3">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda columna: Programación de Cita -->                    
                    <div class="space-y-4">
                        <!-- Fecha de Cita -->                    
                        <div>
                            <label for="fecha_limite" class="block text-sm font-medium text-blue-700 mb-1">Fecha de Cita</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-blue-400"></i>
                                </div>
                                <input 
                                    type="date" 
                                    id="fecha_limite" 
                                    name="fecha_limite" 
                                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                                    required>
                            </div>
                        </div>
                        
                        <!-- Hora de Cita -->                    
                        <div>
                            <label for="hora_limite" class="block text-sm font-medium text-blue-700 mb-1">Hora de Cita</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-blue-400"></i>
                                </div>
                                <input 
                                    type="time" 
                                    id="hora_limite" 
                                    name="hora_limite" 
                                    class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                                    required>
                            </div>
                        </div>
                        
                        <!-- Código de Color -->                    
                        <div>
                            <label class="block text-sm font-medium text-blue-700 mb-1">Código de Color</label>
                            <div class="flex items-center space-x-2">
                                <div id="selectedColor" class="w-10 h-10 rounded-lg border border-blue-200 shadow-inner transition-all duration-200" style="background-color: #4A90E2"></div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        id="color" 
                                        name="color" 
                                        value="#4A90E2" 
                                        class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        @input="previewColor = $event.target.value">
                                </div>
                            </div>
                            <div id="colorPicker" class="mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200 grid grid-cols-5 gap-1"></div>
                        </div>
                    </div>
                    
                    <!-- Tercera columna: Especialidad Médica -->                    
                    <div class="space-y-4">
                        <!-- Especialidad Médica -->                    
                        <div>
                            <label class="block text-sm font-medium text-blue-700 mb-1">Especialidad Médica</label>
                            <div class="flex items-center space-x-2">
                                <div id="selectedIcon" class="w-10 h-10 rounded-lg border border-blue-200 flex items-center justify-center shadow-inner transition-all duration-200" style="background-color: #e3f2fd">
                                    <i class="fas fa-user-md text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        id="icono" 
                                        name="icono" 
                                        value="fa-user-md" 
                                        class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        @input="previewIcon = $event.target.value">
                                </div>
                            </div>
                            <div id="iconSelector" class="mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200 grid grid-cols-5 gap-1 max-h-28 overflow-y-auto"></div>
                        </div>
                    </div>
                    

                </div>
                
                <!-- Botones de acción -->                
                <div class="flex justify-end space-x-3 pt-4 border-t border-blue-200 mt-6">
                    <button 
                        type="button"
                        @click="$store.actividades.closeModal()"
                        class="px-5 py-2.5 bg-white border border-blue-300 text-blue-700 text-sm font-medium rounded-lg 
                               hover:bg-blue-50
                               focus:ring-4 focus:ring-blue-300/50
                               transition-all duration-300 ease-out
                               active:scale-95 flex items-center">
                        <i class="fas fa-times-circle mr-2"></i> Cancelar
                    </button>
                    <button 
                        type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg 
                               hover:from-blue-600 hover:to-blue-700
                               focus:ring-4 focus:ring-blue-300/50
                               shadow-md hover:shadow-xl
                               transform hover:-translate-y-0.5
                               transition-all duration-300 ease-out
                               active:scale-95 flex items-center">
                        <i class="fas fa-clipboard-check mr-2"></i> Registrar Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>


<script src="{{ asset('js/actividades.js') }}"></script>