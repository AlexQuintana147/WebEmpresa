<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Actividades</title>
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
                                @forelse($actividades->where('estado', 'pendiente') as $actividad)
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
                            </div>
                        </div>
                        
                        <!-- Columna: Actividades En Progreso -->
                        <div class="bg-blue-50 rounded-xl p-4 shadow-md">
                            <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-spinner mr-2"></i> En Progreso
                            </h2>
                            <div class="space-y-4">
                                @forelse($actividades->where('estado', 'en_progreso') as $actividad)
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
                            </div>
                        </div>
                        
                        <!-- Columna: Actividades Completadas -->
                        <div class="bg-green-50 rounded-xl p-4 shadow-md">
                            <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Completadas
                            </h2>
                            <div class="space-y-4">
                                @forelse($actividades->where('estado', 'completada') as $actividad)
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
                            </div>
                        </div>
                        
                        <!-- Mensaje cuando no hay actividades en ninguna columna -->
                        @if($actividades->isEmpty())
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
        x-show="$store.actividades.modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="$store.actividades.closeModal()"
        class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        
        <div 
            class="relative p-8 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white"
            x-show="$store.actividades.modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800" x-text="$store.actividades.modalTitle"></h3>
                <button 
                    @click="$store.actividades.closeModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="actividadForm" class="space-y-6">
                @csrf
                <input type="hidden" id="actividad_id" name="actividad_id">
                <input type="hidden" id="actividad_padre_id" name="actividad_padre_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" id="titulo" name="titulo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                    </div>
                    
                    <div class="col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                    </div>
                    
                    <div>
                        <label for="nivel" class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                        <select id="nivel" name="nivel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="principal">Principal</option>
                            <option value="secundaria">Secundaria</option>
                            <option value="terciaria">Terciaria</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="estado" name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="fecha_limite" class="block text-sm font-medium text-gray-700 mb-1">Fecha Límite</label>
                        <input type="date" id="fecha_limite" name="fecha_limite" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                    </div>
                    
                    <div>
                        <label for="hora_limite" class="block text-sm font-medium text-gray-700 mb-1">Hora Límite</label>
                        <input type="time" id="hora_limite" name="hora_limite" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                    </div>
                    
                    <div>
                        <label for="prioridad" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                        <select id="prioridad" name="prioridad" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="1">Baja</option>
                            <option value="2">Media</option>
                            <option value="3">Alta</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <div class="flex items-center space-x-3">
                            <div id="selectedColor" class="w-10 h-10 rounded-lg border border-gray-300" style="background-color: #4A90E2"></div>
                            <input type="text" id="color" name="color" value="#4A90E2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div id="colorPicker" class="mt-2 flex flex-wrap"></div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                        <div class="flex items-center space-x-3">
                            <div id="selectedIcon" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-tasks text-xl"></i>
                            </div>
                            <input type="text" id="icono" name="icono" value="fa-tasks" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div id="iconSelector" class="mt-2 flex flex-wrap"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="$store.actividades.closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg 
                               hover:bg-gray-300
                               focus:ring-4 focus:ring-gray-300/50
                               transition-all duration-300 ease-out
                               active:scale-95">
                        Cancelar
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-sm font-medium rounded-lg 
                               hover:from-amber-600 hover:to-amber-700
                               focus:ring-4 focus:ring-amber-300/50
                               shadow-md hover:shadow-xl
                               transform hover:-translate-y-0.5
                               transition-all duration-300 ease-out
                               active:scale-95">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>


<script src="{{ asset('js/actividades.js') }}"></script>