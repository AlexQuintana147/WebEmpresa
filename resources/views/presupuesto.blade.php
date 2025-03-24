<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Presupuesto</title>
    <style>[x-cloak] { display: none !important; }</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Usamos la misma versión de Alpine.js que en el header para evitar conflictos -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Inicializar los stores directamente para evitar problemas de sincronización
        window.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine !== 'undefined') {
                // Si Alpine ya está disponible, inicializar los stores inmediatamente
                initializeStores();
            } else {
                // Si Alpine aún no está disponible, esperar a que se inicialice
                document.addEventListener('alpine:init', initializeStores);
            }
            
            function initializeStores() {
                // Stores específicos para los modales de presupuesto
                // Usar nombres únicos para evitar conflictos con otros stores
                Alpine.store('categoriaModal', {
                    open: false,
                    item: null
                });
                
                Alpine.store('transaccionModal', {
                    open: false,
                    item: null
                });
                
                console.log('Stores inicializados en presupuesto:', {
                    categoriaModal: Alpine.store('categoriaModal'),
                    transaccionModal: Alpine.store('transaccionModal')
                });
            }
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex" x-data="{}">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />

            <!-- Main Content Area -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Title -->
                    <div class="mb-10 text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Gestión de Presupuesto</h1>
                        <p class="text-xl text-gray-600">Controla y administra tus finanzas de manera eficiente</p>
                    </div>

                    <!-- Budget Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Budget Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-700 text-sm font-medium">Presupuesto Total</p>
                                    <h3 class="text-2xl font-bold text-blue-900">
                                        @auth
                                            ${{ number_format($presupuestoTotal ?? 0, 2) }}
                                        @else
                                            $50,000
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-4 bg-blue-200 rounded-full shadow-inner">
                                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Spent Amount Card -->
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-700 text-sm font-medium">Gastado</p>
                                    <h3 class="text-2xl font-bold text-red-900">
                                        @auth
                                            ${{ number_format($gastos ?? 0, 2) }}
                                        @else
                                            $32,450
                                        @endauth
                                    </h3>
                                    @auth
                                        @if($presupuestoTotal > 0)
                                            <p class="text-xs font-medium mt-1 text-red-600">
                                                {{ round(($gastos / $presupuestoTotal) * 100) }}% del presupuesto
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-xs font-medium mt-1 text-red-600">65% del presupuesto</p>
                                    @endauth
                                </div>
                                <div class="p-4 bg-red-200 rounded-full shadow-inner">
                                    <i class="fas fa-chart-line text-red-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Remaining Amount Card -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-700 text-sm font-medium">Restante</p>
                                    <h3 class="text-2xl font-bold"
                                        @auth
                                            @php
                                                $colorRestante = $restante >= 0 ? 'text-green-900' : 'text-red-600';
                                            @endphp
                                            class="{{ $colorRestante }}"
                                        @else
                                            class="text-green-900"
                                        @endauth
                                    >
                                        @auth
                                            ${{ number_format($restante ?? 0, 2) }}
                                            @if($restante < 0)
                                                <i class="fas fa-exclamation-circle ml-1 text-sm"></i>
                                            @endif
                                        @else
                                            $17,550
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-4 bg-green-200 rounded-full shadow-inner">
                                    <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Savings Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-700 text-sm font-medium">Ahorros</p>
                                    <h3 class="text-2xl font-bold text-purple-900">
                                        @auth
                                            ${{ number_format($ahorros ?? 0, 2) }}
                                        @else
                                            $5,000
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-4 bg-purple-200 rounded-full shadow-inner">
                                    <i class="fas fa-save text-purple-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Visualization Section -->
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <h2 class="text-xl font-semibold mb-6">Resumen de Presupuesto</h2>
                            
                            <!-- Budget Progress Bar -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progreso del Presupuesto</span>
                                    <span class="text-sm font-medium"
                                        @auth
                                            @php
                                                $porcentaje = $presupuestoTotal > 0 ? round(($gastos / $presupuestoTotal) * 100) : 0;
                                                $colorTexto = $porcentaje < 70 ? 'text-green-600' : ($porcentaje < 100 ? 'text-yellow-600' : 'text-red-600');
                                            @endphp
                                            class="{{ $colorTexto }} font-bold"
                                        @else
                                            class="text-yellow-600 font-bold"
                                        @endauth
                                    >
                                        @auth
                                            {{ $porcentaje }}%
                                            @if($porcentaje > 100)
                                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                            @endif
                                        @else
                                            65%
                                        @endauth
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 relative overflow-hidden">
                                    @auth
                                        @php
                                            $barColor = $porcentaje < 70 ? 'bg-green-500' : ($porcentaje < 100 ? 'bg-yellow-500' : 'bg-red-500');
                                            $barWidth = $porcentaje > 100 ? '100%' : $porcentaje . '%';
                                        @endphp
                                        <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $barWidth }}"></div>
                                        @if($porcentaje > 100)
                                            <div class="absolute top-0 right-0 h-3 bg-red-300 border-l-2 border-red-700 overflow-hidden" style="width: {{ min($porcentaje - 100, 50) }}%">
                                                <div class="w-full h-full bg-red-500 opacity-60 animate-pulse"></div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 65%"></div>
                                    @endauth
                                </div>
                                <div class="flex justify-between text-xs font-medium mt-1">
                                    <span class="text-gray-600">$0</span>
                                    <span class="text-gray-600">
                                        @auth
                                            ${{ number_format($presupuestoTotal ?? 0, 2) }}
                                        @else
                                            $50,000
                                        @endauth
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Category Distribution -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium mb-4">Distribución por Categoría</h3>
                                    <div class="space-y-4">
                                        @auth
                                            @if(count($categorias) > 0)
                                                @foreach($categorias as $categoria)
                                                    @php
                                                        $porcentaje = $presupuestoTotal > 0 ? round(($categoria->presupuesto / $presupuestoTotal) * 100) : 0;
                                                    @endphp
                                                    <!-- Category Bar -->
                                                    <div>
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-sm font-medium">{{ $categoria->nombre }}</span>
                                                            <span class="text-sm font-medium">${{ number_format($categoria->presupuesto, 2) }} ({{ $porcentaje }}%)</span>
                                                        </div>
                                                        @php
                                                            $gastoCategoria = $categoria->transacciones->where('tipo', 'gasto')->sum('monto');
                                                            $porcentajeGasto = $categoria->presupuesto > 0 ? round(($gastoCategoria / $categoria->presupuesto) * 100) : 0;
                                                            $barColorCat = $porcentajeGasto < 70 ? 'bg-' . $categoria->color . '-500' : ($porcentajeGasto < 100 ? 'bg-yellow-500' : 'bg-red-500');
                                                            $barWidthCat = $porcentajeGasto > 100 ? '100%' : $porcentajeGasto . '%';
                                                        @endphp
                                                        <div class="w-full bg-gray-200 rounded-full h-3 relative overflow-hidden">
                                                            <div class="{{ $barColorCat }} h-3 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $barWidthCat }}"></div>
                                                            @if($porcentajeGasto > 100)
                                                                <div class="absolute top-0 right-0 h-3 bg-red-300 border-l-2 border-red-700 overflow-hidden" style="width: {{ min($porcentajeGasto - 100, 30) }}%">
                                                                    <div class="w-full h-full bg-red-500 opacity-60 animate-pulse"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-gray-500 text-sm">No hay categorías de presupuesto. Añade una para comenzar.</p>
                                            @endif
                                        @else
                                            <!-- Category Bar -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-sm font-medium">Vivienda</span>
                                                    <span class="text-sm font-medium">$12,000 (24%)</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 24%"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Category Bar -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-sm font-medium">Alimentación</span>
                                                    <span class="text-sm font-medium">$8,000 (16%)</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 16%"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Category Bar -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-sm font-medium">Transporte</span>
                                                    <span class="text-sm font-medium">$5,000 (10%)</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-purple-600 h-2 rounded-full" style="width: 10%"></div>
                                                </div>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-medium mb-4">Ingresos vs Gastos</h3>
                                    <div class="flex items-center justify-center h-48">
                                        <!-- Chart.js Canvas -->
                                        <canvas id="ingresosGastosChart" width="300" height="200"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Chart.js Script -->
                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const ctx = document.getElementById('ingresosGastosChart').getContext('2d');
                                        
                                        @auth
                                            const ingresos = {{ $ingresos }};
                                            const gastos = {{ $gastos }};
                                            const ingresosLabel = 'Ingresos: ${{ number_format($ingresos, 2) }}';
                                            const gastosLabel = 'Gastos: ${{ number_format($gastos, 2) }}';
                                        @else
                                            const ingresos = 40000;
                                            const gastos = 32450;
                                            const ingresosLabel = 'Ingresos: $40,000';
                                            const gastosLabel = 'Gastos: $32,450';
                                        @endauth
                                        
                                        const chart = new Chart(ctx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: [ingresosLabel, gastosLabel],
                                                datasets: [{
                                                    data: [ingresos, gastos],
                                                    backgroundColor: [
                                                        'rgba(75, 192, 120, 0.8)',
                                                        'rgba(255, 99, 132, 0.8)'
                                                    ],
                                                    borderColor: [
                                                        'rgba(75, 192, 120, 1)',
                                                        'rgba(255, 99, 132, 1)'
                                                    ],
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        labels: {
                                                            font: {
                                                                size: 12
                                                            }
                                                        }
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(context) {
                                                                return context.label;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Budget Categories and Transactions Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Expense Categories -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold">Categorías de Gastos</h2>
                                @auth
                                    <button type="button" @click="$store.categoriaModal.open = true" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300" id="btnAddCategoria">
                                        <i class="fas fa-plus mr-2"></i> Añadir Gastos
                                    </button>
                                @else
                                    <button type="button" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300" id="btnAddCategoria">
                                        <i class="fas fa-plus mr-2"></i> Añadir Gastos
                                    </button>
                                @endauth
                            </div>
                            <div class="space-y-4">
                                @auth
                                    @if(count($categorias) > 0)
                                        @foreach($categorias as $categoria)
                                            <!-- Category Item -->
                                            @php
                                                $gastoCategoria = $categoria->transacciones->where('tipo', 'gasto')->sum('monto');
                                                $porcentajeGasto = $categoria->presupuesto > 0 ? round(($gastoCategoria / $categoria->presupuesto) * 100) : 0;
                                                $statusColor = $porcentajeGasto < 70 ? 'bg-' . $categoria->color . '-50 border-' . $categoria->color . '-500' : 
                                                              ($porcentajeGasto < 100 ? 'bg-yellow-50 border-yellow-500' : 'bg-red-50 border-red-500');
                                                $textColor = $porcentajeGasto < 70 ? 'text-' . $categoria->color . '-700' : 
                                                            ($porcentajeGasto < 100 ? 'text-yellow-700' : 'text-red-700');
                                            @endphp
                                            <div class="flex items-center justify-between p-4 {{ $statusColor }} rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 mb-2">
                                                <div class="flex items-center space-x-3">
                                                    <div class="p-3 bg-{{ $categoria->color }}-200 rounded-full shadow-inner">
                                                        <i class="fas {{ $categoria->icono }} text-{{ $categoria->color }}-600 text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-800">{{ $categoria->nombre }}</span>
                                                        <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                            <div class="{{ $porcentajeGasto < 70 ? 'bg-' . $categoria->color . '-500' : ($porcentajeGasto < 100 ? 'bg-yellow-500' : 'bg-red-500') }} h-1.5 rounded-full" style="width: {{ min($porcentajeGasto, 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <div class="flex items-center mb-1">
                                                        <span class="{{ $textColor }} font-bold mr-1">${{ number_format($gastoCategoria, 2) }}</span>
                                                        <span class="text-gray-500 text-sm">/ ${{ number_format($categoria->presupuesto, 2) }}</span>
                                                    </div>
                                                    <div class="flex space-x-2">
                                                        <button class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200" @click="$store.categoriaModal.open = true; $store.categoriaModal.item = {id: {{ $categoria->id }}, nombre: '{{ $categoria->nombre }}', icono: '{{ str_replace('fa-', '', $categoria->icono) }}', color: '{{ $categoria->color }}', presupuesto: {{ $categoria->presupuesto }} }">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <!-- Paginación de Categorías -->
                                        @if(isset($totalPaginasCategorias) && $totalPaginasCategorias > 1)
                                            <div class="flex justify-center items-center space-x-2 mt-4 pt-2 border-t border-gray-200">
                                                <!-- Botón Anterior -->
                                                <a href="{{ route('presupuesto.index', ['pagina_categorias' => max(1, $paginaCategorias - 1), 'pagina_transacciones' => $paginaTransacciones]) }}" 
                                                   class="px-3 py-1 rounded-md {{ $paginaCategorias <= 1 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                    <i class="fas fa-chevron-left text-xs"></i>
                                                </a>
                                                
                                                <!-- Números de Página -->
                                                @for($i = 1; $i <= $totalPaginasCategorias; $i++)
                                                    <a href="{{ route('presupuesto.index', ['pagina_categorias' => $i, 'pagina_transacciones' => $paginaTransacciones]) }}" 
                                                       class="px-3 py-1 rounded-md {{ $i == $paginaCategorias ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                        {{ $i }}
                                                    </a>
                                                @endfor
                                                
                                                <!-- Botón Siguiente -->
                                                <a href="{{ route('presupuesto.index', ['pagina_categorias' => min($totalPaginasCategorias, $paginaCategorias + 1), 'pagina_transacciones' => $paginaTransacciones]) }}" 
                                                   class="px-3 py-1 rounded-md {{ $paginaCategorias >= $totalPaginasCategorias ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                    <i class="fas fa-chevron-right text-xs"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-gray-500">No hay categorías de presupuesto. Añade una para comenzar.</p>
                                        </div>
                                    @endif
                                @else
                                    @foreach([['id' => 1, 'nombre' => 'Vivienda', 'icono' => 'fa-home', 'color' => 'blue', 'presupuesto' => 12000, 'gastoCategoria' => 10000], 
                                             ['id' => 2, 'nombre' => 'Alimentación', 'icono' => 'fa-utensils', 'color' => 'green', 'presupuesto' => 8000, 'gastoCategoria' => 6500], 
                                             ['id' => 3, 'nombre' => 'Transporte', 'icono' => 'fa-car', 'color' => 'purple', 'presupuesto' => 5000, 'gastoCategoria' => 4200]] as $categoria)
                                        @php
                                            $porcentajeGasto = $categoria['presupuesto'] > 0 ? round(($categoria['gastoCategoria'] / $categoria['presupuesto']) * 100) : 0;
                                            $statusColor = $porcentajeGasto < 70 ? 'bg-' . $categoria['color'] . '-50 border-' . $categoria['color'] . '-500' : 
                                                        ($porcentajeGasto < 100 ? 'bg-yellow-50 border-yellow-500' : 'bg-red-50 border-red-500');
                                            $textColor = $porcentajeGasto < 70 ? 'text-' . $categoria['color'] . '-700' : 
                                                        ($porcentajeGasto < 100 ? 'text-yellow-700' : 'text-red-700');
                                        @endphp
                                        <!-- Category Item (Demo) -->
                                        <div class="flex items-center justify-between p-4 {{ $statusColor }} rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 mb-2">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-3 bg-{{ $categoria['color'] }}-200 rounded-full shadow-inner">
                                                    <i class="fas {{ $categoria['icono'] }} text-{{ $categoria['color'] }}-600 text-lg"></i>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-800">{{ $categoria['nombre'] }}</span>
                                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                        <div class="{{ $porcentajeGasto < 70 ? 'bg-' . $categoria['color'] . '-500' : ($porcentajeGasto < 100 ? 'bg-yellow-500' : 'bg-red-500') }} h-1.5 rounded-full" style="width: {{ min($porcentajeGasto, 100) }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <div class="flex items-center mb-1">
                                                    <span class="{{ $textColor }} font-bold mr-1">${{ number_format($categoria['gastoCategoria'], 2) }}</span>
                                                    <span class="text-gray-500 text-sm">/ ${{ number_format($categoria['presupuesto'], 2) }}</span>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endauth
                            </div>
                        </div>
                        
                        <!-- Recent Transactions -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold">Transacciones Recientes</h2>
                                @auth
                                    <button type="button" @click="$store.transaccionModal.open = true" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300" id="btnAddTransaccion">
                                        <i class="fas fa-plus mr-2"></i> Añadir Transaccion
                                    </button>
                                @else
                                    <button type="button" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300" id="btnAddTransaccion">
                                        <i class="fas fa-plus mr-2"></i> Añadir Transaccion
                                    </button>
                                @endauth
                            </div>
                            <div class="space-y-4">
                                @auth
                                    @if(count($transacciones) > 0)
                                        @foreach($transacciones as $transaccion)
                                            <!-- Transaction Item -->
                                            <div class="flex items-center justify-between p-4 {{ $transaccion->tipo == 'ingreso' ? 'bg-green-50 hover:bg-green-100' : 'bg-red-50 hover:bg-red-100' }} rounded-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 {{ $transaccion->tipo == 'ingreso' ? 'border-green-500' : 'border-red-500' }} shadow-sm hover:shadow-md mb-2">
                                                <div class="flex items-center space-x-3">
                                                    <div class="p-3 {{ $transaccion->tipo == 'ingreso' ? 'bg-green-200' : 'bg-red-200' }} rounded-full shadow-inner">
                                                        <i class="fas {{ $transaccion->tipo == 'ingreso' ? 'fa-dollar-sign' : 'fa-shopping-cart' }} {{ $transaccion->tipo == 'ingreso' ? 'text-green-600' : 'text-red-600' }} text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-800">{{ $transaccion->descripcion }}</p>
                                                        <div class="flex items-center text-sm text-gray-500">
                                                            <i class="fas fa-calendar-alt mr-1 text-xs"></i>
                                                            <p>{{ $transaccion->fecha->format('d M Y') }}</p>
                                                            @if($transaccion->categoria)
                                                                <span class="mx-2">•</span>
                                                                <span class="px-2 py-1 rounded-full text-xs {{ $transaccion->tipo == 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-' . $transaccion->categoria->color . '-100 text-' . $transaccion->categoria->color . '-800' }}">
                                                                    <i class="fas {{ $transaccion->categoria->icono ?? 'fa-tag' }} mr-1 text-xs"></i>
                                                                    {{ $transaccion->categoria->nombre ?? 'Sin categoría' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-lg font-bold {{ $transaccion->tipo == 'ingreso' ? 'text-green-600' : 'text-red-600' }} mr-4">{{ $transaccion->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($transaccion->monto, 2) }}</span>
                                                    <div class="flex space-x-2">
                                                        <button class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200" @click="$store.transaccionModal.open = true; $store.transaccionModal.item = {id: {{ $transaccion->id }}, descripcion: '{{ $transaccion->descripcion }}', monto: {{ $transaccion->monto }}, tipo: '{{ $transaccion->tipo }}', fecha: '{{ $transaccion->fecha->format('Y-m-d') }}', categoria_id: {{ $transaccion->categoria_id ?? 'null' }} }">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('transacciones.destroy', $transaccion) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <!-- Paginación de Transacciones -->
                                        @if(isset($totalPaginasTransacciones) && $totalPaginasTransacciones > 1)
                                            <div class="flex justify-center items-center space-x-2 mt-4 pt-2 border-t border-gray-200">
                                                <!-- Botón Anterior -->
                                                <a href="{{ route('presupuesto.index', ['pagina_transacciones' => max(1, $paginaTransacciones - 1), 'pagina_categorias' => $paginaCategorias]) }}" 
                                                   class="px-3 py-1 rounded-md {{ $paginaTransacciones <= 1 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                    <i class="fas fa-chevron-left text-xs"></i>
                                                </a>
                                                
                                                <!-- Números de Página -->
                                                @for($i = 1; $i <= $totalPaginasTransacciones; $i++)
                                                    <a href="{{ route('presupuesto.index', ['pagina_transacciones' => $i, 'pagina_categorias' => $paginaCategorias]) }}" 
                                                       class="px-3 py-1 rounded-md {{ $i == $paginaTransacciones ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                        {{ $i }}
                                                    </a>
                                                @endfor
                                                
                                                <!-- Botón Siguiente -->
                                                <a href="{{ route('presupuesto.index', ['pagina_transacciones' => min($totalPaginasTransacciones, $paginaTransacciones + 1), 'pagina_categorias' => $paginaCategorias]) }}" 
                                                   class="px-3 py-1 rounded-md {{ $paginaTransacciones >= $totalPaginasTransacciones ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                                    <i class="fas fa-chevron-right text-xs"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-gray-500">No hay transacciones registradas. Añade una para comenzar.</p>
                                        </div>
                                    @endif
                                @else
                                    @foreach([
                                        ['id' => 1, 'descripcion' => 'Supermercado', 'monto' => 150, 'tipo' => 'gasto', 'fecha' => now()->subDays(3), 'categoria_id' => 2, 'categoria' => ['nombre' => 'Alimentación', 'icono' => 'fa-utensils', 'color' => 'green']],
                                        ['id' => 2, 'descripcion' => 'Ingreso Salario', 'monto' => 3000, 'tipo' => 'ingreso', 'fecha' => now()->subDays(5), 'categoria_id' => null, 'categoria' => null],
                                        ['id' => 3, 'descripcion' => 'Gasolina', 'monto' => 80, 'tipo' => 'gasto', 'fecha' => now()->subDays(2), 'categoria_id' => 3, 'categoria' => ['nombre' => 'Transporte', 'icono' => 'fa-car', 'color' => 'purple']]
                                    ] as $transaccion)
                                        <!-- Transaction Item (Demo) -->
                                        <div class="flex items-center justify-between p-4 {{ $transaccion['tipo'] == 'ingreso' ? 'bg-green-50 hover:bg-green-100' : 'bg-red-50 hover:bg-red-100' }} rounded-lg transition-all duration-300 transform hover:-translate-y-1 border-l-4 {{ $transaccion['tipo'] == 'ingreso' ? 'border-green-500' : 'border-red-500' }} shadow-sm hover:shadow-md mb-2">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-3 {{ $transaccion['tipo'] == 'ingreso' ? 'bg-green-200' : 'bg-red-200' }} rounded-full shadow-inner">
                                                    <i class="fas {{ $transaccion['tipo'] == 'ingreso' ? 'fa-dollar-sign' : 'fa-shopping-cart' }} {{ $transaccion['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }} text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $transaccion['descripcion'] }}</p>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-calendar-alt mr-1 text-xs"></i>
                                                        <p>{{ $transaccion['fecha']->format('d M Y') }}</p>
                                                        @if(isset($transaccion['categoria']) && $transaccion['categoria'])
                                                            <span class="mx-2">•</span>
                                                            <span class="px-2 py-1 rounded-full text-xs {{ $transaccion['tipo'] == 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-' . $transaccion['categoria']['color'] . '-100 text-' . $transaccion['categoria']['color'] . '-800' }}">
                                                                <i class="fas {{ $transaccion['categoria']['icono'] ?? 'fa-tag' }} mr-1 text-xs"></i>
                                                                {{ $transaccion['categoria']['nombre'] ?? 'Sin categoría' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-lg font-bold {{ $transaccion['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }} mr-4">{{ $transaccion['tipo'] == 'ingreso' ? '+' : '-' }}${{ number_format($transaccion['monto'], 2) }}</span>
                                                <div class="flex space-x-2">
                                                    <button class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Modals -->
    <!-- Categoria Modal -->
    <div x-cloak x-show="$store.categoriaModal.open" x-data="{selectedIcon: '', selectedColor: '', iconOptions: ['home', 'utensils', 'car', 'shopping-cart', 'wallet', 'money-bill', 'credit-card', 'piggy-bank', 'gift', 'tag', 'plane', 'graduation-cap', 'briefcase', 'heartbeat', 'gamepad', 'film', 'music', 'book', 'tshirt', 'dumbbell']}" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="categoria-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Background overlay -->
            <div x-show="$store.categoriaModal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="$store.categoriaModal.open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transform transition-all"
                x-init="
                    selectedIcon = $store.categoriaModal.item?.icono?.replace('fa-', '') || 'tag';
                    selectedColor = $store.categoriaModal.item?.color || 'blue';
                ">
                
                <!-- Category Modal Content -->
                <form x-bind:action="$store.categoriaModal.item ? '{{ url("/categorias") }}/' + $store.categoriaModal.item.id : '{{ route("categorias.store") }}'" method="POST">
                    @csrf
                    <template x-if="$store.categoriaModal.item">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <!-- Header with icon -->
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-2xl font-bold text-gray-900 flex items-center" id="categoria-modal-title">
                            <div class="mr-3 p-3 rounded-full" :class="`bg-${selectedColor}-100`">
                                <i class="fas fa-2x" :class="`fa-${selectedIcon} text-${selectedColor}-600`"></i>
                            </div>
                            <span x-text="$store.categoriaModal.item ? 'Editar Categoría' : 'Nueva Categoría'"></span>
                        </h3>
                        <button @click="$store.categoriaModal.open = false; $store.categoriaModal.item = null" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times fa-lg"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Nombre -->
                        <div class="relative">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Categoría</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" name="nombre" id="nombre" x-bind:value="$store.categoriaModal.item?.nombre || ''" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Alimentación, Transporte, etc." required>
                            </div>
                        </div>
                        
                        <!-- Icono Selector -->
                        <div>
                            <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">Elige un Icono</label>
                            <input type="hidden" name="icono" id="icono" x-bind:value="selectedIcon">
                            
                            <div class="grid grid-cols-5 gap-2 p-3 bg-gray-50 rounded-lg max-h-40 overflow-y-auto">
                                <template x-for="icon in iconOptions" :key="icon">
                                    <div 
                                        @click="selectedIcon = icon" 
                                        class="cursor-pointer p-3 rounded-lg flex items-center justify-center transition-all duration-200" 
                                        :class="selectedIcon === icon ? `bg-${selectedColor}-100 ring-2 ring-${selectedColor}-500` : 'hover:bg-gray-100'">
                                        <i class="fas" :class="`fa-${icon} text-${selectedColor}-600`"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Color Selector -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Elige un Color</label>
                            <input type="hidden" name="color" id="color" x-bind:value="selectedColor">
                            
                            <div class="grid grid-cols-5 gap-2 p-3 bg-gray-50 rounded-lg">
                                <template x-for="color in ['blue', 'red', 'green', 'yellow', 'purple', 'pink', 'indigo', 'gray', 'orange', 'teal']" :key="color">
                                    <div 
                                        @click="selectedColor = color" 
                                        class="cursor-pointer p-3 rounded-lg flex items-center justify-center transition-all duration-200" 
                                        :class="selectedColor === color ? `bg-${color}-100 ring-2 ring-${color}-500` : 'hover:bg-gray-100'">
                                        <div class="w-6 h-6 rounded-full" :class="`bg-${color}-500`"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Presupuesto -->
                        <div>
                            <label for="presupuesto" class="block text-sm font-medium text-gray-700 mb-1">Presupuesto</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-dollar-sign text-gray-400"></i>
                                </div>
                                <input type="number" name="presupuesto" id="presupuesto" x-bind:value="$store.categoriaModal.item?.presupuesto || 0" min="0" step="0.01" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="$store.categoriaModal.open = false; $store.categoriaModal.item = null" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span x-text="$store.categoriaModal.item ? 'Actualizar' : 'Guardar'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaccion Modal -->
    <div x-cloak x-show="$store.transaccionModal.open" x-data="{selectedTipo: 'gasto'}" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="transaccion-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Background overlay -->
            <div x-show="$store.transaccionModal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="$store.transaccionModal.open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle bg-white rounded-2xl shadow-xl transform transition-all"
                x-init="selectedTipo = $store.transaccionModal.item?.tipo || 'gasto'">
                
                <!-- Transaction Modal Content -->
                <form x-bind:action="$store.transaccionModal.item ? '{{ url("/transacciones") }}/' + $store.transaccionModal.item.id : '{{ route("transacciones.store") }}'" method="POST">
                    @csrf
                    <template x-if="$store.transaccionModal.item">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <!-- Header with icon -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="p-3 mr-3 rounded-full" :class="selectedTipo === 'ingreso' ? 'bg-green-100' : 'bg-red-100'">
                                <i class="fas" :class="selectedTipo === 'ingreso' ? 'fa-dollar-sign text-green-600' : 'fa-shopping-cart text-red-600'"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900" id="transaccion-modal-title">
                                <span x-text="$store.transaccionModal.item ? 'Editar Transacción' : 'Añadir Transacción'"></span>
                            </h3>
                        </div>
                        <button @click="$store.transaccionModal.open = false; $store.transaccionModal.item = null" type="button" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-5">
                        <!-- Tipo (Visual Selector) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Transacción</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div @click="selectedTipo = 'ingreso'" 
                                    class="cursor-pointer p-3 rounded-lg border-2 flex items-center justify-center transition-all duration-200" 
                                    :class="selectedTipo === 'ingreso' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'">
                                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                                    <span class="font-medium">Ingreso</span>
                                    <input type="radio" name="tipo" value="ingreso" x-bind:checked="selectedTipo === 'ingreso'" class="hidden">
                                </div>
                                <div @click="selectedTipo = 'gasto'" 
                                    class="cursor-pointer p-3 rounded-lg border-2 flex items-center justify-center transition-all duration-200" 
                                    :class="selectedTipo === 'gasto' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300'">
                                    <i class="fas fa-shopping-cart text-red-600 mr-2"></i>
                                    <span class="font-medium">Gasto</span>
                                    <input type="radio" name="tipo" value="gasto" x-bind:checked="selectedTipo === 'gasto'" class="hidden">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" name="descripcion" id="descripcion" x-bind:value="$store.transaccionModal.item?.descripcion || ''" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Compra supermercado, Pago salario, etc." required>
                            </div>
                        </div>
                        
                        <!-- Monto -->
                        <div>
                            <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-dollar-sign text-gray-400"></i>
                                </div>
                                <input type="number" name="monto" id="monto" x-bind:value="$store.transaccionModal.item?.monto || ''" min="0.01" step="0.01" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <!-- Fecha -->
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" name="fecha" id="fecha" x-bind:value="$store.transaccionModal.item?.fecha || new Date().toISOString().split('T')[0]" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                        </div>
                        
                        <!-- Categoría (solo para gastos) -->
                        <div x-show="selectedTipo === 'gasto'">
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-folder text-gray-400"></i>
                                </div>
                                <select name="categoria_id" id="categoria_id" x-bind:value="$store.transaccionModal.item?.categoria_id || ''" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" :required="selectedTipo === 'gasto'">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" x-bind:selected="$store.transaccionModal.item && $store.transaccionModal.item.categoria_id == {{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="$store.transaccionModal.open = false; $store.transaccionModal.item = null" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span x-text="$store.transaccionModal.item ? 'Actualizar' : 'Guardar'"></span>
                        </button>
                    </div>
    
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Verificar que Alpine.js esté correctamente inicializado
    console.log('Alpine disponible en DOMContentLoaded:', window.Alpine);
    
    // Añadir listeners a los botones para asegurar que abran los modales
    document.getElementById('btnAddCategoria')?.addEventListener('click', function() {
        console.log('Botón Añadir Categoría clickeado');
        if (window.Alpine) {
            window.Alpine.store('categoriaModal').open = true;
            console.log('Estado del modal después del clic:', window.Alpine.store('categoriaModal'));
        }
    });
    
    document.getElementById('btnAddTransaccion')?.addEventListener('click', function() {
        console.log('Botón Añadir Transacción clickeado');
        if (window.Alpine) {
            window.Alpine.store('transaccionModal').open = true;
            console.log('Estado del modal después del clic:', window.Alpine.store('transaccionModal'));
        }
    });
});

document.addEventListener('alpine:init', () => {
    console.log('Alpine inicializado en alpine:init');
    // Reinicializar los stores para asegurar que estén correctamente configurados
    if (window.Alpine) {
        window.Alpine.store('categoriaModal', {
            open: false,
            item: null
        });
        
        window.Alpine.store('transaccionModal', {
            open: false,
            item: null
        });
    }
});

document.addEventListener('alpine:load', () => {
    // Verificar que Alpine.js esté correctamente inicializado
    console.log('Alpine cargado completamente:', window.Alpine);
    console.log('Estado actual de los modales:', {
        categoriaModal: Alpine.store('categoriaModal'),
        transaccionModal: Alpine.store('transaccionModal')
    });
});

// Asegurarse de que los modales estén correctamente inicializados
setTimeout(() => {
    console.log('Verificando estado de modales después de 500ms:', {
        categoriaModal: Alpine.store('categoriaModal'),
        transaccionModal: Alpine.store('transaccionModal')
    });
    
    // Verificar si los botones tienen los eventos correctos
    const btnAddCategoria = document.getElementById('btnAddCategoria');
    const btnAddTransaccion = document.getElementById('btnAddTransaccion');
    
    console.log('Botones encontrados:', {
        btnAddCategoria: !!btnAddCategoria,
        btnAddTransaccion: !!btnAddTransaccion
    });
}, 500);
</script>