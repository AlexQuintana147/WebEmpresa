<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Pacientes - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .hidden { display: none !important; }
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
        .transition-message {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .message-hidden {
            opacity: 0;
            transform: translateY(-10px);
        }
        .message-visible {
            opacity: 1;
            transform: translateY(0);
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="gestionPacientes">
                <!-- Título de la página -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Gestión de Pacientes</h1>
                </div>
                
                <!-- Mensaje de estado -->
                <div 
                    id="messageContainer"
                    class="border-l-4 p-4 mb-6 hidden transition-message message-hidden"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i id="messageIcon" class=""></i>
                        </div>
                        <div class="ml-3">
                            <p id="messageText"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario de verificación de DNI (mostrar solo si el doctor no tiene DNI) -->
                <div id="dniVerificationForm" class="medical-card bg-white overflow-hidden mb-6 {{ $doctor ? 'hidden' : '' }}">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Verificación de Identidad</h2>
                        <p class="text-gray-600 mb-4">Para acceder a la lista de pacientes, primero debe verificar su identidad ingresando su DNI.</p>
                        
                        <form id="verificarDniForm" class="space-y-4">
                            <div>
                                <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-id-card text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="dni" 
                                        name="dni"
                                        class="focus:ring-cyan-500 focus:border-cyan-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="Ingrese su DNI" 
                                        maxlength="8"
                                        pattern="[0-9]{8}"
                                        required
                                    >
                                </div>
                                <p id="dniError" class="mt-1 text-sm text-red-600 hidden"></p>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <p id="loadingDni" class="text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Verificando DNI...</p>
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                                    id="verificarDniBtn"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    <span>Verificar DNI</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Formulario para completar datos del doctor (mostrar después de verificar DNI) -->
                <div id="doctorForm" class="medical-card bg-white overflow-hidden mb-6 hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Complete sus Datos</h2>
                        <p class="text-gray-600 mb-4">Hemos verificado su DNI. Por favor, confirme sus datos para continuar.</p>
                        
                        <form id="guardarDatosMedicoForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input 
                                        type="text" 
                                        id="nombre" 
                                        name="nombre"
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
                                        name="apellido_paterno"
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
                                        name="apellido_materno"
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
                                        name="telefono"
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
                                        name="correo"
                                        class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                        required
                                        placeholder="ejemplo@correo.com"
                                    >
                                </div>
                                
                                <div>
                                    <label for="especialidad" class="block text-sm font-medium text-gray-700">Especialidad</label>
                                    <select
                                        id="especialidad" 
                                        name="especialidad"
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
                                <p id="loadingGuardar" class="text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Guardando datos...</p>
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                                    id="guardarDatosBtn"
                                >
                                    <i class="fas fa-save"></i>
                                    <span>Guardar Datos</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de Pacientes (mostrar solo si el doctor tiene DNI) -->
                <div id="pacientesContainer" class="medical-card bg-white overflow-hidden {{ $doctor ? '' : 'hidden' }}">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Listado de Pacientes</h2>
                            <button 
                                class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                                id="nuevoPacienteBtn"
                            >
                                <i class="fas fa-plus"></i>
                                <span>Nuevo Paciente</span>
                            </button>
                        </div>
                        
                        <!-- Información de carga -->
                        <div id="loadingPacientes" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <p class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i> <span id="loadingPacientesText">Cargando pacientes...</span>
                            </p>
                        </div>
                        
                        <div id="noPacientes" class="mb-4 p-3 bg-yellow-50 text-yellow-700 rounded hidden">
                            <p>No se encontraron pacientes asociados a su cuenta. Puede agregar nuevos pacientes usando el botón "Nuevo Paciente".</p>
                        </div>
                        
                        <!-- Tabla de pacientes -->
                        <div id="tablaPacientes" class="overflow-x-auto hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="pacientesTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Aquí se insertarán las filas de pacientes dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Botón para agendar citas -->
                        <div class="mt-6 flex justify-center">
                            <a href="{{ route('citas.index') }}" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-calendar-check text-xl"></i>
                                <span class="text-lg font-medium">Agendar Cita Médica</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables globales
        let doctor = {{ $doctor ? json_encode($doctor) : 'null' }};
        let pacientes = {{ isset($pacientes) ? json_encode($pacientes) : '[]' }};
        let initialized = false;
        let loading = false;
        let formData = {
            dni: '',
            nombre: '',
            apellido_paterno: '',
            apellido_materno: '',
            especialidad: '',
            telefono: '',
            correo: ''
        };
        
        // Elementos del DOM
        const messageContainer = document.getElementById('messageContainer');
        const messageIcon = document.getElementById('messageIcon');
        const messageText = document.getElementById('messageText');
        const dniVerificationForm = document.getElementById('dniVerificationForm');
        const doctorForm = document.getElementById('doctorForm');
        const pacientesContainer = document.getElementById('pacientesContainer');
        const loadingPacientes = document.getElementById('loadingPacientes');
        const loadingPacientesText = document.getElementById('loadingPacientesText');
        const noPacientes = document.getElementById('noPacientes');
        const tablaPacientes = document.getElementById('tablaPacientes');
        const pacientesTableBody = document.getElementById('pacientesTableBody');
        const loadingDni = document.getElementById('loadingDni');
        const loadingGuardar = document.getElementById('loadingGuardar');
        const dniError = document.getElementById('dniError');
        
        // Inicialización
        function init() {
            // Si ya tenemos los datos del doctor, no necesitamos hacer nada más
            if (doctor) {
                console.log('Doctor ya registrado:', doctor);
                // Asegurarse de que pacientes siempre sea un array
                if (!Array.isArray(pacientes)) {
                    pacientes = [];
                }
                renderPacientes();
            }
            
            // Marcar como inicializado después de que todo esté listo
            initialized = true;
            updateLoadingState();
        }
        
        // Mostrar mensaje
        function showMessage(type, text) {
            messageText.textContent = text;
            
            if (type === 'success') {
                messageContainer.classList.remove('bg-red-100', 'border-red-400', 'text-red-700');
                messageContainer.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
                messageIcon.classList.remove('fa-exclamation-circle');
                messageIcon.classList.add('fa-check-circle');
            } else {
                messageContainer.classList.remove('bg-green-100', 'border-green-400', 'text-green-700');
                messageContainer.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
                messageIcon.classList.remove('fa-check-circle');
                messageIcon.classList.add('fa-exclamation-circle');
            }
            
            messageContainer.classList.remove('hidden', 'message-hidden');
            messageContainer.classList.add('message-visible');
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                hideMessage();
            }, 5000);
        }
        
        // Ocultar mensaje
        function hideMessage() {
            messageContainer.classList.remove('message-visible');
            messageContainer.classList.add('message-hidden');
            setTimeout(() => {
                messageContainer.classList.add('hidden');
            }, 300);
        }
        
        // Actualizar estado de carga
        function updateLoadingState() {
            if (loading || !initialized) {
                loadingPacientes.classList.remove('hidden');
                loadingPacientesText.textContent = loading ? 'Cargando pacientes...' : 'Inicializando...';
                noPacientes.classList.add('hidden');
                tablaPacientes.classList.add('hidden');
            } else {
                loadingPacientes.classList.add('hidden');
                
                if (!pacientes || pacientes.length === 0) {
                    noPacientes.classList.remove('hidden');
                    tablaPacientes.classList.add('hidden');
                } else {
                    noPacientes.classList.add('hidden');
                    tablaPacientes.classList.remove('hidden');
                }
            }
        }
        
        // Renderizar pacientes en la tabla
        function renderPacientes() {
            // Verificar inmediatamente si hay pacientes
            if (!Array.isArray(pacientes) || pacientes.length === 0) {
                loadingPacientes.classList.add('hidden');
                noPacientes.classList.remove('hidden');
                tablaPacientes.classList.add('hidden');
                return;
            }

            // Si hay pacientes, proceder con la renderización
            const tbody = document.getElementById('pacientesTableBody');
            tbody.innerHTML = '';
            
            pacientes.forEach(paciente => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${paciente.dni}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${paciente.nombre} ${paciente.apellido_paterno} ${paciente.apellido_materno}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>Tel: ${paciente.telefono}</div>
                        <div>Email: ${paciente.correo}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <button class="text-blue-600 hover:text-blue-800 mr-2" onclick="editarPaciente(${paciente.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800" onclick="eliminarPaciente(${paciente.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Mostrar la tabla y ocultar los otros elementos
            loadingPacientes.classList.add('hidden');
            noPacientes.classList.add('hidden');
            tablaPacientes.classList.remove('hidden');
        }
        
        // Verificar DNI
        function verificarDni(e) {
            e.preventDefault();
            
            loading = true;
            loadingDni.classList.remove('hidden');
            dniError.classList.add('hidden');
            
            const dniInput = document.getElementById('dni');
            formData.dni = dniInput.value;
            
            // Validar formato de DNI
            if (!/^\d{8}$/.test(formData.dni)) {
                dniError.textContent = 'El DNI debe tener 8 dígitos numéricos';
                dniError.classList.remove('hidden');
                loading = false;
                loadingDni.classList.add('hidden');
                return;
            }
            
            // Consultar API de RENIEC
            fetch('/doctores/verificar-dni', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dni: formData.dni })
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
                loading = false;
                loadingDni.classList.add('hidden');
                
                if (!data.success) {
                    throw new Error(data.message || 'No se pudo verificar el DNI');
                }
                
                // Llenar el formulario con los datos obtenidos
                formData.nombre = data.data.nombres;
                formData.apellido_paterno = data.data.apellidoPaterno;
                formData.apellido_materno = data.data.apellidoMaterno;
                
                document.getElementById('nombre').value = formData.nombre;
                document.getElementById('apellido_paterno').value = formData.apellido_paterno;
                document.getElementById('apellido_materno').value = formData.apellido_materno;
                
                // Mostrar formulario para completar datos
                dniVerificationForm.classList.add('hidden');
                doctorForm.classList.remove('hidden');
                
                showMessage('success', 'DNI verificado correctamente. Por favor complete sus datos.');
            })
            .catch(error => {
                console.error('Error:', error);
                loading = false;
                loadingDni.classList.add('hidden');
                showMessage('error', error.message || 'Ocurrió un error al verificar el DNI. Por favor intente nuevamente.');
            });
        }
        
        // Guardar datos del médico
        function guardarDatosMedico(e) {
            e.preventDefault();
            
            loading = true;
            loadingGuardar.classList.remove('hidden');
            
            // Actualizar formData con los valores del formulario
            formData.telefono = document.getElementById('telefono').value;
            formData.correo = document.getElementById('correo').value;
            formData.especialidad = document.getElementById('especialidad').value;
            
            // Enviar datos al servidor
            fetch('/doctores/guardar-dni', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!response.ok) {
                    const errorData = contentType && contentType.includes('application/json') 
                        ? await response.json()
                        : { message: 'Error en la respuesta del servidor' };
                    throw new Error(errorData.message || 'Error al guardar los datos');
                }
                
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Respuesta del servidor no válida');
                }
                
                return response.json();
            })
            .then(data => {
                loading = false;
                loadingGuardar.classList.add('hidden');
                
                if (data.success) {
                    doctor = data.doctor;
                    doctorForm.classList.add('hidden');
                    pacientesContainer.classList.remove('hidden');
                    
                    showMessage('success', 'Datos guardados correctamente. Ya puede gestionar sus pacientes.');
                    
                    // Inicializar la vista de pacientes sin recargar la página
                    pacientes = [];
                    renderPacientes();
                } else {
                    throw new Error(data.message || 'No se pudieron guardar los datos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loading = false;
                loadingGuardar.classList.add('hidden');
                showMessage('error', error.message || 'Ocurrió un error al guardar los datos. Por favor intente nuevamente.');
            });
        }
        
        // Funciones para ver y editar pacientes (placeholders)
        function verPaciente(id) {
            console.log('Ver paciente:', id);
            // Implementar lógica para ver paciente
        }
        
        function editarPaciente(id) {
            console.log('Editar paciente:', id);
            // Implementar lógica para editar paciente
        }
        
        // Event listeners
        document.getElementById('verificarDniForm').addEventListener('submit', verificarDni);
        document.getElementById('guardarDatosMedicoForm').addEventListener('submit', guardarDatosMedico);
        messageContainer.addEventListener('click', hideMessage);
        
        // Inicializar la aplicación
        init();
    });
    </script>
</body>
</html>