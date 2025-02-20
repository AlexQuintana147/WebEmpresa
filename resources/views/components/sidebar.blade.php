<div class="bg-gradient-to-b from-gray-900 to-black text-white w-64 min-h-screen py-6 px-4 flex flex-col shadow-xl">
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-white tracking-wide">Administración</h1>
        <div class="mt-2 h-0.5 bg-gradient-to-r from-transparent via-gray-500 to-transparent opacity-30"></div>
    </div>
    <nav class="flex-1 space-y-2">
        <a href="/dashboard" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('dashboard') ? 'bg-white/15 border-r-4 border-cyan-500' : '' }}">
            <i class="fas fa-tachometer-alt text-cyan-400 mr-3"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        <a href="/chat" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('chat') ? 'bg-white/15 border-r-4 border-indigo-500' : '' }}">
            <i class="fas fa-robot text-indigo-400 mr-3"></i>
            <span class="font-medium">Chat IA</span> 
            <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 rounded-full animate-gradient-x">BETA</span>
        </a>
        <a href="/users" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('users') ? 'bg-white/15 border-r-4 border-emerald-500' : '' }}">
            <i class="fas fa-users text-emerald-400 mr-3"></i>
            <span class="font-medium">Usuarios</span>
        </a>
        <a href="/products" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('products') ? 'bg-white/15 border-r-4 border-amber-500' : '' }}">
            <i class="fas fa-box text-amber-400 mr-3"></i>
            <span class="font-medium">Productos</span>
        </a>
        <a href="/sales" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('sales') ? 'bg-white/15 border-r-4 border-fuchsia-500' : '' }}">
            <i class="fas fa-shopping-cart text-fuchsia-400 mr-3"></i>
            <span class="font-medium">Ventas</span>
        </a>
        <a href="/settings" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-1 {{ request()->is('settings') ? 'bg-white/15 border-r-4 border-rose-500' : '' }}">
            <i class="fas fa-cog text-rose-400 mr-3"></i>
            <span class="font-medium">Configuración</span>
        </a>
    </nav>
    <div class="mt-auto pt-4 border-t border-gray-700/50">
        <div class="flex items-center px-4 py-2 space-x-3 text-sm text-gray-400">
            <i class="fas fa-circle text-emerald-500"></i>
            <span>Online</span>
        </div>
    </div>
</div>