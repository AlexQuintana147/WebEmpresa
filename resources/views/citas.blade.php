<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agendar Cita - Clínica Ricardo Palma</title>
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Información del Paciente -->
                @if(auth()->check() && auth()->user()->paciente)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Información del Paciente</h2>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <p class="text-gray-600">Nombre completo: <span class="font-medium text-gray-800">{{ auth()->user()->paciente->nombre }} {{ auth()->user()->paciente->apellido }}</span></p>
                                <p class="text-gray-600">DNI: <span class="font-medium text-gray-800">{{ auth()->user()->paciente->dni }}</span></p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-6">
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Agendar Nueva Cita</h2>
                    <form action="{{ route('citas.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Selector de Categoría de Doctor -->
                        <div class="space-y-2">
                            <label for="categoria" class="block text-sm font-medium text-gray-700">Especialidad Médica</label>
                            <select name="categoria" id="categoria" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Seleccione una especialidad</option>
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad }}">{{ $especialidad }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selector de Doctor -->
                        <div class="space-y-2">
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Primero seleccione una especialidad</option>
                            </select>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" name="fecha" id="fecha" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="space-y-2">
                                <label for="hora" class="block text-sm font-medium text-gray-700">Hora</label>
                                <input type="time" name="hora" id="hora" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Motivo de la Cita -->
                        <div class="space-y-2">
                            <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Cita</label>
                            <textarea name="motivo" id="motivo" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Describa brevemente el motivo de su cita"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
            
            // Limpiar opciones actuales
            doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
            
            if (categoria) {
                // Hacer la petición AJAX para obtener los doctores de la categoría seleccionada
                fetch(`/api/doctores/${categoria}`)
                    .then(response => response.json())
                    .then(doctores => {
                        doctorSelect.innerHTML = '<option value="">Seleccione un doctor</option>';
                        doctores.forEach(doctor => {
                            doctorSelect.innerHTML += `<option value="${doctor.id}">${doctor.nombre} ${doctor.apellido}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        doctorSelect.innerHTML = '<option value="">Error al cargar doctores</option>';
                    });
            } else {
                doctorSelect.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
            }
        });
    </script>
    
</body>
</html>