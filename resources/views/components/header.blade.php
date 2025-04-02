<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-check" content="{{ Auth::check() }}">
    <title>Clínica Ricardo Palma</title>
    <style>[x-cloak]{display:none !important;}</style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            // Store para el modal de login
            Alpine.store('modal', {
                open: false,
                type: null,
                item: null
            });
            
            // No inicializar los stores de presupuesto aquí para evitar conflictos
            // Los stores específicos se inicializan en sus respectivas páginas
            
            console.log('Store modal inicializado en header:', {
                modal: Alpine.store('modal')
            });
        })
    </script>
</head>
<body>
<header class="bg-gradient-to-br from-cyan-900 via-blue-800 to-teal-900 shadow-lg border-b border-cyan-700 text-white relative overflow-hidden" x-data="{ isLogin: true }" x-cloak>
    
    <!-- Patrón médico decorativo en el fondo -->
    <div class="absolute inset-0 bg-gradient-to-b from-cyan-500/10 to-teal-500/10 pointer-events-none"></div>
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-5 left-10 w-6 h-6 border-2 border-white rounded-full"></div>
        <div class="absolute top-10 right-20 w-4 h-8 border-2 border-white rounded-full"></div>
        <div class="absolute bottom-5 left-1/4 w-8 h-8 border-2 border-white rotate-45"></div>
        <div class="absolute top-1/2 right-1/3 w-6 h-6 border-2 border-white rounded-md"></div>
    </div>
    
    <!-- Contenido principal del header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Indicador de salud del sistema -->
            <div class="hidden md:flex items-center space-x-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400/20 to-cyan-500/20">
                    @auth
                        <i class="fas fa-heartbeat text-cyan-400 text-lg animate-pulse-medical"></i>
                    @else
                        <i class="fas fa-heart-crack text-red-400 text-lg"></i>
                    @endauth
                </div>
                <div class="flex flex-col">
                    @auth
                        <span class="text-cyan-300 text-xs font-medium">Sistema Médico Activo</span>
                        <span class="text-cyan-100 text-xs">{{ now()->timezone('America/Lima')->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="text-red-300 text-xs font-medium">Sistema Inactivo</span>
                        <span class="text-gray-400 text-xs">Inicie sesión para acceder</span>
                    @endauth
                </div>
            </div>

            <!-- Profile and Login Section -->
            <div class="flex items-center justify-end w-full md:w-auto">
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <div class="relative cursor-pointer transform transition-all duration-300 hover:scale-105">
                            <!-- Gradient border animation container -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-teal-600 rounded-full opacity-0 group-hover:opacity-100 blur transition-opacity duration-300"></div>
                            
                            <!-- Profile image container -->
                            <div class="relative bg-gradient-to-br from-cyan-900 to-teal-900 p-0.5 rounded-full">
                                <img class="h-12 w-12 rounded-full object-cover shadow-lg ring-2 ring-cyan-700/50 transform transition-all duration-300 group-hover:ring-cyan-500/50" 
                                    src="{{ Auth::user()->imagen ?? asset('images/iconPerfil.png') }}"
                                    alt="Profile picture">
                            </div>
                            
                            <!-- Status indicator -->
                            <div class="absolute -bottom-1 -right-1 rounded-full p-1 bg-gradient-to-r from-cyan-900 to-teal-900">
                                @auth
                                    <div class="h-3.5 w-3.5 rounded-full bg-gradient-to-r from-green-400 to-green-500 shadow-lg shadow-green-500/50 group-hover:animate-pulse"></div>
                                @else
                                    <div class="h-3.5 w-3.5 rounded-full bg-gradient-to-r from-red-400 to-red-500 shadow-lg shadow-red-500/50 group-hover:animate-pulse"></div>
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        @auth
                            <div class="flex items-center space-x-4">
                                <span class="text-white text-base font-medium">{{ Auth::user()->nombre }}</span>
                                <button onclick="window.location.href='/opciones'" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-teal-600 text-white text-sm font-medium rounded-lg 
                                               border-2 border-cyan-400/30
                                               hover:from-cyan-600 hover:to-teal-700
                                               focus:ring-4 focus:ring-cyan-300/50
                                               shadow-md hover:shadow-xl
                                               transform hover:-translate-y-0.5
                                               transition-all duration-300 ease-out
                                               active:scale-95">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-user-md w-5 h-5"></i>
                                        <span>Opciones</span>
                                    </span>
                                </button>
                                <button onclick="logout()" 
                                        class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg 
                                               hover:from-red-700 hover:to-red-800 
                                               focus:ring-4 focus:ring-red-300/50 
                                               shadow-md hover:shadow-xl 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-300 ease-out 
                                               active:scale-95">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-sign-out-alt w-5 h-5"></i>
                                        <span>Salir</span>
                                    </span>
                                </button>
                            </div>
                        @else
                            <button @click="$store.modal.open = true" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-sm font-medium rounded-lg 
                                           hover:from-cyan-700 hover:to-teal-800 
                                           focus:ring-4 focus:ring-cyan-300/50 
                                           shadow-md hover:shadow-xl 
                                           transform hover:-translate-y-0.5 
                                           transition-all duration-300 ease-out 
                                           active:scale-95">
                                <span class="flex items-center space-x-2">
                                    <i class="fas fa-user-md w-5 h-5"></i>
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
         class="fixed inset-0 bg-cyan-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center">
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
                <form x-show="isLogin" class="space-y-5" id="loginForm">
                    @csrf
                    <!-- Área para mensajes de error -->
                    <div id="login-error" class="hidden p-3 mb-3 text-sm text-red-700 bg-red-100 rounded-lg" role="alert"></div>
                    
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" id="email" name="correo" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                        <div class="invalid-feedback text-red-600 text-sm mt-1 hidden"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                        <input type="password" id="password" name="contrasena" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                        <div class="invalid-feedback text-red-600 text-sm mt-1 hidden"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="h-4 w-4 text-cyan-600 focus:ring-cyan-500/20 border-gray-300 rounded transition duration-200">
                            <label for="remember" class="ml-2 block text-sm text-gray-600">Recordarme</label>
                        </div>
                        <a href="#" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium transition duration-200">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-sm font-semibold rounded-xl 
                                       hover:from-cyan-700 hover:to-teal-800 
                                       focus:ring-4 focus:ring-cyan-500/20 
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

                <!-- Register Form -->
                <form x-show="!isLogin" class="space-y-5">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="reg-name" class="block text-sm font-semibold text-gray-700">Nombre</label>
                        <input type="text" id="reg-name" name="name" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" id="reg-email" name="email" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                        <input type="password" id="reg-password" name="password" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-1.5">
                        <label for="reg-password-confirm" class="block text-sm font-semibold text-gray-700">Confirmar Contraseña</label>
                        <input type="password" id="reg-password-confirm" name="password_confirmation" 
                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                      transition duration-200 text-gray-900">
                    </div>

                    <div class="space-y-3 pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-sm font-semibold rounded-xl 
                                       hover:from-cyan-700 hover:to-teal-800 
                                       focus:ring-4 focus:ring-cyan-500/20 
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
                            Ya tengo una cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

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
        .then(response => {
            if (response.ok) {
                window.location.href = '/';
            }
        })
        .catch(error => {
            console.error('Error al cerrar sesión:', error);
        });
    }
    
    // Sistema de notificaciones para el login
    function showNotification(message, type = 'error') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        notification.style.transform = 'translateX(100%)';

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });

        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
</script>

<!-- Incluir el script de login.js -->
<script src="{{ asset('js/login.js') }}"></script>