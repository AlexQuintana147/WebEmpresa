<div class="bg-gradient-to-br from-cyan-900 via-blue-800 to-teal-900 text-white w-64 min-h-screen py-8 px-6 flex flex-col shadow-2xl relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-cyan-500/10 to-teal-500/10 pointer-events-none"></div>
    <!-- Patrón médico decorativo -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-10 left-10 w-8 h-8 border-2 border-white rounded-full"></div>
        <div class="absolute top-40 right-10 w-6 h-12 border-2 border-white rounded-full"></div>
        <div class="absolute bottom-40 left-12 w-10 h-10 border-2 border-white rotate-45"></div>
        <div class="absolute bottom-20 right-12 w-8 h-8 border-2 border-white rounded-md"></div>
    </div>
    <div class="relative mb-10 text-center space-y-4">
        <!-- Logo container with subtle glow effect -->
        <div class="relative w-32 h-32 mx-auto">
            <div class="absolute inset-0 bg-cyan-500/30 rounded-full blur-xl animate-pulse-medical"></div>
            <img src="{{ asset('images/logo.png') }}" alt="Clínica Ricardo Palma Logo" 
                 class="relative w-full h-full object-contain drop-shadow-2xl transition-all duration-300 hover:scale-105"/>
        </div>

        <!-- Title with enhanced gradient and animation -->
        <div class="relative">
            <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 via-teal-100 to-blue-50 tracking-wider hover:from-cyan-100 hover:to-white transition-all duration-500">
                Clínica Ricardo Palma
            </h1>
            
            <!-- Decorative elements -->
            <div class="mt-4 flex items-center justify-center space-x-2">
                <div class="h-0.5 w-12 bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent"></div>
                <div class="w-2 h-2 rounded-full bg-cyan-400/50"></div>
                <div class="h-0.5 w-12 bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent"></div>
            </div>
        </div>
    </div>
    <nav class="relative flex-1 space-y-2">
        <a href="/" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('/') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/10 border-r-4 border-cyan-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400/20 to-cyan-500/20 group-hover:from-cyan-400/30 group-hover:to-cyan-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-hospital text-cyan-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-cyan-300 transition-colors duration-300">Sobre Nosotros</span>
        </a>
        <a href="/chat" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('chat') ? 'bg-gradient-to-r from-indigo-500/20 to-indigo-500/10 border-r-4 border-indigo-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-400/20 to-indigo-500/20 group-hover:from-indigo-400/30 group-hover:to-indigo-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-user-doctor text-indigo-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-indigo-300 transition-colors duration-300">Chat IA</span>
            <span class="ml-2 px-2.5 py-1 text-xs font-semibold bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 rounded-full shadow-lg animate-gradient-x">BETA</span>
        </a>
        {{-- 
        <a href="/instrucciones" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('instrucciones') ? 'bg-gradient-to-r from-rose-500/20 to-rose-500/10 border-r-4 border-rose-400' : '' }}">
            
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-rose-400/20 to-rose-500/20 group-hover:from-rose-400/30 group-hover:to-rose-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-notes-medical text-rose-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            
            <span class="font-medium tracking-wide group-hover:text-rose-300 transition-colors duration-300">Instrucciones</span>
        </a>--}}
        <!-- Secciones exclusivas para Gestión (rol_id = 4) -->
        @auth
            @if(Auth::user()->rol_id == 4)
            <!-- Separador con símbolo de gestión -->
            <div class="my-6 flex items-center justify-center">
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent"></div>
                <div class="mx-2 text-emerald-400/50"><i class="fas fa-chart-line text-sm"></i></div>
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent"></div>
            </div>
            
            <div class="relative group/management">
                <!-- Efecto de neón flotante -->
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-400/20 to-green-500/20 rounded-xl blur-sm opacity-0 group-hover/management:opacity-40 transition-opacity duration-300"></div>
                
                <!-- Contenedor principal del área de gestión -->
                <div class="relative bg-gradient-to-br from-emerald-900 to-green-900 rounded-xl p-1 shadow-2xl border border-emerald-700/50 hover:border-emerald-400/30 transition-all duration-300">
                    <!-- Cabecera destacada -->
                    <div class="flex items-center px-5 py-4">
                        <div class="mr-3 relative">
                            <i class="fas fa-briefcase text-emerald-400 text-xl"></i>
                            <div class="absolute -right-1 -bottom-1 w-2 h-2 bg-emerald-400 rounded-full animate-pulse-medical"></div>
                        </div>
                        <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 to-green-100">Área de Gestión</h2>
                    </div>
                    
                    <!-- Separador dinámico -->
                    <div class="h-px bg-gradient-to-r from-transparent via-emerald-400/20 to-transparent mb-4"></div>

                    <!-- Elementos de navegación para gestión -->
                    <div class="space-y-3 px-2 pb-2">
                        <!-- Gestión Financiera -->
                        <a href="/presupuesto" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-emerald-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-emerald-500/10 {{ request()->is('presupuesto') ? 'bg-gradient-to-r from-emerald-500/20 to-emerald-500/10 border-r-4 border-emerald-400' : '' }}">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400/20 to-emerald-500/20 group-hover:from-emerald-400/30 group-hover:to-emerald-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-file-invoice-dollar text-emerald-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-emerald-300 transition-colors duration-300">Gestión Financiera</span>
                                <p class="text-xs text-gray-400 mt-1">Presupuestos y finanzas</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-emerald-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Reportes Administrativos -->
                        <a href="/reportes" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-emerald-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-green-500/10">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-green-400/20 to-green-500/20 group-hover:from-green-400/30 group-hover:to-green-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-chart-pie text-green-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-green-300 transition-colors duration-300">Reportes</span>
                                <p class="text-xs text-gray-400 mt-1">Análisis y estadísticas</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-green-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Administración de Recursos -->
                        <a href="/recursos" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-emerald-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-teal-500/10">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-teal-400/20 to-teal-500/20 group-hover:from-teal-400/30 group-hover:to-teal-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-boxes-stacked text-teal-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-teal-300 transition-colors duration-300">Recursos</span>
                                <p class="text-xs text-gray-400 mt-1">Gestión de inventario</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-teal-300 transition-colors duration-300"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        @endauth
        
        <!-- Secciones exclusivas para doctores (rol_id = 3) -->
        @auth
            @if(Auth::user()->rol_id == 3)
            <!-- Separador con símbolo médico -->
            <div class="my-6 flex items-center justify-center">
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-indigo-400/30 to-transparent"></div>
                <div class="mx-2 text-indigo-400/50"><i class="fas fa-staff-aesculapius text-sm"></i></div>
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-indigo-400/30 to-transparent"></div>
            </div>
            
            <div class="relative group/doctor">
                <!-- Efecto de neón flotante -->
                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-400/20 to-purple-500/20 rounded-xl blur-sm opacity-0 group-hover/doctor:opacity-40 transition-opacity duration-300"></div>
                
                <!-- Contenedor principal del área médica -->
                <div class="relative bg-gradient-to-br from-indigo-900 to-purple-900 rounded-xl p-1 shadow-2xl border border-indigo-700/50 hover:border-indigo-400/30 transition-all duration-300">
                    <!-- Cabecera destacada -->
                    <div class="flex items-center px-5 py-4">
                        <div class="mr-3 relative">
                            <i class="fas fa-user-md text-indigo-400 text-xl"></i>
                            <div class="absolute -right-1 -bottom-1 w-2 h-2 bg-indigo-400 rounded-full animate-pulse-medical"></div>
                        </div>
                        <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-purple-100">Área Médica</h2>
                    </div>
                    
                    <!-- Separador dinámico -->
                    <div class="h-px bg-gradient-to-r from-transparent via-indigo-400/20 to-transparent mb-4"></div>

                    <!-- Elementos de navegación para médicos -->
                    <div class="space-y-3 px-2 pb-2">
                        <!-- Gestión de Pacientes -->
                        <a href="/actividades" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-indigo-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-amber-500/10 {{ request()->is('actividades') ? 'bg-gradient-to-r from-amber-500/20 to-amber-500/10 border-r-4 border-amber-400' : '' }}">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400/20 to-amber-500/20 group-hover:from-amber-400/30 group-hover:to-amber-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-clipboard-list text-amber-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-amber-300 transition-colors duration-300">Gestión de Pacientes</span>
                                <p class="text-xs text-gray-400 mt-1">Administración de casos</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-amber-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Tareas -->
                        <a href="/tareas" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-indigo-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-fuchsia-500/10 {{ request()->is('calendario') ? 'bg-gradient-to-r from-fuchsia-500/20 to-fuchsia-500/10 border-r-4 border-fuchsia-400' : '' }}">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-fuchsia-400/20 to-fuchsia-500/20 group-hover:from-fuchsia-400/30 group-hover:to-fuchsia-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-calendar-plus text-fuchsia-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-fuchsia-300 transition-colors duration-300">Horarios Semanales</span>
                                <p class="text-xs text-gray-400 mt-1">Agenda de citas</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-fuchsia-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Notas -->
                        <a href="/notas" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-indigo-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-teal-500/10 {{ request()->is('notas') ? 'bg-gradient-to-r from-teal-500/20 to-teal-500/10 border-r-4 border-teal-400' : '' }}">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-teal-400/20 to-teal-500/20 group-hover:from-teal-400/30 group-hover:to-teal-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-stethoscope text-teal-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-teal-300 transition-colors duration-300">Notas</span>
                                <p class="text-xs text-gray-400 mt-1">Registros clínicos</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-teal-300 transition-colors duration-300"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        @endauth

        <!-- Sección diferenciada para Pacientes (solo visible para pacientes) -->
        @auth
            @if(Auth::user()->rol_id == 2)
            <!-- Separador con símbolo médico -->
            <div class="my-6 flex items-center justify-center">
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-cyan-400/30 to-transparent"></div>
                <div class="mx-2 text-cyan-400/50"><i class="fas fa-staff-snake text-sm"></i></div>
                <div class="h-px w-12 bg-gradient-to-r from-transparent via-cyan-400/30 to-transparent"></div>
            </div>
            
            <div class="relative group/patient">
                <!-- Efecto de neón flotante -->
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400/20 to-teal-500/20 rounded-xl blur-sm opacity-0 group-hover/patient:opacity-40 transition-opacity duration-300"></div>
                
                <!-- Contenedor principal del área de pacientes -->
                <div class="relative bg-gradient-to-br from-blue-900 to-teal-900 rounded-xl p-1 shadow-2xl border border-cyan-700/50 hover:border-cyan-400/30 transition-all duration-300">
                    <!-- Cabecera destacada -->
                    <div class="flex items-center px-5 py-4">
                        <div class="mr-3 relative">
                            <i class="fas fa-heartbeat text-cyan-400 text-xl"></i>
                            <div class="absolute -right-1 -bottom-1 w-2 h-2 bg-cyan-400 rounded-full animate-pulse-medical"></div>
                        </div>
                        <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-teal-100">Área de Pacientes</h2>
                    </div>
                    
                    <!-- Separador dinámico -->
                    <div class="h-px bg-gradient-to-r from-transparent via-cyan-400/20 to-transparent mb-4"></div>

                    <!-- Elementos de navegación para pacientes -->
                    <div class="space-y-3 px-2 pb-2">
                        <!-- Historial Médico -->
                        <a href="/historial" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-cyan-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-cyan-500/10">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400/20 to-cyan-500/20 group-hover:from-cyan-400/30 group-hover:to-cyan-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-file-medical text-cyan-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-cyan-300 transition-colors duration-300">Historial Médico</span>
                                <p class="text-xs text-gray-400 mt-1">Registros completos</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-cyan-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Atención Médica -->
                        <a href="/atencionmedica" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-blue-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-blue-500/10">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400/20 to-blue-500/20 group-hover:from-blue-400/30 group-hover:to-blue-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-user-nurse text-blue-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-blue-300 transition-colors duration-300">Atención Médica</span>
                                <p class="text-xs text-gray-400 mt-1">Consulta especializada</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-blue-300 transition-colors duration-300"></i>
                        </a>

                        <!-- Atención Directa -->
                        <a href="/atenciondirecta" class="group flex items-center p-3 rounded-lg transition-all duration-300 hover:bg-purple-900/20 hover:translate-x-2 hover:shadow-lg hover:shadow-purple-500/10">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-purple-400/20 to-purple-500/20 group-hover:from-purple-400/30 group-hover:to-purple-500/30 transition-all duration-300 mr-3">
                                <i class="fas fa-comment-medical text-teal-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium tracking-wide group-hover:text-teal-300 transition-colors duration-300">Atención Directa</span>
                                <p class="text-xs text-gray-400 mt-1">Comunicación inmediata</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-sm ml-2 group-hover:text-teal-300 transition-colors duration-300"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        @endauth
    </nav>
    <!-- Indicador de estado del sistema médico -->
    <div class="mt-auto pt-6 border-t border-cyan-700/30">
        <div class="flex items-center px-5 py-3 space-x-3 text-sm">
            @auth
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-green-400/10 to-green-500/10">
                    <i class="fas fa-heartbeat text-green-400 text-lg animate-pulse-medical"></i>
                </div>
                <span class="font-medium tracking-wide text-green-400">Sistema Activo</span>
            @else
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-red-400/10 to-red-500/10">
                    <i class="fas fa-heart-crack text-red-400 text-lg"></i>
                </div>
                <span class="font-medium tracking-wide text-gray-400">Sistema Inactivo</span>
            @endauth
        </div>
    </div>
</div>