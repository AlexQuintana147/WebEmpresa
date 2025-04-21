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
<body class="bg-gradient-to-br from-blue-50 to-cyan-50 min-h-screen">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <x-header />
            <!-- Mensajes de éxito/error -->
            @if(session('success'))
                <div class="max-w-2xl mx-auto mt-6">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow">
                        <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="max-w-2xl mx-auto mt-6">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Por favor corrige los siguientes errores:
                        <ul class="list-disc ml-6 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <!-- Contenido Principal -->
            <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <!-- Título de la página con decoración médica -->
                <div class="mb-10 text-center relative">
                    <h1 class="text-4xl font-bold text-cyan-700 mb-2 tracking-tight flex items-center justify-center gap-2">
                        <i class="fa-solid fa-calendar-check text-cyan-500"></i> Agendar Nueva Cita
                    </h1>
                    <p class="text-gray-500">Reserva tu cita médica de manera rápida y sencilla</p>
                </div>
                <!-- Card del formulario -->
                <div class="medical-card bg-white p-8 medical-gradient shadow-lg rounded-xl border border-cyan-100">
                    <form action="{{ route('citas.store') }}" method="POST" class="space-y-7">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="categoria" class="block text-gray-700 font-semibold mb-1">Especialidad Médica</label>
                                <div class="relative">
                                    <select name="categoria" id="categoria" class="select-medical w-full border border-cyan-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-sm transition">
                                        <option value="">Seleccione una especialidad</option>
                                        @foreach($especialidades as $especialidad)
                                            <option value="{{ $especialidad }}">{{ $especialidad }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-layer-group absolute right-3 top-3 text-cyan-300"></i>
                                </div>
                            </div>
                            <div>
                                <label for="doctor_id" class="block text-gray-700 font-semibold mb-1">Doctor</label>
                                <div class="relative">
                                    <select name="doctor_id" id="doctor_id" class="select-medical w-full border border-cyan-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-sm transition">
                                        <option value="">Primero seleccione una especialidad</option>
                                    </select>
                                    <i class="fa-solid fa-user-md absolute right-3 top-3 text-cyan-300"></i>
                                </div>
                            </div>
                            <div>
                                <label for="tipo_consulta" class="block text-gray-700 font-semibold mb-1">¿Qué desea?</label>
                                <div class="relative">
                                    <select name="tipo_consulta" id="tipo_consulta" class="select-medical w-full border border-cyan-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-sm transition">
                                        <option value="">Seleccione el tipo de consulta</option>
                                        <option value="consulta_general">Consulta General</option>
                                        <option value="atencion_medica">Atención Médica</option>
                                        <option value="consulta_especialidad">Consulta de Especialidad</option>
                                        <option value="revision_resultados">Revisión de Resultados</option>
                                    </select>
                                    <i class="fa-solid fa-stethoscope absolute right-3 top-3 text-cyan-300"></i>
                                </div>
                            </div>
                            <div>
                                <label for="fecha_cita" class="block text-gray-700 font-semibold mb-1">Día Disponible</label>
                                <div class="relative">
                                    <select name="fecha_cita" id="fecha_cita" class="select-medical w-full border border-cyan-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-sm transition">
                                        <option value="">Primero seleccione un doctor</option>
                                    </select>
                                    <i class="fa-solid fa-calendar-day absolute right-3 top-3 text-cyan-300"></i>
                                </div>
                            </div>
                            <div>
                                <label for="hora_cita" class="block text-gray-700 font-semibold mb-1">Horario Disponible</label>
                                <div class="relative">
                                    <select name="hora_cita" id="hora_cita" class="select-medical w-full border border-cyan-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-sm transition">
                                        <option value="">Primero seleccione un día</option>
                                    </select>
                                    <i class="fa-solid fa-clock absolute right-3 top-3 text-cyan-300"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-lg shadow-md transition flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Agendar Cita
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
            const tipoConsulta = document.getElementById('tipo_consulta').value;
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
                                const diaSemana = fecha.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
                                
                                // Filtrar días según el tipo de consulta
                                if (tipoConsulta === 'consulta_general' && !(diaSemana === 1 || diaSemana === 2)) {
                                    continue; // Saltar si no es lunes o martes para consulta general
                                } else if (tipoConsulta === 'atencion_medica' && diaSemana !== 3) {
                                    continue; // Saltar si no es miércoles para atención médica
                                } else if (tipoConsulta === 'consulta_especialidad' && diaSemana !== 4) {
                                    continue; // Saltar si no es jueves para consulta de especialidad
                                } else if (tipoConsulta === 'revision_resultados' && diaSemana !== 6) {
                                    continue; // Saltar si no es sábado para revisión de resultados
                                }
                                
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
        document.getElementById('tipo_consulta').addEventListener('change', function() {
            const doctorId = document.getElementById('doctor_id').value;
            if (doctorId) {
                // Volver a cargar los días disponibles cuando cambie el tipo de consulta
                document.getElementById('doctor_id').dispatchEvent(new Event('change'));
            }
            actualizarHorarios();
        });
    </script>
    
</body>
</html>