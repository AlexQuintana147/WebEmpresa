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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700">{{ $paciente->estado ? 'En Tratamiento' : 'Pendiente' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-700 flex gap-2">
                                            <button onclick="showHistorialModal('{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}')" class="bg-cyan-100 hover:bg-cyan-200 text-cyan-700 font-semibold py-1 px-3 rounded shadow-sm border border-cyan-200 transition-all flex items-center gap-1" title="Ver historial">
                                                <i class="fas fa-notes-medical"></i> Historial
                                            </button>
                                            <button onclick="showDetallesModal({{ $index }}, {{ $paciente->citas->last()->id ?? 'null' }})" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-1 px-3 rounded shadow-sm border border-cyan-600 transition-all flex items-center gap-1" title="Más detalles">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
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
                      <div class="mt-8 flex justify-end gap-4">
                        <button id="btnDiagnosticoIAAlt" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2 transition-all duration-200">
                            <i class="fas fa-vials"></i> Diagnóstico IA Alternativo
                        </button>
                        <button onclick="closeModalPaciente()" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded shadow transition-all">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        console.log('Script de pacientes.blade.php cargado');
        window.pacientes = @json($pacientes);

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
                    document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-green-300 bg-green-50 text-green-900'><b>Diagnóstico IA Alternativo:</b><br>${respuestaBot}</div>`;
                }
                document.getElementById('modalPaciente').classList.remove('hidden');
                const btnDiagnosticoIAAlt = document.getElementById('btnDiagnosticoIAAlt');
                if (btnDiagnosticoIAAlt) {
                    btnDiagnosticoIAAlt.disabled = !!respuestaBot;
                    if (respuestaBot) {
                        btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo (ya realizado)';
                    } else {
                        btnDiagnosticoIAAlt.innerHTML = '<i class="fas fa-vials"></i> Diagnóstico IA Alternativo';
                    }
                }
                if (btnDiagnosticoIAAlt && !btnDiagnosticoIAAlt.dataset.listener) {
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
                                document.getElementById('modalPacienteContent').innerHTML += `<div class='mt-6 p-4 rounded border border-green-300 bg-green-50 text-green-900'><b>Diagnóstico IA Alternativo:</b><br>${data.respuesta}</div>`;
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
                    btnDiagnosticoIAAlt.dataset.listener = 'true';
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
    </script>
</body>
</html>