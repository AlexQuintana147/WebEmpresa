<div class="bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white w-64 min-h-screen py-8 px-6 flex flex-col shadow-2xl relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-purple-500/5 pointer-events-none"></div>
    <div class="relative mb-10 text-center">
        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-100 to-blue-50 tracking-wide">Administración</h1>
        <div class="mt-3 h-0.5 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent opacity-30"></div>
    </div>
    <nav class="relative flex-1 space-y-2">
        <a href="/" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('/') ? 'bg-gradient-to-r from-cyan-500/20 to-cyan-500/10 border-r-4 border-cyan-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400/20 to-cyan-500/20 group-hover:from-cyan-400/30 group-hover:to-cyan-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-chart-line text-cyan-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-cyan-300 transition-colors duration-300">Inicio</span>
        </a>
        <a href="/chat" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('chat') ? 'bg-gradient-to-r from-indigo-500/20 to-indigo-500/10 border-r-4 border-indigo-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-400/20 to-indigo-500/20 group-hover:from-indigo-400/30 group-hover:to-indigo-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-brain text-indigo-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-indigo-300 transition-colors duration-300">Chat IA</span>
            <span class="ml-2 px-2.5 py-1 text-xs font-semibold bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 rounded-full shadow-lg animate-gradient-x">BETA</span>
        </a>
        <a href="/instrucciones" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('instrucciones') ? 'bg-gradient-to-r from-rose-500/20 to-rose-500/10 border-r-4 border-rose-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-rose-400/20 to-rose-500/20 group-hover:from-rose-400/30 group-hover:to-rose-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-book-open text-rose-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-rose-300 transition-colors duration-300">Instrucciones</span>
        </a>
        <a href="/presupuesto" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('presupuesto') ? 'bg-gradient-to-r from-emerald-500/20 to-emerald-500/10 border-r-4 border-emerald-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400/20 to-emerald-500/20 group-hover:from-emerald-400/30 group-hover:to-emerald-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-wallet text-emerald-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-emerald-300 transition-colors duration-300">Presupuesto Mensual</span>
        </a>
        <a href="/lista-de-actividades" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('lista-de-actividades') ? 'bg-gradient-to-r from-amber-500/20 to-amber-500/10 border-r-4 border-amber-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400/20 to-amber-500/20 group-hover:from-amber-400/30 group-hover:to-amber-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-tasks text-amber-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-amber-300 transition-colors duration-300">Lista de Actividades</span>
        </a>
        <a href="/calendario" class="group flex items-center py-3.5 px-5 rounded-xl transition-all duration-300 hover:bg-white/10 hover:translate-x-1 {{ request()->is('calendario') ? 'bg-gradient-to-r from-fuchsia-500/20 to-fuchsia-500/10 border-r-4 border-fuchsia-400' : '' }}">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-fuchsia-400/20 to-fuchsia-500/20 group-hover:from-fuchsia-400/30 group-hover:to-fuchsia-500/30 transition-all duration-300 mr-3">
                <i class="fas fa-calendar-alt text-fuchsia-400 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
            <span class="font-medium tracking-wide group-hover:text-fuchsia-300 transition-colors duration-300">Calendario</span>
        </a>
    </nav>
    <div class="mt-auto pt-6 border-t border-gray-700/30">
        <div class="flex items-center px-5 py-3 space-x-3 text-sm text-gray-400">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-red-400/10 to-red-500/10">
                <i class="fas fa-power-off text-red-400 text-lg"></i>
            </div>
            <span class="font-medium tracking-wide">Offline</span>
        </div>
    </div>
</div>