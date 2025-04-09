<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendario Médico - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        
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
        .calendar-grid {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            gap: 1px;
        }
        .calendar-header {
            background-color: #f0f9ff;
            font-weight: 600;
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #e0f2fe;
        }
        .time-slot {
            padding: 10px 5px;
            text-align: center;
            border-right: 1px solid #e0f2fe;
            background-color: #f0f9ff;
            font-weight: 500;
        }
        .calendar-cell {
            min-height: 60px;
            border: 1px solid #e0f2fe;
            padding: 5px;
            position: relative;
            transition: all 0.2s ease;
        }
        .calendar-cell:hover {
            background-color: #f0f9ff;
        }
        .event {
            padding: 5px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 2px;
            cursor: pointer;
            color: white;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .event:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
            z-index: 50;
            overflow-y: auto;
        }
        .drawer.open {
            right: 0;
        }
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }
    </style>
    <script src="{{ asset('js/calendario.js') }}" defer></script>
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="calendario">
                <script type="text/javascript">
                document.addEventListener('alpine:init', () => {
                    Alpine.data('calendario', () => ({
                        drawer: {
                            open: false,
                            mode: 'create', // 'create', 'edit', 'view'
                            currentEvent: null
                        },
                        days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                        timeSlots: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'],
                        events: [],
                        newEvent: {
                            titulo: '',
                            descripcion: '',
                            dia_semana: 1,
                            hora_inicio: '',
                            hora_fin: '',
                            color: '#4A90E2',
                            icono: 'fa-user-doctor'
                        },
                        iconOptions: [
                            { value: 'fa-user-doctor', label: 'Doctor' },
                            { value: 'fa-stethoscope', label: 'Estetoscopio' },
                            { value: 'fa-hospital', label: 'Hospital' },
                            { value: 'fa-pills', label: 'Medicamentos' },
                            { value: 'fa-syringe', label: 'Jeringa' },
                            { value: 'fa-notes-medical', label: 'Notas Médicas' },
                            { value: 'fa-procedures', label: 'Procedimientos' },
                            { value: 'fa-briefcase-medical', label: 'Maletín Médico' }
                        ],
                        colorOptions: [
                            '#4A90E2', '#50C878', '#FF6B6B', '#FFD700', '#9370DB', '#FF8C00', '#20B2AA', '#FF69B4'
                        ],
                        errors: {},
                        loading: false,
                        message: null,
                        
                        init() {
                            this.fetchEvents();
                            
                            // Escuchar eventos personalizados
                            this.$watch('drawer.open', value => {
                                if (!value) {
                                    this.resetForm();
                                }
                            });
                        },
                        
                        fetchEvents() {
                            this.loading = true;
                            fetch('/tareas-json')
                                .then(response => {
                                    // Verificar si la respuesta es exitosa
                                    if (!response.ok) {
                                        // Si el estado es 401, probablemente la sesión expiró
                                        if (response.status === 401) {
                                            return response.json().then(data => {
                                                if (data.session_expired) {
                                                    this.message = {
                                                        type: 'error',
                                                        text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
                                                    };
                                                    // Redirigir al login después de 2 segundos
                                                    setTimeout(() => {
                                                        window.location.href = '/';
                                                    }, 2000);
                                                    throw new Error('Sesión expirada');
                                                }
                                            });
                                        }
                                        throw new Error(`Error de red: ${response.status}`);
                                    }
                                    // Verificar el tipo de contenido para asegurarse de que es JSON
                                    const contentType = response.headers.get('content-type');
                                 if (!contentType || !contentType.includes('application/json')) {
                                     // Intentar obtener el texto de la respuesta para diagnóstico
                                     return response.text().then(text => {
                                         console.error('Respuesta no JSON recibida:', text.substring(0, 150) + '...');
                                         throw new Error('La respuesta no es JSON válido. Posiblemente la sesión ha expirado.');
                                     });
                                 }
                                 return response.json();
                             })
                             .then(data => {
                                 this.events = data;
                                 this.loading = false;
                             })
                             .catch(error => {
                                 console.error('Error:', error);
                                 // Si no es un error de sesión expirada, mostrar mensaje genérico
                                 if (!this.message || this.message.type !== 'error') {
                                     this.message = {
                                         type: 'error',
                                         text: 'Ha ocurrido un error en el servidor. Por favor, recargue la página o inicie sesión nuevamente.'
                                     };
                                 }
                                 this.loading = false;
                             });
                         },
                         
                         getEventsForCell(day, time) {
                             return this.events.filter(event => {
                                 return event.dia_semana === day && 
                                        this.isTimeInRange(time, event.hora_inicio, event.hora_fin);
                             });
                         },
                         
                         isTimeInRange(time, start, end) {
                             const timeDate = new Date(`2000-01-01T${time}:00`);
                             const startDate = new Date(`2000-01-01T${start}`);
                             const endDate = new Date(`2000-01-01T${end}`);
                             
                             return timeDate >= startDate && timeDate < endDate;
                         },
                         
                         getDayName(day) {
                             return this.days[day - 1];
                         },
                         
                         openCreateDrawer(day, time) {
                             this.drawer.mode = 'create';
                             this.drawer.currentEvent = null;
                             this.newEvent = {
                                 titulo: '',
                                 descripcion: '',
                                 dia_semana: day,
                                 hora_inicio: time,
                                 hora_fin: this.getNextHour(time),
                                 color: '#4A90E2',
                                 icono: 'fa-user-doctor'
                             };
                             this.drawer.open = true;
                         },
                         
                         openViewDrawer(event) {
                             this.drawer.mode = 'view';
                             this.drawer.currentEvent = event;
                             this.newEvent = { ...event };
                             this.drawer.open = true;
                         },
                         
                         closeDrawer() {
                             this.drawer.open = false;
                         },
                         
                         getNextHour(time) {
                             const [hours, minutes] = time.split(':').map(Number);
                             let nextHour = hours + 1;
                             if (nextHour > 23) nextHour = 23;
                             return `${nextHour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                         },
                         
                         resetForm() {
                             this.newEvent = {
                                 titulo: '',
                                 descripcion: '',
                                 dia_semana: 1,
                                 hora_inicio: '',
                                 hora_fin: '',
                                 color: '#4A90E2',
                                 icono: 'fa-user-doctor'
                             };
                             this.errors = {};
                         },
                         
                         saveEvent() {
                             // Validación básica
                             this.errors = {};
                             
                             if (!this.newEvent.titulo) {
                                 this.errors.titulo = 'El título es obligatorio';
                             }
                             
                             if (!this.newEvent.hora_inicio) {
                                 this.errors.hora_inicio = 'La hora de inicio es obligatoria';
                             }
                             
                             if (!this.newEvent.hora_fin) {
                                 this.errors.hora_fin = 'La hora de fin es obligatoria';
                             } else if (this.newEvent.hora_inicio >= this.newEvent.hora_fin) {
                                 this.errors.hora_fin = 'La hora de fin debe ser posterior a la hora de inicio';
                             }
                             
                             if (Object.keys(this.errors).length > 0) {
                                 return;
                             }
                             
                             this.loading = true;
                             
                             const url = this.drawer.mode === 'edit' ? `/tareas/${this.newEvent.id}` : '/tareas';
                             const method = this.drawer.mode === 'edit' ? 'PUT' : 'POST';
                             
                             fetch(url, {
                                 method: method,
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                 },
                                 body: JSON.stringify(this.newEvent)
                             })
                             .then(response => {
                                 // Verificar si la respuesta es exitosa
                                 if (!response.ok) {
                                     // Si el estado es 401, probablemente la sesión expiró
                                     if (response.status === 401) {
                                         throw new Error('Sesión expirada');
                                     }
                                     throw new Error(`Error de red: ${response.status}`);
                                 }
                                 // Verificar el tipo de contenido para asegurarse de que es JSON
                                 const contentType = response.headers.get('content-type');
                                 if (!contentType || !contentType.includes('application/json')) {
                                     // Intentar obtener el texto de la respuesta para diagnóstico
                                     return response.text().then(text => {
                                         console.error('Respuesta no JSON recibida:', text.substring(0, 150) + '...');
                                         throw new Error('La respuesta no es JSON válido. Posiblemente la sesión ha expirado.');
                                     });
                                 }
                                 return response.json();
                             })
                             .then(data => {
                                 if (data.success) {
                                     this.message = {
                                         type: 'success',
                                         text: data.message || 'Horario guardado correctamente'
                                     };
                                     this.fetchEvents();
                                     this.closeDrawer();
                                 } else {
                                     this.message = {
                                         type: 'error',
                                         text: data.message || 'Error al guardar el horario'
                                     };
                                     if (data.errors) {
                                         this.errors = data.errors;
                                     }
                                 }
                                 this.loading = false;
                             })
                             .catch(error => {
                                 console.error('Error:', error);
                                 // Si no es un error de sesión expirada, mostrar mensaje genérico
                                 if (error.message === 'Sesión expirada') {
                                     this.message = {
                                         type: 'error',
                                         text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
                                     };
                                     // Redirigir al login después de 2 segundos
                                     setTimeout(() => {
                                         window.location.href = '/';
                                     }, 2000);
                                 } else {
                                     this.message = {
                                         type: 'error',
                                         text: 'Ha ocurrido un error en el servidor. Por favor, recargue la página o inicie sesión nuevamente.'
                                     };
                                 }
                                 this.loading = false;
                             });
                         },
                         
                         deleteEvent() {
                             if (!confirm('¿Está seguro de eliminar este horario?')) {
                                 return;
                             }
                             
                             this.loading = true;
                             
                             fetch(`/tareas/${this.drawer.currentEvent.id}`, {
                                 method: 'DELETE',
                                 headers: {
                                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                 }
                             })
                             .then(response => {
                                 // Verificar si la respuesta es exitosa
                                 if (!response.ok) {
                                     // Si el estado es 401, probablemente la sesión expiró
                                     if (response.status === 401) {
                                         throw new Error('Sesión expirada');
                                     }
                                     throw new Error(`Error de red: ${response.status}`);
                                 }
                                 // Verificar el tipo de contenido para asegurarse de que es JSON
                                 const contentType = response.headers.get('content-type');
                                 if (!contentType || !contentType.includes('application/json')) {
                                     // Intentar obtener el texto de la respuesta para diagnóstico
                                     return response.text().then(text => {
                                         console.error('Respuesta no JSON recibida:', text.substring(0, 150) + '...');
                                         throw new Error('La respuesta no es JSON válido. Posiblemente la sesión ha expirado.');
                                     });
                                 }
                                 return response.json();
                             })
                             .then(data => {
                                 if (data.success) {
                                     this.message = {
                                         type: 'success',
                                         text: data.message || 'Horario eliminado correctamente'
                                     };
                                     this.fetchEvents();
                                     this.closeDrawer();
                                 } else {
                                     this.message = {
                                         type: 'error',
                                         text: data.message || 'Error al eliminar el horario'
                                     };
                                 }
                                 this.loading = false;
                             })
                             .catch(error => {
                                 console.error('Error:', error);
                                 // Si no es un error de sesión expirada, mostrar mensaje genérico
                                 if (error.message === 'Sesión expirada') {
                                     this.message = {
                                         type: 'error',
                                         text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
                                     };
                                     // Redirigir al login después de 2 segundos
                                     setTimeout(() => {
                                         window.location.href = '/';
                                     }, 2000);
                                 } else {
                                     this.message = {
                                         type: 'error',
                                         text: 'Ha ocurrido un error en el servidor. Por favor, recargue la página o inicie sesión nuevamente.'
                                     };
                                 }
                                 this.loading = false;
                             });
                         }
                     }));
                 });
                 </script>
                                    
                <!-- Título de la página con decoración médica -->
                <div class="mb-10 text-center relative">
                    <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                        <div class="absolute top-10 left-10 w-8 h-8 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 0s;"></div>
                        <div class="absolute bottom-5 right-10 w-6 h-6 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 0.5s;"></div>
                        <div class="absolute top-5 right-20 w-10 h-10 border-2 border-cyan-200 rounded-full opacity-20 animate-float-medical" style="animation-delay: 1s;"></div>
                    </div>
                    <h1 class="text-3xl font-bold text-cyan-800 relative z-10">
                        <i class="fas fa-calendar-alt mr-2 text-cyan-600"></i> Calendario Médico
                    </h1>
                    <p class="text-cyan-600 mt-2">Gestione sus horarios de atención semanal</p>
                </div>
                
                <!-- Tarjeta del Calendario -->
                <div class="medical-card bg-white p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-cyan-800">
                            <i class="fas fa-calendar-week mr-2 text-cyan-600"></i> Horario Semanal
                        </h2>
                        <button @click="openCreateDrawer(1, '08:00')" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg shadow transition-all duration-300 flex items-center">
                            <i class="fas fa-plus mr-2"></i> Nuevo Horario
                        </button>
                    </div>
                    
                    <!-- Calendario -->
                    <div class="overflow-x-auto">
                        <div class="calendar-grid min-w-[900px]">
                            <!-- Encabezados -->
                            <div class="calendar-header">Hora</div>
                            <template x-for="day in [1, 2, 3, 4, 5, 6, 7]" :key="day">
                                <div class="calendar-header" x-text="getDayName(day)"></div>
                            </template>
                            
                            <!-- Filas de horarios -->
                            <template x-for="time in timeSlots" :key="time">
                                <template>
                                    <!-- Hora -->
                                    <div class="time-slot" x-text="time"></div>
                                    
                                    <!-- Celdas para cada día -->
                                    <template x-for="day in [1, 2, 3, 4, 5, 6, 7]" :key="day">
                                        <div class="calendar-cell" @click="openCreateDrawer(day, time)">
                                            <template x-for="event in getEventsForCell(day, time)" :key="event.id">
                                                <div 
                                                    class="event" 
                                                    :style="`background-color: ${event.color}`"
                                                    @click.stop="openViewDrawer(event)"
                                                >
                                                    <div class="flex items-center">
                                                        <i :class="`fas ${event.icono} mr-1`"></i>
                                                        <span x-text="event.titulo"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Drawer para crear/editar/ver eventos -->
                <div class="drawer-overlay" :class="{'open': drawer.open}" @click="closeDrawer"></div>
                <div class="drawer" :class="{'open': drawer.open}">
                    <div class="p-6">
                        <!-- Encabezado del drawer -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-cyan-800">
                                <template x-if="drawer.mode === 'create'">
                                    <span><i class="fas fa-plus-circle mr-2 text-cyan-600"></i> Nuevo Horario</span>
                                </template>
                                <template x-if="drawer.mode === 'edit'">
                                    <span><i class="fas fa-edit mr-2 text-cyan-600"></i> Editar Horario</span>
                                </template>
                                <template x-if="drawer.mode === 'view'">
                                    <span><i class="fas fa-eye mr-2 text-cyan-600"></i> Detalles del Horario</span>
                                </template>
                            </h3>
                            <button @click="closeDrawer" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        
                        <!-- Mensaje de éxito/error -->
                        <template x-if="message">
                            <div :class="{
                                'p-4 mb-4 rounded-lg': true,
                                'bg-green-100 text-green-800 border border-green-200': message.type === 'success',
                                'bg-red-100 text-red-800 border border-red-200': message.type === 'error'
                            }">
                                <div class="flex items-center">
                                    <i :class="{
                                        'fas mr-2': true,
                                        'fa-check-circle': message.type === 'success',
                                        'fa-exclamation-circle': message.type === 'error'
                                    }"></i>
                                    <span x-text="message.text"></span>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Formulario o detalles -->
                        <template x-if="drawer.mode === 'view'">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" :style="`background-color: ${drawer.currentEvent.color}`">
                                        <i :class="`fas ${drawer.currentEvent.icono} text-white`"></i>
                                    </div>
                                    <h4 class="text-lg font-semibold" x-text="drawer.currentEvent.titulo"></h4>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Día</p>
                                            <p class="font-medium" x-text="getDayName(drawer.currentEvent.dia_semana)"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Horario</p>
                                            <p class="font-medium" x-text="`${drawer.currentEvent.hora_inicio} - ${drawer.currentEvent.hora_fin}`"></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Descripción</p>
                                    <p x-text="drawer.currentEvent.descripcion || 'Sin descripción'"></p>
                                </div>
                                
                                <div class="flex space-x-3 mt-6">
                                    <button @click="drawer.mode = 'edit'" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-300">
                                        <i class="fas fa-edit mr-2"></i> Editar
                                    </button>
                                    <button @click="deleteEvent" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors duration-300">
                                        <i class="fas fa-trash-alt mr-2"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="drawer.mode === 'create' || drawer.mode === 'edit'">
                            <form @submit.prevent="saveEvent" class="space-y-4">
                                <!-- Título -->
                                <div>
                                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                    <input 
                                        type="text" 
                                        id="titulo" 
                                        x-model="newEvent.titulo" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                        placeholder="Título del horario"
                                    >
                                    <p x-show="errors.titulo" x-text="errors.titulo" class="mt-1 text-sm text-red-600"></p>
                                </div>
                                
                                <!-- Descripción -->
                                <div>
                                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                    <textarea 
                                        id="descripcion" 
                                        x-model="newEvent.descripcion" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                        rows="3"
                                        placeholder="Descripción opcional"
                                    ></textarea>
                                </div>
                                
                                <!-- Día de la semana -->
                                <div>
                                    <label for="dia_semana" class="block text-sm font-medium text-gray-700 mb-1">Día de la semana</label>
                                    <select 
                                        id="dia_semana" 
                                        x-model.number="newEvent.dia_semana" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                    >
                                        <template x-for="(day, index) in days" :key="index">
                                            <option :value="index + 1" x-text="day"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <!-- Horario -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-1">Hora de inicio</label>
                                        <input 
                                            type="time" 
                                            id="hora_inicio" 
                                            x-model="newEvent.hora_inicio" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                        >
                                        <p x-show="errors.hora_inicio" x-text="errors.hora_inicio" class="mt-1 text-sm text-red-600"></p>
                                    </div>
                                    <div>
                                        <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-1">Hora de fin</label>
                                        <input 
                                            type="time" 
                                            id="hora_fin" 
                                            x-model="newEvent.hora_fin" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                        >
                                        <p x-show="errors.hora_fin" x-text="errors.hora_fin" class="mt-1 text-sm text-red-600"></p>
                                    </div>
                                </div>
                                
                                <!-- Color e Icono -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="color in colorOptions" :key="color">
                                                <button 
                                                    type="button"
                                                    @click="newEvent.color = color"
                                                    :class="{
                                                        'w-8 h-8 rounded-full border-2 transition-all duration-200': true,
                                                        'border-gray-300': newEvent.color !== color,
                                                        'border-white ring-2 ring-cyan-500 transform scale-110': newEvent.color === color
                                                    }"
                                                    :style="`background-color: ${color}`"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="icon in iconOptions" :key="icon.value">
                                                <button 
                                                    type="button"
                                                    @click="newEvent.icono = icon.value"
                                                    :class="{
                                                        'w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200': true,
                                                        'bg-gray-200 text-gray-700': newEvent.icono !== icon.value,
                                                        'bg-cyan-500 text-white transform scale-110': newEvent.icono === icon.value
                                                    }"
                                                    :title="icon.label"
                                                >
                                                    <i :class="`fas ${icon.value}`"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex space-x-3 mt-6">
                                    <button 
                                        type="button"
                                        @click="closeDrawer" 
                                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg transition-colors duration-300"
                                    >
                                        <i class="fas fa-times mr-2"></i> Cancelar
                                    </button>
                                    <button 
                                        type="submit" 
                                        class="flex-1 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center"
                                        :disabled="loading"
                                    >
                                        <template x-if="loading">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                        </template>
                                        <template x-if="!loading">
                                            <i class="fas fa-save mr-2"></i>
                                        </template>
                                        <span x-text="drawer.mode === 'edit' ? 'Actualizar' : 'Guardar'"></span>
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>