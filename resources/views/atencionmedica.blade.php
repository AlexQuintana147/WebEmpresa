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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
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
        
        /* Clases de utilidad para mostrar/ocultar elementos */
        .hidden {
            display: none !important;
        }
        .disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-blue-50">
    <div class="min-h-screen flex" id="app">
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
                    <div id="paso1" class="medical-card bg-white p-8 text-center">
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-6">Ingrese su DNI para comenzar</h2>
                        <p class="text-gray-600 mb-8">Para brindarle una atención personalizada, necesitamos verificar su identidad mediante su DNI.</p>
                        
                        <div class="max-w-md mx-auto">
                            <div class="mb-4">
                                <label for="dni_input" class="block text-sm font-medium text-gray-700 mb-2 text-left">Documento Nacional de Identidad (DNI)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-id-card text-cyan-500"></i>
                                    </div>
                                    <input 
                                        id="dni_input"
                                        type="text" 
                                        class="block w-full pl-10 pr-3 py-3 border-2 border-cyan-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" 
                                        placeholder="Ingrese su DNI (8 dígitos)" 
                                        maxlength="8"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-left">El DNI debe contener exactamente 8 dígitos numéricos</p>
                            </div>
                            <div id="error_dni" class="text-red-500 text-sm mt-2 bg-red-50 p-2 rounded-md hidden"></div>
                            
                            <button 
                                id="verificar_dni_btn"
                                class="mt-6 w-full bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300 flex items-center justify-center shadow-md"
                            >
                                <span id="btn_text">Verificar DNI</span>
                                <span id="loading_indicator" class="hidden flex items-center">
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
                    <div id="paso2" class="medical-card bg-white p-8 hidden">
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-6 text-center">Complete sus datos personales</h2>
                        <p class="text-gray-600 mb-8 text-center">No encontramos su DNI en nuestro sistema. Por favor, complete el siguiente formulario para registrarse.</p>
                        
                        <form action="{{ route('pacientes.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="dni" id="form_dni">
                            
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
                                <button type="button" id="volver_paso1" class="text-cyan-600 hover:text-cyan-800 font-medium flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Volver
                                </button>
                                
                                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300">
                                    Registrar y Continuar
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Paso 3: Asociar paciente existente -->
                    <div id="paso3" class="medical-card bg-white p-8 text-center hidden">
                        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 animate-pulse-medical">
                            <i class="fas fa-user-check text-green-600 text-3xl"></i>
                        </div>
                        
                        <h2 class="text-2xl font-semibold text-cyan-800 mb-4">¡Bienvenido de nuevo!</h2>
                        <p class="text-gray-600 mb-6">Hemos encontrado sus datos en nuestro sistema. A continuación puede ver su información registrada.</p>
                        
                        <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                            <h3 class="text-lg font-medium text-blue-800 mb-4"><i class="fas fa-user-md mr-2"></i>Información Completa del Paciente</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Nombre completo:</p>
                                    <p class="font-medium text-gray-800" id="paciente_nombre_completo"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">DNI:</p>
                                    <p class="font-medium text-gray-800" id="paciente_dni"></p>
                                </div>
                                <div id="paciente_telefono_container" class="hidden">
                                    <p class="text-sm text-gray-500">Teléfono:</p>
                                    <p class="font-medium text-gray-800" id="paciente_telefono"></p>
                                </div>
                                <div id="paciente_correo_container" class="hidden">
                                    <p class="text-sm text-gray-500">Correo:</p>
                                    <p class="font-medium text-gray-800" id="paciente_correo"></p>
                                </div>
                                <!-- Mostrar fecha de registro si existe -->
                                <div id="paciente_registro_container" class="hidden">
                                    <p class="text-sm text-gray-500">Fecha de registro:</p>
                                    <p class="font-medium text-gray-800" id="paciente_registro"></p>
                                </div>
                                <!-- Mostrar última actualización si existe -->
                                <div id="paciente_actualizacion_container" class="hidden">
                                    <p class="text-sm text-gray-500">Última actualización:</p>
                                    <p class="font-medium text-gray-800" id="paciente_actualizacion"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="asociar_container" class="hidden">
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <p class="text-yellow-700">Para continuar con el agendamiento de citas, primero debe asociar este perfil a su cuenta.</p>
                                </div>
                            </div>
                            
                            <form action="{{ route('pacientes.asociar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="paciente_id" id="paciente_id_asociar">
                                
                                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300">
                                    Asociar a mi cuenta
                                </button>
                            </form>
                        </div>
                        
                        <div id="usuario_asociado_container" class="mt-8 hidden">
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <p class="text-green-700">Este perfil ya está asociado a una cuenta de usuario.</p>
                                </div>
                            </div>
                            
                            <div class="bg-cyan-50 p-6 rounded-lg mb-6">
                                <h3 class="text-xl font-semibold text-cyan-800 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Continúe para agendar su cita médica</h3>
                                <p class="text-gray-600 mb-4">Ya puede proceder a agendar una cita médica con nuestros especialistas o revisar su historial de atenciones.</p>
                                
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('citas.index') }}" class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white py-4 px-8 rounded-lg font-medium hover:from-cyan-600 hover:to-teal-600 transition-all duration-300 inline-flex items-center justify-center shadow-lg transform hover:-translate-y-1">
                                        <i class="fas fa-calendar-plus mr-2 text-lg"></i> Agendar Cita Médica
                                    </a>
                                    
                                    <a href="{{ route('historial.index') }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-4 px-8 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 inline-flex items-center justify-center">
                                        <i class="fas fa-history mr-2"></i> Ver Historial
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="button" id="verificar_otro_dni" class="text-cyan-600 hover:text-cyan-800 font-medium">
                                <i class="fas fa-redo mr-2"></i> Verificar otro DNI
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Notificaciones -->
    <div id="notification_container" class="fixed bottom-0 right-0 m-6 w-full max-w-sm overflow-hidden rounded-lg shadow-lg hidden">
        <div id="notification" class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i id="notification_icon"></i>
                </div>
                <div class="ml-3">
                    <p id="notification_message" class="text-sm font-medium"></p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button id="close_notification" class="inline-flex rounded-md p-1.5">
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

<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables para almacenar el estado
            let currentStep = 1;
            let pacienteData = null;
            let pacienteExiste = false;
            let formData = {
                dni: '',
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                correo: '',
                telefono: ''
            };
            let loading = false;
            let errors = {};

            // Referencias a elementos del DOM
            const paso1 = document.getElementById('paso1');
            const paso2 = document.getElementById('paso2');
            const paso3 = document.getElementById('paso3');
            const dniInput = document.getElementById('dni_input');
            const errorDni = document.getElementById('error_dni');
            const verificarDniBtn = document.getElementById('verificar_dni_btn');
            const btnText = document.getElementById('btn_text');
            const loadingIndicator = document.getElementById('loading_indicator');
            const volverPaso1 = document.getElementById('volver_paso1');
            const verificarOtroDni = document.getElementById('verificar_otro_dni');
            const formDni = document.getElementById('form_dni');
            const nombreInput = document.getElementById('nombre');
            const apellidoPaternoInput = document.getElementById('apellido_paterno');
            const apellidoMaternoInput = document.getElementById('apellido_materno');

            // Inicialización
            @if(Auth::check() && isset($paciente))
                // Si el usuario está autenticado y tiene un paciente asociado
                pacienteExiste = true;
                pacienteData = @json($paciente);
                currentStep = 3;
                mostrarPaso(3);
                actualizarDatosPaciente();
            @else
                // Si no está autenticado o no tiene paciente asociado
                currentStep = 1;
                mostrarPaso(1);
            @endif

            // Verificar si hay mensajes de notificación
            const successMsg = document.querySelector('meta[name="success-message"]');
            const errorMsg = document.querySelector('meta[name="error-message"]');
            
            if (successMsg) {
                mostrarNotificacion(successMsg.getAttribute('content'), 'success');
            } else if (errorMsg) {
                mostrarNotificacion(errorMsg.getAttribute('content'), 'error');
            }
            
            // Función para mostrar notificaciones
            function mostrarNotificacion(mensaje, tipo) {
                // Crear el contenedor de notificación si no existe
                let notificationContainer = document.getElementById('notification_container');
                
                if (!notificationContainer) {
                    notificationContainer = document.createElement('div');
                    notificationContainer.id = 'notification_container';
                    notificationContainer.className = 'fixed top-4 right-4 z-50 max-w-md';
                    document.body.appendChild(notificationContainer);
                }
                
                // Crear la notificación
                const notification = document.createElement('div');
                notification.className = `rounded-lg shadow-lg p-4 mb-4 flex items-center justify-between ${
                    tipo === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 
                    'bg-red-100 border-l-4 border-red-500 text-red-700'
                }`;
                
                // Icono según el tipo
                const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                // Contenido de la notificación
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${icon} mr-3 text-xl"></i>
                        <p>${mensaje}</p>
                    </div>
                    <button class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Agregar la notificación al contenedor
                notificationContainer.appendChild(notification);
                
                // Eliminar la notificación después de 5 segundos
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }

            // Event Listeners
            verificarDniBtn.addEventListener('click', verificarDni);
            dniInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    verificarDni();
                }
                // Validar que solo se ingresen números
                if (!/^\d*$/.test(this.value)) {
                    this.value = this.value.replace(/\D/g, '');
                }
                // Habilitar/deshabilitar botón según longitud del DNI
                if (this.value.length === 8 && /^\d+$/.test(this.value)) {
                    verificarDniBtn.classList.remove('disabled');
                } else {
                    verificarDniBtn.classList.add('disabled');
                }
            });
            volverPaso1.addEventListener('click', function() {
                mostrarPaso(1);
            });
            verificarOtroDni.addEventListener('click', function() {
                dniInput.value = '';
                pacienteData = null;
                mostrarPaso(1);
            });
            document.getElementById('close_notification').addEventListener('click', function() {
                document.getElementById('notification_container').classList.add('hidden');
            });

            // Funciones
            function mostrarPaso(paso) {
                currentStep = paso;
                paso1.classList.add('hidden');
                paso2.classList.add('hidden');
                paso3.classList.add('hidden');

                if (paso === 1) {
                    paso1.classList.remove('hidden');
                } else if (paso === 2) {
                    paso2.classList.remove('hidden');
                } else if (paso === 3) {
                    paso3.classList.remove('hidden');
                }
            }

            function verificarDni() {
                const dni = dniInput.value;
                
                if (dni.length !== 8) {
                    mostrarError('El DNI debe tener 8 dígitos');
                    return;
                }
                
                if (!/^\d+$/.test(dni)) {
                    mostrarError('El DNI debe contener solo números');
                    return;
                }
                
                setLoading(true);
                errorDni.classList.add('hidden');
                
                // Consultamos la API de RENIEC a través de nuestro backend
                fetch('/reniec/consultar-dni', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ dni: dni })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const reniecData = result.data;
                        // Guardamos los datos de RENIEC para usarlos si el paciente no existe
                        if (reniecData.nombres && reniecData.apellidoPaterno && reniecData.apellidoMaterno) {
                            formData.nombre = reniecData.nombres;
                            formData.apellido_paterno = reniecData.apellidoPaterno;
                            formData.apellido_materno = reniecData.apellidoMaterno;
                        }
                    }
                    
                    // Ahora verificamos si el paciente existe en nuestra base de datos
                    return fetch('/pacientes/verificar-dni', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ dni: dni })
                    });
                })
                .then(response => response.json())
                .then(data => {
                    setLoading(false);
                    if (data.success) {
                        if (data.exists) {
                            pacienteExiste = true;
                            pacienteData = data.paciente;
                            actualizarDatosPaciente();
                            mostrarPaso(3);
                        } else {
                            // Si el paciente no existe, mostramos el formulario de registro
                            // y autocompletamos con datos de RENIEC si están disponibles
                            document.getElementById('form_dni').value = dni;
                            
                            if (formData.nombre) {
                                document.getElementById('nombre').value = formData.nombre;
                                document.getElementById('apellido_paterno').value = formData.apellido_paterno;
                                document.getElementById('apellido_materno').value = formData.apellido_materno;
                            }
                            
                            mostrarPaso(2);
                        }
                    } else {
                        mostrarError(data.message || 'Error al verificar el DNI');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setLoading(false);
                    mostrarError('Ocurrió un error al procesar la solicitud');
                });
            }
            
            // Función para actualizar los datos del paciente en el paso 3
            function actualizarDatosPaciente() {
                if (!pacienteData) return;
                
                // Actualizamos los datos básicos
                document.getElementById('paciente_nombre_completo').textContent = `${pacienteData.nombre} ${pacienteData.apellido_paterno} ${pacienteData.apellido_materno}`;
                document.getElementById('paciente_dni').textContent = pacienteData.dni;
                
                // Actualizamos teléfono si existe
                if (pacienteData.telefono) {
                    document.getElementById('paciente_telefono').textContent = pacienteData.telefono;
                    document.getElementById('paciente_telefono_container').classList.remove('hidden');
                } else {
                    document.getElementById('paciente_telefono_container').classList.add('hidden');
                }
                
                // Actualizamos correo si existe
                if (pacienteData.correo) {
                    document.getElementById('paciente_correo').textContent = pacienteData.correo;
                    document.getElementById('paciente_correo_container').classList.remove('hidden');
                } else {
                    document.getElementById('paciente_correo_container').classList.add('hidden');
                }
                
                // Actualizamos fecha de registro si existe
                if (pacienteData.created_at) {
                    document.getElementById('paciente_registro').textContent = formatearFecha(pacienteData.created_at);
                    document.getElementById('paciente_registro_container').classList.remove('hidden');
                } else {
                    document.getElementById('paciente_registro_container').classList.add('hidden');
                }
                
                // Actualizamos fecha de actualización si existe
                if (pacienteData.updated_at) {
                    document.getElementById('paciente_actualizacion').textContent = formatearFecha(pacienteData.updated_at);
                    document.getElementById('paciente_actualizacion_container').classList.remove('hidden');
                } else {
                    document.getElementById('paciente_actualizacion_container').classList.add('hidden');
                }
                
                // Verificamos si el paciente está asociado a un usuario
                if (pacienteData.user_id) {
                    document.getElementById('usuario_asociado_container').classList.remove('hidden');
                    document.getElementById('asociar_container').classList.add('hidden');
                } else {
                    document.getElementById('usuario_asociado_container').classList.add('hidden');
                    document.getElementById('asociar_container').classList.remove('hidden');
                    document.getElementById('paciente_id_asociar').value = pacienteData.id;
                }
            }
            
            // Función para formatear fechas
            function formatearFecha(fechaStr) {
                const fecha = new Date(fechaStr);
                return fecha.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            
            // Función para mostrar errores
            function mostrarError(mensaje) {
                const errorDni = document.getElementById('error_dni');
                errorDni.textContent = mensaje;
                errorDni.classList.remove('hidden');
            }
            
            // Función para mostrar/ocultar el indicador de carga
            function setLoading(isLoading) {
                const btnText = document.getElementById('btn_text');
                const loadingIndicator = document.getElementById('loading_indicator');
                const verificarBtn = document.getElementById('verificar_dni_btn');
                
                if (isLoading) {
                    btnText.classList.add('hidden');
                    loadingIndicator.classList.remove('hidden');
                    verificarBtn.classList.add('disabled');
                    verificarBtn.disabled = true;
                } else {
                    btnText.classList.remove('hidden');
                    loadingIndicator.classList.add('hidden');
                    verificarBtn.classList.remove('disabled');
                    verificarBtn.disabled = false;
                }
            }
            
            // Función para mostrar el paso 1 (verificación DNI)
            function mostrarPaso1() {
                document.getElementById('paso1').classList.remove('hidden');
                document.getElementById('paso2').classList.add('hidden');
                document.getElementById('paso3').classList.add('hidden');
            }
            
            // Función para mostrar el paso 2 (registro de paciente)
            function mostrarPaso2() {
                document.getElementById('paso1').classList.add('hidden');
                document.getElementById('paso2').classList.remove('hidden');
                document.getElementById('paso3').classList.add('hidden');
            }
            
            // Función para mostrar el paso 3 (paciente existente)
            function mostrarPaso3() {
                document.getElementById('paso1').classList.add('hidden');
                document.getElementById('paso2').classList.add('hidden');
                document.getElementById('paso3').classList.remove('hidden');
            }
            
            // Eliminamos la duplicación de event listeners, ya que estos ya están definidos arriba
            // y la duplicación podría causar comportamientos inesperados
        }); 
    </script>