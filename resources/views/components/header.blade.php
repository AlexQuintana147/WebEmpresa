<header class="bg-white shadow-lg border-b border-gray-100" x-data>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-end h-16">
            <!-- Profile Dropdown -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img class="h-10 w-10 rounded-full border-2 border-gray-300 object-cover shadow-sm" 
                        src="https://ui-avatars.com/api/?background=random&color=fff&name=User" 
                        alt="Default profile picture">
                    <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-gray-400"></div>
                </div>
                <div class="flex flex-col">
                    <button @click="$store.modal.open = true" 
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg 
                                   hover:from-blue-700 hover:to-blue-800 
                                   focus:ring-4 focus:ring-blue-300 
                                   shadow-md hover:shadow-lg 
                                   transform hover:-translate-y-0.5 
                                   transition-all duration-200 ease-in-out">
                        Ingresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-data
         x-show="$store.modal.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="$store.modal.open = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-5 border w-96 shadow-xl rounded-lg bg-white"
             x-show="$store.modal.open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-700">Iniciar Sesión</h3>
                    <button @click="$store.modal.open = false"
                            class="text-gray-600 hover:text-gray-800 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" id="password" name="password" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Recordarme</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg 
                                   hover:from-blue-700 hover:to-blue-800 
                                   focus:ring-4 focus:ring-blue-300 
                                   shadow-md hover:shadow-lg 
                                   transform hover:-translate-y-0.5 
                                   transition-all duration-200 ease-in-out">
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>