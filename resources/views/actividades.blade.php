<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Actividades</title>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
            <div class="container mx-auto px-4 py-8" x-data>
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Lista de Actividades</h1>
                    <button 
                        @click="$store.actividades.openModal('create')"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-sm font-medium rounded-lg 
                               hover:from-amber-600 hover:to-amber-700
                               focus:ring-4 focus:ring-amber-300/50
                               shadow-md hover:shadow-xl
                               transform hover:-translate-y-0.5
                               transition-all duration-300 ease-out
                               active:scale-95">
                        <span class="flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Nueva Actividad</span>
                        </span>
                    </button>
                </div>

                <!-- Contenedor principal de columnas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna: Actividades Pendientes -->
                    <div class="bg-yellow-50 rounded-xl p-4 shadow-md">
                        <h2 class="text-xl font-bold text-yellow-800 mb-4 flex items-center">
                            <i class="fas fa-tasks mr-2"></i> Pendientes
                        </h2>
                        <div class="space-y-4">
                            @if(Auth::check())
                                @forelse($actividadesPendientes as $actividad)
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
                                        
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            {{ $actividad->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($actividad->estado == 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $actividad->estado)) }}
                                        </span>
                                        
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                               ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                            Prioridad: {{ $actividad->prioridad == 1 ? 'Baja' : ($actividad->prioridad == 2 ? 'Media' : 'Alta') }}
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
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors"
                                                {{ $actividad->estado == 'pendiente' ? 'disabled' : '' }}>
                                                Pendiente
                                            </button>
                                            <button 
                                                @click="$store.actividades.changeStatus({{ $actividad->id }}, 'en_progreso')"
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors"
                                                {{ $actividad->estado == 'en_progreso' ? 'disabled' : '' }}>
                                                En Progreso
                                            </button>
                                            <button 
                                                @click="$store.actividades.changeStatus({{ $actividad->id }}, 'completada')"
                                                class="px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition-colors"
                                                {{ $actividad->estado == 'completada' ? 'disabled' : '' }}>
                                                Completada
                                            </button>
                                        </div>
                                        <button 
                                            @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                            class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                            <i class="fas fa-plus mr-1"></i> Subactividad
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
                                                                {{ ucfirst(str_replace('_', ' ', $subactividad->estado)) }}
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
                                            <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <i class="fas fa-tasks text-yellow-500 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">No hay actividades pendientes</h3>
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
                        
                        <!-- Columna: Actividades En Progreso -->
                        <div class="bg-blue-50 rounded-xl p-4 shadow-md">
                            <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-spinner mr-2"></i> En Progreso
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
                                                    En Progreso
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                       ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                    Prioridad: {{ $actividad->prioridad == 1 ? 'Baja' : ($actividad->prioridad == 2 ? 'Media' : 'Alta') }}
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
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors">
                                                        Pendiente
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'completada')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                                                        Completada
                                                    </button>
                                                </div>
                                                <button 
                                                    @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                                    class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                    <i class="fas fa-plus mr-1"></i> Subactividad
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
                                                                        {{ ucfirst(str_replace('_', ' ', $subactividad->estado)) }}
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
                                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-spinner text-blue-500 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">No hay actividades en progreso</h3>
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
                        
                        <!-- Columna: Actividades Completadas -->
                        <div class="bg-green-50 rounded-xl p-4 shadow-md">
                            <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Completadas
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
                                                    Completada
                                                </span>
                                                
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $actividad->prioridad == 1 ? 'bg-gray-100 text-gray-800' : 
                                                       ($actividad->prioridad == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                    Prioridad: {{ $actividad->prioridad == 1 ? 'Baja' : ($actividad->prioridad == 2 ? 'Media' : 'Alta') }}
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
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors">
                                                        Pendiente
                                                    </button>
                                                    <button 
                                                        @click="$store.actividades.changeStatus({{ $actividad->id }}, 'en_progreso')"
                                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                                        En Progreso
                                                    </button>
                                                </div>
                                                <button 
                                                    @click="$store.actividades.addSubactivity({{ $actividad->id }})"
                                                    class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                    <i class="fas fa-plus mr-1"></i> Subactividad
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
                                                                        {{ ucfirst(str_replace('_', ' ', $subactividad->estado)) }}
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
                                            <h3 class="text-lg font-semibold text-gray-800">No hay actividades completadas</h3>
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
            
            <!-- Modal Header con gradiente -->            
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6 text-white relative">
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
                    <i class="fas" :class="$store.actividades.modalMode === 'create' ? 'fa-plus-circle' : 'fa-edit'"></i>
                    <span class="ml-2" x-text="$store.actividades.modalMode === 'create' ? 'Creando nueva actividad' : 'Modificando actividad existente'"></span>
                </div>
            </div>
            
            <!-- Contenido del formulario -->            
            <form id="actividadForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="actividad_id" name="actividad_id">
                <input type="hidden" id="actividad_padre_id" name="actividad_padre_id">
                
                <!-- Vista previa de la actividad -->                
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 flex items-start space-x-4" x-data="{previewTitle: '', previewDesc: '', previewColor: '#4A90E2', previewIcon: 'fa-tasks'}" x-init="
                    $watch('$store.actividades.modalOpen', value => {
                        if (value) {
                            setTimeout(() => {
                                previewTitle = document.getElementById('titulo').value || 'Nueva Actividad';
                                previewDesc = document.getElementById('descripcion').value || '';
                                previewColor = document.getElementById('color').value || '#4A90E2';
                                previewIcon = document.getElementById('icono').value || 'fa-tasks';
                            }, 100);
                        }
                    });
                    $watch('previewTitle', value => { if (!value) previewTitle = 'Nueva Actividad'; });
                ">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg" :style="`background-color: ${previewColor}`">
                        <i class="fas text-white text-xl" :class="previewIcon"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800" x-text="previewTitle || 'Nueva Actividad'"></h3>
                        <p class="text-sm text-gray-600 mt-1" x-text="previewDesc" x-show="previewDesc"></p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full" 
                                  :style="`background-color: ${previewColor}20; color: ${previewColor}`">
                                <span x-text="document.getElementById('nivel')?.value === 'principal' ? 'Principal' : 
                                              (document.getElementById('nivel')?.value === 'secundaria' ? 'Secundaria' : 'Terciaria')"></span>
                            </span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="document.getElementById('estado')?.value === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                         (document.getElementById('estado')?.value === 'en_progreso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')">
                                <span x-text="document.getElementById('estado')?.value === 'pendiente' ? 'Pendiente' : 
                                              (document.getElementById('estado')?.value === 'en_progreso' ? 'En Progreso' : 'Completada')"></span>
                            </span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="document.getElementById('prioridad')?.value == 1 ? 'bg-gray-100 text-gray-800' : 
                                         (document.getElementById('prioridad')?.value == 2 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')">
                                <span x-text="'Prioridad: ' + (document.getElementById('prioridad')?.value == 1 ? 'Baja' : 
                                                            (document.getElementById('prioridad')?.value == 2 ? 'Media' : 'Alta'))"></span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Campos del formulario -->                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Título y Descripción (agrupados) -->                    
                    <div class="col-span-3 space-y-3">
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                            <input 
                                type="text" 
                                id="titulo" 
                                name="titulo" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200" 
                                required
                                @input="previewTitle = $event.target.value"
                                placeholder="Ingrese el título de la actividad">
                        </div>
                        
                        <!-- Descripción (justo debajo del título) -->                    
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea 
                                id="descripcion" 
                                name="descripcion" 
                                rows="2" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                placeholder="Describa los detalles de la actividad"></textarea>
                        </div>
                    </div>
                    
                    <!-- Primera columna: Configuración básica -->                    
                    <div class="space-y-4">
                        <!-- Nivel -->                    
                        <div>
                            <label for="nivel" class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                </div>
                                <select 
                                    id="nivel" 
                                    name="nivel" 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="principal">Principal</option>
                                    <option value="secundaria">Secundaria</option>
                                    <option value="terciaria">Terciaria</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado -->                    
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tasks text-gray-400"></i>
                                </div>
                                <select 
                                    id="estado" 
                                    name="estado" 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Prioridad -->                    
                        <div>
                            <label for="prioridad" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-flag text-gray-400"></i>
                                </div>
                                <select 
                                    id="prioridad" 
                                    name="prioridad" 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                    @change="$dispatch('input', $event.target.value)">
                                    <option value="1">Baja</option>
                                    <option value="2">Media</option>
                                    <option value="3">Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda columna: Fechas y Personalización -->                    
                    <div class="space-y-4">
                        <!-- Fecha Límite -->                    
                        <div>
                            <label for="fecha_limite" class="block text-sm font-medium text-gray-700 mb-1">Fecha Límite</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                </div>
                                <input 
                                    type="date" 
                                    id="fecha_limite" 
                                    name="fecha_limite" 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200" 
                                    required>
                            </div>
                        </div>
                        
                        <!-- Hora Límite -->                    
                        <div>
                            <label for="hora_limite" class="block text-sm font-medium text-gray-700 mb-1">Hora Límite</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                                <input 
                                    type="time" 
                                    id="hora_limite" 
                                    name="hora_limite" 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200" 
                                    required>
                            </div>
                        </div>
                        
                        <!-- Color -->                    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex items-center space-x-2">
                                <div id="selectedColor" class="w-10 h-10 rounded-lg border border-gray-300 shadow-inner transition-all duration-200" style="background-color: #4A90E2"></div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        id="color" 
                                        name="color" 
                                        value="#4A90E2" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                        @input="previewColor = $event.target.value">
                                </div>
                            </div>
                            <div id="colorPicker" class="mt-2 p-2 bg-gray-50 rounded-lg border border-gray-200 grid grid-cols-5 gap-1"></div>
                        </div>
                    </div>
                    
                    <!-- Tercera columna: Icono y Descripción -->                    
                    <div class="space-y-4">
                        <!-- Icono -->                    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                            <div class="flex items-center space-x-2">
                                <div id="selectedIcon" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center shadow-inner transition-all duration-200" style="background-color: #f8f9fa">
                                    <i class="fas fa-tasks text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        id="icono" 
                                        name="icono" 
                                        value="fa-tasks" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                        @input="previewIcon = $event.target.value">
                                </div>
                            </div>
                            <div id="iconSelector" class="mt-2 p-2 bg-gray-50 rounded-lg border border-gray-200 grid grid-cols-5 gap-1 max-h-28 overflow-y-auto"></div>
                        </div>
                    </div>
                    

                </div>
                
                <!-- Botones de acción -->                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 mt-6">
                    <button 
                        type="button"
                        @click="$store.actividades.closeModal()"
                        class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg 
                               hover:bg-gray-50
                               focus:ring-4 focus:ring-gray-300/50
                               transition-all duration-300 ease-out
                               active:scale-95 flex items-center">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                    <button 
                        type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-sm font-medium rounded-lg 
                               hover:from-amber-600 hover:to-amber-700
                               focus:ring-4 focus:ring-amber-300/50
                               shadow-md hover:shadow-xl
                               transform hover:-translate-y-0.5
                               transition-all duration-300 ease-out
                               active:scale-95 flex items-center">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>


<script src="{{ asset('js/actividades.js') }}"></script>