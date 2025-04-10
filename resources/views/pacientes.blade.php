<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Pacientes - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
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
    </style>
</head>
<body class="bg-blue-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Contenido Principal -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="gestionPacientes">
                <!-- Título de la página -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Pacientes</h1>
                </div>
                
                <!-- Mensaje de estado -->
                <div 
                    x-show="message" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    :class="{
                        'bg-green-100 border-green-400 text-green-700': message?.type === 'success',
                        'bg-red-100 border-red-400 text-red-700': message?.type === 'error'
                    }"
                    class="border-l-4 p-4 mb-6"
                    @click="message = null"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i :class="{
                                'fas fa-check-circle': message?.type === 'success',
                                'fas fa-exclamation-circle': message?.type === 'error'
                            }"></i>
                        </div>
                        <div class="ml-3">
                            <p x-text="message?.text"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario de verificación de DNI (mostrar solo si el doctor no tiene DNI) -->
                <div x-show="!doctor" class="medical-card bg-white overflow-hidden mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Verificación de Identidad</h2>
                        <p class="text-gray-600 mb-4">Para acceder a la lista de pacientes, primero debe verificar su identidad ingresando su DNI.</p>
                        
                        <form @submit.prevent="verificarDni" class="space-y-4">
                            <div>
                                <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-id-card text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="dni" 
                                        x-model="formData.dni" 
                                        class="focus:ring-cyan-500 focus:border-cyan-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="Ingrese su DNI" 
                                        maxlength="8"
                                        pattern="[0-9]{8}"
                                        required
                                    >
                                </div>
                                <p x-show="errors.dni" x-text="errors.dni" class="mt-1 text-sm text-red-600"></p>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <p x-show="loading" class="text-sm text-cyan-600"><i class="fas fa-spinner fa-spin mr-2"></i> Verificando DNI...</p>
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                                    :disabled="loading"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    <span>Verificar DNI</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Formulario para completar datos del doctor (mostrar después de verificar DNI) -->
                <div x-show="showDoctorForm" class="medical-card bg-white overflow-hidden mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Complete sus Datos</h2>
                        <p class="text-gray-600 mb-4">Hemos verificado su DNI. Por favor, confirme sus datos para continuar.</p>
                        
                        <form @submit.prevent="guardarDatosMedico" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input 
                                        type="text" 
                                        id="nombre" 
                                        x-model="formData.nombre" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50" 
                                        required
                                        readonly
                                    >
                                </div>
                                
                                <div>
                                    <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                                    <input 
                                        type="text" 
                                        id="apellido_paterno" 
                                        x-model="formData.apellido_paterno" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50" 
                                        required
                                        readonly
                                    >
                                </div>
                                
                                <div>
                                    <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                                    <input 
                                        type="text" 
                                        id="apellido_materno" 
                                        x-model="formData.apellido_materno" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50" 
                                        required
                                        readonly
                                    >
                                </div>

                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input 
                                        type="tel" 
                                        id="telefono" 
                                        x-model="formData.telefono" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                        required
                                        pattern="[0-9]{9}"
                                        placeholder="Ingrese su número de teléfono"
                                    >
                                </div>

                                <div>
                                    <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                    <input 
                                        type="email" 
                                        id="correo" 
                                        x-model="formData.correo" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                        required
                                        placeholder="ejemplo@correo.com"
                                    >
                                </div>
                                
                                <div>
                                    <label for="especialidad" class="block text-sm font-medium text-gray-700">Especialidad</label>
                                    <select
                                        id="especialidad" 
                                        x-model="formData.especialidad" 
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        required
                                    >
                                        <option value="">Seleccione una especialidad</option>
                                        <option value="Cardiología">Cardiología</option>
                                        <option value="Dermatología">Dermatología</option>
                                        <option value="Endocrinología">Endocrinología</option>
                                        <option value="Gastroenterología">Gastroenterología</option>
                                        <option value="Ginecología">Ginecología</option>
                                        <option value="Medicina General">Medicina General</option>
                                        <option value="Medicina Interna">Medicina Interna</option>
                                        <option value="Neurología">Neurología</option>
                                        <option value="Oftalmología">Oftalmología</option>
                                        <option value="Oncología">Oncología</option>
                                        <option value="Pediatría">Pediatría</option>
                                        <option value="Psiquiatría">Psiquiatría</option>
                                        <option value="Traumatología">Traumatología</option>
                                        <option value="Urología">Urología</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <p x-show="loading" class="text-sm text-cyan-600"><i class="fas fa-spinner fa-spin mr-2"></i> Guardando datos...</p>
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                                    :disabled="loading"
                                >
                                    <i class="fas fa-save"></i>
                                    <span>Guardar Datos</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de Pacientes (mostrar solo si el doctor tiene DNI) -->
                <div x-show="doctor" class="medical-card bg-white overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Listado de Pacientes</h2>
                            <button 
                                class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                            >
                                <i class="fas fa-plus"></i>
                                <span>Nuevo Paciente</span>
                            </button>
                        </div>
                        
                        <!-- Información de carga -->
                        <div x-show="loading" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <p>Cargando pacientes...</p>
                        </div>
                        
                        <div x-show="!loading && pacientes.length === 0" class="mb-4 p-3 bg-yellow-50 text-yellow-700 rounded">
                            <p>No se encontraron pacientes asociados a su cuenta. Puede agregar nuevos pacientes usando el botón "Nuevo Paciente".</p>
                        </div>
                        
                        <!-- Tabla de pacientes -->
                        <div x-show="!loading && pacientes.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="paciente in pacientes" :key="paciente.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" x-text="paciente.dni"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900" x-text="`${paciente.nombre} ${paciente.apellido_paterno} ${paciente.apellido_materno}`"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500" x-text="paciente.telefono || 'No registrado'"></div>
                                                <div class="text-sm text-gray-500" x-text="paciente.correo || 'No registrado'"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="text-cyan-600 hover:text-cyan-900 mr-3">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                                <button class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gestionPacientes', () => ({
            doctor: {{ $doctor ? json_encode($doctor) : 'null' }},
            pacientes: {{ $pacientes ? json_encode($pacientes) : '[]' }},
            loading: false,
            showDoctorForm: false,
            message: null,
            errors: {},
            formData: {
                dni: '',
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                especialidad: '',
                telefono: '',
                correo: ''
            },
            
            init() {
                // Si ya tenemos los datos del doctor, no necesitamos hacer nada más
                if (this.doctor) {
                    console.log('Doctor ya registrado:', this.doctor);
                }
            },
            
            verificarDni() {
                this.loading = true;
                this.errors = {};
                
                // Validar formato de DNI
                if (!/^\d{8}$/.test(this.formData.dni)) {
                    this.errors.dni = 'El DNI debe tener 8 dígitos numéricos';
                    this.loading = false;
                    return;
                }
                
                // Consultar API de RENIEC
                fetch('/doctores/verificar-dni', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ dni: this.formData.dni })
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        const errorData = contentType && contentType.includes('application/json') 
                            ? await response.json()
                            : { message: 'Error en la respuesta del servidor' };
                        throw new Error(errorData.message);
                    }
                    
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    throw new Error('Respuesta del servidor no válida');
                })
                .then(data => {
                    this.loading = false;
                    
                    if (!data.success) {
                        throw new Error(data.message || 'No se pudo verificar el DNI');
                    }
                    
                    // Llenar el formulario con los datos obtenidos
                    this.formData.nombre = data.data.nombres;
                    this.formData.apellido_paterno = data.data.apellidoPaterno;
                    this.formData.apellido_materno = data.data.apellidoMaterno;
                    
                    // Mostrar formulario para completar datos
                    this.showDoctorForm = true;
                    
                    this.message = {
                        type: 'success',
                        text: 'DNI verificado correctamente. Por favor complete sus datos.'
                    };
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                    this.message = {
                        type: 'error',
                        text: error.message || 'Ocurrió un error al verificar el DNI. Por favor intente nuevamente.'
                    };
                });
            },
            
            guardarDatosMedico() {
                this.loading = true;
                this.errors = {};
                
                // Enviar datos al servidor
                fetch('/doctores/guardar-dni', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error al guardar los datos');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.loading = false;
                    
                    if (data.success) {
                        this.doctor = data.doctor;
                        this.showDoctorForm = false;
                        
                        this.message = {
                            type: 'success',
                            text: 'Datos guardados correctamente. Ahora puede acceder a la lista de pacientes.'
                        };
                        
                        // Recargar la página después de 2 segundos para mostrar la lista de pacientes
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        this.message = {
                            type: 'error',
                            text: data.message || 'No se pudieron guardar los datos'
                        };
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                    this.message = {
                        type: 'error',
                        text: error.message || 'Ocurrió un error al guardar los datos. Por favor intente nuevamente.'
                    };
                });
            }
        }));
    });
    </script>
</body>
</html>