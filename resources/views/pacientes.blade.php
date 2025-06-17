<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pacientes - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-cyan-700 flex items-center gap-2">
                        <i class="fas fa-users-medical"></i> Gestión de Pacientes
                    </h1>
                </div>
                
                <!-- Mensaje de estado -->
                <div 
                    id="messageContainer"
                    class="border-l-4 p-4 mb-6 hidden transition-message message-hidden bg-white rounded-lg shadow flex items-center gap-3"
                >
                    <i id="messageIcon" class="text-xl"></i>
                    <span id="messageText" class="text-base"></span>
                </div>
                
                <!-- Formulario de verificación de DNI (mostrar solo si el doctor no tiene DNI) -->
                <div id="dniVerificationForm" class="medical-card bg-white overflow-hidden mb-6 {{ $doctor ? 'hidden' : '' }} rounded-xl shadow border border-cyan-100">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fas fa-id-card"></i> Verificación de Identidad</h2>
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
                                        autocomplete="off"
                                    >
                                </div>
                                <p id="dniError" class="mt-1 text-sm text-red-600 hidden"></p>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <p id="loadingDni" class="text-sm text-cyan-600 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Verificando DNI...</p>
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2 shadow" 
                                    id="verificarDniBtn"
                                >
                                    <i class="fas fa-check-circle"></i> <span>Verificar DNI</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Formulario para completar datos del doctor (mostrar después de verificar DNI) -->
                <div id="doctorForm" class="medical-card bg-white overflow-hidden mb-6 hidden rounded-xl shadow border border-cyan-100">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fas fa-user-md"></i> Complete sus Datos</h2>
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
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2 shadow" 
                                    id="guardarDatosBtn"
                                >
                                    <i class="fas fa-save"></i> <span>Guardar Datos</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Información del Doctor -->
                <div id="doctorInfo" class="medical-card bg-white overflow-hidden mb-6 {{ $doctor ? '' : 'hidden' }} rounded-xl shadow border border-cyan-100">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fas fa-user-md"></i> Información del Médico</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Nombre Completo</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $doctor ? $doctor->nombre . ' ' . $doctor->apellido_paterno . ' ' . $doctor->apellido_materno : '' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Especialidad</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $doctor ? $doctor->especialidad : '' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">DNI</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $doctor ? $doctor->dni : '' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Teléfono</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $doctor ? $doctor->telefono : '' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Correo Electrónico</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $doctor ? $doctor->correo : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Pacientes del Doctor -->
                <div id="pacientesList" class="medical-card bg-white overflow-hidden mb-6 {{ $doctor ? '' : 'hidden' }} shadow-lg rounded-2xl border border-cyan-200">
                    <div class="p-6">
                        <h2 class="text-3xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fas fa-users"></i> Lista de Pacientes</h2>
                        @if(count($pacientes) > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full divide-y divide-cyan-100 bg-white">
                                <thead class="bg-cyan-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">DNI</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Nombre Completo</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Correo</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Teléfono</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Fecha Registro</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-cyan-700 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-cyan-100">
                                    @foreach($pacientes as $index => $paciente)
                                    <tr class="hover:bg-cyan-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $paciente->dni }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                            <i class="fas fa-user-circle text-cyan-400 mr-1"></i>{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700">{{ $paciente->correo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700">{{ $paciente->telefono }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $paciente->created_at ? $paciente->created_at->format('d/m/Y') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700">{{ $paciente->citas->where('estado', 'pendiente')->count() > 0 || $paciente->citas->where('estado', 'En Espera')->count() > 0 ? 'Pendiente' : 'Completo' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700 flex gap-2">
                                            <button onclick="showHistorialModal({{ $paciente->id }})" class="bg-cyan-100 hover:bg-cyan-200 text-cyan-700 font-semibold py-1 px-3 rounded shadow-sm border border-cyan-200 transition-all flex items-center gap-1" title="Ver historial">
                                                <i class="fas fa-notes-medical"></i> Historial
                                            </button>
                                            <button onclick="showDetallesModal({{ $index }}, {{ $paciente->citas->last()->id ?? 'null' }})" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-1 px-3 rounded shadow-sm border border-cyan-600 transition-all flex items-center gap-1" title="Más detalles">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
                                            @if(!$paciente->doctor_id)
                                            <button onclick="showSeguimientoModal({{ $paciente->id }})" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded shadow-sm border border-green-600 transition-all flex items-center gap-1" title="Asignar seguimiento">
                                                <i class="fas fa-user-md"></i> Dar Seguimiento
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-8">
                            <i class="fas fa-user-slash text-5xl text-cyan-200 mb-4"></i>
                            <p class="text-gray-500 text-lg">No se encontraron pacientes para este doctor.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Modal para Detalles/Historial -->
                <div id="modalPaciente" class="fixed z-50 inset-0 overflow-y-auto hidden">
                  <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                      <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-2xl transform transition-all max-w-lg w-full p-8 z-50 border-t-8 border-cyan-500">
                      <div class="flex justify-between items-center mb-6">
                        <h3 id="modalPacienteTitle" class="text-2xl font-bold text-cyan-700"></h3>
                        <button onclick="closeModalPaciente()" class="text-gray-400 hover:text-cyan-600 focus:outline-none" title="Cerrar">
                          <i class="fas fa-times text-xl"></i>
                        </button>
                      </div>
                      <div id="modalPacienteContent" class="text-gray-700 text-lg min-h-[60px] pb-2">
                        <!-- Aquí el contenido dinámico -->
                      </div>
                      <div id="modalButtons" class="mt-8 flex justify-end gap-4">
                        <!-- Los botones se agregarán dinámicamente desde JavaScript -->
                      </div>
                    </div>
                  </div>
                </div>
            </main>
        </div>
    </div>

    <style>
    .markdown-content {
        font-size: 14px;
        line-height: 1.6;
    }
    .markdown-content strong {
        font-weight: 700;
        color: #155e75; /* Azul verdoso oscuro */
    }
    .markdown-content ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .markdown-content ol {
        list-style-type: decimal;
        margin-left: 1.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .markdown-content li {
        margin-bottom: 0.25rem;
    }
    .markdown-content p {
        margin-bottom: 0.75rem;
    }
</style>

<script>
        console.log('Script de pacientes.blade.php cargado');
        window.pacientes = @json($pacientes);
        
        /**
         * Formatea texto con sintaxis Markdown a HTML
         * @param {string} texto - Texto con formato Markdown
         * @return {string} - HTML formateado
         */
        function formatearMarkdown(texto) {
            if (!texto) return '';
            
            // Convertir negritas: **texto** a <strong>texto</strong>
            texto = texto.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
            
            // Procesar listas numeradas (líneas que comienzan con número seguido de punto o paréntesis)
            let lines = texto.split('\n');
            let inOrderedList = false;
            let inUnorderedList = false;
            
            for (let i = 0; i < lines.length; i++) {
                // Detectar listas numeradas
                if (lines[i].match(/^\d+\.\s+/)) {
                    if (!inOrderedList) {
                        lines[i] = '<ol>\n<li>' + lines[i].replace(/^\d+\.\s+/, '') + '</li>';
                        inOrderedList = true;
                    } else {
                        lines[i] = '<li>' + lines[i].replace(/^\d+\.\s+/, '') + '</li>';
                    }
                } 
                // Detectar listas con viñetas
                else if (lines[i].match(/^[\*\-]\s+/)) {
                    if (!inUnorderedList) {
                        lines[i] = '<ul>\n<li>' + lines[i].replace(/^[\*\-]\s+/, '') + '</li>';
                        inUnorderedList = true;
                    } else {
                        lines[i] = '<li>' + lines[i].replace(/^[\*\-]\s+/, '') + '</li>';
                    }
                } 
                // Cerrar listas si la siguiente línea no es parte de la lista
                else {
                    if (inOrderedList) {
                        lines[i-1] += '\n</ol>';
                        inOrderedList = false;
                    }
                    if (inUnorderedList) {
                        lines[i-1] += '\n</ul>';
                        inUnorderedList = false;
                    }
                }
            }
            
            // Cerrar listas al final si es necesario
            if (inOrderedList) {
                lines[lines.length-1] += '\n</ol>';
            }
            if (inUnorderedList) {
                lines[lines.length-1] += '\n</ul>';
            }
            
            // Convertir saltos de línea en etiquetas <p>
            texto = lines.join('\n');
            
            // Añadir espaciado entre secciones
            texto = texto.replace(/\n\n([^<])/g, '\n<p>$1');
            texto = texto.replace(/([^>])\n\n/g, '$1</p>\n');
            
            return texto;
        }

        function showDetallesModal(index, citaId) {
            try {
                const paciente = window.pacientes[index];
                let cita = null;
                if (paciente.citas && paciente.citas.length > 0) {
                    if (citaId) {
                        cita = paciente.citas.find(c => c.id == citaId);
                    }
                    if (!cita) {
                        cita = paciente.citas.reduce((a, b) => new Date(a.fecha) > new Date(b.fecha) ? a : b);
                    }
                }
                let descripcion = cita ? (cita.descripcion_malestar ?? '-') : '-';
                let respuestaBot = cita ? (cita.respuesta_bot ?? null) : null;
                let idCita = cita ? cita.id : null;
                document.getElementById('modalPacienteTitle').innerText = 'Descripción del malestar';
                document.getElementById('modalPacienteContent').innerHTML = `<p class='text-gray-700 whitespace-pre-line'>${descripcion ? descripcion : '-'}</p>`;
                if (respuestaBot) {
                    document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-green-300 bg-green-50 text-green-900'><h3 class="font-bold text-lg mb-2">Diagnóstico IA Alternativo:</h3><div class="whitespace-pre-line markdown-content">${formatearMarkdown(respuestaBot)}</div></div>`;
                }
                document.getElementById('modalPaciente').classList.remove('hidden');
                
                // Botones en el modal
                const modalButtons = document.getElementById('modalButtons');
                if (modalButtons) {
                    // Limpiar botones anteriores
                    while (modalButtons.firstChild) {
                        modalButtons.removeChild(modalButtons.firstChild);
                    }
                    
                    // Botón para registrar historial médico
                    const btnRegistrarHistorial = document.createElement('button');
                    const estadoCita = cita ? cita.estado : null;
                    const esCompletada = estadoCita === 'completada';
                    
                    btnRegistrarHistorial.className = `${esCompletada ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'} text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2 transition-all duration-200`;
                    btnRegistrarHistorial.innerHTML = '<i class="fas fa-notes-medical"></i> ' + (esCompletada ? 'Historial Registrado' : 'Registrar Historial');
                    btnRegistrarHistorial.disabled = esCompletada;
                    
                    if (!esCompletada) {
                        btnRegistrarHistorial.addEventListener('click', function() {
                            window.location.href = `/pacientes/${paciente.id}/historial/crear/${idCita || ''}`;
                        });
                    }
                    
                    modalButtons.appendChild(btnRegistrarHistorial);
                    
                    // Botón para diagnóstico IA
                    const btnDiagnosticoIAAlt = document.createElement('button');
                    btnDiagnosticoIAAlt.id = 'btnDiagnosticoIAAlt';
                    btnDiagnosticoIAAlt.className = 'bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2 transition-all duration-200';
                    btnDiagnosticoIAAlt.disabled = !!respuestaBot;
                    btnDiagnosticoIAAlt.innerHTML = respuestaBot ? 
                        '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo (ya realizado)' : 
                        '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo';
                    btnDiagnosticoIAAlt.addEventListener('click', async function() {
                        btnDiagnosticoIAAlt.disabled = true;
                        btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials fa-spin"></i> Consultando IA...';
                        const descripcionElem = document.querySelector('#modalPacienteContent p');
                        const descripcion = descripcionElem ? descripcionElem.innerText.trim() : '';
                        try {
                            const resp = await fetch('/diagnostico-ia', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ descripcion })
                            });
                            const data = await resp.json();
                            if (data.success) {
                                document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-green-300 bg-green-50 text-green-900'><h3 class="font-bold text-lg mb-2">Diagnóstico IA Alternativo:</h3><div class="whitespace-pre-line markdown-content">${formatearMarkdown(data.respuesta)}</div></div>`;
                                // Guardar respuesta IA en la cita correcta
                                if (idCita) {
                                    await fetch(`/citas/${idCita}/respuesta-bot`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({ respuesta_bot: data.respuesta })
                                    });
                                }
                                btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo (ya realizado)';
                            } else {
                                document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-red-300 bg-red-50 text-red-900'><b>Error IA Alternativo:</b> ${data.error}</div>`;
                                btnDiagnosticoIAAlt.disabled = false;
                                btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo';
                            }
                        } catch (e) {
                            document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-red-300 bg-red-50 text-red-900'><b>Error IA Alternativo:</b> ${e.message}</div>`;
                            btnDiagnosticoIAAlt.disabled = false;
                            btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo';
                        }
                    });
                    modalButtons.appendChild(btnDiagnosticoIAAlt);
                    
                    // Botón para cerrar
                    const btnCerrar = document.createElement('button');
                    btnCerrar.className = 'bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded shadow transition-all';
                    btnCerrar.innerHTML = 'Cerrar';
                    btnCerrar.addEventListener('click', closeModalPaciente);
                    modalButtons.appendChild(btnCerrar);
                }
            } catch (e) {
                document.getElementById('modalPacienteTitle').innerText = 'Error al mostrar detalles';
                document.getElementById('modalPacienteContent').innerHTML = `<pre class='text-red-600'>${e.message}\n${e.stack}</pre>`;
                document.getElementById('modalPaciente').classList.remove('hidden');
            }
        }

        function closeModalPaciente() {
            var modal = document.getElementById('modalPaciente');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function showHistorialModal(pacienteId) {
            try {
                document.getElementById('modalPacienteTitle').innerText = 'Historial Médico';
                document.getElementById('modalPacienteContent').innerHTML = `
                    <div class="flex justify-center items-center py-4">
                        <i class="fas fa-spinner fa-spin text-3xl text-cyan-500"></i>
                        <span class="ml-2 text-gray-600">Cargando historial médico...</span>
                    </div>
                `;
                document.getElementById('modalPaciente').classList.remove('hidden');
                
                // Hacer petición AJAX para obtener el historial médico
                fetch(`/pacientes/${pacienteId}/historial`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar el historial médico');
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Extraer solo la parte del historial médico del HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Obtener la información del paciente
                        const pacienteInfo = doc.querySelector('.bg-blue-50.p-6.rounded-lg.mb-8');
                        
                        // Obtener la línea de tiempo de historiales
                        const historiales = doc.querySelector('.relative.pl-8.space-y-8');
                        
                        if (!historiales) {
                            document.getElementById('modalPacienteContent').innerHTML = `
                                <div class="text-center py-4">
                                    <i class="fas fa-notes-medical text-3xl text-cyan-300 mb-2"></i>
                                    <p class="text-gray-600">Este paciente no tiene registros en su historial médico.</p>
                                </div>
                            `;
                        } else {
                            // Mostrar el historial médico en el modal
                            document.getElementById('modalPacienteContent').innerHTML = `
                                <div class="max-h-[60vh] overflow-y-auto pr-2">
                                    ${pacienteInfo ? pacienteInfo.outerHTML : ''}
                                    ${historiales.outerHTML}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        document.getElementById('modalPacienteContent').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle text-3xl text-red-500 mb-2"></i>
                                <p class="text-red-600">${error.message}</p>
                            </div>
                        `;
                    });
                
                // Botones en el modal
                const modalButtons = document.getElementById('modalButtons');
                if (modalButtons) {
                    // Limpiar botones anteriores
                    while (modalButtons.firstChild) {
                        modalButtons.removeChild(modalButtons.firstChild);
                    }
                    
                    // Botón para cerrar
                    const btnCerrar = document.createElement('button');
                    btnCerrar.className = 'bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded shadow transition-all';
                    btnCerrar.innerHTML = 'Cerrar';
                    btnCerrar.addEventListener('click', closeModalPaciente);
                    modalButtons.appendChild(btnCerrar);
                }
            } catch (e) {
                document.getElementById('modalPacienteTitle').innerText = 'Error al mostrar historial';
                document.getElementById('modalPacienteContent').innerHTML = `<pre class='text-red-600'>${e.message}\n${e.stack}</pre>`;
                document.getElementById('modalPaciente').classList.remove('hidden');
            }
        }
        
        function showSeguimientoModal(pacienteId) {
            try {
                document.getElementById('modalPacienteTitle').innerText = 'Confirmar Seguimiento';
                document.getElementById('modalPacienteContent').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-user-md text-5xl text-green-500 mb-4"></i>
                        <p class="text-gray-700 text-lg mb-6">¿Está seguro que desea dar seguimiento a este paciente?</p>
                        <p class="text-gray-600 mb-4">Al confirmar, usted será asignado como el doctor de este paciente y no se podrá cambiar posteriormente.</p>
                    </div>
                `;
                
                // Botones en el modal
                const modalButtons = document.getElementById('modalButtons');
                if (modalButtons) {
                    // Limpiar botones anteriores
                    while (modalButtons.firstChild) {
                        modalButtons.removeChild(modalButtons.firstChild);
                    }
                    
                    // Botón para confirmar
                    const btnConfirmar = document.createElement('button');
                    btnConfirmar.className = 'bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded shadow transition-all mr-4';
                    btnConfirmar.innerHTML = 'Confirmar';
                    btnConfirmar.addEventListener('click', () => asignarDoctor(pacienteId));
                    modalButtons.appendChild(btnConfirmar);
                    
                    // Botón para cancelar
                    const btnCancelar = document.createElement('button');
                    btnCancelar.className = 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition-all';
                    btnCancelar.innerHTML = 'Cancelar';
                    btnCancelar.addEventListener('click', closeModalPaciente);
                    modalButtons.appendChild(btnCancelar);
                }
                
                document.getElementById('modalPaciente').classList.remove('hidden');
            } catch (e) {
                console.error('Error al mostrar modal de seguimiento:', e);
            }
        }
        
        function asignarDoctor(pacienteId) {
            try {
                // Mostrar indicador de carga
                document.getElementById('modalPacienteContent').innerHTML = `
                    <div class="flex justify-center items-center py-4">
                        <i class="fas fa-spinner fa-spin text-3xl text-green-500"></i>
                        <span class="ml-2 text-gray-600">Asignando doctor al paciente...</span>
                    </div>
                `;
                
                // Realizar petición AJAX para asignar el doctor
                fetch('/pacientes/' + pacienteId + '/asignar-doctor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al asignar doctor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalPacienteContent').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                                <p class="text-gray-700 text-lg mb-2">${data.message}</p>
                                <p class="text-gray-600 mb-4">Puede ver los detalles en la sección de Atención Directa.</p>
                            </div>
                        `;
                        
                        // Actualizar botones
                        const modalButtons = document.getElementById('modalButtons');
                        if (modalButtons) {
                            // Limpiar botones anteriores
                            while (modalButtons.firstChild) {
                                modalButtons.removeChild(modalButtons.firstChild);
                            }
                            
                            // Botón para ir a atención directa
                            const btnAtenciónDirecta = document.createElement('button');
                            btnAtenciónDirecta.className = 'bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded shadow transition-all mr-4';
                            btnAtenciónDirecta.innerHTML = 'Ver Atención Directa';
                            btnAtenciónDirecta.addEventListener('click', () => window.location.href = '/atenciondirecta');
                            modalButtons.appendChild(btnAtenciónDirecta);
                            
                            // Botón para cerrar
                            const btnCerrar = document.createElement('button');
                            btnCerrar.className = 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition-all';
                            btnCerrar.innerHTML = 'Cerrar';
                            btnCerrar.addEventListener('click', () => {
                                closeModalPaciente();
                                // Recargar la página para actualizar la lista de pacientes
                                window.location.reload();
                            });
                            modalButtons.appendChild(btnCerrar);
                        }
                    } else {
                        throw new Error(data.message || 'Error al asignar doctor');
                    }
                })
                .catch(error => {
                    document.getElementById('modalPacienteContent').innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle text-5xl text-red-500 mb-4"></i>
                            <p class="text-red-600 text-lg mb-2">Error</p>
                            <p class="text-gray-700 mb-4">${error.message}</p>
                        </div>
                    `;
                    
                    // Actualizar botones
                    const modalButtons = document.getElementById('modalButtons');
                    if (modalButtons) {
                        // Limpiar botones anteriores
                        while (modalButtons.firstChild) {
                            modalButtons.removeChild(modalButtons.firstChild);
                        }
                        
                        // Botón para cerrar
                        const btnCerrar = document.createElement('button');
                        btnCerrar.className = 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition-all';
                        btnCerrar.innerHTML = 'Cerrar';
                        btnCerrar.addEventListener('click', closeModalPaciente);
                        modalButtons.appendChild(btnCerrar);
                    }
                });
            } catch (e) {
                console.error('Error al asignar doctor:', e);
            }
        }
    </script>
</body>
</html>