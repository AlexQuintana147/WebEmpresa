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
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow text-lg flex items-center gap-2">
                        <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-2xl mx-auto mt-6">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow text-lg flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
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
            <!-- Validación de cita existente -->
            @php
                $citasPendientes = isset($citas) ? $citas->whereIn('estado', ['pendiente', 'En Espera']) : collect([]);
            @endphp
            @if(count($citasPendientes) > 0)
                <div class="max-w-2xl mx-auto mt-8">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded shadow text-lg flex items-start gap-3">
                        <i class="fa-solid fa-calendar-check text-2xl mt-1"></i>
                        <div>
                            <span class="font-bold">Ya tienes una cita pendiente:</span>
                            <ul class="mt-2 text-base">
                                @foreach($citasPendientes as $cita)
                                    <li>
                                        <b>Fecha:</b> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}<br>
                                        <b>Hora:</b> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}<br>
                                        <b>Doctor:</b> {{ $cita->doctor->nombre }} {{ $cita->doctor->apellido_paterno }}<br>
                                        <b>Estado:</b> {{ ucfirst($cita->estado) }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-4 text-cyan-700 font-semibold">Por el momento no puedes registrar otra cita hasta que tu cita pendiente sea atendida o cancelada.</div>
                            <div class="mt-4">
                                <button id="btn-cancelar-cita" data-cita-id="{{ $citasPendientes->first()->id ?? '' }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition flex items-center gap-2">
                                    <i class="fa-solid fa-times-circle"></i> Cancelar Cita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @if(isset($citas) && count($citas) > 0)
                <div class="max-w-2xl mx-auto mt-8 mb-8">
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded shadow text-lg flex items-start gap-3">
                        <i class="fa-solid fa-info-circle text-2xl mt-1"></i>
                        <div>
                            <span class="font-bold">Historial de citas:</span>
                            <ul class="mt-2 text-base">
                                @foreach($citas as $cita)
                                    <li>
                                        <b>Fecha:</b> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}<br>
                                        <b>Hora:</b> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}<br>
                                        <b>Doctor:</b> {{ $cita->doctor->nombre }} {{ $cita->doctor->apellido_paterno }}<br>
                                        <b>Estado:</b> <span class="{{ $cita->estado == 'cancelada' ? 'text-red-600' : ($cita->estado == 'atendida' ? 'text-green-600' : 'text-yellow-600') }}">{{ ucfirst($cita->estado) }}</span>
                                    </li>
                                    @if(!$loop->last)<hr class="my-2">@endif
                                @endforeach
                            </ul>
                        </div>
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
                <!-- MODAL NUEVO PARA AGENDAR CITA (SOLO CAMPO DE MALSTAR EDITABLE) -->
                <div id="modal-cita" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                    <form id="form-cita" class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative space-y-4">
                        <button type="button" id="close-modal-cita" class="absolute top-2 right-2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                        <h2 class="text-xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-notes-medical"></i> Nueva Cita</h2>
                        <div id="modal-error-msg" class="hidden"></div>
                        <div>
                            <label for="modal-doctor" class="block font-semibold mb-1">Doctor</label>
                            <input id="modal-doctor" class="w-full border rounded px-3 py-2 bg-gray-100" value="" readonly required>
                            <input type="hidden" id="modal-doctor-id" name="doctor_id">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="modal-dia" class="block font-semibold mb-1">Día</label>
                                <input id="modal-dia" class="w-full border rounded px-3 py-2 bg-gray-100" value="" readonly required>
                                <input type="hidden" id="modal-dia-id" name="dia_semana">
                            </div>
                            <div>
                                <label for="modal-hora-inicio" class="block font-semibold mb-1">Hora Inicio</label>
                                <input type="time" id="modal-hora-inicio" name="hora_inicio" class="w-full border rounded px-3 py-2 bg-gray-100" value="" readonly required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="modal-hora-fin" class="block font-semibold mb-1">Hora Fin</label>
                                <input type="time" id="modal-hora-fin" name="hora_fin" class="w-full border rounded px-3 py-2 bg-gray-100" value="" readonly required>
                            </div>
                            <div></div>
                        </div>
                        <div>
                            <label for="modal-motivo" class="block font-semibold mb-1">Motivo</label>
                            <input type="text" id="modal-motivo" name="motivo" class="w-full border rounded px-3 py-2 bg-gray-100" value="" readonly required>
                        </div>
                        <div>
                            <label for="modal-descripcion" class="block font-semibold mb-1">Descripción del malestar</label>
                            <textarea id="modal-descripcion" name="descripcion_malestar" rows="3" class="w-full border rounded px-3 py-2" maxlength="1000" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="modal-submit-cita" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded-lg shadow-md transition flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Confirmar Cita
                            </button>
                        </div>
                    </form>
                </div>
                <!-- FIN MODAL NUEVO -->
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
                        <input type="hidden" name="dia_semana" id="input-dia_semana">
                        <input type="hidden" name="hora_inicio" id="input-hora_inicio">
                        <input type="hidden" name="hora_fin" id="input-hora_fin">
                        <input type="hidden" name="descripcion_malestar" id="input-descripcion-malestar">
                        <div class="flex justify-end mt-6">
                            <button type="button" id="btn-agendar-cita" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 px-8 rounded-lg shadow-md transition flex items-center gap-2" disabled>
                                <i class="fa-solid fa-paper-plane"></i> Agendar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </main>
            @endif
        </div>
    </div>
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prevenir envío tradicional del formulario SIEMPRE
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();
            });
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
                document.getElementById('btn-agendar-cita').disabled = true;
                document.getElementById('input-dia_semana').value = '';
                document.getElementById('input-hora_inicio').value = '';
                document.getElementById('input-hora_fin').value = '';
                if (!doctorId) return;
                fetch(`/doctores/${doctorId}/horario`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.horario && data.horario.length > 0) {
                            scheduleDiv.classList.remove('hidden');
                            const dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                            data.horario.forEach((item, idx) => {
                                const dia = dias[item.dia_semana-1] || 'Día';
                                const card = document.createElement('button');
                                card.type = 'button';
                                card.className = 'select-horario bg-cyan-50 border-l-4 border-cyan-400 rounded-lg p-4 shadow flex flex-col gap-1 hover:bg-cyan-100 focus:ring-2 focus:ring-cyan-500 outline-none transition mb-2';
                                card.dataset.diaSemana = item.dia_semana;
                                card.dataset.horaInicio = item.hora_inicio;
                                card.dataset.horaFin = item.hora_fin;
                                card.dataset.diaNombre = dia;
                                card.innerHTML = `<div class='font-semibold text-cyan-700'><i class=\"fa-solid fa-calendar-day\"></i> ${dia}</div>
                                    <div class='text-gray-700'><i class=\"fa-solid fa-clock\"></i> ${item.hora_inicio} - ${item.hora_fin}</div>
                                    <div class='text-gray-500 text-sm'>${item.titulo || ''}</div>`;
                                card.addEventListener('click', function() {
                                    // Marcar seleccionado
                                    document.querySelectorAll('.select-horario').forEach(btn => btn.classList.remove('ring-2', 'ring-cyan-500', 'bg-cyan-200'));
                                    card.classList.add('ring-2', 'ring-cyan-500', 'bg-cyan-200');
                                    // Guardar horario seleccionado
                                    document.getElementById('input-dia_semana').value = item.dia_semana;

                                    // Normaliza la hora a HH:MM:SS
                                    function normalizaHora(hora) {
                                        // Si ya viene como HH:MM:SS, retorna igual
                                        if (/^\d{2}:\d{2}:\d{2}$/.test(hora)) return hora;
                                        // Si viene como HH:MM, agrega :00
                                        if (/^\d{2}:\d{2}$/.test(hora)) return hora + ':00';
                                        // Si viene como 900, 0930, etc
                                        let h = hora.replace(/[^0-9]/g, '');
                                        if (h.length === 3) h = '0' + h;
                                        if (h.length === 4) return h.slice(0,2) + ':' + h.slice(2,4) + ':00';
                                        return '';
                                    }

                                    const horaInicio = normalizaHora(item.hora_inicio);
                                    const horaFin = normalizaHora(item.hora_fin);

                                    document.getElementById('input-hora_inicio').value = horaInicio;
                                    document.getElementById('input-hora_fin').value = horaFin;

                                    // Log para depuración
                                    console.log('Hora inicio enviada:', horaInicio);
                                    console.log('Hora fin enviada:', horaFin);

                                    document.getElementById('btn-agendar-cita').disabled = false;
                                });
                                scheduleContent.appendChild(card);
                            });
                        } else {
                            scheduleDiv.classList.remove('hidden');
                            scheduleContent.innerHTML = `<div class='text-gray-500'>No hay horario registrado para este doctor.</div>`;
                        }
                    });
            });
            // ABRIR MODAL NUEVO
            window.openModalCita = function() {
                const doctorSelect = document.getElementById('doctor_id');
                const selectedHorario = document.querySelector('.select-horario.ring-2');
                let errorMsg = '';
                if (!doctorSelect || !doctorSelect.value) errorMsg = 'Selecciona un doctor antes de agendar.';
                if (!selectedHorario) errorMsg = 'Selecciona un horario antes de agendar.';
                if (errorMsg) {
                    alert(errorMsg);
                    return;
                }
                // LLENAR CAMPOS ANTES DE MOSTRAR EL MODAL
                document.getElementById('modal-doctor').value = doctorSelect.options[doctorSelect.selectedIndex].text;
                document.getElementById('modal-doctor-id').value = doctorSelect.value;
                document.getElementById('modal-dia').value = selectedHorario.getAttribute('data-dia-nombre') || '';
                document.getElementById('modal-dia-id').value = selectedHorario.getAttribute('data-dia-semana');
                document.getElementById('modal-hora-inicio').value = selectedHorario.getAttribute('data-hora-inicio');
                document.getElementById('modal-hora-fin').value = selectedHorario.getAttribute('data-hora-fin');
                // Motivo: si hay un campo específico, úsalo. Si no, busca en el dataset o en el texto de la tarjeta
                let motivo = '';
                if(selectedHorario.querySelector('.text-gray-500')) {
                    motivo = selectedHorario.querySelector('.text-gray-500').textContent.trim();
                } else if(selectedHorario.getAttribute('data-motivo')) {
                    motivo = selectedHorario.getAttribute('data-motivo');
                }
                document.getElementById('modal-motivo').value = motivo;
                document.getElementById('modal-descripcion').value = '';
                document.getElementById('modal-error-msg').classList.add('hidden');
                document.getElementById('modal-error-msg').innerHTML = '';
                document.getElementById('modal-cita').classList.remove('hidden');
            };
            document.getElementById('btn-agendar-cita').onclick = openModalCita;
            document.getElementById('close-modal-cita').onclick = function() {
                document.getElementById('modal-cita').classList.add('hidden');
            };
            // SUBMIT AJAX NUEVO MODAL
            const formCita = document.getElementById('form-cita');
            formCita.onsubmit = async function(e) {
                // Antes de enviar, asegúrate de que los campos hidden tengan el valor correcto (ya se llenaron al abrir el modal)
                // Validación frontend extra
                for (const [key, val] of new FormData(formCita).entries()) {
                    if (!val || val.toString().trim() === '') {
                        mostrarErrorModalNuevo('Por favor llena todos los campos.');
                        return;
                    }
                }
                const btn = document.getElementById('modal-submit-cita');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Agendando...';
                document.getElementById('modal-error-msg').classList.add('hidden');
                document.getElementById('modal-error-msg').innerHTML = '';
                const formData = new FormData(formCita);
                try {
                    const res = await fetch(formCita.action || window.location.pathname, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]')?.value,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    let data = null;
                    try {
                        data = await res.clone().json();
                    } catch (e) {
                        mostrarErrorModalNuevo('Respuesta inesperada del servidor.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Confirmar Cita';
                        return;
                    }
                    if (res.ok && data.success) {
                        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> ¡Cita agendada!';
                        btn.classList.remove('bg-cyan-500','hover:bg-cyan-600');
                        btn.classList.add('bg-green-500');
                        setTimeout(() => {
                            document.getElementById('modal-cita').classList.add('hidden');
                            window.location.reload();
                        }, 1200);
                    } else {
                        mostrarErrorModalNuevo(data?.message || 'Error al agendar cita.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Confirmar Cita';
                    }
                } catch (err) {
                    mostrarErrorModalNuevo('Error inesperado.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Confirmar Cita';
                }
            };
            function mostrarErrorModalNuevo(msg) {
                const div = document.getElementById('modal-error-msg');
                div.classList.remove('hidden');
                div.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow mb-4';
                div.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i> ' + msg;
            }
            
            // El manejo del botón de cancelar cita se ha movido al script al final del body
        });
    </script>
    
    <!-- Scripts adicionales -->
    <script>
        // Este script se ejecutará cuando el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Script principal cargado');
            
            // Obtener el botón de cancelar cita por su ID
            const btnCancelarCita = document.getElementById('btn-cancelar-cita');
            
            if (btnCancelarCita) {
                console.log('Botón de cancelar cita encontrado en script principal');
                
                // Añadir el evento click al botón
                btnCancelarCita.addEventListener('click', async function(event) {
                    // Prevenir comportamiento por defecto del botón
                    event.preventDefault();
                    console.log('Evento click activado en botón cancelar');
                    
                    try {
                        // Obtener el ID de la cita directamente del elemento de datos
                        const citaId = this.getAttribute('data-cita-id');
                        console.log('ID de la cita a cancelar:', citaId);
                        
                        if (!citaId) {
                            alert('No se encontró la cita para cancelar.');
                            return;
                        }
                        
                        // Confirmar la cancelación
                        if (!confirm('¿Estás seguro de que deseas cancelar esta cita?')) {
                            return;
                        }
                        
                        // Deshabilitar el botón y mostrar estado de carga
                        btnCancelarCita.disabled = true;
                        btnCancelarCita.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Cancelando...';
                        console.log('Botón deshabilitado, enviando solicitud...');
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        console.log('CSRF Token:', csrfToken);
                        
                        const response = await fetch(`/api/citas/cancelar/${citaId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            // Asegurarse de que la solicitud incluya el token CSRF
                            credentials: 'same-origin'
                        });
                        
                        console.log('Respuesta recibida, status:', response.status);
                        
                        let data;
                        try {
                            data = await response.json();
                            // Depuración de la respuesta
                            console.log('Respuesta del servidor:', data);
                            console.log('Status:', response.status);
                        } catch (jsonError) {
                            console.error('Error al procesar la respuesta JSON:', jsonError);
                            alert('Error al procesar la respuesta del servidor');
                            btnCancelarCita.disabled = false;
                            btnCancelarCita.innerHTML = '<i class="fa-solid fa-times-circle"></i> Cancelar Cita';
                            return;
                        }
                        
                        if (response.ok && data.success) {
                            console.log('Cita cancelada exitosamente');
                            btnCancelarCita.innerHTML = '<i class="fa-solid fa-check-circle"></i> Cita cancelada';
                            btnCancelarCita.classList.remove('bg-red-500', 'hover:bg-red-600');
                            btnCancelarCita.classList.add('bg-green-500');
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            console.error('Error en la respuesta:', data.message);
                            alert(data.message || 'Error al cancelar la cita.');
                            btnCancelarCita.disabled = false;
                            btnCancelarCita.innerHTML = '<i class="fa-solid fa-times-circle"></i> Cancelar Cita';
                        }
                    } catch (error) {
                        console.error('Error en la solicitud:', error);
                        alert('Ocurrió un error al cancelar la cita.');
                        btnCancelarCita.disabled = false;
                        btnCancelarCita.innerHTML = '<i class="fa-solid fa-times-circle"></i> Cancelar Cita';
                    }
                });
            } else {
                console.warn('No se encontró el botón de cancelar cita en el script principal');
            }
        });
    </script>
</body>
</html>