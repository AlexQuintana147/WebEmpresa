<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agendar Cita - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Estilos médicos personalizados */
        .medical-gradient {
            background: linear-gradient(135deg, #e6f7ff 0%, #cce7f8 100%);
            position: relative;
            overflow: hidden;
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
        .select-medical {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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
<body class="bg-gradient-to-br from-blue-50 to-cyan-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Contenido Principal -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Título de la página con decoración médica -->
                <div class="mb-10 text-center relative">
                    <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                        <div class="absolute top-10 left-10 w-8 h-8 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 0s;"></div>
                        <div class="absolute top-5 right-10 w-6 h-6 border-2 border-blue-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 1s;"></div>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 relative z-10">Agendar Nueva Cita</h1>
                    <p class="mt-2 text-gray-600">Complete el formulario para programar su cita médica</p>
                </div>
                <!-- Información del Paciente -->
                @if(auth()->check() && auth()->user()->paciente)
                    <div class="medical-card bg-white p-6 mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Información del Paciente</h2>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <p class="text-gray-600">Nombre completo: <span class="font-medium text-gray-800">{{ auth()->user()->paciente->nombre }} {{ auth()->user()->paciente->apellido }}</span></p>
                                <p class="text-gray-600">DNI: <span class="font-medium text-gray-800">{{ auth()->user()->paciente->dni }}</span></p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="medical-card bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Para agendar una cita, primero debe completar su información como paciente.
                                    Por favor, contacte con administración.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Formulario de Cita -->
                <div class="medical-card bg-white p-6 medical-gradient">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Agendar Nueva Cita</h2>
                    <form action="{{ route('citas.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Selector de Categoría de Doctor -->
                        <div class="space-y-2">
                            <label for="categoria" class="block text-sm font-medium text-gray-700">Especialidad Médica</label>
                            <select name="categoria" id="categoria" class="select-medical mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white">
                                <option value="">Seleccione una especialidad</option>
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad }}">{{ $especialidad }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selector de Doctor -->
                        <div class="space-y-2">
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="select-medical mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white">
                                <option value="">Primero seleccione una especialidad</option>
                            </select>
                        </div>

                        <!-- Selector de Tipo de Consulta -->
                        <div class="space-y-2">
                            <label for="tipo_consulta" class="block text-sm font-medium text-gray-700">¿Qué desea?</label>
                            <select name="tipo_consulta" id="tipo_consulta" class="select-medical mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white">
                                <option value="">Seleccione el tipo de consulta</option>
                                <option value="consulta_general">Consulta General</option>
                                <option value="atencion_medica">Atención Médica</option>
                                <option value="consulta_especialidad">Consulta de Especialidad</option>
                                <option value="revision_resultados">Revisión de Resultados</option>
                            </select>
                        </div>

                        <!-- Selector de Día Disponible -->
                        <div class="space-y-2">
                            <label for="fecha_cita" class="block text-sm font-medium text-gray-700">Día Disponible</label>
                            <select name="fecha_cita" id="fecha_cita" class="select-medical mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white">
                                <option value="">Primero seleccione un doctor</option>
                            </select>
                        </div>

                        <!-- Selector de Horario Disponible -->
                        <div class="space-y-2">
                            <label for="hora_cita" class="block text-sm font-medium text-gray-700">Horario Disponible</label>
                            <select name="hora_cita" id="hora_cita" class="select-medical mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white">
                                <option value="">Primero seleccione un día</option>
                            </select>
                        </div>
                    
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:scale-105">
                                Agendar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.getElementById('categoria').addEventListener('change', function() {
            const categoria = this.value;
            const doctorSelect = document.getElementById('doctor_id');
            const tipoConsultaSelect = document.getElementById('tipo_consulta');
            const fechaSelect = document.getElementById('fecha_cita');
            
            // Limpiar opciones actuales
            doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
            tipoConsultaSelect.innerHTML = '<option value="">Seleccione el tipo de consulta</option>';
            tipoConsultaSelect.innerHTML += `
                <option value="consulta_general">Consulta General</option>
                <option value="atencion_medica">Atención Médica</option>
                <option value="consulta_especialidad">Consulta de Especialidad</option>
                <option value="revision_resultados">Revisión de Resultados</option>
            `;
            fechaSelect.innerHTML = '<option value="">Primero seleccione un doctor y tipo de consulta</option>';
            
            if (categoria) {
                fetch(`/api/doctores/${categoria}`)
                    .then(response => response.json())
                    .then(data => {
                        doctorSelect.innerHTML = '<option value="">Seleccione un doctor</option>';
                        if (data && data.doctores && Array.isArray(data.doctores)) {
                            for (const doctor of data.doctores) {
                                const nombreCompleto = `${doctor.nombre} ${doctor.apellido_paterno || ''} ${doctor.apellido_materno || ''}`.trim();
                                doctorSelect.innerHTML += `<option value="${doctor.id}">${nombreCompleto}</option>`;
                            }
                        } else {
                            console.error('La respuesta no tiene el formato esperado:', data);
                            doctorSelect.innerHTML = '<option value="">Error: formato de respuesta inválido</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        doctorSelect.innerHTML = '<option value="">Error al cargar doctores</option>';
                    });
            } else {
                doctorSelect.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
                fechaSelect.innerHTML = '<option value="">Primero seleccione un doctor y tipo de consulta</option>';
            }
        });

        function actualizarHorarios() {
            const doctorId = document.getElementById('doctor_id').value;
            const tipoConsulta = document.getElementById('tipo_consulta').value;
            const fecha = document.getElementById('fecha_cita').value;
            const horaSelect = document.getElementById('hora_cita');
            
            if (!doctorId || !tipoConsulta || !fecha) {
                horaSelect.innerHTML = '<option value="">Complete la selección anterior</option>';
                return;
            }
            
            horaSelect.innerHTML = '<option value="">Cargando horarios disponibles...</option>';
            
            // Hacer la petición AJAX para obtener los horarios disponibles según el tipo de consulta
            fetch(`/api/doctores/${doctorId}/horarios-disponibles/${fecha}/${tipoConsulta}`)
                .then(response => response.json())
                .then(data => {
                    horaSelect.innerHTML = '<option value="">Seleccione un horario</option>';
                    if (data.success && data.horarios && Array.isArray(data.horarios)) {
                        for (const horario of data.horarios) {
                            const horaInicio = horario.hora_inicio.substring(0, 5);
                            const horaFin = horario.hora_fin.substring(0, 5);
                            horaSelect.innerHTML += `<option value="${horario.hora_inicio}">${horaInicio} - ${horaFin}</option>`;
                        }
                    } else {
                        console.error('La respuesta no tiene el formato esperado:', data);
                        horaSelect.innerHTML = '<option value="">Error: formato de respuesta inválido</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
                });
        }

        document.getElementById('doctor_id').addEventListener('change', function() {
            const doctorId = this.value;
            const fechaSelect = document.getElementById('fecha_cita');
            const horaSelect = document.getElementById('hora_cita');
            
            fechaSelect.innerHTML = '<option value="">Cargando días disponibles...</option>';
            horaSelect.innerHTML = '<option value="">Primero seleccione un día</option>';
            
            if (doctorId) {
                fetch(`/api/doctores/${doctorId}/dias-disponibles`)
                    .then(response => response.json())
                    .then(data => {
                        fechaSelect.innerHTML = '<option value="">Seleccione un día disponible</option>';
                        if (data && data.dias && Array.isArray(data.dias)) {
                            for (const dia of data.dias) {
                                const fecha = new Date(dia.fecha);
                                const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                fechaSelect.innerHTML += `<option value="${dia.fecha}">${fechaFormateada}</option>`;
                            }
                        } else {
                            console.error('La respuesta no tiene el formato esperado:', data);
                            fechaSelect.innerHTML = '<option value="">Error: formato de respuesta inválido</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        fechaSelect.innerHTML = '<option value="">Error al cargar días disponibles</option>';
                    });
            } else {
                fechaSelect.innerHTML = '<option value="">Primero seleccione un doctor</option>';
                horaSelect.innerHTML = '<option value="">Primero seleccione un día</option>';
            }
        });

        document.getElementById('fecha_cita').addEventListener('change', actualizarHorarios);
        document.getElementById('tipo_consulta').addEventListener('change', actualizarHorarios);
    </script>
    
</body>
</html>