<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Opciones de Usuario</title>
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

            <!-- Profile Settings Content -->
            <main class="p-6">
                <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-8">Configuración del Perfil</h1>

                    <form id="profileForm" class="space-y-8" enctype="multipart/form-data">
                        @csrf
                        <!-- Profile Image Section -->
                        <div class="border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="space-y-4">
                                    <label class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                                    <div class="flex items-center space-x-6">
                                        <div class="relative h-24 w-24 group">
                                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full opacity-50 group-hover:opacity-100 blur transition-opacity duration-300"></div>
                                            <div class="relative">
                                                <img id="preview" src="{{ Auth::user()->imagen ?? asset('images/iconPerfil.png') }}" 
                                                     alt="Profile picture" 
                                                     class="h-24 w-24 rounded-full object-cover border-2 border-white">
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-2">
                                            <label class="relative cursor-pointer">
                                                <span class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg 
                                                           hover:from-blue-700 hover:to-blue-800 
                                                           focus:ring-4 focus:ring-blue-300/50 
                                                           shadow-md hover:shadow-xl 
                                                           transform hover:-translate-y-0.5 
                                                           transition-all duration-200 ease-in-out 
                                                           inline-flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Cambiar Imagen
                                                </span>
                                                <input type="file" id="profileImage" name="imagen" accept="image/*" class="hidden" onchange="previewImage(event)">
                                            </label>
                                            <p class="text-sm text-gray-500">JPG, PNG o GIF (Max. 2MB)</p>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="updateImageBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-blue-700 hover:to-blue-800 
                                               focus:ring-4 focus:ring-blue-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Actualizar Imagen de Perfil
                                </button>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="w-2/3">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" value="{{ Auth::user()->nombre }}" 
                                           class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                                  focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                                  transition duration-200 text-gray-900"
                                           pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+"
                                           title="Por favor ingrese solo letras (se permiten acentos y ñ)"
                                           required>
                                </div>
                                <button type="button" id="updateNameBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-green-700 hover:to-green-800 
                                               focus:ring-4 focus:ring-green-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Actualizar Nombre
                                </button>
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="w-2/3 space-y-4">
                                    <div class="space-y-2">
                                        <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                                        <input type="password" id="current_password" name="current_password" 
                                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                                      transition duration-200 text-gray-900">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                                        <input type="password" id="new_password" name="new_password" 
                                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                                      transition duration-200 text-gray-900"
                                               pattern="^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,}$"
                                               title="La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un carácter especial">
                                        <p class="text-sm text-gray-500 mt-1">
                                            La contraseña debe contener:
                                            <span class="block mt-1 ml-2">• Mínimo 6 caracteres</span>
                                            <span class="block ml-2">• Al menos una letra mayúscula</span>
                                            <span class="block ml-2">• Al menos un número</span>
                                            <span class="block ml-2">• Al menos un carácter especial (!@#$%^&*)</span>
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                               class="block w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm 
                                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                                      transition duration-200 text-gray-900">
                                    </div>
                                </div>
                                <button type="button" id="updatePasswordBtn"
                                        class="py-3 px-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-sm font-semibold rounded-xl 
                                               hover:from-purple-700 hover:to-purple-800 
                                               focus:ring-4 focus:ring-purple-500/20 
                                               shadow-md hover:shadow-lg 
                                               transform hover:-translate-y-0.5 
                                               transition-all duration-200 ease-in-out
                                               flex items-center justify-center h-12">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Actualizar Contraseña
                                </button>
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
            const reader = new FileReader();
            reader.onload = function(e) {
                formData.append('imagen', e.target.result);
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
        fetch('/actualizar-perfil', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(successMessage);
                location.reload();
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