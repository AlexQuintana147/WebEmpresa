<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perfil Médico - Clínica Ricardo Palma</title>
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
    <style>
        @keyframes pulse-medical {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .animate-pulse-medical {
            animation: pulse-medical 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes float-medical {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        .animate-float-medical {
            animation: float-medical 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-cyan-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />

            <!-- Profile Settings Content -->
            <main class="p-6 relative">
                <!-- Elementos decorativos médicos flotantes -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-10 left-10 w-8 h-8 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 0s;"></div>
                    <div class="absolute top-40 right-20 w-6 h-12 border-2 border-teal-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 1s;"></div>
                    <div class="absolute bottom-20 left-1/4 w-10 h-10 border-2 border-blue-200 rotate-45 opacity-20 animate-float-medical" style="animation-delay: 2s;"></div>
                    <div class="absolute top-1/3 right-1/3 w-8 h-8 border-2 border-cyan-200 rounded-md opacity-20 animate-float-medical" style="animation-delay: 3s;"></div>
                </div>
                
                <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden">
                    <!-- Patrón médico de fondo -->
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-50/30 to-teal-50/30 pointer-events-none"></div>
                    <div class="absolute inset-0 opacity-5 pointer-events-none">
                        <div class="absolute top-10 left-10 w-6 h-6 border-2 border-cyan-700 rounded-full"></div>
                        <div class="absolute top-40 right-20 w-4 h-8 border-2 border-teal-700 rounded-full"></div>
                        <div class="absolute bottom-20 left-1/4 w-8 h-8 border-2 border-blue-700 rotate-45"></div>
                        <div class="absolute top-1/3 right-1/3 w-6 h-6 border-2 border-cyan-700 rounded-md"></div>
                    </div>
                    
                    <!-- Encabezado con estilo médico -->
                    <div class="relative flex items-center mb-8">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-teal-600 mr-4 shadow-lg">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-700 to-teal-700">Perfil Médico</h1>
                            <p class="text-cyan-600 text-sm">Gestione su información personal en la Clínica Ricardo Palma</p>
                        </div>
                        <!-- Indicador de estado del sistema -->
                        <div class="ml-auto flex items-center space-x-2 bg-gradient-to-r from-cyan-50 to-teal-50 px-4 py-2 rounded-full shadow-sm">
                            <div class="h-3 w-3 rounded-full bg-green-500 animate-pulse-medical"></div>
                            <span class="text-sm text-cyan-800 font-medium">Sistema Médico Activo</span>
                        </div>
                    </div>

                    <form id="profileForm" class="space-y-8 relative" enctype="multipart/form-data">
                        @csrf
                        <!-- Profile Image Section -->
                        <div class="border border-cyan-200 rounded-xl p-6 shadow-sm bg-gradient-to-br from-white to-cyan-50">
                            <div class="flex items-center justify-between">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-id-card-alt text-cyan-600 mr-2"></i>
                                        <label class="block text-sm font-medium text-cyan-800">Foto de Perfil Médico</label>
                                    </div>
                                    <div class="flex items-center space-x-6">
                                        <div class="relative h-24 w-24 group">
                                            <!-- Efecto de pulso médico alrededor de la imagen -->
                                            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-teal-600 rounded-full opacity-50 group-hover:opacity-100 blur transition-opacity duration-300 animate-pulse-medical"></div>
                                            <div class="relative">
                                                <img id="preview" src="{{ Auth::user()->imagen ?? asset('images/iconPerfil.png') }}" 
                                                     alt="Profile picture" 
                                                     class="h-24 w-24 rounded-full object-cover border-2 border-white">
                                                <!-- Símbolo médico superpuesto -->
                                                <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-md">
                                                    <div class="h-5 w-5 rounded-full bg-gradient-to-r from-cyan-500 to-teal-500 flex items-center justify-center">
                                                        <i class="fas fa-heartbeat text-white text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-2">
                                            <label class="relative cursor-pointer">
                                                <span class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-sm font-medium rounded-lg 
                                                           hover:from-cyan-700 hover:to-teal-800 
                                                           focus:ring-4 focus:ring-cyan-300/50 
                                                           shadow-md hover:shadow-xl 
                                                           transform hover:-translate-y-0.5 
                                                           transition-all duration-200 ease-in-out 
                                                           inline-flex items-center">
                                                    <i class="fas fa-camera-retro mr-2"></i>
                                                    Cambiar Imagen
                                                </span>
                                                <input type="file" id="profileImage" name="imagen" accept="image/*" class="hidden" onchange="previewImage(event)">
                                            </label>
                                            <p class="text-sm text-cyan-600">JPG, PNG o GIF (Max. 2MB)</p>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="updateImageBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-cyan-700 hover:to-teal-800 
                                               focus:ring-4 focus:ring-cyan-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <i class="fas fa-user-md mr-2"></i>
                                    Actualizar Imagen de Perfil
                                </button>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="border border-cyan-200 rounded-xl p-6 shadow-sm bg-gradient-to-br from-white to-cyan-50">
                            <div class="flex items-center justify-between">
                                <div class="w-2/3">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-user-nurse text-cyan-600 mr-2"></i>
                                        <label for="nombre" class="block text-sm font-medium text-cyan-800">Nombre</label>
                                    </div>
                                    <input type="text" id="nombre" name="nombre" value="{{ Auth::user()->nombre }}" 
                                           class="block w-full px-4 py-3 border border-cyan-200 rounded-xl shadow-sm 
                                                  focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                                  transition duration-200 text-cyan-900 bg-white"
                                           pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+"
                                           title="Por favor ingrese solo letras (se permiten acentos y ñ)"
                                           required>
                                </div>
                                <button type="button" id="updateNameBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-teal-700 hover:to-teal-800 
                                               focus:ring-4 focus:ring-teal-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <i class="fas fa-signature mr-2"></i>
                                    Actualizar Nombre
                                </button>
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="border border-cyan-200 rounded-xl p-6 shadow-sm bg-gradient-to-br from-white to-cyan-50">
                            <div class="flex items-start justify-between">
                                <div class="w-2/3 space-y-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-lock-alt text-cyan-600 mr-2"></i>
                                        <h3 class="text-lg font-medium text-cyan-800">Seguridad de la Cuenta</h3>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label for="current_password" class="block text-sm font-medium text-cyan-800">Contraseña Actual</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-key text-cyan-500"></i>
                                            </div>
                                            <input type="password" id="current_password" name="current_password" 
                                                   class="block w-full pl-10 pr-4 py-3 border border-cyan-200 rounded-xl shadow-sm 
                                                          focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                                          transition duration-200 text-cyan-900 bg-white">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="new_password" class="block text-sm font-medium text-cyan-800">Nueva Contraseña</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-cyan-500"></i>
                                            </div>
                                            <input type="password" id="new_password" name="new_password" 
                                                   class="block w-full pl-10 pr-4 py-3 border border-cyan-200 rounded-xl shadow-sm 
                                                          focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                                          transition duration-200 text-cyan-900 bg-white"
                                                   pattern="^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,}$"
                                                   title="La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un carácter especial">
                                        </div>
                                        <div class="mt-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                                            <p class="text-sm text-cyan-800 font-medium mb-1">
                                                <i class="fas fa-shield-alt mr-1 text-cyan-600"></i> Requisitos de seguridad:
                                            </p>
                                            <div class="grid grid-cols-2 gap-1 text-sm text-cyan-700">
                                                <div class="flex items-center">
                                                    <i class="fas fa-check-circle text-xs mr-1 text-teal-500"></i>
                                                    <span>Mínimo 6 caracteres</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-check-circle text-xs mr-1 text-teal-500"></i>
                                                    <span>Una letra mayúscula</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-check-circle text-xs mr-1 text-teal-500"></i>
                                                    <span>Un número</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-check-circle text-xs mr-1 text-teal-500"></i>
                                                    <span>Un carácter especial</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="new_password_confirmation" class="block text-sm font-medium text-cyan-800">Confirmar Nueva Contraseña</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-cyan-500"></i>
                                            </div>
                                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                                   class="block w-full pl-10 pr-4 py-3 border border-cyan-200 rounded-xl shadow-sm 
                                                          focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 
                                                          transition duration-200 text-cyan-900 bg-white">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="updatePasswordBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-blue-700 hover:to-blue-800 
                                               focus:ring-4 focus:ring-blue-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                        
                        <!-- Información médica adicional -->
                        <div class="border border-cyan-200 rounded-xl p-6 shadow-sm bg-gradient-to-br from-white to-cyan-50">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-heartbeat text-cyan-600 mr-2"></i>
                                <h3 class="text-lg font-medium text-cyan-800">Información del Sistema</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center p-3 bg-cyan-50 rounded-lg border border-cyan-200">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-teal-500 mr-3">
                                        <i class="fas fa-calendar-check text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-cyan-800">Último acceso</p>
                                        <p class="text-xs text-cyan-600">{{ now()->timezone('America/Lima')->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-cyan-50 rounded-lg border border-cyan-200">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 mr-3">
                                        <i class="fas fa-user-shield text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-cyan-800">Estado de la cuenta</p>
                                        <p class="text-xs text-cyan-600">Activa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Update Image Button
    document.getElementById('updateImageBtn').addEventListener('click', function() {
        const formData = new FormData();
        const fileInput = document.getElementById('profileImage');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            
            // Check file size before conversion
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                alert('La imagen es demasiado grande. El tamaño máximo permitido es 2MB.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Image = e.target.result;
                formData.append('imagen', base64Image);
                formData.append('_token', csrfToken);
                
                submitFormData(formData, 'Imagen de perfil actualizada correctamente');
            };
            reader.readAsDataURL(file);
        } else {
            alert('Por favor seleccione una imagen');
        }
    });
    
    // Update Name Button
    document.getElementById('updateNameBtn').addEventListener('click', function() {
        const formData = new FormData();
        const nombre = document.getElementById('nombre').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        if (nombre.trim() === '') {
            alert('El nombre no puede estar vacío');
            return;
        }
        
        formData.append('nombre', nombre);
        formData.append('_token', csrfToken);
        
        submitFormData(formData, 'Nombre actualizado correctamente');
    });
    
    // Update Password Button
    document.getElementById('updatePasswordBtn').addEventListener('click', function() {
        const formData = new FormData();
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        if (currentPassword === '') {
            alert('Por favor ingrese su contraseña actual');
            return;
        }
        
        if (newPassword === '') {
            alert('Por favor ingrese una nueva contraseña');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            alert('Las contraseñas no coinciden');
            return;
        }
        
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        formData.append('new_password_confirmation', confirmPassword);
        formData.append('_token', csrfToken);
        
        submitFormData(formData, 'Contraseña actualizada correctamente');
    });
    
    function submitFormData(formData, successMessage) {
        // Asegurarse de que el token CSRF esté incluido en el FormData
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);
        
        fetch('/actualizar-perfil', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(successMessage);
                location.reload();
            } else if (data.errors && data.errors.nombre) {
                alert(data.errors.nombre[0]);
            } else {
                alert(data.message || 'Error al actualizar el perfil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el perfil');
        });
    }
    </script>
</body>
</html>