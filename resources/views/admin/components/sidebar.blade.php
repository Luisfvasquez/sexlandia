<aside
    class="bg-chocolate text-cream w-64 min-h-screen fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 lg:static lg:inset-0 border-r border-white/10"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

    <div class="p-5 border-b border-white/10 flex justify-between items-center">
        <h1 class="text-xl md:text-2xl font-serif font-semibold tracking-wide text-bone">
            Sexlandia
        </h1>

        {{-- Botón para cerrar en móviles --}}
        <button @click="sidebarOpen = false" class="lg:hidden text-cream/50 hover:text-bone">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="p-4 space-y-2 overflow-y-auto">

        <a href="{{ route('dashboard') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.purchases.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.purchases.*') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Compras
        </a>
        <a href="{{ route('admin.orders.create') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.orders.create') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            POS
        </a>

        <a href="{{ route('admin.orders.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.orders.index') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Ordenes de venta
        </a>

        <a href="{{ route('admin.products.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Productos
        </a>

        <a href="{{ route('admin.inventories.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.inventories.*') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Inventario
        </a>

        <a href="{{ route('admin.clients.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.clients.index') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Clientes
        </a>

        <a href="{{ route('admin.index') }}"
            class="block px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.index') ? 'bg-wine text-white' : 'text-cream/70 hover:bg-white/5 hover:text-bone' }}">
            Administración
        </a>

        {{--  <a href="{{ route('delivery.dashboard') }}" No habilitado para este sistema por ahora
            class="block px-4 py-2 rounded transition-colors text-emerald-400 hover:bg-white/5 hover:text-emerald-300">
            🚚 Panel Delivery
        </a> --}}

        {{-- Configuración (Colapsable con Alpine.js) --}}
        <div x-data="{ configOpen: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.users-roles.*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="configOpen = !configOpen"
                class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition-colors text-cream/70 hover:bg-white/5 hover:text-bone focus:outline-none">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-cream/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Configuración</span>
                </span>
                <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': configOpen }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="configOpen" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100" class="pl-4 space-y-1" style="display: none;">
                <a href="{{ route('admin.roles.index') }}"
                    class="block px-4 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-wine text-white font-medium' : 'text-cream/60 hover:bg-white/5 hover:text-bone' }}">
                    Roles y Permisos
                </a>
                <a href="{{ route('admin.users-roles.index') }}"
                    class="block px-4 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.users-roles.*') ? 'bg-wine text-white font-medium' : 'text-cream/60 hover:bg-white/5 hover:text-bone' }}">
                    Asignar Roles
                </a>
            </div>
        </div>

    </nav>

</aside>
