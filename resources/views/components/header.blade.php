<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-check" content="{{ Auth::check() }}">
    <title>WebEmpresa</title>
    <style>[x-cloak]{display:none !important;}</style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            })
        })
    </script>
</head>
<body>
<header class="bg-gradient-to-br from-gray-900 via-gray-800 to-black shadow-lg border-b border-gray-700 text-white relative overflow-hidden" x-data="{ isLogin: true }" x-cloak>
    
    <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-purple-500/5 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <!-- Profile and Login Section -->
            <div class="flex items-center justify-end w-full">
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <div class="relative cursor-pointer transform transition-all duration-300 hover:scale-105">
                            <!-- Gradient border animation container -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full opacity-0 group-hover:opacity-100 blur transition-opacity duration-300"></div>
                            
                            <!-- Profile image container -->
                            <div class="relative bg-gradient-to-br from-gray-900 to-black p-0.5 rounded-full">
                                <img class="h-12 w-12 rounded-full object-cover shadow-lg ring-2 ring-gray-700/50 transform transition-all duration-300 group-hover:ring-red-500/50" 
                                    src="{{ asset('images/iconPerfil.png') }}"
                                    alt="Profile picture">
                            </div>
                            
                            <!-- Status indicator -->
                            <div class="absolute -bottom-1 -right-1 rounded-full p-1 bg-gradient-to-r from-gray-900 to-black">
                                <div class="h-3.5 w-3.5 rounded-full bg-gradient-to-r from-red-400 to-red-500 shadow-lg shadow-red-500/50 group-hover:animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        @auth
                            <div class="flex items-center space-x-4">
                                <span class="text-white text-sm font-medium">{{ Auth::user()->nombre }}</span>
                                <button onclick="logout()" 
                                        class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg 
                                               hover:from-red-700 hover:to-red-800 
                                               focus:ring-4 focus:ring-red-300/50 
                                               shadow-md hover:shadow-xl 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-300 ease-out 
                                               active:scale-95">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Salir</span>
                                    </span>
                                </button>
                            </div>
                        @else
                            <button @click="$store.modal.open = true" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg 
                                           hover:from-blue-700 hover:to-blue-800 
                                           focus:ring-4 focus:ring-blue-300/50 
                                           shadow-md hover:shadow-xl 
                                           transform hover:-translate-y-0.5 
                                           transition-all duration-300 ease-out 
                                           active:scale-95">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Ingresar</span>
                                </span>
                            </button>
                        @endauth
                    </div>
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
         class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-8 border-0 w-[28rem] shadow-2xl rounded-2xl bg-white"
             x-show="$store.modal.open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="mt-2">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800" x-text="isLogin ? 'Iniciar Sesión' : 'Crear Cuenta'"></h3>
                    <button @click="$store.modal.open = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Login Form -->
                <form x-show="isLogin" class="space-y-5">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" id="email" name="email" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                        <input type="password" id="password" name="password" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500/20 border-gray-300 rounded transition duration-200">
                            <label for="remember" class="ml-2 block text-sm text-gray-600">Recordarme</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition duration-200">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl 
                                       hover:from-blue-700 hover:to-blue-800 
                                       focus:ring-4 focus:ring-blue-500/20 
                                       shadow-md hover:shadow-lg 
                                       transform hover:-translate-y-0.5 
                                       transition-all duration-200 ease-in-out">
                            Iniciar Sesión
                        </button>
                        <button type="button" 
                                @click="isLogin = false"
                                class="w-full py-3 px-4 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 text-sm font-semibold rounded-xl 
                                       border border-gray-200
                                       hover:from-gray-100 hover:to-gray-200 
                                       focus:ring-4 focus:ring-gray-200 
                                       shadow-sm hover:shadow 
                                       transform hover:-translate-y-0.5 
                                       transition-all duration-200 ease-in-out">
                            Crear una cuenta
                        </button>
                    </div>
                </form>

                <!-- Registration Form -->
                <form x-show="!isLogin" class="space-y-5">
                    <div class="space-y-1.5">
                        <label for="reg-name" class="block text-sm font-semibold text-gray-700">Nombre Completo</label>
                        <input type="text" id="reg-name" name="nombre" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900"
                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+"
                               title="Por favor ingrese solo letras (se permiten acentos y ñ)"
                               onkeypress="return /[A-Za-zÁáÉéÍíÓóÚúÑñ\s]/i.test(event.key)"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Solo se permiten letras en este campo</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" id="reg-email" name="correo" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                        <input type="password" id="reg-password" name="contrasena" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900"
                               pattern="^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,}$"
                               title="La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un carácter especial">
                        <p class="mt-1 text-sm text-gray-500">
                            La contraseña debe contener:
                            <span class="block mt-1 ml-2">• Mínimo 6 caracteres</span>
                            <span class="block ml-2">• Al menos una letra mayúscula</span>
                            <span class="block ml-2">• Al menos un número</span>
                            <span class="block ml-2">• Al menos un carácter especial (!@#$%^&*)</span>
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-password-confirmation" class="block text-sm font-semibold text-gray-700">Confirmar Contraseña</label>
                        <input type="password" id="reg-password-confirmation" name="password_confirmation" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-3 pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl 
                                       hover:from-blue-700 hover:to-blue-800 
                                       focus:ring-4 focus:ring-blue-500/20 
                                       shadow-md hover:shadow-lg 
                                       transform hover:-translate-y-0.5 
                                       transition-all duration-200 ease-in-out">
                            Crear Cuenta
                        </button>
                        <button type="button" 
                                @click="isLogin = true"
                                class="w-full py-3 px-4 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 text-sm font-semibold rounded-xl 
                                       border border-gray-200
                                       hover:from-gray-100 hover:to-gray-200 
                                       focus:ring-4 focus:ring-gray-200 
                                       shadow-sm hover:shadow 
                                       transform hover:-translate-y-0.5 
                                       transition-all duration-200 ease-in-out">
                            Volver al Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>
<script src="{{ asset('js/register.js') }}"></script>
<script src="{{ asset('js/login.js') }}"></script>
<script>
function logout() {
    fetch('/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        window.location.href = '/';
    })
    .catch(error => {
        console.error('Error during logout:', error);
    });
}
</script>
</body>
</html>