<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session('success'))
    <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if(session('error'))
    <meta name="error-message" content="{{ session('error') }}">
    @endif
    <title>Atención Médica - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        
        /* Estilos médicos personalizados */
        .medical-gradient {
            background: linear-gradient(135deg, #e6f7ff 0%, #cce7f8 100%);
            position: relative;
            overflow: hidden;
        }
        .medical-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.5;
            pointer-events: none;
        }
        .medical-card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .medical-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #0891b2);
        }
        .medical-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        @keyframes pulse-medical {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .animate-pulse-medical {
            animation: pulse-medical 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes float-medical {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float-medical {
            animation: float-medical 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-blue-50">
    <div class="min-h-screen flex" x-data="{ 
        modalOpen: false,
        step: 1,
        dni: '',
        pacienteExiste: false,
        pacienteData: null,
        formData: {
            dni: '',
            nombre: '',
            apellido_paterno: '',
            apellido_materno: '',
            correo: '',
            telefono: ''
        },
        errors: {},
        loading: false,
        verificarDni() {
            if (this.dni.length !== 8) {
                this.errors = { dni: 'El DNI debe tener 8 dígitos' };
                return;
            }
            
            this.loading = true;
            this.errors = {};
            
            fetch('/pacientes/verificar-dni', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                },
                body: JSON.stringify({ dni: this.dni })
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    if (data.exists) {
                        this.pacienteExiste = true;
                        this.pacienteData = data.paciente;
                        this.step = 3; // Saltar al paso de asociación
                    } else {
                        this.formData.dni = this.dni;
                        this.step = 2; // Ir al formulario de registro
                    }
                } else {
                    this.errors = data.errors || { dni: data.message };
                }
            })
            .catch(error => {
                this.loading = false;
                this.errors = { general: 'Ocurrió un error al verificar el DNI' };
                console.error('Error:', error);
            });
        }
    }" x-cloak>
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <x-header />
            
            <!-- Contenido Principal -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Título de la página con decoración médica -->
                <div class="mb-10 text-center relative">
                    <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                        <div class="absolute top-10 left-10 w-8 h-8 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 0s;"></div>
                        <div class="absolute top-40 right-20 w-6 h-12 border-2 border-teal-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 1s;"></div>
                        <div class="absolute bottom-20 left-1/4 w-10 h-10 border-2 border-blue-200 rotate-45 opacity-20 animate-float-medical" style="animation-delay: 2s;"></div>
                        <div class="absolute top-1/3 right-1/3 w-8 h-8 border-2 border-cyan-200 rounded-md opacity-20 animate-float-medical" style="animation-delay: 3s;"></div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-cyan-800 mb-2">Atención Médica</h1>
                    <div class="h-1 w-24 bg-gradient-to-r from-cyan-500 to-teal-500 mx-auto mb-4"></div>
                    <p class="text-xl text-cyan-700">Obtenga su cita médica de manera rápida y segura</p>
                </div>
                
                <!-- Tarjetas informativas sobre la importancia del DNI -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="medical-card bg-white p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-cyan-100 flex items-center justify-center mb-4 animate-pulse-medical">
                            <i class="fas fa-id-card text-cyan-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-cyan-800 mb-2">Identificación Segura</h3>
                        <p class="text-gray-600">Su DNI nos permite verificar su identidad de manera segura y proteger su información médica confidencial.</p>
                    </div>
                    
                    <div class="medical-card bg-white p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-teal-100 flex items-center justify-center mb-4 animate-pulse-medical" style="animation-delay: 0.5s;">
                            <i class="fas fa-file-medical-alt text-teal-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-teal-800 mb-2">Historial Médico</h3>
                        <p class="text-gray-600">Accedemos a su historial médico para brindarle una atención personalizada y evitar procedimientos duplicados.</p>
                    </div>
                    
                    <div class="medical-card bg-white p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4 animate-pulse-medical" style="animation-delay: 1s;">
                            <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Proceso Simplificado</h3>
                        <p class="text-gray-600">Con su DNI, agilizamos el proceso de registro y programación de citas, ahorrándole tiempo en trámites administrativos.</p>
                    </div>
                </div>
                
                <!-- Contenedor principal del formulario -->
                <div class="max-w-3xl mx-auto">
                    <!-- Paso 1: Verificación de DNI -->
                    <div x-show="step === 1" class="medical-card bg-white p-8 text-center">
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-6">Ingrese su DNI para comenzar</h2>
                        <p class="text-gray-600 mb-8">Para brindarle una atención personalizada, necesitamos verificar su identidad mediante su DNI.</p>
                        
                        <div class="max-w-md mx-auto">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    x-model="dni" 
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" 
                                    placeholder="Ingrese su DNI (8 dígitos)" 
                                    maxlength="8"
                                    @keyup.enter="verificarDni"
                                >
                            </div>
                            <div x-show="errors.dni" class="text-red-500 text-sm mt-2" x-text="errors.dni"></div>
                            
                            <button 
                                @click="verificarDni" 
                                class="mt-6 w-full bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300 flex items-center justify-center"
                                :disabled="loading"
                            >
                                <span x-show="!loading">Verificar DNI</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Verificando...
                                </span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Paso 2: Formulario de registro de paciente -->
                    <div x-show="step === 2" class="medical-card bg-white p-8">
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-6 text-center">Complete sus datos personales</h2>
                        <p class="text-gray-600 mb-8 text-center">No encontramos su DNI en nuestro sistema. Por favor, complete el siguiente formulario para registrarse.</p>
                        
                        <form action="{{ route('pacientes.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="dni" x-model="formData.dni">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                                </div>
                                
                                <div>
                                    <label for="apellido_paterno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                                    <input type="text" id="apellido_paterno" name="apellido_paterno" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                                </div>
                                
                                <div>
                                    <label for="apellido_materno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                                    <input type="text" id="apellido_materno" name="apellido_materno" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                                </div>
                                
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <input type="email" id="correo" name="correo" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4">
                                <button type="button" @click="step = 1" class="text-cyan-600 hover:text-cyan-800 font-medium flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Volver
                                </button>
                                
                                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300">
                                    Registrar y Continuar
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Paso 3: Asociar paciente existente -->
                    <div x-show="step === 3" class="medical-card bg-white p-8 text-center">
                        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 animate-pulse-medical">
                            <i class="fas fa-user-check text-green-600 text-3xl"></i>
                        </div>
                        
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-4">¡Bienvenido de nuevo!</h2>
                        <p class="text-gray-600 mb-6">Hemos encontrado sus datos en nuestro sistema.</p>
                        
                        <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                            <h3 class="text-lg font-medium text-blue-800 mb-4">Información del Paciente</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Nombre completo:</p>
                                    <p class="font-medium text-gray-800" x-text="pacienteData?.nombre + ' ' + pacienteData?.apellido_paterno + ' ' + pacienteData?.apellido_materno"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">DNI:</p>
                                    <p class="font-medium text-gray-800" x-text="pacienteData?.dni"></p>
                                </div>
                                <div x-show="pacienteData?.telefono">
                                    <p class="text-sm text-gray-500">Teléfono:</p>
                                    <p class="font-medium text-gray-800" x-text="pacienteData?.telefono"></p>
                                </div>
                                <div x-show="pacienteData?.correo">
                                    <p class="text-sm text-gray-500">Correo:</p>
                                    <p class="font-medium text-gray-800" x-text="pacienteData?.correo"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="!pacienteData?.usuario_id">
                            <p class="text-gray-600 mb-6">¿Desea asociar este perfil a su cuenta de usuario?</p>
                            
                            <form action="{{ route('pacientes.asociar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="paciente_id" :value="pacienteData?.id">
                                
                                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300">
                                    Asociar a mi cuenta
                                </button>
                            </form>
                        </div>
                        
                        <div x-show="pacienteData?.usuario_id" class="mt-8">
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <p class="text-green-700">Este perfil ya está asociado a una cuenta de usuario.</p>
                                </div>
                            </div>
                            
                            <a href="{{ route('citas.index') }}" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300 inline-block">
                                Continuar a Citas Médicas
                            </a>
                        </div>
                        
                        <div class="mt-6">
                            <button type="button" @click="step = 1; dni = ''; pacienteData = null;" class="text-cyan-600 hover:text-cyan-800 font-medium">
                                <i class="fas fa-redo mr-2"></i> Verificar otro DNI
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Notificaciones -->
    <div x-data="{
        showNotification: false,
        message: '',
        type: 'success',
        init() {
            // Verificar si hay mensajes de éxito o error en las meta tags
            const successMsg = document.querySelector('meta[name="success-message"]');
            const errorMsg = document.querySelector('meta[name="error-message"]');
            
            if (successMsg) {
                this.message = successMsg.getAttribute('content');
                this.type = 'success';
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 5000);
            } else if (errorMsg) {
                this.message = errorMsg.getAttribute('content');
                this.type = 'error';
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 5000);
            }
        }
    }" x-cloak>
        <div 
            x-show="showNotification" 
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-0 right-0 m-6 w-full max-w-sm overflow-hidden rounded-lg shadow-lg"
        >
            <div :class="{
                'bg-green-50 border-l-4 border-green-500': type === 'success',
                'bg-red-50 border-l-4 border-red-500': type === 'error'
            }" class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i :class="{
                            'fas fa-check-circle text-green-500': type === 'success',
                            'fas fa-exclamation-circle text-red-500': type === 'error'
                        }"></i>
                    </div>
                    <div class="ml-3">
                        <p :class="{
                            'text-green-700': type === 'success',
                            'text-red-700': type === 'error'
                        }" class="text-sm font-medium" x-text="message"></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="showNotification = false" class="inline-flex rounded-md p-1.5" :class="{
                                'bg-green-50 text-green-500 hover:bg-green-100': type === 'success',
                                'bg-red-50 text-red-500 hover:bg-red-100': type === 'error'
                            }">
                                <span class="sr-only">Cerrar</span>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
