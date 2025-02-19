<div class="bg-black text-white w-64 py-6 px-4 flex flex-col">
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-white">Administración</h1>
    </div>
    <nav class="flex-1">
        <a href="/dashboard" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-zinc-800 mb-1 {{ request()->is('dashboard') ? 'bg-zinc-800' : '' }}">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
        <a href="/users" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-zinc-800 mb-1 {{ request()->is('users') ? 'bg-zinc-800' : '' }}">
            <i class="fas fa-users mr-2"></i> Usuarios
        </a>
        <a href="/products" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-zinc-800 mb-1 {{ request()->is('products') ? 'bg-zinc-800' : '' }}">
            <i class="fas fa-box mr-2"></i> Productos
        </a>
        <a href="/sales" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-zinc-800 mb-1 {{ request()->is('sales') ? 'bg-zinc-800' : '' }}">
            <i class="fas fa-shopping-cart mr-2"></i> Ventas
        </a>
        <a href="/settings" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-zinc-800 {{ request()->is('settings') ? 'bg-zinc-800' : '' }}">
            <i class="fas fa-cog mr-2"></i> Configuración
        </a>
    </nav>
</div>