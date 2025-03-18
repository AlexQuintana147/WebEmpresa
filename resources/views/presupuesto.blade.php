<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Presupuesto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false,
                type: null,
                item: null
            })
        })
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
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
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Presupuesto Total</p>
                                    <h3 class="text-2xl font-bold">
                                        @auth
                                            ${{ number_format($presupuestoTotal ?? 0, 2) }}
                                        @else
                                            $50,000
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Spent Amount Card -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Gastado</p>
                                    <h3 class="text-2xl font-bold">
                                        @auth
                                            ${{ number_format($gastos ?? 0, 2) }}
                                        @else
                                            $32,450
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-3 bg-red-100 rounded-full">
                                    <i class="fas fa-chart-line text-red-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Remaining Amount Card -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Restante</p>
                                    <h3 class="text-2xl font-bold">
                                        @auth
                                            ${{ number_format($restante ?? 0, 2) }}
                                        @else
                                            $17,550
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Savings Card -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Ahorros</p>
                                    <h3 class="text-2xl font-bold">
                                        @auth
                                            ${{ number_format($ahorros ?? 0, 2) }}
                                        @else
                                            $5,000
                                        @endauth
                                    </h3>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-save text-purple-600 text-xl"></i>
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
                                    <span class="text-sm font-medium text-gray-700">
                                        @auth
                                            @php
                                                $porcentaje = $presupuestoTotal > 0 ? round(($gastos / $presupuestoTotal) * 100) : 0;
                                            @endphp
                                            {{ $porcentaje }}%
                                        @else
                                            65%
                                        @endauth
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: @auth {{ $porcentaje }}% @else 65% @endauth"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>$0</span>
                                    <span>
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
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-{{ $categoria->color }}-600 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
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
                                        <div class="flex items-end h-40 space-x-6">
                                            <!-- Income Bar -->
                                            <div class="flex flex-col items-center">
                                                @auth
                                                    @php
                                                        $maxValue = max($ingresos, $gastos);
                                                        $ingresoHeight = $maxValue > 0 ? ($ingresos / $maxValue) * 100 : 0;
                                                    @endphp
                                                    <div class="w-16 bg-gradient-to-t from-green-500 to-green-300 rounded-t-lg" style="height: {{ $ingresoHeight }}%"></div>
                                                    <div class="mt-2 text-sm font-medium">Ingresos</div>
                                                    <div class="text-xs text-gray-500">${{ number_format($ingresos, 2) }}</div>
                                                @else
                                                    <div class="w-16 bg-gradient-to-t from-green-500 to-green-300 rounded-t-lg" style="height: 80%"></div>
                                                    <div class="mt-2 text-sm font-medium">Ingresos</div>
                                                    <div class="text-xs text-gray-500">$40,000</div>
                                                @endauth
                                            </div>
                                            
                                            <!-- Expense Bar -->
                                            <div class="flex flex-col items-center">
                                                @auth
                                                    @php
                                                        $gastoHeight = $maxValue > 0 ? ($gastos / $maxValue) * 100 : 0;
                                                    @endphp
                                                    <div class="w-16 bg-gradient-to-t from-red-500 to-red-300 rounded-t-lg" style="height: {{ $gastoHeight }}%"></div>
                                                    <div class="mt-2 text-sm font-medium">Gastos</div>
                                                    <div class="text-xs text-gray-500">${{ number_format($gastos, 2) }}</div>
                                                @else
                                                    <div class="w-16 bg-gradient-to-t from-red-500 to-red-300 rounded-t-lg" style="height: 65%"></div>
                                                    <div class="mt-2 text-sm font-medium">Gastos</div>
                                                    <div class="text-xs text-gray-500">$32,450</div>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Budget Categories Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Expense Categories -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold">Categorías de Gastos</h2>
                                <button type="button" @click="$store.modal.open = true; $store.modal.type = 'categoria'" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300">
                                    <i class="fas fa-plus mr-2"></i> Añadir
                                </button>
                            </div>
                            <div class="space-y-4">
                                @auth
                                    @if(count($categorias) > 0)
                                        @foreach($categorias as $categoria)
                                            <!-- Category Item -->
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                                <div class="flex items-center space-x-3">
                                                    <div class="p-2 bg-{{ $categoria->color }}-100 rounded-lg">
                                                        <i class="fas {{ $categoria->icono }} text-{{ $categoria->color }}-600"></i>
                                                    </div>
                                                    <span class="font-medium">{{ $categoria->nombre }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-gray-600 mr-4">${{ number_format($categoria->presupuesto, 2) }}</span>
                                                    <div class="flex space-x-2">
                                                        <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: {{ $categoria->id }}, nombre: '{{ $categoria->nombre }}', icono: '{{ str_replace('fa-', '', $categoria->icono) }}', color: '{{ $categoria->color }}', presupuesto: {{ $categoria->presupuesto }} }">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-gray-500">No hay categorías de presupuesto. Añade una para comenzar.</p>
                                        </div>
                                    @endif
                                @else
                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-blue-100 rounded-lg">
                                                <i class="fas fa-home text-blue-600"></i>
                                            </div>
                                            <span class="font-medium">Vivienda</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$12,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 1, nombre: 'Vivienda', icono: 'home', color: 'blue', presupuesto: 12000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-green-100 rounded-lg">
                                                <i class="fas fa-utensils text-green-600"></i>
                                            </div>
                                            <span class="font-medium">Alimentación</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$8,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 2, nombre: 'Alimentación', icono: 'utensils', color: 'green', presupuesto: 8000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-purple-100 rounded-lg">
                                                <i class="fas fa-car text-purple-600"></i>
                                            </div>
                                            <span class="font-medium">Transporte</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$5,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 3, nombre: 'Transporte', icono: 'car', color: 'purple', presupuesto: 5000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold">Transacciones Recientes</h2>
                                <button type="button" @click="$store.modal.open = true; $store.modal.type = 'transaccion'" class="flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300">
                                    <i class="fas fa-plus mr-2"></i> Añadir
                                </button>
                            </div>
                            <div class="space-y-4">
                                @auth
                                    @if(count($transacciones) > 0)
                                        @foreach($transacciones as $transaccion)
                                            <!-- Transaction Item -->
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                                <div class="flex items-center space-x-3">
                                                    <div class="p-2 bg-{{ $transaccion->tipo == 'ingreso' ? 'green' : 'red' }}-100 rounded-lg">
                                                        <i class="fas {{ $transaccion->tipo == 'ingreso' ? 'fa-dollar-sign' : 'fa-shopping-cart' }} text-{{ $transaccion->tipo == 'ingreso' ? 'green' : 'red' }}-600"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">{{ $transaccion->descripcion }}</p>
                                                        <p class="text-sm text-gray-500">{{ $transaccion->fecha->format('d M Y') }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-{{ $transaccion->tipo == 'ingreso' ? 'green' : 'red' }}-600 mr-4">{{ $transaccion->tipo == 'ingreso' ? '+' : '-' }}${{ number_format($transaccion->monto, 2) }}</span>
                                                    <div class="flex space-x-2">
                                                        <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'transaccion'; $store.modal.item = {id: {{ $transaccion->id }}, descripcion: '{{ $transaccion->descripcion }}', monto: {{ $transaccion->monto }}, tipo: '{{ $transaccion->tipo }}', fecha: '{{ $transaccion->fecha->format('Y-m-d') }}', categoria_id: {{ $transaccion->categoria_id ?? 'null' }} }">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('transacciones.destroy', $transaccion) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-gray-500">No hay transacciones registradas. Añade una para comenzar.</p>
                                        </div>
                                    @endif
                                @else
                                    <!-- Transaction Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-red-100 rounded-lg">
                                                <i class="fas fa-shopping-cart text-red-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Supermercado</p>
                                                <p class="text-sm text-gray-500">23 Oct 2023</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-red-600 mr-4">-$150.00</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'transaccion'; $store.modal.item = {id: 1, descripcion: 'Supermercado', monto: 150, tipo: 'gasto', fecha: '2023-10-23', categoria_id: 2}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-green-100 rounded-lg">
                                                <i class="fas fa-utensils text-green-600"></i>
                                            </div>
                                            <span class="font-medium">Alimentación</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$8,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 2, nombre: 'Alimentación', icono: 'utensils', color: 'green', presupuesto: 8000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-purple-100 rounded-lg">
                                                <i class="fas fa-car text-purple-600"></i>
                                            </div>
                                            <span class="font-medium">Transporte</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$5,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 3, nombre: 'Transporte', icono: 'car', color: 'purple', presupuesto: 5000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Transaction Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-green-100 rounded-lg">
                                                <i class="fas fa-dollar-sign text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Ingreso Salario</p>
                                                <p class="text-sm text-gray-500">20 Oct 2023</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-green-600 mr-4">+$3,000.00</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'transaccion'; $store.modal.item = {id: 2, descripcion: 'Ingreso Salario', monto: 3000, tipo: 'ingreso', fecha: '2023-10-20', categoria_id: null}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-green-100 rounded-lg">
                                                <i class="fas fa-utensils text-green-600"></i>
                                            </div>
                                            <span class="font-medium">Alimentación</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$8,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 2, nombre: 'Alimentación', icono: 'utensils', color: 'green', presupuesto: 8000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Item (Demo) -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-purple-100 rounded-lg">
                                                <i class="fas fa-car text-purple-600"></i>
                                            </div>
                                            <span class="font-medium">Transporte</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-gray-600 mr-4">$5,000</span>
                                            <div class="flex space-x-2">
                                                <button class="text-blue-500 hover:text-blue-700" @click="$store.modal.open = true; $store.modal.type = 'categoria'; $store.modal.item = {id: 3, nombre: 'Transporte', icono: 'car', color: 'purple', presupuesto: 5000}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="alert('Esta es una demostración. Inicia sesión para realizar esta acción.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                @endauth
                            </div>
                        </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Modals -->
    <div x-cloak x-show="$store.modal.open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="$store.modal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="$store.modal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <!-- Category Modal -->
                <div x-show="$store.modal.type === 'categoria'">
                    <form x-bind:action="$store.modal.item ? '{{ url("/categorias") }}/' + $store.modal.item.id : '{{ route("categorias.store") }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" x-if="$store.modal.item">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        <span x-text="$store.modal.item ? 'Editar Categoría' : 'Añadir Categoría'"></span>
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <!-- Nombre -->
                                        <div>
                                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                            <input type="text" name="nombre" id="nombre" x-bind:value="$store.modal.item?.nombre || ''" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Icono -->
                                        <div>
                                            <label for="icono" class="block text-sm font-medium text-gray-700">Icono</label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                                    <i class="fas" :class="$store.modal.item?.icono ? 'fa-' + $store.modal.item.icono.replace('fa-', '') : 'fa-tag'"></i>
                                                </span>
                                                <input type="text" name="icono" id="icono" x-bind:value="$store.modal.item?.icono?.replace('fa-', '') || 'tag'" placeholder="tag, home, car, etc." class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300">
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Usa nombres de iconos de Font Awesome (sin el prefijo fa-)</p>
                                        </div>
                                        
                                        <!-- Color -->
                                        <div>
                                            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                                            <select id="color" name="color" x-bind:value="$store.modal.item?.color || 'blue'" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <option value="blue">Azul</option>
                                                <option value="red">Rojo</option>
                                            <option value="green">Verde</option>
                                            <option value="yellow">Amarillo</option>
                                            <option value="purple">Morado</option>
                                            <option value="pink">Rosa</option>
                                            <option value="indigo">Índigo</option>
                                            <option value="gray">Gris</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Presupuesto -->
                                    <div>
                                        <label for="presupuesto" class="block text-sm font-medium text-gray-700">Presupuesto</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">$</span>
                                            <input type="number" name="presupuesto" id="presupuesto" x-bind:value="$store.modal.item?.presupuesto || 0" min="0" step="0.01" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-text="$store.modal.item ? 'Actualizar' : 'Guardar'"></span>
                        </button>
                        <button @click="$store.modal.open = false; $store.modal.item = null" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
                
                <!-- Transaction Modal -->
                <div x-show="$store.modal.type === 'transaccion'">
                    <form x-bind:action="$store.modal.item ? '{{ url("/transacciones") }}/' + $store.modal.item.id : '{{ route("transacciones.store") }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" x-if="$store.modal.item">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        <span x-text="$store.modal.item ? 'Editar Transacción' : 'Añadir Transacción'"></span>
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <!-- Descripción -->
                                        <div>
                                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                                            <input type="text" name="descripcion" id="descripcion" x-bind:value="$store.modal.item?.descripcion || ''" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Monto -->
                                        <div>
                                            <label for="monto" class="block text-sm font-medium text-gray-700">Monto</label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">$</span>
                                                <input type="number" name="monto" id="monto" x-bind:value="$store.modal.item?.monto || 0" min="0" step="0.01" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300" required>
                                            </div>
                                        </div>
                                        
                                        <!-- Tipo -->
                                        <div>
                                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                                            <select id="tipo" name="tipo" x-bind:value="$store.modal.item?.tipo || 'gasto'" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                                <option value="gasto">Gasto</option>
                                                <option value="ingreso">Ingreso</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Fecha -->
                                        <div>
                                            <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                                            <input type="date" name="fecha" id="fecha" x-bind:value="$store.modal.item?.fecha || new Date().toISOString().split('T')[0]" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Categoría -->
                                        <div>
                                            <label for="categoria_id" class="block text-sm font-medium text-gray-700">Categoría</label>
                                            <select id="categoria_id" name="categoria_id" x-bind:value="$store.modal.item?.categoria_id || ''" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <option value="">Sin categoría</option>
                                                @auth
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="1">Vivienda</option>
                                                    <option value="2">Alimentación</option>
                                                    <option value="3">Transporte</option>
                                                @endauth
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <span x-text="$store.modal.item ? 'Actualizar' : 'Guardar'"></span>
                            </button>
                            <button @click="$store.modal.open = false; $store.modal.item = null" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>