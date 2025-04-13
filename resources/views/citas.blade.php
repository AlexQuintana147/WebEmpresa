<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agendar Cita - Clínica Ricardo Palma</title>
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="agendarCita">
                <!-- Título de la página -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Agendar Cita Médica</h1>
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
                
                <!-- Verificación de DNI (mostrar solo si el paciente no está autenticado) -->
                <div id="dniVerificationForm" class="medical-card bg-white overflow-hidden mb-6 {{ $paciente ? 'hidden' : '' }}">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Verificación de Identidad</h2>
                        <p class="text-gray-600 mb-4">Para agendar una cita, primero debe verificar su identidad ingresando su DNI.</p>
                        
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
                
                <!-- Formulario para agendar cita (mostrar después de verificar DNI) -->
                <div id="citaForm" class="medical-card bg-white overflow-hidden mb-6 {{ $paciente ? '' : 'hidden' }}">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Agendar Nueva Cita</h2>
                        <p class="text-gray-600 mb-4">Complete el formulario para agendar una cita con un especialista.</p>
                        
                        <form id="agendarCitaForm" class="space-y-6">
                            <!-- Datos del paciente -->
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <h3 class="text-md font-medium text-blue-800 mb-2">Datos del Paciente</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                        <p class="mt-1 text-gray-900" id="nombrePaciente">{{ $paciente ? $paciente->nombre_completo : '' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">DNI</label>
                                        <p class="mt-1 text-gray-900" id="dniPaciente">{{ $paciente ? $paciente->dni : '' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selección de especialidad -->
                            <div>
                                <label for="especialidad" class="block text-sm font-medium text-gray-700">Especialidad Médica</label>
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
                            
                            <!-- Selección de doctor -->
                            <div id="doctorContainer" class="hidden">
                                <label for="doctor" class="block text-sm font-medium text-gray-700">Doctor</label>
                                <select
                                    id="doctor" 
                                    name="doctor_id"
                                    class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required
                                    disabled
                                >
                                    <option value="">Seleccione un doctor</option>
                                </select>
                                <p id="loadingDoctores" class="mt-1 text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Buscando doctores disponibles...</p>
                                <p id="noDoctores" class="mt-1 text-sm text-red-600 hidden">No hay doctores disponibles para esta especialidad.</p>
                            </div>
                            
                            <!-- Selección de fecha -->
                            <div id="fechaContainer" class="hidden">
                                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha de la Cita</label>
                                <input 
                                    type="date" 
                                    id="fecha" 
                                    name="fecha"
                                    class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required
                                    disabled
                                    min="{{ date('Y-m-d') }}"
                                >
                                <p id="loadingFechas" class="mt-1 text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Verificando disponibilidad...</p>
                            </div>
                            
                            <!-- Selección de horario -->
                            <div id="horarioContainer" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Horarios Disponibles</label>
                                <div id="horariosDisponibles" class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <!-- Aquí se cargarán dinámicamente los horarios disponibles -->
                                </div>
                                <p id="loadingHorarios" class="mt-1 text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando horarios disponibles...</p>
                                <p id="noHorarios" class="mt-1 text-sm text-red-600 hidden">No hay horarios disponibles para la fecha seleccionada.</p>
                            </div>
                            
                            <!-- Motivo de consulta -->
                            <div id="motivoContainer" class="hidden">
                                <label for="motivo_consulta" class="block text-sm font-medium text-gray-700">Motivo de Consulta</label>
                                <select
                                    id="motivo_consulta" 
                                    name="motivo_consulta"
                                    class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required
                                >
                                    <option value="">Seleccione un motivo</option>
                                    <option value="Consulta general">Consulta general</option>
                                    <option value="Control rutinario">Control rutinario</option>
                                    <option value="Emergencia">Emergencia</option>
                                    <option value="Seguimiento">Seguimiento de tratamiento</option>
                                    <option value="Exámenes">Revisión de exámenes</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            
                            <!-- Descripción del malestar -->
                            <div id="descripcionContainer" class="hidden">
                                <label for="descripcion_malestar" class="block text-sm font-medium text-gray-700">Descripción del Malestar</label>
                                <textarea 
                                    id="descripcion_malestar" 
                                    name="descripcion_malestar"
                                    rows="4"
                                    class="mt-1 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Describa brevemente sus síntomas o el motivo de su consulta"
                                ></textarea>
                            </div>
                            
                            <!-- Botón de envío -->
                            <div class="flex items-center justify-between pt-2">
                                <p id="loadingAgendar" class="text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Agendando cita...</p>
                                <button 
                                    type="submit" 
                                    id="agendarCitaBtn"
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed hidden"
                                    disabled
                                >
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Agendar Cita</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Citas agendadas -->
                <div id="citasAgendadas" class="medical-card bg-white overflow-hidden {{ $paciente ? '' : 'hidden' }}">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Mis Citas</h2>
                        
                        <!-- Información de carga -->
                        <div id="loadingCitas" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <p class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i> <span>Cargando citas...</span>
                            </p>
                        </div>
                        
                        <div id="noCitas" class="mb-4 p-3 bg-yellow-50 text-yellow-700 rounded hidden">
                            <p>No tiene citas agendadas. Puede agendar una nueva cita usando el formulario anterior.</p>
                        </div>
                        
                        <!-- Tabla de citas -->
                        <div id="tablaCitas" class="overflow-x-auto hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="citasTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Aquí se insertarán las filas de citas dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables globales
        let paciente = {{ $paciente ? json_encode($paciente) : 'null' }};
        let citas = {{ isset($citas) ? json_encode($citas) : '[]' }};
        let doctoresDisponibles = [];
        let horariosSeleccionados = [];
        let initialized = false;
        let loading = false;
        
        // Elementos del DOM
        const messageContainer = document.getElementById('messageContainer');
        const messageIcon = document.getElementById('messageIcon');
        const messageText = document.getElementById('messageText');
        const dniVerificationForm = document.getElementById('dniVerificationForm');
        const citaForm = document.getElementById('citaForm');
        const citasAgendadas = document.getElementById('citasAgendadas');
        const loadingCitas = document.getElementById('loadingCitas');
        const noCitas = document.getElementById('noCitas');
        const tablaCitas = document.getElementById('tablaCitas');
        const citasTableBody = document.getElementById('citasTableBody');
        const loadingDni = document.getElementById('loadingDni');
        const dniError = document.getElementById('dniError');
        
        // Elementos del formulario de cita
        const especialidadSelect = document.getElementById('especialidad');
        const doctorContainer = document.getElementById('doctorContainer');
        const doctorSelect = document.getElementById('doctor');
        const loadingDoctores = document.getElementById('loadingDoctores');
        const noDoctores = document.getElementById('noDoctores');
        const fechaContainer = document.getElementById('fechaContainer');
        const fechaInput = document.getElementById('fecha');
        const loadingFechas = document.getElementById('loadingFechas');
        const horarioContainer = document.getElementById('horarioContainer');
        const horariosDisponibles = document.getElementById('horariosDisponibles');
        const loadingHorarios = document.getElementById('loadingHorarios');
        const noHorarios = document.getElementById('noHorarios');
        const motivoContainer = document.getElementById('motivoContainer');
        const descripcionContainer = document.getElementById('descripcionContainer');
        const agendarCitaBtn = document.getElementById('agendarCitaBtn');
        const loadingAgendar = document.getElementById('loadingAgendar');
        
        // Inicialización
        function init() {
            // Si ya tenemos los datos del paciente, no necesitamos hacer nada más
            if (paciente) {
                console.log('Paciente ya registrado:', paciente);
                // Asegurarse de que citas siempre sea un array
                if (!Array.isArray(citas)) {
                    citas = [];
                }
                renderCitas();
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
                loadingCitas.classList.remove('hidden');
                noCitas.classList.add('hidden');
                tablaCitas.classList.add('hidden');
            } else {
                loadingCitas.classList.add('hidden');
                
                if (citas.length === 0) {
                    noCitas.classList.remove('hidden');
                    tablaCitas.classList.add('hidden');
                } else {
                    noCitas.classList.add('hidden');
                    tablaCitas.classList.remove('hidden');
                }
            }
        }
        
        // Renderizar citas
        function renderCitas() {
            // Verificar inmediatamente si hay citas
            updateLoadingState();
            
            if (citas.length === 0) {
                return;
            }
            
            // Limpiar tabla
            citasTableBody.innerHTML = '';
            
            // Agregar filas de citas
            citas.forEach(cita => {
                const row = document.createElement('tr');
                
                // Formatear fecha
                const fecha = new Date(cita.fecha);
                const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                
                // Determinar clase de estado
                let estadoClass = '';
                switch (cita.estado) {
                    case 'pendiente':
                        estadoClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'confirmada':
                        estadoClass = 'bg-green-100 text-green-800';
                        break;
                    case 'cancelada':
                        estadoClass = 'bg-red-100 text-red-800';
                        break;
                    case 'completada':
                        estadoClass = 'bg-blue-100 text-blue-800';
                        break;
                }
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fechaFormateada}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cita.hora_inicio} - ${cita.hora_fin}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cita.doctor.nombre_completo}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cita.doctor.especialidad}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                            ${cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ${cita.estado === 'pendiente' ? `
                            <button 
                                class="text-red-600 hover:text-red-900 mr-2 cancelar-cita"
                                data-cita-id="${cita.id}"
                            >
                                <i class="fas fa-times-circle"></i> Cancelar
                            </button>
                        ` : ''}
                        <button 
                            class="text-blue-600 hover:text-blue-900 ver-cita"
                            data-cita-id="${cita.id}"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                `;
                
                citasTableBody.appendChild(row);
            });
            
            // Agregar event listeners para botones de acción
            document.querySelectorAll('.cancelar-cita').forEach(btn => {
                btn.addEventListener('click', function() {
                    const citaId = this.getAttribute('data-cita-id');
                    cancelarCita(citaId);
                });
            });
            
            document.querySelectorAll('.ver-cita').forEach(btn => {
                btn.addEventListener('click', function() {
                    const citaId = this.getAttribute('data-cita-id');
                    verDetalleCita(citaId);
                });
            });
        }
        
        // Verificar DNI
        if (document.getElementById('verificarDniForm')) {
            document.getElementById('verificarDniForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const dni = document.getElementById('dni').value.trim();
                
                if (dni.length !== 8 || !/^\d+$/.test(dni)) {
                    dniError.textContent = 'El DNI debe tener 8 dígitos numéricos.';
                    dniError.classList.remove('hidden');
                    return;
                }
                
                dniError.classList.add('hidden');
                loadingDni.classList.remove('hidden');
                document.getElementById('verificarDniBtn').disabled = true;
                
                // Llamada a la API para verificar DNI
                fetch('/api/pacientes/verificar-dni', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ dni })
                })
                .then(response => response.json())
                .then(data => {
                    loadingDni.classList.add('hidden');
                    document.getElementById('verificarDniBtn').disabled = false;
                    
                    if (data.success) {
                        // DNI verificado correctamente
                        paciente = data.paciente;
                        dniVerificationForm.classList.add('hidden');
                        citaForm.classList.remove('hidden');
                        citasAgendadas.classList.remove('hidden');
                        
                        // Actualizar datos del paciente en el formulario
                        document.getElementById('nombrePaciente').textContent = paciente.nombre_completo;
                        document.getElementById('dniPaciente').textContent = paciente.dni;
                        
                        // Cargar citas del paciente
                        citas = data.citas || [];
                        renderCitas();
                        
                        showMessage('success', 'DNI verificado correctamente. Ahora puede agendar una cita.');
                    } else {
                        // Error al verificar DNI
                        dniError.textContent = data.message || 'No se encontró un paciente con ese DNI.';
                        dniError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingDni.classList.add('hidden');
                    document.getElementById('verificarDniBtn').disabled = false;
                    dniError.textContent = 'Ocurrió un error al verificar el DNI. Intente nuevamente.';
                    dniError.classList.remove('hidden');
                });
            });
        }
        
        // Eventos para el formulario de cita
        if (especialidadSelect) {
            // Evento al cambiar especialidad
            especialidadSelect.addEventListener('change', function() {
                const especialidad = this.value;
                
                // Reiniciar selecciones posteriores
                doctorSelect.innerHTML = '<option value="">Seleccione un doctor</option>';
                doctorSelect.disabled = true;
                fechaInput.value = '';
                fechaInput.disabled = true;
                horariosDisponibles.innerHTML = '';
                motivoContainer.classList.add('hidden');
                descripcionContainer.classList.add('hidden');
                agendarCitaBtn.classList.add('hidden');
                agendarCitaBtn.disabled = true;
                
                if (!especialidad) {
                    doctorContainer.classList.add('hidden');
                    fechaContainer.classList.add('hidden');
                    horarioContainer.classList.add('hidden');
                    return;
                }
                
                // Mostrar carga de doctores
                doctorContainer.classList.remove('hidden');
                loadingDoctores.classList.remove('hidden');
                noDoctores.classList.add('hidden');
                
                // Buscar doctores por especialidad
                fetch(`/api/doctores/por-especialidad/${especialidad}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingDoctores.classList.add('hidden');
                    
                    if (data.success && data.doctores && data.doctores.length > 0) {
                        doctoresDisponibles = data.doctores;
                        
                        // Llenar select de doctores
                        doctorSelect.innerHTML = '<option value="">Seleccione un doctor</option>';
                        doctoresDisponibles.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = `${doctor.nombre_completo}`;
                            doctorSelect.appendChild(option);
                        });
                        
                        doctorSelect.disabled = false;
                    } else {
                        noDoctores.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingDoctores.classList.add('hidden');
                    noDoctores.classList.remove('hidden');
                });
            });
            
            // Evento al cambiar doctor
            doctorSelect.addEventListener('change', function() {
                const doctorId = this.value;
                
                // Reiniciar selecciones posteriores
                fechaInput.value = '';
                fechaInput.disabled = true;
                horariosDisponibles.innerHTML = '';
                motivoContainer.classList.add('hidden');
                descripcionContainer.classList.add('hidden');
                agendarCitaBtn.classList.add('hidden');
                agendarCitaBtn.disabled = true;
                
                if (!doctorId) {
                    fechaContainer.classList.add('hidden');
                    horarioContainer.classList.add('hidden');
                    return;
                }
                
                // Mostrar selector de fecha
                fechaContainer.classList.remove('hidden');
                fechaInput.disabled = false;
            });
            
            // Evento al cambiar fecha
            fechaInput.addEventListener('change', function() {
                const fecha = this.value;
                const doctorId = doctorSelect.value;
                
                // Reiniciar selecciones posteriores
                horariosDisponibles.innerHTML = '';
                motivoContainer.classList.add('hidden');
                descripcionContainer.classList.add('hidden');
                agendarCitaBtn.classList.add('hidden');
                agendarCitaBtn.disabled = true;
                
                if (!fecha || !doctorId) {
                    horarioContainer.classList.add('hidden');
                    return;
                }
                
                // Mostrar carga de horarios
                horarioContainer.classList.remove('hidden');
                loadingHorarios.classList.remove('hidden');
                noHorarios.classList.add('hidden');
                
                // Buscar horarios disponibles
                fetch(`/api/citas/horarios-disponibles/${doctorId}/${fecha}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingHorarios.classList.add('hidden');
                    
                    if (data.success && data.horarios && data.horarios.length > 0) {
                        // Limpiar selección previa
                        horariosSeleccionados = [];
                        
                        // Llenar grid de horarios
                        horariosDisponibles.innerHTML = '';
                        data.horarios.forEach(horario => {
                            const horarioBtn = document.createElement('button');
                            horarioBtn.type = 'button';
                            horarioBtn.className = 'py-2 px-4 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-blue-50 focus:outline-none';
                            horarioBtn.textContent = `${horario.hora_inicio} - ${horario.hora_fin}`;
                            horarioBtn.dataset.horaInicio = horario.hora_inicio;
                            horarioBtn.dataset.horaFin = horario.hora_fin;
                            
                            horarioBtn.addEventListener('click', function() {
                                // Deseleccionar todos los horarios
                                document.querySelectorAll('#horariosDisponibles button').forEach(btn => {
                                    btn.classList.remove('bg-blue-100', 'border-blue-500', 'text-blue-700');
                                    btn.classList.add('border-gray-300', 'text-gray-700');
                                });
                                
                                // Seleccionar este horario
                                this.classList.remove('border-gray-300', 'text-gray-700');
                                this.classList.add('bg-blue-100', 'border-blue-500', 'text-blue-700');
                                
                                // Guardar selección
                                horariosSeleccionados = [{
                                    hora_inicio: this.dataset.horaInicio,
                                    hora_fin: this.dataset.horaFin
                                }];
                                
                                // Mostrar campos adicionales
                                motivoContainer.classList.remove('hidden');
                                descripcionContainer.classList.remove('hidden');
                                agendarCitaBtn.classList.remove('hidden');
                                agendarCitaBtn.disabled = false;
                            });
                            
                            horariosDisponibles.appendChild(horarioBtn);
                        });
                    } else {
                        noHorarios.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingHorarios.classList.add('hidden');
                    noHorarios.classList.remove('hidden');
                });
            });
        }
        
        // Evento para agendar cita
        if (document.getElementById('agendarCitaForm')) {
            document.getElementById('agendarCitaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!paciente || !doctorSelect.value || !fechaInput.value || horariosSeleccionados.length === 0) {
                    showMessage('error', 'Por favor complete todos los campos requeridos.');
                    return;
                }
                
                const motivoConsulta = document.getElementById('motivo_consulta').value;
                const descripcionMalestar = document.getElementById('descripcion_malestar').value;
                
                if (!motivoConsulta) {
                    showMessage('error', 'Por favor seleccione un motivo de consulta.');
                    return;
                }
                
                // Mostrar carga
                loadingAgendar.classList.remove('hidden');
                agendarCitaBtn.disabled = true;
                
                // Preparar datos para enviar
                const citaData = {
                    paciente_id: paciente.id,
                    doctor_id: doctorSelect.value,
                    fecha: fechaInput.value,
                    hora_inicio: horariosSeleccionados[0].hora_inicio,
                    hora_fin: horariosSeleccionados[0].hora_fin,
                    motivo_consulta: motivoConsulta,
                    descripcion_malestar: descripcionMalestar
                };
                
                // Enviar solicitud para agendar cita
                fetch('/api/citas/agendar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(citaData)
                })
                .then(response => response.json())
                .then(data => {
                    loadingAgendar.classList.add('hidden');
                    agendarCitaBtn.disabled = false;
                    
                    if (data.success) {
                        // Cita agendada correctamente
                        showMessage('success', 'Cita agendada correctamente.');
                        
                        // Agregar la nueva cita a la lista
                        citas.push(data.cita);
                        renderCitas();
                        
                        // Reiniciar formulario
                        document.getElementById('agendarCitaForm').reset();
                        doctorContainer.classList.add('hidden');
                        fechaContainer.classList.add('hidden');
                        horarioContainer.classList.add('hidden');
                        motivoContainer.classList.add('hidden');
                        descripcionContainer.classList.add('hidden');
                        agendarCitaBtn.classList.add('hidden');
                    } else {
                        // Error al agendar cita
                        showMessage('error', data.message || 'Ocurrió un error al agendar la cita. Intente nuevamente.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingAgendar.classList.add('hidden');
                    agendarCitaBtn.disabled = false;
                    showMessage('error', 'Ocurrió un error al agendar la cita. Intente nuevamente.');
                });
            });
        }
        
        // Función para cancelar cita
        function cancelarCita(citaId) {
            if (!confirm('¿Está seguro que desea cancelar esta cita?')) {
                return;
            }
            
            fetch(`/api/citas/cancelar/${citaId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', 'Cita cancelada correctamente.');
                    
                    // Actualizar estado de la cita en la lista
                    const citaIndex = citas.findIndex(c => c.id == citaId);
                    if (citaIndex !== -1) {
                        citas[citaIndex].estado = 'cancelada';
                        renderCitas();
                    }
                } else {
                    showMessage('error', data.message || 'Ocurrió un error al cancelar la cita.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Ocurrió un error al cancelar la cita.');
            });
        }
        
        // Función para ver detalle de cita
        function verDetalleCita(citaId) {
            const cita = citas.find(c => c.id == citaId);
            if (!cita) return;
            
            // Aquí podrías mostrar un modal con los detalles de la cita
            alert(`Detalles de la cita:\n\nFecha: ${new Date(cita.fecha).toLocaleDateString('es-ES')}\nHora: ${cita.hora_inicio} - ${cita.hora_fin}\nDoctor: ${cita.doctor.nombre_completo}\nEspecialidad: ${cita.doctor.especialidad}\nMotivo: ${cita.motivo_consulta}\nDescripción: ${cita.descripcion_malestar || 'No especificada'}\nEstado: ${cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)}`);
        }
        
        // Inicializar
        init();
    });
    </script>
</body>
</html>