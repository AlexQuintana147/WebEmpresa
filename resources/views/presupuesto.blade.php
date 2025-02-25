<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>presupuesto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
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
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Presupuesto Total</p>
                                    <h3 class="text-2xl font-bold">$50,000</h3>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Spent Amount Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Gastado</p>
                                    <h3 class="text-2xl font-bold">$32,450</h3>
                                </div>
                                <div class="p-3 bg-red-100 rounded-full">
                                    <i class="fas fa-chart-line text-red-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Remaining Amount Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Restante</p>
                                    <h3 class="text-2xl font-bold">$17,550</h3>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Savings Card -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Ahorros</p>
                                    <h3 class="text-2xl font-bold">$5,000</h3>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-save text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Categories -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Expense Categories -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4">Categorías de Gastos</h2>
                            <div class="space-y-4">
                                <!-- Category Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <i class="fas fa-home text-blue-600"></i>
                                        </div>
                                        <span class="font-medium">Vivienda</span>
                                    </div>
                                    <span class="text-gray-600">$12,000</span>
                                </div>

                                <!-- Category Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <i class="fas fa-utensils text-green-600"></i>
                                        </div>
                                        <span class="font-medium">Alimentación</span>
                                    </div>
                                    <span class="text-gray-600">$8,000</span>
                                </div>

                                <!-- Category Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-purple-100 rounded-lg">
                                            <i class="fas fa-car text-purple-600"></i>
                                        </div>
                                        <span class="font-medium">Transporte</span>
                                    </div>
                                    <span class="text-gray-600">$5,000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4">Transacciones Recientes</h2>
                            <div class="space-y-4">
                                <!-- Transaction Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-red-100 rounded-lg">
                                            <i class="fas fa-shopping-cart text-red-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Supermercado</p>
                                            <p class="text-sm text-gray-500">23 Oct 2023</p>
                                        </div>
                                    </div>
                                    <span class="text-red-600">-$150.00</span>
                                </div>

                                <!-- Transaction Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <i class="fas fa-dollar-sign text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Ingreso Salario</p>
                                            <p class="text-sm text-gray-500">20 Oct 2023</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600">+$3,000.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>