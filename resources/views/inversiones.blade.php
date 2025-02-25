<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inversiones</title>
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
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Gestión de Inversiones</h1>
                        <p class="text-xl text-gray-600">Monitorea y administra tu portafolio de inversiones</p>
                    </div>

                    <!-- Investment Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Portfolio Value -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Valor Total del Portafolio</p>
                                    <h3 class="text-2xl font-bold">$125,000</h3>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Returns -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Rendimiento Total</p>
                                    <h3 class="text-2xl font-bold text-green-600">+15.8%</h3>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Investments -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Inversiones Activas</p>
                                    <h3 class="text-2xl font-bold">8</h3>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-briefcase text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Profit -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Beneficio Mensual</p>
                                    <h3 class="text-2xl font-bold">$2,450</h3>
                                </div>
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Portfolio Distribution -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold mb-4">Distribución del Portafolio</h2>
                            <div class="space-y-4">
                                <!-- Investment Category -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <i class="fas fa-building text-blue-600"></i>
                                        </div>
                                        <span class="font-medium">Bienes Raíces</span>
                                    </div>
                                    <span class="text-gray-600">40%</span>
                                </div>

                                <!-- Investment Category -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <i class="fas fa-chart-bar text-green-600"></i>
                                        </div>
                                        <span class="font-medium">Acciones</span>
                                    </div>
                                    <span class="text-gray-600">35%</span>
                                </div>

                                <!-- Investment Category -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-yellow-100 rounded-lg">
                                            <i class="fas fa-coins text-yellow-600"></i>
                                        </div>
                                        <span class="font-medium">Criptomonedas</span>
                                    </div>
                                    <span class="text-gray-600">25%</span>
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
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <i class="fas fa-arrow-up text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Compra de Acciones</p>
                                            <p class="text-sm text-gray-500">23 Oct 2023</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600">+$5,000</span>
                                </div>

                                <!-- Transaction Item -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-red-100 rounded-lg">
                                            <i class="fas fa-arrow-down text-red-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Venta de Criptomonedas</p>
                                            <p class="text-sm text-gray-500">20 Oct 2023</p>
                                        </div>
                                    </div>
                                    <span class="text-red-600">-$2,500</span>
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