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
                <!-- Título y botón de nuevo horario -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Horario Semanal</h1>
                    <button 
                        @click="openCreateDrawer(1, '08:00')"
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2"
                    >
                        <i class="fas fa-plus"></i>
                        <span>Nuevo Horario</span>
                    </button>
                </div>

                <!-- Mensaje de estado -->
                <div 
                    x-show="message" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    :class="{
                        'bg-green-100 border-green-400 text-green-700': message?.type === 'success',
                        'bg-red-100 border-red-400 text-red-700': message?.type === 'error'
                    }"
                    class="border-l-4 p-4 mb-6"
                    @click="message = null"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i :class="{
                                'fas fa-check-circle': message?.type === 'success',
                                'fas fa-exclamation-circle': message?.type === 'error'
                            }"></i>
                        </div>
                        <div class="ml-3">
                            <p x-text="message?.text"></p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Tareas -->
                <div class="medical-card bg-white overflow-hidden">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Listado de Tareas</h2>
                        
                        <!-- Información de carga -->
                        <div x-show="loading" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <p>Cargando tareas...</p>
                        </div>
                        
                        <div x-show="!loading && events.length === 0" class="mb-4 p-3 bg-yellow-50 text-yellow-700 rounded">
                            <p>No se encontraron tareas. Puede crear una nueva tarea usando el botón "Nuevo Horario".</p>
                        </div>
                        
                        <!-- Diseño de Tarjetas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="event in events" :key="event.id">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-200 flex items-center space-x-3">
                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full" :style="`background-color: ${event.color || '#4A90E2'}`">
                                            <i :class="`text-white fas ${event.icono || 'fa-user-doctor'}`"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900" x-text="event.titulo"></h3>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-sm text-gray-500 mb-2"><i class="fas fa-calendar-day mr-2"></i> Día: <span x-text="getDayName(event.dia_semana)"></span></p>
                                        <p class="text-sm text-gray-500 mb-2"><i class="fas fa-clock mr-2"></i> Hora: <span x-text="event.hora_inicio"></span> - <span x-text="event.hora_fin"></span></p>
                                        <p class="text-sm text-gray-500 truncate"><i class="fas fa-file-alt mr-2"></i> Descripción: <span x-text="event.descripcion || 'Sin descripción'"></span></p>
                                    </div>
                                    <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-right">
                                        <button @click.stop="openViewDrawer(event)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-cyan-700 bg-cyan-100 hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                            <i class="fas fa-eye mr-2"></i> Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
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
                            console.log('Obteniendo tareas...');
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
                                    console.log('Tareas recibidas:', data);
                                    // Asegurarse de que data sea un array
                                    this.events = Array.isArray(data) ? data : [];
                                    this.events.sort((a, b) => a.id - b.id);
                                    // Mostrar mensaje si hay tareas
                                    if (this.events.length > 0) {
                                        this.message = {
                                            type: 'success',
                                            text: `Se han cargado ${this.events.length} tareas correctamente.`
                                        };
                                        // Ocultar el mensaje después de 3 segundos
                                        setTimeout(() => {
                                            this.message = null;
                                        }, 3000);
                                    }
                                    this.loading = false;
                                })
                                .catch(error => {
                                    console.error('Error al obtener tareas:', error);
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
                            // Convertir day a número para asegurar comparación correcta
                            const dayNumber = parseInt(day);
                            
                            // Si no hay eventos, devolver un array vacío
                            if (!this.events || !Array.isArray(this.events)) {
                                return [];
                            }
                            
                            return this.events.filter(event => {
                                // Verificar que el evento tenga todas las propiedades necesarias
                                if (!event || !event.dia_semana || !event.hora_inicio || !event.hora_fin) {
                                    return false;
                                }
                                
                                // Convertir dia_semana del evento a número para comparación
                                const eventDay = parseInt(event.dia_semana);
                                
                                // Verificar si el evento pertenece a este día y rango de tiempo
                                return eventDay === dayNumber && 
                                       this.isTimeInRange(time, event.hora_inicio, event.hora_fin);
                            });
                        },
                        
                        isTimeInRange(time, start, end) {
                            // Validar que todos los parámetros sean strings válidos
                            if (typeof time !== 'string' || typeof start !== 'string' || typeof end !== 'string') {
                                return false;
                            }
                            
                            // Asegurar que los formatos de hora sean consistentes
                            const timeStr = time.includes(':') ? time : `${time}:00`;
                            const startStr = start.includes(':') ? start : `${start}:00`;
                            const endStr = end.includes(':') ? end : `${end}:00`;
                            
                            try {
                                // Crear objetos Date para comparación
                                const timeDate = new Date(`2000-01-01T${timeStr}`);
                                const nextHourDate = new Date(`2000-01-01T${timeStr}`);
                                nextHourDate.setHours(nextHourDate.getHours() + 1);
                                
                                const startDate = new Date(`2000-01-01T${startStr}`);
                                const endDate = new Date(`2000-01-01T${endStr}`);
                                
                                return (startDate >= timeDate && startDate < nextHourDate) || // Comienza en esta hora
                                       (endDate > timeDate && endDate <= nextHourDate) || // Termina en esta hora
                                       (startDate <= timeDate && endDate >= nextHourDate); // Abarca toda la hora
                            } catch (error) {
                                console.error('Error al comparar horas:', error);
                                return false;
                            }
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
                            const method = 'POST'; // Siempre usamos POST
                            
                            // Preparar los datos a enviar
                            let eventData = {...this.newEvent};
                            
                            // Si estamos editando, añadir el campo _method para el spoofing de método HTTP
                            if (this.drawer.mode === 'edit') {
                                eventData._method = 'PUT';
                            }
                            
                            fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(eventData)
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