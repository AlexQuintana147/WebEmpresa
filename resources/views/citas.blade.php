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
                        </div>
                        <!-- Aquí se mostrará el horario del doctor -->
                        <div id="doctor-schedule" class="mt-10 hidden">
                            <h3 class="text-lg font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-calendar-days"></i> Horario semanal del doctor</h3>
                            <div id="schedule-content" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
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
            // Limpiar opciones actuales
            doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
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
            }
        });

        document.getElementById('doctor_id').addEventListener('change', function() {
            const doctorId = this.value;
            const scheduleDiv = document.getElementById('doctor-schedule');
            const scheduleContent = document.getElementById('schedule-content');
            scheduleDiv.classList.add('hidden');
            scheduleContent.innerHTML = '';
            if (!doctorId) return;
            fetch(`/doctores/${doctorId}/horario`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.horario && data.horario.length > 0) {
                        scheduleDiv.classList.remove('hidden');
                        // Mostrar horario por día
                        const dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                        data.horario.forEach(item => {
                            const dia = dias[item.dia_semana-1] || 'Día';
                            const card = `<div class='bg-cyan-50 border-l-4 border-cyan-400 rounded-lg p-4 shadow flex flex-col gap-1'>
                                <div class='font-semibold text-cyan-700'><i class="fa-solid fa-calendar-day"></i> ${dia}</div>
                                <div class='text-gray-700'><i class="fa-solid fa-clock"></i> ${item.hora_inicio} - ${item.hora_fin}</div>
                                <div class='text-gray-500 text-sm'>${item.titulo || ''}</div>
                            </div>`;
                            scheduleContent.innerHTML += card;
                        });
                    } else {
                        scheduleDiv.classList.remove('hidden');
                        scheduleContent.innerHTML = `<div class='text-gray-500'>No hay horario registrado para este doctor.</div>`;
                    }
                });
        });
    </script>
    
</body>
</html>